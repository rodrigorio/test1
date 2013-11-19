<?php

class Anio
{
    private $iId;
    private $sDescripcion;
    private $oCiclo;
	
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
     * @param string $sDescripcion
     */
    public function setDescripcion($sDescripcion){
        $this->sDescripcion = $sDescripcion;
    }

    /**
     * @param Ciclo $oCiclo
     */
    public function setCiclo($oCiclo){
        $this->oCiclo = $oCiclo;
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

    /**
     * @return Ciclo
     */
    public function getCiclo(){
        return $this->oCiclo;
    }
}