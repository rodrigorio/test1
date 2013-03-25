<?php

class SeguimientoMySQLIntermediary extends SeguimientoIntermediary
{
    /**
     * solo para generar los selects de filtros o las consultas dentro de esta clase,
     * para cualquier otra cosa se utiliza los metodos
     *
     * isSeguimientoPersonalizado()
     * isSeguimientoSCC()
     *
     * Los valores de las constantes TIENEN QUE COINCIDIR CON EL NOMBRE DE LAS CLASES CONCRETAS
     * 'SeguimientoSCC', 'SeguimientoPersonalizado'
     * 
     */
    const TIPO_SEGUIMIENTO_SCC = "SeguimientoSCC";
    const TIPO_SEGUIMIENTO_PERSONALIZADO = "SeguimientoPersonalizado";

    private static $instance = null;

    protected function __construct( $conn) {
            parent::__construct($conn);
    }

    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    /**
     * Nombre de la clase => Descripcion
     */
    public function obtenerTiposSeguimientos()
    {
        return array(self::TIPO_SEGUIMIENTO_SCC => 'Seguimiento Competencia Curricular',
                     self::TIPO_SEGUIMIENTO_PERSONALIZADO => 'Seguimiento Personalizado');
    }

    public final function buscar($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                          s.id as iId,
                          s.discapacitados_id as iDiscapacitadoId,
                          s.frecuenciaEncuentros as sFrecuenciaEncuentros,
                          s.diaHorario as sDiaHorario,
                          s.practicas_id as iPracticaId,
                          s.usuarios_id as iUsuarioId,
                          s.antecedentes as sAntecedentes,
                          s.pronostico as sPronostico,
                          s.fechaCreacion as dFechaCreacion,
                          s.estado AS sEstado,
                          IF(sp.id IS NULL, '".self::TIPO_SEGUIMIENTO_SCC."', '".self::TIPO_SEGUIMIENTO_PERSONALIZADO."') as tipo,

                          a.id as iArchivoId,
                          a.nombre as sArchivoNombre,
                          a.nombreServidor as sArchivoNombreServidor,
                          a.descripcion as sArchivoDescripcion,
                          a.tipoMime as sArchivoTipoMime,
                          a.tamanio as iArchivoTamanio,
                          a.fechaAlta as sArchivoFechaAlta,
                          a.orden as iArchivoOrden,
                          a.titulo as sArchivoTitulo,
                          a.tipo as sArchivoTipo,
                          
                          p.nombre
                    FROM
                        seguimientos s
                    LEFT JOIN
                        seguimientos_personalizados sp ON sp.id = s.id
                    LEFT JOIN
                        seguimientos_scc sscc ON s.id = sscc.id
                    LEFT JOIN
			(SELECT * FROM archivos WHERE archivos.tipo = 'antecedentes') AS a ON a.seguimientos_id = s.id
                    JOIN
                        discapacitados d ON d.id = s.discapacitados_id
                    JOIN
                        personas p ON p.id = d.id
                    JOIN
                        usuarios u ON u.id = s.usuarios_id ";

            $WHERE = array();

            if(isset($filtro['s.estado']) && $filtro['s.estado'] != ""){
                $WHERE[] = $this->crearFiltroSimple('s.estado', $filtro['s.estado']);
            }
            if(isset($filtro['p.apellido']) && $filtro['p.apellido'] != ""){
                $WHERE[] = $this->crearFiltroTexto('p.apellido', $filtro['p.apellido']);
            }
            if(isset($filtro['p.numeroDocumento']) && $filtro['p.numeroDocumento'] != ""){
                $WHERE[] = $this->crearFiltroSimple('p.numeroDocumento', $filtro['p.numeroDocumento']);
            }
            if(isset($filtro['tipo']) && $filtro['tipo'] != ""){
                if($filtro['tipo'] == "SeguimientoSCC"){
                    $WHERE[] = " sscc.id != 'null' ";
                }else{
                    $WHERE[] = " sp.id != 'null' ";
                }
            }            
            //filtro de la fecha. es un array que adentro tiene fechaDesde y fechaHasta
            if(isset($filtro['fecha']) && null !== $filtro['fecha']){
                if(is_array($filtro['fecha'])){                    
                    $WHERE[] = $this->crearFiltroFechaDesdeHasta('s.fechaCreacion', $filtro['fecha']);
                }
            }
         	if(isset($filtro['u.id']) && $filtro['u.id'] != ""){
                $WHERE[] = $this->crearFiltroSimple('u.id', $filtro['u.id']);
            }
            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by sEstado asc, $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by sEstado asc, s.fechaCreacion desc ";
            }
            
