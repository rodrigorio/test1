<?php
/** Description of class CategoriaMySQLIntermediary
 *
 */
 
 
class CategoriaMySQLIntermediary extends CategoriaIntermediary
{
     static $singletonInstance = 0;


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
		if (!self::$singletonInstance){
			$sClassName = __CLASS__;
			self::$singletonInstance = new $sClassName($conn);
		}
		return(self::$singletonInstance);
	}
	
	
 private  function insertar(Categoria $oCategoria)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into categorias ".
                    " set nombre =".$db->escape($oCategoria->getNombre(),true)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
 private  function actualizar(Categoria $oCategoria)
   {
		try{
			$db = $this->conn;
			$sSQL =	" update categorias ".
                    " set nombre =".$db->escape($oCategoria->getNombre(),true)." " .
                    " where id =".$db->escape($oCategoria->getId(),false,MYSQL_TYPE_INT)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar(Categoria $oCategoria)
    {
        try{
			if($oCategoria->getId() != null){
            	return actualizar($oCategoria);
            }else{
				return insertar($oCategoria);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }

public final function obtener($filtro, &$foundRows = 0){
	 	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        c.id as iId, c.nombre as sNombre
                        FROM
                       categorias c ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

			$acategoriasategorias = array();
            while($oObj = $db->oNextRecord()){
            	$oCategoria 		= new stdClass();
            	$oCategoria->iId 	= $oObj->iId;
            	$oCategoria->sNombre= $oObj->sNombre;
            	$acategoriasategorias[]		= Factory::getCategoriaInstance($oCategoria);
            }

            //si es solo un elemento devuelvo el objeto si hay mas de un elemento o 0 devuelvo el array.
            if(count($acategoriasategorias) == 1){
                return $aCategorias[0];
            }else{
                return $aCategorias;
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
    
	//borra muchas especialidades
	//public function borrar($objects){}
    
    //borra una especialidad
    public function borrar(Categoria $oCategoria) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from categorias where id=".$db->escape($oCategoria->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

	
    public function existe($filtro){}
    
    public function listar(&$iRecordsTotal,$sOrderBy=null,$sOrder=null,$iIniLimit = null,$iRecordCount = null){
    	try{
			$db = $this->conn;
			$sSQL = "select SQL_CALC_FOUND_ROWS id as iICategoria, nombre as sNombre from categorias " ;
			
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
    

    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
}
?>	