<?php

/**
 * Podria haber hecho clases asociativas pero mejor asi por cuestiones de practicidad.
 */
class ParametroUsuario extends Parametro
{
    private $sValor;

    /**
     * Seria el id de usuario de la tabla NxN que relaciona parametros con usuarios.
     */
    private $iGrupoId;

    /**
     * El string que distingue la entidad a la que esta asociado el parametro
     * (el nombre del usuario)
     */
    private $sGrupo;

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

    public function setValor($sValor)
    {
        $this->sValor = $sValor;
    }

    public function getValor()
    {
        return $this->sValor;
    }

    public function getGrupoId()
    {
        return $this->iGrupoId;
    }

    public function setGrupoId($iGrupoId)
    {
        $this->iGrupoId = $iGrupoId;
    }

    public function getGrupo()
    {
        return $this->sGrupo;
    }

    public function setGrupo($sGrupo)
    {
        $this->sGrupo = $sGrupo;
    }
}