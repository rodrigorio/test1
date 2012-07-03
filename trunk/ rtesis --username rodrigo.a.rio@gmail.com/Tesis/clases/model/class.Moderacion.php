<?php

class Moderacion
{
   /**
    * Los valores tienen que corresponder con el enum de la tabla
    */
    const ESTADO_RECHAZADO = "rechazado";
    const ESTADO_APROBADO = "aprobado";
    const ESTADO_PENDIENTE = "pendiente";

    private $iId;
    private $dFecha;
    private $sMensaje;
    private $sEstado = self::ESTADO_PENDIENTE;

   /**
     *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase
     * @param stdClass $oParams
     */
    public function __construct(stdClass $oParams = null){
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

    public function setId($iId)
    {
        $this->iId = $iId;
    }

    public function setFecha($dFecha)
    {
        $this->dFecha = $dFecha;
    }

    public function setMensaje($sMensaje)
    {
        $this->sMensaje = $sMensaje;
        return $this;
    }
    
    public function getId()
    {
        return $this->iId;
    }

    public function getFecha($format = false)
    {
        if($format){
            return Utils::fechaFormateada($this->dFecha);
        }else{
            return $this->dFecha;
        }
    }

    public function getMensaje($nl2br = false)
    {
        if($nl2br){
            return nl2br($this->sMensaje);
        }else{
            return $this->sMensaje;
        }
    }

    public function getEstado()
    {
        return $this->sEstado;
    }

    public function setEstadoPendiente()
    {
        $this->sEstado = self::ESTADO_PENDIENTE;
        return $this;
    }

    public function setEstadoAprobado()
    {
        $this->sEstado = self::ESTADO_APROBADO;
        return $this;
    }

    public function setEstadoRechazado()
    {
        $this->sEstado = self::ESTADO_RECHAZADO;
        return $this;
    }

    public function isAprobado()
    {
        return ($this->sEstado == self::ESTADO_APROBADO)?true:false;
    }

    public function isRechazado()
    {
        return ($this->sEstado == self::ESTADO_RECHAZADO)?true:false;
    }

    public function isPendiente()
    {
        return ($this->sEstado == self::ESTADO_PENDIENTE)?true:false;
    }
}