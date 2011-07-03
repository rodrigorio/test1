<?php

/**
 *
 *
 *
 */
class Usuario extends PersonaAbstract
{
    private $dFechaAlta;

    private $sSitioWeb;
    
    /*
     * Cuando se crea el usuario se guarda el nombre de usuario con la concatenacion de nombre y apellido
     * reemplazando espacios blancos con '.' (puntos)
     */
    private $sNombreUsuario;

    private $sContrasenia;

    private $oEspecialidad;

    private $iInvitacionesDisponibles;
    
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

    public function getNombreUsuario(){
        return $this->sNombreUsuario;
    }

    public function getContrasenia(){
    	return $this->sContrasenia;
    }

    public function getFechaAlta(){
        return $this->dFechaAlta;
    }

    public function getSitioWeb(){
        return $this->sSitioWeb;
    }

    public function getEspecialidad(){
        return $this->oEspecialidad;
    }

    public function setNombreUsuario($sNombreUsuario){
    	$this->sNombreUsuario = $sNombreUsuario;
    }

    public function setFechaAlta($dFechaAlta){
        $this->dFechaAlta = $dFechaAlta;
    }

    public function setSitioWeb($sSitioWeb){
    	$this->sSitioWeb = $sSitioWeb;
    }

    public function setContrasenia($contrasenia){
        $this->sContrasenia = $contrasenia;
    }

    public function setEspecialidad($oEspecialidad){
        $this->oEspecialidad = $oEspecialidad;
    }

    public function setInvitacionesDisponibles($iInvitacionesDisponibles){
        $this->iInvitacionesDisponibles = $iInvitacionesDisponibles;
        return $this;
    }
    public function getInvitacionesDisponibles(){
        return $this->iInvitacionesDisponibles;
    }
}