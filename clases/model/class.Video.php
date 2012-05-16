<?php
class Video
{
    private $iId;
    private $iSeguimientosId;
    private $iFichasAbstractasId;
    private $sCodigo; //codigo es la url donde esta el video
    private $iOrden;
    private $sTitulo;
    private $sDescripcion;
    private $sOrigen; //origen es el lugar donde esta guardado por ej YouTube

    /**
     *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase
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
    
    public function setCodigo($sCodigo)
    {
        $this->sCodigo = $sCodigo;
        return $this;
    }
    public function getCodigo()
    {
        return $this->sCodigo;
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
 public function setOrigen($sOrigen)
    {
        $this->sOrigen = $sOrigen;
        return $this;
    }
    public function getOrigen()
    {
        return $this->sOrigen;
    }
}