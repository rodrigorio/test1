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
    protected $oObjetivoRelevancia;
    protected $dEstimacion = null;
    protected $bActivo = true;

    /**
     * array de objetos evolucion, se generan a lo largo de las entradas por fecha
     * aparece != cuando el objetivo esta asociado a una entrada por fecha o cuando esta asociado
     * a un seguimiento y se quiere consultar la evolucion en un objetivo
     */
    protected $aEvolucion = null;
    
    /**
     *  @param int $iId
     */
    public function setId($iId){
        $this->iId = (int)$iId;
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
     *  @param  $oObjetivoRelevancia
     *  
     */
    public function setObjetivoRelevancia($oObjetivoRelevancia){
        $this->oObjetivoRelevancia = $oObjetivoRelevancia;
    }

    /**
     *  @return int $iId
     */
    public function getId(){
        return $this->iId;
    }

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
     *  @return  $oObjetivoRelevancia
     */
    public function getObjetivoRelevancia(){
        return $this->oObjetivoRelevancia;
    }

    public function isActivo($flag = null){
        if(null !== $flag){
            $this->bActivo = $flag ? true : false;
            return $this;
        }else{
            return $this->bActivo;
        }
    }

    /**
     * Puede que devuelva solo un objeto en el array si se esta levantando un objetivo
     * para una entrada por fecha (objeto evolucion correspondiente a la fecha de la entrada)
     */
    public function getEvolucion()
    {
        return $this->aEvolucion;
    }
   
    public function setEvolucion($aEvolucion){
        $this->aEvolucion = $aEvolucion;
        return $this;
    }
}