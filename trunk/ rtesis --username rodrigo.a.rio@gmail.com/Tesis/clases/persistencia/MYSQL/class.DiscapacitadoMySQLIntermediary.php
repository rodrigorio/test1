<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classpersonaMySQLIntermediary
 *
 * @author Andres
 */
class DiscapacitadoMySQLIntermediary extends DiscapacitadoIntermediary
{
	private static $instance = null;

	protected function __construct( $conn) {
		parent::__construct($conn);
	}

	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return InstitucionMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
        
	public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = $this->conn;
            
            $sSQL = "SELECT
                        p.id as iId, 
                        p.nombre as sNombre,
                        p.apellido as sApellido,
                        p.documento_tipos_id as iTipoDocumentoId,
                        p.numeroDocumento as sNumeroDocumento,
                        p.sexo as sSexo,
                        p.fechaNacimiento as dFechaNacimiento,
                        p.email as sEmail, 
                        p.telefono as sTelefono,
                        p.celular as sCelular,
                        p.fax as sFax, 
                        p.domicilio as sDomicilio,
                        p.instituciones_id as iInstitucionId,
                        p.ciudades_id as iCiudadId,
                        p.ciudadOrigen as sCiudadOrigen,
                        p.codigoPostal as sCodigoPostal,
                        p.empresa as sEmpresa,
                        p.universidad as sUniversidad, 
                        p.secundaria as sSecundaria,
                        
                        d.nombreApellidoPadre as sNombreApellidoPadre,
                        d.nombreApellidoMadre as sNombreApellidoMadre,
                        d.fechaNacimientoPadre as dFechaNacimientoPadre,
                        d.fechaNacimientoMadre as dFechaNacimientoMadre,
                        d.ocupacionPadre as sOcupacionPadre, d.ocupacionMadre as sOcupacionMadre,
                        d.nombreHermanos as sNombreHermanos,
                        d.usuarios_id as iUsuarioId,

                        f.id as iFotoId, f.nombreBigSize as sFotoNombreBigSize,
                        f.nombreMediumSize as sFotoNombreMediumSize, f.nombreSmallSize as sFotoNombreSmallSize,
                        f.orden as iFotoOrden, f.titulo as sFotoTitulo,
                        f.descripcion as sFotoDescripcion, f.tipo as sFotoTipo
                    FROM
                        personas p 
                    JOIN discapacitados d ON p.id = d.id 
                    LEFT JOIN fotos f ON f.personas_id = d.id ";

            $WHERE = array();
            if(isset($filtro['p.id']) && $filtro['p.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.id', $filtro['p.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['d.id']) && $filtro['d.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.id', $filtro['p.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['p.nombre']) && $filtro['p.nombre']!=""){
                $WHERE[] = $this->crearFiltroTexto('p.nombre', $filtro['p.nombre']);
            }
            if(isset($filtro['p.numeroDocumento']) && $filtro['p.numeroDocumento']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.numeroDocumento', $filtro['p.numeroDocumento'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['p.documento_tipos_id']) && $filtro['p.documento_tipos_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.documento_tipos_id', $filtro['p.documento_tipos_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['p.email']) && $filtro['p.email']!=""){
                $WHERE[] = $this->crearFiltroTexto('p.email', $filtro['p.email']);
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

            $aDiscapacitado = array();
            while($oObj = $db->oNextRecord()){
                $oDiscapacitado = new stdClass();
                $oDiscapacitado->iId = $oObj->iId;
                $oDiscapacitado->sNombre = $oObj->sNombre;
                $oDiscapacitado->sApellido = $oObj->sApellido;
                $oDiscapacitado->sNumeroDocumento = $oObj->sNumeroDocumento;
                $oDiscapacitado->iTipoDocumentoId = $oObj->iTipoDocumentoId;
                $oDiscapacitado->sSexo = $oObj->sSexo;
                $oDiscapacitado->dFechaNacimiento = $oObj->dFechaNacimiento;
                $oDiscapacitado->sEmail	= $oObj->sEmail;
                $oDiscapacitado->sTelefono = $oObj->sTelefono;
                $oDiscapacitado->sCelular = $oObj->sCelular;
                $oDiscapacitado->sFax = $oObj->sFax;
                $oDiscapacitado->sDomicilio = $oObj->sDomicilio;
                $oDiscapacitado->iCiudadId = $oObj->iCiudadId; //para sacar objeto ciudad por demanda
                $oDiscapacitado->iInstitucionId = $oObj->iInstitucionId; //lo mismo xq es un obj pesado
                $oDiscapacitado->oCiudad = null;
                $oDiscapacitado->oInstitucion = null;
                $oDiscapacitado->oFotoPerfil = null;
                $oDiscapacitado->sCiudadOrigen = $oObj->sCiudadOrigen;
                $oDiscapacitado->sCodigoPostal = $oObj->sCodigoPostal;
                $oDiscapacitado->sEmpresa = $oObj->sEmpresa;
                $oDiscapacitado->sUniversidad = $oObj->sUniversidad;
                $oDiscapacitado->sSecundaria = $oObj->sSecundaria;
                $oDiscapacitado->sNombreApellidoPadre = $oObj->sNombreApellidoPadre;
                $oDiscapacitado->sNombreApellidoMadre = $oObj->sNombreApellidoMadre;
                $oDiscapacitado->dFechaNacimientoPadre = $oObj->dFechaNacimientoPadre;
                $oDiscapacitado->dFechaNacimientoMadre = $oObj->dFechaNacimientoMadre;
                $oDiscapacitado->sOcupacionPadre = $oObj->sOcupacionPadre;
                $oDiscapacitado->sOcupacionMadre = $oObj->sOcupacionMadre;
                $oDiscapacitado->sNombreHermanos = $oObj->sNombreHermanos;
                $oDiscapacitado->iUsuarioId = $oObj->iUsuarioId;

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
                    $oDiscapacitado->oFotoPerfil = Factory::getFotoInstance($fotoPerfil);
                }

                //creo el discapacitado
                $oDiscapacitado = Factory::getDiscapacitadoInstance($oDiscapacitado);
                $aDiscapacitado[] = $oDiscapacitado;
            }

