<?php

class FotoMySQLIntermediary extends FotoIntermediary
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

    /**
     * Todos los que heredan de persona tienen foto de perfil
     */
    public function guardarFotoPerfil(PersonaAbstract $oPersona)
    {
        if(null !== $oPersona->getFotoPerfil()->getId()){
            return $this->actualizar($oPersona->getFotoPerfil());
        }else{
            $iId = $oPersona->getId();
            return $this->insertarAsociado($oPersona->getFotoPerfil(), $iId, get_class($oPersona));
        }        
    }

    /**
     * En el peor de los casos se guarda 1 foto sola, esto es por si se usa alguna libreria javascript
     * en la que se pueden subir muchas fotos al mismo tiempo
     */
    public function guardarFotosFicha(FichaAbstract $oFicha)
    {
        if(null !== $oFicha->getFotos()){
            foreach($oFicha->getFotos() as $oFoto){
                if(null !== $oFoto->getId()){
                    return $this->actualizar($oFoto);
                }else{
                    $iId = $oFicha->getId();
                    return $this->insertarAsociado($oFoto, $iId, get_class($oFicha));
                }
            }
        }                    
    } 
    
    public function guardarFotosSeguimiento(SeguimientoAbstract $oSeguimiento)
    {
        if(null !== $oSeguimiento->getFotos()){
            foreach($oSeguimiento->getFotos() as $oFoto){
                if(null !== $oFoto->getId()){
                    return $this->actualizar($oFoto);
                }else{
                    $iId = $oSeguimiento->getId();
                    return $this->insertarAsociado($oFoto, $iId, get_class($oSeguimiento));
                }
            }
        }                    
    }

    public function borrar($aFotos)
    {
        try{
            $db = $this->conn;

            if(is_array($aFotos)){
                $db->begin_transaction();
                foreach($aFotos as $oFoto){
                    $db->execSQL("DELETE FROM fotos WHERE id = ".$this->escInt($oFoto->getId()));
                }
                $db->commit();
                return true;
            }else{
                $db->execSQL("DELETE FROM fotos WHERE id = ".$this->escInt($aFotos->getId()));
                $db->commit();
                return true;
            }

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }  
    }

    public function actualizar($oFoto){
        try{
            $db = $this->conn;
            
            $iOrden = ($oFoto->getOrden() == "" || $oFoto->getOrden() == '0') ? "null" : $oFoto->getOrden();
            
            $sSQL = " UPDATE fotos SET" .
                    " nombreBigSize = ".$this->escStr($oFoto->getNombreBigSize()).", " .
                    " nombreMediumSize = ".$this->escStr($oFoto->getNombreMediumSize()).", " .
                    " nombreSmallSize = ".$this->escStr($oFoto->getNombreSmallSize()).", ".
                    " orden = ".$iOrden.", " .
                    " titulo = ".$this->escStr($oFoto->getTitulo()).", " .
                    " descripcion = ".$this->escStr($oFoto->getDescripcion()).", " .
                    " tipo = ".$this->escStr($oFoto->getTipo())." " .
                    " WHERE id = ".$this->escInt($oFoto->getId());

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){            
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Inserta un archivo en DB, recibe 2 parametros extra para guardar el id del
     * objeto al que se asocia. Esto solo se precisa al insertar una nueva foto.
     *
     * @param Archivo $oArchivo
     * @param integer $iIdItem
     * @param string $sObjetoAsociado El nombre de la clase del objeto que tiene asociada la foto
     */
    public function insertarAsociado($oFoto, $iIdItem, $sObjetoAsociado)
    {
        try{
            $db = $this->conn;
            $iIdItem = $this->escInt($iIdItem);

            $sSQL = " INSERT INTO fotos SET ";

            switch($sObjetoAsociado){
                case "Publicacion": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "Review": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "SeguimientoSCC": $sSQL .= "seguimientos_id = ".$iIdItem.", "; break;
                case "SeguimientoPersonalizado": $sSQL .= "seguimientos_id = ".$iIdItem.", "; break;
                case "Discapacitado": $sSQL .= "personas_id = ".$iIdItem.", "; break;
                case "Usuario": $sSQL .= "personas_id = ".$iIdItem.", "; break;
                case "Categoria": $sSQL .= "categorias_id = ".$iIdItem.", "; break;
            }

            $sSQL .= " nombreBigSize = ".$this->escStr($oFoto->getNombreBigSize()).", " .
                     " nombreMediumSize = ".$this->escStr($oFoto->getNombreMediumSize()).", " .
                     " nombreSmallSize = ".$this->escStr($oFoto->getNombreSmallSize()).", ".
                     " titulo = ".$this->escStr($oFoto->getTitulo()).", " .
                     " descripcion = ".$this->escStr($oFoto->getDescripcion()).", " .
                     " tipo = ".$this->escStr($oFoto->getTipo())." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();            
            $db->commit();
                        
            $oFoto->setId($iLastId);

            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

   
    /**
     * Dependiendo el filtro el obtener puede devolver las fotos de publicaciones, seguimientos, etc.
     */
    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        f.id as iFotoId,
                        f.nombreBigSize as sFotoNombreBigSize,
                        f.nombreMediumSize as sFotoNombreMediumSize,
                        f.nombreSmallSize as sFotoNombreSmallSize,
                        f.orden as iFotoOrden,
                        f.titulo as sFotoTitulo,
                        f.descripcion as sFotoDescripcion,
                        f.tipo as sFotoTipo
                    FROM
                        fotos f ";

            $WHERE = array();
            
            if(isset($filtro['f.id']) && $filtro['f.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('f.id', $filtro['f.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['f.seguimientos_id']) && $filtro['f.seguimientos_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('f.seguimientos_id', $filtro['f.seguimientos_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['f.fichas_abstractas_id']) && $filtro['f.fichas_abstractas_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('f.fichas_abstractas_id', $filtro['f.fichas_abstractas_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['f.personas_id']) && $filtro['f.personas_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('f.personas_id', $filtro['f.personas_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['f.categorias_id']) && $filtro['f.categorias_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('f.categorias_id', $filtro['f.categorias_id'], MYSQL_TYPE_INT);
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

            $aFotos = array();
            while($oObj = $db->oNextRecord()){
                $oFoto = new stdClass();
                $oFoto->iId = $oObj->iFotoId;
                $oFoto->sNombreBigSize = $oObj->sFotoNombreBigSize;
                $oFoto->sNombreMediumSize = $oObj->sFotoNombreMediumSize;
                $oFoto->sNombreSmallSize = $oObj->sFotoNombreSmallSize;
                $oFoto->iOrden = $oObj->iFotoOrden;
                $oFoto->sTitulo = $oObj->sFotoTitulo;
                $oFoto->sDescripcion = $oObj->sFotoDescripcion;
                $oFoto->sTipo = $oObj->sFotoTipo;

                $aFotos[] = Factory::getFotoInstance($oFoto);
           }

           return $aFotos;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function isFotoPublicacionUsuario($iFotoId, $iUsuarioId)
    {        
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        fotos f
                        JOIN fichas_abstractas fa ON f.fichas_abstractas_id = fa.id
                        LEFT JOIN publicaciones p ON fa.id = p.id
                        LEFT JOIN reviews r ON fa.id = r.id
                      WHERE
                        f.id = ".$this->escInt($iFotoId)." AND
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

    public function isFotoSeguimientoUsuario($iFotoId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        fotos f
                        JOIN seguimientos s ON f.seguimientos_id = s.id
                      WHERE
                        f.id = ".$this->escInt($iFotoId)." AND
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

    public function obtenerFotoDestacada($filtro)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        f.id as iFotoId,
                        f.nombreBigSize as sFotoNombreBigSize,
                        f.nombreMediumSize as sFotoNombreMediumSize,
                        f.nombreSmallSize as sFotoNombreSmallSize,
                        f.orden as iFotoOrden,
                        f.titulo as sFotoTitulo,
                        f.descripcion as sFotoDescripcion,
                        f.tipo as sFotoTipo
                    FROM
                        fotos f ";

            $WHERE = array();

            if(isset($filtro['f.fichas_abstractas_id']) && $filtro['f.fichas_abstractas_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('f.fichas_abstractas_id', $filtro['f.fichas_abstractas_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['f.categorias_id']) && $filtro['f.categorias_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('f.categorias_id', $filtro['f.categorias_id'], MYSQL_TYPE_INT);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            $sSQL .= " order by f.orden asc ";            
            $sSQL .= " limit 1";

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $oObj = $db->oNextRecord();
            
            $oFoto = new stdClass();
            $oFoto->iId = $oObj->iFotoId;
            $oFoto->sNombreBigSize = $oObj->sFotoNombreBigSize;
            $oFoto->sNombreMediumSize = $oObj->sFotoNombreMediumSize;
            $oFoto->sNombreSmallSize = $oObj->sFotoNombreSmallSize;
            $oFoto->iOrden = $oObj->iFotoOrden;
            $oFoto->sTitulo = $oObj->sFotoTitulo;
            $oFoto->sDescripcion = $oObj->sFotoDescripcion;
            $oFoto->sTipo = $oObj->sFotoTipo;
          
            return Factory::getFotoInstance($oFoto);

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }        
    }
    
    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($objects){}
    public function guardar($object){}
}
