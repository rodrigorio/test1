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
                    //si hay foto nueva borro los archivos del sistema.
                    $aNombreArchivos = $oDiscapacitado->getFotoPerfil()->getArrayNombres();
                    
                    foreach($aNombreArchivos as $nombreServidorArchivo){
                        $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
                        if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                            unlink($pathServidorArchivo);
                        }
                    }
                }
                $oDiscapacitado = null;
            }
            return $result;
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    public function rechazarModeracionDiscapacitado($iDiscapacitadoId, $pathServidor)
    {
        try{
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);            
            $result = false;
            $filtro = array('dm.id' => $iDiscapacitadoId);            
            if($oDiscapacitadoIntermediary->existeModeracion($filtro)){
                $aDiscapacitadoMod = $oDiscapacitadoIntermediary->obtenerModeracion($filtro, $iRecordsTotal);
                $oDiscapacitadoMod = $aDiscapacitadoMod[0];
                
                list($result, $cambioFoto) = $oDiscapacitadoIntermediary->rechazarCambiosModeracion($iDiscapacitadoId);
                //si cambio foto hay que borrar los archivos del servidor.
                if($result && $cambioFoto){
                    $oFotoMod = $oDiscapacitadoMod->getFotoPerfil();
                    $aNombreArchivos = $oFotoMod->getArrayNombres();

                    foreach($aNombreArchivos as $nombreServidorArchivo){
                        $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
                        if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                            unlink($pathServidorArchivo);
                        }
                    }
                }
            }
            return $result;
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }        
    }

    public function eliminarInstitucion($iInstitucionId){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->borrar($iInstitucionId);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    public function obtenerAccionesSistema($filtro = null, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
            return $oPermisosIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    public function getAccionById($iAccionId)
    {
        try{
            $filtro = array('a.id' => $iAccionId);
            $oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
            $aAcciones = $oPermisosIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aAcciones){
                return $aAcciones[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }        
    }
    public function guardarAccion($oAccion)
    {
        try{
            $oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
            return $oPermisosIntermediary->guardar($oAccion);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }        
    }
    public function borrarAccion($oAccion)
    {
        try{
            $oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
            return $oPermisosIntermediary->borrar($oAccion);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    public function existeAccion($oAccion)
    {
        try{
            $filtro = array("cp.controlador" => $oAccion->getModulo()."_".$oAccion->getControlador(), "a.accion" => $oAccion->getNombre());
            $oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
            return $oPermisosIntermediary->existe($filtro);
        }catch(Exception $e){
           throw new Exception($e->getMessage());
        }        
    }

    public function obtenerUsuariosSistema($filtro = null, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);            
            return $oUsuarioIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }        
    }
}