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
    protected $dEstimacion;
    protected $fEvolucion;
    protected $oObjetivoRelevancia;
    
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
     *  @param date $fEvolucion
     */
    public function setEvolucion($fEvolucion){
        $this->fEvolucion = $fEvolucion;
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
     *  @return date $fEvolucion
     */
    public function getEvolucion(){
        return $this->dEvolucion;
    }
    /**
     *  @return  $oObjetivoRelevancia
     */
    public function getObjetivoRelevancia(){
        return $this->oObjetivoRelevancia;
    }
}