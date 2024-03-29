<?php

class ObjetivoAprendizaje extends ObjetivoAbstract{
	
    private $oEjeTematico;
    
    /**
     * Necesario porque es clave compuesta la asociacion entre objetivo y seguimiento
     * (para obtener la evolucion a demanda, es auxiliar. para guardar una asociacion etc se pasa el seguimientoId por parametro
     * en el metodo de controlador)
     */
    private $iSeguimientoSCCId = null;
    
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

    public function isObjetivoAprendizaje(){ return true; }

    public function getSeguimientoSCCId(){
        return $this->iSeguimientoSCCId;
    }

    public function setSeguimientoSCCId($iSeguimientoId){
        $this->iSeguimientoSCCId = $iSeguimientoId;
    }
    
    public function setEjeTematico($oEjeTematico){
        $this->oEjeTematico = $oEjeTematico;
    }

    public function getEje(){
        return $this->oEjeTematico;
    }

    public function getEvolucion()
    {
    	if($this->aEvolucion === null){
            $this->aEvolucion = SeguimientosController::getInstance()->obtenerEvolucionObjetivoScc($this->iId, $this->iSeguimientoSCCId);
    	}
        return $this->aEvolucion;
    }

    /**
     * retorna 1 objeto evolucion correspondiente a la fecha, si no existe = null
     */
    public function getEvolucionByDate($dFecha)
    {
        return SeguimientosController::getInstance()->obtenerEvolucionObjetivoSccByDate($this->iId, $this->iSeguimientoSCCId, $dFecha);
    }

    public function getUltimaEvolucionToDate($dFecha)
    {
        return SeguimientosController::getInstance()->obtenerEvolucionObjetivoSccToDate($this->iId, $this->iSeguimientoSCCId, $dFecha);
    }
}