<?php
/**
 * @Classname: IniDescriptions
 * @author Juan M. Hidalgo
 * @version 18/11/2006
 * @Language PHP 5
 * @uses class.Descriptions.php
 */

require_once("class.Descriptions.php");
class IniDescriptions extends Descriptions {
	private static $singletonInstance;
	private $sFilePath;
	private $IniFile;

	/**
	 * @param string $sDefaultLanguage
	 * @param string $sCurrentLanguage
	 * @param string $sFilePath
	 */
	public function __construct($sDefaultLanguage,$sCurrentLanguage,$sFilePath){
		parent::__construct($sDefaultLanguage,$sCurrentLanguage);
		$this->sFilePath = $sFilePath;
		if(file_exists($sFilePath)){
			$this->IniFile 	= parse_ini_file($sFilePath,true);
		}
	}

	/**
	 * @param string $sDefaultLanguage
	 * @param string $sCurrentLanguage
	 * @param string $sFilePath
	 */
	public static function instance($sDefaultLanguage,$sCurrentLanguage,$sFilePath){
		if (!self::$singletonInstance) {
			self::$singletonInstance = new IniDescriptions($sDefaultLanguage,$sCurrentLanguage,$sFilePath);
		}
		return self::$singletonInstance;
	}

	/**
	 * @param string $sLabel
	 * @return string
	 */
	public function getDescription($sLabel){
		if(isset($this->IniFile[$this->sCurrentLanguage][$sLabel])){
			return trim($this->IniFile[$this->sCurrentLanguage][$sLabel]);
		}elseif (isset($this->IniFile[$this->sDefaultLanguage][$sLabel])){
			return trim($this->IniFile[$this->sDefaultLanguage][$sLabel]);
		}else{
			return $sLabel;
		}

	}

}