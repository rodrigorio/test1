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
    
    /////////////////////////////
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
}
?>
