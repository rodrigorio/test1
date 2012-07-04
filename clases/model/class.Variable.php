<?php

/**
 * Description of class Variable
 *
 * @author Andrés
 */
class Variable {
	private $iId;
	private $sNombre;
	private $iTipo;
	private $sDescripcion;
	private $oUnidad;	
	private $dFechaHora;
	
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
 	 *  @param int $iTipo
	 */
	public function setTipo($iTipo){
		$this->iTipo = (int)$iTipo;
	}
	/**
	 * @param string $sDescripcion
	 */
	public function setDescripcion($sDescripcion){
		$this->sDescripcion = $sDescripcion;
	}
   /**
 	 *  @param int $oUnidad
	 */
	public function setUnidadId($oUnidad){
		$this->oUnidad=$oUnidad;
	}	
	/**
 	 *  @param int $dFechaHora
	 */
	public function setFechaHora($dFechaHora){
		$this->dFechaHora = (int)$dFechaHora;
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
	 * @return string $iTipo
	 */
	public function getTipo(){
		return $this->iTipo;
	}
	/**
	 * @return string $sDescripcion
	 */
	public function getDescripcion(){
		return $this->sDescripcion;
	}
    /**
	 * @return string $oUnidad
	 */
	public function getUnidad(){
		
		if($this->oUnidad==null){
			//TODO llamar a metodo que trae unidad segun variableid
		} 		
		return $this->oUnidad;
	}
    /**
	 * @return string $dFechaHora
	 */
	public function getFechaHora(){
		return $this->dFechaHora;
	}
}