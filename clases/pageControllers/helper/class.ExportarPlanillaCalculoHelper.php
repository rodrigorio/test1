<?php

/**
 * @author Matias Velilla
 */
class ExportarPlanillaCalculoHelper extends FileManagerAbstract
{
    const SEPARADOR_CSV = ",";
    const SEPARADOR_XLS = ";";
    const REGULAR_MIME_TYPE = "text/csv";
    const XLS_MIME_TYPE = "application/excel";

    private $sSeparador = "";

    private $sMimeType = "";

    /**
     * Donde se acumulan las lineas que se van a imprimir en el archivo.
     */
    private $aFileLines = array();

    public function __construct() {
        parent::__construct();

        $this->initExportarXLS();
    }

    public function initExportarXLS()
    {
        $this->sSeparador = self::SEPARADOR_XLS;
        $this->sMimeType = self::XLS_MIME_TYPE;
    }

    public function initExportarCSV()
    {
        $this->sSeparador = self::SEPARADOR_CSV;
        $this->sMimeType = self::REGULAR_MIME_TYPE;
    }

    public function addFileLine($aColumns)
    {
        $sDataLine = "";
        foreach($aColumns as $sColumn){
            $sDataLine .= $sColumn.$this->sSeparador;
        }
        //borro el ultimo separador
        $sDataLine = substr ($sDataLine, 0, -1);
        $this->aFileLines[] = $sDataLine;
    }

    public function generarArchivo($idItem = "", $extra = "")
    {
        try{
            $fileName = "usuarios-sistema";
            $nombreArchivo = $this->generarNombreArchivo($fileName, $idItem, $extra);
            $rutaDestino = $this->getDirectorioDownloads(true).$nombreArchivo;

            $fp = fopen($rutaDestino, 'w');
            foreach($this->aFileLines as $line){
                fwrite($fp, $line);
                fwrite($fp, chr(13).chr(10));
            }
            fclose($fp);
            
            return array($fileName, $this->sMimeType, $nombreArchivo);
        }catch(Exception $e){
            //elimino el archivo temporal:
            if(is_file($rutaDestino) && file_exists($rutaDestino)){
                unlink($rutaDestino);
            }
            throw new Exception($e->getMessage(), 0);
        }
    }
}