            if($iIniLimit !== null && $iRecordCount !== null){
                $sSQL .= " limit ".$this->escInt($iIniLimit).", ".$this->escInt($iRecordCount);
            }
            
            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aSeguimientos = array();
            while($oObj = $db->oNextRecord()){

                //todos los objetos que estan en relacion de composicion se crean en esta instancia.
                $oSeguimiento = new stdClass();
                $oSeguimiento->iId = $oObj->iId;
                $oSeguimiento->oDiscapacitado = SeguimientosController::getInstance()->getDiscapacitadoById($oObj->iDiscapacitadoId);
                $oSeguimiento->sFrecuenciaEncuentros = $oObj->sFrecuenciaEncuentros;
                $oSeguimiento->sDiaHorario = $oObj->sDiaHorario;
                $oSeguimiento->oPractica = SeguimientosController::getInstance()->getPracticaById($oObj->iPracticaId);
                $oSeguimiento->iUsuarioId = $oObj->iUsuarioId;
                $oSeguimiento->oUsuario = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);
                $oSeguimiento->sAntecedentes = $oObj->sAntecedentes;
                $oSeguimiento->sPronostico = $oObj->sPronostico;
                $oSeguimiento->dFechaCreacion = $oObj->dFechaCreacion;
                $oSeguimiento->sEstado = $oObj->sEstado;

                if(null !== $oObj->iArchivoId){
                    $oAntecedentes = new stdClass();
                    $oAntecedentes->iId = $oObj->iArchivoId;
                    $oAntecedentes->sNombre = $oObj->sArchivoNombre;
                    $oAntecedentes->sNombreServidor = $oObj->sArchivoNombreServidor;
                    $oAntecedentes->sDescripcion = $oObj->sArchivoDescripcion;
                    $oAntecedentes->sTipoMime = $oObj->sArchivoTipoMime;
                    $oAntecedentes->iTamanio = $oObj->iArchivoTamanio;
                    $oAntecedentes->sFechaAlta = $oObj->sArchivoFechaAlta;
                    $oAntecedentes->iOrden = $oObj->iArchivoOrden;
                    $oAntecedentes->sTitulo = $oObj->sArchivoTitulo;
                    $oAntecedentes->sTipo = $oObj->sArchivoTipo;
                    $oSeguimiento->oAntecedentes = Factory::getArchivoInstance($oAntecedentes);
                }

