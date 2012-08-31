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

    public function setDescripcion($sDescripcion)
    {
        $this->sDescripcion = $sDescripcion;
    }
    public function getDescripcion()
    {
        return $this->sDescripcion;
    }

    public function setNamespace($sNamespace)
    {
        $this->sNamespace = $sNamespace;
    }
    public function getNamespace()
    {
        return $this->sNamespace;
    }

    public function getTipo()
    {
        return $this->sTipo;
    }
    public function setTipoNumerico()
    {
        $this->sTipo = self::TIPO_NUMERICO;
        return $this;
    }
    public function setTipoBooleano()
    {
        $this->sTipo = self::TIPO_BOOLEANO;
        return $this;
    }
    public function setTipoCadena()
    {
        $this->sTipo = self::TIPO_CADENA;
        return $this;
    }    
}