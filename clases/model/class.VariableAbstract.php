<?php

/**
 * Description of class Variable
 *
 * @author Andrés
 */
 abstract class VariableAbstract
{
    protected $iId;
    protected $sNombre;
    protected $sDescripcion;
    protected $dFecha;
    protected $valor = null;
				
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

    public function getFecha($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFecha);
        }else{
            return $this->dFecha;
        }
    }

    public function setFecha($dFecha){
        $this->dFecha = $dFecha;
    }
}