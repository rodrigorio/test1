<?php

class ArchivoMySQLIntermediary extends ArchivoIntermediary
{
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
    
    public function guardarCurriculumVitae($oUsuario)
    {
        $iIdUsuario = $oUsuario->getId();
        if(null !== $oUsuario->getCurriculumVitae()->getId()){
            return $this->actualizar($oArchivo);
        }else{
            return $this->insertarAsociado($oUsuario->getCurriculumVitae(), $iIdUsuario, get_class($oUsuario));
        }
    }

    public function guardarAntecedentesFile($oSeguimiento)
    {
        $iIdSeguimiento = $oSeguimiento->getId();
        return $this->insertarAsociado($oSeguimiento->getArchivoAntecedentes(), $iIdSeguimiento, get_class($oSeguimiento));
    }

    /**
     * En el peor de los casos se guarda 1 archivo solo, esto es por si se usa alguna libreria javascript
     * en la que se pueden subir muchos archivos al mismo tiempo
     */
    public function guardarArchivosFicha(FichaAbstract $oFicha)
    {
        if(null !== $oFicha->getArchivos()){
            foreach($oFicha->getArchivos() as $oArchivo){
                if(null !== $oArchivo->getId()){
                    return $this->actualizar($oArchivo);
                }else{
                    $iId = $oFicha->getId();
                    return $this->insertarAsociado($oArchivo, $iId, get_class($oFicha));
                }
            }
        }
    }
    
    public function guardarArchivosSeguimiento(SeguimientoAbstract $oSeguimiento)
    {
        if(null !== $oSeguimiento->getArchivos()){
            foreach($oSeguimiento->getArchivos() as $oArchivo){
                if(null !== $oArchivo->getId()){
                    return $this->actualizar($oArchivo);
                }else{
                    $iId = $oSeguimiento->getId();
                    return $this->insertarAsociado($oArchivo, $iId, get_class($oSeguimiento));
                }
            }
        }
    }
    
    public function borrar($aArchivos)
    {
        try{
            $db = $this->conn;

            if(is_array($aArchivos)){
                $db->begin_transaction();
                foreach($aArchivos as $oArchivo){
                    $db->execSQL("DELETE FROM archivos WHERE id = ".$this->escInt($oArchivo->getId()));
                }
                $db->commit();
                return true;
            }else{
                $db->execSQL("DELETE FROM archivos WHERE id = ".$this->escInt($aArchivos->getId()));
                $db->commit();
                return true;
            }
            
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }        
    }

