<?php

class Informe
{
    private $oUsuario = null;
    private $iUsuarioId;

    /* cada usuario tiene un objeto ConfiguracionInforme se tiene que setear antes de exportar */
    private $oConfiguracion;

    public function __construct(stdClass $oParams = null) {
        $vArray = get_object_vars($oParams);
        $vThisVars = get_class_vars(__CLASS__);
        if (is_array($vArray)) {
            foreach ($vArray as $varName => $value) {
                if (array_key_exists($varName, $vThisVars)) {
                    $this->$varName = $value;
                } else {
                    throw new Exception("Unknown property $varName in " . __CLASS__, -1);
                }
            }
        }
    }

    public function setConfiguracion($oConfiguracion){
        $this->oConfiguracion = $oConfiguracion;
    }

    public function setUsuario($oUsuario){
        $this->oUsuario = $oUsuario;
    }

    public function getUsuario(){
        return $this->oUsuario;
    }

    public function setUsuarioId($iUsuarioId){
        $this->iUsuarioId = $iUsuarioId;
        if(!empty($iUsuarioId) && null !== $this->oUsuario && $this->oUsuario->getId() != $iUsuarioId){
            $this->oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
        }
    }

    public function getUsuarioId()
    {
        if($this->iUsuarioId === null && $this->oUsuario !== null){
            return $this->oUsuario->getId();
        }
        return $this->iUsuarioId;
    }

    /**
     * @return string $sTitulo
     */
    public function getTitulo($nl2br = false){
        return $this->oConfiguracion->getTitulo($nl2br);
    }

    /**
     * @return string $sSubtitulo
     */
    public function getSubtitulo($nl2br = false){
        return $this->oConfiguracion->getSubtitulo($nl2br);
    }

    /**
     * @return string $sPie
     */
    public function getPie($nl2br = false){
        return $this->oConfiguracion->getPie($nl2br);
    }
}
