<?php

/**
 * Description of classUsuarioMySQLIntermediary
 *
 * @author Andres
 */
class UsuarioMySQLIntermediary extends UsuarioIntermediary
{
    /* tienen que corresponder con los ids de la tabla perfiles */
    const PERFIL_ADMINISTRADOR = 1;
    const PERFIL_MODERADOR = 2;
    const PERFIL_INTEGRANTE_ACTIVO = 3;
    const PERFIL_INTEGRANTE_INACTIVO = 4;

    private static $instance = null;

    protected function __construct($conn) {
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return GroupMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    /**
     * Se fija si existen objetos usuarios que cumplan con el filtro,
     * al objeto/s le asigna el perfil dependiendo lo que levanto de la DB.
     * Retorna null si no encuentra resutados, un objeto PerfilAbstract o un array de objetos PerfilAbstract.
     * arroja excepcion si hubo algun problema en la consulta.
     */
    public function obtener($filtro, &$foundRows = 0){
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        p.id as iId, p.nombre as sNombre, p.apellido as sApellido,
                        p.sexo as sSexo, p.fechaNacimiento as dFechaNacimiento,
                        p.email as sEmail, p.telefono as sTelefono, p.celular as sCelular,
                        p.fax as sFax, p.domicilio as sDomicilio, p.ciudadOrigen as sCiudadOrigen,
                        p.codigoPostal as sCodigoPostal, p.empresa as sEmpresa,
                        p.universidad as sUniversidad, p.secundaria as sSecundaria,

                        u.sitioWeb as sSitioWeb, u.perfiles_id, u.nombre as sNombreUsuario,
                        u.fechaAlta as dFechaAlta, u.contrasenia as sContrasenia
                    FROM
                        personas p JOIN usuarios u ON p.id = u.id ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro, "u");
                    }

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

			$aUsuarios = array();
            while($oObj = $db->oNextRecord()){
                $oUsuario 				= new stdClass();
                $oUsuario->iId 			= $oObj->iId;
                $oUsuario->sNombre 		= $oObj->sNombre;
                $oUsuario->sApellido 	= $oObj->sApellido;
                $oUsuario->sSexo 		= $oObj->sSexo;
                $oUsuario->dFechaNacimiento = $oObj->dFechaNacimiento;
                $oUsuario->sEmail 		= $oObj->sEmail;
                $oUsuario->sTelefono 	= $oObj->sTelefono;
                $oUsuario->sCelular	 	= $oObj->sCelular;
                $oUsuario->sFax 		= $oObj->sFax;
                $oUsuario->sDomicilio 	= $oObj->sDomicilio;
                $oUsuario->oCiudades 	= null;
                $oUsuario->sCiudadOrigen= $oObj->sCiudadOrigen;
                $oUsuario->sCodigoPostal= $oObj->sCodigoPostal;
                $oUsuario->sEmpresa		= $oObj->sEmpresa;
                $oUsuario->sUniversidad = $oObj->sUniversidad;
                $oUsuario->sSecundaria 	= $oObj->sSecundaria;
                $oUsuario->sSitioWeb 	= $oObj->sSitioWeb;
                $oUsuario->sNombreUsuario 	= $oObj->sNombreUsuario;
                $oUsuario->sContrasenia = $oObj->sContrasenia;
                $oUsuario->dFechaAlta 	= $oObj->dFechaAlta;
                //creo el usuario
                $oUsuario = Factory::getUsuarioInstance($oUsuario);
                //creo el perfil con el usuario asignado
                $oPerfilAbstract 		= new stdClass();
                $oPerfilAbstract->iId	= $oObj->perfiles_id;
                $oPerfilAbstract->usuario 	= $oUsuario;
                switch($oObj->perfiles_id){
                    case self::PERFIL_ADMINISTRADOR:{ $oPerfil       = Factory::getAdministradorInstance($oPerfilAbstract); break; }
                    case self::PERFIL_MODERADOR:{ $oPerfil           = Factory::getModeradorInstance($oPerfilAbstract); break; }
                    case self::PERFIL_INTEGRANTE_ACTIVO:{ $oPerfil   = Factory::getIntegranteActivoInstance($oPerfilAbstract); break; }
                    case self::PERFIL_INTEGRANTE_INACTIVO:{ $oPerfil = Factory::getIntegranteInactivoInstance($oPerfilAbstract); break; }
                }
                $aUsuarios[] = $oPerfil;
            }

            //si es solo un elemento devuelvo el objeto si hay mas de un elemento devuelvo el array.
            if(count($aUsuarios) == 1){
                return $aUsuarios[0];
            }else{
                return $aUsuarios;
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function existe($filtro){}

    public function actualizar(stdClass $object){}

    public function actualizarCampoArray($objects, $cambios){}

    public function insertar($objects){}

    public function guardar(stdClass $object){}

    public function borrar($objects){}

    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}

    //////////////////////////// FIN MATIAS ///////////////////////////
    
    private function actualizar(Usuario $oUsuario)
    {
        try{
			$db = $this->conn;
            $db->begin_transaction();
			$sSQL =	" update personas " .
                    " set nombre =".$db->escape($oUsuario->getNombre(),true).", " .
                    " apellido =".$db->escape($oUsuario->getApellido(),true).", " .
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

             $sSQL =" update usuarios ".
                    " set sitioWeb=".$db->escape($oUsuario->getSitioWeb,true).", " .
					" especialidades_id =".$db->escape($oUsuario->getEspecialidades_id,false,MYSQL_TYPE_INT).", ".
                    " perfiles_id =".$db->escape($oUsuario->getPerfiles_id,false,MYSQL_TYPE_INT).", ".
					" contrasenia=".$db->escape($oUsuario->getContrasenia,true).", " .
			        " fechaAlta= ".$db->escape($oUsuario->getFechaAlta, false,MYSQL_TYPE_DATE);

			 $db->execSQL($sSQL);
			 $db->commit();

            
		}catch(Exception $e){
            $db->rollbak_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }

    public function guardar(Usuario $oUsuario)
    {
        try{
			if($oUsuario->getId() != null){
            	return updateUsuario($oUsuario);
            }else{
				return insertarUsuario($oUsuario);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
    private  function insertar(Usuario $oUsuario)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into personas ".
                    " set nombre =".$db->escape($oUsuario->getNombre(),true).", " .
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
    public function obtener($id)
    {
       try{
			$db = $this->conn;
            
			$sSQL =	" select p.numeroDocumento as numeroDocumento,
            u.contasenia as contrasenia from personas p
            join usuarios u on p.id = u.id where p.id =".$id."";
            $oUsuario = $db->getDBObject($sSQL);
            if($oUsuario){
				return Factory::getUsuarioInstance($oUsuario);
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
    public function obtenerListaUsuarios(&$iRecordsTotal,$sOrderBy=null,$sOrder=null,$iIniLimit = null,$iRecordCount = null){
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
				$vResult[] = Factory::getUsuariosInstance($oUsuarios);//?????????????????
			}
			$iRecordsTotal = (int) $db->getDBValue(" select FOUND_ROWS() as list_count ");

			return $vResult;
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

    public function _delete(Usuario $oUsuario) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from usuarios where id=".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT));
            $db->execSQL("delete from personas where id=".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

}