<?php
/*********************************************************************************
*       Filename: template.php
*       PHP 5.0 & Templates Modified 18/11/2006
*
*       Usage:
*       $tpl = new Template($app_path);
*       $tpl->load_file($template_filename, "main");
*       $tpl->set_var("ID", 2);
*       $tpl->set_var("Value", "Name");
*       $tpl->parse("DynBlock", false); // true if you want to create a list
*
*       $tpl->pparse("main", false); // parse and output block
*                 OR
*       $tpl->parse("main", false); // parse block
*       $tpl->print_var("main");    // output block
*********************************************************************************/

class Templates extends HelperAbstract
{
    private $sTemplate;
    private $DBlocks;       	/* initial data files and blocks */
    private $ParsedBlocks;   	/* resulted data and variables	*/
    private $sPath;
    private $tempout;
    private $sError;
    private $vLabels;
    private $startVarTag	= "{";
    private $endVarTag		= "}";
    private $sLabelPrefix 	= null;
    private $sFunTransaltion	= null;
    private $fileContents       = array();

    public function __construct($sTemplatesPath=".",$sLabelPrefix="lbl"){
        $this->DBlocks      = array();
        $this->ParsedBlocks = array();
        $this->sPath        = $sTemplatesPath;
        $this->sTemplate    = null;
        $this->tempout      = null;
        $this->sError       = null;
        $this->vLabels      = null;
        $this->sLabelPrefix = $sLabelPrefix;
    }

    /**
     * Load template from file
     *
     * @param string $sFilename
     * @param string $sBlockName
     */
    public function load_file($sFilename, $sBlockName){
        $nName = null;
        if (file_exists($sFilename)){
            $this->DBlocks[$sBlockName] = file_get_contents($sFilename);
            $this->blockLabels($sBlockName);
            $nName = $this->NextDBlockName($sBlockName);
            while ($nName != ""){
                $this->SetBlock($sBlockName, $nName);
                $nName = $this->NextDBlockName($sBlockName);
            }
        }else{
            throw new Exception("File not found. $sFilename ");
        }
    }

    /**
     * Load only a template section from file
     *
     * @param string $filename
     * @param string $targetBlock
     * @param string $sectionName
     */
    public function load_file_section($sFilename, $sBlockName, $sSectionName, $concat = false) {
        $nName = null;
        if (file_exists($sFilename) || array_key_exists($sFilename, $this->fileContents)) {
            if(array_key_exists($sFilename, $this->fileContents)){
                $holeFile = $this->fileContents[$sFilename];
            }else{
                $holeFile = file_get_contents($sFilename);
                $this->fileContents[$sFilename] = $holeFile;
            }
            $posStart =  strpos($holeFile, "<!--Begin" . trim($sSectionName) . "-->");
            $posEnd = strpos($holeFile, "<!--End" . trim($sSectionName) . "-->") + strlen("<!--End" . trim($sSectionName) . "-->");
            if($concat){
                $this->DBlocks[$sBlockName] .= substr($holeFile, $posStart,  $posEnd - $posStart);
            }else{
                $this->DBlocks[$sBlockName] = substr($holeFile, $posStart,  $posEnd - $posStart);
            }
            $this->blockLabels($sBlockName);
            $nName = $this->NextDBlockName($sBlockName);
            while ($nName != "") {
                $this->SetBlock($sBlockName, $nName);
                $nName = $this->NextDBlockName($sBlockName);
            }
        }
    }

    /**
     * Load template from string
     * - Use load_from_string instead of this -
     * @param string $sTemplate
     * @param string $sName
     */
    public function load_html($html, $sName) {
        $this->load_from_string($html, $sName);
    }

