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
    public function registrar($obj){
    	try{
			$oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
			$obj2 = new stdClass();
			$obj2->sRelacion 	= "Colega";
			$obj2->sNombre 		= $obj->sNombre;
			$obj2->sApellido 	= $obj->sNombre;
			$obj2->sEmail 		= $obj->sEmail.rand();
            $oUsuarioIntermediary->enviarInvitacion(61,Factory::getInvitadoInstance($obj2));
            return $oUsuarioIntermediary->guardar(Factory::getUsuarioInstance($obj));
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
    /**
     * @param stdClass $obj
     */
    public function validarUrlTmp($user,$inv,$email,$token){
    	try{
			$oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->validarUrlTmp($user,$inv,$email,$token);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
    
}