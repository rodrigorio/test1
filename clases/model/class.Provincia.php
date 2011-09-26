<?php
/**
 *
 * @author Rodrigo A. Rio
 * @email rodigo.a.rio@gmail.com
 */
class Provincia {
	private $iId;
	private $sNombre;
	private $oPais;
	
 	/**
 	 *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase Provincia
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
 	 *  @param int $iId
	 */
	public function setId($iId){
		$this->iId = (int)$iId;
	}
	/**
	 * @param string $sNombre
	 */
	public function setNombre($sNombre){
		$this->sNombre = $sNombre;
	}
	/**
	 * @param Pais $oPais
	 */
	public function setPais($oPais){
		$this->oPais = $oPais;
	}
	/**
	 *  @return int 
	 */
	public function getId(){
		return $this->iId ;
	}
	/**
	 * @return string 
	 */
	public function getNombre(){
		return $this->sNombre;
	}
	/**
	 * @return Pais 
	 */
	public function getPais(){
		return $this->oPais;
	}
}
?>
