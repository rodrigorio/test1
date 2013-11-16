<?php

class EjeTematico{
    
    private $iId;
    private $sDescripcion;
    private $oArea;
    
    /**
     * Esta es una descripcion del estado inicial que solo estara presente
     * cuando la instancia este asociada a un diagnostico SCC.
     */
    private $sEstadoInicial = null;
    
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
     *  @param string $sDescripcion
     */
    public function setDescripcion($sDescripcion){
        $this->sDescripcion = $sDescripcion;
    }

    /**
     *  @return int $iId
     */
    public function getId(){
        return $this->iId;
    }
    
    /**
     *  @return string $sDescripcion
     */
    public function getDescripcion(){
        return $this->sDescripcion;
    }

    public function getArea()
    {
        return $this->oArea;
    }

    public function setArea($oArea)
    {
        $this->oArea = $oArea;
    }

    public function getEstadoInicial($nl2br = false){
        if($nl2br){
            return nl2br($this->sEstadoInicial);
        }else{
            return $this->sEstadoInicial;
        }
    }

    public function setEstadoInicial($sEstadoInicial)
    {
        $this->sEstadoInicial = $sEstadoInicial;
    }
}