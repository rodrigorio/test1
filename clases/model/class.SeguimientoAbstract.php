<?php


/**
 * Description of classSeguimientoAbstract
 *
 * 
 */
abstract class SeguimientoAbstract
{
   protected $iId;
   protected $oDiscapacitado;
   protected $sFrecuenciaEncuentros;
   protected $oUsuario;
   protected $oPractica;
   protected $sAntecedentes;
   protected $sPronostico;
   protected $sDiaHorario;
   protected $dFechaCreacion;
   protected $sEstado;
   /*
    * array objetos Foto
    */
    protected $aFotos = null;
   /*
    * array objetos Archivo
    */
    protected $aArchivos = null;
    protected $fArchivoAntecedente = null;

public function __construct(){}
   
public function setId($id){
        $this->iId = $id;
    }
public function setFrecuenciaEncuentros($sFrecuenciaEncuentros){
        $this->sFrecuenciaEncuentros = $sFrecuenciaEncuentros;
    }
public function setDiscapacitado($oDiscapacitado){
        $this->oDiscapacitado = $oDiscapacitado;
    }
public function setUsuario($oUsuario){
        $this->oUsuario = $oUsuario;
    }
public function setPractica($oPractica){
        $this->oPractica = $oPractica;
    }
public function setAntecedentes($sAntecedentes){
        $this->sAntecedentes = $sAntecedentes;
    }
public function setPronostico($sPronostico){
        $this->sPronostico = $sPronostico;
    }
public function setDiaHorario($sDiaHorario){
        $this->sDiaHorario = $sDiaHorario;
    }
public function setFechaCreacion($dFechaCreacion){
        $this->dFechaCreacion = $dFechaCreacion;
    }
    
public function setEstado($sEstado){
        $this->sEstado = $sEstado;
    }

public function getId(){
        return $this->iId;
    }
public function getFrecuenciaEncuentros(){
        return $this->sFrecuenciaEncuentros;
    }
public function getDiscapacitado(){
        return $this->oDiscapacitado;
    }
    
public function getUsuario(){
        return $this->oUsuario;
    }
public function getPractica(){
        return $this->oPractica;
    }    
public function getAntecedentes(){
        return $this->sAntecedentes;
    }     
public function getPronostico(){
        return $this->sPronostico;
    }  

public function getDiaHorario(){
        return $this->sDiaHorario;
    }      
public function getFechaCreacion(){
	return $this->dFechaCreacion;
	}
public function getEstado(){
      return  $this->sEstado;
    }


    public function setFotos($aFotos)
    {
        $this->aFotos = $aFotos;
    }
    public function setArchivos($aArchivos)
    {
        $this->aArchivos = $aArchivos;
    }

    /**
     * @return array|null Foto
     */
    public function getFotos()
    {
    	if($this->aFotos == null){
            $this->aFotos = SeguimientosController::getInstance()->obtenerFotosSeguimiento($this->iId);
    	}
        return $this->aFotos;
    }
    /**
     * @return array|null Archivo
     */
    public function getArchivos()
    {
    	if($this->aArchivos == null){
            $this->aArchivos = SeguimientosController::getInstance()->obtenerArchivosSeguimiento($this->iId);
    	}
        return $this->aArchivos;
    }
    /**
     * @return null|Archivo
     */
    public function getArchivoAntecedentes()
    {
    	if($this->fArchivoAntecedente == null){
            $this->fArchivoAntecedente = SeguimientosController::getInstance()->obtenerArchivoAntecedente($this->iId);
    	}
        return $this->fArchivoAntecedente;
    }
    
     public function setArchivoAntecedentes($fAntecedentes){
     	$this->fArchivoAntecedente = $fAntecedentes;
     }
}