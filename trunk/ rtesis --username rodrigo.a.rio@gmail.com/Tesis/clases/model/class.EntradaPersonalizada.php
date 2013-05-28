<?php

/**
 * @author Rodrigo A. Rio 
 */
class EntradaPersonalizada extends EntradaAbstract
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

        $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EDICION_SEGUIMIENTOS');
        //si cant dias expiracion es menor que fecha actual - fecha entrada entonces no se puede editar
        if(true){ $this->isEditable = false; }
    }

    public function getObjetivos()
    {
        if(null === $this->aObjetivos){
            $this->aObjetivos = SeguimientosController::getInstance()->getObjetivosPersonalizadosByEntrada($this->iSeguimientoId, $this->dFecha);
        }
        return $this->aObjetivos;
    }    
}



