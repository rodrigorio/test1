<?php


/**
 * Description of Ficha abstract *
 * @author Andres
 */
abstract class FichaAbstract
 {
 	protected  $id;
 	protected  $sTitulo;
 	protected  $dFecha;
 	protected  $bActivo;
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
    * array objetos Archivo
    */
    protected $aEmbedVideos = null;
    
 public function __construct(){}
 	
  public function setId($id){
        $this->iId = $id;
    }
    
 public function setTitulo($sTitulo){
    	$this->sTitulo = $sTitulo;
    }
    
 public function setFecha($dFecha){
        $this->dFecha = $dFecha;
    }
    
 public function isActivo($flag = null){
        if(null !== $flag){
            $this->bActivo = $flag ? true : false;
        }else{
            return $this->bActivo;
        }
 }
  
 public function setDescripcion($sDescripcion){
    	$this->sDescripcion = $sDescripcion;
    }
  public function setFotos($aFotos)
    {
        $this->aFotos = $aFotos;
    }   
	public function addFoto($oFoto){
		$this->aFotos[] = $oFoto;		
	}    
    public function setArchivos($aArchivos)
    {
        $this->aArchivos = $aArchivos;
    }
 
        ////gets
     /**
     * @return array|null Foto
     */
    public function getFotos()
    {
    	if($this->aFotos == null){
            $this->aFotos = ComunidadController::getInstance()->obtenerFotosPublicacion($this->iId);
    	}
        return $this->aFotos;
    }  
 public function getArchivos()
    {
    	if($this->aArchivos == null){
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