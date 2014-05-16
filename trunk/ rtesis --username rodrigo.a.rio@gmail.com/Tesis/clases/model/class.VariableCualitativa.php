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
     *  @param Modalidad $oModalidad
     */
    public function setValor($oModalidad){
        $this->valor = $oModalidad;
    }

   /**
    * @return Modalidad corresponde a la modalidad seleccionada
    */
    public function getValor(){
        return $this->valor;
    }

    /**
     * Si el valor !== null entonces devuelve el string que describe la modalidad
     */
    public function getValorStr()
    {
        if($this->valor !== null){
            return $this->valor->getModalidad();
        }
        return null;
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
