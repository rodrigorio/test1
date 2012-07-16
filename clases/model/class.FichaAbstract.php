<?php


/**
 * Description of Ficha abstract *
 * @author Andres
 */
abstract class FichaAbstract
{
    protected  $iId;
    protected  $sTitulo;
    protected  $dFecha;
    protected  $bActivo = true;
    protected  $sDescripcion;
    
   /**
    * array objetos Moderacion, el historial completo
    */
    protected  $aModeraciones;

   /**
    * objeto Moderacion, estado de la ultima entrada en moderaciones, null si no tiene
    */
    protected  $oModeracion = null;
 	 	
   /**
    * array objetos Foto
    */
    protected $aFotos = null;

   /**
    * array objetos Archivo
    */
    protected $aArchivos = null;
    
   /**
    * array objetos EmbedVideo
    */
    protected $aEmbedVideos = null;
    
    public function __construct(){}
 	
    public function setId($iId){
        $this->iId = $iId;
        return $this;
    }
    
    public function setTitulo($sTitulo){
        $this->sTitulo = $sTitulo;
        return $this;
    }
    
    public function setFecha($dFecha){
        $this->dFecha = $dFecha;
        return $this;
    }
    
    public function isActivo($flag = null){
        if(null !== $flag){
            $this->bActivo = $flag ? true : false;
            return $this;
        }else{
            return $this->bActivo;
        }
    }
  
    public function setDescripcion($sDescripcion){
    	$this->sDescripcion = $sDescripcion;
        return $this;
    }

    public function setFotos($aFotos)
    {
        $this->aFotos = $aFotos;
        return $this;
    }

    public function addFoto($oFoto){
        $this->aFotos[] = $oFoto;
        return $this;
    }

    public function setArchivos($aArchivos)
    {
        $this->aArchivos = $aArchivos;
        return $this;
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
    	if($this->aFotos === null){
            $this->aFotos = ComunidadController::getInstance()->obtenerFotosFicha($this->iId);
    	}
        return $this->aFotos;
    }

    public function getArchivos()
    {
        if($this->aArchivos === null){
            $this->aArchivos = ComunidadController::getInstance()->obtenerArchivosFicha($this->iId);
        }
        return $this->aArchivos;
    }

    public function getEmbedVideos(){
        if($this->aEmbedVideos === null){
            $this->aEmbedVideos = ComunidadController::getInstance()->obtenerEmbedVideosFicha($this->iId);
        }
        return $this->aEmbedVideos;
    }

    public function getId(){
        return $this->iId;
    }

    public function getTitulo(){
        return $this->sTitulo;
    }

    public function getFecha($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFecha);
        }else{
            return $this->dFecha;
        }
    }

    public function getDescripcion($nl2br = false){
        if($nl2br){
            return nl2br($this->sDescripcion);
        }else{
            return $this->sDescripcion;
        }
    }

    public function getHistorialModeraciones()
    {
        if($this->aModeraciones === null){
            $this->aModeraciones = AdminController::getInstance()->obtenerHistorialModeracionesFicha($this->iId);
        }
        return $this->aModeraciones;
    }

    public function getModeracion()
    {
        return $this->oModeracion;
    }

    public function setModeracion($oModeracion)
    {
        $this->oModeracion = $oModeracion;
    }
 }