<?php
/**
 * Mysql Driver
 */


/**
* Usar SINGLETON
*/

class MYSQLException extends Exception {
 	public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return  "Mysql: [{$this->code}]: {$this->message}\n" . $this->getTraceAsString();
    }
}

class MYSQL{
	/**
	 * Host Name or IP
	 *
	 * @var string
	 */
	private $sHost			= null;
	/**
	 * Database name
	 *
	 * @var string
	 */
	private $sDatabase		= null;
	/**
	 * Username
	 *
	 * @var string
	 */
	private $sUsername		= null;
	/**
	 * Password
	 *
	 * @var string
	 */
	private $sPass			= null;
	/**
	 * Connections id
	 *
	 * @var array of resource
	 */
	private $LINK_ID		= null;
	/**
	 * Query Id
	 *
	 * @var array of resource
	 */
	private $QUERY_ID		= null;
	/**
	 * Tables Id
	 *
	 * @var array
	 */
	private $TABLES_ID		= null;
	private $RECORD 		= null;
	private $ROW			= null;
	private $FIELDS			= null;
	/**
	 * Error code
	 *
	 * @var int
	 */
	private $ERRNO			= 0;
	/**
	 * Error Message
	 *
	 * @var string
	 */
	public $ERROR			= null;
	private $VECFIELDS 		= null;
	private $RESULTADO 		= null;
	private $bShowErrors	= true;
	private $bExceptions	= true;
	private $bUQUERY  		= false;
	private	$bAutoCommit 	= false;
	private $bInTransaction = false;
	private $sLastQuery 	= null;

	/**
	* @param $host
	* @param $db
	* @param $user
	* @param $pw
	* @param $bAutoCommit
	*/
	public function __construct($host=null, $user=null, $pw=null, $db=null, $bAutoCommit=true){
		if( !is_null($host) &&
		!is_null($db) &&
		!is_null($user) &&
		!is_null($pw)){
			$this->sHost = $host;
			$this->sDatabase = $db;
			$this->sUsername=$user;
			$this->sPass=$pw;
			$this->bAutoCommit = $bAutoCommit;
			$this->connect(0);
			$this->QUERY_ID = array();
			$this->ROW = array();
			if(!$this->bAutoCommit){
				$this->query("SET AUTOCOMMIT=0");
			}
		}
	}

	/**
	* @param int $iNum
	*/
	private function halt($iNum){
		if($this->bShowErrors){
			$this->error($iNum);
			if($this->bExceptions){
				throw new MYSQLException($this->ERROR ,$this->ERRNO);
			}else{
				trigger_error("MYSQL Error: $this->ERRNO: $this->ERROR. Query: " . $this->sLastQuery[$iNum]);
			}
		}
	}

	/**
	* @param int $iNum
	*/
	private function connect($iNum = 0){
		if(!is_resource($this->LINK_ID[$iNum])){
			$this->LINK_ID[$iNum]= @mysql_connect($this->sHost,$this->sUsername,$this->sPass,false);
			if($this->LINK_ID[$iNum] === false){
				throw new MYSQLException(mysql_error(),mysql_errno());
			}else{
				if (!@mysql_select_db($this->sDatabase,$this->LINK_ID[$iNum]))
				$this->halt($iNum);
			}
		}
	}

	/**
	 * @return bool
	 */
	private function error($iNum = 0){
		$this->ERRNO = @mysql_errno($this->LINK_ID[$iNum]);
		$this->ERROR = @mysql_error($this->LINK_ID[$iNum]);
		if($this->ERRNO!=0){
			return $this->ERROR;
		}else{
			return false;
		}

	}

	/**
	 * @param string $str
	 * @param bool $bQuote
	 * @return string
	 */
	public function escape($str=null, $bQuote = false){
            if (is_null($str) || strlen($str) == 0){
                    return "NULL";
            }
            if($bQuote){
                    return "'" . mysql_real_escape_string($str) . "'" ;
            }else{
                    return mysql_real_escape_string($str);
            }
	}

	/**
	*
	* @param string $Query_Sting
	* @param bool	$bNew
	* @return bool | int
	*/
	public function query($Query_String,$bNew = false){

		$iNum = $bNew ? count($this->QUERY_ID) : 0;
		$this->connect($iNum);
		$this->free_results($iNum);

		$this->QUERY_ID[$iNum] = mysql_query($Query_String);
		$this->ROW[$iNum] = null;
		if(is_resource($this->QUERY_ID[$iNum]) || $this->QUERY_ID[$iNum]){
			$this->sLastQuery[$iNum] = $Query_String;
			$this->bUQUERY = false;
			return $iNum;
		}elseif($this->error($iNum)){
			$this->sLastQuery[$iNum] = null;
			$this->halt($iNum);
			return false;
		}
	}

	/**
	 * @return bool
	 */
	public function start_trans(){
		$i = $this->query("START TRANSACTION",true);
		$this->bInTransaction = true;
		$this->free_results($i);
		return $this->bInTransaction;
	}

	public function begin_transaction(){
		return $this->start_trans();
	}

	/**
	 * @return bool
	 */
	public function commit(){
		if(!$this->bInTransaction) return false;
		$i = $this->query("COMMIT",true);
		$res = !$this->error($i);
		if(!$res){
			$res =  !$this->rollback();
		}
		$this->bInTransaction = false;
		return $res;
	}

	public function commit_transaction(){
		return $this->commit();
	}

	/**
	 * @return bool
	 */
	public function rollback(){
		if(!$this->bInTransaction) return false;
		$res = $this->query("ROLLBACK");
		$this->bInTransaction = false;
		return $res;
	}

	public function rollback_transaction(){
		return $this->rollback();
	}

