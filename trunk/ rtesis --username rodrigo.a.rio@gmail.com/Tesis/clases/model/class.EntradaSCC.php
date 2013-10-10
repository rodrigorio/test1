<?php

/**
 * @author Rodrigo A. Rio 
 */
class EntradaSCC extends EntradaAbstract
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

        if(!SeguimientosController::getInstance()->isEntidadEditable($this->$dFechaHoraCreacion)){
            $this->bEditable = false;
        }
    }

    public function getObjetivos()
    {                
        if(null === $this->aObjetivos){
            $this->aObjetivos = SeguimientosController::getInstance()->getObjetivosAprendizajeByEntrada($this->iSeguimientoId, $this->dFechaHoraCreacion);
        }
        return $this->aObjetivos;
    } 
}