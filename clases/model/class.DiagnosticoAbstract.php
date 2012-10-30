<?php

abstract class DiagnosticoAbstract
{
    protected $iId;
    protected $sDescripcion;

    public function __construct(){}
    
    public function isDiagnosticoPersonalizado(){ return false; }
    public function isDiagnosticoSCC(){ return false; }

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
    public function getDescripcion($nl2br = false){
        if($nl2br){
            return nl2br($this->sDescripcion);
        }else{
            return $this->sDescripcion;
        }
    }
}