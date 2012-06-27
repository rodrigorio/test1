<?php
/**
 * Description of classDiagnosticoPersonalizado
 *
 * @author Rodrigo A. Rio
 */
class DiagnosticoPersonalizado extends DiagnosticoAbstract{
	private $sCodigo;
	
	/**
 	 *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase Ciclo
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
?>