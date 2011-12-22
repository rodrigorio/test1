<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classSeguimientoPersonalizado
 *
 * @author Andres
 */
class SeguimientoPersonalizado extends SeguimientoAbstract {
    //put your code here
    
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
    
 	public function getTipoSeguimiento(){
    	return "PERSONALIZADO";
    }
}
?>
