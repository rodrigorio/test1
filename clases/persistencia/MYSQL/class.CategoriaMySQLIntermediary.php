<?php
/** Description of class CategoriaMySQLIntermediary
 *
 */
 
 
class CategoriaMySQLIntermediary extends CategoriaIntermediary
{
    private static $instance = null;

	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return CategoriaMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
	
	
 public  function insertar($oCategoria)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into categorias ".
                    " set nombre =".$db->escape($oCategoria->getNombre(),true).",".
                    " descripcion=".$db->escape($oCategoria->getDescripcion(),true)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
 public  function actualizar($oCategoria)
   {
		try{
			$db = $this->conn;
			$sSQL =	" update categorias ".
                    " set nombre =".$db->escape($oCategoria->getNombre(),true).", " .
                    " descripcion=".$db->escape($oCategoria->getDescripcion(),true)." " .
                    " where id =".$db->escape($oCategoria->getId(),false,MYSQL_TYPE_INT)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oCategoria)
    {
        try{
			if($oCategoria->getId() != null){
            	return $this->actualizar($oCategoria);
            }else{
				return $this->insertar($oCategoria);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }

	public final function obtener($filtro,&$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
	 	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        c.id as iId, c.nombre as sNombre, c.descripcion as sDescripcion
                        FROM
                       categorias c ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

			$aCategorias = array();
            while($oObj = $db->oNextRecord()){
            	$oCategoria 		= new stdClass();
            	$oCategoria->iId 	= $oObj->iId;
            	$oCategoria->sNombre= $oObj->sNombre;
            	$oCategoria->sDescripcion= $oObj->sDescripcion;
            	$aCategorias[]		= Factory::getCategoriaInstance($oCategoria);
            }

            return $aCategorias;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
    
	//borra muchas especialidades
	//public function borrar($objects){}
    
    //borra una especialidad
    public function borrar($oCategoria) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from categorias where id=".$db->escape($oCategoria->getId(),false,MYSQL_TYPE_INT));
			$db->commit();
			return true;
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}

	
    public function existe($filtro){}
    
    public function listar(&$iRecordsTotal,$sOrderBy=null,$sOrder=null,$iIniLimit = null,$iRecordCount = null){
    	try{
			$db = $this->conn;
			$sSQL = "select SQL_CALC_FOUND_ROWS id as iICategoria, nombre as sNombre, descripcion as sDescripcion from categorias " ;
			
			if (isset($sOrderBy) && isset($sOrder)){
				$sSQL .= " order by $sOrderBy $sOrder ";
			}
			
			if ($iIniLimit && $iRecordCount){
				$sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
			}
			$db->query($sSQL);
			
			while( ($oCategoria = $db->oNextRecord() ) ){
				$vResult[] = Factory::getCategoriaInstance($oCategoria);
			}
			$iRecordsTotal = (int) $db->getDBValue(" select FOUND_ROWS() as list_count ");

			return $vResult;
			$db->commit();
		
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}	
	}
    
 	public function actualizarCampoArray($objects, $cambios){} 	
}