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
    const EDICION_REGULAR = "regular";
    const EDICION_ESPORADICA = "esporadica";

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
     * Si expiro el plazo de expiracion devuelve falso
     */
    protected $bEditable = null;

    /**
     * Indica si la entrada se salvo al menos 1 vez.
     */
    protected $bGuardada = false;

    protected $eTipoEdicion = self::EDICION_REGULAR;
    
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

    public function setEdicionRegular()
    {
        $this->eTipoEdicion = self::EDICION_REGULAR;
        return $this;
    }

    public function setEdicionEsporadica()
    {
        $this->eTipoEdicion = self::EDICION_ESPORADICA;
        return $this;
    }

    public function isEsporadica(){
        return $this->eTipoEdicion == self::EDICION_ESPORADICA ? true:false;
    }

    public function isRegular(){
        return $this->eTipoEdicion == self::EDICION_REGULAR ? true:false;
    }

    public function getTipoEdicion(){
        return $this->eTipoEdicion;
    }

    public function isEditable($flag = null){
        if(null !== $flag){
            $this->bEditable = $flag ? true : false;
            return $this;
        }else{
            if(null === $this->bEditable){
                if(SeguimientosController::getInstance()->isEntidadEditable($this->dFechaHoraCreacion)){
                    $this->bEditable = true;
                }else{
                    $this->bEditable = false;
                }
            }
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

    public function addUnidad($oUnidad){
        $this->aUnidades[] = $oUnidad;
    }

    public function setObjetivos($aObjetivos){
        $this->aObjetivos = $aObjetivos;
    }

    abstract public function getObjetivos();
}