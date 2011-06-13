<?php

/**
 * Description of class ProvinciaMySQLIntermediary
 *
 * @author Rodrigo A. Rio
 */
class ProvinciaMySQLIntermediaryMySQLIntermediary extends ProvinciaIntermediary
{
     static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return ProvinciaMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (!self::$singletonInstance){
			$sClassName = __CLASS__;
			self::$singletonInstance = new $sClassName($conn);
		}
		return(self::$singletonInstance);
	}
}
?>
