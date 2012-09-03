<?php

/**
 * Podria haber hecho clases asociativas pero mejor asi por cuestiones de practicidad.
 */
class ParametroSistema extends Parametro
{
    private $sValor;

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

    public function setValor($sValor)
    {
        $this->sValor = $sValor;
    }

    public function getValor()
    {
        return $this->sValor;
    }
}
