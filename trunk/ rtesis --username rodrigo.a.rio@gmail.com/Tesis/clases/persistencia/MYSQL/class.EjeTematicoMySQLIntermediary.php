<?php

class EjeTematicoMySQLIntermediary extends EjeTematicoIntermediary
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
                        e.id as iId, e.descripcion as sDescripcion, e.contenidos as sContenidos, e.areas_id as iAreaId, 
                        a.descripcion as sDescripcionArea, a.ciclos_id as iCicloId,
                        c.descripcion as sDescripcionCiclo, c.niveles_id as iNivelId,
                        n.descripcion as sDescripcionNivel
                    FROM
                        ejes e 
                    JOIN 
                    	areas a ON e.areas_id = a.id
                    JOIN
                        ciclos c ON a.ciclos_id = c.id
                    JOIN
                        niveles n ON c.niveles_id = n.id";
            
            if(!empty($filtro)){
                $sSQL .= " WHERE ".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aEjesTematicos = array();
            while($oObj = $db->oNextRecord()){
                
            	$oEjeTematico = new stdClass();
            	$oEjeTematico->iId = $oObj->iId;
            	$oEjeTematico->sDescripcion = $oObj->sDescripcion;
            	$oEjeTematico->sContenidos = $oObj->sContenidos;

                $oArea = new stdClass();
            	$oArea->iId = $oObj->iAreaId;
            	$oArea->sDescripcion = $oObj->sDescripcionArea;
                $oArea = Factory::getAreaInstance($oArea);

            	$oCiclo	= new stdClass();
            	$oCiclo->iId = $oObj->iCicloId;
            	$oCiclo->sDescripcion = $oObj->sDescripcionCiclo;
                $oCiclo = Factory::getCicloInstance($oCiclo);

            	$oNivel = new stdClass();
            	$oNivel->iId = $oObj->iNivelId;
            	$oNivel->sDescripcion = $oObj->sDescripcionNivel;
            	$oNivel = Factory::getNivelInstance($oNivel);
                
            	$oCiclo->setNivel($oNivel);
                $oArea->setCiclo($oCiclo);
                $oEjeTematico->oArea = $oArea;

            	$aEjesTematicos[] = Factory::getEjeTematicoInstance($oEjeTematico);
            }

            return $aEjesTematicos;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertar($oEjeTematico)
    {
        try{
            $db = $this->conn;
            $sSQL = " insert into ejes ".
                    " set descripcion = ".$this->escStr($oEjeTematico->getDescripcion()).", ".
                    " set contenidos = ".$this->escStr($oEjeTematico->getContenidos()).", ".
                    " areas_id = ".$this->escInt($oEjeTematico->getArea()->getId())." ";
			 
            $db->execSQL($sSQL);

            $iLastId = $db->insert_id();
            $oEjeTematico->setId($iLastId);

            $db->commit();
             
        }catch(Exception $e){
                throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oEjeTematico)
    {
        try{
            $db = $this->conn;
        
            $sSQL = " update ejes ".
                    " set descripcion = ".$this->escStr($oEjeTematico->getDescripcion()).", ".
                    " set contenidos = ".$this->escStr($oEjeTematico->getContenidos()).", ".
                    " areas_id = ".$this->escInt($oEjeTematico->getArea()->getId())." ".
                    " where id = ".$this->escInt($oEjeTematico->getId())." ";

             $db->execSQL($sSQL);
             $db->commit();
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function guardar($oEjeTematico)
    {
        if(null === $oEjeTematico->getArea()){
            throw new Exception("El Eje Tematico ".$oEjeTematico->getDescripcion()." no tiene Area", 0);
        }
        
        try{
            if ($oEjeTematico->getId() !== null) {
                return $this->actualizar($oEjeTematico);
            } else {
                return $this->insertar($oEjeTematico);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($oEjeTematico)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from ejes where id = ".$this->escInt($oEjeTematico->getId()));
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
                        ejes e
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

    //estos metodos son para el abm de asociaciones de un EjeTematico a un seguimiento SCC
    public function borrarEjeTematicoSeguimientoSCC($iSeguimientoSCCId, $iEjeTematicoId){}
    public function existeEjeTematicoSeguimientoSCC($iSeguimientoSCCId, $iEjeTematicoId){}
    public function guardarEjeTematicoSeguimientoSCC($iSeguimientoSCCId, $oEjeTematico){}
    public function asociarEjeTematicoSeguimientoSCC($iSeguimientoSCCId, $oEjeTematico){}
    public function actualizarEjeTematicoSeguimientoSCC($iSeguimientoSCCId, $oEjeTematico){}
}