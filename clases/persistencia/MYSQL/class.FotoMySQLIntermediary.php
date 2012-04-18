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
            
            $sSQL = " UPDATE fotos SET" .
                    " nombreBigSize = ".$this->escStr($oFoto->getNombreBigSize()).", " .
                    " nombreMediumSize = ".$this->escStr($oFoto->getNombreMediumSize()).", " .
                    " nombreSmallSize = ".$this->escStr($oFoto->getNombreSmallSize()).", ".
                    " orden = ".$this->escInt($oFoto->getOrden()).", " .
                    " titulo = ".$this->escStr($oFoto->getTitulo()).", " .
                    " descripcion = ".$this->escStr($oFoto->getDescripcion()).", " .
                    " tipo = ".$this->escStr($oFoto->getTipo()).", " .
                    " WHERE id = ".$this->escInt($oFoto->getId());

            $db->execSQL($sSQL);

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


    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    
    
    }
    
    /**
     * Dependiendo el filtro el obtener puede devolver las fotos de publicaciones, seguimientos, etc.
     */
    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        f.id as iFotoId, f.nombreBigSize as sFotoNombreBigSize,
                        f.nombreMediumSize as sFotoNombreMediumSize, f.nombreSmallSize as sFotoNombreSmallSize,
                        f.orden as iFotoOrden, f.titulo as sFotoTitulo,
                        f.descripcion as sFotoDescripcion, f.tipo as sFotoTipo
                    FROM
                        fotos f ";

            $WHERE = array();
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
    

    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($objects){}
    public function guardar($object){}
}