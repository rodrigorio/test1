<?php

class InformeConfiguracion
{
    private $iUsuarioId;
    private $sTitulo = null;
    private $sSubtitulo = null;
    private $sPie = null;

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

    public function getUsuarioId(){
        return $this->iUsuarioId;
    }
    /**
     * @param string $sTitulo
     */
    public function setTitulo($sTitulo){
            $this->sTitulo = $sTitulo;
    }
    /**
     * @return string $sTitulo
     */
    public function getTitulo($nl2br = false){
        if($nl2br){
            return nl2br($this->sTitulo);
        }
        return $this->sTitulo;
    }

    /**
     * @param string $sSubtitulo
     */
    public function setSubtitulo($sSubtitulo){
            $this->sSubtitulo = $sSubtitulo;
    }
    /**
     * @return string $sSubtitulo
     */
    public function getSubtitulo($nl2br = false){
        if($nl2br){
            return nl2br($this->sSubtitulo);
        }
        return $this->sSubtitulo;
    }

    /**
     * @param string $sPie
     */
    public function setPie($sPie){
            $this->sPie = $sPie;
    }
    /**
     * @return string $sPie
     */
    public function getPie($nl2br = false){
        if($nl2br){
            return nl2br($this->sPie);
        }
        return $this->sPie;
    }
}
