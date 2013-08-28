<?php

class Evolucion {
    private $iId;
    private $fProgreso;
    private $sComentarios;
    private $dFechaHora;
	
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

    /**
     *  @param int $iId
     */
    public function setId($iId){
            $this->iId = (int)$iId;
    }
    /**
     * @param string $sDescripcion
     */
    public function setComentarios($sComentarios){
            $this->sComentarios = $sComentarios;
    }
    /**
     *  @return int $iId
     */
    public function getId(){
            return $this->iId ;
    }
    /**
     * @return string $sComentarios
     */
    public function getComentarios($nl2br = false){
        if($nl2br){
            return nl2br($this->sComentarios);
        }else{
            return $this->sComentarios;
        }
    }

    public function setFechaHora($dFechaHora){
        $this->dFechaHora = $dFechaHora;
        return $this;
    }
    
    public function getFechaHora($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFechaHora);
        }else{
            return $this->dFechaHora;
        }
    }

    public function setProgreso($fProgreso)
    {
        $this->fProgreso = $fProgreso;
        return $this;
    }

    public function getProgreso()
    {
        return $this->fProgreso;
    }

}