<?php

/**
 * SeguimientoPersonalizado
 *
 * @author Andres
 */
class SeguimientoPersonalizado extends SeguimientoAbstract{

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

    public function isSeguimientoPersonalizado(){ return true; }

    /**
     *
     * Devuelve objetivos con evolucion completa si es que la tienen.
     * 
     */
    public function getObjetivos(){
        if($this->aObjetivos === null){
            $this->aObjetivos = SeguimientosController::getInstance()->getObjetivosPersonalizados($this->iId);
        }
        return $this->aObjetivos;
    }

    public function setObjetivos($aObjetivosPersonalizados)
    {
    	$this->aObjetivos = $aObjetivosPersonalizados;
        return $this;
    }

    public function addObjetivo($oObjetivoPersonalizado){
        $this->aObjetivos[] = $oObjetivoPersonalizado;
        return $this;
    }

    public function setDiagnostico($oDiagnosticoPersonalizado){
    	$this->oDiagnostico = $oDiagnosticoPersonalizado;
    }

    public function getDiagnostico(){
    	if(!$this->oDiagnostico){
            $this->oDiagnostico = SeguimientosController::getInstance()->getDiagnosticoPersonalizadoBySeguimientoId($this->iId);
    	}
    	return $this->oDiagnostico;
    }
}
