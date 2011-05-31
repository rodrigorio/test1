<?php
/**
 * Intermediary Abstract
 * @author Rodrigo A. Rio <rodrigorio@netpowermdp.com.ar>
 * @abstract 
 * @package Persistence
 */

/**
 * @abstract 
 * @package Persistence
 */
abstract class Intermediary{
    /**
     * @var DB
     */
    protected $conn;

    /**
     * @param object $conn
     */
    protected function __construct(IMYSQL $conn){
            $this->conn	= $conn;
    }

    /**
     * @param DB $conn;
     */
    abstract  protected static function &getInstance(IMYSQL $conn);
}
?>