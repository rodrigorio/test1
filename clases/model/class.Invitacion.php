<?php

/**
 * Clase asociativa. se genera de la relacion entre usuarios e invitados.
 * 
 */
class Invitacion{

    const ESTADO_ACEPTADA = "aceptada";
    const ESTADO_PENDIENTE = "pendiente";

    private $oUsuario;
    private $oInvitado;
    private $dFecha;
    private $sToken;
    private $sRelacion;
    private $sEstado = self::ESTADO_PENDIENTE;
    
    public function __construct(stdClass $oParams = null){
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

    public function getRelacion() {
        return $this->sRelacion;
    }

    public function setRelacion($sRelacion){
        $this->sRelacion = $sRelacion;
    }

    public function getEstado()
    {
        return $this->sEstado;
    }

    public function setEstadoPendiente()
    {
        $this->sEstado = self::ESTADO_PENDIENTE;
        return $this;
    }

    public function setEstadoAceptada()
    {
        $this->sEstado = self::ESTADO_ACEPTADA;
        return $this;
    }

    public function setToken($sToken)
    {
        $this->sToken = $sToken;
    }

    public function getToken()
    {
        return $this->sToken;
    }

    public function getFecha($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFecha);
        }else{
            return $this->dFecha;
        }
    }

    public function setFecha($dFecha){
        $this->dFecha = $dFecha;
        return $this;
    }

    public function setUsuario($oUsuario)
    {
        $this->oUsuario = $oUsuario;
    }

    public function getUsuario($oUsuario)
    {
        return $this->oUsuario;
    }
    
    public function setInvitado($oInvitado)
    {
        $this->oInvitado = $oInvitado;
    }

    public function getInvitado($oInvitado)
    {
        return $this->oInvitado;
    }
}