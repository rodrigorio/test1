<?php

/**
 * ObjetivoAbstract
 *
 * @author Andres
 */
abstract class ObjetivoAbstract
{
    protected $iId;
    protected $sDescripcion;
    protected $dFechaCreacion;
    protected $oRelevancia = null;
    protected $dEstimacion = null;
    protected $bActivo = true;

    /**
     * Esto se setea cuando se levantan los objetos desde persistencia a traves de un metodo
     * que calcula el controlador.
     * Determina cuando un usuario integrante activo puede modificar el objetivo.
     * (debido al periodo de expiracion seteado por parametro)
     */
    protected $isEditable = true;

    /**
     * array de objetos evolucion, se generan a lo largo de las entradas por fecha
     * se cargan a demanda, para evitar sobre carga de consultas.
     */
    protected $aEvolucion = null;
    
    /**
     * este metodo es muy util para mejorar performance, aunque los objetivos no tengan 
     * el listado completo de evolucion van a tener de fabrica el objeto Evolucion de la ultima entrada por fecha
     */
    protected $oUltimaEvolucion = null;

    public function isObjetivoPersonalizado(){ return false; }
    public function isObjetivoAprendizaje(){ return false; }

    public function isEditable($flag = null){
        if(null !== $flag){
            $this->isEditable = $flag ? true : false;
            return $this;
        }else{
            return $this->isEditable;
        }
    }

    public function getFechaCreacion($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFechaCreacion, "d/m/Y");
        }else{
            return $this->dFechaCreacion;
        }
    }
    
    /**
     *  @param string $sDescripcion
     */
    public function setDescripcion($sDescripcion){
        $this->sDescripcion = $sDescripcion;
    }
    /**
     *  @param date $dEstimacion
     */
    public function setEstimacion($dEstimacion){
        $this->dEstimacion = $dEstimacion;
    }

    /**
     *  @param  $oRelevancia
     *  
     */
    public function setRelevancia($oRelevancia){
        $this->oRelevancia = $oRelevancia;
    }

    public function getId()
    {
        return $this->iId;
    }

    public function setId($iId)
    {
        $this->iId = $iId;
        return $this;
    }

    public function getDescripcion($nl2br = false)
    {
        if($nl2br){
            return nl2br($this->sDescripcion);
        }else{
            return $this->sDescripcion;
        }
    }
    
    /**
     *  @return date $dEstimacion
     */
    public function getEstimacion($format = false){
        if($format){
            return Utils::fechaFormateada($this->dEstimacion, "d/m/Y");
        }else{
            return $this->dEstimacion;
        }
    }

    /**
     * Compara con la fecha actual y determina si la fecha de estimacion esta vencida
     */
    public function isEstimacionVencida()
    {
        if(time() > strtotime($this->dEstimacion)){
            return true;
        }
        return false;
    }
    
    /**
     *  @return  $oRelevancia
     */
    public function getRelevancia(){
        return $this->oRelevancia;
    }

    public function isActivo($flag = null){
        if(null !== $flag){
            $this->bActivo = $flag ? true : false;
            return $this;
        }else{
            return $this->bActivo;
        }
    }

    public function isLogrado(){
        if(null !== $this->oUltimaEvolucion){
            return $this->oUltimaEvolucion->isObjetivoLogrado();
        }else{
            return false;
        }
    }

    abstract public function getEvolucion();
    abstract public function getEje();
    
    /**
     * retorna 1 objeto evolucion correspondiente a la fecha, si no existe = null
     */
    abstract public function getEvolucionByDate($dFecha);
   
    public function setEvolucion($aEvolucion){
        $this->aEvolucion = $aEvolucion;
        return $this;
    }

    public function getUltimaEvolucion()
    {
        return $this->oUltimaEvolucion;
    }

    public function addEvolucion($oEvolucion)
    {
        $this->aEvolucion[] = $oEvolucion;
        return $this;
    }
}