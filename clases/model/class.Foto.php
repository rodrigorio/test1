<?php
/**
 * @author Matias Velilla
 */
class Foto
{
    private $iId;
    private $iSeguimientosId;
    private $iFichasAbstractasId;
    private $iPersonasId;
    private $iCategoriasId;
    private $sNombreBigSize;
    private $sNombreMediumSize;
    private $sNombreSmallSize;
    private $iOrden;
    private $sTitulo;
    private $sDescripcion;
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

    public function setNombreBigSize($sNombreBigSize)
    {
        $this->sNombreBigSize = $sNombreBigSize;
        return $this;
    }
    public function getNombreBigSize()
    {
        return $this->sNombreBigSize;
    }

    public function setNombreMediumSize($sNombreMediumSize)
    {
        $this->sNombreMediumSize = $sNombreMediumSize;
        return $this;
    }
    public function getNombreMediumSize()
    {
        return $this->sNombreMediumSize;
    }

    public function setNombreSmallSize($sNombreSmallSize)
    {
        $this->sNombreSmallSize = $sNombreSmallSize;
        return $this;
    }
    public function getNombreSmallSize()
    {
        return $this->sNombreSmallSize;
    }
    /**
     * @return array 3 celdas con los 3 nombres de los archivos
     */
    public function getArrayNombres()
    {
        $aNombres = array(
            "nombreBigSize" => $this->sNombreBigSize,
            "nombreMediumSize" => $this->sNombreMediumSize,
            "nombreSmallSize" => $this->sNombreSmallSize,
        );
        
        return $aNombres;
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

    public function setDescripcion($sDescripcion)
    {
        $this->sDescripcion = $sDescripcion;
        return $this;
    }
    public function getDescripcion()
    {
        return $this->sDescripcion;
    }

    /**
     * Estos son asi porque en la DB el campo tipo es un enum
     */
    public function setTipoPerfil()
    {
        $this->sTipo = "perfil";
        return $this;
    }
    public function setTipoAdjunto()
    {
        $this->sTipo = "adjunto";
        return $this;
    }
    public function getTipo()
    {
        return $this->sTipo;
    }
}