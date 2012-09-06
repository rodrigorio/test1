<?php

class Denuncia
{
   /**
    * Los valores tienen que corresponder con el enum de la tabla
    */
    const RAZON_INFO_FALSA = "informacion_falsa";
    const RAZON_CONTENIDO_INAPROPIADO = "contenido_inapropiado";
    const RAZON_PROPIEDAD_INTELECTUAL = "propiedad_intelectual";
    const RAZON_SPAM = "spam";

    private $iId;
    private $dFecha;
    private $sMensaje;
    private $sRazon;
    private $oUsuario;
    
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

    public function getRazon()
    {
        return $this->sRazon;
    }

    public function setRazonSpam()
    {
        $this->sRazon = self::RAZON_SPAM;
        return $this;
    }

    public function setRazonPropiedadIntelectual()
    {
        $this->sRazon = self::RAZON_PROPIEDAD_INTELECTUAL;
        return $this;
    }

    public function setRazonContenidoInapropiado()
    {
        $this->sRazon = self::RAZON_CONTENIDO_INAPROPIADO;
        return $this;
    }
    
    public function setRazonInformacionFalsa()
    {
        $this->sRazon = self::RAZON_INFO_FALSA;
        return $this;
    }

    public function getUsuario(){
    	if($this->oUsuario == null && !empty($this->iUsuarioId)){
            $this->oUsuario = ComunidadController::getInstance()->getUsuarioById($this->iUsuarioId);
    	}
        return $this->oUsuario;
    }

    public function getUsuarioId()
    {
        return $this->iUsuarioId;
    }

    public function setUsuarioId($iUsuarioId){
        $this->iUsuarioId = $iUsuarioId;
        if(!empty($iUsuarioId) && null !== $this->oUsuario && $this->oUsuario->getId() != $iUsuarioId){
            $this->oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
        }
    }

    public function setUsuario($oUsuario)
    {
        $this->oUsuario = $oUsuario;
        return $this;
    }
}