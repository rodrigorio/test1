<?php

class NivelMySQLIntermediary extends NivelIntermediary
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
                        n.id as iId,
                        n.descripcion as sDescripcion
                    FROM
                       niveles n ";

            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aNiveles = array();
            while($oObj = $db->oNextRecord()){
            	$oNivel = new stdClass();
            	$oNivel->iId = $oObj->iId;
            	$oNivel->sDescripcion = $oObj->sDescripcion;
            	$aNiveles[] = Factory::getNivelInstance($oNivel);
            }
            
            return $aNiveles;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertar($oNivel)
    {
        try{
            $db = $this->conn;
            $sSQL = " insert into niveles ".
                    " set descripcion = ".$this->escStr($oNivel->getDescripcion())." ";
                    			 
            $db->execSQL($sSQL);

            $iLastId = $db->insert_id();
            $oNivel->setId($iLastId);

            $db->commit();
             
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oNivel)
    {
        try{
            $db = $this->conn;

            $sSQL = " update niveles ".
                    " set descripcion = ".$this->escStr($oNivel->getDescripcion())." ".
                    " where id = ".$this->escInt($oNivel->getId())." ";
                    			 
            $db->execSQL($sSQL);
            $db->commit();
             
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function guardar($oNivel)
    {
        try{
            if($oNivel->getId() !== null){
                return $this->actualizar($oNivel);
            }else{
                return $this->insertar($oNivel);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($oNivel)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from niveles where id = ".$this->escInt($oNivel->getId()));
            $db->commit();
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
		
    public function actualizarCampoArray($objects, $cambios){}
 	
    public function existe($filtro)
    {
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        niveles n 
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
}