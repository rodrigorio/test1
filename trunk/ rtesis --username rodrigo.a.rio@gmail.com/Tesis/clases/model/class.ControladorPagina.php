<?php
/*
 * IMPORTANTE: esta clase no la usa para nada el front controller, solo se usa en el administrador
 * para operar con los permisos y los parametros del sistema.
 * 
 */
class ControladorPagina
{
    private $iId;
    private $sKey;
       
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

    public function setKey($sKey)
    {
        $this->sKey = $sKey;
    }
    public function getKey()
    {
        return $this->sKey;
    }
}