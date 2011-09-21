<?php

/**
 * Controlador principal de la 'logica de negocio'. 
 *
 */
class ComunidadController
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
     * Retorna excepcion si no encuentra la publicacion
     *
     */
    public function obtenerPublicacion($publicacionId)
    {

    }

    public function obtenerUltimaPublicacion()
    {

    }

    public function enviarInvitacion($oUsuario, $oInvitado, $sDescripcion){
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->enviarInvitacion($oUsuario,Factory::getInvitadoInstance($oInvitado), $sDescripcion);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function listaPaises(){
    	try{
            $oPaisIntermediary = PersistenceFactory::getPaisIntermediary($this->db);
            return $oPaisIntermediary->obtener(array());
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function listaProvinciasByPais($iPaisId){
    	try{
            $filtro = array("p.paises_id"=>$iPaisId);
            $oProvinciaIntermediary = PersistenceFactory::getProvinciaIntermediary($this->db);
            return $oProvinciaIntermediary ->obtener($filtro);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function listaCiudadByProvincia($iProvinciaId){
    	try{
            $filtro = array("c.provincia_id"=>$iProvinciaId);
            $oCiudadIntermediary = PersistenceFactory::getCiudadIntermediary($this->db);
            return $oCiudadIntermediary->obtener($filtro);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     *
     */
    public function getCiudadById($iId){
        try{
            $filtro = array('c.id' => $iId);
            $oCiudadIntermediary = PersistenceFactory::getCiudadIntermediary($this->db);
            return $oCiudadIntermediary ->obtener($filtro);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    ///tipea andres
    public function guardarInstitucion($oInstitucion){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->guardar($oInstitucion);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
	public function borrarInstitucion($oInstitucion){
    	try{
			$oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->borrar($oInstitucion);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
    
    //ver lo del filtro Andres
	public function obtenerInstitucion($filtro){
    	try{
			$oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->obtener($filtro);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
	public function obtenerMisInstituciones($filtro){
    	try{
			$oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->obtenerMisInstituciones($filtro);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }

    public function existeInstitucion($filtro){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->existe($filtro);
        }catch(Exception $e){
           echo $e->getMessage();
        }
    }

    public function listaTiposDeInstitucion($filtro, &$iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount){
    try{
        $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
        return $oInstitucionIntermediary->listaTiposDeInstitucion($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
    
    
    public function guardarDiscapacitado($oDiscapacitado){
    	try{
			$oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            return $oDiscapacitadoIntermediary->guardar($oDiscapacitado);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
	public function borrarDiscapacitado($oDiscapacitado){
    	try{
			$oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            return $oDiscapacitadoIntermediary->borrar($oDiscapacitado);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
	public function obtenerDiscapacitado($filtro){
    	try{
			$oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            return $oDiscapacitadoIntermediary->obtener($filtro);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
	public function existeDiscapacitado($filtro){
    	try{
			$oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            return $oDiscapacitadoIntermediary->existe($filtro);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
}