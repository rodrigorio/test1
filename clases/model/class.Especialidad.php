<?php

class Especialidad {
    private $iId;
    private $sNombre;
    private $sDescripcion;
	
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
    public function getDescripcion($nl2br = false){
        if($nl2br){
            return nl2br($this->sDescripcion);
        }else{
            return $this->sDescripcion;
        }
    }
}