    /**
     * Load template from string
     *
     * @param string $sTemplate
     * @param string $sName
     */
    public function load_from_string($sTemplate,$sBlockName){
        if (strlen($sTemplate)){
            $this->DBlocks[$sBlockName] = $sTemplate;
            $this->blockLabels($sBlockName);
            $nName = $this->NextDBlockName($sBlockName);
            while ($nName != ""){
                $this->SetBlock($sBlockName, $nName);
                $nName = $this->NextDBlockName($sBlockName);
            }
        }
    }

    /**
     * Load template from string
     * - Use load_from_string($sTemplate,$sBlockName) -
     *
     * @param string $sTemplate
     * @param string $sName
     */
    public function load_BlockFromVar ($sName, $sVariable){
        //$this->load_from_string($sString,$sBlockName);
        $this->DBlocks[$sName] = $sVariable;
        $nName = $this->NextDBlockName($sName);
        while ($nName != "") {
            $this->SetBlock($sName, $nName);
            $nName = $this->NextDBlockName($sName);
        }
    }


    private function NextDBlockName($sTemplateName){
        $sTemplate  = $this->DBlocks[$sTemplateName];
        $BTag       = strpos($sTemplate, "<!--Begin");
        if($BTag === false){
            return null;
        }else{
            $ETag   = strpos($sTemplate, "-->", $BTag);
            $sName  = substr($sTemplate, $BTag + 9, $ETag - ($BTag + 9));
            if(strpos($sTemplate, "<!--End" . $sName . "-->") > 0){
                return $sName;
            }else{
                return null;
            }
        }
    }

    private function SetBlock($sTplName, $sBlockName){
        if(!isset($this->DBlocks[$sBlockName])){
            $this->DBlocks[$sBlockName] = $this->getBlock($this->DBlocks[$sTplName], $sBlockName);
        }

        $this->DBlocks[$sTplName] = $this->replaceBlock($this->DBlocks[$sTplName], $sBlockName);

        $nName = $this->NextDBlockName($sBlockName);
        while($nName != ""){
            $this->SetBlock($sBlockName, $nName);
            $nName = $this->NextDBlockName($sBlockName);
        }
    }

    private function getBlock($sTemplate, $sName){
        $alpha 	= strlen($sName) + 12;
        $BBlock = strpos($sTemplate, "<!--Begin" . $sName . "-->");
        $EBlock = strpos($sTemplate, "<!--End" . $sName . "-->");
        if($BBlock === false || $EBlock === false){
            return null;
        }else{
            return substr($sTemplate, $BBlock + $alpha, $EBlock - $BBlock - $alpha);
        }
    }

    /**
     * Replaces an entire block with a var
     * <strong>Before</strong> &lt;!--BeginMyBlock-->Block content&lt;!--EndBlock-->
     * <strong>After</strong>  {MyBlock}
     *
     * @param string $sTemplate
     * @param string $sName
     * @return string
     */
    private function replaceBlock($sTemplate, $sName){
        $BBlock = strpos($sTemplate, "<!--Begin" . $sName . "-->");
        $EBlock = strpos($sTemplate, "<!--End" . $sName . "-->");
        if($BBlock === false || $EBlock === false){
            return $sTemplate;
        }else{
            return substr($sTemplate,0,$BBlock) . "{" . $sName . "}" . substr($sTemplate, $EBlock + strlen("<!--End" . $sName . "-->"));
        }
    }

    /**
     * Returns a block without parsing
     *
     * @param string $sBlockName
     * @return string
     */
    public function GetVar($sBlockName){
        return $this->DBlocks[$sBlockName];
    }

    /**
     * Return a block that has been parsed already
     *
     * @param string $sBlockName
     * @return string
     */
    public function getParsedVar($sBlockName) {
        return $this->ParsedBlocks[$sBlockName];
    }

    /**
     * Replaces the value of a variable in template
     *
     * @param string $sName
     * @param string $sValue
     */
    public function set_var($sVarName, $sValue){
        $this->ParsedBlocks[$sVarName] = $sValue;
    }
	
