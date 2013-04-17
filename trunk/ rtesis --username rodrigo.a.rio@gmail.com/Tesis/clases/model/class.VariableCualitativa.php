<?php

/**
 *
 * @author Andres
 */
  class VariableCualitativa extends VariableAbstract
{
    /**
     * array objetos Modalidad, nunca esta null porque es una relacion de composicion
     */
    private $aModalidades;  	

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
     * En la clase VariableCualitativa es redeclarada para devolver true.
     */
    public function isVariableCualitativa(){ return true; }
	
    /**
     *  @param int $iModalidad
     */
    public function setValor($iModalidadId){
        $this->valor = (int)$iModalidadId;
    }

   /**
    * @return int valor
    *
    * corresponde al id de la modalidad seleccionada
    */
    public function getValor(){
        return $this->valor;
    }

    public function getModalidades()
    {
        return $this->aModalidades;
    }

    public function setModalidades($aModalidades)
    {
        $this->aModalidades = $aModalidades;
    }

    public function addModalidad($oModalidad){
        $this->aModalidades[] = $oModalidad;
    }
 }