	/**
	 * @return int
	 */
	public function insert_id(){
		return mysql_insert_id($this->LINK_ID);
	}

	/**
	 * Free query results
	 *
	 * @param int $iNum
	 */
	public function free_results($iNum = 0){
		if(is_resource($this->QUERY_ID[$iNum])){
			mysql_free_result($this->QUERY_ID[$iNum]);
			unset($this->QUERY_ID[$iNum]);
		}
	}

	/**
	 * Move to next record
	 *
	 * @param int $iNum
	 * @return bool
	 */
	public function next_record($iNum = 0){
		if(!is_resource($this->QUERY_ID[$iNum])){
			return false;
		}
		$this->ROW[$iNum] = mysql_fetch_array($this->QUERY_ID[$iNum],MYSQL_BOTH);

		return $this->ROW[$iNum] !== FALSE;
	}

	/**
	 * Move to specific record
	 *
	 * @param int $pos
	 * @param int $iNum
	 * @return bool
	 */
	public function seek($pos,$iNum = 0){
		if(is_resource($this->QUERY_ID[$iNum]) && ($pos <= mysql_num_rows($this->QUERY_ID[$iNum]) -1) && !$this->bUQUERY ){
			$this->ROW[$iNum] = mysql_data_seek($this->QUERY_ID[$iNum],$pos);
		}else $this->ROW[$iNum] = null;

		return $this->ROW[$iNum] !== FALSE;
	}

	/**
	* @return int
	*/
	public function num_rows($iNum = 0){
		return !$this->bUQUERY ? mysql_num_rows($this->QUERY_ID[$iNum]) : null;
	}

	/**
	* @return int
	*/
	public function num_fields($iNum = 0){
		return mysql_num_fields($this->QUERY_ID[$iNum]);
	}

	/**
	* @param int $iNum
	* @return int
	*/
	public function affected_rows($iNum = 0){
		return mysql_affected_rows($this->LINK_ID[$iNum]);
	}

	/**
	 * @param string $name
	 * @param int $iNum
	 * @return string
	 */
	public function f($name,$iNum = 0){
		if(!is_array($this->ROW) && !$this->next_record($iNum)){
			return null;
		}
		if(isset($this->ROW[$iNum][$name])){
			return $this->ROW[$iNum][$name];
		}else return null;
	}


	/**
	 * @param string $name
	 */
	public function p($name,$iNum = 0){
		echo $this->f($name,$iNum = 0);
	}

	/**
	* @param int $iNum
	*/
	public function disconnect($iNum = 0){
		if(is_resource($this->QUERY_ID[$iNum])){
			mysql_free_result($this->QUERY_ID[$iNum]);
		}
		mysql_close($this->LINK_ID[$iNum]);
	}


	/**
	 * Unbuffered Query
	 * @param string $Query_String
	 * @return bool
	 */
	public function uquery($Query_String,$iNum = 0){
		$this->free_results($iNum);
		$this->connect();
		$this->ERRNO = 0;


		$this->QUERY_ID[$iNum] = mysql_unbuffered_query($Query_String,$this->LINK_ID);
		$this->ROW[$iNum] = null;
		if(is_resource($this->QUERY_ID[$iNum]) || $this->QUERY_ID[$iNum]){
			$this->sLastQuery[$iNum] = $Query_String;
			$this->bUQUERY = true;
			return true;
		}elseif($this->error($iNum)){
			$this->sLastQuery[$iNum] = null;
			$this->halt($iNum);
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
		$i = $this->query($sSQL,true);
		if($this->next_record($i)){
			$sResult =$this->f(0,$i);
			$this->free_results($i);
			return $sResult;
		}else{
			$this->free_results($i);
			return null;
		}
	}

	/**
	 * Return a field value from DB using $sSQL, only returns the firstf field result
	 *
	 * @param string $sSQL
	 * @return string
	 */
	public function getDbValue($sSQL = null){

		if(!$sSQL){
			return null;
		}
		$i = $this->query($sSQL, true);
		if($this->next_record($i)){

			$sResult = $this->f(0,$i);
			$this->free_results($i);
			return $sResult;
		}else{
			$this->free_results($i);
			return null;
		}
	}


	/**
	 * Return array using SQL query ID => Value (Field 0 => Field 1)
	 *
	 * @param string $sSSQL
	 * @return array
	 */
	public function getDBArrayQuery($sSSQL){
		if(!$sSSQL){
			return null;
		}
		$oVec	= null;
		$i 		= $this->query($sSSQL,true);
		while($this->next_record($i)){
			$iId 				= $this->f(0,$i);
			$sValue 		= $this->f(1,$i);
			$oVec[$iId]	= $sValue;
		}
		$this->free_results($i);
		return $oVec;
	}

/**
	 * Return Object using SQL query. If query get more than one row the first one is returned.
	 *
	 * @param string $sSQL
	 * @return object
	 */
	public function & getDBObject($sSQL){
		$i = $this->query($sSQL,true);
		if($i === false){
			return false;
		}

		if($this->num_rows($i) > 0){
			$oReturn = mysql_fetch_object($this->QUERY_ID[$i]);
		}else{
			$oReturn = null;
		}
		return $oReturn;
	}

	/**
	 * Return Array of Object using SQL query.
	 *
	 * @param string $sSQL
	 * @return object
	 */
	public function & getDBObjectArray($sSQL){
		$i = $this->query($sSQL,true);
		if($i === false){
			return false;
		}

		$vReturn = null;
		if($this->num_rows($i) > 0){
			while( ($oReturn =  mysql_fetch_object($this->QUERY_ID[$i]))!== false){
				$vReturn[]	= $oReturn;
			}
		}else{
			$vReturn = null;
		}
		return $vReturn;
	}

}
?>
