<?php

class Publicacion extends FichaAbstract implements PublicacionesInterface
{
    private $iUsuarioId;
    private $oUsuario;
    private $bActivoComentarios = true;
    private $bPublico = false;
    private $sDescripcionBreve;
    private $sKeywords;
    private $aComentarios;

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

    public function getUsuarioId()
    {
        return $this->iUsuarioId;
    }
    
    public function getDescripcionBreve(){
        return $this->sDescripcionBreve;
    }
    
    public function getKeywords(){
        return $this->sKeywords;
    }

    public function getComentarios()
    {
        if($this->aComentarios === null){
            $this->aComentarios = ComunidadController::getInstance()->obtenerComentariosPublicacion($this->iId);
        }
        return $this->aComentarios;
    }

    public function setComentarios($aComentarios)
    {
        $this->aComentarios = $aComentarios;
        return $this;
    }

    public function addComentario($oComentario)
    {
        $this->aComentarios[] = $oComentario;
        return $this;
    }
}