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
                    " set fecha = '".$oComentario->getFecha()."', ".
                    " descripcion =".$db->escape($oComentario->getDescripcion(),true).", " .
			        " valoracion =".$db->escape($oComentario->getValoracion(),false,MYSQL_TYPE_FLOAT).", " .
                    " usuario_id =".$db->escape($oComentario->getUsuarioId,false,MYSQL_TYPE_INT).", ".
                    " where id =".$db->escape($oComentario->getId(),false,MYSQL_TYPE_INT)." " ;	
			 
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
                    " usuario_id =".$db->escape($oComentario->getUsuarioId,false,MYSQL_TYPE_INT).", ".
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
	
	public function actualizarCampoArray($objects, $cambios){
		
	}
}