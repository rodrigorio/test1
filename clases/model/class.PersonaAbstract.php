<?php

/**
 *
 *
 */
abstract class PersonaAbstract
{
    protected $iId;
    protected $sNombre;
    protected $sApellido;
    protected $sSexo;
    protected $iTipoDocumento;
    protected $sNumeroDocumento;
    protected $dFechaNacimiento;
    protected $sEmail;
    protected $sTelefono;
    protected $sCelualr;
    protected $sFax;
    protected $sDomicilio;
    protected $instituciones_id;
    protected $oCiudad;
    protected $sCiudadOrigen;
    protected $sCodigoPostal;
    protected $sEmpresa;
    protected $sUniversidad;
    protected $sSecundaria;
    
    public function __construct(){}

    public function setId($id){
        $this->iId = $id;
    }
    public function setNombre($sNombre){
        $this->sNombre = $sNombre;
    }
    public function setApellido($sApellido){
        $this->sApellido = $sApellido;
    }
    public function setSexo($sSexo){
        $this->sSexo = $sSexo;
    }
    public function setTipoDocumento($iTipoDoc){
        $this->iTipoDocumento = $iTipoDoc;
    }
    public function setNumeroDocumento($sNumeroDocumento){
        $this->sNumeroDocumento = $sNumeroDocumento;
    }
    public function setFechaNacimiento($dFechaNacimiento){
        $this->dFechaNacimiento = $dFechaNacimiento;
    }
    public function setEmail($sEmail){
        $this->sEmail = $sEmail;
    }
//.....faltan agegar los demas
    public function getId(){
        return $this->iId;
    }
	public function getNombre(){
        return $this->sNombre;
    }
    public function getApellido(){
        return $this->sApellido;
    }
    public function getSexo(){
        return $this->sSexo;
    }
    public function getTipoDocumento(){
        return $this->iTipoDocumento;
    }
    public function getNumeroDocumento(){
        return $this->sNumeroDocumento;
    }
    public function getFechaNacimiento(){
        return $this->dFechaNacimiento;
    }
    public function getEmail(){
       return $this->sEmail;
    }
//.....faltan agegar los demas
}
?>