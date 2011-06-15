<?php

/**
 *
 *@author Rodrigo A. Rio
 *@email rodrigo.a.rio@gmail.com
 *
 */
class Invitado extends PersonaAbstract{
   
    private $sRelacion;//relacion que tiene con el que invita

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

    public function getRelacion() {
        return $this->sRelacion;
    }

    public function setRelacion($sRelacion){
        $this->sRelacion = $sRelacion;
    }
}
?>