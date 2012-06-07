<?php

/**
 * Esta clase viene a cumplir el papel de un adapter.
 * Mas simple porque no determino los metodos en un archivo de interfaz.
 *
 * Sin embargo es un adaptador porque en los page controllers se van a llamar a estos metodos
 * y los metodos de esta clase son los que van a utilizar las librerias de embed videos.
 *
 * Por el momento va a utilizar una clase llamada AutoEmbed.class.php. pero se pueden probar otras.
 *
 */
class EmbedVideoHelper extends FileManagerAbstract
{
    /**
     * Instancia de AutoEmbed
     */
    private $utilClass;

    private $aServidoresValidos = array(
        'YouTube',
        'YouTube (Playlists)',
        'Google Video',
        'MetaCafe',
        'Vimeo',
        'Clarin',
        'Flickr',
        'JustinTV',
        'LiveLeak',
        'Yahoo Video'
    );

    public function __construct(){
        parent::__construct();

        $this->utilClass = new AutoEmbed();
    }

    /**
     * @return array
     */
    public function getServidoresValidos()
    {
        return $this->aServidoresValidos;
    }

    public function getStringServidoresValidos()
    {
        $cadena = "";
        if(empty($this->aServidoresValidos)){ return $cadena; }

        foreach ($this->aServidoresValidos as $servidor) {            
            $cadena .= $servidor.", ";
        }

        $cadena = substr($cadena, 0, -2);
        $cadena .= ".";

        return $cadena;
    }

    public function canBeParsed($sUrlCode){
        return $this->utilClass->parseUrl($sUrlCode);
    }

    public function getEmbedVideoThumbnail($oEmbedVideo){

        if(!$this->utilClass->parseUrl($oEmbedVideo->getCodigo())){
            throw new Exception("No se encontro un video para insertar desde la url ingresada. (o el servidor no es soportado)");
        }

        $imageURL = $this->utilClass->getImageURL();

        if(empty($imageURL)){
            $imageURL = $this->getDirectorioImagenesSitio()."defaultVideoMedium.png";
        }

        return $imageURL;
    }

    public function getEmbedVideoCode($oEmbedVideo)
    {
        if(!$this->utilClass->parseUrl($oEmbedVideo->getCodigo())){
            throw new Exception("No se encontro un video para insertar desde la url ingresada. (o el servidor no es soportado)");
        }

        $this->utilClass->setParam('wmode','transparent');
        $this->utilClass->setParam('autoplay','false');

        return $this->utilClass->getEmbedCode();
    }

    /**
     * Ojo que si esto va a parar a la consulta sql el campo es un enum y el string que
     * devuelve el metodo acerca del servidor del video tiene que coincidir con el nombre
     * del campo enum de la tabla.
     */
    public function getServidor($oEmbedVideo)
    {
        if(null === $this->utilClass->getStub()){
            $this->utilClass->parseUrl($oEmbedVideo->getCodigo());
        }
        
        return $this->utilClass->getStub("title");
    }
}