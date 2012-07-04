<?php
/**
 * Description of class Unidad
 *
 * @author Andrés
 */
class Unidad{
	private $iId;
	private $sNombre;
	private $sDescripcion;
	private $iEditable;
	private $dFechaHora;
	private $iPorDefecto;
	
		
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
	 * @param string $sDescripcion
	 */
	public function setDescripcion($sDescripcion){
		$this->sDescripcion = $sDescripcion;
	}
    /**
 	 *  @param int $iEditable
	 */
	public function setEditable($iEditable){
		$this->iEditable = (int)$iEditable;
	}
    /**
 	 *  @param int $dFechaHora
	 */
	public function setFechaHora($dFechaHora){
		$this->dFechaHora = (int)$dFechaHora;
	}
	/**
 	 *  @param int $iPorDefecto
	 */
	public function setPorDefecto($iPorDefecto){
		$this->iPorDefecto= (int)$iPorDefecto;
	}	
	
	//gets
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
    /**
	 * @return string $sDescripcion
	 */
	public function getDescripcion(){
		return $this->sDescripcion;
	}
	/**
	 * @return string $iEditable
	 */
	public function getEditable(){
		return $this->iEditable;
	}
    /**
	 * @return string $dFechaHora
	 */
	public function getFechaHora(){
		return $this->dFechaHora;
	}
    /**
	 * @return string $iPorDefecto
	 */
	public function getPorDefecto(){
		return $this->iPorDefecto;
	}
   
}