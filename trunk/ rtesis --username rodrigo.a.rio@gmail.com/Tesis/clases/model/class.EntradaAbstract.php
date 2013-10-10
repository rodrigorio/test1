<?php

/**
 * @author Matias Velilla
 *
 * Cuando se guardan las entradas se descartan los objetivos !!
 *
 * los objetivos se guardan de forma paralela aunque en la vista aparezca toda
 * la info junta respecto a una fecha especifica.
 *
 */
abstract class EntradaAbstract
{
    protected $iId;
    
    /**
     * Se asigna cuando se crean los objetos entrada.
     * No significa una referencia doble, se necesita para poder levantar on demand 
     * los conjuntos de objetivos y unidades
     */
    protected $iSeguimientoId;

    /**
     * Fecha en la que se creo una entrada (puede no coincidir con la fecha de la entrada)
     */
    protected $dFechaHoraCreacion;

    /**
     * Fecha de la entrada en calendario
     */
    protected $dFecha;

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

    public function getId()
    {
        return $this->iId;
    }

    public function setId($iId)
    {
        $this->iId = $iId;
        return $this;
    }

    public function getSeguimientoId()
    {
        return $this->iSeguimientoId;
    }

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

    public function getFechaHoraCreacion($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFechaHoraCreacion, "d/m/Y");
        }else{
            return $this->dFechaHoraCreacion;
        }
    }

    public function getFecha($format = false)
    {
        if($format){
            return Utils::fechaFormateada($this->dFecha, "d/m/Y");
        }else{
            return $this->dFecha;
        }
    }
    
    public function setFechaCreacion($dFechaHoraCreacion){
        $this->dFechaHoraCreacion = $dFechaHoraCreacion;
    }

    public function setFecha($dFecha){
        $this->dFecha = $dFecha;
    }

    /**
     * Establece fecha actual como fecha de la entrada
     */
    public function setFechaToday(){
        $this->dFecha = date('Y-m-d', time());
    }

    /**
     * Las unidades esporadicas no se levantan en este metodo
     */
    public function getUnidades()
    {
        if(null === $this->aUnidades){
            $this->aUnidades = SeguimientosController::getInstance()->getUnidadesByEntrada($this);
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