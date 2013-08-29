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
    protected $oRelevancia = null;
    protected $dEstimacion = null;
    protected $bActivo = true;

    /**
     * array de objetos evolucion, se generan a lo largo de las entradas por fecha
     * se cargan a demanda, para evitar sobre carga de consultas.
     */
    protected $aEvolucion = null;

    public function isObjetivoPersonalizado(){ return false; }
    public function isObjetivoAprendizaje(){ return false; }
    
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
    public function getEstimacion(){
        return $this->dEstimacion;
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

    abstract public function getEvolucion();
    
    /**
     * retorna 1 objeto evolucion correspondiente a la fecha, si no existe = null
     */
    abstract public function getEvolucionByDate($dFecha);
   
    public function setEvolucion($aEvolucion){
        $this->aEvolucion = $aEvolucion;
        return $this;
    }

    public function addEvolucion($oEvolucion)
    {
        $this->aEvolucion[] = $oEvolucion;
        return $this;
    }
}