<?php

/**
 * @author Matias Velilla
 *
 * metodos utiles para que utilizen los helpers de upload download exportacion, etc.
 */
abstract class FileManagerAbstract extends HelperAbstract
{
    private $nombreCarpetaImagenesSitio = "gui/images";
    private $nombreCarpetaDownloads = "downloads";
    private $nombreCarpetaUploadsUsuario = "uploads/usuarios";
    private $nombreCarpetaUploadsSitio = "uploads/sitio";
    private $nombreCarpetaFotos = "fotos";
    private $nombreCarpetaArchivos = "archivos";

    /**
     * Referencia: http://www.htmlquick.com/es/reference/mime-types.html
     */
    protected $tiposMimeDocumentos = array(
        "text/csv" => ".csv",
        "application/excel" => ".xls",
        "application/vnd.ms-excel" => ".xls",
        "application/x-excel" => ".xls",
        "application/mspowerpoint" => ".ppt",
        "application/powerpoint" => ".ppt",
        "application/vnd.ms-powerpoint" => ".ppt",
        "application/x-mspowerpoint" => ".ppt",
        "application/msword" => ".doc",
        "application/pdf" => ".pdf",
        "application/plain" => ".txt",
        "text/plain" => ".txt",
        "application/rtf" => ".rtf",
        "application/x-rtf" => ".rtf",
        "text/richtext" => ".rtf"
    );

    protected $tiposMimeCompresiones = array(
        "application/x-compressed" => ".zip",
        "application/x-zip-compressed" => ".zip",
        "application/zip" => ".zip",
        "multipart/x-zip" => ".zip",
        "application/x-rar-compressed" => ".rar",
        "application/x-gzip" => ".gz"
    );

    protected $tiposMimeFotos = array (
        "application/octet-stream" => ".psd",
        "image/bmp" => ".bmp",
        "image/x-windows-bmp" => ".bmp",
        "image/gif" => ".gif",
        "image/jpeg" => ".jpe",
        "image/pjpeg" => ".jpe",
        "image/jpeg" => ".jpg",
        "image/pjpeg" => ".jpg",
        "image/jpeg" => ".jpeg",
        "image/pjpeg" => ".jpeg",
        "image/png" => ".png",
        "image/x-icon" => ".ico"
    );

    protected $tiposMimeAudioVideo = array (
        "application/x-shockwave-flash" => ".swf",
        "application/x-troff-msvideo" => ".avi",
        "video/avi" => ".avi",
        "video/msvideo" => ".avi",
        "video/x-msvideo" => ".avi",
        "video/quicktime" => ".mov",
        "video/quicktime" => ".qt",
        "audio/mpeg" => ".mpg",
        "video/mpeg" => ".mpe",
        "video/mpeg" => ".mpeg",
        "audio/mpeg" => ".mp3",
        "audio/mpeg3" => ".mp3",
        "audio/x-mpeg-3" => ".mp3",
        "video/mpeg" => ".mp3",
        "video/x-mpeg" => ".mp3",
        "audio/wav" => ".wav",
        "audio/x-wav" => ".wav"
    );

    /**
     * Es la union de los dos array
     */
    protected $tiposMimeDocumentosCompresiones = array();

    /**
     * Se guarda el conjunto actual de tipos validos con el que se va a trabajar
     */
    protected $tiposValidos = array();

    /**
     * Estos son privates para proteger que no se toqueteen cosas sensibles.
     * Si desde una de las clases que heredan se necesitan los directorios que se usen los get's
     */
    private $directorioUploadArchivos;
    private $directorioUploadFotos;

    private $directorioDownloads;
    private $directorioImagenesSitio;
       
    public function __construct(){
        
        $this->tiposMimeDocumentosCompresiones = array_merge($this->tiposMimeDocumentos, $this->tiposMimeCompresiones);
       
        $this->utilizarDirectorioUploadUsuarios();        
        
        //el directorio de downloads utilizado para la generacion de archivos exportados es para uso unico de usuarios.
        $baseUrl = $this->getRequest()->getBaseUrl();
        $this->directorioDownloads = $baseUrl."/".$this->nombreCarpetaDownloads."/";
        $this->directorioImagenesSitio = $baseUrl."/".$this->nombreCarpetaImagenesSitio."/";
    }

    public function setTiposValidosDocumentos()
    {
        $this->tiposValidos = $this->tiposMimeDocumentos;
        return $this;
    }

    public function setTiposValidosCompresiones()
    {
        $this->tiposValidos = $this->tiposMimeCompresiones;
        return $this;
    }

    public function setTiposValidosAudioVideo()
    {
        $this->tiposValidos = $this->tiposMimeAudioVideo;
        return $this;
    }

    public function setTiposValidosFotos()
    {
        $this->tiposValidos = $this->tiposMimeFotos;
        return $this;
    }

    public function setTiposValidosDocumentosCompresiones()
    {
        $this->tiposValidos = $this->tiposMimeDocumentosCompresiones;
        return $this;
    }

