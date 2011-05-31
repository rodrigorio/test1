<?php
if(!class_exists("Descriptions")){
	abstract class Descriptions{
		protected static $sDefaultLanguage;
		protected static $sCurrentLanguage;

		public function __construct($sDefaultLanguage,$sCurrentLanguage){
			$this->sDefaultLanguage = $sDefaultLanguage;
			$this->sCurrentLanguage	= $sCurrentLanguage;
		}

		public abstract function getDescription($sLabel);
	}
}
?>