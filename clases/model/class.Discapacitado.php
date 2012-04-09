<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classPersona
 *
 * @author Andres
 */
class Discapacitado extends PersonaAbstract
 {
    private $id;
    private $sNombreApellidoPadre;
    private $sNombreApellidoMadre;
    private $dFechaNacimientoPadre;
    private $dFechaNacimientoMadre;
    private $sOcupacionPadre;
    private $sOcupacionMadre;
    private $sNombreHermanos;
    /**
     * Es una relacion de agregacion
     * Puede ser una persona y que el usuario que la creo ya no exista
     *
     * Hacemos que se obtenga a demanda el objeto usuario porque
     * se necesita en la minoria de los casos
     */
    private $oUsuario = null;
    private $iUsuarioId;
    		
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
 	public function getId(){
        return $this->iId;
    }
    public function getNombreApellidoPadre(){
        return $this->sNombreApellidoPadre;
    }
    public function getNombreApellidoMadre(){
        return $this->sNombreApellidoMadre;
    }
    public function getFechaNacimientoPadre(){
        return $this->dFechaNacimientoPadre;
    }
    public function getFechaNacimientoMadre(){
        return $this->dFechaNacimientoMadre;
    }
    public function getOcupacionPadre(){
        return $this->sOcupacionPadre;
    }
    public function getOcupacionMadre(){
        return $this->sOcupacionMadre;
    }
    public function getNombreHermanos(){
        return $this->sNombreHermanos;
    }

    /*
     * Objeto usuario se devuelve on demand
     */
    public function getUsuario(){
    	if($this->oUsuario == null && !empty($this->iUsuarioId)){
            $this->oUsuario = ComunidadController::getInstance()->getUsuarioById($this->iUsuarioId);
    	}
        return $this->oUsuario;
    }
    public function setUsuario($oUsuario){
        $this->oUsuario = $oUsuario;
    }
    public function setUsuarioId($iUsuarioId){
        $this->iUsuarioId = $iUsuarioId;
        if(!empty($iUsuarioId) && null !== $this->oUsuario && $this->oUsuario->getId() != $iUsuarioId){
            $this->oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
        }
    }
    
    public function setId($iId){
        $this->iId = $iId;
    }
    public function setNombreApellidoPadre($sNombreApellidoPadre){
        $this->sNombreApellidoPadre = $sNombreApellidoPadre;
    }
    public function setNombreApellidoMadre($sNombreApellidoMadre){
        $this->sNombreApellidoMadre = $sNombreApellidoMadre;
    }
    public function setFechaNacimientoPadre($dFechaNacimientoPadre){
        $this->dFechaNacimientoPadre = $dFechaNacimientoPadre;
    }
    public function setFechaNacimientoMadre($dFechaNacimientoMadre){
        $this->dFechaNacimientoMadre = $dFechaNacimientoMadre;
    }
    public function setNombreHermanos($sNombreHermanos){
        $this->sNombreHermanos = $sNombreHermanos;
    }
    public function setOcupacionPadre($sOcupacionPadre){
        $this->sOcupacionPadre = $sOcupacionPadre;
    }
    public function setOcupacionMadre($sOcupacionMadre){
        $this->sOcupacionMadre = $sOcupacionMadre;
    }
}