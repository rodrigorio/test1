<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classObjetivoPersonalizado
 *
 * @author Andres
 */
class ObjetivoPersonalizado extends ObjetivoAbstract{
	
	
	private $oObjetivoEje;
	
   /**
     *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase 
     * @param stdClass $oParams
     */
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
    
    /**
     *  @param int $iObjetivoEjeId
     */
    public function setObjetivoEje($oObjetivoEje){
        $this->oObjetivoEje = $oObjetivoEje;
    }
    /**
     *  
     */
    public function getObjetivoEje(){
        return $this->oObjetivoEje;
    }
    
}

