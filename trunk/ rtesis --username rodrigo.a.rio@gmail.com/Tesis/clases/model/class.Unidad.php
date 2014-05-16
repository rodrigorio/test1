<?php

/**
 * Una vez establecido si es regular o esporadica no se podra editar el valor.
 * El valor se determina solo al crear la unidad.
 */
class Unidad{

    const TIPO_EDICION_REGULAR = "regular";
    const TIPO_EDICION_ESPORADICA = "esporadica";

    private $iId;
    private $sNombre;
    private $sDescripcion;
    private $bPreCargada;
    private $dFechaHora;
    private $bAsociacionAutomatica;
    private $eTipoEdicion;

    /**
     * Esta relacion se necesita porque pueden existir unidades creadas por usuario
     * que todavia no se asignaron a ningun seguimiento.
     * Si la unidad se crea desde el administrador entonces el integrante no existe y la propiedad se mantendra en null
     */
    private $oUsuario = null;
    private $iUsuarioId = null;

    /**
     * Cuando las variables son las de una unidad que esta asociada a un seguimiento
     * entonces estas deben tener el valor mas reciente.
     *
     * Tambien tiene que haber metodos para obtener las variables de una unidad
     * pero que los valores dependan de una fecha determinada.
     *
     * TODO on demand, porque depende de lo que se necesite en el momento, y es mucha info.
     *
     */
    private $aVariables = null;

    /**
     * Esta fecha es != null cuando la unidad esta borrada logicamente. guarda el dia en la que se borro.
     * Es necesaria para saber que unidades mostrar cuando se visualiza una entrada.
     */
    private $dFechaBorradoLogico = null;

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
    * @param string $sNombre
    */
    public function setNombre($sNombre){
        $this->sNombre = $sNombre;
    }

    /**
     * @param string $sDescripcion
     */
    public function setDescripcion($sDescripcion){
            $this->sDescripcion = $sDescripcion;
    }

    /**
     *  @param int $dFechaHora
     */
    public function setFechaHora($dFechaHora){
        $this->dFechaHora = $dFechaHora;
    }

    public function setVariables($aVariables){
        $this->aVariables = $aVariables;
        return $this;
    }

    public function getVariables()
    {
        if($this->aVariables === null){
            $this->aVariables = SeguimientosController::getInstance()->getVariablesByUnidadId($this->iId);
        }
        return $this->aVariables;
    }

    public function addVariable($oVariable)
    {
        $this->aVariables[] = $oVariable;
        return $this;
    }

    /**
     *  @return int $iId
     */
    public function getId(){
        return $this->iId ;
    }

    /**
     * @return string $sNombre
     */
    public function getNombre(){
        return $this->sNombre;
    }

    /**
     * @return string $sDescripcion
     */
    public function getDescripcion($nl2br = false){
        if($nl2br){
            return nl2br($this->sDescripcion);
        }
        return $this->sDescripcion;
    }

    /**
     * @return string $dFechaHora
     */
    public function getFechaHora(){
            return $this->dFechaHora;
    }

    public function isPreCargada($flag = null){
        if(null !== $flag){
            $this->bPreCargada = $flag ? true : false;
            return $this;
        }else{
            return $this->bPreCargada;
        }
    }

    public function isAsociacionAutomatica($flag = null){
        if(null !== $flag){
            $this->bAsociacionAutomatica = $flag ? true : false;
            return $this;
        }else{
            return $this->bAsociacionAutomatica;
        }
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

    public function setTipoEdicionEsporadica()
    {
        $this->eTipoEdicion = self::TIPO_EDICION_ESPORADICA;
    }

    public function setTipoEdicionRegular()
    {
        $this->eTipoEdicion = self::TIPO_EDICION_REGULAR;
    }

    public function getTipoEdicion()
    {
        return $this->eTipoEdicion;
    }

    public function isTipoEdicionEsporadica()
    {
        return $this->eTipoEdicion == self::TIPO_EDICION_ESPORADICA ? true : false;
    }

    public function isTipoEdicionRegular()
    {
        return $this->eTipoEdicion == self::TIPO_EDICION_REGULAR ? true : false;
    }

    public function getFechaBorradoLogico()
    {
        return $this->dFechaBorradoLogico;
    }

    /**
     * Setea la fecha actual como fecha de borrado
     */
    public function setFechaBorradoLogicoHoy()
    {
        $today = date('Y-m-d');
        $this->dFechaBorradoLogico = $today;
    }

    /**
     * Obtiene la ultima entrada en la que la unidad fue asociada para todas las entradas de un seguimiento del usuario.
     * puede dar null si no se utilizo en ninguna entrada o si la unidad no tiene usuario porq es de asociacion automatica.
     */
    public function getUltimaEntrada($iSeguimientoId)
    {
        if($this->isAsociacionAutomatica()){
            return null;
        }
        return SeguimientosController::getInstance()->getUltimaEntradaSeguimientoByUnidadId($iSeguimientoId, $this->iId);
    }
}
