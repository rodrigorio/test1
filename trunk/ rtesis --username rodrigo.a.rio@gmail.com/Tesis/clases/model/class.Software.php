<?php

/**
 * @todo FALTA TODO EL TEMA DE CALCULAR EL RATING Y LA CANTIDAD QUE VOTARON
 */
class Software extends FichaAbstract
{
    private $iUsuarioId;
    private $oUsuario;
    private $iCategoriaId;
    private $oCategoria;
    private $bActivoComentarios = true;
    private $bPublico = false;
    private $sDescripcionBreve;
    private $sEnlaces;
    private $aComentarios;
    
    /**
     * No esta en la base de datos pero sirve para obtener el dato luego de la primera vez que se calcula.
     * -1 indica que no hay valoraciones desde los comentarios
     */
    private $fRating = -1;

    /**
     * Es un flag necesario para determinar si el software tiene valoracion.
     */
    private $bRatingCalculado = false;

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

    public function setCategoriaId($iCategoriaId){
        $this->iCategoriaId = $iCategoriaId;
        if(!empty($iCategoriaId) && null !== $this->oCategoria && $this->oCategoria->getId() != $iCategoriaId){
            $this->oCategoria = ComunidadController::getInstance()->getCategoriaById($iCategoriaId);
        }
    }

    public function setCategoria($oCategoria)
    {
        $this->oCategoria = $oCategoria;
        return $this;
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

    public function setEnlaces($sEnlaces){
    	$this->sEnlaces = $sEnlaces;
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

    public function getCategoria(){
    	if($this->oCategoria == null && !empty($this->iCategoriaId)){
            $this->oCategoria = ComunidadController::getInstance()->getCategoriaById($this->iCategoriaId);
    	}
        return $this->oCategoria;
    }

    public function getUsuarioId()
    {
        return $this->iCategoriaId;
    }
    
    public function getDescripcionBreve(){
        return $this->sDescripcionBreve;
    }
    
    public function getEnlaces($nl2br = false){
        if($nl2br){
            return nl2br($this->sEnlaces);
        }
        return $this->sEnlaces;
    }

    public function getComentarios()
    {
        if($this->aComentarios === null){
            $this->aComentarios = ComunidadController::getInstance()->obtenerComentariosSoftware($this->iId);
        }
        return $this->aComentarios;
    }

    /**
     * Redondea a 1 solo decimal.
     *
     * Solo si al menos un comentario emitio valoracion modifica el valor del rating para la instancia.
     */
    public function getRating()
    {
        if(!$this->bRatingCalculado){
            $fPromedio = 0;
            $iCont = 0;
            $aComentarios = $this->getComentarios();
            foreach($aComentarios as $oComentario){
                if($oComentario->emitioValoracion()){
                    $fPromedio += $oComentario->getValoracion();
                    $iCont++;
                }
            }

            $fPromedio = round(($fPromedio / $iCont), 1);

            //si al menos un comentario emitio valoracion guardo el rating calculado, sino queda el -1
            if($iCont > 0){
                $this->fRating = $fPromedio;
            }

            $this->bRatingCalculado = true;
        }

        return $this->fRating;
    }

    /**
     * Devuelve true si el software tiene valoracion
     */
    public function tieneValoracion(){
        return($this->getRating() >= 0)?true:false;
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