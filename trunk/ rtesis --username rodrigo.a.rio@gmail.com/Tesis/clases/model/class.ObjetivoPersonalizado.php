<?php

/**
 * Description of classObjetivoPersonalizado
 *
 * @author Andres
 */
class ObjetivoPersonalizado extends ObjetivoAbstract
{	
    private $oObjetivoPersonalizadoEje;
	
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
    
    /**
     *  @param int $iObjetivoEjeId
     */
    public function setObjetivoPersonalizadoEje($oObjetivoPersonalizadoEje){
        $this->oObjetivoPersonalizadoEje = $oObjetivoPersonalizadoEje;
    }

    /**
     *  
     */
    public function getObjetivoPersonalizadoEje(){
        return $this->oObjetivoPersonalizadoEje;
    }    
}
