<?php

class DiagnosticoPersonalizado extends DiagnosticoAbstract
{
    private $sCodigo;
	
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

    public function isDiagnosticoPersonalizado(){ return true; }

    /**
     * @param string $sCodigo
     */
    public function setCodigo($sCodigo){
        $this->sCodigo = $sCodigo;
    }

    /**
     * @return string
     */
    public function getCodigo(){
        return $this->sCodigo;
    }    
}