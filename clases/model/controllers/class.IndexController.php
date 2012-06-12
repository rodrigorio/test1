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

    ///*** METODOS BASICOS DE ADJUNTOS ***///
    
    public function borrarFoto($oFoto, $pathServidor)
    {
    	try{
            $aNombreArchivos = $oFoto->getArrayNombres();

            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            $oFotoIntermediary->borrar($oFoto);

            foreach($aNombreArchivos as $nombreServidorArchivo){
                $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
                if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                    unlink($pathServidorArchivo);
                }
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function borrarEmbedVideo($oEmbedVideo)
    {
    	try{
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            return $oEmbedVideoIntermediary->borrar($oEmbedVideo);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function borrarArchivo($oArchivo, $pathServidor)
    {
    	try{
            $pathServidorArchivo = $pathServidor.$oArchivo->getNombreServidor();

            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            $oArchivoIntermediary->borrar($oArchivo);

            if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                unlink($pathServidorArchivo);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Devuelve una foto suelta sin asociarse a ningun objeto.
     * Esto se necesita para el formulario en el que se modifica orden, titulo, etc.
     * Tambien para obtener el objeto cuando se tiene que borrar.
     */
    public function getFotoById($iFotoId)
    {
        try{
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            $filtro = array('f.id' => $iFotoId);
            $iRecordsTotal = 0;
            $aFotos = $oFotoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aFotos){
                return $aFotos[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    public function getEmbedVideoById($iEmbedVideoId)
    {
        try{
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            $filtro = array('v.id' => $iEmbedVideoId);
            $iRecordsTotal = 0;
            $aEmbedVideos = $oEmbedVideoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aEmbedVideos){
                return $aEmbedVideos[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    public function getArchivoById($iArchivoId)
    {
        try{
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            $filtro = array('a.id' => $iArchivoId);
            $iRecordsTotal = 0;
            $aArchivos = $oArchivoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aArchivos){
                return $aArchivos[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    /**
     * Este metodo se debe usar solo para guardar la informacion del formulario de edicion de foto.
     * Titulo, descripcion, etc.
     *
     * No sirve para asociar la foto a ninguna entidad
     */
    public function guardarFoto($oFoto)
    {
    	try{
            if(null === $oFoto->getId()){
                throw new Exception("La foto no posee Id");
            }
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            return $oFotoIntermediary->actualizar($oFoto);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Este metodo se debe usar solo para guardar la informacion del formulario de edicion de archivo.
     * Titulo, descripcion, orden, etc.
     *
     * No sirve para asociar el archivo a ninguna entidad
     */
    public function guardarArchivo($oArchivo)
    {
    	try{
            if(null === $oArchivo->getId()){
                throw new Exception("El archivo no posee Id");
            }
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            return $oArchivoIntermediary->actualizar($oArchivo);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Este metodo se debe usar solo para guardar la informacion del formulario de edicion de foto.
     * Titulo, descripcion, etc.
     *
     * No sirve para asociar la foto a ninguna entidad
     */
    public function guardarEmbedVideo($oEmbedVideo)
    {
    	try{
            if(null === $oEmbedVideo->getId()){
                throw new Exception("El video no posee Id");
            }
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            return $oEmbedVideoIntermediary->actualizar($oEmbedVideo);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
}