    /**
     * Recibe un array o un bloque sin parsear y lo/s elimina
     *
     */
    public function unset_blocks($bloques)
    {
        if(null === $bloques){ return; }

        if(is_array($bloques)){
            foreach ($bloques as $block){
                $this->DBlocks[$block] = "";
            }
            return;
        }

        $this->DBlocks[$bloques] = "";
        return;
    }
		
    /**
     * Print the value of a variable
     *
     * @param string $sVarName
     */
    public function print_var($sVarName){
        if(isset($this->ParsedBlocks[$sVarName])){
            echo $this->ParsedBlocks[$sVarName];
        }
    }

    function assign_var($sName){
        $this->tempout.= $this->ParsedBlocks[$sName];
    }

    /**
     * Fill HTML combo with values in vData
     *
     * @param array $vData
     * @param string $sSelectedKey
     * @param string $sBlockName
     */
    public function setVarCombo($vData,$sSelectedKey,$sBlockName){
        if(!is_array($vData) || !strlen($sBlockName)){
            throw new Exception(__METHOD__ ." Missing vData or BlockName");
            exit;
        }

        foreach($vData as $sKey => $sValue){
            $sSelected = ($sKey == $sSelectedKey) ? 'selected="selected"' : "";
            $sOpts .="<option value=\"$sKey\" $sSelected>$sValue</option>";
        }
        $this->set_var("$sBlockName",$sOpts);
    }


    /**
     * Parse a Block or full template
     *
     * @param string $sBlockName
     * @param bool $bRepeat
     */
    public function parse($sBlockName, $bRepeat=false,$bTranslate=false){
        if($bTranslate){
                $this->Translate();
        }

        if(isset($this->DBlocks[$sBlockName])){
            if($bRepeat && isset($this->ParsedBlocks[$sBlockName])){
                $this->ParsedBlocks[$sBlockName] .= $this->ProceedTpl($this->DBlocks[$sBlockName]);
            }else{
                $this->ParsedBlocks[$sBlockName] = $this->ProceedTpl($this->DBlocks[$sBlockName]);
            }
        }else{
            if(E_ERROR & error_reporting() == E_ERROR){
                throw new Exception("Block with name $sBlockName does't exist");
            }else{
                $this->sError .= "<div>Block with name <i>$sBlockName</i> does't exist</div>";
            }
        }
    }


    // This function should be called after parse main page
    // For examples see on project "New-Nina" page templatesabm.php
    public function put_without_parse ($sTplName, $sKeyName, $sValue) {
        $pBlock = $this->ParsedBlocks[$sTplName] ;
        $this->ParsedBlocks[$sTplName] = str_replace("@##_".$sKeyName."_##@", $sValue, $pBlock);
    }


    /**
     * Parse a Block or full template and return it
     *
     * El resultado debe ser seteado en el body de Response.
     *
     * @param string $sTplName
     * @param bool $bRepeat
     */
    public function pparse($sBlockName, $bRepeat=false,$bTranslate=false){
        $this->parse($sBlockName, $bRepeat,$bTranslate);
        return $this->ParsedBlocks[$sBlockName];
    }

    /**
     * Load all vars from a block in an array
     *
     * @param string $sTpl
     * @param string $beginSymbol
     * @param string $endSymbol
     * @return array
     */
    private function blockVars($sTpl,$beginSymbol = "{",$endSymbol="}"){
        $iCount = preg_match_all("|$beginSymbol([a-zA-Z0-9\_\.]*)$endSymbol|U", $sTpl, $vResult, PREG_PATTERN_ORDER);

        if( ($iCount > 0) && isset($vResult[1])){
                return(array_values(array_unique($vResult[1])));
        }else{
                return null;
        }
        /*
        $beginSymbolLength 	= strlen($beginSymbol);
        $endTag 			= 0;
        $vVars				= null;
        while (($beginTag = strpos($sTpl,$beginSymbol,$endTag)) !== false){
                if (($endTag = strpos($sTpl,$endSymbol,$beginTag)) !== false){
                        $vVars[] = substr($sTpl, $beginTag + $beginSymbolLength, $endTag - $beginTag - $beginSymbolLength);
                }
        }
        return $vVars;*/
    }


