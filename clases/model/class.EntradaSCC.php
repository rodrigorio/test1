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

        $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EDICION_SEGUIMIENTOS');
        //si cant dias expiracion < (fecha actual - fecha entrada) entonces no se puede editar
        $dayDiff = Utils::dateDiffDays($this->dFechaHora, date("Y-m-d H:i:s")); //ejemplo: '2009-12-20 20:12:10' que es la hora q viene desde el SQL
        if($cantDiasExpiracion < $dayDiff ){ $this->isEditable = false; }
    }

    public function getObjetivos()
    {                
        if(null === $this->aObjetivos){
            $this->aObjetivos = SeguimientosController::getInstance()->getObjetivosAprendizajeByEntrada($this->iSeguimientoId, $this->dFechaHora);
        }
        return $this->aObjetivos;
    } 
}