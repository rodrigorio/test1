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
    protected $sNacionalidad;
    protected $sSexo;
    protected $iTipoDocumentoId;
    protected $sNumeroDocumento;
    protected $dFechaNacimiento;
    protected $sEmail;
    protected $sTelefono;
    protected $sCelular;
    protected $sFax;
    protected $sDomicilio;
    protected $oInstitucion;
    /*
     * este atributo es para pedir por demanda el objeto ciudad si $oCiudad = null.
     */
    protected $iCiudadId;
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

    public function setNacionalidad($sNacionalidad){
        $this->sNacionalidad = $sNacionalidad;
    }

    public function setSexo($sSexo){
        $this->sSexo = $sSexo;
    }

    public function setTipoDocumento($iTipoDoc){
        $this->iTipoDocumentoId = $iTipoDoc;
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

    public function setTelefono($sTelefono){
        $this->sTelefono = $sTelefono;
    }

    public function setCelular($sCelular){
        $this->sCelular = $sCelular ;
    }

    public function setFax($sFax){
        $this->sFax = $sFax;
    }

    public function setDomicilio($sDomicilio){
        $this->sDomicilio = $sDomicilio;
    }

    public function setInstitucion($oInstitucion){
        $this->oInstitucion = $oInstitucion;
    }

    /**
     * Si se setea el id de ciudad y ya hay un objeto ciudad cargado
     * se fija si corresponde a la misma ciudad, de lo contrario levanta de nuevo el objeto
     */
    public function setCiudadId($iCiudadId){
        $this->iCiudadId = $iCiudadId;
        if(null !== $this->oCiudad && $this->oCiudad->getId() != $iCiudadId){
            $this->oCiudad = ComunidadController::getInstance()->getCiudadById($iCiudadId);
        }
    }

    public function setCiudad($oCiudad){
        $this->oCiudad = $oCiudad;
    }

    public function setCiudadOrigen($sCiudadOrigen){
        $this->sCiudadOrigen = $sCiudadOrigen;
    }

    public function setCodigoPostal($sCodigoPostal){
        $this->sCodigoPostal = $sCodigoPostal;
    }

    public function setEmpresa($sEmpresa){
        $this->sEmpresa = $sEmpresa;
    }

    public function setUniversidad($sUniversidad){
        $this->sUniversidad = $sUniversidad;
    }

    public function setSecundaria($sSecundaria){
        $this->sSecundaria = $sSecundaria;
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

    public function getNacionalidad(){
        return $this->sNacionalidad;
    }

    public function getSexo(){
        return $this->sSexo;
    }

    public function getTipoDocumento(){
        return $this->iTipoDocumentoId;
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

    public function getCiudadId(){
    	return $this->iCiudadId;
    }

    public function getCiudad(){
    	if($this->oCiudad == null){
            $this->oCiudad = ComunidadController::getInstance()->getCiudadById($this->iCiudadId);
    	}
    	return $this->oCiudad;
    }

    public function getTelefono(){
        return $this->sTelefono ;
    }

    public function getCelular(){
        return $this->sCelular;
    }

    public function getFax(){
        return $this->sFax ;
    }

    public function getDomicilio(){
        return $this->sDomicilio ;
    }

    public function getInstitucion(){
        return $this->oInstitucion ;
    }

    public function getCiudadOrigen(){
        return $this->sCiudadOrigen ;
    }

    public function getCodigoPostal(){
        return $this->sCodigoPostal ;
    }

    public function getEmpresa(){
        return $this->sEmpresa ;
    }

    public function getUniversidad(){
        return $this->sUniversidad;
    }

    public function getSecundaria(){
       	return $this->sSecundaria;
    }
}