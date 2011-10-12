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
    
    private $sContraseniaNueva;
    
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

    /**
     * Recibe un campo de la tabla 'privacidad' y devuelve el valor
     * para el usuario actual.
     *
     * Solo los usuarios que operan el sistema van a tener valores de privacidad.
     * A su vez se puede consultar la privacidad de campos para usuarios que no estan
     * logueados (sin perfil asociado) por eso el metodo va en esta clase.
     */
    public function obtenerPrivacidadCampo($nombreCampo)
    {
        return SysController::getInstance()->getPrivacidadCampo($this->iId, $nombreCampo);
    }
    public function obtenerPrivacidad()
    {
        return SysController::getInstance()->getPrivacidad($this->iId);
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
    public function getContraseniaNueva(){
        return $this->sContraseniaNueva;
    }
    public function getInvitacionesDisponibles(){
        return $this->iInvitacionesDisponibles;
    }

    ///////////////////////SETS//////////////////////////
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

    public function setContraseniaNueva($pass){
        $this->sContraseniaNueva = $pass;
    }
    
}