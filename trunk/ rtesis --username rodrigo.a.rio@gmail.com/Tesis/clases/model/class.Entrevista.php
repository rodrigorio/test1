<?php

/**
 * @author Andres
 */
class Entrevista{

    const TIPO_LOCAL = "local";
    const TIPO_REMOTA = "remota";

    private $iId;
    private $sDescripcion;
    private $dFechaHora;

    private $oUsuario = null;
    private $iUsuarioId = null;

    private $dFechaBorradoLogico = null;

    private $aPreguntas = null;

    //las siguientes propiedades es para una entrevista asociada a un seguimiento
    private $iSeguimientoId; //uso interno, para obtener respuestas cuando esta asociada a seg.
    private $dFechaRealizado;
    private $bRealizada = false;
    private $aPreguntasRespuestas; //preguntas con sus respuestas si esta asociada a un seg y realizada
    /**
     * Si expiro el plazo de expiracion devuelve falso
     */
    private $bEditable = null;

    /**
     * Pensado para que en un futuro se pueda compartir el link y que se complete remotamente
     */
    private $eTipo;
    private $sUrlTokenKey;

    public function __construct(stdClass $oParams = null) {
        $vArray = get_object_vars($oParams);
        $vThisVars = get_class_vars(__CLASS__);
        if (is_array($vArray)) {
            foreach ($vArray as $varName => $value) {
                if (array_key_exists($varName, $vThisVars)) {
                    $this->$varName = $value;
                } else {
                    throw new Exception("Unknown property $varName in " . __CLASS__, -1);
                }
            }
        }
    }

    /**
     *  @param int $iId
     */
    public function setId($iId){
        $this->iId = (int)$iId;
    }

    /**
     *  @return int $iId
     */
    public function getId(){
        return $this->iId ;
    }

    /**
     * @param string $sDescripcion
     */
    public function setDescripcion($sDescripcion){
        $this->sDescripcion = $sDescripcion;
    }

    /**
     * @return string $sDescripcion
     */
    public function getDescripcion(){
        return $this->sDescripcion;
    }

    /**
     * @return string $dFechaHora
     */
    public function getFechaHora(){
            return $this->dFechaHora;
    }

    /**
     *  @param string $dFechaHora
     */
    public function setFechaHora($dFechaHora){
        $this->dFechaHora = $dFechaHora;
    }

    public function setPreguntas($aPreguntas){
        $this->aPreguntas = $aPreguntas;
        return $this;
    }

    public function getPreguntas()
    {
        if($this->aPreguntas === null){
            $this->aPreguntas = SeguimientosController::getInstance()->getPreguntasByEntrevistaId($this->iId);
        }
        return $this->aPreguntas;
    }

    public function getPreguntasRespuestas()
    {
        if(null === $this->iSeguimientoId || !$this->isRealizada()){
            return null;
        }

        if($this->aPreguntasRespuestas === null){
            $this->aPreguntasRespuestas = SeguimientosController::getInstance()->getPreguntasRespuestasBySeguimientoId($this->iSeguimientoId, $this->iId);
        }
        return $this->aPreguntasRespuestas;
    }

    public function addPregunta($oPregunta)
    {
        $this->aPreguntas[] = $oPregunta;
        return $this;
    }

    /**
     * Setea la fecha actual como fecha de borrado
     */
    public function setFechaBorradoLogicoHoy()
    {
        $today = date('Y-m-d');
        $this->dFechaBorradoLogico = $today;
    }

    public function getFechaBorradoLogico()
    {
        return $this->dFechaBorradoLogico;
    }

    public function setFechaRealizadoHoy()
    {
        $today = date('Y-m-d');
        $this->dFechaRealizado = $today;
    }

    public function getFechaRealizado($format = false)
    {
        if($format){
            return Utils::fechaFormateada($this->dFechaRealizado, "d/m/Y");
        }else{
            return $this->dFechaRealizado;
        }
    }

    public function setFechaRealizado($dFechaRealizado){
        $this->dFechaRealizado = $dFechaRealizado;
    }

    public function setUsuario($oUsuario){
        $this->oUsuario = $oUsuario;
    }

    public function getUsuario(){
        return $this->oUsuario;
    }

    public function setUsuarioId($iUsuarioId){
        $this->iUsuarioId = $iUsuarioId;
        if(!empty($iUsuarioId) && null !== $this->oUsuario && $this->oUsuario->getId() != $iUsuarioId){
            $this->oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
        }
    }

    public function getUsuarioId()
    {
        if($this->iUsuarioId === null && $this->oUsuario !== null){
            return $this->oUsuario->getId();
        }
        return $this->iUsuarioId;
    }

    public function isRealizada($flag = null){
        if(null !== $flag){
            $this->bRealizada = $flag ? true : false;
        }else{
            return $this->bRealizada;
        }
    }

    public function setTipoLocal()
    {
        $this->eTipo = self::TIPO_LOCAL;
    }

    public function setTipoRemota()
    {
        $this->eTipo = self::TIPO_REMOTA;
    }

    public function getTipo()
    {
        return $this->eTipo;
    }

    public function isTipoLocal()
    {
        return $this->eTipo == self::TIPO_LOCAL ? true : false;
    }

    public function isTipoRemota()
    {
        return $this->eTipo == self::TIPO_REMOTA ? true : false;
    }

    public function setUrlTokenKey($sUrlTokenKey)
    {
        $this->sUrlTokenKey = $sUrlTokenKey;
    }

    public function getUrlTokenKey()
    {
        return $this->sUrlTokenKey;
    }

    public function getSeguimientoId()
    {
        return $this->iSeguimientoId;
    }

    /**
     * Si ya esta realizada y la fecha de realizado esta expirada entonces no es editable
     *
     * (si no esta realizada entonces la fecha de realizado == null siempre)
     */
    public function isEditable($flag = null){
        if(null !== $flag){
            $this->bEditable = $flag ? true : false;
            return $this;
        }else{
            if(!$this->isRealizada()){
                return true;
            }
            if(null === $this->bEditable){
                if(SeguimientosController::getInstance()->isEntidadEditable($this->dFechaRealizado)){
                    $this->bEditable = true;
                }else{
                    $this->bEditable = false;
                }
            }
            return $this->bEditable;
        }
    }
}
