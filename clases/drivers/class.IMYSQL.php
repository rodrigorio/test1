<?php
/**
 * Mysql Driver
 * @author Rodrigo A. Rio <rodrigorio@netpowermdp.com.ar>
 * @package Drivers
 */

//@TODO Implementar prepared querys
class MYSQLException extends Exception {}

define("MYSQL_TYPE_STRING",1);
define("MYSQL_TYPE_INT",2);
define("MYSQL_TYPE_FLOAT",3);
define("MYSQL_TYPE_DATE",4);

class IMYSQL implements DB
{
    /**
     * Host Name or IP
     *
     * @var string
     */
    private $sHost = null;
    /**
     * Database name
     *
     * @var string
     */
    private $sDatabase = null;
    /**
     * Username
     *
     * @var string
     */
    private $sUsername = null;
    /**
     * Password
     *
     * @var string
     */
    private $sPass = null;
    /**
     * Connections id
     *
     * @var array of resource
     */
    /**
     * @var int
     */
    private $iPort = 3306;

    private $LINK_ID = null;
    /**
     * Query Id
     *
     * @var array of resource
     */
    private $QUERY_ID = null;
    /**
     * Tables Id
     *
     * @var array
     */
    private $TABLES_ID = null;
    private $RECORD = null;
    private $ROW = null;
    private $FIELDS = null;
    /**
     * Error code
     *
     * @var int
     */
    private $ERRNO = 0;
    /**
     * Error Message
     *
     * @var string
     */
    private $ERROR          = null;
    private $VECFIELDS      = null;
    private $RESULTADO      = null;
    private $bShowErrors    = true;
    private $bExceptions    = true;
    private $bAutoCommit    = false;
    private $bInTransaction = false;
    private $sLastQuery     = null;
	
    private $keyEncript = "ABC1234abc00934GGooslk667rpPMABC";
    /**
     * @param string $host
     * @param string $db
     * @param string $user
     * @param string $pw
     * @param string $bAutoCommit
     * @return MYSQL
     */
    public static function getInstance($host=null, $db=null, $user=null, $pw=null, $bAutoCommit=false, $sCharset="utf8"){
        if(!self::$singletonInstance){
            $sClass = __CLASS__;
            self::$singletonInstance = new $sClass($conn);
        }
        return(self::$singletonInstance);
    }

    /**
    * @param string $sHost
    * @param string $sUser
    * @param string $sPass
    * @param string $sDBName
    * @param int $iPort
    * @param bool $bAutoCommit
    */
    public function __construct($sHost=null, $sUser=null, $sPass=null, $sDBName=null, $iPort=3306, $bAutoCommit=false, $sCharset="utf8"){
        if(!is_null($sHost) && !is_null($sDBName) && !is_null($sUser) && !is_null($sPass)){
            $this->sHost = $sHost;
            $this->sDatabase = $sDBName;
            $this->sUsername=$sUser;
            $this->sPass=$sPass;
            $this->iPort = $iPort;
            $this->bAutoCommit = ($bAutoCommit == 1)?true:false;
            $this->connect();
            mysqli_set_charset($this->LINK_ID, $sCharset);
            $this->QUERY_ID = null;
            $this->ROW = array();
            mysqli_autocommit($this->LINK_ID, $this->bAutoCommit);
        }
    }

    /**
     * When clone the object, clean all data
     */
    public function __clone(){
        $this->QUERY_ID = null;
        $this->sLastQuery = null;
        $this->bInTransaction = false;
        $this->ROW = null;
        $this->ERRNO = null;
        $this->ERROR = null;
        $this->RECORD = null;
        $this->FIELDS = null;
    }

    /**
    *
    */
    private function halt(){
        if($this->bShowErrors){
            if($this->error()){
                //SystemLog::write($this->ERROR . " ***SQL: {$this->sLastQuery}",SYSTEMLOG_CRIT,true);
                if($this->bInTransaction){
                        $this->rollback();
                }
                throw new MYSQLException($this->ERROR ,$this->ERRNO);
            }else{
                throw new MYSQLException("Unknown error" ,$this->ERRNO);
            }
        }
    }

    /**
    * Connect to mysql server
    */
    private function connect(){
        if ( !($this->LINK_ID instanceof mysqli) || (!@mysqli_ping($this->LINK_ID))){
            $this->LINK_ID = @mysqli_connect($this->sHost,$this->sUsername,$this->sPass,$this->sDatabase,$this->iPort);
            if($this->LINK_ID === false){
                throw new MYSQLException( mysqli_connect_error(),mysqli_connect_errno() );
            }
        }
    }

