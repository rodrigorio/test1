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
    
    private function updateUsuario (Usuario $oUsuario)
    {
        try{
			$db = $this->conn;
			$sSQL =	" insert into personas ".

            //insertar codigopara update similar al insert


             $db->execSQL($sSQL);
			 $db->commit();


		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }

    public function guardarUsuario(Usuario $oUsuario)
    {
        try{
			if($oUsuario->getId() != null){
            return updateUsuario($oUsuario);
            }else{
				return insertUsuario($oUsuario);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
    private  function insertUsuario(Usuario $oUsuario)
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

    //este no le puse byid porque seguro le p onemos otros parametros
    public function obtenerUsuario($id)
    {
       try{
			$db = $this->conn;
            
			$sSQL =	" select p.numeroDocumento as numeroDocumento,
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
/**
 *se pueden agregar parametros para filtrar por campos
 */
    public function getListUsuarios(&$iRecordsTotal,$sOrderBy=null,$sOrder=null,$iIniLimit = null,$iRecordCount = null){
		try{
			$db = $this->conn;
			$sSQL = "select SQL_CALC_FOUND_ROWS p.numeroDocumento as numeroDocumento,
            u.contasenia as contrasenia from personas p
            join usuarios u on p.id = u.id where p.id =";
			if (isset($sOrderBy) && isset($sOrder)){
				$sSQL .= " order by $sOrderBy $sOrder ";
			}

			if ($iIniLimit && $iRecordCount){
				$sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
			}
			$db->query($sSQL);

			while( ($oUsuarios = $db->oNextRecord() ) ){
				$vResult[] = Factory::getTorneosInstance($oUsuarios);
			}
			$iRecordsTotal = (int) $db->getDBValue(" select FOUND_ROWS() as list_count ");

			return $vResult;
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

}
?>
