<?php
/**
 * Description of class CiudadMySQLIntermediary
 *
 * @author Rodrigo A. Rio
 */
class CiudadMySQLIntermediaryMySQLIntermediary extends CiudadIntermediary
{
     static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return CiudadMySQLIntermediary
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
