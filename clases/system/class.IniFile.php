<?php

class IniFile{
	private $vContent;
	private $bSections;

	public function __construct($bSections = true){
		$this->bSections 	= $bSections;
		$this->vContent 	= null;
	}

	public function load($sFilename){
		$this->vContent = parse_ini_file($sFilename,$this->bSections);
	}

	public function setContent($sVarName,$sValue,$sSection = null){
		if(isset($sSection)){
			$this->vContent[$sSection][$sVarName] = $sValue;
		}else{
			$this->vContent[$sVarName] = $sValue;
		}
	}
	public function getContent($sValue,$sSection){
		if(isset($sSection)){
			return $this->vContent[$sSection][$sVarName];
		}else{
			return $this->vContent[$sVarName];
		}
	}

	public function save($sFilename = null){
		if(is_array($this->vContent) && $sFilename){
			if(!is_writable(dirname($sFilename)) || (file_exists($sFilename) && !is_writable($sFilename))){
				return false;
			}
			$fp = fopen($sFilename,"w");
			if($this->bSections){
				foreach($this->vContent as $sSection => $vValues){
					fputs($fp,"[$sSection]\n");
					foreach($vValues as $sKeyName => $sValue){
						fputs($fp,"$sKeyName = \"$sValue\"\n");
					}
				}
			}else{
				foreach($this->vContent as $sKeyName => $sValue){
						fputs($fp,"$sKeyName = \"$sValue\"\n");
					}
			}
			fclose($fp);
		}else{
			return false;
		}
		return true;
	}
}

?>