                if($oObj->tipo == self::TIPO_SEGUIMIENTO_SCC){
                    $oSeguimiento->oDiagnostico = SeguimientosController::getInstance()->getDiagnosticoSeguimientoPersonalizadoById($oObj->iId);
                    $aSeguimientos[] = Factory::getSeguimientoSCCInstance($oSeguimiento);
                }else{
                    $oSeguimiento->oDiagnostico = SeguimientosController::getInstance()->getDiagnosticoSeguimientoSCCById($oObj->iId);
                    $aSeguimientos[] = Factory::getSeguimientoPersonalizadoInstance($oSeguimiento);
                }
            }

            return $aSeguimientos;

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
                        seguimientos s 
                    JOIN 
                    	usuarios u ON s.usuarios_id = u.id
                    WHERE ".$this->crearCondicionSimple($filtro,"",false,"OR");

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ 
            	return false; 
            }
            return true;
    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                          s.id as iId,
                          s.discapacitados_id as iDiscapacitadoId,
                          s.frecuenciaEncuentros as sFrecuenciaEncuentros,
                          s.diaHorario as sDiaHorario,
                          s.practicas_id as iPracticaId,
                          s.usuarios_id as iUsuarioId,
                          s.antecedentes as sAntecedentes,
                          s.pronostico as sPronostico,
                          s.fechaCreacion as dFechaCreacion,
                          s.estado as sEstado,
                          IF(sp.id IS NULL, '".self::TIPO_SEGUIMIENTO_SCC."', '".self::TIPO_SEGUIMIENTO_PERSONALIZADO."') as tipo,

                          a.id as iArchivoId,
                          a.nombre as sArchivoNombre,
                          a.nombreServidor as sArchivoNombreServidor,
                          a.descripcion as sArchivoDescripcion,
                          a.tipoMime as sArchivoTipoMime,
                          a.tamanio as iArchivoTamanio,
                          a.fechaAlta as sArchivoFechaAlta,
                          a.orden as iArchivoOrden,
                          a.titulo as sArchivoTitulo,
                          a.tipo as sArchivoTipo
                    FROM
                        seguimientos s
                    LEFT JOIN
                        seguimientos_personalizados sp ON sp.id = s.id
                    LEFT JOIN
                        seguimientos_scc sscc ON s.id = sscc.id
                    JOIN usuarios u ON u.id = s.usuarios_id
                    LEFT JOIN
			(SELECT * FROM archivos WHERE archivos.tipo = 'antecedentes') AS a ON a.seguimientos_id = s.id 
                    JOIN personas p ON p.id = s.discapacitados_id ";

            if(!empty($filtro)){
                $sSQL .= " WHERE ".$this->crearCondicionSimple($filtro);
            }
            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }
            
            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aSeguimientos = array();
            while($oObj = $db->oNextRecord()){

                $oSeguimiento = new stdClass();
                $oSeguimiento->iId = $oObj->iId;
                $oSeguimiento->oDiscapacitado = SeguimientosController::getInstance()->getDiscapacitadoById($oObj->iDiscapacitadoId);
                $oSeguimiento->sFrecuenciaEncuentros = $oObj->sFrecuenciaEncuentros;
                $oSeguimiento->sDiaHorario = $oObj->sDiaHorario;
                $oSeguimiento->oPractica = SeguimientosController::getInstance()->getPracticaById($oObj->iPracticaId);
                $oSeguimiento->iUsuarioId       = $oObj->iUsuarioId;
                $oSeguimiento->oUsuario = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);
                $oSeguimiento->sAntecedentes = $oObj->sAntecedentes;
                $oSeguimiento->sPronostico = $oObj->sPronostico;
                $oSeguimiento->dFechaCreacion = $oObj->dFechaCreacion;
                $oSeguimiento->sEstado = $oObj->sEstado;

                if(null !== $oObj->iArchivoId){
                    $oAntecedentes = new stdClass();
                    $oAntecedentes->iId = $oObj->iArchivoId;
                    $oAntecedentes->sNombre = $oObj->sArchivoNombre;
                    $oAntecedentes->sNombreServidor = $oObj->sArchivoNombreServidor;
                    $oAntecedentes->sDescripcion = $oObj->sArchivoDescripcion;
                    $oAntecedentes->sTipoMime = $oObj->sArchivoTipoMime;
                    $oAntecedentes->iTamanio = $oObj->iArchivoTamanio;
                    $oAntecedentes->sFechaAlta = $oObj->sArchivoFechaAlta;
                    $oAntecedentes->iOrden = $oObj->iArchivoOrden;
                    $oAntecedentes->sTitulo = $oObj->sArchivoTitulo;
                    $oAntecedentes->sTipo = $oObj->sArchivoTipo;
                    $oSeguimiento->oAntecedentes = Factory::getArchivoInstance($oAntecedentes);
                }
                
                if($oObj->tipo == self::TIPO_SEGUIMIENTO_SCC){
                    $oSeguimiento->oDiagnostico = SeguimientosController::getInstance()->getDiagnosticoSeguimientoSCCById($oObj->iId);
                    $objSeguimiento = Factory::getSeguimientoSCCInstance($oSeguimiento);
                }else{
                    $oSeguimiento->oDiagnostico = SeguimientosController::getInstance()->getDiagnosticoSeguimientoPersonalizadoById($oObj->iId);
                    $objSeguimiento = Factory::getSeguimientoPersonalizadoInstance($oSeguimiento);
                }
                $aSeguimientos[] = $objSeguimiento;
            }

            return $aSeguimientos;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oSeguimiento)
    {
        if($oSeguimiento->getUsuario() === null){
            throw new Exception("El seguimiento no tiene usuario");
        }
        if($oSeguimiento->getDiscapacitado() === null){
            throw new Exception("El seguimiento no tiene discapacitado asociado");
        }
        if($oSeguimiento->getPractica() === null){
            throw new Exception("El seguimiento no tiene practica");
        }
        
        try{
            if($oSeguimiento->getId() !== null){

                //porque el diagnostico se crea cuando se inserta el seguimiento
                if($oSeguimiento->getDiagnostico() === null){
                    throw new Exception("El seguimiento no tiene diagnostico");
                }
                
                if($oSeguimiento->isSeguimientoPersonalizado()){
                    return $this->actualizar($oSeguimiento);
                }else{
                    return $this->actualizarSCC($oSeguimiento);
                }
            }else{
                if($oSeguimiento->isSeguimientoPersonalizado()){
                    return $this->insertar($oSeguimiento);
                }else{
                    return $this->insertarSCC($oSeguimiento);
                }
            }            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizar($oSeguimientoPersonalizado)
    {
        try{
            $db = $this->conn;

            $iUsuarioId = $oSeguimientoPersonalizado->getUsuario()->getId();
            $iDiscapacitadoId = $oSeguimientoPersonalizado->getDiscapacitado()->getId();
            $iDiagnosticoId = $oSeguimientoPersonalizado->getDiagnostico()->getId();
            $iPracticaId = $oSeguimientoPersonalizado->getPractica()->getId();
			
            $db->begin_transaction();

            $sSQL = " UPDATE seguimientos SET ".
                    " frecuenciaEncuentros = ".$db->escape($oSeguimientoPersonalizado->getFrecuenciaEncuentros(),true).", ".
                    " diaHorario = ".$db->escape($oSeguimientoPersonalizado->getDiaHorario(),true).", ".
                    " discapacitados_id = ".$db->escape($iDiscapacitadoId,false,MYSQL_TYPE_INT).", ".
                    " usuarios_id = ".$db->escape($iUsuarioId,false,MYSQL_TYPE_INT).", ".
                    " practicas_id = ".$db->escape($iPracticaId,false,MYSQL_TYPE_INT).", ".
                    " antecedentes = ".$db->escape($oSeguimientoPersonalizado->getAntecedentes(),true).", ".
                    " pronostico = ".$db->escape($oSeguimientoPersonalizado->getPronostico(), true) .", ".
                    " estado = ".$db->escape($oSeguimientoPersonalizado->getEstado(), true)." ".
                    " WHERE id = ".$db->escape($oSeguimientoPersonalizado->getId(),false,MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);
			 
            $sSQL = " UPDATE seguimientos_personalizados SET ".
                    " diagnosticos_personalizado_id = ".$db->escape($iDiagnosticoId,false,MYSQL_TYPE_INT)." ".
                    " WHERE id = ".$db->escape($oSeguimientoPersonalizado->getId(),false,MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;
	
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizarSCC($oSeguimientoSCC)
    {
        try{
            $db = $this->conn;
					
            $iUsuarioId = $oSeguimientoSCC->getUsuario()->getId();
            $iDiscapacitadoId = $oSeguimientoSCC->getDiscapacitado()->getId();
            $iDiagnosticoId = $oSeguimientoSCC->getDiagnostico()->getId();
            $iPracticaId = $oSeguimientoSCC->getPractica()->getId();
			
            $db->begin_transaction();
            
            $sSQL = " UPDATE seguimientos SET ".
                    " frecuenciaEncuentros = ".$db->escape($oSeguimientoSCC->getFrecuenciaEncuentros(),true).", " .
                    " diaHorario = ".$db->escape($oSeguimientoSCC->getDiaHorario(),true).", " .
                    " discapacitados_id = ".$db->escape($iDiscapacitadoId,false,MYSQL_TYPE_INT).", ".
                    " usuarios_id = ".$db->escape($iUsuarioId,false,MYSQL_TYPE_INT).", ".
                    " practicas_id = ".$db->escape($iPracticaId,false,MYSQL_TYPE_INT).", ".
                    " antecedentes = ".$db->escape($oSeguimientoSCC->getAntecedentes(),true).", " .
                    " pronostico = ".$db->escape($oSeguimientoSCC->getPronostico(), true) .", ".
                    " estado = ".$db->escape($oSeguimientoSCC->getEstado(), true) ." ".
                    " WHERE id = ".$db->escape($oSeguimientoSCC->getId(),false,MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);
			 
            $sSQL = " UPDATE seguimientos_scc SET ".
                    " diagnosticos_scc_id = ".$db->escape($iDiagnosticoId,false,MYSQL_TYPE_INT)." ".
                    " WHERE id = ".$db->escape($oSeguimientoSCC->getId(),false,MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function insertar($oSeguimientoPersonalizado)
    {
        try{
            $db = $this->conn;

            $iUsuarioId = $oSeguimientoPersonalizado->getUsuario()->getId();
            $iDiscapacitadoId = $oSeguimientoPersonalizado->getDiscapacitado()->getId();
            $iPracticaId = $oSeguimientoPersonalizado->getPractica()->getId();
			
            $db->begin_transaction();

            $sSQL = " INSERT INTO seguimientos ".
                    " set frecuenciaEncuentros = ".$db->escape($oSeguimientoPersonalizado->getFrecuenciaEncuentros(),true).", ".
                    " diaHorario = ".$db->escape($oSeguimientoPersonalizado->getDiaHorario(),true).", ".
                    " discapacitados_id = ".$db->escape($iDiscapacitadoId,false,MYSQL_TYPE_INT).", ".
                    " usuarios_id = ".$db->escape($iUsuarioId,false,MYSQL_TYPE_INT).", ".
                    " practicas_id = ".$db->escape($iPracticaId,false,MYSQL_TYPE_INT).", ".
                    " antecedentes = ".$db->escape($oSeguimientoPersonalizado->getAntecedentes(),true).", ".
                    " pronostico = ".$db->escape($oSeguimientoPersonalizado->getPronostico(), true) ." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();

            $oDiagnostico = Factory::getDiagnosticoPersonalizadoInstance(new stdClass());
            SeguimientosController::getInstance()->guardarDiagnostico($oDiagnostico);

            $sSQL = " INSERT INTO seguimientos_personalizados SET ".
                    " id = ".$this->escInt($iLastId).", ".
                    " diagnostico_personalizado_id = ".$db->escape($oDiagnostico->getId(),false,MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);

          
            $sSQL = "SELECT u.id as iId FROM unidades u WHERE u.porDefecto = 1";
            $db->query($sSQL);
            while($oObj = $db->oNextRecord()){
            	$iUnidadId = $oObj->iId;
            }

            $sSQL = " insert into seguimiento_x_unidades set ".
                    " unidad_id = ".$this->escInt($iUnidadId).", ".
                    " seguimiento_id = ".$this->escInt($iLastId)." ";
            

            $db->execSQL($sSQL);
            $db->commit();

            $oSeguimientoPersonalizado->setId($iLastId);
            $oSeguimientoPersonalizado->setDiagnostico($oDiagnostico);

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            $oSeguimientoPersonalizado->setId(null);
            throw new Exception($e->getMessage(), 0);
        }
    }
   
    public function insertarSCC($oSeguimientoSCC)
    {
        try{

            $iUsuarioId = $oSeguimientoSCC->getUsuario()->getId();
            $iDiscapacitadoId = $oSeguimientoSCC->getDiscapacitado()->getId();
            $iPracticaId = $oSeguimientoSCC->getPractica()->getId();

            $db = $this->conn;
									
            $db->begin_transaction();

            $sSQL = " insert into seguimientos ".
                    " set frecuenciaEncuentros =".$db->escape($oSeguimientoSCC->getFrecuenciaEncuentros(),true).", " .
                    " diaHorario =".$db->escape($oSeguimientoSCC->getDiaHorario(),true).", " .
                    " discapacitados_id =".$db->escape($iDiscapacitadoId,false,MYSQL_TYPE_INT).", ".
                    " usuarios_id =".$db->escape($iUsuarioId,false,MYSQL_TYPE_INT).", ".
                    " practicas_id =".$db->escape($iPracticaId,false,MYSQL_TYPE_INT).", ".
                    " antecedentes =".$db->escape($oSeguimientoSCC->getAntecedentes(),true).", " .
                    " pronostico= ".$db->escape($oSeguimientoSCC->getPronostico(), true) ." ";
			
            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
			
            $oDiagnostico = Factory::getDiagnosticoSCCInstance(new stdClass());
            $diagnosticoSCCId = SeguimientosController::getInstance()->guardarDiagnostico($oDiagnostico);
			
            $sSQL =" insert into seguimientos_scc set ".
            " id=".$db->escape($iLastId,false).", " .
            " diagnosticos_scc_id=".$db->escape($diagnosticoSCCId,false,MYSQL_TYPE_INT)." " ;
		
            $db->execSQL($sSQL);
            $db->commit();
            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
   }
    
   public function borrar($oSeguimiento)
   {
       //REVISAR... cuando se borra un seguimiento borrar todo lo q no desaparece x cascada.

       /*
        try{
            $db = $this->conn;
            
            $db->begin_transaction();
			
            $iDiagnosticoId = $oSeguimiento->getDiagnostico()->getId();
            $iSeguimientoId = $oSeguimiento->getId();
			
            $db->execSQL("delete from diagnosticos where id = '".$iDiagnosticoId."'");						            
            $db->execSQL("delete from seguimientos where id = '".$iSeguimientoId."'");
                        
            $db->commit();
            return true;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
        */
    }

    /**
     * La relacion con videos archivos y fotos es de la clase abstracta
     * asi que me alcanza con utilizar la tabla seguimientos
     */
    public function obtenerCantidadElementosAdjuntos($iSeguimientoId)
    {
        try{
            $cantFotos = $cantVideos = $cantArchivos = 0;

            $db = $this->conn;

            $db->query("SELECT
                            COUNT(*) as cantidad
                        FROM
                            archivos where seguimientos_id = '".$iSeguimientoId."'");
            $cantArchivos = $db->oNextRecord()->cantidad;

            $db->query("SELECT
                            COUNT(*) as cantidad
                        FROM
                            fotos where seguimientos_id = '".$iSeguimientoId."'");
            $cantFotos = $db->oNextRecord()->cantidad;

            $db->query("SELECT
                            COUNT(*) as cantidad
                        FROM
                            embed_videos where seguimientos_id = '".$iSeguimientoId."'");
            $cantVideos = $db->oNextRecord()->cantidad;

            return array($cantFotos, $cantVideos, $cantArchivos);
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    public function isSeguimientoUsuario($iUsuarioId, $iSeguimientoId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        seguimientos s
                      JOIN 
                      	seguimientos _personalizados sp 
                      ON 
                      	sp.id = s.id
                      WHERE
                        s.id = ".$this->escInt($iSeguimientoId)." AND
                        s.usuarios_id = ".$this->escInt($iUsuarioId);

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
   public function isDiagnosticoPersonalizadoUsuario($iUsuarioId, $iDiagnosticoPersonalizadoId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        seguimientos s
                      JOIN 
                      	seguimientos_personalizados sp 
                      ON 	
                      	sp.id = s.id
                      WHERE
                        sp.diagnosticos_personalizado_id = ".$this->escInt($iDiagnosticoPersonalizadoId)." AND
                        s.usuarios_id = ".$this->escInt($iUsuarioId);

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
   public function isDiagnosticoSCCUsuario($iUsuarioId, $iDiagnosticoSCCId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        seguimientos s
                      JOIN 
                      	seguimientos_SCC sscc 
                      ON 	
                      	sscc.id = s.id
                      WHERE
                        sscc.diagnosticos_scc_id = ".$this->escInt($iDiagnosticoSCCId)." AND
                        s.usuarios_id = ".$this->escInt($iUsuarioId);

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
   /**
     * El controlador de seguimiento con el metodo "isDiagnosticoSeguimientoUsuario" tiene que verificar que es el diagnostico de un seguimiento que haya
     * creado el usuario que esta en sesion
     */

    public function asociarSeguimientoXContenidoVariables($iSeguimientoId,$vUnidad)
    {
        try{
            $db = $this->conn;            
            $db->begin_transaction();   
          $sSQL =  "CREATE TEMPORARY TABLE tablaTemporal ( variable VARCHAR(50), valor INT(15), tipo VARCHAR(50),fechaModificacion TIMESTAMP)"  
          $db->execSQL($sSQL);                   
            $sSQL = " insert into tablaTemporal (seguimiento_id, variable_id, valor, fechaHora) VALUES ";
            for ($i=0; $j< count($vUnidad); $j++) { 
            	$oVariable = $vUnidad[$j]->getVariables();
            
            for ($i=0; $i< count($vVariable); $i++) {
            	$oVariable = $vVariable[$i];
            	$sSQL .= " (".$this->escInt($iSeguimientoId).", "
            		.$this->escInt($oVariable->getId()).", "
            		.$this->escInt($oVariable->???Valor()).", "
            		.$this->escDate($oVariable->getFechaHora()).") ";
            	if (count($vVariable) > $i+1) {
            		$sSQL .= ",";
            	}
            	
            	}
            	if (count($vUnidad) > $j+1) {
            		$sSQL .= ",";
              } 
            }          
           
                      
            $db->execSQL($sSQL);
            $db->commit();

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
                 
    }
    /*public function asociarSeguimientoXContenidoVariables($iSeguimientoId,$oUnidad)
    {
        try{
            $db = $this->conn;            
            $db->begin_transaction();   
            $vVariable = $oUnidad ->getVariables();                     
            $sSQL = " insert into seguimientos_x_contenido_variables (seguimiento_id, variable_id, valor, fechaHora) VALUES ";
                        
            for ($i=0; $i< count($vVariable); $i++) {
            	$oVariable = $vVariable[$i];
            	$sSQL .= " (".$this->escInt($iSeguimientoId).", "
            		.$this->escInt($oVariable->getId()).", "
            		.$this->escInt($oVariable->Valor()).", "
            		.$this->escDate($oVariable->getFechaHora()).") ";
            	if (count($vVariable) > $i+1) {
            		$sSQL .= ",";
            	}
            } 
                      
            $db->execSQL($sSQL);
            $db->commit();

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
                 
    }*/
    
    public function actualizarCampoArray($objects, $cambios){}    
}