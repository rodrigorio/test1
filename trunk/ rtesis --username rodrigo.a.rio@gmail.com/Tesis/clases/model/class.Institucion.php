<?php
class Institucion {
	private $iId;
	private $sNombre;
	private $oCiudad;
 	private $iModerado;
 	private $sDescripcion;
  	private $iTipoInstitucion;
  	private $sDireccion;
  	private $sEmail;
  	private $sTelefono;
  	private $sSitioWeb;
  	private $sHorariosAtencion;
  	private $sAutoridades;
  	private $sCargo;
  	private $sPersoneriaJuridica;
  	private $sSedes;
  	private $sActividadesMes;
	
 	/**
 	 *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase Pais
	 * @param stdClass $oParams
	 */
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
	 * @param Ciudad $oCiudad
	 */
	public function setCiudad($oCiudad){
		$this->oCiudad = $oCiudad;
	}
	/**
 	 *  @param int $iModerado
	 */
	public function setModerado($iModerado){
		$this->iModerado = (int)$iModerado;
	}
	/**
 	 *  @param int $iTipoInstitucion
	 */
	public function setTipoInstitucion($iTipoInstitucion){
		$this->iTipoInstitucion = (int)$iTipoInstitucion;
	}
	/**
	 * @param string $sDescripcion
	 */
	public function setDescripcion($sDescripcion){
		$this->sDescripcion = $sDescripcion;
	}
	/**
	 * @param string $sDireccion
	 */
	public function setDireccion($sDireccion){
		$this->$sDireccion = $sDireccion;
	}
	/**
	 * @param string $sEmail
	 */
	public function setEmail($sEmail){
		$this->$sEmail = $sEmail;
	}
	/**
	 * @param string $sTelefono
	 */
	public function setTelefono($sTelefono){
		$this->$sTelefono = $sTelefono;
	}
	/**
	 * @param string $sSitioWeb
	 */
	public function setSitioWeb($sSitioWeb){
		$this->$sSitioWeb = $sSitioWeb;
	}
	/**
	 * @param string $sHorariosAtencion
	 */
	public function setHorariosAtencion($sHorariosAtencion){
		$this->$sHorariosAtencion = $sHorariosAtencion;
	}
	/**
	 * @param string $sAutoridades
	 */
	public function setAutoridades($sAutoridades){
		$this->$sAutoridades = $sAutoridades;
	}
	/**
	 * @param string $sCargo
	 */
	public function setCargo($sCargo){
		$this->$sCargo = $sCargo;
	}
	/**
	 * @param string $sPersoneriaJuridica
	 */
	public function setPersoneriaJuridica($sPersoneriaJuridica){
		$this->$sPersoneriaJuridica = $sPersoneriaJuridica;
	}
	/**
	 * @param string $sSedes
	 */
	public function setSedes($sSedes){
		$this->$sSedes = $sSedes;
	}
	/**
	 * @param string $sSedes
	 */
	public function setActividadesMes($sActividadesMes){
		$this->$sActividadesMes = $sActividadesMes;
	}
	///////////////gets//////////////
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
	 * @return  Ciudad $oCiudad
	 */
	public function getCiudad(){
		return $this->oCiudad;
	}
	
}
?>