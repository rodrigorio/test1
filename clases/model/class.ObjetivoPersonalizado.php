<?php

/**
 * Description of classObjetivoPersonalizado
 *
 * @author Andres
 */
class ObjetivoPersonalizado extends ObjetivoAbstract
{
    private $iId;
    private $oObjetivoPersonalizadoEje;
	
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

    public function getId()
    {
        return $this->iId;
    }

    public function setId($iId)
    {
        $this->iId = $iId;
    }
    
    /**
     *  @param int $iObjetivoEjeId
     */
    public function setObjetivoPersonalizadoEje($oObjetivoPersonalizadoEje){
        $this->oObjetivoPersonalizadoEje = $oObjetivoPersonalizadoEje;
    }

    /**
     *  
     */
    public function getObjetivoPersonalizadoEje(){
        return $this->oObjetivoPersonalizadoEje;
    }

    public function getEvolucion()
    {
    	if($this->aEvolucion === null){
            $this->aEvolucion = SeguimientosController::getInstance()->obtenerEvolucionObjetivoPersonalizado($this->getId());
    	}
        return $this->aEvolucion;
    }

    /**
     * retorna 1 objeto evolucion correspondiente a la fecha, si no existe = null
     */
    public function getEvolucionByDate($dFecha)
    {
        return SeguimientosController::getInstance()->obtenerEvolucionObjetivoPersonalizadoByDate($this->getId(), $dFecha);
    }
}
