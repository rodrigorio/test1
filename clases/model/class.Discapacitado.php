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
    private $sNombreApellidoPadre;
    private $sNombreApellidoMadre;
    private $dFechaNacimientoPadre;
    private $dFechaNacimientoMadre;
    private $sOcupacionPadre;
    private $sOcupacionMadre;
    private $sNombreHermanos;
    
	
	
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
}
?>
