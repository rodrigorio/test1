<?php
/**
 * @Classname: MysqlDescriptions
 * @author Juan M. Hidalgo
 * @version 18/11/2006
 * @Language PHP 5
 * @uses class.Descriptions.php
 */

require_once("class.Descriptions.php");

class MysqlDescriptions extends Descriptions {
	private static $singletonInstance;
	private $sTableName;
	private $sLangField;
	private $sLabelField;
	private $sDescField;
	private $db;

	/**
	 * @param string $sDefaultLanguage
	 * @param string $sCurrentLanguage
	 * @param MYSQL $DBH
	 * @param string $sTableName
	 * @param string $sLangField
	 * @param string $sLabelField
	 * @param string $sDescField
	 */
	public function __construct($sDefaultLanguage,$sCurrentLanguage,$DBH,$sTableName,$sLangField,$sLabelField,$sDescField){
		parent::__construct($sDefaultLanguage,$sCurrentLanguage);
		$this->sTableName	= $sTableName;
		$this->sLangField	= $sLangField;
		$this->sLabelField	= $sLabelField;
		$this->sDescField	= $sDescField;
		$this->db			= $DBH;
	}

	/**
	 * @param string $sDefaultLanguage
	 * @param string $sCurrentLanguage
	 * @param MYSQL $DBH
	 * @param string $sTableName
	 * @param string $sLangField
	 * @param string $sLabelField
	 * @param string $sDescField
	 */
	public static function instance($sDefaultLanguage,$sCurrentLanguage,$DBH,$sTableName,$sLangField,$sLabelField,$sDescField){
		if (!self::$singletonInstance) {
			self::$singletonInstance = new MysqlDescriptions($sDefaultLanguage,$sCurrentLanguage,$DBH,$sTableName,$sLangField,$sLabelField,$sDescField);
		}
		return self::$singletonInstance;
	}

	/**
	 * @param string $sLabel
	 * @return string
	 */
	public function getDescription($sLabel,$bCreate = true){
		$sSQL 	= 	"Select $this->sDescField from $this->sTableName " .
		" where $this->sLangField = '$this->sCurrentLanguage' " .
		" and $this->sLabelField like '$sLabel' ";

		$db 	= $this->db;
		$db->query("$sSQL");

		if($db->next_record()){
			return $db->f("$this->sDescField");
		}else{
			$sSQL 	= 	"Select $this->sDescField from $this->sTableName " .
						" where $this->sLangField = '$this->sDefaultLanguage' " .
						" and $this->sLabelField like '$sLabel' ";
			$db->query($sSQL);
			if($db->next_record()){
				return $db->f("$this->sDescField");
			}else{
				return $sLabel;
			}

		}
	}

	/**
	 * Insert new label
	 *
	 * @param string $sLang
	 * @param string $sLabel
	 * @param string $sDescription
	 * @return bool
	 */
	public function insertDescription($sLang,$sLabel,$sDescription){
		$db		= $this->db;
		$sSQL	= 	"insert into $this->sTableName ($this->sLangField,$this->sLabelField,$this->sDescField) values (" .
					"'" . $db->escape($sLang) . "', '" . $db->escape($sLabel) . "','" . $db->escape($sDescription) . "')";

		$db->query($sSQL);
		return (bool) $db->affected_rows() > 0;
	}

	/**
	 * Update label
	 *
	 * @param string $sLang
	 * @param string $sLabel
	 * @param string $sDescription
	 * @return bool
	 */
	public function setDescription($sLang,$sLabel,$sDescription){
		$db		= $this->db;
		$sSQL	= 	"update $this->sTableName set $this->sDescField= '" . $db->escape($sDescription) ."'" .
					" where $this->sLangField = '" .  $db->escape($sLang) . "' and $this->sLabelField = '" . $db->escape($sLabel) ."'";
		$db->query($sSQL);
		return (bool) $db->affected_rows() > 0;
	}

	public function labelExists($sLabel,$sLang=null){
		$db		= $this->db;
		$lang 	= $sLang != null ? $sLang : $this->sDefaultLanguage;
		$sSQL 	= "select * from $this->sTableName where $this->sLabelField = '" . $db->escape($sLabel) . "' and $this->sLangField = '" . $db->escape($lang) ."'";
		$db->query($sSQL);
		return (bool) $db->next();
	}
}