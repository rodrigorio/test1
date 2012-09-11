<?php
/**
 *
 * Description of classUsuarioMySQLIntermediary
 *
 * @author Andres
 */
class UsuarioMySQLIntermediary extends UsuarioIntermediary
{
    /* tienen que corresponder con los ids de la tabla perfiles */
    const PERFIL_ADMINISTRADOR = 1;
    const PERFIL_MODERADOR = 5;
    const PERFIL_INTEGRANTE_ACTIVO = 2;
    const PERFIL_INTEGRANTE_INACTIVO = 3;

    private static $instance = null;

    protected function __construct($conn) {
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return GroupMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    /**
     * Devuelve un array con los perfiles con el formato $[nombrePerfil] = idPerfil
     * NOTA: no se hace un sql sino que se devuelven las constantes de la clase.
     */
    public function obtenerPerfiles()
    {
        return array('administrador' => self::PERFIL_ADMINISTRADOR,
                     'moderador' => self::PERFIL_MODERADOR,
                     'integrante activo' => self::PERFIL_INTEGRANTE_ACTIVO,
                     'integrante inactivo' => self::PERFIL_INTEGRANTE_INACTIVO);
    }

    /**
     * Devuelve un objeto perfil con el usuario asociado.
     */
    public function obtenerPerfil($oUsuario){
        $db = $this->conn;

        $sSQL = "SELECT u.perfiles_id FROM usuarios u WHERE u.id = ".$oUsuario->getId();

        $db->query($sSQL);

        $record = $db->oNextRecord();

        //creo el perfil con el usuario asignado
        $oPerfilAbstract             = new stdClass();
        $oPerfilAbstract->iId        = $record->perfiles_id;
        $oPerfilAbstract->oUsuario   = $oUsuario;
        switch($record->perfiles_id){
            case self::PERFIL_ADMINISTRADOR:{ $oPerfil       = Factory::getAdministradorInstance($oPerfilAbstract); break; }
            case self::PERFIL_MODERADOR:{ $oPerfil           = Factory::getModeradorInstance($oPerfilAbstract); break; }
            case self::PERFIL_INTEGRANTE_ACTIVO:{ $oPerfil   = Factory::getIntegranteActivoInstance($oPerfilAbstract); break; }
            case self::PERFIL_INTEGRANTE_INACTIVO:{ $oPerfil = Factory::getIntegranteInactivoInstance($oPerfilAbstract); break; }
        }

        return $oPerfil;
    }

    /**
     * Sirve para guardar en DB la modificacion de un perfil para un usuario
     * Luego guarda el usuario
     *
     * @param Perfil $oPerfil El objeto perfil a guardar
     * @param boolean $bGuardarUsuario si se pasa falso no guarda el objeto usuario
     */
    public function guardarPerfil($oPerfil, $bGuardarUsuario = true){
        if($bGuardarUsuario){
            $this->guardar($oPerfil->getUsuario());
            $db = clone($this->conn);
        }else{
            $db = $this->conn;
        }

        $sSQL = "UPDATE usuarios
                 SET perfiles_id = ".$this->escInt($oPerfil->getId())."
                 WHERE id = ".$this->escInt($oPerfil->getUsuario()->getId());

        $db->execSQL($sSQL);
        $db->commit();

        return true;
    }

    /**
     * Se fija si existen objetos usuarios que cumplan con el filtro,
     * al objeto/s le asigna el perfil dependiendo lo que levanto de la DB.
     * Retorna null si no encuentra resutados, un objeto Usuario o un array de objetos Usuario.
     * arroja excepcion si hubo algun problema en la consulta.
     */
    public function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        p.id as iId, p.nombre as sNombre, p.apellido as sApellido,
                        p.sexo as sSexo, p.fechaNacimiento as dFechaNacimiento,
                        p.email as sEmail, p.telefono as sTelefono, p.celular as sCelular,
                        p.fax as sFax, p.domicilio as sDomicilio, p.ciudadOrigen as sCiudadOrigen,
                        p.ciudades_id as iCiudadId, p.instituciones_id as iInstitucionId,
                        p.codigoPostal as sCodigoPostal, p.empresa as sEmpresa,
                        p.universidad as sUniversidad, p.secundaria as sSecundaria,
                        p.documento_tipos_id as iTipoDocumentoId,
                        p.numeroDocumento as sNumeroDocumento,

                        u.sitioWeb as sSitioWeb, u.nombre as sNombreUsuario, u.activo as bActivo,
                        u.fechaAlta as dFechaAlta, u.contrasenia as sContrasenia,
                        u.invitacionesDisponibles as iInvitacionesDisponibles,
                        u.cargoInstitucion as sCargoInstitucion, u.biografia as sBiografia,
                        u.universidadCarrera as sUniveridadCarrera, u.carreraFinalizada as bCarreraFinalizada,
                        u.urlTokenKey as sUrlTokenKey,

                        e.id as iEspecialidadId,
                        e.nombre as sEspecialidadNombre, e.descripcion as sEspecialidadDescripcion,

                        a.id as iCvId, a.nombre as sCvNombre,
                        a.nombreServidor as sCvNombreServidor, a.descripcion as sCvDescripcion,
                        a.tipoMime as sCvTipoMime, a.tamanio as iCvTamanio,
                        a.fechaAlta as sCvFechaAlta, a.orden as iCvOrden,
                        a.titulo as sCvTitulo, a.tipo as sCvTipo,

                        f.id as iFotoId, f.nombreBigSize as sFotoNombreBigSize,
                        f.nombreMediumSize as sFotoNombreMediumSize, f.nombreSmallSize as sFotoNombreSmallSize,
                        f.orden as iFotoOrden, f.titulo as sFotoTitulo,
                        f.descripcion as sFotoDescripcion, f.tipo as sFotoTipo
                    FROM
                        personas p JOIN usuarios u ON p.id = u.id
                        LEFT JOIN especialidades e ON u.especialidades_id = e.id
                        LEFT JOIN archivos a ON a.usuarios_id = u.id
                        LEFT JOIN fotos f ON f.personas_id = u.id ";

            $WHERE = array();
            if(isset($filtro['p.id']) && $filtro['p.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.id', $filtro['p.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['p.nombre']) && $filtro['p.nombre']!=""){
                $WHERE[] = $this->crearFiltroTexto('p.nombre', $filtro['p.nombre']);
            }
            if(isset($filtro['p.numeroDocumento']) && $filtro['p.numeroDocumento']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.numeroDocumento', $filtro['p.numeroDocumento']);
            }
            if(isset($filtro['p.documento_tipos_id']) && $filtro['p.documento_tipos_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.documento_tipos_id', $filtro['p.documento_tipos_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['u.contrasenia']) && $filtro['u.contrasenia']!=""){
                $WHERE[] = $this->crearFiltroTexto('u.contrasenia', $filtro['u.contrasenia']);
            }
            if(isset($filtro['p.email']) && $filtro['p.email']!=""){
                $WHERE[] = $this->crearFiltroTexto('p.email', $filtro['p.email']);
            }
            if(isset($filtro['u.nombre']) && $filtro['u.nombre']!=""){
                $WHERE[] = $this->crearFiltroTexto('u.nombre', $filtro['u.nombre']);
            }
            if(isset($filtro['u.urlTokenKey']) && $filtro['u.urlTokenKey']!=""){
                $WHERE[] = $this->crearFiltroTexto('u.urlTokenKey', $filtro['u.urlTokenKey']);
            }
           
            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);
                                
            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }
                       
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aUsuarios = array();
            while($oObj = $db->oNextRecord()){                                
                $oUsuario                   = new stdClass();
                $oUsuario->iId              = $oObj->iId;
                $oUsuario->sNombre          = $oObj->sNombre;
                $oUsuario->sApellido        = $oObj->sApellido;
                $oUsuario->iTipoDocumentoId = $oObj->iTipoDocumentoId;
                $oUsuario->sNumeroDocumento = $oObj->sNumeroDocumento;
                $oUsuario->sSexo            = $oObj->sSexo;
                $oUsuario->dFechaNacimiento = $oObj->dFechaNacimiento;
                $oUsuario->sEmail           = $oObj->sEmail;
                $oUsuario->sTelefono        = $oObj->sTelefono;
                $oUsuario->sCelular         = $oObj->sCelular;
                $oUsuario->sFax             = $oObj->sFax;
                $oUsuario->sDomicilio       = $oObj->sDomicilio;
                $oUsuario->iCiudadId        = $oObj->iCiudadId; //para sacar objeto ciudad por demanda
                $oUsuario->iInstitucionId   = $oObj->iInstitucionId; //lo mismo xq es un obj pesado
                $oUsuario->oCiudad          = null;
                $oUsuario->oInstitucion     = null;
                $oUsuario->oEspecialidad    = null;
                $oUsuario->oFotoPerfil      = null;
                $oUsuario->oCurriculumVitae = null;
                $oUsuario->sCiudadOrigen    = $oObj->sCiudadOrigen;
                $oUsuario->sCodigoPostal    = $oObj->sCodigoPostal;
                $oUsuario->sEmpresa         = $oObj->sEmpresa;
                $oUsuario->sUniversidad     = $oObj->sUniversidad;
                $oUsuario->sSecundaria      = $oObj->sSecundaria;
                $oUsuario->sSitioWeb        = $oObj->sSitioWeb;
                $oUsuario->sNombreUsuario   = $oObj->sNombreUsuario;
                $oUsuario->sContrasenia     = $oObj->sContrasenia;
                $oUsuario->dFechaAlta       = $oObj->dFechaAlta;
                $oUsuario->sUrlTokenKey     = $oObj->sUrlTokenKey;
                $oUsuario->sCargoInstitucion    = $oObj->sCargoInstitucion;
                $oUsuario->sBiografia           = $oObj->sBiografia;
                $oUsuario->sUniveridadCarrera   = $oObj->sUniveridadCarrera;
                $oUsuario->bCarreraFinalizada   = $oObj->bCarreraFinalizada ? true : false;
                $oUsuario->bActivo = ($oObj->bActivo == '1')?true:false;
                $oUsuario->iInvitacionesDisponibles = $oObj->iInvitacionesDisponibles;

                //objeto especialidad si tiene
                if(null !== $oObj->iEspecialidadId){
                    $oEspecialidad = new stdClass();
                    $oEspecialidad->iId             = $oObj->iEspecialidadId;
                    $oEspecialidad->sNombre         = $oObj->sEspecialidadNombre;
                    $oEspecialidad->sDescripcion    = $oObj->sEspecialidadDescripcion;
                    $oUsuario->oEspecialidad = Factory::getEspecialidadInstance($oEspecialidad);
                }

                if(null !== $oObj->iCvId){
                    $oCurriculumVitae = new stdClass();
                    $oCurriculumVitae->iId = $oObj->iCvId;
                    $oCurriculumVitae->sNombre = $oObj->sCvNombre;
                    $oCurriculumVitae->sNombreServidor = $oObj->sCvNombreServidor;
                    $oCurriculumVitae->sDescripcion = $oObj->sCvDescripcion;
                    $oCurriculumVitae->sTipoMime = $oObj->sCvTipoMime;
                    $oCurriculumVitae->iTamanio = $oObj->iCvTamanio;
                    $oCurriculumVitae->sFechaAlta = $oObj->sCvFechaAlta;
                    $oCurriculumVitae->iOrden = $oObj->iCvOrden;
                    $oCurriculumVitae->sTitulo = $oObj->sCvTitulo;
                    $oCurriculumVitae->sTipo = $oObj->sCvTipo;
                    $oUsuario->oCurriculumVitae = Factory::getArchivoInstance($oCurriculumVitae);
                }

                if(null !== $oObj->iFotoId){
                    $fotoPerfil = new stdClass();
                    $fotoPerfil->iId = $oObj->iFotoId;
                    $fotoPerfil->sNombreBigSize = $oObj->sFotoNombreBigSize;
                    $fotoPerfil->sNombreMediumSize = $oObj->sFotoNombreMediumSize;
                    $fotoPerfil->sNombreSmallSize = $oObj->sFotoNombreSmallSize;
                    $fotoPerfil->iOrden = $oObj->iFotoOrden;
                    $fotoPerfil->sTitulo = $oObj->sFotoTitulo;
                    $fotoPerfil->sDescripcion = $oObj->sFotoDescripcion;
                    $fotoPerfil->sTipo = $oObj->sFotoTipo;
                    $oUsuario->oFotoPerfil = Factory::getFotoInstance($fotoPerfil);
                }

                $aUsuarios[] = Factory::getUsuarioInstance($oUsuario);
           }

           return $aUsuarios;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Este metodo es similar al obtener pero la consulta utiliza diversos join para lograr los filtros en los listados de usuarios.
     * Si una persona filtra por el nombre de la ciudad en esta consulta se extrae la descripcion en el join con ciudad para generar la condicion en el where.
     */
    public function buscar($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        p.id as iId, p.nombre as sNombre, p.apellido as sApellido,
                        p.sexo as sSexo, p.fechaNacimiento as dFechaNacimiento,
                        p.email as sEmail, p.telefono as sTelefono, p.celular as sCelular,
                        p.fax as sFax, p.domicilio as sDomicilio, p.ciudadOrigen as sCiudadOrigen,
                        p.ciudades_id as iCiudadId, p.instituciones_id as iInstitucionId,
                        p.codigoPostal as sCodigoPostal, p.empresa as sEmpresa,
                        p.universidad as sUniversidad, p.secundaria as sSecundaria,
                        p.documento_tipos_id as iTipoDocumentoId,
                        p.numeroDocumento as sNumeroDocumento,

                        u.sitioWeb as sSitioWeb, u.nombre as sNombreUsuario, u.activo as bActivo,
                        u.fechaAlta as dFechaAlta, u.contrasenia as sContrasenia,
                        u.invitacionesDisponibles as iInvitacionesDisponibles,
                        u.cargoInstitucion as sCargoInstitucion, u.biografia as sBiografia,
                        u.universidadCarrera as sUniveridadCarrera, u.carreraFinalizada as bCarreraFinalizada,
                        u.urlTokenKey as sUrlTokenKey,

                        f.id as iFotoId, f.nombreBigSize as sFotoNombreBigSize,
                        f.nombreMediumSize as sFotoNombreMediumSize, f.nombreSmallSize as sFotoNombreSmallSize,
                        f.orden as iFotoOrden, f.titulo as sFotoTitulo,
                        f.descripcion as sFotoDescripcion, f.tipo as sFotoTipo,

                        a.id as iCvId, a.nombre as sCvNombre,
                        a.nombreServidor as sCvNombreServidor, a.descripcion as sCvDescripcion,
                        a.tipoMime as sCvTipoMime, a.tamanio as iCvTamanio,
                        a.fechaAlta as sCvFechaAlta, a.orden as iCvOrden,
                        a.titulo as sCvTitulo, a.tipo as sCvTipo,

                        pe.descripcion as sPerfilDescripcion
                    FROM
                        personas p JOIN usuarios u ON p.id = u.id
                        JOIN perfiles pe ON u.perfiles_id = pe.id
                        LEFT JOIN fotos f ON f.personas_id = u.id
                        LEFT JOIN archivos a ON a.usuarios_id = u.id
                        LEFT JOIN ciudades c ON p.ciudades_id = c.id
                        LEFT JOIN instituciones i ON p.instituciones_id = i.id";

            $WHERE = array();

            if(isset($filtro['p.apellido']) && $filtro['p.apellido']!=""){
                $WHERE[] = $this->crearFiltroTexto('p.apellido', $filtro['p.apellido']);
            }            
            if(isset($filtro['p.numeroDocumento']) && $filtro['p.numeroDocumento']!=""){
                $WHERE[] = $this->crearFiltroTexto('p.numeroDocumento', $filtro['p.numeroDocumento']);
            }
            if(isset($filtro['i.nombre']) && $filtro['i.nombre'] != ""){
                $WHERE[] = $this->crearFiltroTexto('i.nombre', $filtro['i.nombre']);
            }
            if(isset($filtro['c.nombre']) && $filtro['c.nombre']!=""){
                $WHERE[] = $this->crearFiltroTexto('c.nombre', $filtro['c.nombre']);
            }
            if(isset($filtro['u.especialidades_id']) && $filtro['u.especialidades_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('u.especialidades_id', $filtro['u.especialidades_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['u.perfiles_id']) && $filtro['u.perfiles_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('u.perfiles_id', $filtro['u.perfiles_id'], MYSQL_TYPE_INT);
            }            
            if(isset($filtro['u.activo']) && $filtro['u.activo']!=""){
                $WHERE[] = $this->crearFiltroSimple('u.activo', $filtro['u.activo']);
            }
            if(isset($filtro['p.instituciones_id']) && $filtro['p.instituciones_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.instituciones_id', $filtro['p.instituciones_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['u.urlTokenKey']) && $filtro['u.urlTokenKey']!=""){
                $WHERE[] = $this->crearFiltroTexto('u.urlTokenKey', $filtro['u.urlTokenKey']);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);
                       
            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by p.apellido";
            }
            
            if ($iIniLimit !== null && $iRecordCount !== null ){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }
            
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aUsuarios = array();
            while($oObj = $db->oNextRecord()){
                $oUsuario                   = new stdClass();
                $oUsuario->iId              = $oObj->iId;
                $oUsuario->sNombre          = $oObj->sNombre;
                $oUsuario->sApellido        = $oObj->sApellido;
                $oUsuario->iTipoDocumentoId = $oObj->iTipoDocumentoId;
                $oUsuario->sNumeroDocumento = $oObj->sNumeroDocumento;
                $oUsuario->sSexo            = $oObj->sSexo;
                $oUsuario->dFechaNacimiento = $oObj->dFechaNacimiento;
                $oUsuario->sEmail           = $oObj->sEmail;
                $oUsuario->sTelefono        = $oObj->sTelefono;
                $oUsuario->sCelular         = $oObj->sCelular;
                $oUsuario->sFax             = $oObj->sFax;
                $oUsuario->sDomicilio       = $oObj->sDomicilio;
                $oUsuario->iCiudadId        = $oObj->iCiudadId; //para sacar objeto ciudad por demanda
                $oUsuario->iInstitucionId   = $oObj->iInstitucionId; //lo mismo xq es un obj pesado
                $oUsuario->oCiudad          = null;
                $oUsuario->oInstitucion     = null;
                $oUsuario->oEspecialidad    = null;
                $oUsuario->oFotoPerfil      = null;
                $oUsuario->oCurriculumVitae = null;
                $oUsuario->sCiudadOrigen    = $oObj->sCiudadOrigen;
                $oUsuario->sCodigoPostal    = $oObj->sCodigoPostal;
                $oUsuario->sEmpresa         = $oObj->sEmpresa;
                $oUsuario->sUniversidad     = $oObj->sUniversidad;
                $oUsuario->sSecundaria      = $oObj->sSecundaria;
                $oUsuario->sSitioWeb        = $oObj->sSitioWeb;
                $oUsuario->sNombreUsuario   = $oObj->sNombreUsuario;
                $oUsuario->sContrasenia     = $oObj->sContrasenia;
                $oUsuario->dFechaAlta       = $oObj->dFechaAlta;
                $oUsuario->sUrlTokenKey     = $oObj->sUrlTokenKey;
                $oUsuario->sCargoInstitucion    = $oObj->sCargoInstitucion;
                $oUsuario->sBiografia           = $oObj->sBiografia;
                $oUsuario->sUniveridadCarrera   = $oObj->sUniveridadCarrera;
                $oUsuario->bCarreraFinalizada   = $oObj->bCarreraFinalizada ? true : false;
                $oUsuario->bActivo = ($oObj->bActivo == '1')?true:false;
                $oUsuario->iInvitacionesDisponibles = $oObj->iInvitacionesDisponibles;

                if(null !== $oObj->iFotoId){
                    $fotoPerfil = new stdClass();
                    $fotoPerfil->iId = $oObj->iFotoId;
                    $fotoPerfil->sNombreBigSize = $oObj->sFotoNombreBigSize;
                    $fotoPerfil->sNombreMediumSize = $oObj->sFotoNombreMediumSize;
                    $fotoPerfil->sNombreSmallSize = $oObj->sFotoNombreSmallSize;
                    $fotoPerfil->iOrden = $oObj->iFotoOrden;
                    $fotoPerfil->sTitulo = $oObj->sFotoTitulo;
                    $fotoPerfil->sDescripcion = $oObj->sFotoDescripcion;
                    $fotoPerfil->sTipo = $oObj->sFotoTipo;
                    $oUsuario->oFotoPerfil = Factory::getFotoInstance($fotoPerfil);
                }

                if(null !== $oObj->iCvId){
                    $oCurriculumVitae = new stdClass();
                    $oCurriculumVitae->iId = $oObj->iCvId;
                    $oCurriculumVitae->sNombre = $oObj->sCvNombre;
                    $oCurriculumVitae->sNombreServidor = $oObj->sCvNombreServidor;
                    $oCurriculumVitae->sDescripcion = $oObj->sCvDescripcion;
                    $oCurriculumVitae->sTipoMime = $oObj->sCvTipoMime;
                    $oCurriculumVitae->iTamanio = $oObj->iCvTamanio;
                    $oCurriculumVitae->sFechaAlta = $oObj->sCvFechaAlta;
                    $oCurriculumVitae->iOrden = $oObj->iCvOrden;
                    $oCurriculumVitae->sTitulo = $oObj->sCvTitulo;
                    $oCurriculumVitae->sTipo = $oObj->sCvTipo;
                    $oUsuario->oCurriculumVitae = Factory::getArchivoInstance($oCurriculumVitae);
                }

                $aUsuarios[] = Factory::getUsuarioInstance($oUsuario);
           }

           return $aUsuarios;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Devuelve todos los usuarios que realizan seguimientos a un discapacitado
     */
    public function obtenerUsuariosAsociadosPersona($iDiscapacitadoId)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT DISTINCT SQL_CALC_FOUND_ROWS
                        p.id as iId, p.nombre as sNombre, p.apellido as sApellido,
                        p.sexo as sSexo, p.fechaNacimiento as dFechaNacimiento,
                        p.email as sEmail, p.telefono as sTelefono, p.celular as sCelular,
                        p.fax as sFax, p.domicilio as sDomicilio, p.ciudadOrigen as sCiudadOrigen,
                        p.ciudades_id as iCiudadId, p.instituciones_id as iInstitucionId,
                        p.codigoPostal as sCodigoPostal, p.empresa as sEmpresa,
                        p.universidad as sUniversidad, p.secundaria as sSecundaria,
                        p.documento_tipos_id as iTipoDocumentoId,
                        p.numeroDocumento as sNumeroDocumento,

                        u.sitioWeb as sSitioWeb, u.nombre as sNombreUsuario, u.activo as bActivo,
                        u.fechaAlta as dFechaAlta, u.contrasenia as sContrasenia,
                        u.invitacionesDisponibles as iInvitacionesDisponibles,
                        u.cargoInstitucion as sCargoInstitucion, u.biografia as sBiografia,
                        u.universidadCarrera as sUniveridadCarrera, u.carreraFinalizada as bCarreraFinalizada,
                        u.urlTokenKey as sUrlTokenKey,

                        f.id as iFotoId, f.nombreBigSize as sFotoNombreBigSize,
                        f.nombreMediumSize as sFotoNombreMediumSize, f.nombreSmallSize as sFotoNombreSmallSize,
                        f.orden as iFotoOrden, f.titulo as sFotoTitulo,
                        f.descripcion as sFotoDescripcion, f.tipo as sFotoTipo,

                        a.id as iCvId, a.nombre as sCvNombre,
                        a.nombreServidor as sCvNombreServidor, a.descripcion as sCvDescripcion,
                        a.tipoMime as sCvTipoMime, a.tamanio as iCvTamanio,
                        a.fechaAlta as sCvFechaAlta, a.orden as iCvOrden,
                        a.titulo as sCvTitulo, a.tipo as sCvTipo
                    FROM
                        personas p JOIN usuarios u ON p.id = u.id
                        LEFT JOIN fotos f ON f.personas_id = u.id
                        LEFT JOIN archivos a ON a.usuarios_id = u.id
                        JOIN seguimientos s ON s.usuarios_id = u.id
                    WHERE
                        s.discapacitados_id = ".$iDiscapacitadoId." 
                    ORDER BY p.apellido ASC";

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aUsuarios = array();
            while($oObj = $db->oNextRecord()){
                $oUsuario                   = new stdClass();
                $oUsuario->iId              = $oObj->iId;
                $oUsuario->sNombre          = $oObj->sNombre;
                $oUsuario->sApellido        = $oObj->sApellido;
                $oUsuario->iTipoDocumentoId = $oObj->iTipoDocumentoId;
                $oUsuario->sNumeroDocumento = $oObj->sNumeroDocumento;
                $oUsuario->sSexo            = $oObj->sSexo;
                $oUsuario->dFechaNacimiento = $oObj->dFechaNacimiento;
                $oUsuario->sEmail           = $oObj->sEmail;
                $oUsuario->sTelefono        = $oObj->sTelefono;
                $oUsuario->sCelular         = $oObj->sCelular;
                $oUsuario->sFax             = $oObj->sFax;
                $oUsuario->sDomicilio       = $oObj->sDomicilio;
                $oUsuario->iCiudadId        = $oObj->iCiudadId; //para sacar objeto ciudad por demanda
                $oUsuario->iInstitucionId   = $oObj->iInstitucionId; //lo mismo xq es un obj pesado
                $oUsuario->oCiudad          = null;
                $oUsuario->oInstitucion     = null;
                $oUsuario->oEspecialidad    = null;
                $oUsuario->oFotoPerfil      = null;
                $oUsuario->oCurriculumVitae = null;
                $oUsuario->sCiudadOrigen    = $oObj->sCiudadOrigen;
                $oUsuario->sCodigoPostal    = $oObj->sCodigoPostal;
                $oUsuario->sEmpresa         = $oObj->sEmpresa;
                $oUsuario->sUniversidad     = $oObj->sUniversidad;
                $oUsuario->sSecundaria      = $oObj->sSecundaria;
                $oUsuario->sSitioWeb        = $oObj->sSitioWeb;
                $oUsuario->sNombreUsuario   = $oObj->sNombreUsuario;
                $oUsuario->sContrasenia     = $oObj->sContrasenia;
                $oUsuario->dFechaAlta       = $oObj->dFechaAlta;
                $oUsuario->sUrlTokenKey     = $oObj->sUrlTokenKey;
                $oUsuario->sCargoInstitucion    = $oObj->sCargoInstitucion;
                $oUsuario->sBiografia           = $oObj->sBiografia;
                $oUsuario->sUniveridadCarrera   = $oObj->sUniveridadCarrera;
                $oUsuario->bCarreraFinalizada   = $oObj->bCarreraFinalizada ? true : false;
                $oUsuario->bActivo = ($oObj->bActivo == '1')?true:false;
                $oUsuario->iInvitacionesDisponibles = $oObj->iInvitacionesDisponibles;

                if(null !== $oObj->iFotoId){
                    $fotoPerfil = new stdClass();
                    $fotoPerfil->iId = $oObj->iFotoId;
                    $fotoPerfil->sNombreBigSize = $oObj->sFotoNombreBigSize;
                    $fotoPerfil->sNombreMediumSize = $oObj->sFotoNombreMediumSize;
                    $fotoPerfil->sNombreSmallSize = $oObj->sFotoNombreSmallSize;
                    $fotoPerfil->iOrden = $oObj->iFotoOrden;
                    $fotoPerfil->sTitulo = $oObj->sFotoTitulo;
                    $fotoPerfil->sDescripcion = $oObj->sFotoDescripcion;
                    $fotoPerfil->sTipo = $oObj->sFotoTipo;
                    $oUsuario->oFotoPerfil = Factory::getFotoInstance($fotoPerfil);
                }

                if(null !== $oObj->iCvId){
                    $oCurriculumVitae = new stdClass();
                    $oCurriculumVitae->iId = $oObj->iCvId;
                    $oCurriculumVitae->sNombre = $oObj->sCvNombre;
                    $oCurriculumVitae->sNombreServidor = $oObj->sCvNombreServidor;
                    $oCurriculumVitae->sDescripcion = $oObj->sCvDescripcion;
                    $oCurriculumVitae->sTipoMime = $oObj->sCvTipoMime;
                    $oCurriculumVitae->iTamanio = $oObj->iCvTamanio;
                    $oCurriculumVitae->sFechaAlta = $oObj->sCvFechaAlta;
                    $oCurriculumVitae->iOrden = $oObj->iCvOrden;
                    $oCurriculumVitae->sTitulo = $oObj->sCvTitulo;
                    $oCurriculumVitae->sTipo = $oObj->sCvTipo;
                    $oUsuario->oCurriculumVitae = Factory::getArchivoInstance($oCurriculumVitae);
                }

                $aUsuarios[] = Factory::getUsuarioInstance($oUsuario);
           }

           return $aUsuarios;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }        
    }

    public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        personas p 
                    JOIN 
                    	usuarios u ON p.id = u.id
                    WHERE ".$this->crearCondicionSimple($filtro,"",false,"OR");

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ 
            	return false; 
            }
            return true;
    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }
    
    public function actualizarCampoArray($objects, $cambios){}

    public function registrar(Usuario $oUsuario, $iUserId)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();
            
            $filtro["u.nombre"] = $oUsuario->getNombreUsuario();

            if($this->existe($filtro)){
                return 10;
            }

            $filtro["p.numeroDocumento"]= $oUsuario->getNumeroDocumento();
            if($this->existe($filtro)){
                return 11;
            }

            $filtro["p.email"]= $oUsuario->getEmail();
            if($this->existe($filtro)){
                return 12;
            }

            $sSQL = " update personas " .
                    " set nombre =".$db->escape($oUsuario->getNombre(),true).", " .
                    " apellido =".$db->escape($oUsuario->getApellido(),true).", " .
                    " documento_tipos_id =".$db->escape($oUsuario->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),true).", " .
                    " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
                    " fechaNacimiento= ".$db->escape($oUsuario->getFechaNacimiento(), true,MYSQL_TYPE_DATE);

            if($oUsuario->getEmail()){
                $sSQL .=" ,email = ".$db->escape($oUsuario->getEmail(),true)." ";
            }

            $sSQL .=" WHERE id = ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);

            $sUrlTokenKey = md5($time.$oUsuario->getNombre().$oUsuario->getApellido());
            
            $sSQL =" insert usuarios ".
                   " set id = ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT).", ".
                   " perfiles_id = ".self::PERFIL_INTEGRANTE_INACTIVO.", ".
                   " nombre = ".$db->escape($oUsuario->getNombreUsuario(),true).", ".
                   " urlTokenKey = '".$sUrlTokenKey."', ".
                   " contrasenia = ".$db->escape($oUsuario->getContrasenia(),true)." ";

            $db->execSQL($sSQL);

            $sSQL =" update usuario_x_invitado ".
                   " SET estado = 'aceptada' ".
                   " WHERE usuarios_id= ".$db->escape($iUserId,false,MYSQL_TYPE_INT)." ".
                   " AND invitados_id=".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);

