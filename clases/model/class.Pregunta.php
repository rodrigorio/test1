<?php

 /**
 * @author Andres
 *
 */
class Pregunta extends PreguntaAbstract{

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
     *  @param string $sRespuesta
     */
    public function setRespuesta($sRespuesta){
        if(empty($sRespuesta)){ $sRespuesta = null; }
        $this->respuesta = $sRespuesta;
    }

    /**
     * @return string $sRespuesta
     */
    public function getRespuesta($nl2br = false){
        if($nl2br && null !== $this->respuesta){
            return nl2br($this->respuesta);
        }else{
            return $this->respuesta;
        }
    }
}
