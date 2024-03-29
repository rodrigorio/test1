<?php

/**
 * SeguimientoSCC
 *
 * @author Andres
 */
class SeguimientoSCC extends SeguimientoAbstract
{
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

    public function isSeguimientoSCC(){ return true; }

    /**
     *
     * Devuelve objetivos con evolucion completa si es que la tienen.
     *
     */
    public function getObjetivos($sOrderBy = null, $sOrder = null){
        if($this->aObjetivos === null){
            $this->aObjetivos = SeguimientosController::getInstance()->getObjetivosAprendizajeAsociadosSeguimientoScc($this->iId, $sOrderBy, $sOrder);
        }
        return $this->aObjetivos;
    }
    
    public function setObjetivos($aObjetivosAprendizaje)
    {
    	$this->aObjetivos = $aObjetivosAprendizaje;
        return $this;
    }

    public function addObjetivo($oObjetivoAprendizaje){
        $this->aObjetivos[] = $oObjetivoAprendizaje;
        return $this;
    }

    public function setDiagnostico($oDiagnosticoSCC){
    	$this->oDiagnostico = $oDiagnosticoSCC;
    }

    public function getDiagnostico(){
    	return $this->oDiagnostico;
    }
}