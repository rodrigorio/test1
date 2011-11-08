<?php

/**
 * Para descargar archivos, desde los page controllers genera los encabezados necesarios, etc.
 *
 * @author Matias Velilla
 */
class DownloadHelper extends HelperAbstract
{
    private $directorioUploadArchivos;

    public function __construct()
    {
        $this->utilizarDirectorioUploadUsuarios();
    }

    /**
     * Genera e inicializa el helper para trabajar en los directorios
     * de uploads destinado a los usuarios.
     */
    public function utilizarDirectorioUploadUsuarios()
    {
        $baseUrl = $this->getRequest()->getBaseUrl();
        $directorioUpload = $baseUrl."/uploads/usuarios/";
        $this->directorioUploadArchivos = $directorioUpload."archivos/";
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

        $directorioUpload = $baseUrl."/uploads/sitio/".$modulo."/";
        $this->directorioUploadArchivos = $directorioUpload."archivos/";
        
        return $this;
    }

    public function generarDescarga($oArchivo)
    {
        $nombreDestino = $oArchivo->getNombre();
        if(empty($nombreDestino)){
            $nombreDestino = $oArchivo->getNombreServidor();
        }

        $archivoPathServidor = $this->directorioUploadArchivos.$oArchivo->getNombreServidor();

        $this->getResponse()->setRawHeader("Content-type: ".$oArchivo->getTipoMime())
                            ->setRawHeader("Content-Disposition: attachment; filename=\"$nombreDestino\"\n")
                            ->setBody("");

        $fp = fopen("$archivoPathServidor", "rb");
        fpassthru($fp);        
    }
}