<?php

class ModalidadMySQLIntermediary extends ModalidadIntermediary
{
    private static $instance = null;

    protected function __construct( $conn) {
        parent::__construct($conn);
    }

    public static function &getInstance(IMYSQL $conn){
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }
	
    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        vcm.id as iId,
                        vcm.modalidad as sModalidad,
                        vcm.orden as iOrden
                    FROM
                       variable_cualitativa_modalidades vcm
                    WHERE
                       vcm.borradoLogico = 0 ";

            if(!empty($filtro)){
                $sSQL .= "AND ".$this->crearCondicionSimple($filtro);
            }

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by orden asc ";
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aModalidades = array();
            while($oObj = $db->oNextRecord()){
            	$oModalidad = new stdClass();
            	$oModalidad->iId = $oObj->iId;
            	$oModalidad->sModalidad = $oObj->sModalidad;
                $oModalidad->iOrden = $oObj->iOrden;
                
            	$aModalidades[] = Factory::getModalidadInstance($oModalidad);
            }
            
            return $aModalidades;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardarModalidadesVariableCualitativa(VariableCualitativa $oVariable)
    {        
        try
        {
            if(null !== $oVariable->getModalidades()){                                
                $db = $this->conn;
                $db->begin_transaction();                
                foreach($oVariable->getModalidades() as $oModalidad){                    
                    if(null !== $oModalidad->getId()){
                        $this->actualizar($oModalidad);
                    }else{
                        $iVariableId = $oVariable->getId();
                        $this->insertarAsociado($oModalidad, $iVariableId);
                    }
                }
                $db->commit();                
            }
            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Inserta una modalidad asociada a la variable a la cual pertenece.
     * 
     */
    public function insertarAsociado($oModalidad, $iVariableId)
    {
        try{
            $iVariableId = $this->escInt($iVariableId);

            $sSQL = " INSERT INTO variable_cualitativa_modalidades SET ".
                     " variables_id = '".$iVariableId."', ".
                     " modalidad = ".$this->escStr($oModalidad->getModalidad()).", ".
                     " orden = ".$this->escInt($oModalidad->getOrden())." ";           

            $this->conn->execSQL($sSQL);            
            $oModalidad->setId($this->conn->insert_id());
            
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oModalidad)
    {
        try{
            $sSQL = " UPDATE variable_cualitativa_modalidades SET ".
                    " modalidad = ".$this->escStr($oModalidad->getModalidad()).", ".
                    " orden = ".$this->escInt($oModalidad->getOrden())." ".
                    " where id = ".$this->escInt($oModalidad->getId())." ";
                    			 
            $this->conn->execSQL($sSQL);
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borradoLogico($iModalidadId)
    {
        try{
            $sSQL = " UPDATE variable_cualitativa_modalidades SET ".
                    " borradoLogico = 1 ".
                    " where id = ".$this->escInt($iModalidadId)." ";
            $db->execSQL($sSQL);
            $this->conn->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function borrar($iModalidadId)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from variable_cualitativa_modalidades where id = ".$this->escInt($iModalidadId));
            $db->commit();
            return true;
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
                        variable_cualitativa_modalidades vcm 
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

    public function isModalidadVariableUsuario($iModalidadId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        variable_cualitativa_modalidades vcm
                        JOIN variables v ON vcm.variables_id = v.id 
                        JOIN unidades u ON v.unidad_id = u.id
                      WHERE
                        vcm.id = ".$this->escInt($iModalidadId)." AND
                        u.usuarios_id = ".$this->escInt($iUsuarioId)." ";

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

    /**
     * Devuelve true si la modalidad se selecciono como valor de una variable cualitativa asociada a un seguimiento de un usuario.
     */
    public function isUtilizadaEnSeguimientoUsuario($iModalidadId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        variable_cualitativa_modalidades vcm
                        JOIN variables v ON vcm.variables_id = v.id
                        JOIN seguimiento_x_contenido_variables scv ON scv.variable_id = v.id
                        JOIN seguimientos s ON scv.seguimiento_id = s.id 
                      WHERE
                        vcm.id = ".$this->escInt($iModalidadId)." AND
                        s.usuarios_id = ".$this->escInt($iUsuarioId)." AND
                        scv.valorNumerico = ".$this->escInt($iModalidadId)." ";

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

    public function guardar($object){}
    public function insertar($objects){}
    public function actualizarCampoArray($objects, $cambios){}
}