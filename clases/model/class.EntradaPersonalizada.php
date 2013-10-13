<?php

/**
 * @author Matias Velilla
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
    }

    //en el array de 'evolucion' tendria que estar asociado a los objetivos el objeto evolucion de la fecha de la entrada!!
    public function getObjetivos()
    {
        if(null === $this->aObjetivos){
            $this->aObjetivos = SeguimientosController::getInstance()->getObjetivosPersonalizadosByEntrada($this->iSeguimientoId, $this->dFechaHoraCreacion);
        }
        return $this->aObjetivos;
    }    
}



