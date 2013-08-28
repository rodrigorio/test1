<?php

/**
 * ObjetivoAbstract
 *
 * @author Andres
 */
abstract class ObjetivoAbstract
{
    protected $sDescripcion;    
    protected $oRelevancia;
    protected $dEstimacion = null;
    protected $bActivo = true;

    /**
     * array de objetos evolucion, se generan a lo largo de las entradas por fecha
     * se cargan a demanda, para evitar sobre carga de consultas.
     */
    protected $aEvolucion = null;
    
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

    /**
     *  porque el objetivo personaliado tiene id normal, pero el scc es clave compuesta
     */
    abstract public function getId();

    public function getDescripcion($nl2br = false){
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