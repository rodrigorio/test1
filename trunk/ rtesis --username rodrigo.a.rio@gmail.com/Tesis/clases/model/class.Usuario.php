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
    private $sNombreUsuario;//de donde salio este atributo??
    private $sContrasenia;

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

    public function getNombreUsuario()
    {
        return $this->sNombreUsuario;
    public function getFechaAlta(){
        return $this->dFechaAlta;
    }
    public function getSitioWeb(){
        return $this->sSitioWeb;
    }
    public function getNombreUsuario(){
        return $this->sNombreUsuario;
    }
    public function getContrasenia(){
        return $this->sContrasenia;
    public function getFechaAlta(){
        return $this->dFechaAlta;
    }
    public function getSitioWeb(){
        return $this->sSitioWeb;
    }
    public function getNombreUsuario(){
        return $this->sNombreUsuario;
    }
    public function getContrasenia(){
        return $this->sContrasenia;
    }

    public function setNombreUsuario($sNombreUsuario)
    {
        $this->nombreUsuario = $sNombreUsuario;
        return $this;
 	public function setFechaAlta($dFechaAlta){
        $this->dFechaAlta = $dFechaAlta;
    }
    public function setSitioWeb($sSitioWeb){
        $this->sSitioWeb = $sSitioWeb;
 	public function setFechaAlta($dFechaAlta){
        $this->dFechaAlta = $dFechaAlta;
    }
    public function setSitioWeb($sSitioWeb){
        $this->sSitioWeb = $sSitioWeb;
    }
    public function getContrasenia()
    {
        return $this->sContrasenia;
    public function setNombreUsuario($nombreUsuario){
        $this->sNombreUsuario = $nombreUsuario;
    public function setNombreUsuario($nombreUsuario){
        $this->sNombreUsuario = $nombreUsuario;
    }

        public function setContrasenia($contrasenia){
        $this->sContrasenia = $contrasenia;
    public function setContrasenia($contrasenia){
        $this->sContrasenia = $contrasenia;
    }
}
?>