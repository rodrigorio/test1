<?php
/*Description of class VariableTexto
 *
 * @author Andr�s
 */
 class VariableTexto extends Variable {
 	
 	private $sValor;
 	
 	
 /**
 	 *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase Variable
	 * @param stdClass $oParams
	 */
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
     * En la clase VariableTexto es redeclarada para devolver true.
     */
    public function isVariableTexto(){ return true; }
     /**
 	 *  @param string $sValor
	 */
	public function setValor($sValor){
		$this->sValor = $sValor;
	}
     /**
	 * @return string $sValor
	 */
	public function getValor(){
		return $this->sValor;
	}
 }