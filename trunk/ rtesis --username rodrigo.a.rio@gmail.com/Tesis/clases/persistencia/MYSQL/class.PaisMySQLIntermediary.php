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
class PaisMySQLIntermediary extends PaisIntermediary
{
	private static $instance = null;

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
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
	
	public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
		try{
            $db = clone($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        p.id as iId, p.nombre as sNombre, p.codigo as sCodigo
                    FROM
                       paises p ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aPaises = array();
            while($oObj = $db->oNextRecord()){
            	$oPais 			= new stdClass();
            	$oPais->iId 	= $oObj->iId;
            	$oPais->sNombre	= $oObj->sNombre;
            	$oPais->sCodigo	= $oObj->sCodigo;
            	$aPaises[]		= Factory::getPaisInstance($oPais);
            }
            
            return $aPaises;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
	public  function insertar($oPais)
   		{
		try{
			$db = $this->conn;
			$sSQL =	" insert into paises ".
                    " set nombre =".$db->escape($oPais->getNombre(),true)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
	public  function actualizar($oPais)
   	{
		try{
			$db = $this->conn;
			$sSQL =	" update paises ".
                    " set nombre =".$db->escape($oPais->getNombre(),true)." " .
                    " where id =".$db->escape($oPais->getId(),false,MYSQL_TYPE_INT)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oPais)
    {
        try{
			if($oPais->getId() != null){
            	return $this->actualizar($oPais);
            }else{
				return $this->insertar($oPais);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
 	public function borrar($oPais) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from paises where id=".$db->escape($oPais->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
		
	public function actualizarCampoArray($objects, $cambios){
		
	}
 	
	public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        paises p 
					WHERE ".$this->crearCondicionSimple($filtro,"",false,"OR");

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ 
            	return false; 
            }
            return true;
    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
           	return false; 
        }
    }
}