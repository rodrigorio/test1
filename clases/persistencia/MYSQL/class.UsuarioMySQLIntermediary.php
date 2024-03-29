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

            $sSQL = $this->generateSelectUsuarios(). "

                        e.id as iEspecialidadId,
                        e.nombre as sEspecialidadNombre,
                        e.descripcion as sEspecialidadDescripcion

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
                $WHERE[] = $this->crearFiltroTexto($db->decryptData('p.nombre',true), $filtro['p.nombre'] );
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
            if(isset($filtro['p.email']) && $filtro['p.email'] != ""){
                $WHERE[] = $this->crearFiltroSimple($db->decryptData('p.email',true), $filtro['p.email']);
            }
            if(isset($filtro['u.nombre']) && $filtro['u.nombre']!=""){
                $WHERE[] = $this->crearFiltroTexto($db->decryptData('u.nombre',true), $filtro['u.nombre']);
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

            $sSQL =  $this->generateSelectUsuarios(). "

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
                $WHERE[] = $this->crearFiltroTexto($db->decryptData('p.apellido',true),  $filtro['p.apellido'] );
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

            $sSQL =  "SELECT SQL_CALC_FOUND_ROWS
                       DISTINCT p.id as iId,
                        ".$db->decryptData( 'p.nombre')." as sNombre,
                        ".$db->decryptData( 'p.apellido')." as sApellido,
                        p.sexo as sSexo, p.fechaNacimiento as dFechaNacimiento,
                        ".$db->decryptData( 'p.email ')." as sEmail,
                        ".$db->decryptData( 'p.telefono')." as sTelefono,
                        ".$db->decryptData( 'p.celular')." as sCelular,
                        ".$db->decryptData( 'p.fax')." as sFax,
                        ".$db->decryptData( 'p.domicilio')." as sDomicilio,
                        ".$db->decryptData( 'p.ciudadOrigen')." as sCiudadOrigen,
                        p.ciudades_id as iCiudadId, p.instituciones_id as iInstitucionId,
                        ".$db->decryptData( 'p.codigoPostal')." as sCodigoPostal,
                        ".$db->decryptData( 'p.empresa')." as sEmpresa,
                        ".$db->decryptData( 'p.universidad')." as sUniversidad,
                        ".$db->decryptData( 'p.secundaria')." as sSecundaria,
                        p.documento_tipos_id as iTipoDocumentoId,
                        p.numeroDocumento as sNumeroDocumento,

                        ".$db->decryptData( 'u.sitioWeb' )." as sSitioWeb,
                        ".$db->decryptData( 'u.nombre' )." as sNombreUsuario, u.activo as bActivo,
                        u.fechaAlta as dFechaAlta, u.contrasenia as sContrasenia,
                        u.invitacionesDisponibles as iInvitacionesDisponibles,
                        u.cargoInstitucion as sCargoInstitucion,
                        ".$db->decryptData( 'u.biografia' )." as sBiografia,
                        u.universidadCarrera as sUniveridadCarrera, u.carreraFinalizada as bCarreraFinalizada,
                        u.urlTokenKey as sUrlTokenKey,

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
                    WHERE ".$this->crearCondicionSimple($filtro);

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

    public function registrarInvitado($oObj)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $sSQL = " UPDATE personas SET ".
                    " nombre = ".$db->encryptData($this->escStr($oObj->sNombre)).", " .
                    " apellido = ".$db->encryptData($this->escStr($oObj->sApellido)).", " .
                    " documento_tipos_id = ".$this->escInt($oObj->iTipoDocumentoId).", ".
                    " numeroDocumento = ".$this->escStr($oObj->sNumeroDocumento).", " .
                    " sexo = ".$this->escStr($oObj->sSexo).", " .
                    " fechaNacimiento = ".$this->escDate($oObj->dFechaNacimiento)." ".
                    " WHERE id = ".$this->escInt($oObj->iInvitadoId)." ";

            $db->execSQL($sSQL);

            $time = time();
            $sUrlTokenKey = md5($time.$oObj->sNombre.$oObj->sApellido);

            $sSQL =" INSERT INTO usuarios SET ".
                   " id = ".$this->escInt($oObj->iInvitadoId).", ".
                   " perfiles_id = ".self::PERFIL_INTEGRANTE_INACTIVO.", ".
                   " nombre = ".$db->encryptData($this->escStr($oObj->sNombreUsuario)).", ".
                   " urlTokenKey = '".$sUrlTokenKey."', ".
                   " contrasenia = ".$this->escStr($oObj->sContrasenia)." ";

            $db->execSQL($sSQL);

            //seteo la invitacion como aceptada
            $sSQL =" UPDATE usuario_x_invitado ".
                   " SET estado = 'aceptada' ".
                   " WHERE usuarios_id = ".$this->escInt($oObj->iUsuarioId)." ".
                   " AND invitados_id = ".$this->escInt($oObj->iInvitadoId)." ";

            $db->execSQL($sSQL);

            //borro las otras invitaciones pendientes a la misma persona si es que las hay
            $sSQL = "DELETE FROM usuario_x_invitado
                     WHERE invitados_id = ".$this->escInt($oObj->iInvitadoId)."
                     AND estado = 'pendiente' ";
            $db->execSQL($sSQL);

            //inicio privacidad con valores por defecto
            $db->execSQL("INSERT INTO privacidad SET usuarios_id = ".$this->escInt($oObj->iInvitadoId)." ");

            //asocio los parametros de usuario con los valores por defecto.
            $sSQL = "INSERT INTO parametro_x_usuario(parametros_id, usuarios_id, valor)
                     SELECT parametros_id, ".$this->escInt($oObj->iInvitadoId).", valorDefecto
                     FROM parametros_usuario ";

            $db->execSQL($sSQL);

            //inserto configuracion informes con valores en nulo. 1 x usuario
            $db->execSQL("INSERT INTO configuraciones_informes SET usuarios_id = ".$this->escInt($oObj->iInvitadoId)." ");

            $db->commit();

            //si todo salio bien creo el nuevo usuario y lo devuelvo
            $oUsuario = new stdClass();
            $oUsuario->iId              = $oObj->iInvitadoId;
            $oUsuario->sNombre          = $oObj->sNombre;
            $oUsuario->sApellido        = $oObj->sApellido;
            $oUsuario->iTipoDocumentoId = $oObj->iTipoDocumentoId;
            $oUsuario->sNumeroDocumento = $oObj->sNumeroDocumento;
            $oUsuario->sSexo            = $oObj->sSexo;
            $oUsuario->dFechaNacimiento = $oObj->dFechaNacimiento;
            $oUsuario->sEmail           = $oObj->sEmail;
            $oUsuario->oCiudad          = null;
            $oUsuario->oInstitucion     = null;
            $oUsuario->oEspecialidad    = null;
            $oUsuario->oFotoPerfil      = null;
            $oUsuario->oCurriculumVitae = null;
            $oUsuario->sNombreUsuario   = $oObj->sNombreUsuario;
            $oUsuario->sContrasenia     = $oObj->sContrasenia;
            $oUsuario->dFechaAlta       = date("Y/m/d");
            $oUsuario->sUrlTokenKey     = $sUrlTokenKey;
            $oUsuario->bActivo = true;
            $oUsuario->iInvitacionesDisponibles = 5;

            return Factory::getUsuarioInstance($oUsuario);

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
                $ciudadId = $this->escInt($oUsuario->getCiudad()->getId());
            }else{
                $ciudadId = null;
            }

            if(null != $oUsuario->getInstitucion()){
                $institucionId = $this->escInt($oUsuario->getInstitucion()->getId());
            }else{
                $institucionId = null;
            }

            if(null != $oUsuario->getEspecialidad()){
                $especialidadId = $this->escInt($oUsuario->getEspecialidad()->getId());
            }else{
                $especialidadId = null;
            }

            $carreraFinalizada = $oUsuario->isCarreraFinalizada() ? "1" : "0";

            $activo = $oUsuario->isActivo()?"1":"0";

            $db->begin_transaction();
            $sSQL = " update personas " .
                    " set nombre =".$db->encryptData($db->escape($oUsuario->getNombre(),true)).", " .
                    " apellido =".$db->encryptData($db->escape($oUsuario->getApellido(),true)).", " .
                    " documento_tipos_id =".$db->escape($oUsuario->getTipoDocumento(), false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),true).", " .
                    " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
                    " fechaNacimiento = ".$this->escDate($oUsuario->getFechaNacimiento()).", ".
                    " email =".$db->encryptData($db->escape($oUsuario->getEmail(),true)).", " .
                    " telefono =".$db->encryptData($db->escape($oUsuario->getTelefono(),true)).", " .
                    " celular =".$db->encryptData($db->escape($oUsuario->getCelular(),true)).", " .
                    " fax =".$db->encryptData($db->escape($oUsuario->getFax(),true)).", " .
                    " domicilio =".$db->encryptData($db->escape($oUsuario->getDomicilio(),true)).", " .
                    " instituciones_id = ".$this->escInt($institucionId).", ".
                    " ciudades_id = ".$this->escInt($ciudadId).", ".
                    " ciudadOrigen =".$db->encryptData($db->escape($oUsuario->getCiudadOrigen(),true)).", " .
                    " codigoPostal =".$db->encryptData($db->escape($oUsuario->getCodigoPostal(),true)).", " .
                    " empresa =".$db->encryptData($db->escape($oUsuario->getEmpresa(),true)).", " .
                    " universidad =".$db->encryptData($db->escape($oUsuario->getUniversidad(),true)).", " .
                    " secundaria =".$db->encryptData($db->escape($oUsuario->getSecundaria(),true))." ".
                    " WHERE id = ".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);

            $sSQL =" update usuarios ".
                   " set sitioWeb = ".$db->encryptData($db->escape($oUsuario->getSitioWeb(),true)).", " .
                   " especialidades_id = ".$this->escInt($especialidadId).", ".
                   " cargoInstitucion = ".$this->escStr($oUsuario->getCargoInstitucion()).", ".
                   " biografia = ".$db->encryptData($this->escStr($oUsuario->getBiografia())).", ".
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
                $ciudadId = $this->escInt($oUsuario->getCiudad()->getId());
            }else{
                $ciudadId = null;
            }

            if($oUsuario->getInstitucion() != null){
                $institucionId = $this->escInt($oUsuario->getInstitucion()->getId());
            }else{
                $institucionId = null;
            }

            if($oUsuario->getEspecialidad() != null){
                $especialidadId = $this->escInt($oUsuario->getEspecialidad()->getId());
            }else{
                $especialidadId = null;
            }

            $carreraFinalizada = $oUsuario->isCarreraFinalizada() ? "1" : "0";

            $time = time();
            $sUrlTokenKey = md5($time.$oUsuario->getNombre().$oUsuario->getApellido());

            $db->begin_transaction();


            $sSQL = " insert into personas ".
            " set nombre =".$db->encryptData($db->escape($oUsuario->getNombre(),true)).", " .
            " apellido =".$db->encryptData($db->escape($oUsuario->getApellido(),true)).", " .
            " documento_tipos_id =".$db->escape($oUsuario->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
            " numeroDocumento =".$db->escape($oUsuario->getNumeroDocumento(),false,MYSQL_TYPE_INT).", " .
            " sexo =".$db->escape($oUsuario->getSexo(),true).", " .
            " fechaNacimiento = ".$this->escDate($oUsuario->getFechaNacimiento()).", ".
            " email =".$db->encryptData($db->escape($oUsuario->getEmail(),true)).", " .
            " telefono =".$db->encryptData($db->escape($oUsuario->getTelefono(),true)).", " .
            " celular =".$db->encryptData($db->escape($oUsuario->getCelular(),true)).", " .
            " fax =".$db->encryptData($db->escape($oUsuario->getFax(),true)).", " .
            " domicilio =".$db->encryptData($db->escape($oUsuario->getDomicilio(),true)).", " .
            " instituciones_id = ".$this->escInt($institucionId).", ".
            " ciudades_id = ".$this->escInt($ciudadId).", ".
            " ciudadOrigen =".$db->encryptData($db->escape($oUsuario->getCiudadOrigen(),true)).", " .
            " codigoPostal =".$db->escape($oUsuario->getCodigoPostal(),true).", " .
            " empresa =".$db->encryptData($db->escape($oUsuario->getEmpresa(),true)).", " .
            " universidad =".$db->encryptData($db->escape($oUsuario->getUniversidad(),true)).", " .
            " secundaria =".$db->encryptData($db->escape($oUsuario->getSecundaria(),true))." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            if($oUsuario->getEspecialidad()!= null){
                $iEspecialidadId = $oUsuario->getEspecialidad()->getId();
            }else{
                $iEspecialidadId = null;
            }

            $sSQL = " insert into usuarios set ".
                    " id = ".$db->escape($iLastId,false,MYSQL_TYPE_INT).", " .
                    " sitioWeb = ".$db->encryptData($db->escape($oUsuario->getSitioWeb(),true)).", " .
                    " especialidades_id = ".$this->escInt($especialidadId).", ".
                    " perfiles_id = ".self::PERFIL_INTEGRANTE_INACTIVO.", ".
                    " cargoInstitucion = ".$this->escStr($oUsuario->getCargoInstitucion()).", ".
                    " biografia = ".$db->encryptData($this->escStr($oUsuario->getBiografia())).", ".
                    " universidadCarrera = ".$this->escStr($oUsuario->getUniversidadCarrera()).", ".
                    " carreraFinalizada = ".$carreraFinalizada.", ".
                    " urlTokenKey = '".$sUrlTokenKey."', ".
                    " nombre = ".$db->encryptData($db->escape($oUsuario->getNombreUsuario(),true)).",".
                    " contrasenia = ".$db->escape($oUsuario->getContrasenia(),true)." ";

            $db->execSQL($sSQL);

            $db->execSQL("insert into privacidad set usuarios_id = ".$db->escape($iLastId,false,MYSQL_TYPE_INT));

            //asocio los parametros de usuario con los valores por defecto.
            $sSQL = "INSERT INTO parametro_x_usuario(parametros_id, usuarios_id, valor)
                     SELECT parametros_id, '".$iLastId."', valorDefecto
                     FROM parametros_usuario ";

            $db->execSQL($sSQL);

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

    public function borrar($oUsuario)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from personas where id=".$db->escape($oUsuario->getId(),false,MYSQL_TYPE_INT));
            $db->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
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
                    WHERE email = ".$db->encryptData($email);

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
                    WHERE nombre = ".$db->encryptData($nombreUsuario);

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

    public function existePasswordTemporal($filtro)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        usuario_passwords_temporales upt ";

            $WHERE = array();

            if(isset($filtro['upt.usuarios_id']) && $filtro['upt.usuarios_id'] != ""){
                $WHERE[] = $this->crearFiltroSimple('upt.usuarios_id', $filtro['upt.usuarios_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['upt.token']) && $filtro['upt.token'] != ""){
                $WHERE[] = $this->crearFiltroSimple('upt.token', $filtro['upt.token']);
            }
            if(isset($filtro['expiracion'])){
                $WHERE[] = " TO_DAYS(NOW()) - TO_DAYS(upt.fecha) <= ".$filtro['expiracion']." ";
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

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

    public function borrarPasswordTemporalExpiradaUsuario($iUsuarioId, $iDiasExpiracion)
    {
        try{
            $db = $this->conn;

            $db->begin_transaction();

            //borro las invitaciones expiradas
            $sSQL = "DELETE FROM usuario_passwords_temporales
                     WHERE usuarios_id = ".$iUsuarioId."
                     AND TO_DAYS(NOW()) - TO_DAYS(fecha) >= ".$iDiasExpiracion." ";

            $db->execSQL($sSQL);

            $db->commit();
            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertarPasswordTemporal($oPasswordTemporal, $iUsuarioId)
    {
        try{
            $db = $this->conn;

            $sSQL = " INSERT INTO usuario_passwords_temporales SET ".
                    " usuarios_id = ".$this->escInt($iUsuarioId).", " .
                    " contraseniaNueva = ".$this->escStr($oPasswordTemporal->getPasswordMd5()).", ".
                    " token = ".$this->escStr($oPasswordTemporal->getToken()).", ".
                    " email = ".$this->escStr($oPasswordTemporal->getEmail())." ";

            $db->execSQL($sSQL);
            $db->commit();

            $oPasswordTemporal->setFecha(date("Y/m/d"));

            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }

    public function confirmarPasswordTemporal($sToken, $iDiasExpiracion)
    {
        try{
            $sToken = $this->escStr($sToken);

            $db = $this->conn;
            $db->begin_transaction();

            //asocio el nuevo password al usuario
            $sSQL = "UPDATE usuarios SET contrasenia = (
                        SELECT contraseniaNueva
                        FROM usuario_passwords_temporales upt
                        WHERE upt.token = ".$sToken." )
                     WHERE id = (
                        SELECT usuarios_id
                        FROM usuario_passwords_temporales upt
                        WHERE upt.token = ".$sToken." )";

            $db->execSQL($sSQL);

            //borro el registro temporal
            $sSQL = "DELETE FROM usuario_passwords_temporales WHERE token = ".$sToken;
            $db->execSQL($sSQL);

            $db->commit();
            return true;

        }catch(Exception $e){
            throw $e;
        }
    }

    public function actualizarCampoArray($objects, $cambios){}

    private function generateSelectUsuarios(){
    	$db = $this->conn;
      	$sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        p.id as iId,
                        ".$db->decryptData( 'p.nombre')." as sNombre,
                        ".$db->decryptData( 'p.apellido')." as sApellido,
                        p.sexo as sSexo, p.fechaNacimiento as dFechaNacimiento,
                        ".$db->decryptData( 'p.email ')." as sEmail,
                        ".$db->decryptData( 'p.telefono')." as sTelefono,
                        ".$db->decryptData( 'p.celular')." as sCelular,
                        ".$db->decryptData( 'p.fax')." as sFax,
                        ".$db->decryptData( 'p.domicilio')." as sDomicilio,
                        ".$db->decryptData( 'p.ciudadOrigen')." as sCiudadOrigen,
                        p.ciudades_id as iCiudadId, p.instituciones_id as iInstitucionId,
                        ".$db->decryptData( 'p.codigoPostal')." as sCodigoPostal,
                        ".$db->decryptData( 'p.empresa')." as sEmpresa,
                        ".$db->decryptData( 'p.universidad')." as sUniversidad,
                        ".$db->decryptData( 'p.secundaria')." as sSecundaria,
                        p.documento_tipos_id as iTipoDocumentoId,
                        p.numeroDocumento as sNumeroDocumento,

                        ".$db->decryptData( 'u.sitioWeb' )." as sSitioWeb,
                        ".$db->decryptData( 'u.nombre' )." as sNombreUsuario, u.activo as bActivo,
                        u.fechaAlta as dFechaAlta, u.contrasenia as sContrasenia,
                        u.invitacionesDisponibles as iInvitacionesDisponibles,
                        u.cargoInstitucion as sCargoInstitucion,
                        ".$db->decryptData( 'u.biografia' )." as sBiografia,
                        u.universidadCarrera as sUniveridadCarrera, u.carreraFinalizada as bCarreraFinalizada,
                        u.urlTokenKey as sUrlTokenKey,

                        a.id as iCvId, a.nombre as sCvNombre,
                        a.nombreServidor as sCvNombreServidor, a.descripcion as sCvDescripcion,
                        a.tipoMime as sCvTipoMime, a.tamanio as iCvTamanio,
                        a.fechaAlta as sCvFechaAlta, a.orden as iCvOrden,
                        a.titulo as sCvTitulo, a.tipo as sCvTipo,

                        f.id as iFotoId, f.nombreBigSize as sFotoNombreBigSize,
                        f.nombreMediumSize as sFotoNombreMediumSize, f.nombreSmallSize as sFotoNombreSmallSize,
                        f.orden as iFotoOrden, f.titulo as sFotoTitulo,
                        f.descripcion as sFotoDescripcion, f.tipo as sFotoTipo,
               ";
      	return $sSQL;
    }
}