    /**
     * @return bool
     */
    private function error(){
            $this->ERRNO = @mysqli_errno($this->LINK_ID);
            $this->ERROR = @mysqli_error($this->LINK_ID);
            if($this->ERRNO!=0){
                    return $this->ERROR;
            }else{
                    return false;
            }

    }


    /**
     * @param string $sDateTime (DDMMYYYY HHMMSS)
     * @param bool
     */
    private function formatDate($sDateTime, $bUseTime = false){
        if(strlen($sDateTime) < 8){
            return null;
        }
        list($sDate,$sTime) = explode(" ",$sDateTime);
        list($iDay,$iMonth,$iYear) = preg_split("[/.-]",$sDate);
        
        return $bUseTime ? $iYear . "-" . $iMonth . "-" . $iDay . " $sTime" : $iYear . "-" . $iMonth . "-" . $iDay;
    }

    /**
     * @param string $str
     * @param bool $bQuote
     * @return string
     */
    public function escape($str = null, $bQuote = false, $iType = MYSQL_TYPE_STRING)
    {        
        if(is_null($str) || strlen($str) == 0){
            return "NULL";
        }

        switch ($iType){
            case MYSQL_TYPE_STRING:
                $sValue = mysqli_real_escape_string($this->LINK_ID, $str);
                break;
            case MYSQL_TYPE_INT:                
                $sValue = (int)$str;
                break;
            case MYSQL_TYPE_FLOAT:
                $sValue = str_replace(",",".",$str);
                break;
            case MYSQL_TYPE_DATE:
                //$sValue = $this->formatDate($str, true);
                $sValue = $str;
                break;
            default:
                $sValue = mysqli_real_escape_string($this->LINK_ID,$str);
                break;
        }

        if($bQuote){
            return "'".$sValue."'";
        }else{
            return $sValue;
        }
    }

    /**
     * @return bool
     */
    public function start_trans(){
            $this->bInTransaction = mysqli_autocommit($this->LINK_ID, false);
            return $this->bInTransaction;
    }

    /**
     * @return bool
     */
    public function begin_transaction(){
            return $this->start_trans();
    }

    /**
     * @return bool
     */
    public function commit(){
            //if((!$this->bInTransaction) || ((int) $this->getDbValue("SELECT @@autocommit") == 0)) return false;
            $res = mysqli_commit($this->LINK_ID);
            if(!$res){
                    $res =  !$this->rollback();
            }
            //$this->bInTransaction = false;
            return $res;
    }

    /**
     * @return bool
     */
    public function commit_transaction(){
            return $this->commit();
    }

    /**
     * @return bool
     */
    public function rollback(){
            if(!$this->bInTransaction) return false;
            $res = mysqli_rollback($this->LINK_ID);
            $this->bInTransaction = false;
            return $res;
    }

    /**
     * @return bool
     */
    public function inTransaction(){
            return $this->bInTransaction;
    }

    /**
     * @return bool
     */
    public function rollback_transaction(){
            return $this->rollback();
    }

    /**
     * @return int
     */
    public function insert_id(){
            return mysqli_insert_id($this->LINK_ID);
    }

    /**
     * Free query results
     *
     */
    public function free_results(){
            //if(($this->QUERY_ID !== false) && ($this->QUERY_ID != null)){
            if($this->QUERY_ID instanceof mysqli_result){
                    mysqli_free_result($this->QUERY_ID);
            }
    }

    /**
     * Move to next record
     *
     * @return bool
     */
    public function next_record(){
            $this->ROW = mysqli_fetch_array($this->QUERY_ID);
            return $this->ROW != NULL;
    }

    /**
     * Move next record and return and object with current Row
     *
     * @return object
     */
    public function oNextRecord(){
            if($this->QUERY_ID === false){
                    return false;
            }
            return mysqli_fetch_object($this->QUERY_ID);
    }

    /**
     * Move to specific record
     *
     * @param int $pos
     * @return bool
     */
    public function seek($pos){
            if(is_resource($this->QUERY_ID) && ($pos <= mysqli_num_rows($this->QUERY_ID) -1) && !$this->bUQUERY ){
                    $this->ROW = mysqli_data_seek($this->QUERY_ID,$pos);
            }else $this->ROW = null;

            return $this->ROW !== FALSE;
    }

    /**
    * @return int
    */
    public function num_rows(){
            return mysqli_num_rows($this->QUERY_ID);
    }