    /**
     * Load all the labels in an array
     * - Useful for traductions -
     *
     * @param string $sBlockName
     */
    private function blockLabels($sBlockName){
        $sTpl               = $this->DBlocks[$sBlockName];
        $beginSymbol        = $this->startVarTag .  $this->sLabelPrefix;
        $endSymbol          = $this->endVarTag;
        $beginSymbolLength  = strlen($beginSymbol);
        $endTag             = 0;
        $vVars              = null;
        while (($beginTag = strpos($sTpl,$beginSymbol,$endTag)) !== false){
            if (($endTag = strpos($sTpl,$endSymbol,$beginTag)) !== false){
                $this->vLabels[] = $this->sLabelPrefix . substr($sTpl, $beginTag + $beginSymbolLength, $endTag - $beginTag - $beginSymbolLength);
            }
        }
        if(is_array($this->vLabels)){
            $this->vLabels = array_unique($this->vLabels);
        }
    }

    /**
     * Delete Label from labels list
     *
     * @param string $sLabel
     */
    public function delLabel($sLabel){
        if(isset($this->vLabels[$sLabel])){
            unset($this->vLabels[$sLabel]);
        }
    }

    /**
     * @param string $sLabelPrefix
     */
    public function setLabelPrefix($sLabelPrefix){
        $this->sLabelPrefix = $sLabelPrefix;
    }

    /**
     * Return all collected labels
     *
     * @return array
     */
    public function getLabels(){
        return $this->vLabels;
    }

    public function setTranlateCallback($sFunctionName){
        $this->sFunTransaltion = $sFunctionName;
    }

    private function Translate(){
        if($this->sFunTransaltion){
                call_user_func($this->sFunTransaltion);
                $this->vLabels = null;
        }
    }

    private function ProceedTpl($sTpl){
        $vars = $this->blockVars($sTpl,"{","}");
        if(is_array($vars)){
                foreach ($vars as $value){
                        if(preg_match("/^[\w\_][\w\_]*$/",$value)){
                                if(isset($this->ParsedBlocks[$value])){
                                        $sTpl = str_replace("{".$value."}",$this->ParsedBlocks[$value],$sTpl);
                                }else{
                                        if(isset($this->DBlocks[$value])){
                                                $this->parse($value, false);
                                                $sTpl = str_replace("{".$value."}", $this->ParsedBlocks[$value], $sTpl);
                                        }else{
                                                $sTpl = str_replace("{".$value."}","",$sTpl);
                                        }
                                }
                        }
                }
        }
        return $sTpl;
    }

    public function PrintAll(){
        $res = "<table border=\"1\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">";
        $res .= "<tr bgcolor=\"#C0C0C0\" align=\"center\"><td>Key</td><td>Value</td></tr>";
        $res .= "<tr bgcolor=\"#FFE0E0\"><td colspan=\"2\" align=\"center\">ParsedBlocks</td></tr>";
        reset($this->ParsedBlocks);
        while(list($key, $value) = each($this->ParsedBlocks)){
                $res .= "<tr><td><pre>" . htmlspecialchars($key) . "</pre></td>";
                $res .= "<td><pre>" . htmlspecialchars($value) . "</pre></td></tr>";
        }
        $res .= "<tr bgcolor=\"#E0FFE0\"><td colspan=\"2\" align=\"center\">DBlocks</td></tr>";
        reset($this->DBlocks);
        while(list($key, $value) = each($this->DBlocks)){
                $res .= "<tr><td><pre>" . htmlspecialchars($key) . "</pre></td>";
                $res .= "<td><pre>" . htmlspecialchars($value) . "</pre></td></tr>";
        }
        $res .= "</table>";
        return $res;
    }
}