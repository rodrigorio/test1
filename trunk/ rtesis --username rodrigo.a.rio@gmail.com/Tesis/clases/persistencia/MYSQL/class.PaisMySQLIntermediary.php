<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class PaisMySQLIntermediary
 *
 * @author Rodrigo A. Rio
 */
class PaisMySQLIntermediaryMySQLIntermediary extends PaisIntermediary
{
     static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return PaisMySQLIntermediary
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
