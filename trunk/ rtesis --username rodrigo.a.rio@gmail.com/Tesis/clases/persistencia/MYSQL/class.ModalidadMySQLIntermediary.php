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
                       variable_cualitativa_modalidades vcm ";

            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
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
        if(null !== $oVariable->getModalidades()){
            foreach($oVariable->getModalidades() as $oModalidad){
                if(null !== $oModalidad->getId()){
                    $this->actualizar($oModalidad);
                }else{
                    $iVariableId = $oVariable->getId();
                    $this->insertarAsociado($oModalidad, $iVariableId);
                }
            }
        }
    }

    /**
     * Inserta una modalidad asociada a la variable a la cual pertenece.
     * 
     */
    public function insertarAsociado($oModalidad, $iVariableId)
    {
        try{
            $db = $this->conn;
            $iVariableId = $this->escInt($iVariableId);

            $sSQL = " INSERT INTO variable_cualitativa_modalidades SET ".
                     " variables_id = '".$iVariableId."', ".
                     " modalidad = ".$this->escStr($oModalidad->getModalidad()).", ".
                     " orden = ".$this->escInt($oModalidad->getOrden())." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            $db->commit();

            $oModalidad->setId($iLastId);
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oModalidad)
    {
        try{
            $db = $this->conn;

            $sSQL = " UPDATE variable_cualitativa_modalidades SET ".
                    " modalidad = ".$this->escStr($oModalidad->getModalidad()).", ".
                    " orden = ".$this->escInt($oModalidad->getOrden())." ".
                    " where id = ".$this->escInt($oModalidad->getId())." ";
                    			 
            $db->execSQL($sSQL);
            $db->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function borrar($oModalidad)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from variable_cualitativa_modalidades where id = ".$this->escInt($oModalidad->getId()));
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

    public function guardar($object){}
    public function insertar($objects){}
    public function actualizarCampoArray($objects, $cambios){}
}