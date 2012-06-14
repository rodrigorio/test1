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
       
    public function __construct(){}
   
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

    public function getPronostico(){
        return $this->sPronostico;
    }

    public function getDiaHorario(){
        return $this->sDiaHorario;
    }

    public function getFechaCreacion(){
        return $this->dFechaCreacion;
    }

    /**
     * Estos son asi porque en la DB el campo tipo es un enum
     */
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

    /**
     * @return null|Archivo
     */
    public function getArchivoAntecedentes()
    {
        return $this->oAntecedentes;
    }
    
    public function setArchivoAntecedentes($oAntecedentes){
        $this->oAntecedentes = $oAntecedentes;
    }
}