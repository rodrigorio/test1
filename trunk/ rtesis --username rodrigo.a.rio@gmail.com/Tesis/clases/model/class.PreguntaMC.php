<?php
/**
 * @author Andres
 * Esta clase es redefinida para guardar las preguntas cuando el tipo es Multiples Choise 
 */
class PreguntaMC extends Pregunta{
               
    private $oOpcion = null;
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
     /**
     * En la clase PreguntaMC es redeclarada para devolver true.
     */
    public function isPreguntaMC(){ return true; }
    /**
     * En este metodo como el tipo de pregunta es con opciones guardo el objeto opción.
     */
    public function setRespuesta($oOpcion){
        $this->oOpcion = $oOpcion;
        return $this;
    }
    /**
     * En este metodo como el tipo de pregunta es con opciones guardo el array de  opciones de la pregunta.
     */
    public function setOpciones($aOpciones){
        $this->aOpciones = $aOpciones;
        return $this;
    }
      /**
     * @return object $oOpcion
     * 
     * En este metodo como el tipo de pregunta es con opciones devuelvo el objeto opción.
     */
     
    public function getRespuesta(){
           
        return $this->oOpcion;
    }
        
     /**
     * @return array $aOpciones
     * 
     * Este metodo devuelve las opciones de esta pregunta a ser seleccionada.
     */
    public function getOpciones()
    {
        if($this->aOpciones === null){
            $this->aOpciones = SeguimientosController::getInstance()->getOpcionesByPreguntaId($this->iId);
        }
        return $this->aOpciones;
    }      
}