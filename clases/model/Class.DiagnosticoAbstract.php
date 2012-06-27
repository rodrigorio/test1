<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClassDiagnosticoAbstract
 *
 * @author Andres
 */
abstract class DiagnosticoAbstract {
	protected $iId;
	protected $sDescripcion;
	
	public function __construct(){}
	
	/**
 	 *  @param int $iId
	 */
	public function setId($iId){
		$this->iId = (int)$iId;
	}
	/**
	 * @param string $sDescripcion
	 */
	public function setDescripcion($sDescripcion){
		$this->sDescripcion = $sDescripcion;
	}
	/**
	 *  @return int $iId
	 */
	public function getId(){
		return $this->iId ;
	}
	/**
	 * @return string $sDescripcion
	 */
	public function getDescripcion(){
		return $this->sDescripcion;
	}
}
?>