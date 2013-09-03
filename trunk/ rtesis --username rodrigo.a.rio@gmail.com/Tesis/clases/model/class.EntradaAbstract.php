<?php

/**
 * @author Rodrigo A. Rio
 *
 * Cuando se guardan las entradas se descartan los objetivos !!
 *
 * los objetivos se guardan de forma paralela aunque en la vista aparezca toda
 * la info junta respecto a una fecha especifica.
 *
 */
abstract class EntradaAbstract
{
    /**
     * Se asigna cuando se crean los objetos entrada.
     * No significa una referencia doble, se necesita para poder levantar on demand 
     * los conjuntos de objetivos y unidades
     */
    protected $iSeguimientoId;
    protected $dFecha;
    protected $aObjetivos = null;
    protected $aUnidades = null;
    
    /**
     * La idea es que en el constructor de la entrada si el periodo de expiracion es menor
     * se setee en falso
     */
    protected $isEditable = true;
    
    protected function __construct(){}

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

    /**
     * Las unidades esporadicas no se levantan en este metodo
     */
    public function getUnidades()
    {
        if(null === $this->aUnidades){
            $this->aUnidades = SeguimientosController::getInstance()->getUnidadesByEntrada($this->iSeguimientoId, $this->dFecha);
        }
        return $this->aUnidades;
    }

    /**
     * No se deberian setear unidades marcadas como de edicion esporadica
     */
    public function setUnidades($aUnidades){
        $this->aUnidades = $aUnidades;
    }

    public function setObjetivos($aObjetivos){
        $this->aObjetivos = $aObjetivos;
    }

    abstract public function getObjetivos();
}