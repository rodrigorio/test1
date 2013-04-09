<?php

/**
 * Modalidad para una variable cualitativa. Por ejemplo para una variable "comportamiento"
 * las modalidades podrian ser "malo" "bueno" "excelente"
 * cada una de las opciones es un objeto de esta clase que se prensenta como array de objetos en los objetos VariablesCualitativas
 */
class Modalidad
{
    private $iId;
    private $sModalidad;
    private $iOrden;
	
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

    public function setModalidad($sModalidad){
        $this->sModalidad = $sModalidad;
    }

    /**
     *  @return int $iId
     */
    public function getId(){
        return $this->iId ;
    }

    /**
     * @return string $sModalidad
     */
    public function getModalidad(){
        return $this->sModalidad;
    }

    public function getOrden()
    {
        return $this->iOrden;
    }

    public function setOrden($iOrden)
    {
        $this->iOrden = $iOrden;
    }
}