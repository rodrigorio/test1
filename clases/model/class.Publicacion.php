<?php

/**
 *
 *
 *
 */
class Publicacion extends FichaAbstract
{
    private $iUsuarioId;
    private $bModerado;
    private $bPublico;
    private $bActivoComentario;
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
    }
    
public function isModerado($flag = null){
        if(null !== $flag){
            $this->bModerado = $flag ? true : false;
        }else{
            return $this->bModerado;
        }
 }
 
public function isPublico($flag = null){
        if(null !== $flag){
            $this->bPublico = $flag ? true : false;
        }else{
            return $this->bPublico;
        }
 }
public function isActivoComentario($flag = null){
        if(null !== $flag){
            $this->bActivoComentario = $flag ? true : false;
        }else{
            return $this->bActivoComentario;
        }
 }
public function setDescripcionBreve($sDescripcionBreve){
    	$this->sDescripcionBreve = $sDescripcionBreve;
    }
public function setKeywords($sKeywords){
    	$this->sKeywords = $sKeywords;
    }
    
    //gets
public function getUsuarioId(){
        return $this->iUsuarioId;
    }
    
public function getDescripcionBreve(){
        return $this->sDescripcionBreve;
    }
    
public function getKeywords(){
        return $this->sKeywords;
    }
  
    
    
    
}
?>
