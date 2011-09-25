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

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        p.id as iId, p.nombre as sNombre, p.apellido as sApellido,
                        p.sexo as sSexo, p.fechaNacimiento as dFechaNacimiento,
                        p.email as sEmail, p.telefono as sTelefono, p.celular as sCelular,
                        p.fax as sFax, p.domicilio as sDomicilio, p.ciudadOrigen as sCiudadOrigen,
                        p.codigoPostal as sCodigoPostal, p.empresa as sEmpresa,
                        p.universidad as sUniversidad, p.secundaria as sSecundaria,
						p.`documento_tipos_id` as iTipoDocumentoId,
  						p.`numeroDocumento` as sNumeroDocumento,
                        u.sitioWeb as sSitioWeb, u.perfiles_id, u.nombre as sNombreUsuario,
                        u.fechaAlta as dFechaAlta, u.contrasenia as sContrasenia,
                        u.invitacionesDisponibles as iInvitacionesDisponibles,
                        p.nacionalidad as sNacionalidad
                    FROM
                        personas p JOIN usuarios u ON p.id = u.id ";
                    if(!empty($filtro)){
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
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
                $oUsuario->sNacionalidad 	= $oObj->sNacionalidad;
                $oUsuario->iTipoDocumentoId = $oObj->iTipoDocumentoId;
                $oUsuario->sNumeroDocumento = $oObj->sNumeroDocumento;
                $oUsuario->sSexo 		= $oObj->sSexo;
                $oUsuario->dFechaNacimiento = $oObj->dFechaNacimiento;
                $oUsuario->sEmail 		= $oObj->sEmail;
                $oUsuario->sTelefono 	= $oObj->sTelefono;
                $oUsuario->sCelular	 	= $oObj->sCelular;
                $oUsuario->sFax 		= $oObj->sFax;
                $oUsuario->sDomicilio 	= $oObj->sDomicilio;
                $oUsuario->oCiudad 		= null;
                $oUsuario->oInstitucion = null;
                $oUsuario->sCiudadOrigen= $oObj->sCiudadOrigen;
                $oUsuario->sCodigoPostal= $oObj->sCodigoPostal;
                $oUsuario->sEmpresa		= $oObj->sEmpresa;
                $oUsuario->sUniversidad = $oObj->sUniversidad;
                $oUsuario->sSecundaria 	= $oObj->sSecundaria;
                $oUsuario->sSitioWeb 	= $oObj->sSitioWeb;
                $oUsuario->sNombreUsuario 	= $oObj->sNombreUsuario;
                $oUsuario->sContrasenia = $oObj->sContrasenia;
                $oUsuario->dFechaAlta 	= $oObj->dFechaAlta;
                $oUsuario->iInvitacionesDisponibles = $oObj->iInvitacionesDisponibles;
                //creo el usuario
                $oUsuario = Factory::getUsuarioInstance($oUsuario);

                //creo el perfil con el usuario asignado
                $oPerfilAbstract            = new stdClass();
                $oPerfilAbstract->iId       = $oObj->perfiles_id;
                $oPerfilAbstract->oUsuario   = $oUsuario;
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

	public function registrar(Usuario $oUsuario,$iUserId){
        try{
			$db = $this->conn;
			$db->begin_transaction();
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
			$sSQL =	" update personas " .
                    " set nombre =".$db->escape($oUsuario->getNombre(),true).", " .
                    " apellido =".$db->escape($oUsuario->getApellido(),true).", " .
					" nacionalidad =".$db->escape($oUsuario->getNacionalidad(),true).", " .
					" documento_tipos_id =".$db->escape($oUsuario->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),true).", " .
                    " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
                    " fechaNacimiento= ".$db->escape($oUsuario->getFechaNacimiento(), true,MYSQL_TYPE_DATE);
					if($oUsuario->getEmail()){
	                	$sSQL .=" ,email = ".$db->escape($oUsuario->getEmail(),true)." ";
					}
					$sSQL .=" WHERE id = ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)." ";
			 $db->execSQL($sSQL);
			 $sSQL =" insert usuarios ".
			        " set id=  ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT).", ".
                    " perfiles_id=".self::PERFIL_INTEGRANTE_INACTIVO.", ".
                    " nombre=".$db->escape($oUsuario->getNombreUsuario(),true).", ".
                    " contrasenia=".$db->escape($oUsuario->getContrasenia(),true)." ";

			 $db->execSQL($sSQL);
			 $sSQL =" update usuario_x_invitado ".
			        " SET estado = 'aceptada' ".
			        " WHERE usuarios_id= ".$db->escape($iUserId,false,MYSQL_TYPE_INT)." ".
                    " AND invitados_id=".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)." ";
			 $db->execSQL($sSQL);
			 $db->commit();
		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }
    
	private function enviarEmail($orig, $dest, $asunto, $body){
    	try{
    		$mail = new PHPMailer();
    		//Con PluginDir le indicamos a la clase phpmailer donde se 
		  	//encuentra la clase smtp que como he comentado al principio de 
		  	//este ejemplo va a estar en el subdirectorio includes
			$mail->PluginDir = "../../system/";

		  	//Con la propiedad Mailer le indicamos que vamos a usar un 
		  	//servidor smtp
		  	$mail->Mailer = "smtp";
		  	
		  	//Asignamos a Host el nombre de nuestro servidor smtp
		  	$mail->Host = "smtp.hotpop.com";

		  	//Le indicamos que el servidor smtp requiere autenticación
		  	$mail->SMTPAuth = true;
		
		  	//Le decimos cual es nuestro nombre de usuario y password
		  	$mail->Username = "rrio@HotPOP.com"; 
		  	$mail->Password = "mipassword";
		
		  	//Indicamos cual es nuestra dirección de correo y el nombre que 
		  	//queremos que vea el usuario que lee nuestro correo
		 	$mail->From = $orig;
		 	$mail->FromName = "Eduardo Garcia";
		
			//el valor por defecto 10 de Timeout es un poco escaso dado que voy a usar 
			//una cuenta gratuita, por tanto lo pongo a 30  
			$mail->Timeout=30;
		
	  		//Indicamos cual es la dirección de destino del correo
			$mail->AddAddress($dest);
			
			//Asignamos asunto y cuerpo del mensaje
			//El cuerpo del mensaje lo ponemos en formato html, haciendo 
			//que se vea en negrita
			$mail->Subject = $asunto;
			$mail->Body = "<b>Mensaje de prueba </b><br/><p>$body</p>";
		
			//Definimos AltBody por si el destinatario del correo no admite email con formato html 
			$mail->AltBody = "Mensaje de prueba mandado con phpmailer en formato solo texto";
			
			$mail->IsHTML(true);
			//se envia el mensaje, si no ha habido problemas 
			//la variable $exito tendra el valor true
			$exito = $mail->Send();
			
			//Si el mensaje no ha podido ser enviado se realizaran 4 intentos mas como mucho 
			//para intentar enviar el mensaje, cada intento se hara 5 segundos despues 
			//del anterior, para ello se usa la funcion sleep	
	  		$intentos=1; 
			while ((!$exito) && ($intentos < 5)) {
				sleep(5);
		     	//echo $mail->ErrorInfo;
		     	$exito = $mail->Send();
		     	$intentos=$intentos+1;	
	   		}
		   if(!$exito) {
				echo "Problemas enviando correo electrónico a ".$valor;
				echo "<br/>".$mail->ErrorInfo;	
		   }else{
				echo "Mensaje enviado correctamente";
		   }
    	}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }
    
    public function sendMail($orig, $dest, $asunto, $body){
    	  // Varios destinatarios
			$para  = $dest;
			
			// subject
			$titulo = $asunto;
			
			// message
			$mensaje = $body;
			
			// Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
			$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
			$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Cabeceras adicionales
			$cabeceras .= "To: ".$dest. "\r\n";
			$cabeceras .= 'From: Registracion <'.$orig.'>' . "\r\n";
			$cabeceras .= 'Cc:' . "\r\n";
			$cabeceras .= 'Bcc: ' . "\r\n";
			
			// Mail it
			if (mail($para, $titulo, $mensaje, $cabeceras)){
				return true;
			}else{
				return false;
			}
    }
    /**
     * 
     * Enter description here ...
     * @param unknown_type $iIdUsuario
     * @param Invitado $oInvitado {
     * 		nombre
     * 		apellido
     * 		email
     * 		relacion
     * }
     * @throws Exception
     */
	public function enviarInvitacion($oUsuario , Invitado $oInvitado){
		
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

			$sSQL 	= " insert invitados ".
			        " set id= ".$iUltimoId."";
			$db->execSQL($sSQL);
			$iIdUsuario = $oUsuario->getId();
			$time 	= time();
			$token 	= md5($time);
			$sSQL =" insert usuario_x_invitado ".
			        " set usuarios_id= ".$iIdUsuario.", ".
                    " invitados_id=".$iUltimoId.", ".
                    " relacion=".$db->escape($oInvitado->getRelacion(),true).",".
			 		" token=".$db->escape($token,true)."";
			$db->execSQL($sSQL);

			$sSQL =" update usuarios u ".
			        " set u.invitacionesDisponibles = u.invitacionesDisponibles-1 ".
			 		" WHERE u.id= ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)."";
			$db->execSQL($sSQL);

			$db->commit();
			
			$nom 	= $oInvitado->getNombre();
			$ape 	= $oInvitado->getApellido();
			$email 	= $oInvitado->getEmail();
			
			$body 	= "<p>Usted ha sido invitado por ".$oUsuario->getNombre().", ".$oUsuario->getApellido()."";
			$body 	.= "<br/> para que pueda integrar la comunidad de profesionales de personas discapacitas, etc, etc.";
			$body 	.= "<a href='http://www.rodrigorio.com.ar/tesis/registracion?token=$token' > registrate</a>";
			$body 	.= "</p>";
			$msg = '
			<html>
			<head>
			  <title>Usted ha sido invitado para registrarse en .....</title>
			</head>
			<body>
			  <p>Haga click en el siguiente enlace para poder registrarse!</p>
			  <div>'.$body.'</div>
			</body>
			</html>
			';
			$asunto = "registracion";
			$dest 	= $oInvitado->getEmail();
			$orig	= "registracion@sistemadegestion.com";
			$this->sendMail($orig, $dest, $asunto, $msg);
			return  true;
		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }

    public function actualizar($oUsuario)
    {
        try{
			$db = $this->conn;
					
			if($oUsuario->getCiudad()!= null){
				$ciudadId = $oUsuario->getCiudad()->getId();
			}else {
				$ciudadId = 'null';
			}
        	if($oUsuario->getInstitucion()!= null){
				$institucionId = $oUsuario->getInstitucion()->getId();
			}else {
				$institucionId = 'null';
			}
			
            $db->begin_transaction();
            $sSQL = " update personas " .
                    " set nombre =".$db->escape($oUsuario->getNombre(),true).", " .
                    " apellido =".$db->escape($oUsuario->getApellido(),true).", " .
            		" nacionalidad =".$db->escape($oUsuario->getNacionalidad(),true).", " .
					" documento_tipos_id =".$db->escape($oUsuario->getTipoDocumento(), false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),true).", " .
                    " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
                    " fechaNacimiento= ".$db->escape($oUsuario->getFechaNacimiento(), true,MYSQL_TYPE_DATE).",".
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
                    " secundaria =".$db->escape($oUsuario->getSecundaria(),true)." ".
            		" WHERE id = ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)." ";
			$db->execSQL($sSQL);

            $sSQL =" update usuarios ".
                    " set sitioWeb=".$db->escape($oUsuario->getSitioWeb(),true).", " .
					" especialidades_id =".$db->escape($oUsuario->getEspecialidad(),false,MYSQL_TYPE_INT).", ".
               //     " perfiles_id =".$db->escape($oUsuario->getPerfiles_id(),false,MYSQL_TYPE_INT).", ".
					" contrasenia =".$db->escape($oUsuario->getContrasenia(),true)." ".
            		" WHERE id = ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)." ";
			$db->execSQL($sSQL);
			$db->commit();
			return true;
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
					" nacionalidad =".$db->escape($oUsuario->getNacionalidad(),true).", " .
					" documento_tipos_id =".$db->escape($oUsuario->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),false,MYSQL_TYPE_INT).", " .
                    " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
                    " fechaNacimiento= ".$db->escape($oUsuario->getFechaNacimiento(), true,MYSQL_TYPE_DATE).", " .
                    " email =".$db->escape($oUsuario->getEmail(),true).", " .
                    " telefono =".$db->escape($oUsuario->getTelefono(),true).", " .
                    " celular =".$db->escape($oUsuario->getCelular(),true).", " .
                    " fax =".$db->escape($oUsuario->getFax(),true).", " .
                    " domicilio =".$db->escape($oUsuario->getDomicilio(),true).", " .//revisar esto
                    " instituciones_id =".$db->escape($institucionId,false,MYSQL_TYPE_INT).", ".
                    " ciudades_id =".$db->escape($ciudadId,false,MYSQL_TYPE_INT).", ".
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
                    " id=".$db->escape($iLastId,false,MYSQL_TYPE_INT).", " .
                    " sitioWeb=".$db->escape($oUsuario->getSitioWeb(),true).", " .
					" especialidades_id =".$db->escape($iEspecialidadId,true).", ".
                    " perfiles_id =".self::PERFIL_INTEGRANTE_INACTIVO.", ".
					" nombre=".$db->escape($oUsuario->getNombreUsuario(),true).",".
					" contrasenia=".$db->escape(md5($oUsuario->getContrasenia()),true)." ";

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

    /**
     *  El array se arma con las tablas 'controladores_pagina', 'acciones', 'acciones_x_perfil' y 'perfiles'
     *
     *  En la tabla controladores aparecen los diferentes page controllers del sistema. La cadena tiene el formato "modulo_controlador"
     *
     *  En la tabla acciones se relacionan los controladores x accion y a cada accion se le asigna un grupo.
     *
     *  Los id de grupos posibles para las acciones son:
     *      1)ADMIN 2)MODERADOR 3)INTEGANTE ACTIVO 4)INTEGANTE INACTIVO 5)VISITANTES
     * 
     */
    public function permisosPorPerfil($iIdPerfil){
        try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
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

            return $db->getDBArrayQuery($sSQL);
	  	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
	
	public function validarUrlTmp($token){
		 try{
            $db = $this->conn;
            $sSQL = "SELECT 
            		  ui.`usuarios_id`,
					  ui.`invitados_id`,
					  ui.`relacion`,
					  ui.`fecha`,
					  ui.`estado`,
					  ui.`token`,
					  p.`email`,
					  p.`nombre`,
					  p.`apellido`
					FROM 
					  `usuario_x_invitado` ui
					JOIN
						usuarios u ON u.id = ui.usuarios_id
					JOIN
						personas p ON p.id = ui.invitados_id
					WHERE DATE_SUB(ui.fecha,INTERVAL 5 DAY) <= now() 
						 AND ui.token = ".$db->escape($token,true)." AND ui.estado = 'pendiente' ";
            return $db->getDBObject($sSQL);
	 	}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}
	
	public function guardarNuevaContrasenia($iId){
		try{
			$db = $this->conn;
			$time 	= time();
			$token 	= md5($time);
			$pass	= substr( md5(microtime()), 1, 8);
			$oPass = new stdClass();
			$oPass->nuevaContrasenia = $pass;
			$oPass->token = $token;
			$sSQL = " insert into usuarios_datos_temp set ".
                    " id=".$db->escape($iId,false,MYSQL_TYPE_INT).", " .
                    " contraseniaNueva=".$db->escape(md5($pass),true).", ".
                    " token=".$db->escape($token,true)." ";

			 $db->execSQL($sSQL);
			 $db->commit();
			 return $oPass;
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}
	
	public function validarConfirmacionContrasenia($token){
		 try{
            $db = $this->conn;
            $sSQL = "SELECT 
            			udt.id as iId, 
            			udt.contraseniaNueva as sContraseniaNueva
					FROM 
					  `usuarios_datos_temp` udt
					JOIN
						usuarios u ON udt.id = u.id
					JOIN
						personas p ON p.id = udt.id
					WHERE DATE_SUB(udt.fecha,INTERVAL 5 DAY) <= now() 
						 AND udt.token = ".$db->escape($token,true)." ";
            $objUsuario = $db->getDBObject($sSQL);
            if($objUsuario){
            	$filtro   = array('u.id' => $objUsuario->iId);
				$oPerfil  = $this->obtener($filtro);
				$oUsuario = $oPerfil->getUsuario();
				$oUsuario->setContrasenia( $objUsuario->sContraseniaNueva );
				return $this->guardar($oUsuario);
            }else{
            	return false;
            }
	 	}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}
}