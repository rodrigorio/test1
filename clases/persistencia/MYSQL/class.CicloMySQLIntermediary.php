<?php

class CicloMySQLIntermediary extends CicloIntermediary
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

            $sSQL = "SELECT
                        c.id as iId, c.descripcion as sDescripcion, c.niveles_id as iNivelesId, 
                        n.descripcion as sDescripcionNivel
                    FROM
                       ciclos c 
                    JOIN niveles n ON c.niveles_id = n.id ";

            if(!empty($filtro)){
                $sSQL .=" WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aCiclos = array();
            while($oObj = $db->oNextRecord()){
                
            	$oCiclo	= new stdClass();
            	$oCiclo->iId = $oObj->iId;
            	$oCiclo->sDescripcion = $oObj->sDescripcion;

                $oNivel = new stdClass();
            	$oNivel->iId = $oObj->iNivelesId;
            	$oNivel->sDescripcion = $oObj->sDescripcionNivel;
            	$oCiclo->oNivel = Factory::getNivelInstance($oNivel);
                
            	$aCiclos[] = Factory::getCicloInstance($oCiclo);
            }

            return $aCiclos;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertar($oCiclo)
    {
        try{            
            $db = $this->conn;
            $sSQL = " insert into ciclos ".
                    " set descripcion = ".$this->escStr($oCiclo->getDescripcion()).", ".
                    " niveles_id = ".$this->escInt($oCiclo->getNivel()->getId())." ";
			 
            $db->execSQL($sSQL);

            $iLastId = $db->insert_id();
            $oCiclo->setId($iLastId);

            $db->commit();
             
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oCiclo)
    {
        try{
            $db = $this->conn;
        
            $sSQL = " update ciclos ".
                    " set descripcion = ".$this->escStr($oCiclo->getDescripcion()).", ".
                    " niveles_id = ".$this->escInt($oCiclo->getNivel()->getId())." ".
                    " where id = ".$this->escInt($oCiclo->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oCiclo)
    {
        if(null === $oCiclo->getNivel()){
            throw new Exception("El ciclo ".$oCiclo->getDescripcion()." no tiene nivel", 0);
        }
        
        try{
            if($oCiclo->getId() !== null){
            	return $this->actualizar($oCiclo);
            }else{
                return $this->insertar($oCiclo);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($oCiclo)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from ciclos where id = ".$this->escInt($oCiclo->getId()));
            $db->commit();
            return true;
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
                        ciclos c 
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
   public function existeCicloByDescripcion($sDescripcion, $oNivel)
    {
    	try{
            $db = $this->conn;
                        
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                     FROM
                        ciclos c 
                     WHERE 
                     c.descripcion = ".$this->escStr($sDescripcion). "
                      AND 
                     c.niveles_id = " .$this->escInt($oNivel->getId());
            
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