<?php

class Comentario
{
    private $iId;
    private $iReviewId;
    private $iPublicacionId;
    private $iArchivoId;
    private $dFecha;
    private $sDescripcion;
    private $fValoracion;
    private $iUsuarioId;
    private $oUsuario;
    private $sNombreApellido;
    

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
    }
    public function setReviewId($iReviewId)
    {
        $this->iReviewId = $iReviewId;
     }
    public function setPublicacionId($iPublicacionId)
    {
        $this->iPublicacionId = $iPublicacionId;
    }
    public function setArchivoId($iArchivoId)
    {
        $this->iArchivoId = $iArchivoId;
    }
    public function setFecha($dFecha)
    {
        $this->dFecha = $dFecha;
    }
    public function setDescripcion($sDescripcion)
    {
        $this->sDescripcion = $sDescripcion;
        return $this;
    }
    
    public function setUsuario($oUsuario)
    {
        $this->oUsuario = $oUsuario;
        return $this;
    }
    public function setUsuarioId($iUsuarioId)
    {
        $this->iUsuarioId = $iUsuarioId;
     }
   public function setValoracion($fValoracion)
    {
        $this->fValoracion = $fValoracion;
    }
    public function setNombreApellido($sNombreApellido)
    {
        $this->sNombreApellido = $sNombreApellido;
    }
    
    //GETS
    public function getId()
    {
        return $this->iId;
    }
    public function getReviewId()
    {
        return $this->iReviewId;
    }
    public function getPublicacionId()
    {
        return $this->iPublicacionId;
    }
    public function getArchivoId()
    {
        return $this->iArchivoId;
    }
    public function getFecha()
    {
        return $this->dFecha;
    }
    public function getDescripcion()
    {
        return $this->sDescripcion;
    }
    public function getValoracion()
    {
        return $this->fValoracion;
    }
    public function getUsuario(){
    	if($this->oUsuario == null && !empty($this->iUsuarioId)){
            $this->oUsuario = ComunidadController::getInstance()->getUsuarioById($this->iUsuarioId);
    	}
        return $this->oUsuario;
    }
    public function getUsuarioId()
    {
        return $this->iUsuarioId;
    }
    public function getNombreApellido()
    {
        return $this->sNombreApellido;
    }
}