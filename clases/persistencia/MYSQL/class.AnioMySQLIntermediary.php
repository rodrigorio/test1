<?php

class AnioMySQLIntermediary extends AnioIntermediary
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
                        anios a
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
            
            $aAnios = array();
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

            	$oAnio = new stdClass();
            	$oAnio->iId = $oObj->iId;
            	$oAnio->sDescripcion = $oObj->sDescripcion;
            	$oAnio->oCiclo = $oCiclo;
            	$aAnios[] = Factory::getAnioInstance($oAnio);
                
            }
            return $aAnios;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public  function insertar($oAnio)
    {
        try{
            $db = $this->conn;
            $sSQL = " insert into anios ".
                    " set descripcion = ".$this->escStr($oAnio->getDescripcion()).", ".
                    " ciclos_id = ".$this->escInt($oAnio->getCiclo()->getId())." ";
			 
            $db->execSQL($sSQL);

            $iLastId = $db->insert_id();
            $oAnio->setId($iLastId);
            
            $db->commit();

        }catch(Exception $e){
                throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oAnio)
    {
        try{
            $db = $this->conn;

            $sSQL = " update anios ".
                    " set descripcion = ".$this->escStr($oAnio->getDescripcion()).", ".
                    " ciclos_id = ".$this->escInt($oAnio->getCiclo()->getId())." ".
                    " where id = ".$this->escInt($oAnio->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oAnio)
    {
        if(null === $oAnio->getCiclo()){
            throw new Exception("El año ".$oAnio->getDescripcion()." no tiene ciclo", 0);
        }
        
        try{
            if($oAnio->getId() !== null){
                return $this->actualizar($oAnio);
            }else{
                return $this->insertar($oAnio);
            }


        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($iAnioId)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from anios where id = ".$this->escInt($iAnioId));
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
                        anios a
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
   public function verificarExisteAnioByDescripcion($sDescripcion, $oCiclo)
    {
    	try{
            $db = $this->conn;
                        
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                     FROM
                        anios a
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