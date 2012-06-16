<?php

class Review extends FichaAbstract
{
    private $iUsuarioId;
    private $oUsuario;
    private $bActivoComentarios;
    private $bModerado;
    private $bPublico;
    private $sDescripcionBreve;
    private $sKeywords;
    private $aComentarios;

    /**
     * 'product','business','event','person','place','website','url'
     *
     * This optional property provides the type of the item being reviewed
     */
    private $sItemType;

    /**
     * varchar 255
     *
     * ITEM must have at a minimum the name
     *
     */
    private $sItemName;

    /**
     * varchar 255
     *
     * an event item must have the "summary" subproperty inside the respective hCalendar "vevent"
     */
    private $sItemEventSummary;

    /**
     * varchar 500
     *
     * should provide at least one URI ("url") for the item
     */
    private $sItemUrl;

    /**
     * double
     *
     * The rating is a fixed point integer (one decimal point of precision) from 1.0 to 5.0
     */
    private $fRating = null;

    /**
     * varchar 500
     *
     * URL de la fuente de donde se extrajo informacion
     */
    private $sFuenteOriginal;

    public function __construct(stdClass $oParams = null){
        parent::__construct();
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

    public function setUsuarioId($iUsuarioId){
        $this->iUsuarioId = $iUsuarioId;
        if(!empty($iUsuarioId) && null !== $this->oUsuario && $this->oUsuario->getId() != $iUsuarioId){
            $this->oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
        }
    }

    public function setUsuario($oUsuario)
    {
        $this->oUsuario = $oUsuario;
        return $this;
    }

    public function isModerado($flag = null){
        if(null !== $flag){
            $this->bModerado = $flag ? true : false;
            return $this;
        }else{
            return $this->bModerado;
        }
    }

    public function isPublico($flag = null){
        if(null !== $flag){
            $this->bPublico = $flag ? true : false;
            return $this;
        }else{
            return $this->bPublico;
        }
    }

    public function isActivoComentarios($flag = null){
        if(null !== $flag){
            $this->bActivoComentarios = $flag ? true : false;
            return $this;
        }else{
            return $this->bActivoComentarios;
        }
    }

    public function setDescripcionBreve($sDescripcionBreve){
    	$this->sDescripcionBreve = $sDescripcionBreve;
        return $this;
    }

    public function setKeywords($sKeywords){
    	$this->sKeywords = $sKeywords;
        return $this;
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

    public function getDescripcionBreve(){
        return $this->sDescripcionBreve;
    }

    public function getKeywords(){
        return $this->sKeywords;
    }

    public function setItemType($sItemType)
    {
        $this->sItemType = $sItemType;
        return $this;
    }

    public function setItemName($sItemName)
    {
        $this->sItemName = $sItemName;
        return $this;
    }

    public function setItemEventSummary($sItemEventSummary)
    {
        $this->sItemEventSummary = $sItemEventSummary;
        return $this;
    }

    public function setItemUrl($sItemUrl)
    {
        $this->sItemUrl = $sItemUrl;
        return $this;
    }

    public function setRating($fRating)
    {
        $this->fRating = $fRating;
        return $this;
    }

    public function setFuenteOriginal($sFuenteOriginal)
    {
        $this->sFuenteOriginal = $sFuenteOriginal;
        return $this;
    }
    
    public function getItemType()
    {
        return $this->sItemType;
    }
    
    public function getItemName()
    {
        return $this->sItemName;
    }

    public function getItemEventSummary()
    {
        return $this->sItemEventSummary;
    }

    public function getItemUrl()
    {
        return $this->sItemUrl;
    }

    public function getRating()
    {
        return $this->fRating;
    }

    public function getFuenteOriginal()
    {
        return $this->sFuenteOriginal;
    }

    public function getComentarios()
    {
        if($this->aComentarios === null){
            $this->aComentarios = ComunidadController::getInstance()->obtenerComentariosReview($this->iId);
        }
        return $this->aComentarios;
    }
    public function setComentarios($aComentarios)
    {
        $this->aComentarios = $aComentarios;
        return $this;
    }
    public function addComentario($oComentario)
    {
        $this->aComentarios[] = $oComentario;
        return $this;
    }
}