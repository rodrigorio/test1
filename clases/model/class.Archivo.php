<?php
/**
 *
 * @author Matias Velilla
 */
class Archivo
{
    const TIPO_ADJUNTO = "adjunto";
    const TIPO_CV = "cv";
    const TIPO_ANTECEDENTES = "antecedentes";

    private $iId;
    private $sNombre;
    private $sNombreServidor;
    private $sDescripcion;
    private $sTipoMime;
    private $iTamanio;
    private $sFechaAlta;
    private $iOrden = "";
    private $sTitulo;
    private $sTipo;

    /**
     *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase Provincia
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

    public function setId($iId)
    {
        $this->iId = $iId;
        return $this;
    }
    public function getId()
    {
        return $this->iId;
    }

    public function setNombre($sNombre)
    {
        $this->sNombre = $sNombre;
        return $this;
    }
    public function getNombre()
    {
        return $this->sNombre;
    }

    public function setNombreServidor($sNombreServidor)
    {
        $this->sNombreServidor = $sNombreServidor;
        return $this;
    }
    public function getNombreServidor()
    {
        return $this->sNombreServidor;
    }

    public function setDescripcion($sDescripcion)
    {
        $this->sDescripcion = $sDescripcion;
        return $this;
    }
    public function getDescripcion($nl2br = false)
    {
        if($nl2br){
            return nl2br($this->sDescripcion);
        }else{
            return $this->sDescripcion;
        }
    }

    public function setTipoMime($sTipoMime)
    {
        $this->sTipoMime = $sTipoMime;
        return $this;
    }
    public function getTipoMime()
    {
        return $this->sTipoMime;
    }

    public function setTamanio($iTamanio)
    {
        $this->iTamanio = $iTamanio;
        return $this;
    }
    public function getTamanio()
    {
        return $this->iTamanio;
    }

    public function setFechaAlta($sFechaAlta)
    {
        $this->sFechaAlta = $sFechaAlta;
        return $this;
    }

    public function getFechaAlta($format = false){
        if($format){
            return Utils::fechaFormateada($this->sFechaAlta);
        }else{
            return $this->sFechaAlta;
        }
    }

    public function setOrden($iOrden)
    {
        $this->iOrden = $iOrden;
        return $this;
    }
    public function getOrden()
    {
        return $this->iOrden;
    }

    public function setTitulo($sTitulo)
    {
        $this->sTitulo = $sTitulo;
        return $this;
    }
    public function getTitulo()
    {
        return $this->sTitulo;
    }

    /**
     * Estos son asi porque en la DB el campo tipo es un enum
     */
    public function setTipoCurriculum()
    {
        $this->sTipo = self::TIPO_CV;
        return $this;
    }
    
    public function setTipoAdjunto()
    {
        $this->sTipo = self::TIPO_ADJUNTO;
        return $this;
    }

    public function setTipoAntecedentes()
    {
        $this->sTipo = self::TIPO_ANTECEDENTES;
        return $this;
    }
        
    public function getTipo()
    {
        return $this->sTipo;
    } 
}