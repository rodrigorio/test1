<?php


/**
 * Description of 
 *
 * @author Andres
 */
abstract class FichaAbstract
 {
 	private $id;
 	private $sTitulo;
 	private $dFecha;
 	private $bActivo;
 	private $sDescripcion;
 	
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
        ////gets
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