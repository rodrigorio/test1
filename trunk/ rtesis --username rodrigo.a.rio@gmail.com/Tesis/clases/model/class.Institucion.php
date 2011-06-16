<?php
class Institucion {
	private $iId;
	private $sNombre;
	private $ociudad;
	
 	/**
 	 *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase Pais
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
	 *  @return int $iId
	 */
	public function getId(){
		return $this->iId ;
	}
	/**
	 * @return string $sNombre
	 */
	public function getNombre(){
		return $this->sNombre;
	}
?>