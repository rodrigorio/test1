<?php

/**
 * Solo se usa en el administrador para ABM de parametros en DB
 * para utilizar los parametros propiamente dichos en los page controllers de las vistas se utiliza el
 * metodo obtener del plugin de parametros.
 */
class Parametro
{
   /**
    * Los valores tienen que corresponder con el enum de la tabla
    */
    const TIPO_BOOLEANO = "boolean";
    const TIPO_NUMERICO = "numeric";
    const TIPO_CADENA = "string";
    
    private $iId;
    private $sDescripcion;
    private $sTipo;   
    /**
     * Key o Nombre
     */
    private $sNamespace;
    
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
    public function getId()
    {
        return $this->iId;
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
}