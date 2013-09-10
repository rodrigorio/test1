<?php

/**
 * Description of classSeguimientoAbstract
 *
 * 
 */
abstract class SeguimientoAbstract
{
   /**
    * Los valores tienen que corresponder con el enum de la tabla 
    */
   const ESTADO_ACTIVO = "activo";
   const ESTADO_DETENIDO = "detenido";
   
   protected $iId;
   protected $oDiscapacitado;
   protected $sFrecuenciaEncuentros;
   protected $iUsuarioId;
   protected $oUsuario;
   protected $oPractica;
   protected $sAntecedentes;
   protected $sPronostico;
   protected $sDiaHorario;
   protected $dFechaCreacion;
   protected $sEstado;

   /**
    * Estos atributos se usan unicamente para el caso de asociar al seguimiento.
    * Para obtener los valores por fecha ver las referencias a traves de entrada por fecha (objeto Entrada)
    */
   protected $aObjetivos = null;
   protected $aUnidades = null;

   /**
    * Instancias a objetos entradas, cada entrada tendria una fecha y los valores para todas las variables
    * y todos los objetivos del seguimiento en esa fecha.
    */
   protected $aEntradas = null;
   
   /*
    * array objetos Foto
    */
    protected $aFotos = null;
   /*
    * array objetos Archivo
    */
    protected $aArchivos = null;
    /*
    * array objetos EmbedVideo
    */
    protected $aEmbedVideos = null;
    /**
     * Instancia de clase Archivo
     */
    protected $oAntecedentes;

    protected $oDiagnostico;
        
    public function __construct(){}

    /**
     * En la clase SeguimientoPersonalizado es redeclarada para devolver true.
     */
    public function isSeguimientoPersonalizado(){ return false; }
    /**
     * En la clase SeguimientoSCC es redeclarada para devolver true.
     */
    public function isSeguimientoSCC(){ return false; }
   
    public function setId($id){
        $this->iId = $id;
    }

    public function setFrecuenciaEncuentros($sFrecuenciaEncuentros){
        $this->sFrecuenciaEncuentros = $sFrecuenciaEncuentros;
    }

    public function setDiscapacitado($oDiscapacitado){
        $this->oDiscapacitado = $oDiscapacitado;
    }

    public function setUsuario($oUsuario){
        $this->oUsuario = $oUsuario;
    }

    public function setPractica($oPractica){
        $this->oPractica = $oPractica;
    }

    public function setAntecedentes($sAntecedentes){
        $this->sAntecedentes = $sAntecedentes;
    }

    public function setPronostico($sPronostico){
        $this->sPronostico = $sPronostico;
    }

    public function setDiaHorario($sDiaHorario){
        $this->sDiaHorario = $sDiaHorario;
    }

    public function setFechaCreacion($dFechaCreacion){
        $this->dFechaCreacion = $dFechaCreacion;
    }

    public function getId(){
        return $this->iId;
    }

    public function getFrecuenciaEncuentros(){
        return $this->sFrecuenciaEncuentros;
    }

    public function getDiscapacitado(){
        return $this->oDiscapacitado;
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
        return $this->iUsuarioId;
    }

    public function getPractica(){
        return $this->oPractica;
    }

    public function getAntecedentes(){
        return $this->sAntecedentes;
    }

    public function getPronostico($nl2br = false){
        if($nl2br){
            return nl2br($this->sPronostico);
        }else{
            return $this->sPronostico;
        }
    }

    public function getDiaHorario(){
        return $this->sDiaHorario;
    }

    public function getFechaCreacion($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFecha);
        }else{
            return $this->dFechaCreacion;
        }
    }

    public function setEstadoActivo()
    {
        $this->sEstado = self::ESTADO_ACTIVO;
        return $this;
    }

    public function setEstadoDetenido()
    {
        $this->sEstado = self::ESTADO_DETENIDO;
        return $this;
    }

    public function getEstado(){
        return  $this->sEstado;
    }

    public function setFotos($aFotos){
        $this->aFotos = $aFotos;
    }

    public function addFoto($oFoto){
        $this->aFotos[] = $oFoto;
        return $this;
    }

    public function setArchivos($aArchivos){
        $this->aArchivos = $aArchivos;
    }

    public function addArchivo($oArchivo)
    {
        $this->aArchivos[] = $oArchivo;
        return $this;
    }

    public function setEmbedVideos($aEmbedVideos){
        $this->aEmbedVideos = $aEmbedVideos;
        return $this;
    }

    public function addEmbedVideo($oEmbedVideo)
    {
        $this->aEmbedVideos[] = $oEmbedVideo;
        return $this;
    }

    /**
     * @return array|null Foto
     */
    public function getFotos()
    {
    	if($this->aFotos == null){
            $this->aFotos = SeguimientosController::getInstance()->obtenerFotosSeguimiento($this->iId);
    	}
        return $this->aFotos;
    }

    /**
     * @return array|null Archivo
     */
    public function getArchivos()
    {
    	if($this->aArchivos == null){
            $this->aArchivos = SeguimientosController::getInstance()->obtenerArchivosSeguimiento($this->iId);
    	}
        return $this->aArchivos;
    }

    public function getEmbedVideos(){
        if($this->aEmbedVideos === null){
            $this->aEmbedVideos = SeguimientosController::getInstance()->obtenerEmbedVideosSeguimiento($this->iId);
        }
        return $this->aEmbedVideos;
    }

    public function getArchivoAntecedentes()
    {
        return $this->oAntecedentes;
    }
    
    public function setArchivoAntecedentes($oAntecedentes){
        $this->oAntecedentes = $oAntecedentes;
    }

    /**
     * Devuelve todas las unidades, las de edicion esporadica y las de edicion regular
     */
    public function getUnidades()
    {
        if($this->aUnidades === null){
            $this->aUnidades = SeguimientosController::getInstance()->getUnidadesBySeguimientoId($this->iId);
        }
        return $this->aUnidades;
    }

    public function setUnidades($aUnidades){
        $this->aUnidades = $aUnidades;
        return $this;
    }

    public function addUnidad($oUnidad)
    {
        $this->aUnidades[] = $oUnidad;
        return $this;
    }

    public function getEntradas($dFechaDesde = "", $dFechaHasta = "")
    {
        if($this->aEntradas === null){
            $this->aEntradas = SeguimientosController::getInstance()->getEntradasBySeguimientoId($this->iId, $dFechaDesde, $dFechaHasta);
        }
        return $this->aEntradas;
    }

    /**
     * Para no tener que levantar tanta info si solo se requiere ver la ultima
     */
    public function getUltimaEntrada()
    {
        return SeguimientosController::getInstance()->getUltimaEntradaBySeguimiento($this->iId);
    }

    /**
     * Para no tener que levantar tanta info devuelve la entrada para una fecha determinada
     */
    public function getEntradaByFecha($dFecha)
    {
        $oEntrada = null;
        $oEntrada = SeguimientosController::getInstance()->getEntradaPorFechaBySeguimientoId($this->iId, $dFecha);
        return $oEntrada;
    }

    public function setEntradas($aEntradas){
        $this->aEntradas = $aEntradas;
        return $this;
    }

    public function addEntrada($oEntrada)
    {
        $this->aEntradas[] = $oEntrada;
        return $this;
    }

    abstract public function getObjetivos();
    abstract public function setObjetivos($aObjetivos);
    abstract public function addObjetivo($aObjetivo);
    
    abstract public function getDiagnostico();
    abstract public function setDiagnostico($oDiagnostico);
}