<?php

/**
 * @author Rodrigo A. Rio
 *
 *
 */
class Entrada
{
    protected $dFecha;
    protected $vObjetivos;
    protected $vUnidades;
    
    public function __construct(stdClass $oParams = null){
        parent::__construct();

        $vArray = get_object_vars($oParams);
        $vThisVars = get_class_vars(__CLASS__);
        if(is_array($vArray)){
            foreach($vArray as $varName => $value){
                if(array_key_exists($varName,$vThisVars)){
                    $this->$varName = $value;
                }else{
                    throw new Exception("Unknown property $varName in "  . __CLASS__,-1);
                }
            }
        }

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

	public function getUnidades()
    {
        if($this->vUnidades === null){
         //   $this->vUnidades = SeguimientosController::getInstance()->
        }
        return $this->vUnidades;
    }

    public function setUnidades($vUnidades){
        $this->vUnidades = $vUnidades;
    }
}