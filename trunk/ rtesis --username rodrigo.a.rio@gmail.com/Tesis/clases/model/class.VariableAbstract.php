<?php

/**
 * Description of class Variable
 *
 * @author Andrés
 */
 abstract class Variable
{
    private $iId;
    private $sNombre;
    private $iTipo;
    private $sDescripcion;
    private $dFechaHora;
				
    /**
     * En la clase VariableNumerica es redeclarada para devolver true.
     */
    public function isVariableNumerica(){ return false; }
    /**
     * En la clase VariableTexto es redeclarada para devolver true.
     */
    public function isVariableTexto(){ return false; }
    /**
     * En la clase VariableCualitativa es redeclarada para devolver true.
     */
    public function isVariableCualitativa(){ return false; }
	
	
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
	 * @return string $dFechaHora
	 */
	public function getFechaHora(){
		return $this->dFechaHora;
	}
}