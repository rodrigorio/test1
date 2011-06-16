<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classDiagnosticoSCC
 *
 * @author Rodrigo A. Rio
 */
class DiagnosticoSCC extends DiagnosticoAbstract{
   private $oArea;
   
	/**
 	 *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase Ciclo
	 * @param stdClass $oParams
	 */
	public function __construct(stdClass $oParams = null){
		parent::__contruct();
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
	 * @param Area $oArea
	 */
	public function setArea($oArea){
		$this->oArea = $oArea;
	}
	
	/**
	 * @return Area
	 */
	public function getArea(){
		$this->oArea;
	}
}
?>