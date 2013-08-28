<?php

class ObjetivoAprendizaje extends ObjetivoAbstract{
	
    private $oEjeTematico;
    /**
     * Clave compuesta: objetivo aprendizaje y seguimiento scc
     *
     * ejemplo: array("iObjetivoAprendizajeId" => 1, "iSeguimientoSCCId" => 2);
     */
    private $aId;
    
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

    public function getId()
    {
        return $this->aId;
    }

    public function setId($iObjetivoAprendizajeId, $iSeguimientoSCCId)
    {
        $this->aId = array("iObjetivoAprendizajeId" => $iObjetivoAprendizajeId,
                            "iSeguimientoSCCId" => $iSeguimientoSCCId);
    }
    
    public function setEjeTematico($oEjeTematico){
        $this->oEjeTematico = $oEjeTematico;
    }

    public function getEjeTematico(){
        return $this->oEjeTematico;
    }

    public function getEvolucion()
    {
    	if($this->aEvolucion === null){
            $this->aEvolucion = SeguimientosController::getInstance()->obtenerEvolucionObjetivoScc($this->getId());
    	}
        return $this->aEvolucion;
    }

    /**
     * retorna 1 objeto evolucion correspondiente a la fecha, si no existe = null
     */
    public function getEvolucionByDate($dFecha)
    {
        return SeguimientosController::getInstance()->obtenerEvolucionObjetivoSccByDate($this->getId(), $dFecha);
    }
}