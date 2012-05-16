<?php
class PublicacionMySQLIntermediary extends PublicacionIntermediary
{
 private static $instance = null;

	protected function __construct( $conn) {
		parent::__construct($conn);
	}

	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return PublicacionMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
	public  function existe($filtro){
		try{
			$db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);
   
            $sSQL = "SELECT
                        f.id as iId, f.titulo as sTitulo
                        FROM
                       fichas_abstractas f ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }
            			
			$db->query($sSQL);
			if($db->num_rows() > 0){
				return true;
			}else{
				return false;
			}
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}	
	}
	
	public function guardar($oPublicacion)
    {
        try{
			if($oPublicacion->getId() != null){
            	return $this->actualizar($oPublicacion);
            }else{
				return $this->insertar($oPublicacion);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
 	public  function insertar($oPublicacion)
    {
        try{
                
        	$db = $this->conn;
							               
                $sSQL =	" insert into fichas_abstractas ".
                    " set titulo =".$db->escape($oPublicacion->getTitulo(),true).", ".
                    " fecha = '".$oPublicacion->getFecha()."',".
                    " activo =".$db->escape($oPublicacion->isActivo(),false,MYSQL_TYPE_INT).", ".
                    " descripcion =".$db->escape($oPublicacion->getDescripcion(),true).", ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            
        	if($oPublicacion->getUsuarioId()!= null){
				$usuarioId = $oPublicacion->getUsuario()->getId();
							}else {
				$usuarioId = null;
			}
             $sSQL = " insert into publicaciones set ".
                    " id = ".$db->escape($iLastId,false,MYSQL_TYPE_INT).", " .  
             		" usuarios_id =".$db->escape($usuarioId,false,MYSQL_TYPE_INT).", ".
                    " moderado =".$db->escape($oPublicacion->isModerado(),false,MYSQL_TYPE_INT).", ".
                    " publico =".$db->escape($oPublicacion->isPublico(),false,MYSQL_TYPE_INT).", ".  
            		" activoComentarios =".$db->escape($oPublicacion->isActivoComentario(),false,MYSQL_TYPE_INT).", ".
                    " descripcionBreve =".$db->escape($oPublicacion->getDescripcionBreve(),true).", ".
            		" keywords =".$db->escape($oPublicacion->getKeywords(),true);			
			 $db->execSQL($sSQL);
			 $db->commit();
			 return true;
		}catch(Exception $e){
			return false;
			throw new Exception($e->getMessage(), 0);
		}
	}
   public function actualizar($oPublicacion)
   	{
		try{
			$db = $this->conn;
		        
			$sSQL =	" update fichas_abstractas ".
			        " set titulo =".$db->escape($oPublicacion->getTitulo(),true).", ".
                    " fecha = '".$oPublicacion->getFecha()."',".
                    " activo =".$db->escape($oPublicacion->isActivo(),false,MYSQL_TYPE_INT).", ".
                    " descripcion =".$db->escape($oPublicacion->getDescripcion(),true).", ";

            $db->execSQL($sSQL);
             
        	$sSQL = " update publicaciones set ".
                    " id = ".$db->escape($iLastId,false,MYSQL_TYPE_INT).", " .  
             		" usuarios_id =".$db->escape($usuarioId,false,MYSQL_TYPE_INT).", ".
                    " moderado =".$db->escape($oPublicacion->isModerado(),false,MYSQL_TYPE_INT).", ".
                    " publico =".$db->escape($oPublicacion->isPublico(),false,MYSQL_TYPE_INT).", ".  
            		" activoComentarios =".$db->escape($oPublicacion->isActivoComentario(),false,MYSQL_TYPE_INT).", ".
                    " descripcionBreve =".$db->escape($oPublicacion->getDescripcionBreve(),true).", ".
            		" keywords =".$db->escape($oPublicacion->getKeywords(),true);	
						 
			 $db->execSQL($sSQL);
			 $db->commit();
             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
	public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                          f.id as iId, 
                          f.titulo as sTitulo,
                          f.fecha as dFecha,
                          f.`activo` as bActivo,
                          f.`descripcion` as sDescripcion,
                          p.usuario_id as iUsuarioId,
                          p.moderado as bModerado,
                          p.publico as bPublico,
                          p.activoComentarios as bActivoComentario,
                          p.descripcionBreve as sDescripcionBreve,
                          p.keywords as sKeywords
                    FROM
                        fichas_abstractas f
                    JOIN
                        publicaciones p ON p.id = f.id";
                    
            if(!empty($filtro)){
                $sSQL .="WHERE".$this->crearCondicionSimple($filtro);
            }
            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aPublicaciones = array();
            while($oObj = $db->oNextRecord()){
            	$oPublicacion 			= new stdClass();
            	$oPublicacion->iId 		= $oObj->iId;
            	$oPublicacion->sTitulo  = $oObj->sTitulo;
            	$oPublicacion->dFecha	= $oObj->dFecha;
            	$oPublicacion->bActivo= $oObj->bActivo;
            	$oPublicacion->sDescripcion	= $oObj->sDescripcion;
            	$oPublicacion->iUsuarioId= $oObj->iUsuarioId;
            	$oPublicacion->bModerado= $oObj->bModerado;
            	$oPublicacion->bPublico 	= $oObj->bPublico;
            	$oPublicacion->bActivoComentario 	= $oObj->bActivoComentario;
            	$oPublicacion->sDescripcionBreve 	= $oObj->sDescripcionBreve;
            	$oPublicacion->sKeywords= $oObj->sKeywords;
  
            	$aPublicaciones[] = Factory::getPublicacionInstance($oPublicacion);
            }

            return $aPublicaciones;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
                          
                          
	public function borrar($iFichaAbstractaId)
   {
        try{
            $db = $this->conn;
            $db->execSQL("delete from fichas_abstractas where id = '".$iFichaAbstractaId."'");
            $db->commit();
            return true;
        }catch(Exception $e){
            return false;
            throw new Exception($e->getMessage(), 0);
        }
    }
   	 
    public function actualizarCampoArray($objects, $cambios){}  
}