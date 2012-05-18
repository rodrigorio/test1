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
 
     /**
     * @return array|null Foto
     */
    public function getFotos()
    {
    	if($this->aFotos === null){
            $this->aFotos = ComunidadController::getInstance()->obtenerFotosPublicacion($this->iId);
    	}
        return $this->aFotos;
    }  
 public function getArchivos()
    {
    	if($this->aArchivos === null){
            $this->aArchivos = ComunidadController::getInstance()->obtenerArchivosPublicacion($this->iId);
    	}
        return $this->aArchivos;
    }
 public function getId(){
        return $this->iId;
    }
 public function getTitulo(){
        return $this->sTitulo;
    }
 public function getFecha(){
        return $this->dFecha;
    }
 public function getDescripcion(){
        return $this->sDescripcion;
    }
 } 