<?php

/**
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
            throw $e;
        }
    }
    
    public function obtenerEspecialidadById($iEspecialidadId){
        try{
            $filtro = array('e.id' => $iEspecialidadId);
            $oEspecialidadIntermediary = PersistenceFactory::getEspecialidadIntermediary($this->db);
            $iRecordsTotal = 0;
            $aEspecialidades = $oEspecialidadIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aEspecialidades){
                return $aEspecialidades[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }          
    }
        
    public function guardarEspecialidad($oEspecialidad){
        try{
            $oEspecialidadIntermediary = PersistenceFactory::getEspecialidadIntermediary($this->db);
            return $oEspecialidadIntermediary->guardar($oEspecialidad);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function borrarEspecialidad($iEspecialidadId){
        try{
            $oEspecialidadIntermediary = PersistenceFactory::getEspecialidadIntermediary($this->db);
            return $oEspecialidadIntermediary->borrar($iEspecialidadId);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * El objeto especialidad si o si tiene que traer el nombre
     */
    public function verificarExisteEspecialidad($oEspecialidad){
        try{
            $oEspecialidadIntermediary = PersistenceFactory::getEspecialidadIntermediary($this->db);

            $filtro = array('e.nombre' => $oEspecialidad->getNombre());
            if(null !== $oEspecialidad->getId()){
                $filtro['no_e.id'] = $oEspecialidad->getId();
            }
                
            return $oEspecialidadIntermediary->existe($filtro);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function guardarCategoria($oCategoria)
    {
        try{
            $oCategoriaIntermediary = PersistenceFactory::getCategoriaIntermediary($this->db);
            return $oCategoriaIntermediary->guardar($oCategoria);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function eliminarCategoria($iCategoriaId){
        try{
            $oCategoriaIntermediary = PersistenceFactory::getCategoriaIntermediary($this->db);
            return $oCategoriaIntermediary->borrar($iCategoriaId);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function verificarExisteCategoria($oCategoria)
    {
        try{
            $oCategoriaIntermediary = PersistenceFactory::getCategoriaIntermediary($this->db);

            $filtro = array('c.nombre' => $oCategoria->getNombre());
            if(null !== $oCategoria->getId()){
                $filtro['no_c.id'] = $oCategoria->getId();
            }

            return $oCategoriaIntermediary->existe($filtro);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function guardarNivel($oNivel)
    {
        try{
            $oNivelIntermediary = PersistenceFactory::getNivelIntermediary($this->db);
            return $oNivelIntermediary->guardar($oNivel);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function guardarFotoCategoria($aNombreArchivos, $pathServidor, $oCategoria)
    {
    	try{
            //creo el objeto Foto y lo guardo.
            $oFoto = new stdClass();
            $oFoto->sNombreBigSize = $aNombreArchivos['nombreFotoGrande'];
            $oFoto->sNombreMediumSize = $aNombreArchivos['nombreFotoMediana'];
            $oFoto->sNombreSmallSize = $aNombreArchivos['nombreFotoChica'];

            $oFoto = Factory::getFotoInstance($oFoto);

            $oFoto->setOrden(0);
            $oFoto->setTitulo('Foto Categoria');
            $oFoto->setDescripcion('');
            $oFoto->setTipoPerfil();

            if(null !== $oCategoria->getFoto())
            {
                $this->borrarFotoCategoria($oCategoria, $pathServidor);
            }

            //asociarlo al objeto
            $oCategoria->setFoto($oFoto);

            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            return $oFotoIntermediary->guardarFotoCategoria($oCategoria);

        }catch(Exception $e){
            //si hubo error borro los archivos en disco
            foreach($aNombreArchivos as $nombreServidorArchivo){
                $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
                if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                    unlink($pathServidorArchivo);
                }
            }
            $oCategoria->setFoto(null);

            throw $e;
        }
    }

    public function borrarFotoCategoria($oCategoria, $pathServidor)
    {
    	try{
            if(null === $oCategoria->getFoto()){
                throw new Exception("La categoria no posee una foto");
            }

            IndexController::getInstance()->borrarFoto($oCategoria->getFoto(), $pathServidor);

            $oCategoria->setFoto(null);

        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerModeracionesDiscapacitados($filtro,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            return $oDiscapacitadoIntermediary->obtenerModeracion($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw $e;
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
            throw $e;
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
            throw $e;
        }        
    }

    public function obtenerAccionesSistema($filtro = null, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
            return $oPermisosIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getAccionById($iAccionId)
    {
        try{
            $filtro = array('a.id' => $iAccionId);
            $oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
            $iRecordsTotal = 0;
            $aAcciones = $oPermisosIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aAcciones){
                return $aAcciones[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }        
    }

    public function guardarAccion($oAccion)
    {
        try{
            $oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
            return $oPermisosIntermediary->guardar($oAccion);
        }catch(Exception $e){
            throw $e;
        }        
    }

    public function borrarAccion($oAccion)
    {
        try{
            $oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
            return $oPermisosIntermediary->borrar($oAccion);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function existeAccion($oAccion)
    {
        try{
            $filtro = array("cp.controlador" => $oAccion->getModulo()."_".$oAccion->getControlador(), "a.accion" => $oAccion->getNombre());
            $oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
            return $oPermisosIntermediary->existe($filtro);
        }catch(Exception $e){
           throw $e;
        }        
    }

    public function obtenerUsuariosSistema($filtro = null, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);            
            return $oUsuarioIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }        
    }

    /**
     * Ojo con este metodo capaz que hay que ir actualizandolo a medida que crezca el sistema.
     */
    public function cerrarCuentaIntegrante($oUsuario, $pathServidor)
    {
        try{            
            $oFotoPerfil = $oUsuario->getFotoPerfil();
            $oCurriculumVitae = $oUsuario->getCurriculumVitae();

            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $result = $oUsuarioIntermediary->borrar($oUsuario);
            
            if($result){
                //borro archivos de fotos y adjuntos en el servidor, los registros en db volaron en cascada
                if(null != $oFotoPerfil){
                    $aNombreArchivos = $oFotoPerfil->getArrayNombres();
                    foreach($aNombreArchivos as $nombreServidorArchivo){
                        $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
                        if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                            unlink($pathServidorArchivo);
                        }
                    }
                }
                if(null != $oCurriculumVitae){                    
                    $pathServidorArchivo = $pathServidor.$oCurriculumVitae->getNombreServidor();
                    if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                        unlink($pathServidorArchivo);
                    }
                }
            }

            return $result;
        }catch(Exception $e){
            throw $e;
        }    
    }
    
    /**
     * Devuelve el campo 'descripcion' del perfil para un usuario
     */
    public function obtenerDescripcionPerfilUsuario($oUsuario)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $oPerfil = $oUsuarioIntermediary->obtenerPerfil($oUsuario);
            return $oPerfil->getDescripcion();
        }catch(Exception $e){
            throw $e;
        }                
    }

    /**
     * Para cambiar automaticamente el perfil si estan los datos minimos en un usuario despues de que se modifica
     */
    public function setIntegranteActivoUsuario($oUsuario)
    {
        try{
            $sPerfil = $this->obtenerDescripcionPerfilUsuario($oUsuario);
            if($sPerfil == "integrante inactivo"){
                $oPerfil = new stdClass();
                $oPerfil->oUsuario = $oUsuario;
                $oIntegranteActivo = Factory::getIntegranteActivoInstance($oPerfil);
                
                $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
                $oUsuarioIntermediary->guardarPerfil($oIntegranteActivo, false);
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve un array que contiene en sus claves el nombre del perfil y en la variable el id
     * (corresponde a los valores de la tabla perfiles de la DB)
     */
    public function obtenerArrayPerfiles(){
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->obtenerPerfiles();
        }catch(Exception $e){
            throw $e;
        }            
    }

    /**
     * Recibe un usuario y un id de perfil (que coincide con los registros de la tabla perfil)
     */
    public function cambiarPerfilUsuario($oUsuario, $iPerfilId)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);

            $oPerfil = new stdClass();
            $oPerfil->oUsuario = $oUsuario;

            $aPerfiles = $this->obtenerArrayPerfiles();
            $aPerfilDesc = array_keys($aPerfiles, $iPerfilId); //ya se que devuelve 1 solo elemento
            $sPerfilDesc = $aPerfilDesc[0];

            switch($sPerfilDesc){
                case 'administrador':
                    $oAdministrador = Factory::getAdministradorInstance($oPerfil);
                    $oUsuarioIntermediary->guardarPerfil($oAdministrador, false);
                    break;
                case 'moderador':
                    $oModerador = Factory::getModeradorInstance($oPerfil);
                    $oUsuarioIntermediary->guardarPerfil($oModerador, false);
                    break;
                case 'integrante activo':
                    $oIntegranteActivo = Factory::getIntegranteActivoInstance($oPerfil);
                    $oUsuarioIntermediary->guardarPerfil($oIntegranteActivo, false);
                    break;
                case 'integrante inactivo':
                    $oIntegranteInactivo = Factory::getIntegranteInactivoInstance($oPerfil);
                    $oUsuarioIntermediary->guardarPerfil($oIntegranteInactivo, false);
                    break;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function buscarUsuariosSistema($filtro = null, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Se diferencia del metodo que lleva el mismo nombre en el controlador de comunidad
     */
    public function buscarPublicacionesComunidad($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function buscarPublicacionesModeracion($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $filtro["publico"] = "1";
            $filtro["m.sModeracionEstado"] = "pendiente";

            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * varia del de comunidad porque aca aparece todo si o si.
     */
    public function buscarSoftwareComunidad($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            return $oSoftwareIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function buscarSoftwareModeracion($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $filtro["publico"] = "1";
            $filtro["m.sModeracionEstado"] = "pendiente";

            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            return $oSoftwareIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function buscarInstitucionesModeracion($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $filtro["m.sModeracionEstado"] = "pendiente";

            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function obtenerDenunciasInstitucion($iInstitucionId)
    {
        try{
            $filtro["d.instituciones_id"] = $iInstitucionId;
            $oDenunciaIntermediary = PersistenceFactory::getDenunciaIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oDenunciaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function obtenerDenunciasFicha($iFichaId)
    {
        try{
            $filtro["d.fichas_abstractas_id"] = $iFichaId;
            $oDenunciaIntermediary = PersistenceFactory::getDenunciaIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oDenunciaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function buscarInstitucionesDenuncias($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            //genero el filtro con al menos 1 denuncia.
            $filtro["minDenuncias"] = "1";

            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function buscarSoftwareDenuncias($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            //genero el filtro con al menos 1 denuncia.
            $filtro["minDenuncias"] = "1";

            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            return $oSoftwareIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function buscarPublicacionesDenuncias($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            //genero el filtro con al menos 1 denuncia.
            $filtro["minDenuncias"] = "1";

            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    } 

    /**
     *
     * Este metodo es polimorfico, limpia las denuncias de cualquier entidad que puede ser denunciada
     * dentro del sistema.
     * 
     * @param stdClass $oObj Un objeto que soporte la interfaz de entidad denunciada
     *
     */
    public function limpiarDenuncias($oObj)
    {
        try{
            $oDenunciaIntermediary = PersistenceFactory::getDenunciaIntermediary($this->db);
            return $oDenunciaIntermediary->borrarDenunciasEntidad($oObj);
        }catch (Exception $e){
            throw $e;
        }
        
    }
      
    public function buscarInstitucionesSolicitud($filtro = array(), &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oInstitucionIntermediary->obtenerInstitucionesSolicitud($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function obtenerHistorialSolicitudesInstitucion($iInstitucionId)
    {
        try{
            $filtro["iss.instituciones_id"] = $iInstitucionId;
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oInstitucionIntermediary->obtenerSolicitudes($filtro, $iRecordsTotal, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch (Exception $e){
            throw $e;
        }        
    }
    
    public function getSolicitudInstitucionById($iSolicitudId)
    {
        try{
            $filtro["iss.id"] = $iSolicitudId;
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aSolicitudes = $oInstitucionIntermediary->obtenerSolicitudes($filtro, $iRecordsTotal, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aSolicitudes){
                return $aSolicitudes[0];
            }else{
                return null;
            }
        }catch (Exception $e){
            throw $e;
        }        
    }

    public function obtenerHistorialModeracionesFicha($iFichaId)
    {
        try{
            $filtro["m.fichas_abstractas_id"] = $iFichaId;
            $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oModeracionIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch (Exception $e){
            throw $e;
        }        
    }

    public function obtenerHistorialModeracionesInstitucion($iInstitucionId)
    {
        try{
            $filtro["m.instituciones_id"] = $iInstitucionId;
            $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oModeracionIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function getModeracionById($iModeracionId)
    {
        try{
            $filtro = array('m.id' => $iModeracionId);
            $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aModeracion = $oModeracionIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aModeracion){
                return $aModeracion[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }        
    }

    public function guardarModeracion($oModeracion)
    {
        try{
            $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
            return $oModeracionIntermediary->guardar($oModeracion);
        }catch(Exception $e){
            throw $e;
        }        
    }

    /**
     * Este metodo devuelve un array con objetos del tipo Parametro ParametroSistema y ParametroControlador
     * Todos implementan la interfaz de la clase Parametro.
     */
    public function obtenerParametrosDinamicos($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $filtro['sistema_controlador_parametro'] = "sistema_controlador_parametro";
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
        }  
    }

    /**
     * Este metodo devuelve un array con objetos del tipo ParametroUsuario que implementa la interfaz Parametro
     */
    public function obtenerParametrosDinamicosUsuario($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $filtro['usuario'] = "usuario";
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Este metodo devuelve un array con objetos ParametroUsuario que estan asociados
     * a todos los usuarios del sistema.
     * En su valor tienen el valor por defecto con el que se asocian cuando un nuevo usuario es creado.
     *
     * no tienen el id de ningun usuario en particular porque son los parametros para todos los usuarios.
     * (devuelve los de la tabla parametros_usuario)
     */
    public function obtenerParametrosAsociadosUsuarios($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->obtenerParametrosUsuarios($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Este metodo devuelve unicamente objetos de la clase Parametro.
     * No devuelve ningun parametro de las clases 'asociativas'
     */
    public function obtenerParametros($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function obtenerControladoresPagina($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oControladorPaginaIntermediary = PersistenceFactory::getControladorPaginaIntermediary($this->db);
            return $oControladorPaginaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function getControladorPaginaById($iControladorId)
    {
        try{
            $filtro = array('cp.id' => $iControladorId);
            $oControladorPaginaIntermediary = PersistenceFactory::getControladorPaginaIntermediary($this->db);
            $iRecordsTotal = 0;
            $aControladorPagina = $oControladorPaginaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aControladorPagina){
                return $aControladorPagina[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }        
    }

    public function getControladorPaginaByNombre($sControlador)
    {
        try{
            $filtro = array('cp.controlador' => $sControlador);
            $oControladorPaginaIntermediary = PersistenceFactory::getControladorPaginaIntermediary($this->db);
            $iRecordsTotal = 0;
            $aControladorPagina = $oControladorPaginaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aControladorPagina){
                return $aControladorPagina[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Devuelve unicamente una instancia de clase Parametro
     */
    public function getParametroById($iParametroId)
    {
        try{
            $filtro = array('p.id' => $iParametroId);
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            $iRecordsTotal = 0;
            $aParametros = $oParametrosIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aParametros){
                return $aParametros[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }        
    }

    /**
     *  Devuelve un objeto de la clase ParametroSistema
     */
    public function getParametroSistema($iParametroId)
    {
        try{
            $filtro = array('iId' => $iParametroId, 'sistema' => 'sistema');
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            $iRecordsTotal = 0;
            $aParametros = $oParametrosIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aParametros){
                return $aParametros[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }        
    }

    /**
     *  Devuelve un objeto de la clase ParametroControlador
     */
    public function getParametroControlador($iParametroId, $iControladorId)
    {
        try{
            $filtro = array('iId' => $iParametroId, 'iControladorId' => $iControladorId);
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            $iRecordsTotal = 0;
            $aParametros = $oParametrosIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aParametros){
                return $aParametros[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Devuelve un objeto de la clase ParametroControlador a partir del
     *  nombre del parametro y el nombre del controlador
     *
     *  ejemplo: getParametroControladorByNombre('NOMBRE_PARAMETRO', 'comunidad_publicaciones');
     */
    public function getParametroControladorByNombre($sNamespace, $sControlador)
    {
        try{
            $filtro = array('sNamespace' => $sNamespace, 'sControlador' => $sControlador);
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            $iRecordsTotal = 0;
            $aParametros = $oParametrosIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aParametros){
                return $aParametros[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Devuelve un objeto de la clase ParametroUsuario
     */
    public function getParametroUsuario($iParametroId, $iUsuarioId)
    {
        try{
            $filtro = array('iId' => $iParametroId, 'iUsuarioId' => $iUsuarioId);
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            $iRecordsTotal = 0;
            $aParametros = $oParametrosIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aParametros){
                return $aParametros[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Devuelve un objeto de la clase ParametroUsuario a partir del
     *  nombre del parametro y el id del usuario
     *
     *  ejemplo: getParametroUsuarioByNombre('NOMBRE_PARAMETRO', 63);
     */
    public function getParametroUsuarioByNombre($sNamespace, $iUsuarioId)
    {
        try{
            $filtro = array('sNamespace' => $sNamespace, 'iUsuarioId' => $iUsuarioId);
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            $iRecordsTotal = 0;
            $aParametros = $oParametrosIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aParametros){
                return $aParametros[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function existeParametro($oParametro)
    {
        try{
            $filtro = array("p.namespace" => $oParametro->getNamespace());
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->existe($filtro);
        }catch(Exception $e){
           throw $e;
        }                
    }

    /**
     * Este metodo indica si un parametro se encuentra asociado al sistema o no.
     */
    public function existeParametroSistema($iParametroId)
    {
        try{
            $filtro = array("ps.parametros_id" => $iParametroId);
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->existeParametroSistema($filtro);
        }catch(Exception $e){
           throw $e;
        }                
    }
    
    /**
     * Este metodo indica si un parametro se encuentra asociado a un controlador o no.
     */
    public function existeParametroControlador($iParametroId, $iControladorId)
    {
        try{
            $filtro = array("pc.parametros_id" => $iParametroId,
                            "pc.controladores_pagina_id" => $iControladorId);
            
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->existeParametroControlador($filtro);
        }catch(Exception $e){
           throw $e;
        }
    }
    
    /**
     * Indica si un parametro esta asociado a los usuarios del sistema
     */
    public function existeParametroUsuarios($iParametroId)
    {
        try{
            $filtro = array("pu.parametros_id" => $iParametroId);
            
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->existeParametroUsuarios($filtro);
        }catch(Exception $e){
           throw $e;
        }
    }

    public function guardarParametro($oParametro)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->guardar($oParametro);
        }catch(Exception $e){
            throw $e;
        } 
    }

    public function borrarParametro($oParametro)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->borrar($oParametro->getId());
        }catch(Exception $e){
            throw $e;
        } 
    }

    public function guardarParametroSistema($oParametroSistema)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->guardarParametroSistema($oParametroSistema);
        }catch(Exception $e){
            throw $e;
        }         
    }

    public function guardarParametroControlador($oParametroControlador)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->guardarParametroControlador($oParametroControlador);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * recibe un ParametroUsuario con un valor por defecto.
     * se asocia a todos los usuarios del sistema con ese valor.
     */
    public function asociaParametroUsuariosSistema($oParametroUsuario)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->asociaParametroUsuariosSistema($oParametroUsuario);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * guarda una instancia de ParametroUsuario asociado a un usuario en particular
     */
    public function guardarParametroUsuario($oParametroUsuario)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->guardarParametroUsuario($oParametroUsuario);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function eliminarParametroSistema($oParametroSistema)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->borrarParametroSistema($oParametroSistema);
        }catch(Exception $e){
            throw $e;
        } 
    }

    public function eliminarParametroControlador($oParametroControlador)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->borrarParametroControlador($oParametroControlador);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function eliminarParametroUsuario($oParametroUsuario)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->borrarParametroUsuario($oParametroUsuario);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Elimina la asociacion entre un parametro y todos los usuarios del sistema
     */
    public function eliminarAsociacionParametroUsuarios($iParametroId)
    {
        try{
            $oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
            return $oParametrosIntermediary->eliminarAsociacionParametroUsuarios($iParametroId);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Carga el Eje Tematico por el Admin
     *
     */
    public function guardarEjeTematico($oEjeTematico){
        try{
            $oEjeTematicoIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            return $oEjeTematicoIntermediary->guardar($oEjeTematico);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Elimina el eje tematico
     */
    public function eliminarEjeTematico($iEjeTematicoId)
    {
        try{
            $oEjeTematicoIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            return $oEjeTematicoIntermediary->Borrar($iEjeTematicoId);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Obtiene el eje tematico ById
     */
    public function getEjeTematicoById($iEjeTematicoId)
    {
    	try{
            $filtro = array('e.id' => $iEjeTematicoId);
            $oEjeTematicoIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            $iRecordsTotal = 0;
            $aEjeTematico = $oEjeTematicoIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aEjeTematico){
                return $aEjeTematico[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getEjes($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount )
    {
    	try{
            $oEjeTematicoIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            return $oEjeTematicoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     *  Obtiene el area ById
     */
    public function getAreaById($iAreaId)
    {
    	try{
            $filtro = array('a.id' => $iAreaId);
            $oAreaIntermediary = PersistenceFactory::getAreaIntermediary($this->db);
            $iRecordsTotal = 0;
            $aArea = $oAreaIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aArea){
                return $aArea[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     *  Obtiene las Areas
     */
    public function getAreas($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount )
    {
    	try{
            $oAreaIntermediary = PersistenceFactory::getAreaIntermediary($this->db);
            return $oAreaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     *  Obtiene los Niveles
     */
    public function getNiveles($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount )
    {
    	try{
            $oNivelIntermediary = PersistenceFactory::getNivelIntermediary($this->db);
            return $oNivelIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Obtener ciclos por id de nivel
     *
     */
    public function getCiclosByNivelId($iId)
    {
    	try{
            $filtro = array('n.id' => $iId);
            $oCicloIntermediary = PersistenceFactory::getCicloIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oCicloIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     *  Obtiene los ciclos
     */
    public function getCiclos($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount )
    {
    	try{
            $oCicloIntermediary = PersistenceFactory::getCicloIntermediary($this->db);
            return $oCicloIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Obtener areas por id de ciclo
     *
     */
    public function getAreasByCicloId($iId)
    {
    	try{
            $filtro = array('c.id' => $iId);
            $oAreaIntermediary = PersistenceFactory::getAreaIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oAreaIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Obtener areas por id de ciclo
     *
     */
    public function getEjesByAreaId($iAreaId)
    {
    	try{
            $filtro = array('a.id' => $iAreaId);
            $oEjeTematicoIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            $iRecordsTotal = 0;
            return $aEjeTematico = $oEjeTematicoIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Carga el Area por el Admin
     *
     */
    public function guardarArea($oArea){
        try{
            $oAreaIntermediary = PersistenceFactory::getAreaIntermediary($this->db);
            return $oAreaIntermediary->guardar($oArea);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Elimina el area
     */
    public function eliminarArea($oArea)
    {
        try{
            $oAreaIntermediary = PersistenceFactory::getAreaIntermediary($this->db);
            return $oAreaIntermediary->Borrar($oArea);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Obtiene el Ciclo ById
     */
    public function getCicloById($iCicloId)
    {
    	try{
            $filtro = array('c.id' => $iCicloId);
            $oCicloIntermediary = PersistenceFactory::getCicloIntermediary($this->db);
            $iRecordsTotal = 0;
            $aCiclo = $oCicloIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aCiclo){
                return $aCiclo[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

   /**
     * Carga el Ciclo por el Admin
     *
     */
    public function guardarCiclo($oCiclo){
        try{
            $oCicloIntermediary = PersistenceFactory::getCicloIntermediary($this->db);
            return $oCicloIntermediary->guardar($oCiclo);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Elimina el ciclo
     */
    public function eliminarCiclo($oCiclo)
    {
        try{
            $oCicloIntermediary = PersistenceFactory::getCicloIntermediary($this->db);
            return $oCicloIntermediary->Borrar($oCiclo);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Obtiene el nivel ById
     */
    public function getNivelById($iNivelId)
    {
    	try{
            $filtro = array('n.id' => $iNivelId);
            $oNivelIntermediary = PersistenceFactory::getNivelIntermediary($this->db);
            $iRecordsTotal = 0;
            $aNivel = $oNivelIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aNivel){
                return $aNivel[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
  
    /**
     *  Elimina el Nivel
     */
    public function eliminarNivel($oNivel)
    {
        try{
            $oNivelIntermediary = PersistenceFactory::getNivelIntermediary($this->db);
            return $oNivelIntermediary->borrar($oNivel);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Obtiene el objetivo aprendizaje ById
     */
    public function getObjetivoAprendizajeById($iObjetivoAprendizajeId)
    {
    	try{
            $filtro = array('o.id' => $iObjetivoAprendizajeId);
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iRecordsTotal = 0;
            $aObjetivoAprendizaje = $oObjetivoIntermediary->obtenerObjetivosAprendizaje($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aObjetivoAprendizaje){
                return $aObjetivoAprendizaje[0];
            }else{
                return null;
            }     
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getObjetivosAprendizaje($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount )
    {
    	try{
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            return $oObjetivoIntermediary->obtenerObjetivosAprendizaje($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Carga el objetivo aprendizaje por el Admin
     *
     */
    public function guardarObjetivoAprendizaje($oObjetivoAprendizaje){
        try{
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            return $oObjetivoIntermediary->guardarObjetivoAprendizaje($oObjetivoAprendizaje);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Elimina el eje tematico
     */
    public function eliminarObjetivoAprendizaje($iObjetivoAprendizajeId)
    {
        try{
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            return $oObjetivoIntermediary->Borrar($iObjetivoAprendizajeId);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Verifica si existe el nivel
     */
    public function existeNivelByDescripcion($sDescripcion)
    {
        try{
            $oNivelIntermediary = PersistenceFactory::getNivelIntermediary($this->db);
            $filtro = array('n.descripcion' => $sDescripcion);
            return $oNivelIntermediary->existe($filtro);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     *  Verifica si existe el ciclo
     */
    public function existeCicloByDescripcion($sDescripcion, $oNivel)
    {
        try{
            $oCicloIntermediary = PersistenceFactory::getCicloIntermediary($this->db);
            return $oCicloIntermediary->existeCicloByDescripcion($sDescripcion, $oNivel);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Verifica si existe el area
     */
    public function verificarExisteAreaByDescripcion($sDescripcion, $oCiclo)
    {
        try{
            $oAreaIntermediary = PersistenceFactory::getAreaIntermediary($this->db);
            return $oAreaIntermediary->verificarExisteAreaByDescripcion($sDescripcion, $oCiclo);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Verifica si existe el eje
     */
    public function verificarExisteEjeByDescripcion($sDescripcion, $oArea)
    {
        try{
            $oEjeIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            return $oEjeIntermediary->verificarExisteEjeByDescripcion($sDescripcion, $oArea);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Obtiene todas las unidades precargadas que el sistema brinda para que puedan
     * ser asociadas a los seguimientos SCC de los integrantes de la comunidad
     *
     */
    public function obtenerUnidadesPrecargadasSeguimientosSCC($filtro, &$iRecordsTotal)
    {
    	try{            
            $filtro["u.preCargada"] = "1";
            $filtro["u.asociacionAutomatica"] = "0";
            $filtro["u.borradoLogico"] = "0";

            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            return $oUnidadIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getUnidadById($iUnidadId)
    {
    	try{
            $filtro = array('u.id' => $iUnidadId);
            $iRecordsTotal = 0;
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            $aUnidad = $oUnidadIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aUnidad){
                return $aUnidad[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function guardarUnidad($oUnidad){
        try{
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            return $oUnidadIntermediary->guardar($oUnidad);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Borrar Unidad
     *
     */
    public function borrarUnidad($oUnidad)
    {
    	try{
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);

            $success = true;
            if(null !== $oUnidad->getVariables()){
                //borro todas las variables de la unidad. si devuelve true no hubo errores.
                $success = $this->borrarVariables($oUnidad->getVariables());
            }

            if($success){
                //en este metodo se fija que si al menos una variable tiene borrado logico la unidad tmb
                //se borra logicamente.
                return $oUnidadIntermediary->borrar($oUnidad->getId());
            }else{
                return false;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * recibe un array de 1 o N variables y las borra fisica o logicamente dependiendo el plazo dispuesto para la edicion de seguimientos.
     *
     * Toda variable que tenga asociado un valor a un seguimiento en una fecha que exceda la cantidad de dias del plazo
     * sera borrada logicamente en el sistema.
     *
     * El metodo esta pensado para que pueda ser utilizado tanto en la eliminacion individual de una variable
     * como en la eliminacion de una unidad con un conjunto N de variables.
     */
    public function borrarVariables($aVariables)
    {
    	try{
            $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EDICION_SEGUIMIENTOS');
            $oVariableIntermediary = PersistenceFactory::getVariableIntermediary($this->db);

            //genero un string con los ids separados con ',' para que se realize la transaccion en el sql.
            $sIds = "";
            foreach($aVariables as $oVariable){
                $sIds .= $oVariable->getId().",";
                $sIds = substr($sIds, 0, -1);
            }

            return $oVariableIntermediary->borrarVariables($sIds, $cantDiasExpiracion);
        }catch(Exception $e){
            throw $e;
        }
    }
}