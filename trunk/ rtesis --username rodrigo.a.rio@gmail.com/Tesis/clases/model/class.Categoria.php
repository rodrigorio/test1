<?php

class Categoria{
    private $iId;
    private $sNombre;
    private $sDescripcion;

    /**
     * objeto Foto
     */
    private $oFoto = null;
		
    /**
     * array objetos Software
     */
    private $aSoftware = null;
		
    /**
     *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase Categoria
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
     * @param string $sDescripcion
     */
    public function setDescripcion($sDescripcion){
        $this->sDescripcion = $sDescripcion;
    }
	
    public function setSoftware($aSoftware){
        $this->aSoftware = $aSoftware;
    }

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
        
    public function getDescripcion($nl2br = false){
        if($nl2br){
            return nl2br($this->sDescripcion);
        }else{
            return $this->sDescripcion;
        }
    }

    /**
     * @return array|null Archivo
     */
    public function getSoftware()
    {
    	if($this->aSoftware == null){
            $this->aSoftware = ComunidadController::getInstance()->obtenerSoftwareCategoria($this->iId);
    	}
        return $this->aSoftware;
    }

    public function setFoto($oFoto){
        $this->oFoto = $oFoto;
        return $this;
    }

    public function getFoto(){
        return $this->oFoto;
    }

    public function getNombreAvatar($medium = false){
        if(null == $this->oFoto){
            return $medium ? "defaultCategoriaMedium.png" : "defaultCategoriaSmall.png";
        }

        return $medium ? $this->oFoto->getNombreMediumSize() : $this->oFoto->getNombreSmallSize();
    }
}