<?php

class AreaMySQLIntermediary extends AreaIntermediary
{
    private static $instance = null;

    protected function __construct( $conn){
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
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        a.id as iId, a.descripcion as sDescripcion, a.ciclos_id as iCicloId,  
                        c.descripcion as sDescripcionCiclo, c.niveles_id as iNivelId,
                        n.descripcion as sDescripcionNivel
                    FROM
                        areas a 
                    JOIN 
                    	ciclos c ON a.ciclos_id = c.id
                    JOIN
                        niveles n ON c.niveles_id = n.id ";
            
            if(!empty($filtro)){
                $sSQL .= " WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aAreas = array();
            while($oObj = $db->oNextRecord()){

            	$oNivel = new stdClass();
            	$oNivel->iId = $oObj->iNivelId;
            	$oNivel->sDescripcion = $oObj->sDescripcionNivel;
            	$oNivel = Factory::getNivelInstance($oNivel);

                $oCiclo = new stdClass();
                $oCiclo->iId = $oObj->iCicloId;
                $oCiclo->sDescripcion = $oObj->sDescripcionCiclo;
            	$oCiclo = Factory::getCicloInstance($oCiclo);
                $oCiclo->setNivel($oNivel);

            	$oArea = new stdClass();
            	$oArea->iId = $oObj->iId;
            	$oArea->sDescripcion = $oObj->sDescripcion;
            	$oArea->oCiclo = $oCiclo;
            	$aAreas[] = Factory::getAreaInstance($oArea);
                
            }
            return $aAreas;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public  function insertar($oArea)
    {
        try{
            $db = $this->conn;
            $sSQL = " insert into areas ".
                    " set descripcion = ".$this->escStr($oArea->getDescripcion()).", ".
                    " ciclos_id = ".$this->escInt($oArea->getCiclo()->getId())." ";
			 
            $db->execSQL($sSQL);

            $iLastId = $db->insert_id();
            $oArea->setId($iLastId);
            
            $db->commit();

        }catch(Exception $e){
                throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oArea)
    {
        try{
            $db = $this->conn;

            $sSQL = " update areas ".
                    " set descripcion = ".$this->escStr($oArea->getDescripcion()).", ".
                    " ciclos_id = ".$this->escInt($oArea->getCiclo()->getId())." ".
                    " where id = ".$this->escInt($oArea->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oArea)
    {
        if(null === $oArea->getCiclo()){
            throw new Exception("El area ".$oArea->getDescripcion()." no tiene ciclo", 0);
        }
        
        try{
            if($oArea->getId() !== null){
                return $this->actualizar($oArea);
            }else{
                return $this->insertar($oArea);
            }


        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($oArea)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from areas where id = ".$this->escInt($oArea->getId()));
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
                        areas a 
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
   public function verificarExisteAreaByDescripcion($sDescripcion, $oCiclo)
    {
    	try{
            $db = $this->conn;
                        
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                     FROM
                        areas a 
                     WHERE 
                     a.descripcion = ".$this->escStr($sDescripcion). "
                      AND 
                     a.ciclos_id = " .$this->escInt($oCiclo->getId());
            
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