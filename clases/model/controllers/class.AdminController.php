<?php

/**
 * 
 *
 * @author Matias Velilla
 */
class AdminController
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
	/**********************************
	 *********ESPECIALIDADES*********** 	
	 *********************************/
    public function obtenerEspecialidad($filtro,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oEspecialidadIntermediary = PersistenceFactory::getEspecialidadIntermediary($this->db);
            return $oEspecialidadIntermediary->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
    public function buscar($filtro,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oEspecialidadIntermediary = PersistenceFactory::getEspecialidadIntermediary($this->db);
            return $oEspecialidadIntermediary->search($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
    public function guardarEspecialidad($oEspecialidad){
        try{
            $oEspecialidadIntermediary = PersistenceFactory::getEspecialidadIntermediary($this->db);
            return $oEspecialidadIntermediary->guardar($oEspecialidad);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
     public function eliminarEspecialidad($oEspecialidad){
        try{
            $oEspecialidadIntermediary = PersistenceFactory::getEspecialidadIntermediary($this->db);
            return $oEspecialidadIntermediary->borrar($oEspecialidad);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
     public function especialidadUsadaPorUsuario($oEspecialidad){
        try{
            $oEspecialidadIntermediary = PersistenceFactory::getEspecialidadIntermediary($this->db);
            return $oEspecialidadIntermediary->especialidadUsadaPorUsuario($oEspecialidad);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
    /**********************************
	 **********CATEGORIAS************** 	
	 *********************************/
 public function obtenerCategoria($filtro,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oCategoriaIntermediary = PersistenceFactory::getCategoriaIntermediary($this->db);
            return $oCategoriaIntermediary->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
    public function guardarCategoria($oCategoria){
        try{
            $oCategoriaIntermediary = PersistenceFactory::getCategoriaIntermediary($this->db);
            return $oCategoriaIntermediary->guardar($oCategoria);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
     public function eliminarCategoria($oCategoria){
        try{
            $oCategoriaIntermediary = PersistenceFactory::getCategoriaIntermediary($this->db);
            return $oCategoriaIntermediary->borrar($oCategoria);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
     public function categoriaUsadaPorUsuario($oCategoria){
        try{
            $oCategoriaIntermediary = PersistenceFactory::getCategoriaIntermediary($this->db);
            return $oCategoriaIntermediary->especialidadUsadaPorUsuario($oCategoria);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}
?>