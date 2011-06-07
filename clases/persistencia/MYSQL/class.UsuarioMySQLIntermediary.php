<?php


/**
 * Description of classUsuarioMySQLIntermediary
 *
 * @author Andres
 */
class UsuarioMySQLIntermediary extends UsuarioIntermediary
{ static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return GroupMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (!self::$singletonInstance){
			$sClassName = __CLASS__;
			self::$singletonInstance = new $sClassName($conn);
		}
		return(self::$singletonInstance);
	}
    

    private  function insert(Usuario $oUsuario)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into personas ".
                    " nombre =".$db->escape($oUsuario->getNombre(),true).", " .
                    " apellido =".$db->escape($oUsuario->getApellido,true).", " .
					" documento_tipos_id =".$db->escape($oUsuario->getDocumento_tipo_id,false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento,true).", " .
                    " sexo =".$db->escape($oUsuario->getSexo,true).", " .
                    " fechaNacimiento= ".$db->escape($oUsuario->getFechaNacimiento, false,MYSQL_TYPE_DATE);
                    " email =".$db->escape($oUsuario->getEmail,true).", " .
                    " telefono =".$db->escape($oUsuario->getTelefono,true).", " .
                    " celular =".$db->escape($oUsuario->getCelular,true).", " .
                    " fax =".$db->escape($oUsuario->getFax,true).", " .
                    " domicilio =".$db->escape($oUsuario->getDomicilio,true).", " .
                    " instituciones_id =".$db->escape($oUsuario->getInstituciones_id,false,MYSQL_TYPE_INT).", ".
                    " ciudades_id =".$db->escape($oUsuario->getCiudades_id,false,MYSQL_TYPE_INT).", ".
					" ciudadOrigen =".$db->escape($oUsuario->getCiudadOrigen,true).", " .
                    " codigoPostal =".$db->escape($oUsuario->getCodigoPostal,true).", " .
                    " empresa =".$db->escape($oUsuario->getEmpresa,true).", " .
                    " universidad =".$db->escape($oUsuario->getUniversidad,true).", " .
                    " secundaria =".$db->escape($oUsuario->getSecundaria,true).", " .
			        
			 $db->execSQL($sSQL);
             
             $sSQL =	" insert into usuarios ".
                    " sitioWeb=".$db->escape($oUsuario->getSitioWeb,true).", " .
					" especialidades_id =".$db->escape($oUsuario->getEspecialidades_id,false,MYSQL_TYPE_INT).", ".
                    " perfiles_id =".$db->escape($oUsuario->getPerfiles_id,false,MYSQL_TYPE_INT).", ".
					" contrasenia=".$db->escape($oUsuario->getContrasenia,true).", " .
			        " fechaAlta= ".$db->escape($oUsuario->getFechaAlta, false,MYSQL_TYPE_DATE);

			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

    
    public function obtenerUsuario($id)
    {
       try{
			$db = $this->conn;
            
			$sSQL =	" select SQL_CALC_FOUND_ROWS p.numeroDocumento as numeroDocumento,
            u.contasenia as contrasenia from personas p
            join usuarios u on p.id = u.id where p.id =".$id."";
            $oUsuario = $db->getDBObject($sSQL);
			if($oUsuario){

				return $oUsuario;
			}else{
				return null;
			}
			

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
}
?>
