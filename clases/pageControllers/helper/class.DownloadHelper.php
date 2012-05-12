<?php

/**
 * Para descargar archivos, desde los page controllers genera los encabezados necesarios, etc.
 *
 * @author Matias Velilla
 */
class DownloadHelper extends FileManagerAbstract
{
    /**
     * hay un auxiliar porque se pueden descargar archivos exportados que se generan por el sistema
     * y tambien se pueden descargar archivos previamente subidos por el usuario
     *
     * Por defecto se utiliza la carpeta de uploads, en el caso de que se quiera descargar un archivo
     * generado por el sistema se utiliza la funcion 'utilizarDirectorioDownloads' 
     */
    private $directorioServidor;
    
    public function __construct()
    {
        parent::__construct();

        $this->directorioServidor = $this->getDirectorioUploadArchivos(true);
    }

    public function utilizarDirectorioDownloads(){
        $this->directorioServidor = $this->getDirectorioDownloads(true);
        return $this;
    }
      
    public function generarDescarga($oArchivo)
    {
        $nombreDestino = $oArchivo->getNombre();
        if(empty($nombreDestino)){
            $nombreDestino = $oArchivo->getNombreServidor();
        }

        $archivoPathServidor = $this->directorioServidor.$oArchivo->getNombreServidor();

        $this->getResponse()->setRawHeader("Content-type: ".$oArchivo->getTipoMime())
                            ->setRawHeader("Content-Disposition: attachment; filename=\"$nombreDestino\"\n")
                            ->setBody("");

        $fp = fopen("$archivoPathServidor", "rb");
        fpassthru($fp);        
    }
}