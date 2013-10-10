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

    protected $dFechaHora;

    protected $aObjetivos = null;

    protected $aUnidades = null;
    
    /**
     * La idea es que en el constructor de la entrada si el periodo de expiracion es menor
     * se setee en falso
     */
    protected $bEditable = true;

    /**
     * Indica si la entrada se salvo al menos 1 vez.
     */
    protected $bGuardada = false;
    
    protected function __construct(){}

    public function isEditable($flag = null){
        if(null !== $flag){
            $this->bEditable = $flag ? true : false;
            return $this;
        }else{
            return $this->bEditable;
        }
    }

    /**
     * Se guardo al menos una vez ?
     */
    public function isGuardada($flag = null){
        if(null !== $flag){
            $this->bGuardada = $flag ? true : false;
            return $this;
        }else{
            return $this->bGuardada;
        }
    }

    public function getFechaHora($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFechaHora, "d/m/Y");
        }else{
            return $this->dFechaHora;
        }
    }

    /**
     * Devuelve solo la parte de la fecha y formateada
     */
    public function getFecha($format = false)
    {
        if($format){
            $dFechaFormat = Utils::fechaFormateada($this->dFechaHora, "d/m/Y");
            return strtok($dFechaFormat, " ");
        }else{
            return strtok($this->dFechaHora, " ");
        }
    }

    public function setFecha($dFechaHora){
        $this->dFechaHora = $dFechaHora;
    }

    /**
     * Las unidades esporadicas no se levantan en este metodo
     */
    public function getUnidades()
    {
        if(null === $this->aUnidades){
            $this->aUnidades = SeguimientosController::getInstance()->getUnidadesByEntrada($this->iSeguimientoId, $this->dFechaHora);
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