    //estos son protected para que se puedan usar dentro de los metodos de las clases que heredan.
    protected function getTiposMimeDocumentos(){
        return $this->tiposMimeDocumentos;
    }
    protected function getTiposMimeCompresiones(){
        return $this->tiposMimeCompresiones;
    }
    protected function getTiposMimeFotos(){
        return $this->tiposMimeFotos;
    }
    protected function getTiposMimeAudioVideo(){
        return $this->tiposMimeAudioVideo;
    }

    /**
     * retorna el array seteado como los tipos validos actuales
     *
     * @return array
     */
    public function getTiposValidos()
    {
        return $this->tiposValidos;
    }

    /**
     * Genera e inicializa el helper para trabajar en los directorios
     * de uploads destinado a los usuarios.
     */
    public function utilizarDirectorioUploadUsuarios()
    {
        $baseUrl = $this->getRequest()->getBaseUrl();
        $directorioUpload = $baseUrl."/".$this->nombreCarpetaUploadsUsuario."/";
        $this->directorioUploadArchivos = $directorioUpload.$this->nombreCarpetaArchivos."/";
        $this->directorioUploadFotos = $directorioUpload.$this->nombreCarpetaFotos."/";
        return $this;
    }

    /**
     * Genera e inicializa el helper para trabajar en los directorios de un modulo del sitio
     * Si no se especifica el nombre del modulo lo obtendra del modulo actual que utiliza el page controller desde
     * donde se crea el helper
     */
    public function utilizarDirectorioUploadSitio($modulo)
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        if(empty($modulo)){
            $modulo = $request->getModuleName();
        }

        $directorioUpload = $baseUrl."/".$this->nombreCarpetaUploadsSitio."/".$modulo."/";
        $this->directorioUploadArchivos = $directorioUpload.$this->nombreCarpetaArchivos."/";
        $this->directorioUploadFotos = $directorioUpload.$this->nombreCarpetaFotos."/";

        return $this;
    }

    private function addDocumentRoot($url)
    {
        //le saco la ultima barra porque el getBaseUrl ya la incorpora al string
        $root = $_SERVER['DOCUMENT_ROOT'];
        $root = substr($root,0,-1);
        return $root.$url;
    }

    /**
     * Devuelve el directorio de upload para archivos con el que actualmente opera el helper
     * Util para generar el src de descargas
     *
     * Si se le pasa true entonces la funcion devuelve el path completo con server root
     * util para las funciones copy, unlink, move_uploaded_file, etc.
     */
    public function getDirectorioUploadArchivos($serverRoot = false)
    {
        if($serverRoot){
            return $this->addDocumentRoot($this->directorioUploadArchivos);
        }else{
            return $this->directorioUploadArchivos;
        }
    }

    /**
     * Devuelve el directorio de upload para fotos con el que actualmente opera el helper
     * Util para generar el src de las imagenes
     */
    public function getDirectorioUploadFotos($serverRoot = false)
    {
        if($serverRoot){
            return $this->addDocumentRoot($this->directorioUploadFotos);
        }else{
            return $this->directorioUploadFotos;
        }
    }

    public function getDirectorioDownloads($serverRoot = false)
    {
        if($serverRoot){
            return $this->addDocumentRoot($this->directorioDownloads);
        }else{
            return $this->directorioDownloads;
        }        
    }

    public function getDirectorioImagenesSitio($serverRoot = false)
    {
        if($serverRoot){
            return $this->addDocumentRoot($this->directorioImagenesSitio);
        }else{
            return $this->directorioImagenesSitio;
        }
    }

    /**
     * Devuelve un string con los tipos validos actuales
     * (con los cuales se verifico si el upload fue correcto)
     * Esta funcion es principalmente para agregar un mensaje en el formulario
     * con los tipos validos.
     *
     * @return string
     */
    public function getStringTiposValidos()
    {
        $cadena = "";
        if(empty($this->tiposValidos)){ return $cadena; }

        $tiposValidos = array_unique($this->tiposValidos);

        foreach ($tiposValidos as $ftv) {
            $ftv = str_replace("image/", "", $ftv);
            $cadena .= $ftv." ";
        }

        return $cadena;
    }

    /**
     * Función para eliminar caracteres especiales en un nombre de archivo.
     */
    public function limpiarNombreArchivo($s)
    {
        return InflectorHelper::unaccent($s);
    }

    /**
     * Función para generar el nombre de un archivo cargado
     *
     * @param string $nombreOriginal nombre con el que se subió el archivo.
     * @param string $idItem id del registro al que estará asociado el archivo
     * @param string $extra cualquier cadena que quiera agregarse al nombre del archivo
     *
     */
    public function generarNombreArchivo($nombreOriginal, $idItem, $extra="")
    {
        $separador = "_";
        $nombreNuevo = $this->limpiarNombreArchivo($nombreOriginal);
        $prefijo = "";
        $prefijo .= ($idItem != "") ? $idItem.$separador : "" ;
        $prefijo .= ($extra != "") ? $extra.$separador : "" ;
        $prefijo .= time().$separador;
        return $prefijo.$nombreNuevo;
    }    
}