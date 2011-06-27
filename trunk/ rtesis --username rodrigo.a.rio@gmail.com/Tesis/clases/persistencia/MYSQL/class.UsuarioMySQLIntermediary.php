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
    const PERFIL_MODERADOR = 5;
    const PERFIL_INTEGRANTE_ACTIVO = 2;
    const PERFIL_INTEGRANTE_INACTIVO = 3;

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
                    	$sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
                    }
            
            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

            $aUsuarios = array();
            while($oObj = $db->oNextRecord()){
                $oUsuario                   = new stdClass();
                $oUsuario->iId              = $oObj->iId;
                $oUsuario->sNombre          = $oObj->sNombre;
                $oUsuario->sApellido        = $oObj->sApellido;
                $oUsuario->sSexo            = $oObj->sSexo;
                $oUsuario->dFechaNacimiento = $oObj->dFechaNacimiento;
                $oUsuario->sEmail           = $oObj->sEmail;
                $oUsuario->sTelefono        = $oObj->sTelefono;
                $oUsuario->sCelular         = $oObj->sCelular;
                $oUsuario->sFax             = $oObj->sFax;
                $oUsuario->sDomicilio       = $oObj->sDomicilio;
                $oUsuario->oCiudad          = null;
                $oUsuario->sCiudadOrigen    = $oObj->sCiudadOrigen;
                $oUsuario->sCodigoPostal    = $oObj->sCodigoPostal;
                $oUsuario->sEmpresa         = $oObj->sEmpresa;
                $oUsuario->sUniversidad     = $oObj->sUniversidad;
                $oUsuario->sSecundaria      = $oObj->sSecundaria;
                $oUsuario->sSitioWeb        = $oObj->sSitioWeb;
                $oUsuario->sNombreUsuario   = $oObj->sNombreUsuario;
                $oUsuario->sContrasenia     = $oObj->sContrasenia;
                $oUsuario->dFechaAlta       = $oObj->dFechaAlta;
                
                //creo el usuario
                $oUsuario = Factory::getUsuarioInstance($oUsuario);
                
                //creo el perfil con el usuario asignado
                $oPerfilAbstract            = new stdClass();
                $oPerfilAbstract->iId       = $oObj->perfiles_id;
                $oPerfilAbstract->usuario   = $oUsuario;
                switch($oObj->perfiles_id){
                    case self::PERFIL_ADMINISTRADOR:{ $oPerfil       = Factory::getAdministradorInstance($oPerfilAbstract); break; }
                    case self::PERFIL_MODERADOR:{ $oPerfil           = Factory::getModeradorInstance($oPerfilAbstract); break; }
                    case self::PERFIL_INTEGRANTE_ACTIVO:{ $oPerfil   = Factory::getIntegranteActivoInstance($oPerfilAbstract); break; }
                    case self::PERFIL_INTEGRANTE_INACTIVO:{ $oPerfil = Factory::getIntegranteInactivoInstance($oPerfilAbstract); break; }
                }

                echo "<pre>".print_r($oPerfil)."</pre>";
                
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

    public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        personas p 
                    JOIN 
                    	usuarios u ON p.id = u.id
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

    
    public function actualizarCampoArray($objects, $cambios){}


    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}

    //////////////////////////// FIN MATIAS ///////////////////////////

	public function registrar(Usuario $oUsuario){
        try{
			$db = $this->conn;
			$db->begin_transaction();
			$sSQL =	" insert personas " .
                    " set nombre =".$db->escape($oUsuario->getNombre(),true).", " .
                    " apellido =".$db->escape($oUsuario->getApellido(),true).", " .
					" documento_tipos_id =".$db->escape($oUsuario->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),true).", " .
                    " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
                    " fechaNacimiento= ".$db->escape($oUsuario->getFechaNacimiento(), false,MYSQL_TYPE_DATE);
					if($oUsuario->getEmail()){
	                	$sSQL .=" ,email = ".$db->escape($oUsuario->getEmail(),true)." ";
					}
			 $db->execSQL($sSQL);
			 $iUltimoId = $db->insert_id();
			 $sSQL =" insert usuarios ".
			        " set id= ".$iUltimoId.", ";
                    " prefiles_id=".self::PERFIL_INTEGRANTE_INACTIVO.", ";
                    " nombre=".$db->escape($oUsuario->getNombreUsuario(),true).", ";
                    " contrasenia=".$db->escape($oUsuario->getContrasenia(),true)." ";

			 $db->execSQL($sSQL);
			 $db->commit();
		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }
	public function enviarInvitacion($iIdUsuario , Usuario $oInvitado){
        try{
			$db = $this->conn;
			$db->begin_transaction();

			$sSQL =	" insert personas set";
					if($oInvitado->getNombre()){
						$sSQL .=" nombre =".$db->escape($oInvitado->getNombre(),true).", ";
					}
					if($oInvitado->getApellido()){
                    	$sSQL .=" apellido =".$db->escape($oInvitado->getApellido(),true).", ";
					}
			$sSQL .= " email = ".$db->escape($oInvitado->getEmail(),true)." ";
			$db->execSQL($sSQL);

			$iUltimoId = $db->insert_id();

			$sSQL =" insert usuario_x_invitado ".
			        " set usuarios_id= ".$iIdUsuario.", ";
                    " invitados_id=".$iUltimoId.", ";
                    " relacion=".$db->escape($oInvitado->getRelacion(),true)." ";
			$db->execSQL($sSQL);

			$sSQL =" insert invitados ".
			        " set id= ".$iUltimoId." ";
			$db->execSQL($sSQL);

			$db->commit();
			/**
			 * @todo falta funcion de enviar el email
			 */
		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }
////////////////////////////////////
    public function actualizar($oUsuario)

    {
        try{
			$db = $this->conn;
					
			if($oUsuario->getCiudad()!= null){
				$ciudadId = $oUsuario->getCiudad()->getId();
			}else {
				$ciudadId = null;
			}
        	if($oUsuario->getInstitucion()->getId()!= null){
				$institucionId = $oUsuario->getInstitucion()->getId();
			}else {
				$institucionId = null;
			}
			
            $db->begin_transaction();
            $sSQL = " update personas " .
                    " set nombre =".$db->escape($oUsuario->getNombre(),true).", " .
                    " apellido =".$db->escape($oUsuario->getApellido(),true).", " .
					" documento_tipos_id =".$db->escape($oUsuario->getDocumentoId(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),true).", " .
                    " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
                    " fechaNacimiento= ".$db->escape($oUsuario->getFechaNacimiento(), false,MYSQL_TYPE_DATE);
                    " email =".$db->escape($oUsuario->getEmail(),true).", " .
                    " telefono =".$db->escape($oUsuario->getTelefono(),true).", " .
                    " celular =".$db->escape($oUsuario->getCelular(),true).", " .
                    " fax =".$db->escape($oUsuario->getFax(),true).", " .
                    " domicilio =".$db->escape($oUsuario->getDomicilio(),true).", " .
                    " instituciones_id =".$institucionId.", ".
                    " ciudades_id =".$ciudadId.", ".
					" ciudadOrigen =".$db->escape($oUsuario->getCiudadOrigen(),true).", " .
                    " codigoPostal =".$db->escape($oUsuario->getCodigoPostal(),true).", " .
                    " empresa =".$db->escape($oUsuario->getEmpresa(),true).", " .
                    " universidad =".$db->escape($oUsuario->getUniversidad(),true).", " .
                    " secundaria =".$db->escape($oUsuario->getSecundaria(),true)."";


			 $db->execSQL($sSQL);

             $sSQL =" update usuarios ".
                    " set sitioWeb=".$db->escape($oUsuario->getSitioWeb(),true).", " .
					" especialidades_id =".$db->escape($oUsuario->getEspecialidades_id(),false,MYSQL_TYPE_INT).", ".
                    " perfiles_id =".$db->escape($oUsuario->getPerfiles_id(),false,MYSQL_TYPE_INT).", ".
					" contrasenia =".$db->escape($oUsuario->getContrasenia(),true).", " .
			        " fechaAlta= ".$db->escape($oUsuario->getFechaAlta(), false,MYSQL_TYPE_DATE);

			 $db->execSQL($sSQL);
			 $db->commit();


		}catch(Exception $e){
            $db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }

    public function guardar($oUsuario)
    {
        try{
			if($oUsuario->getId() != null){
            	return $this->actualizar($oUsuario);
            }else{
				return $this->insertar($oUsuario);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
    public function insertar($oUsuario)
   {
		try{
			if($oUsuario->getCiudad()!= null){
				$ciudadId = $oUsuario->getCiudad()->getId();
			}else {
				$ciudadId = null;
			}
        	if($oUsuario->getInstitucion()!= null){
				$institucionId = $oUsuario->getInstitucion()->getId();
			}else {
				$institucionId = null;
			}
			$db = $this->conn;
			$filtro["u.nombre"] 		= $oUsuario->getNombreUsuario();
			if($this->existe($filtro)){
				return 10;
			}
			$filtro["p.numeroDocumento"]= $oUsuario->getNumeroDocumento();
			if($this->existe($filtro)){
				return 11;
			}
			$filtro["p.email"]= $oUsuario->getEmail();
			if($this->existe($filtro)){
				return 12;
			}
			$db->begin_transaction();
			$sSQL =	" insert into personas ".
                    " set nombre =".$db->escape($oUsuario->getNombre(),true).", " .
                    " apellido =".$db->escape($oUsuario->getApellido(),true).", " .
					" documento_tipos_id =".$db->escape($oUsuario->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),false,MYSQL_TYPE_INT).", " .
                    " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
                    " fechaNacimiento= ".$db->escape($oUsuario->getFechaNacimiento(), true,MYSQL_TYPE_DATE).", " .
                    " email =".$db->escape($oUsuario->getEmail(),true).", " .
                    " telefono =".$db->escape($oUsuario->getTelefono(),true).", " .
                    " celular =".$db->escape($oUsuario->getCelular(),true).", " .
                    " fax =".$db->escape($oUsuario->getFax(),true).", " .
                    " domicilio =".$db->escape($oUsuario->getDomicilio(),true).", " .//revisar esto
                    " instituciones_id =".$db->escape($institucionId,true).", ".
                    " ciudades_id =".$db->escape($ciudadId,true).", ".
					" ciudadOrigen =".$db->escape($oUsuario->getCiudadOrigen(),true).", " .
                    " codigoPostal =".$db->escape($oUsuario->getCodigoPostal(),true).", " .
                    " empresa =".$db->escape($oUsuario->getEmpresa(),true).", " .
                    " universidad =".$db->escape($oUsuario->getUniversidad(),true).", " .
                    " secundaria =".$db->escape($oUsuario->getSecundaria(),true)." ";

			$db->execSQL($sSQL);
			$iLastId = $db->insert_id();
			if($oUsuario->getEspecialidad()!= null){
				$iEspecialidadId = $oUsuario->getEspecialidad()->getId();
			}else {
				$iEspecialidadId = null;
			}
             $sSQL =" insert into usuarios set ".
                    " id=".$db->escape($iLastId,false).", " .
                    " sitioWeb=".$db->escape($oUsuario->getSitioWeb(),true).", " .
					" especialidades_id =".$db->escape($iEspecialidadId,true).", ".
                    " perfiles_id =".self::PERFIL_INTEGRANTE_INACTIVO.", ".
					" nombre=".$db->escape($oUsuario->getNombreUsuario(),true).",".
					" contrasenia=".$db->escape($oUsuario->getContrasenia(),true)."";

			 $db->execSQL($sSQL);
			 $db->commit();
			 return true;

		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}

   
    public function borrar($oUsuario) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from usuarios where id=".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT));
            $db->execSQL("delete from personas where id=".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
	
	public function permisosPorPerfil($iIdPerfil){
	  try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
						CONCAT_WS('_',cp.`controlador`,a.`accion`),
						a.`activo`
						from `perfiles` p
						join `acciones_x_perfil` ap ON ap.`perfiles_id` = p.`id`
						join `acciones` a on a.`grupo` =  ap.`grupo`
						join `controladores_pagina` cp on cp.`id` = a.`controladores_pagina_id`
						WHERE p.`id` = $iIdPerfil";
            $db->query($sSQL);
            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            if(empty($foundRows)){ return null; }

            $db->query($sSQL);
            
            return $db->getDBArrayQuery(sSQL);
	  	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
}