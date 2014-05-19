<?php

abstract class PreguntaAbstract
{
    protected $iId;
    protected $sDescripcion;
    protected $dFecha;
    protected $respuesta = null;
    protected $iOrden;

    public function isPreguntaMC(){ return false; }
    public function isPreguntaAbierta(){ return false; }

    /**
 	 *  @param int $iId
	 */
	public function setId($iId){
		$this->iId = (int)$iId;
	}

	/**
	 * @param string $sDescripcion
	 */
	public function setDescripcion($sDescripcion){
		$this->sDescripcion = $sDescripcion;
	}

	/**
	 *  @return int $iId
	 */
	public function getId(){
		return $this->iId ;
	}

    /**
     * @return string $sDescripcion
     */
    public function getDescripcion(){
        return $this->sDescripcion;
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

    public function getOrden()
    {
        return $this->iOrden;
    }

    public function setOrden($iOrden)
    {
        $this->iOrden = $iOrden;
    }
}