    public function actualizar($oArchivo){
        try{
            $db = $this->conn;

            $moderado = $oArchivo->isModerado() ? "1" : "0";
            $activo = $oArchivo->isActivo() ? "1" : "0";
            $publico = $oArchivo->isPublico() ? "1" : "0";
            $activoComentarios = $oArchivo->isActivoComentarios() ? "1" : "0";

            $iOrden = ($oArchivo->getOrden() == "" || $oArchivo->getOrden() == '0') ? "null" : $oArchivo->getOrden();

            $sSQL = " update archivos " .
                    " set nombre = ".$this->escStr($oArchivo->getNombre()).", " .
                    " nombreServidor = ".$this->escStr($oArchivo->getNombreServidor()).", " .
                    " descripcion = ".$this->escStr($oArchivo->getDescripcion()).", ".
                    " tipoMime = ".$this->escStr($oArchivo->getTipoMime()).", " .
                    " tamanio = ".$this->escInt($oArchivo->getTamanio()).", " .
                    " fechaAlta = '".$oArchivo->getFechaAlta()."', ".
                    " orden = ".$iOrden.", " .
                    " titulo = ".$this->escStr($oArchivo->getTitulo()).", " .
                    " tipo = ".$this->escStr($oArchivo->getTipo()).", " .
                    " moderado = ".$this->escInt($moderado).", " .
                    " activo = ".$this->escInt($activo).", " .
                    " publico = ".$this->escInt($publico).", " .
                    " activoComentarios = ".$this->escInt($activoComentarios).
                    " WHERE id = ".$this->escInt($oArchivo->getId());

            $db->execSQL($sSQL);
            $db->commit();

            return true;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Inserta un archivo en DB, recibe 2 parametros extra para guardar el id del
     * objeto al que se asocia. Esto solo se precisa al insertar un nuevo archivo.
     *
     * @param Archivo $oArchivo
     * @param integer $iIdItem
     * @param string $sObjetoAsociado El nombre de la clase del objeto que tiene asociado el archivo. (seguimiento, usuario, categoria, etc)
     */
    public function insertarAsociado($oArchivo, $iIdItem, $sObjetoAsociado)
    {
        try{
            $db = $this->conn;
            $iIdItem = $this->escInt($iIdItem);

            $moderado = $oArchivo->isModerado() ? "1" : "0";
            $activo = $oArchivo->isActivo() ? "1" : "0";
            $publico = $oArchivo->isPublico() ? "1" : "0";
            $activoComentarios = $oArchivo->isActivoComentarios() ? "1" : "0";

            $sSQL = " INSERT INTO archivos SET ";

            switch($sObjetoAsociado){
                case "Publicacion": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "Review": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "SeguimientoSCC": $sSQL .= "seguimientos_id = ".$iIdItem.", "; break;
                case "SeguimientoPersonalizado": $sSQL .= "seguimientos_id = ".$iIdItem.", "; break;
                case "Usuario": $sSQL .= "usuarios_id = ".$iIdItem.", "; break;
                case "Categoria": $sSQL .= "categorias_id = ".$iIdItem.", "; break;
            }

            //orden y fecha quedan con valores por defecto en la insercion.
            
           $sSQL .= " nombre = ".$this->escStr($oArchivo->getNombre()).", " .
            " nombreServidor = ".$this->escStr($oArchivo->getNombreServidor()).", " .
            " descripcion = ".$this->escStr($oArchivo->getDescripcion()).", ".
            " tipoMime = ".$this->escStr($oArchivo->getTipoMime()).", " .
            " tamanio = ".$this->escInt($oArchivo->getTamanio()).", " .
            " titulo = ".$this->escStr($oArchivo->getTitulo()).", " .
            " tipo = ".$this->escStr($oArchivo->getTipo()).", " .
            " moderado = ".$this->escInt($moderado).", " .
            " activo = ".$this->escInt($activo).", " .
            " publico = ".$this->escInt($publico).", " .
            " activoComentarios = ".$this->escInt($activoComentarios)." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            
            $db->commit();

            $oArchivo->setId($iLastId);

            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }        
    }
   
    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null) {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        a.id as iId,
                        a.nombre as sNombre,
                        a.nombreServidor as sNombreServidor,
                        a.descripcion as sDescripcion,
                        a.tipoMime as sTipoMime,
                        a.tamanio as iTamanio,
                        a.fechaAlta as sFechaAlta,
                        a.orden as iOrden,
                        a.titulo as sTitulo,
                        a.tipo as sTipo,
                        a.moderado as bModerado,
                        a.activo as bActivo,
                        a.publico as bPublico,
                        a.activoComentarios as bActivoComentarios
                    FROM
                        archivos a ";

            $WHERE = array();
            if(isset($filtro['a.id']) && $filtro['a.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('a.id', $filtro['a.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['a.seguimientos_id']) && $filtro['a.seguimientos_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('a.seguimientos_id', $filtro['a.seguimientos_id'], MYSQL_TYPE_INT);
                
	            if(isset($filtro['a.tipo']) && $filtro['a.tipo']!=""){
	                $WHERE[] = $this->crearFiltroSimple('a.tipo', $filtro['a.tipo']);
	            }
            }
            if(isset($filtro['a.fichas_abstractas_id']) && $filtro['a.fichas_abstractas_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('a.fichas_abstractas_id', $filtro['a.fichas_abstractas_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['a.usuarios_id']) && $filtro['a.usuarios_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('a.usuarios_id', $filtro['a.usuarios_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['a.categorias_id']) && $filtro['a.categorias_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('a.categorias_id', $filtro['a.categorias_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['a.nombreServidor']) && $filtro['a.nombreServidor']!=""){
                $WHERE[] = $this->crearFiltroSimple('a.nombreServidor', $filtro['a.nombreServidor']);
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

            $aArchivos = array();
            while($oObj = $db->oNextRecord()){
                $oArchivo                       = new stdClass();
                $oArchivo->iId                  = $oObj->iId;
                $oArchivo->sNombre              = $oObj->sNombre;
                $oArchivo->sNombreServidor      = $oObj->sNombreServidor;
                $oArchivo->sDescripcion         = $oObj->sDescripcion;
                $oArchivo->sTipoMime            = $oObj->sTipoMime;
                $oArchivo->iTamanio             = $oObj->iTamanio;
                $oArchivo->sFechaAlta           = $oObj->sFechaAlta;
                $oArchivo->iOrden               = $oObj->iOrden;
                $oArchivo->sTitulo              = $oObj->sTitulo;
                $oArchivo->sTipo                = $oObj->sTipo;
                $oArchivo->bModerado            = $oObj->bModerado;
                $oArchivo->bActivo              = $oObj->bActivo;
                $oArchivo->bPublico             = $oObj->bPublico;
                $oArchivo->bActivoComentarios   = $oObj->bActivoComentarios;

                $aArchivos[] = Factory::getArchivoInstance($oArchivo);
            }
            return $aArchivos;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

 public function obtenerComentarios($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null) {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        c.id as iId,
                        c.fecha as dFecha,
                        c.descripcion as sDescripcion,
                        c.valoracion as fValoracion,
                        c.usuarios_id as iUsuarioId
                        
                    FROM
                        comentario c ";

            $WHERE = array();
            if(isset($filtro['c.id']) && $filtro['c.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.id', $filtro['c.id'], MYSQL_TYPE_INT);
            }
                    
            if(isset($filtro['c.fecha']) && $filtro['c.fecha']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.fecha', $filtro['c.fecha'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['c.descripcion']) && $filtro['c.descripcion']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.descripcion', $filtro['c.descripcion'], MYSQL_TYPE_INT);
            }
              if(isset($filtro['c.valoracion']) && $filtro['c.valoracion']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.valoracion', $filtro['c.valoracion'], MYSQL_TYPE_INT);
            }
              if(isset($filtro['c.usuarios_id']) && $filtro['c.usuarios_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.usuarios_id', $filtro['c.usuarios_id'], MYSQL_TYPE_INT);
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

            $aComentario = array();
            while($oObj = $db->oNextRecord()){
                $oComentario                       = new stdClass();
                $oComentario->iId                  = $oObj->iId;
                $oComentario->sDescripcion         = $oObj->sDescripcion;
                $oComentario->fValoracion            = $oObj->fValoracion;
                $oComentario->iUsuarioId             = $oObj->iUsuarioId;
                
                $aComentarios[] = Factory::getComentarioInstance($oComentario);
            }

            return $aComentarios;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function isArchivoPublicacionUsuario($iArchivoId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        archivos a
                        JOIN fichas_abstractas fa ON a.fichas_abstractas_id = fa.id
                        LEFT JOIN publicaciones p ON fa.id = p.id
                        LEFT JOIN reviews r ON fa.id = r.id
                      WHERE
                        a.id = ".$this->escInt($iArchivoId)." AND
                        (p.usuarios_id = ".$this->escInt($iUsuarioId)." OR r.usuarios_id = ".$this->escInt($iUsuarioId).")";

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

    public function isArchivoSeguimientoUsuario($iArchivoId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        archivos a
                        JOIN seguimientos s ON a.seguimientos_id = s.id
                      WHERE
                        a.id = ".$this->escInt($iArchivoId)." AND 
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
    
    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($objects){}
    public function guardar($object){}
}
