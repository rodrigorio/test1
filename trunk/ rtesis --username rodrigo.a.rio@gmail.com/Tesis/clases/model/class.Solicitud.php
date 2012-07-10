<?php

/**
 * Este objeto tiene que corresponder con las solicitudes para administrar algun tipo de entidad en el sistema.
 */
class Solicitud
{
    private $iId;
    private $oUsuario;
    private $sMensaje;
    private $dFecha;

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

    public function getId()
    {
        return $this->iId;
    }

    public function setUsuario($oUsuario)
    {
        $this->oUsuario = $oUsuario;
    }

    public function getUsuario()
    {
        return $this->oUsuario;
    }

    public function setMensaje($sMensaje)
    {
        $this->sMensaje = $sMensaje;
    }

    public function getMensaje($nl2br = false)
    {
        if($nl2br){
            return nl2br($this->sMensaje);
        }else{
            return $this->sMensaje;
        }
    }

    public function setFecha($dFecha){
        $this->dFecha = $dFecha;
        return $this;
    }

    public function getFecha($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFecha);
        }else{
            return $this->dFecha;
        }
    }
}