<?php
/**
 * Description of class ComentarioMySQLIntermediary
 *
 * @author 
 */
class ComentarioMySQLIntermediary extends ComentarioIntermediary
{
	private static $instance = null;

	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return ComentarioMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
	
public  function insertar($oComentario)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into comentarios ".               
			        " set reviews_id =".$db->escape($oComentario->getReviewId(),false,MYSQL_TYPE_INT)." ,".
			        " publicaciones_id =".$db->escape($oComentario->getUsuarioId(),false,MYSQL_TYPE_INT)." ,".
			        " archivos_id =".$db->escape($oComentario->getArchivoId(),false,MYSQL_TYPE_INT)." ,".
                    " fecha = '".$oComentario->getFecha()."', ".
			        " descripcion =".$db->escape($oComentario->getDescripcion(),true).", " .
			        " valoracion =".$db->escape($oComentario->getValoracion(),false,MYSQL_TYPE_FLOAT).", " .
                    " usuario_id =".$db->escape($oComentario->getUsuarioId(),false,MYSQL_TYPE_INT)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public function actualizar($oComentario)
   {
		try{
			$db = $this->conn;
		        
			$sSQL =	" update comentarios ".
                    " set fecha = '".$oComentario->getFecha()."', ".
                    " descripcion =".$db->escape($oComentario->getDescripcion(),true).", " .
			        " valoracion =".$db->escape($oComentario->getValoracion(),false,MYSQL_TYPE_FLOAT).", " .
                    " where id =".$db->escape($oComentario->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oComentario)
    {
        try{
			if($oComentario->getId() != null){
            	return $this->actualizar($oComentario);
            }else{
				return $this->insertar($oComentario);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
	public function borrar($oComentario) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from comentarios where id=".$db->escape($oComentario->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
	
	public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null) {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        c.id as iId,
                        c.fecha as dFecha,
                        c.descripcion as sDescripcion,
                        c.valoracion as fValoracion,
                        c.usuarios_id as iUsuarioId
                        
                    FROM
                        comentario c ";

            $WHERE = array();
            if(isset($filtro['c.id']) && $filtro['c.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.id', $filtro['c.id'], MYSQL_TYPE_INT);
            }
                    
            if(isset($filtro['c.fecha']) && $filtro['c.fecha']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.fecha', $filtro['c.fecha'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['c.descripcion']) && $filtro['c.descripcion']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.descripcion', $filtro['c.descripcion'], MYSQL_TYPE_INT);
            }
              if(isset($filtro['c.valoracion']) && $filtro['c.valoracion']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.valoracion', $filtro['c.valoracion'], MYSQL_TYPE_INT);
            }
              if(isset($filtro['c.usuarios_id']) && $filtro['c.usuarios_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.usuarios_id', $filtro['c.usuarios_id'], MYSQL_TYPE_INT);
            }
            
            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }
            
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aComentario = array();
            while($oObj = $db->oNextRecord()){
                $oComentario                       = new stdClass();
                $oComentario->iId                  = $oObj->iId;
                $oComentario->sDescripcion         = $oObj->sDescripcion;
                $oComentario->fValoracion            = $oObj->fValoracion;
                $oComentario->iUsuarioId             = $oObj->iUsuarioId;
                
                $aComentarios[] = Factory::getComentarioInstance($oComentario);
            }

            return $aComentarios;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
	
	
	public function actualizarCampoArray($objects, $cambios){
		
	}
}