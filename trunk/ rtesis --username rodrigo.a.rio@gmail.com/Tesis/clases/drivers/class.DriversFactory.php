<?php
/**
 * Database Driver Factory
 * @author Rodrigo A. Rio <rodrigorio@netpowermdp.com.ar>
 * @package Domain
 */

class DriversFactory{
    private static $vInstances;
    private function  ___construct(){}

    /**
     * @param string $sDriverName
     * @param string $sHost
     * @param string $sUser
     * @param string $sPass
     * @param string $sDBName
     * @param int $iPort
     * @param bool $bAutocommit
     * @return DB
     */
    public static function getInstace($sDriverName,$sHost,$sUser,$sPass,$sDBName,$iPort,$bAutocommit){
        if(isset(self::$vInstances[$sDriverName])){
            return self::$vInstances[$sDriverName];
        }else{
            if(class_exists($sDriverName)){
                $oDB = new $sDriverName("$sHost","$sUser","$sPass","$sDBName",$iPort,$bAutocommit);
                self::$vInstances[$sDriverName] = $oDB;
                return $oDB;
            }else{
                throw new Exception("$sDriverName Database Driver not found",1);
            }
        }
    }
}
?>
