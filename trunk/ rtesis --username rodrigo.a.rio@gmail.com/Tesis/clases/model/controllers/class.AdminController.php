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
    
    public function obtenerEspecialidad($filtro = null, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oEspecialidadIntermediary = PersistenceFactory::getEspecialidadIntermediary($this->db);
            return $oEspecialidadIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
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

    public function obtenerModeracionesDiscapacitados($filtro,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            return $oDiscapacitadoIntermediary->obtenerModeracion($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function aprobarModeracionDiscapacitado($iDiscapacitadoId, $pathServidor)
    {
        try{
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            $filtro = array('dm.id' => $iDiscapacitadoId);
            $result = false;
            if($oDiscapacitadoIntermediary->existeModeracion($filtro)){
                $oDiscapacitado = SeguimientosController::getInstance()->getDiscapacitadoById($iDiscapacitadoId);
                list($result, $cambioFoto) = $oDiscapacitadoIntermediary->aplicarCambiosModeracion($oDiscapacitado);
                if($result && $cambioFoto && null !== $oDiscapacitado->getFotoPerfil()){
                    //si hay foto nueva borro los archivos del sistema. Tengo q pasar el objeto para reutilizar el metodo de comunidad controller
                    ComunidadController::getInstance()->borrarFotoPerfil($oDiscapacitado, $pathServidor);                    
                }
                $oDiscapacitado = null;
            }
            return $result;
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    public function eliminarInstitucion($oInstitucion){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->borrar($oInstitucion);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}