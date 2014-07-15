<?php

/**
 * Adapter entre controlador de pagina y libreria de html to pdf
 *
 */
class HtmlToPdf extends HelperAbstract
{
    /**
     * Instancia de WkHtmlToPdf
     */
    private $oUtilClass;
    private $sFileName;
    private $sBinPath;

    public function __construct(){
        $this->setLocalBinPath();
        $aOptions['binPath'] = $this->sBinPath;
        $this->oUtilClass = new WkHtmlToPdf($aOptions);
    }

    public function setLocalBinPath(){
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $this->sBinPath = $parametros->obtener('BIN_PATH')."wkhtmltopdf";
    }

    public function generarFileName($aTokens)
    {
        $this->sFileName = "";
        foreach($aTokens as $sToken){
            $this->sFileName .= $sToken;
        }

        $this->sFileName = InflectorHelper::unaccent($this->sFileName);
        $this->sFileName .= time();
        $this->sFileName = str_replace(" ", "_", $this->sFileName);
        $this->sFileName .= $this->sFileName.".pdf";
    }

    public function agregarPagina($sHtml){
        $this->oUtilClass->addPage($sHtml);
    }

    public function generar(){
        $this->oUtilClass->send($this->sFileName);
    }
}