            $db->execSQL("insert into privacidad set usuarios_id = ".$oUsuario->getId());

            $db->commit();

            $oUsuario->setUrlTokenKey($sUrlTokenKey);
            
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }
        
    public function sendMail($orig, $dest, $asunto, $body){
    	  // Varios destinatarios
            $para  = $dest;

            // subject
            $titulo = $asunto;

            // message
            $mensaje = $body;

            // Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
            $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            // Cabeceras adicionales
            $cabeceras .= "To: ".$dest. "\r\n";
            $cabeceras .= 'From: Registracion <'.$orig.'>' . "\r\n";
            $cabeceras .= 'Cc:' . "\r\n";
            $cabeceras .= 'Bcc: ' . "\r\n";

            // Mail it
            if (mail($para, $titulo, $mensaje, $cabeceras)){
                    return true;
            }else{
                    return false;
            }
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $iIdUsuario
     * @param Invitado $oInvitado {
     * 		nombre
     * 		apellido
     * 		email
     * 		relacion
     * }
     * @throws Exception
     */
    
	public function enviarInvitacion($oUsuario , Invitado $oInvitado){
		
        try{
			$db = $this->conn;
			$db->begin_transaction();
			$sSQL =	" insert personas set";
					if($oInvitado->getNombre()){
						$sSQL .=" nombre =".$db->escape($oInvitado->getNombre(),true).", ";
					}
					if($oInvitado->getApellido()){
                    	$sSQL .=" apellido =".$db->escape($oInvitado->getApellido(),true).", ";
					}
			$sSQL .= " email = ".$db->escape($oInvitado->getEmail(),true)." ";
			$db->execSQL($sSQL);
			$iUltimoId = $db->insert_id();

			$sSQL 	= " insert invitados ".
			        " set id= ".$iUltimoId."";
			$db->execSQL($sSQL);
			$iIdUsuario = $oUsuario->getId();
			$time 	= time();
			$token 	= md5($time);
			$sSQL =" insert usuario_x_invitado ".
			        " set usuarios_id= ".$iIdUsuario.", ".
                    " invitados_id=".$iUltimoId.", ".
                    " relacion=".$db->escape($oInvitado->getRelacion(),true).",".
			 		" token=".$db->escape($token,true)."";
			$db->execSQL($sSQL);

			$sSQL =" update usuarios u ".
			        " set u.invitacionesDisponibles = u.invitacionesDisponibles-1 ".
			 		" WHERE u.id= ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)."";
			$db->execSQL($sSQL);

			$db->commit();
			
			$nom 	= $oInvitado->getNombre();
			$ape 	= $oInvitado->getApellido();
			$email 	= $oInvitado->getEmail();
			
			$body 	= "<p>Usted ha sido invitado por ".$oUsuario->getNombre().", ".$oUsuario->getApellido()."";
			$body 	.= "<br/> para que pueda integrar la comunidad de profesionales de personas discapacitas, etc, etc.";
			$body 	.= "<a href='http://www.rodrigorio.com.ar/tesis/registracion?token=$token' > registrate</a>";
			$body 	.= "</p>";
			$msg = '
			<html>
			<head>
			  <title>Usted ha sido invitado para registrarse en .....</title>
			</head>
			<body>
			  <p>Haga click en el siguiente enlace para poder registrarse!</p>
			  <div>'.$body.'</div>
			</body>
			</html>
			';
			$asunto = "registracion";
			$dest 	= $oInvitado->getEmail();
			$orig	= "registracion@sistemadegestion.com";
			$this->sendMail($orig, $dest, $asunto, $msg);
			return  true;
		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }


    public function actualizar($oUsuario)
    {       
        try{
            $db = $this->conn;

            if(null != $oUsuario->getCiudad()){
                $ciudadId = $oUsuario->getCiudad()->getId();
            }else{
                $ciudadId = 'null';
            }

            if(null != $oUsuario->getInstitucion()){
                $institucionId = $oUsuario->getInstitucion()->getId();
            }else{
                $institucionId = 'null';
            }

            if(null != $oUsuario->getEspecialidad()){
                $especialidadId = $oUsuario->getEspecialidad()->getId();
            }else{
                $especialidadId = 'null';
            }

            $carreraFinalizada = $oUsuario->isCarreraFinalizada() ? "1" : "0";

            $activo = $oUsuario->isActivo()?"1":"0";
			
            $db->begin_transaction();
            $sSQL = " update personas " .
                    " set nombre =".$db->escape($oUsuario->getNombre(),true).", " .
                    " apellido =".$db->escape($oUsuario->getApellido(),true).", " .
                    " documento_tipos_id =".$db->escape($oUsuario->getTipoDocumento(), false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),true).", " .
                    " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
                    " fechaNacimiento = '".$oUsuario->getFechaNacimiento()."',".
                    " email =".$db->escape($oUsuario->getEmail(),true).", " .
                    " telefono =".$db->escape($oUsuario->getTelefono(),true).", " .
                    " celular =".$db->escape($oUsuario->getCelular(),true).", " .
                    " fax =".$db->escape($oUsuario->getFax(),true).", " .
                    " domicilio =".$db->escape($oUsuario->getDomicilio(),true).", " .
                    " instituciones_id =".$institucionId.", ".
                    " ciudades_id =".$ciudadId.", ".
                    " ciudadOrigen =".$db->escape($oUsuario->getCiudadOrigen(),true).", " .
                    " codigoPostal =".$db->escape($oUsuario->getCodigoPostal(),true).", " .
                    " empresa =".$db->escape($oUsuario->getEmpresa(),true).", " .
                    " universidad =".$db->escape($oUsuario->getUniversidad(),true).", " .
                    " secundaria =".$db->escape($oUsuario->getSecundaria(),true)." ".
                    " WHERE id = ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)." ";
            
            $db->execSQL($sSQL);

            $sSQL =" update usuarios ".
                   " set sitioWeb = ".$db->escape($oUsuario->getSitioWeb(),true).", " .
                   " especialidades_id = ".$especialidadId.", ".
                   " cargoInstitucion = ".$this->escStr($oUsuario->getCargoInstitucion()).", ".
                   " biografia = ".$this->escStr($oUsuario->getBiografia()).", ".
                   " universidadCarrera = ".$this->escStr($oUsuario->getUniversidadCarrera()).", ".
                   " carreraFinalizada = ".$carreraFinalizada.", ".
                   " activo = ".$activo.", ".
                   " urlTokenKey = ".$this->escStr($oUsuario->getUrlTokenKey()).", ".
                   " contrasenia = ".$db->escape($oUsuario->getContrasenia(),true)." ".
                   " WHERE id = ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;
            
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oUsuario)
    {
        try{
            if($oUsuario->getId() != null){
                return $this->actualizar($oUsuario);
            }else{
                return $this->insertar($oUsuario);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertar($oUsuario)
    {
        try{
            $db = $this->conn;

            if($oUsuario->getCiudad() != null){
                $ciudadId = $oUsuario->getCiudad()->getId();
            }else{
                $ciudadId = 'null';
            }

            if($oUsuario->getInstitucion() != null){
                $institucionId = $oUsuario->getInstitucion()->getId();
            }else{
                $institucionId = 'null';
            }

            if($oUsuario->getEspecialidad() != null){
                $especialidadId = $oUsuario->getEspecialidad()->getId();
            }else{
                $especialidadId = 'null';
            }
            
            $filtro["u.nombre"] = $oUsuario->getNombreUsuario();
            if($this->existe($filtro)){
                return 10;
            }

            $filtro["p.numeroDocumento"] = $oUsuario->getNumeroDocumento();
            if($this->existe($filtro)){
                return 11;
            }

            $filtro["p.email"]= $oUsuario->getEmail();
            if($this->existe($filtro)){
                return 12;
            }

            $carreraFinalizada = $oUsuario->isCarreraFinalizada() ? "1" : "0";

            $sUrlTokenKey = md5($time.$oUsuario->getNombre().$oUsuario->getApellido());
            
            $db->begin_transaction();
            
            $sSQL = " insert into personas ".
            " set nombre =".$db->escape($oUsuario->getNombre(),true).", " .
            " apellido =".$db->escape($oUsuario->getApellido(),true).", " .
            " documento_tipos_id =".$db->escape($oUsuario->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
            " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),false,MYSQL_TYPE_INT).", " .
            " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
            " fechaNacimiento = '".$oUsuario->getFechaNacimiento()."',".
            " email =".$db->escape($oUsuario->getEmail(),true).", " .
            " telefono =".$db->escape($oUsuario->getTelefono(),true).", " .
            " celular =".$db->escape($oUsuario->getCelular(),true).", " .
            " fax =".$db->escape($oUsuario->getFax(),true).", " .
            " domicilio =".$db->escape($oUsuario->getDomicilio(),true).", " .//revisar esto
            " instituciones_id = ".$institucionId.", ".
            " ciudades_id = ".$ciudadId.", ".
            " ciudadOrigen =".$db->escape($oUsuario->getCiudadOrigen(),true).", " .
            " codigoPostal =".$db->escape($oUsuario->getCodigoPostal(),true).", " .
            " empresa =".$db->escape($oUsuario->getEmpresa(),true).", " .
            " universidad =".$db->escape($oUsuario->getUniversidad(),true).", " .
            " secundaria =".$db->escape($oUsuario->getSecundaria(),true)." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            if($oUsuario->getEspecialidad()!= null){
                $iEspecialidadId = $oUsuario->getEspecialidad()->getId();
            }else{
                $iEspecialidadId = null;
            }

            $sSQL = " insert into usuarios set ".
                    " id = ".$db->escape($iLastId,false,MYSQL_TYPE_INT).", " .
                    " sitioWeb = ".$db->escape($oUsuario->getSitioWeb(),true).", " .
                    " especialidades_id = ".$especialidadId.", ".
                    " perfiles_id = ".self::PERFIL_INTEGRANTE_INACTIVO.", ".
                    " cargoInstitucion = ".$this->escStr($oUsuario->getCargoInstitucion()).", ".
                    " biografia = ".$this->escStr($oUsuario->getBiografia()).", ".
                    " universidadCarrera = ".$this->escStr($oUsuario->getUniversidadCarrera()).", ".
                    " carreraFinalizada = ".$carreraFinalizada.", ".
                    " urlTokenKey = '".$sUrlTokenKey."', ".
                    " nombre = ".$db->escape($oUsuario->getNombreUsuario(),true).",".
                    " contrasenia = ".$db->escape($oUsuario->getContrasenia(),true)." ";

            $db->execSQL($sSQL);   

            $db->execSQL("insert into privacidad set usuarios_id = ".$db->escape($iLastId,false,MYSQL_TYPE_INT));
            
            $db->commit();

            $oUsuario->setId($iLastId);
            $oUsuario->setUrlTokenKey($sUrlTokenKey);
                        
            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }

    public function borrar($oUsuario) {
        try{
            $db = $this->conn;
            $db->execSQL("delete from personas where id=".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT));
            $db->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
	
	public function validarUrlTmp($token){
		 try{
            $db = $this->conn;
            $sSQL = "SELECT 
            		  ui.`usuarios_id`,
					  ui.`invitados_id`,
					  ui.`relacion`,
					  ui.`fecha`,
					  ui.`estado`,
					  ui.`token`,
					  p.`email`,
					  p.`nombre`,
					  p.`apellido`
					FROM 
					  `usuario_x_invitado` ui
					JOIN
						usuarios u ON u.id = ui.usuarios_id
					JOIN
						personas p ON p.id = ui.invitados_id
					WHERE DATE_SUB(ui.fecha,INTERVAL 5 DAY) <= now() 
						 AND ui.token = ".$db->escape($token,true)." AND ui.estado = 'pendiente' ";
            return $db->getDBObject($sSQL);
	 	}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}
	
	public function guardarNuevaContrasenia($iId){
		try{
			$db = $this->conn;
			$time 	= time();
			$token 	= md5($time);
			$pass	= substr( md5(microtime()), 1, 8);
			$oPass = new stdClass();
			$oPass->nuevaContrasenia = $pass;
			$oPass->token = $token;
			$sSQL = " insert into usuarios_datos_temp set ".
                    " id=".$db->escape($iId,false,MYSQL_TYPE_INT).", " .
                    " contraseniaNueva=".$db->escape(md5($pass),true).", ".
                    " token=".$db->escape($token,true)." ";

			 $db->execSQL($sSQL);
			 $db->commit();
			 return $oPass;
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}
	
	public function validarConfirmacionContrasenia($token){
		 try{
            $db = $this->conn;
            $sSQL = "SELECT 
            			udt.id as iId, 
            			udt.contraseniaNueva as sContraseniaNueva
					FROM 
					  `usuarios_datos_temp` udt
					JOIN
						usuarios u ON udt.id = u.id
					JOIN
						personas p ON p.id = udt.id
					WHERE DATE_SUB(udt.fecha,INTERVAL 5 DAY) <= now() 
						 AND udt.token = ".$db->escape($token,true)." ";
            $objUsuario = $db->getDBObject($sSQL);
            if($objUsuario){
            	$filtro   = array('p.id' => $objUsuario->iId);
                $aUsuario = $this->obtener($filtro);
                if(null === $aUsuario){return false;}
                $oUsuario = $aUsuario[0];
                $oUsuario->setContrasenia( $objUsuario->sContraseniaNueva );
                return $this->guardar($oUsuario);
            }else{
            	return false;
            }
	 	}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}

    public function obtenerPrivacidadCampo($filtro, $nombreCampo)
    {
    	try{
            $db = $this->conn;
            $sSQL = "SELECT ".$nombreCampo." as priv
                     FROM
                        privacidad p
                     WHERE ".$this->crearCondicionSimple($filtro);
            
            $db->query($sSQL);            
            return $db->oNextRecord()->priv;

    	}catch(Exception $e){
            return "";
            throw new Exception($e->getMessage(), 0);
        }        
    }

    /**
     * Devuelve la privacidad de todos los campos para un usuario.
     * crea un array ['nombreCampo'] = "publicoPorEj"
     */
    public function obtenerPrivacidad($filtro)
    {
    	try{
            $db = $this->conn;
            $sSQL = "SELECT
                        email, telefono, celular, fax, curriculum
                     FROM
                        privacidad p
                     WHERE ".$this->crearCondicionSimple($filtro);

            $db->query($sSQL);
            $record = $db->oNextRecord();
            
            if(null !== $record){
                return  array('email' => $record->email,
                              'telefono' => $record->telefono,
                              'celular' => $record->celular,
                              'fax' => $record->fax,
                              'curriculum' => $record->curriculum);
            }else{
                return null;
            }
            
    	}catch(Exception $e){
            return "";
            throw new Exception($e->getMessage(), 0);
        }            
    }

    public function updatePrivacidadCampo($filtro, $nombreCampo, $valorPrivacidad)
    {
        $db = $this->conn;
        $sSQL = "UPDATE privacidad p SET ".$nombreCampo." = ".$this->escStr($valorPrivacidad)."
                 WHERE ".$this->crearCondicionSimple($filtro);
        
        $db->execSQL($sSQL);
        $db->commit();
    }

    /**
     * Busca en la DB si existe un mail que este asociado a algun usuario.
     * Si se le pasa userId exceptua el valor de ese registro
     */
    public function existeMailDb($email, $userId)
    {
    	try{
            $db = $this->conn;

            $email = $this->escStr($email);
            
            if(!empty($userId)){
                //ojo con esta verga que si le llega un null lo convierte en 'NULL' (un string)
                $userId = $this->escInt($userId);
            }

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        personas p
                    JOIN
                    	usuarios u ON p.id = u.id
                    WHERE email = ".$email;

            if(!empty($userId)){
                $sSQL .= " and u.id <> ".$userId;
            }

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){
            	return false;
            }
            
            return true;
    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return false;
        }        
    }

    public function existeNombreUsuarioDb($nombreUsuario)
    {
    	try{
            $db = $this->conn;

            $nombreUsuario = $this->escStr($nombreUsuario);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        usuarios 
                    WHERE nombre = ".$nombreUsuario;

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){
            	return false;
            }
            return true;
    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return false;
        }        
    }
    
    public function existeDocumentoUsuario($numeroDocumento)
    {
    	try{
            $db = $this->conn;

            $numeroDocumento = $this->escStr($numeroDocumento);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        personas p
                    JOIN usuarios u ON p.id = u.id 
                    WHERE numeroDocumento = ".$numeroDocumento;

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){
            	return false;
            }
            return true;
    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return false;
        }        
    }
}