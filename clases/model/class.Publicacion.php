<?php

class Publicacion extends FichaAbstract
{
    private $iUsuarioId;
    private $oUsuario;
    private $bActivoComentarios = true;
    private $bModerado = false;
    private $bPublico = false;
    private $sDescripcionBreve;
    private $sKeywords;

    public function __construct(stdClass $oParams = null){
        parent::__construct();
        $vArray = get_object_vars($oParams);
        $vThisVars = get_class_vars(__CLASS__);
        if(is_array($vArray)){
            foreach($vArray as $varName => $value){
                if(array_key_exists($varName,$vThisVars)){
                    $this->$varName = $value;
                }else{
                    throw new Exception("Unknown property $varName in "  . __CLASS__,-1);
                }
            }
        }
    }
    
    public function setUsuarioId($iUsuarioId){
        $this->iUsuarioId = $iUsuarioId;
        if(!empty($iUsuarioId) && null !== $this->oUsuario && $this->oUsuario->getId() != $iUsuarioId){
            $this->oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
        }
    }

    public function setUsuario($oUsuario)
    {
        $this->oUsuario = $oUsuario;
        return $this;
    }
    
public function isModerado($flag = null){
        if(null !== $flag){
            $this->bModerado = $flag ? true : false;
            return $this;
        }else{
            return $this->bModerado;
        }
 }
 
public function isPublico($flag = null){
        if(null !== $flag){
            $this->bPublico = $flag ? true : false;
            return $this;
        }else{
            return $this->bPublico;
        }
 }
public function isActivoComentarios($flag = null){
        if(null !== $flag){
            $this->bActivoComentarios = $flag ? true : false;
            return $this;
        }else{
            return $this->bActivoComentarios;
        }
 }
public function setDescripcionBreve($sDescripcionBreve){
    	$this->sDescripcionBreve = $sDescripcionBreve;
        return $this;
    }
public function setKeywords($sKeywords){
    	$this->sKeywords = $sKeywords;
        return $this;
    }
    
    public function getUsuario(){
    	if($this->oUsuario == null && !empty($this->iUsuarioId)){
            $this->oUsuario = ComunidadController::getInstance()->getUsuarioById($this->iUsuarioId);
    	}
        return $this->oUsuario;
    }
    
    public function getDescripcionBreve(){
        return $this->sDescripcionBreve;
    }
    
    public function getKeywords(){
        return $this->sKeywords;
    }   
}