    /**
    * @return int
    */
    public function num_fields(){
            return mysqli_num_fields($this->QUERY_ID);
    }

    /**
     * @return object
     */
    public function fetch_fields(){

            if( ($this->QUERY_ID instanceof mysqli_result) || ((get_class($this->QUERY_ID) == "com.caucho.quercus.lib.db.MysqliResult"))){
                    $mysqli_type = array();
                    $mysqli_type[0] = "DECIMAL";
                    $mysqli_type[1] = "TINYINT";
                    $mysqli_type[2] = "SMALLINT";
                    $mysqli_type[3] = "INTEGER";
                    $mysqli_type[4] = "FLOAT";
                    $mysqli_type[5] = "DOUBLE";

                    $mysqli_type[7] = "TIMESTAMP";
                    $mysqli_type[8] = "BIGINT";
                    $mysqli_type[9] = "MEDIUMINT";
                    $mysqli_type[10] = "DATE";
                    $mysqli_type[11] = "TIME";
                    $mysqli_type[12] = "DATETIME";
                    $mysqli_type[13] = "YEAR";
                    $mysqli_type[14] = "DATE";

                    $mysqli_type[16] = "BIT";

                    $mysqli_type[246] = "DECIMAL";
                    $mysqli_type[247] = "ENUM";
                    $mysqli_type[248] = "SET";
                    $mysqli_type[249] = "TINYBLOB";
                    $mysqli_type[250] = "MEDIUMBLOB";
                    $mysqli_type[251] = "LONGBLOB";
                    $mysqli_type[252] = "BLOB";
                    $mysqli_type[253] = "VARCHAR";
                    $mysqli_type[254] = "CHAR";
                    $mysqli_type[255] = "GEOMETRY";
                    $vFields = mysqli_fetch_fields($this->QUERY_ID);
                    if(is_array($vFields)){
                            foreach($vFields as $oField){
                                    $oField->type_desc = $mysqli_type[$oField->type];
                            }
                    }
                    return $vFields;
            }
    }

    /**
    * @return int
    */
    public function affected_rows(){
            return mysqli_affected_rows($this->LINK_ID);
    }

    /**
     * @param string $name
     * @param int $iNum
     * @return string
     */
    public function f($name){
            if(!is_array($this->ROW) && !$this->next_record()){
                    return null;
            }
            if(isset($this->ROW[$name])){
                    return $this->ROW[$name];
            }else return null;
    }


    /**
     * @param string $name
     */
    public function p($name){
            echo $this->f($name);
    }

    /**
    * @param int $iNum
    */
    public function disconnect(){
            if($this->QUERY_ID instanceof mysqli_result){
                    mysqli_free_result($this->QUERY_ID);
            }
            mysqli_close($this->LINK_ID);
    }
    /**
     * @return bool
     */
    private function checkEmptySQL($sSQL = null){
            if(!strlen($sSQL)){
                    throw new MYSQLException("The SQL QUERY is empty",0);
            }
    }

    /**
    *
    * @param string $sSQL
    * @return bool
    */
    public function query($sSQL){
            $this->checkEmptySQL($sSQL);
            $this->connect();
            $this->free_results();

            $this->QUERY_ID = mysqli_query($this->LINK_ID,$sSQL);
            $this->ROW = null;
            $this->sLastQuery = $sSQL;
            if($this->QUERY_ID !== false){
                    return true;
            }elseif($this->error()){
                    $this->halt();
                    return false;
            }
    }


    /**
     * Return a field value from db
     *
     * @param string $sTablename
     * @param string $sFieldName coma separated field list
     * @param string $sWhere where clause to use into query without "WHERE" word
     * @return string
     */
    public function getValue($sTablename = null, $sFieldName=null, $sWhere){
            if(($sTablename== null) || ($sFieldName == null) || ($sWhere == null) ){
                    return null;
            }
            $sSQL = "select " . $this->escape($sFieldName) . " from $sTablename where $sWhere ";
            return $this->getDbValue($sSQL);
    }

    /**
     * Return a field value from DB using $sSQL, only returns the firstf field result
     *
     * @param string $sSQL
     * @return string
     */
    public function getDbValue($sSQL = null){
            $this->checkEmptySQL($sSQL);
            $this->connect();
            $this->sLastQuery = $sSQL;
            $iQuery = mysqli_query($this->LINK_ID,$sSQL);
            if($iQuery !== false){
                    $iRow = mysqli_fetch_row ($iQuery);
                    $oResult = $iRow !== null ? $iRow[0] : null;
                    if($iQuery instanceof mysqli_result){
                            mysqli_free_result($iQuery);
                    }
                    return $oResult;
            }elseif($this->error()){
                    $this->halt();
                    return false;
            }
    }


