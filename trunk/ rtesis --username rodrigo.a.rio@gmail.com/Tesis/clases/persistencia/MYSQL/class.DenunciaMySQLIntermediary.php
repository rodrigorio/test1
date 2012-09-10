<?php

class DenunciaMySQLIntermediary extends DenunciaIntermediary
{
    private static $instance = null;

    protected function __construct( $conn) {
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return ComentarioMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    /**
     * Tienen que corresponder con el enum de la tabla denuncias
     *
     * el valor de las celdas es una descripcion para utilizar en vistas.
     */
    public function obtenerRazonesDenuncia()
    {
        return array('informacion_falsa' => 'Información falsa',
                     'contenido_inapropiado' => 'Contenido inapropiado',
                     'propiedad_intelectual' => 'Propiedad Intelectual',
                     'spam' => 'Spam o basura');
    }

    /**
     * polimorfico para todas las entidades del sistema que pueden ser denunciadas
     *
     */
    public function guardarDenunciasEntidad($oObj)
    {
        if(null !== $oObj->getDenuncias()){
            foreach($oObj->getDenuncias() as $oDenuncia){
                if(null !== $oDenuncia->getId()){
                    return $this->actualizar($oDenuncia);
                }else{
                    $iId = $oObj->getId();
                    return $this->insertarAsociado($oDenuncia, $iId, get_class($oObj));
                }
            }
        }
    }

    public function insertarAsociado($oDenuncia, $iIdItem, $sObjetoAsociado)
    {
        try{
            $db = $this->conn;
            $iIdItem = $this->escInt($iIdItem);
            $iUsuarioId = $this->escInt($oDenuncia->getUsuario()->getId());

            $sSQL = " INSERT INTO denuncias SET ";

            switch($sObjetoAsociado){
                case "Publicacion": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "Review": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "Software": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "Institucion": $sSQL .= "instituciones_id = ".$iIdItem.", "; break;
            }

            $sSQL .= " mensaje = ".$this->escStr($oDenuncia->getMensaje()).", ".
                     " usuarios_id = ".$iUsuarioId.", ".
                     " razon = ".$this->escStr($oDenuncia->getRazon())." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            $db->commit();

            $oDenuncia->setId($iLastId);
            $oDenuncia->setFecha(date("Y/m/d"));

            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizar($oDenuncia)
    {
        try{
            $db = $this->conn;
            
            $sSQL = "UPDATE denuncias SET ".
            " razon = ".$this->escStr($oDenuncia->getRazon()).", ".
            " mensaje = ".$this->escStr($oDenuncia->getMensaje())." ".
            " WHERE id = ".$this->escInt($oDenuncia->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oDenuncia)
    {
        try{
            if($oDenuncia->getId() != null){
                return $this->actualizar($oDenuncia);
            }else{
                return $this->insertar($oDenuncia);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($iDenunciaId)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from denuncias where id = '".$iDenunciaId."'");
            $db->commit();
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * polimorfico para todas las entidades del sistema que pueden ser denunciadas
     * con este metodo se limpian todas las denuncias de una entidad.
     */
    public function borrarDenunciasEntidad($oObj)
    {
        try{
            if(null !== $oObj->getDenuncias()){

                $sObjetoAsociado = get_class($oObj);
                $iIdItem = $oObj->getId();

                $db = $this->conn;

                $sSQL = "delete from denuncias where ";

                switch($sObjetoAsociado){
                    case "Publicacion": $sSQL .= "fichas_abstractas_id = ".$iIdItem." "; break;
                    case "Review": $sSQL .= "fichas_abstractas_id = ".$iIdItem." "; break;
                    case "Software": $sSQL .= "fichas_abstractas_id = ".$iIdItem." "; break;
                    case "Institucion": $sSQL .= "instituciones_id = ".$iIdItem." "; break;
                }
                
                $db->execSQL($sSQL);
                $db->commit();

                $oObj->setDenuncias(null);
            }

            return true;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null) {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        d.id as iId,
                        d.usuarios_id as iUsuarioId,
                        d.fecha as dFecha,
                        d.mensaje as sMensaje,
                        d.razon as sRazon
                    FROM
                        denuncias d ";

            $WHERE = array();

            if(isset($filtro['d.id']) && $filtro['d.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('d.id', $filtro['d.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['d.fichas_abstractas_id']) && $filtro['d.fichas_abstractas_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('d.fichas_abstractas_id', $filtro['d.fichas_abstractas_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['d.instituciones_id']) && $filtro['d.instituciones_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('d.instituciones_id', $filtro['d.instituciones_id'], MYSQL_TYPE_INT);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by fecha asc ";
            }
            
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }
            
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aDenuncias = array();
            while($oObj = $db->oNextRecord()){
                $oDenuncia = new stdClass();
                $oDenuncia->iId = $oObj->iId;
                $oDenuncia->dFecha = $oObj->dFecha;
                $oDenuncia->sMensaje = $oObj->sMensaje;
                $oDenuncia->sRazon = $oObj->sRazon;
                $oDenuncia->oUsuario = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);

                $aDenuncias[] = Factory::getDenunciaInstance($oDenuncia);
            }

            return $aDenuncias;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function existe($filtro)
    {
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        denuncias d
                    WHERE ".$this->crearCondicionSimple($filtro);

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
    
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($oComentario){}    
}