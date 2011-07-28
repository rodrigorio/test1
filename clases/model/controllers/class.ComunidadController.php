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
    		$filtro = array("p.id"=>$iPaisId);
			$oProvinciaIntermediary = PersistenceFactory::getProvinciaIntermediary($this->db);
            return $oProvinciaIntermediary ->obtener($filtro);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
    public function listaCiudadByProvincia($iProvinciaId){
    	try{
    		$filtro = array("c.id"=>$iProvinciaId);
			$oCiudadIntermediary = PersistenceFactory::getCiudadIntermediary($this->db);
            return $oCiudadIntermediary->obtener($filtro);
		}catch(Exception $e){
			echo $e->getMessage();
		}
    }
}