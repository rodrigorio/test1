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
    
}