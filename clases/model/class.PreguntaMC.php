<?php

/**
 * @author Andres
 *
 */
class PreguntaMC extends PreguntaAbstract
{
    private $aOpciones = null;

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

    public function isPreguntaMC(){ return true; }

    public function setRespuesta($oOpcion){
        $this->respuesta = $oOpcion;
        return $this;
    }

   /**
    * @return Opcion corresponde a la respuesta marcada
    */
    public function getRespuesta(){
        return $this->respuesta;
    }

    /**
     * Si el valor !== null entonces devuelve el string que describe la opcion seleccionada como respuesta
     */
    public function getRespuestaStr()
    {
        if($this->respuesta !== null){
            return $this->respuesta->getDescripcion();
        }
        return null;
    }

    public function getOpciones()
    {
        return $this->aOpciones;
    }

    public function setOpciones($aOpciones)
    {
        $this->aOpciones = $aOpciones;
    }

    public function addOpcion($oOpcion){
        $this->aOpciones[] = $oOpcion;
    }
}