            return $aDiscapacitado;

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
                    	discapacitados d ON p.id = d.id
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

    public function guardar($oDiscapacitado)
    {
        try{
            if($oDiscapacitado->getId() != null){
                return $this->actualizar($oDiscapacitado);
            }else{
                return $this->insertar($oDiscapacitado);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oDiscapacitado)
    {
        try{
            $db = $this->conn;

            if($oDiscapacitado->getCiudad() != null){
                $ciudadId = $oDiscapacitado->getCiudad()->getId();
            }else{
                $ciudadId = null;
            }

            if($oDiscapacitado->getInstitucion() != null){
                $institucionId = $oDiscapacitado->getInstitucion()->getId();
            }else{
                $institucionId = null;
            }

            if($oDiscapacitado->getUsuario() != null){
                $iUsuarioId = $oDiscapacitado->getUsuario()->getId();
            }else{
                $iUsuarioId = null;
            }
			
            $db->begin_transaction();
            $sSQL = " update personas " .
                    " set nombre = ".$db->escape($oDiscapacitado->getNombre(),true).", " .
                    " apellido = ".$db->escape($oDiscapacitado->getApellido(),true).", " .
                    " documento_tipos_id = ".$db->escape($oDiscapacitado->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento = ".$db->escape($oDiscapacitado->getNumeroDocumento(),true).", " .
                    " sexo = ".$db->escape($oDiscapacitado->getSexo(),true).", " .
                    " telefono = ".$db->escape($oDiscapacitado->getTelefono(),true).", " .
                    " fechaNacimiento = '".$oDiscapacitado->getFechaNacimiento()."', ".
                    " domicilio =".$db->escape($oDiscapacitado->getDomicilio(),true).", " .
                    " instituciones_id = ".$institucionId.", ".
                    " ciudades_id = ".$ciudadId." ".
                    " WHERE id = '".$oDiscapacitado->getId()."' ";


             $db->execSQL($sSQL);

             $sSQL =" update discapacitados ".
                    " set nombreApellidoPadre=".$db->escape($oDiscapacitado->getNombreApellidoPadre(),true).", " .
                    " nombreApellidoMadre =".$db->escape($oDiscapacitado->getNombreApellidoMadre(),true).", ".
                    " fechaNacimientoPadre = '".$oDiscapacitado->getFechaNacimientoPadre()."', ".
                    " fechaNacimientoMadre = '".$oDiscapacitado->getFechaNacimientoMadre()."', ".
                    " ocupacionPadre =".$db->escape($oDiscapacitado->getOcupacionPadre(),true).", ".
                    " ocupacionMadre =".$db->escape($oDiscapacitado->getOcupacionMadre(),true).", ".
                    " nombreHermanos =".$db->escape($oDiscapacitado->getNombreHermanos(),true).", ".
                    " usuarios_id = ".$iUsuarioId." ".
                    " WHERE id = ".$db->escape($oDiscapacitado->getId(),false,MYSQL_TYPE_INT)." ";

             $db->execSQL($sSQL);
             $db->commit();

             return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertar($oDiscapacitado)
    {
        try{

            $db = $this->conn;
            if($oDiscapacitado->getCiudad() != null){
                $ciudadId = $oDiscapacitado->getCiudad()->getId();
            }else{
                $ciudadId = null;
            }

            if($oDiscapacitado->getInstitucion() != null){
                $institucionId = $oDiscapacitado->getInstitucion()->getId();
            }else{
                $institucionId = null;
            }
            
            if($oDiscapacitado->getUsuario() != null){
                $iUsuarioId = $oDiscapacitado->getUsuario()->getId();
            }else{
                $iUsuarioId = null;
            }
									
            $db->begin_transaction();

            $sSQL = " insert into personas ".
                    " set nombre = ".$db->escape($oDiscapacitado->getNombre(),true).", " .
                    " apellido = ".$db->escape($oDiscapacitado->getApellido(),true).", " .
                    " documento_tipos_id = ".$db->escape($oDiscapacitado->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento = ".$db->escape($oDiscapacitado->getNumeroDocumento(),false,MYSQL_TYPE_INT).", " .
                    " sexo = ".$db->escape($oDiscapacitado->getSexo(),true).", " .
                    " telefono = ".$db->escape($oDiscapacitado->getTelefono(),true).", " .
                    " fechaNacimiento = '".$oDiscapacitado->getFechaNacimiento()."', " .
                    " domicilio = ".$db->escape($oDiscapacitado->getDomicilio(),true).", " .//revisar esto
                    " instituciones_id = ".$db->escape($institucionId,true).", ".
                    " ciudades_id = ".$db->escape($ciudadId,true)." ";
            
            $db->execSQL($sSQL);
          
            $iLastId = $db->insert_id();
			
            $sSQL = " insert into discapacitados set ".
                    " id = ".$db->escape($iLastId,false).", " .
                    " nombreApellidoPadre=".$db->escape($oDiscapacitado->getNombreApellidoPadre(),true).", " .
                    " nombreApellidoMadre =".$db->escape($oDiscapacitado->getNombreApellidoMadre(),true).", ".
                    " fechaNacimientoPadre = '".$oDiscapacitado->getFechaNacimientoPadre()."', ".
                    " fechaNacimientoMadre = '".$oDiscapacitado->getFechaNacimientoMadre()."', ".
                    " ocupacionPadre =".$db->escape($oDiscapacitado->getOcupacionPadre(),true).", " .
                    " ocupacionMadre =".$db->escape($oDiscapacitado->getOcupacionMadre(),true).", " .
                    " nombreHermanos =".$db->escape($oDiscapacitado->getNombreHermanos(),true).", ".
                    " usuarios_id = ".$iUsuarioId." ";
					
            $db->execSQL($sSQL);
            $db->commit();

            $oDiscapacitado->setId($iLastId);            

            return true;

        }catch(Exception $e){
            
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }

    /**
     * Si o si se inserta no se puede actualizar una moderacion
     */
    public function guardarModeracion($oDiscapacitado, $cambioFoto = false){
        try{            
            return $this->insertarModeracion($oDiscapacitado, $cambioFoto);
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }        
    }

    public function insertarModeracion($oDiscapacitado, $cambioFoto)
    {
        try{

            $db = $this->conn;
            if($oDiscapacitado->getCiudad() != null){
                $ciudadId = $oDiscapacitado->getCiudad()->getId();
            }else{
                $ciudadId = null;
            }

            if($oDiscapacitado->getInstitucion() != null){
                $institucionId = $oDiscapacitado->getInstitucion()->getId();
            }else{
                $institucionId = null;
            }

            if($oDiscapacitado->getUsuario() != null){
                $iUsuarioId = $oDiscapacitado->getUsuario()->getId();
            }else{
                $iUsuarioId = null;
            }

            if($oDiscapacitado->getFotoPerfil() != null){
                $nombreBigSize = $oDiscapacitado->getFotoPerfil()->getNombreBigSize();
                $nombreMediumSize = $oDiscapacitado->getFotoPerfil()->getNombreMediumSize();
                $nombreSmallSize = $oDiscapacitado->getFotoPerfil()->getNombreSmallSize();
            }else{
                $nombreBigSize = "";
                $nombreMediumSize = "";
                $nombreSmallSize = "";
            }

            $cambioFoto = ($cambioFoto)?"1":"0";

            $db->begin_transaction();

            $sSQL = " insert into discapacitados_moderacion ".
                    " set id = '".$oDiscapacitado->getId()."', ".
                    " nombre =".$db->escape($oDiscapacitado->getNombre(),true).", " .
                    " apellido =".$db->escape($oDiscapacitado->getApellido(),true).", " .
                    " documento_tipos_id =".$db->escape($oDiscapacitado->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oDiscapacitado->getNumeroDocumento(),false,MYSQL_TYPE_INT).", " .
                    " sexo =".$db->escape($oDiscapacitado->getSexo(),true).", " .
                    " telefono =".$db->escape($oDiscapacitado->getTelefono(),true).", " .
                    " fechaNacimiento = '".$oDiscapacitado->getFechaNacimiento()."', " .
                    " domicilio =".$db->escape($oDiscapacitado->getDomicilio(),true).", " .//revisar esto
                    " instituciones_id =".$db->escape($institucionId,true).", ".
                    " ciudades_id =".$db->escape($ciudadId,true).", ".
                    " nombreApellidoPadre=".$db->escape($oDiscapacitado->getNombreApellidoPadre(),true).", " .
                    " nombreApellidoMadre =".$db->escape($oDiscapacitado->getNombreApellidoMadre(),true).", ".
                    " fechaNacimientoPadre = '".$oDiscapacitado->getFechaNacimientoPadre()."', ".
                    " fechaNacimientoMadre = '".$oDiscapacitado->getFechaNacimientoMadre()."', ".
                    " ocupacionPadre =".$db->escape($oDiscapacitado->getOcupacionPadre(),true).", " .
                    " ocupacionMadre =".$db->escape($oDiscapacitado->getOcupacionMadre(),true).", " .
                    " nombreHermanos =".$db->escape($oDiscapacitado->getNombreHermanos(),true).", ".
                    " usuarios_id = ".$iUsuarioId.", ".
                    " nombreBigSize = ".$this->escStr($nombreBigSize).", ".
                    " nombreMediumSize = ".$this->escStr($nombreMediumSize).", ".
                    " nombreSmallSize = ".$this->escStr($nombreSmallSize).", ".
                    " cambioFoto = ".$cambioFoto." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();

            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
            return false;
        }        
    }

    /**
     * Aplica los cambios guardados temporalmente en la tabla discapacitados_moderacion
     * a la tabla discapacitados real.
     *
     * Luego borra el registro de la tabla temporal
     */
    public function aplicarCambiosModeracion($oDiscapacitado)
    {
        try{
            $db = $this->conn;

            $iDiscapacitadoId = $oDiscapacitado->getId();
            $oFoto = $oDiscapacitado->getFotoPerfil();

            $db->begin_transaction();

            $sSQL = " UPDATE discapacitados_moderacion dm, personas p ".
                    " SET p.nombre = dm.nombre, " .
                    " p.apellido = dm.apellido, " .
                    " p.documento_tipos_id = dm.documento_tipos_id, ".
                    " p.numeroDocumento = dm.numeroDocumento, " .
                    " p.sexo = dm.sexo, " .
                    " p.telefono = dm.telefono, ".
                    " p.fechaNacimiento = dm.fechaNacimiento, " .
                    " p.domicilio = dm.domicilio, ".
                    " p.instituciones_id = dm.instituciones_id, ".
                    " p.ciudades_id = dm.ciudades_id ".
                    " WHERE dm.id = p.id ".
                    " AND dm.id = '".$iDiscapacitadoId."'";

            $db->execSQL($sSQL);

            //EL USUARIO DE LA TABLA TEMPORAL NO SE COPIA, ES SOLO PARA SABER QUIEN SOLICITO LA MODIFICACION
            $sSQL = " UPDATE discapacitados_moderacion dm, discapacitados d ".
                    " SET d.nombreApellidoPadre = dm.nombreApellidoPadre, " .
                    " d.nombreApellidoMadre = dm.nombreApellidoMadre, ".
                    " d.fechaNacimientoPadre = dm.fechaNacimientoPadre, ".
                    " d.fechaNacimientoMadre = dm.fechaNacimientoMadre, ".
                    " d.ocupacionPadre = dm.ocupacionPadre, " .
                    " d.ocupacionMadre = dm.ocupacionMadre, " .
                    " d.nombreHermanos = dm.nombreHermanos ".
                    " WHERE dm.id = d.id ".
                    " AND dm.id = '".$iDiscapacitadoId."'";
            
            $db->execSQL($sSQL);

            //porque puede ser que se agrega por primera vez una foto a la persona pero tiene que moderarse
            if(null !== $oFoto && null !== $oFoto->getId()){
                $sSQL = " UPDATE discapacitados_moderacion dm, fotos f ".
                        " SET f.nombreBigSize = dm.nombreBigSize, ".
                        " f.nombreMediumSize = dm.nombreMediumSize, ".
                        " f.nombreSmallSize = dm.nombreSmallSize, ".
                        " f.orden = 0, ".
                        " f.titulo = 'Foto de perfil', ".
                        " f.descripcion = '', ".
                        " f.tipo = 'perfil' ".
                        " WHERE dm.id = f.personas_id ".
                        " AND dm.id = '".$iDiscapacitadoId."'";                 
            }else{
                $sSQL = " INSERT INTO fotos ".
                        "   (personas_id, nombreBigSize, nombreMediumSize, nombreSmallSize, ".
                        "    orden, titulo, descripcion, tipo) ".
                        " SELECT ".
                        "    '".$iDiscapacitadoId."', dm.nombreBigSize, dm.nombreMediumSize, dm.nombreSmallSize, ".
                        "    0, 'Foto de perfil', '', 'perfil' ".                                  
                        " FROM discapacitados_moderacion dm WHERE dm.id = '".$iDiscapacitadoId."'";
            }
            
            $db->execSQL($sSQL);

            //me fijo si la foto es nueva o se mantiene la anterior.
            $sSQL = "SELECT cambioFoto FROM discapacitados_moderacion dm WHERE dm.id = '".$iDiscapacitadoId."'";
            $db->query($sSQL);
            $result = $db->oNextRecord();
            $cambioFoto = ($result->cambioFoto == "1")?true:false;

            $sSQL = "DELETE FROM discapacitados_moderacion WHERE id = '".$iDiscapacitadoId."'";

            $db->execSQL($sSQL);

            $db->commit();

            return array(true, $cambioFoto);
           
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
            return array(false, false);
        }            
    }

    public function rechazarCambiosModeracion($iDiscapacitadoId)
    {
        try{
            $db = $this->conn;

            $db->begin_transaction();

            //me fijo si la foto es nueva o se mantiene la anterior.
            $sSQL = "SELECT cambioFoto FROM discapacitados_moderacion dm WHERE dm.id = '".$iDiscapacitadoId."'";
            $db->query($sSQL);
            $result = $db->oNextRecord();
            $cambioFoto = ($result->cambioFoto == "1")?true:false;

            $db->execSQL("delete from discapacitados_moderacion where id = '".$iDiscapacitadoId."'");

            $db->commit();

            return array(true, $cambioFoto);

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return array(false, false);
        }        
    }

    /**
     * Devuelve objeto Discapacitado pero con los datos sin moderar de la tabla temporal
     */
    public function obtenerModeracion($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        dm.id as iId, dm.nombre as sNombre, dm.apellido as sApellido,
                        dm.documento_tipos_id as iTipoDocumentoId,
                        dm.numeroDocumento as sNumeroDocumento,
                        dm.sexo as sSexo, dm.fechaNacimiento as dFechaNacimiento,
                        dm.telefono as sTelefono,
                        dm.domicilio as sDomicilio,
                        dm.ciudades_id as iCiudadId, dm.instituciones_id as iInstitucionId,
                        dm.nombreApellidoPadre as sNombreApellidoPadre,
                        dm.nombreApellidoMadre as sNombreApellidoMadre,
                        dm.fechaNacimientoPadre as dFechaNacimientoPadre,
                        dm.fechaNacimientoMadre as dFechaNacimientoMadre,
                        dm.ocupacionPadre as sOcupacionPadre, dm.ocupacionMadre as sOcupacionMadre,
                        dm.nombreHermanos as sNombreHermanos,
                        dm.usuarios_id as iUsuarioId,
                        dm.nombreBigSize as sFotoNombreBigSize,
                        dm.nombreMediumSize as sFotoNombreMediumSize,
                        dm.nombreSmallSize as sFotoNombreSmallSize,

                        f.id as iFotoId,                        
                        f.orden as iFotoOrden, f.titulo as sFotoTitulo,
                        f.descripcion as sFotoDescripcion, f.tipo as sFotoTipo
                    FROM
                        discapacitados_moderacion dm
                    LEFT JOIN fotos f ON f.personas_id = dm.id ";

                    if(!empty($filtro)){
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aDiscapacitado = array();
            while($oObj = $db->oNextRecord()){
                $oDiscapacitado = new stdClass();
                $oDiscapacitado->iId = $oObj->iId;
                $oDiscapacitado->sNombre = $oObj->sNombre;
                $oDiscapacitado->sApellido = $oObj->sApellido;
                $oDiscapacitado->iTipoDocumentoId = $oObj->iTipoDocumentoId;
                $oDiscapacitado->sNumeroDocumento = $oObj->sNumeroDocumento;
                $oDiscapacitado->sSexo = $oObj->sSexo;
                $oDiscapacitado->sTelefono = $oObj->sTelefono;
                $oDiscapacitado->dFechaNacimiento = $oObj->dFechaNacimiento;
                $oDiscapacitado->sDomicilio = $oObj->sDomicilio;
                $oDiscapacitado->iCiudadId = $oObj->iCiudadId;
                $oDiscapacitado->iInstitucionId = $oObj->iInstitucionId;
                $oDiscapacitado->oCiudad = null; //a demanda
                $oDiscapacitado->oInstitucion = null; //a demanda
                $oDiscapacitado->oFotoPerfil = null;
                $oDiscapacitado->sNombreApellidoPadre	= $oObj->sNombreApellidoPadre;
                $oDiscapacitado->sNombreApellidoMadre	= $oObj->sNombreApellidoMadre;
                $oDiscapacitado->dFechaNacimientoPadre = $oObj->dFechaNacimientoPadre;
                $oDiscapacitado->dFechaNacimientoMadre 	= $oObj->dFechaNacimientoMadre;
                $oDiscapacitado->sOcupacionPadre = $oObj->sOcupacionPadre;
                $oDiscapacitado->sOcupacionMadre = $oObj->sOcupacionMadre;
                $oDiscapacitado->sNombreHermanos = $oObj->sNombreHermanos;
                $oDiscapacitado->iUsuarioId = $oObj->iUsuarioId;

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
                    $oDiscapacitado->oFotoPerfil = Factory::getFotoInstance($fotoPerfil);
                }else{
                    if(null !== $oObj->sFotoNombreMediumSize){
                        //se agrega foto x primera vez pero primero se modera la foto (no hay registro en la tabla foto)
                        $oFoto = new stdClass();
                        $oFoto->sNombreBigSize = $oObj->sFotoNombreBigSize;
                        $oFoto->sNombreMediumSize = $oObj->sFotoNombreMediumSize;
                        $oFoto->sNombreSmallSize = $oObj->sFotoNombreSmallSize;
                        $oFoto->iOrden = 0;
                        $oFoto->sTitulo = 'Foto de perfil';
                        $oFoto->sDescripcion = '';
                        $oFoto->sTipo = 'perfil';

                        $oDiscapacitado->oFotoPerfil = Factory::getFotoInstance($oFoto);
                    }
                }

                //creo el discapacitado
                $oDiscapacitado = Factory::getDiscapacitadoInstance($oDiscapacitado);
                $aDiscapacitado[] = $oDiscapacitado;
            }

            return $aDiscapacitado;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Si devuelve true para un id de descapacitado entonces quiere decir que tiene datos pendientes
     * de modificacion. (se usa para que no se permita modificar si ya tiene cambios pendientes)
     */
    public function existeModeracion($filtro)
    {
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        discapacitados_moderacion dm
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
  
    public function borrar($oDiscapacitado){
        try{
            $db = $this->conn;
            $db->execSQL("delete from personas where id = ".$db->escape($oDiscapacitado->getId(),false,MYSQL_TYPE_INT));
            $db->commit();
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Funcion booleana que devuelve true si el discapacitado tiene al menos un seguimiento.
     */
    public function tieneSeguimientos($iDiscapacitadoId)
    {
    	try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        discapacitados d
                    JOIN
                        seguimientos s ON d.id = s.discapacitados_id
                    WHERE d.id = '".$iDiscapacitadoId."'";

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