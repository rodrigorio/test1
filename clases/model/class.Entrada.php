<?php

/**
 * @author Rodrigo A. Rio 
 */
class Entrada
{
    /**
     * Se asigna cuando se crean los objetos entrada.
     * No significa una referencia doble, se necesita para poder levantar on demand 
     * los conjuntos de objetivos y unidades
     */
    private $iSeguimientoId;
    private $dFecha;
    private $aObjetivos;
    private $aUnidades;
    
    /**
     * La idea es que en el constructor de la entrada si el periodo de expiracion es menor
     * se setee en falso
     */
    private $isEditable = true;
    
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

    public function getFecha($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFecha);
        }else{
            return $this->dFecha;
        }
    }

    public function setFecha($dFecha){
        $this->dFecha = $dFecha;
    }

    public function getUnidades()
    {
        if(null === $this->aUnidades){
            $this->aUnidades = SeguimientosController::getInstance()->getUnidadesByEntrada($this->iSeguimientoId, $this->dFecha);
        }
        return $this->aUnidades;
    }

    public function setUnidades($aUnidades){
        $this->aUnidades = $aUnidades;
    }

    public function getObjetivos()
    {
        if(null === $this->aObjetivos){
            $this->aObjetivos = SeguimientosController::getInstance()->getObjetivosByEntrada($this->iSeguimientoId, $this->dFecha);
        }
        return $this->aObjetivos;
    }

    public function setObjetivos($aObjetivos){
        $this->aObjetivos = $aObjetivos;
    }
}