<?php
 /**
 * @author Andres
 * El tipo de pregunta es: "preguntas simples" o "preguntas con opciones o Multiples Choice"
 */
class Pregunta{
    
    protected  $iId;
    protected  $sDescripcion;
    private    $sRespuesta = null;     
      

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
     * En la clase PreguntaMC es redeclarada para devolver true.
     */
    public function isPreguntaMC(){ return false; }
     
    
    /**
     *  @param int $iId
     */
    public function setId($iId){
            $this->iId = (int)$iId;
    }
    /**
     * @param string $sDescripcion
     */
    public function setDescripcion($sDescripcion){
            $this->sDescripcion = $sDesccripcion;
    }
     /**
     * @param string $sTipo 
     * El tipo de pregunta es: "preguntas simples" o "preguntas con opciones"
     */
    public function setTipo($sTipo){
            $this->sTipo = $sTipo;
    }
     /**
     * @param string $sRespuesta
     * El campo respuesta por defecto esta en null, cuando el tipo toma el valor pregunta simple
     *  se coloca la respuesta en este campo
     */
    public function setRespuesta($sRespuesta){
            $this->sRespuesta = $sRespuesta;
    }
   
    /**
     *  @return int $iId
     */
    public function getId(){
        return $this->iId ;
    }

    /**
     * @return string $sDescripcion
     */
    public function getDescripcion(){
        return $this->sDescripcion;
    }
     /**
     * @return string $sRespuesta
     */
    public function getRespuesta(){
        return $this->sRespuesta;
    }  
    
         
}