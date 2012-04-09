<?php

/**
 * Controlador principal de la 'logica de negocio'. 
 *
 */
class IndexController
{
    /**
     * @var Instancia de DB
     */
    private $db = null;

    /**
     * @var Instancia de clase que maneja session de usuario
     */
    private $auth = null;
    
    private static $instance = null;

    private function __construct(){ }	
	
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param Auth $session
     */
    public function setAuth(Auth $auth){
        $this->auth = $auth;
    }

    /**
     * @param DB $db
     */
    public function setDBDriver(DB $db){
        $this->db = $db;
    }
	
    /**
     * @param stdClass $obj
     */
    public function registrar($obj,$iUsuarioId){
    	try{
			$oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->registrar(Factory::getUsuarioInstance($obj),$iUsuarioId);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
    /**
     * @param string $token
     */
    public function validarUrlTmp($token){
    	try{
			$oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->validarUrlTmp($token);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
    
    /**
     * @param string $token
     */
    public function recuperarContrasenia($sNombreUsuario,$sEmail){
    	try{
            $request = FrontController::getInstance()->getRequest();
            $filtro = array('u.nombre' => $sNombreUsuario, 'p.email' =>  $sEmail);
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $iRecordsTotal = 0;
            $aUsuario = $oUsuarioIntermediary->obtener($filtro,$iRecordsTotal,null,null,null,null);
            if($aUsuario !== null){
                $oUsuario = $aUsuario[0];
            	$oNuevoPass = $oUsuarioIntermediary->guardarNuevaContrasenia($oUsuario->getId());
            	if($oNuevoPass){
	            	$asunto = "Recuperaci�n de contrase�a";
					$dest 	= $oUsuario()->getEmail();
					$orig	= "servicios@sistemadegestion.com";
					$sToken	= $oNuevoPass->token;
					$sNuevaContrasenia	= $oNuevoPass->nuevaContrasenia;
					$body 	="<html>
								<head>
								  <title>Usted ha sido invitado para registrarse en .....</title>
								</head>
								<body>
								  <p>Si usted no solicit� cambiar su contrase�a omita este mail, en caso contrario 
								  		haga click en el siguiente enlace para confirmar su nueva contrase�a!</p>
								  <p><a href='".$request->getBaseTagUrl()."confirmarContrasenia?token=$sToken'> Confirmar </a></p>		
								  <div><p>Nueva contrase�a : ".$sNuevaContrasenia."</div>
								</body>
							</html>";
	            	$envio = $oUsuarioIntermediary->sendMail($orig, $dest, $asunto, $body);
	            	if($envio){
	            		return true;
	            	}else{
	            		return -1;
	            	}
            	}else{
            		return null;
            	} 
            }else{
            	return null;
            }
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
    /**
     * @param string $token
     */
    public function confirmarContrasenia($sToken){
    	try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $oUsuario = $oUsuarioIntermediary->validarConfirmacionContrasenia($sToken);
            if($oUsuario){
            	return true;
            }else{
            	return null;
            }
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }

    /**
     * devuelve array con id de registro y descripcion de documento
     * para generar el select de tipo documento
     */
    public function obtenerTiposDocumentos()
    {
        $oDocumentoTiposIntermediary = PersistenceFactory::getDocumentoTiposIntermediary($this->db);
        return $oDocumentoTiposIntermediary->obtenerTiposDocumentos();
    }    
}