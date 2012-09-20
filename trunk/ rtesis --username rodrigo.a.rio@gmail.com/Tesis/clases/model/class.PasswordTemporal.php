<?php

class PasswordTemporal
{
    /**
     * este atributo no se guarda en base de datos
     */
    private $sPassword;
    private $sPasswordMd5;
    private $sToken;
    private $dFecha;
    private $sEmail;
    
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
    
    public function getPassword()
    {
        return $this->sPassword;
    }

    public function getPasswordMd5()
    {
        return $this->sPasswordMd5;
    }

    public function getToken()
    {
        return $this->sToken;
    }

    public function getEmail()
    {
        return $this->sEmail;
    }

    public function getFecha($format = false){
        if($format){
            return Utils::fechaFormateada($this->dFecha);
        }else{
            return $this->dFecha;
        }
    }

    public function setPassword($sPassword)
    {
        $this->sPassword = $sPassword;
    }

    public function setPasswordMd5($sPasswordMd5)
    {
        $this->sPasswordMd5 = $sPasswordMd5;
    }

    public function setToken($sToken)
    {
        $this->sToken = $sToken;
    }

    public function setEmail($sEmail)
    {
        $this->sEmail = $sEmail;
    }

    public function setFecha($dFecha)
    {
        $this->dFecha = $dFecha;
    }
}
