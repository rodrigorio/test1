<?php

class Unidad{
    
    private $iId;
    private $sNombre;
    private $sDescripcion;
    private $bEditable;
    private $dFechaHora;
    private $bPorDefecto;

    /**
     * Cuando las variables son las de una unidad que esta asociada a un seguimiento
     * entonces estas deben tener el valor mas reciente.
     *
     * Tambien tiene que haber metodos para obtener las variables de una unidad
     * pero que los valores dependan de una fecha determinada.
     *
     * TODO on demand, porque depende de lo que se necesite en el momento, y es mucha info.
     *
     */
    private $aVariables;
			
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
	
    /**
     *  @param int $iId
     */
    public function setId($iId){
            $this->iId = (int)$iId;
    }

   /**
    * @param string $sNombre
    */
    public function setNombre($sNombre){
        $this->sNombre = $sNombre;
    }
    
    /**
     * @param string $sDescripcion
     */
    public function setDescripcion($sDescripcion){
            $this->sDescripcion = $sDescripcion;
    }

    /**
     *  @param int $dFechaHora
     */
    public function setFechaHora($dFechaHora){
            $this->dFechaHora = (int)$dFechaHora;
    }
	public function setVariables($aVariables){
        $this->aVariables = $aVariables;
        return $this;
    }
    public function getVariables()
    {
        if($this->aVariables === null){
            $this->aVariables = SeguimientosController::getInstance()->getVariablesByUnidadId($this->iId);
        }
        return $this->aVariables;
    }
    public function addVariable($oVariable)
    {
        $this->aVarible[] = $oVariable;
        return $this;
    }
    
    /**
     *  @return int $iId
     */
    public function getId(){
            return $this->iId ;
    }

    /**
     * @return string $sNombre
     */
    public function getNombre(){
            return $this->sNombre;
    }

    /**
     * @return string $sDescripcion
     */
    public function getDescripcion(){
            return $this->sDescripcion;
    }

    /**
     * @return string $dFechaHora
     */
    public function getFechaHora(){
            return $this->dFechaHora;
    }

    public function isEditable($flag = null){
        if(null !== $flag){
            $this->bEditable = $flag ? true : false;
            return $this;
        }else{
            return $this->bEditable;
        }
    }

    public function isPorDefecto($flag = null){
        if(null !== $flag){
            $this->bPorDefecto = $flag ? true : false;
            return $this;
        }else{
            return $this->bPorDefecto;
        }
    }    
}