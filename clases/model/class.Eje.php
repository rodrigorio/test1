<?php

/**
 * Ejes para objetivos personalizados, tienen jerarquia de un nivel.
 *
 * Se tratan como lista con sublistas (solo 2 niveles y no se puede tener mas de un padre)
 */
class Eje{
    
    private $iId;
    private $sDescripcion;

    /**
     * Los ejes padres no se pueden asociar a un objetivo personalizado.
     * Por lo tanto, si el Eje esta asociado a un objetivo este atributo permanece null.
     * Solo tendra la lista de ejes cuando se pida TODA la lista de ejes completa.
     */
    private $aSubEjes = null;

    /**
     * Guarda una instancia de la clase Eje correspondiente al padre, si es que lo posee.
     */
    private $oEjePadre = null;
    
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
     *  @param array $aSubejes
     */
    public function setSubEjes($aSubEjes){
        $this->aSubEjes = $aSubEjes;
    }

    public function addSubEje($oEje){
        $this->aSubEjes[] = $oEje;
        return $this;
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
    
    /**
     *  @param array $aSubEjes
     */
    public function getSubejes(){
        return $this->aSubEjes;
    }
    
    public function getEjePadre(){
        return $this->oEjePadre;
    }

    public function setEjePadre($oEje){
        $this->oEjePadre = $oEje;
        return $this;
    }
}