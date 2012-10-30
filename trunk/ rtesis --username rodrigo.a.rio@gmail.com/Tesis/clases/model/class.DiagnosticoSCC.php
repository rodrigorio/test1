<?php

class DiagnosticoSCC extends DiagnosticoAbstract
{
    /**
     * Ejes Tematicos asociados al diagnostico
     * Instancias de objetos EjeTematico
     */
    private $aEjesTematicos = null;
   
    public function __construct(stdClass $oParams = null)
    {
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

    public function isDiagnosticoSCC(){ return true; }
	
    public function getEjesTematicos()
    {
    	return $this->aEjesTematicos;
    }

    public function setEjesTematicos($aEjesTematicos)
    {
        $this->aEjesTematicos = $aEjesTematicos;
    }

    public function addEjeTematico($oEjeTematico){
        $this->aEjesTematicos[] = $oEjeTematico;
    }
}