    /**
     * Return array using SQL query ID => Value (Field 0 => Field 1)
     *
     * @param string $sSSQL
     * @return array
     */
    public function getDBArrayQuery($sSSQL){
            $this->checkEmptySQL($sSSQL);
            $this->connect();
            $oVec	= null;
            $this->sLastQuery = $sSSQL;
            $iQuery = mysqli_query($this->LINK_ID,$sSSQL);

            if($iQuery !== false){
                    while( ($row =mysqli_fetch_row ($iQuery) ) ){
                            $iId 				= $row[0];
                            $sValue 		= $row[1];
                            $oVec[$iId]	= $sValue;
                    }
                    if($iQuery instanceof mysqli_result){
                            mysqli_free_result($iQuery);
                    }
                    return $oVec;
            }elseif($this->error()){
                    $this->halt();
                    return false;
            }
    }


    /**
     * Return Object using SQL query. If query get more than one row the first one is returned.
     *
     * @param string $sSQL
     * @return object
     */
    public function &getDBObject($sSQL){
            $this->checkEmptySQL($sSQL);
            $this->connect();
            $this->sLastQuery = $sSQL;
            $iQuery = mysqli_query($this->LINK_ID,$sSQL);
            if($iQuery !== false){
                    $iRow =  mysqli_fetch_object($iQuery);
                    $oResult = $iRow !== null ? $iRow : null;
                    if($iQuery instanceof mysqli_result){
                            mysqli_free_result($iQuery);
                    }
                    return $oResult;
            }elseif($this->error()){
                    $this->halt();
                    return false;
            }
    }

    /**
     * Return Array of Object using SQL query.
     *
     * @param string $sSQL
     * @return array of object
     */
    public function &getDBObjectArray($sSQL){
            $this->checkEmptySQL($sSQL);
            $this->connect();
            $oVec	= null;
            $this->sLastQuery = $sSQL;
            $iQuery = mysqli_query($this->LINK_ID,$sSQL);
            if($iQuery !== false){
                    while( ($oRow =mysqli_fetch_object($iQuery) ) ){
                            $vReturn[]	= $oRow;
                    }
                    if($iQuery instanceof mysqli_result){
                            mysqli_free_result($iQuery);
                    }
                    return $vReturn;
            }elseif($this->error()){
                    $this->halt();
                    return false;
            }
    }

    /**
     * Exec SQL query
     * Use this for INSERT,UPDATE,DELETE and STORE PROCEDURES
     * @param string $sSQL
     */
    public function execSQL($sSQL = null){
            $this->checkEmptySQL($sSQL);
            $this->connect();
            $this->sLastQuery = $sSQL;

            $iQuery = mysqli_query($this->LINK_ID,$sSQL);


            if($iQuery !== false){
                if($iQuery instanceof mysqli_result){
                    mysqli_free_result($iQuery);
                }
            }elseif($this->error()){
                
                $this->halt();
            }
    }
    /**
     *Funciones para encriptar y desencriptar datos en la base de datos
     * @param data es el dato que quiero encriptar o desencriptar en la bd
     * @param convert es valor booleano que me dice si lo casteo a string (se usa normalmente en el where cuando necesito 
     * no diferenciar las mayusculas y minusculas)
     * ej:
     *  select * usuarios where CONVERT(AES_DECRYPT(campoEmail,keyEncript), CHAR) LIKE "%RodriGo.a.rio@gmail.com%" 
     *  acá busco en los usuarios el que tenga como email rodrigo.a.rio@gmail.com, mas alla de como haya ingresado 
     *  el email en el formulario(mayusculas o minusculas).
     */
    public function encryptData( $data = null, $convert = null) {
  		if ($data===null) {
  			return null;
  		}
  		
    	$str = "AES_ENCRYPT(".$data.",".$this->escape($this->keyEncript,true).")";
   		if ($convert !== null) {
    		$str = " CONVERT(".$str.",CHAR) ";
    	}
       	
        return $str;
  		 
    }
    
 	public function decryptData( $data = null , $convert = null ) {
  		if($data===null){
  			return null;
  		}
  		$str = "AES_DECRYPT(".$data.",".$this->escape($this->keyEncript,true).")";
  		
 		if ($convert !== null) {
    		$str = " CONVERT(".$str.",CHAR) ";
    	}
    	return  $str;
    }
}
?>