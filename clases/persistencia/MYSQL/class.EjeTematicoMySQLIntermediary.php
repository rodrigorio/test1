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
                        e.id as iId, e.descripcion as sDescripcion, 
                        e.contenidos as sContenidos, 
                        e.areas_id as iAreaId, 
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
                    " contenidos = ".$this->escStr($oEjeTematico->getContenidos()).", ".
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
                    " contenidos = ".$this->escStr($oEjeTematico->getContenidos()).", ".
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
    public function borrarEjeTematicoDiagnosticoSCC($iDiagnosticoSCCId, $iEjeTematicoId)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from diagnosticos_scc_x_ejes 
                          where diagnosticos_scc_id = ".$this->escInt($iDiagnosticoSCCId)."
                          and ejes_id IN (".$this->escInt($iEjeTematicoId).")" );
            $db->commit();
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function existeEjeTematicoDiagnosticoSCC($iDiagnosticoSCCId, $iEjeTematicoId)
    {
    	try{
            $db = $this->conn;
            
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        diagnosticos_scc_x_ejes dxe
                    WHERE 
                        dxe.diagnosticos_scc_id = ".$this->escInt($iDiagnosticoSCCId)."
                    AND
                        dxe.ejes_id = ".$this->escInt($iEjeTematicoId);

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

   
    public function guardarEjeTematicoDiagnosticoSCC($iDiagnosticoSCCId, $oEjeTematico)
    {
        try{
            if($this->existeEjeTematicoSeguimientoSCC($iDiagnosticoSCCId, $oEjeTematico->getId())){
                return $this->actualizarEjeTematicoDiagnosticoSCC($iDiagnosticoSCCId, $oEjeTematico);
            } else {
                return $this->asociarEjeTematicoDiagnosticoSCC($iDiagnosticoSCCId, $oEjeTematico);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 0);
        }        
    }
     /**
     * El controlador de seguimiento con el metodo "isDiagnosticoSeguimientoUsuario" tiene que verificar que es el diagnostico de un seguimiento que haya
     * creado el usuario que esta en sesion
     */

    public function asociarEjeTematicoDiagnosticoSCC($iDiagnosticoSCCId, $vEjeTematico)
    {
        try{
            $db = $this->conn;
            $sSQL = " insert into diagnosticos_scc_x_ejes (diagnosticos_scc_id, ejes_id, estadoInicial) VALUES ";
           
            for ($i=0; $i< count($vEjeTematico); $i++) {
            	$oEjeTematico = $vEjeTematico[$i];
            	$sSQL .= " (".$this->escInt($iDiagnosticoSCCId).", "
            		.$this->escInt($oEjeTematico->getId()).", "
            		.$this->escStr($oEjeTematico->getEstadoInicial()).") ";
            	if (count($vEjeTematico) > $i+1) {
            		$sSQL .= ",";
            	}
            } 
                      
            $db->execSQL($sSQL);
            $db->commit();

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizarEjeTematicoDiagnosticoSCC($iDiagnosticoSCCId, $oEjeTematico)
    {
        try{
            $db = $this->conn;
            $sSQL = " update diagnosticos_scc_x_ejes set ".
                    " estadoInicial = ".$this->escStr($oEjeTematico->getEstadoInicial())." ".
                    " WHERE
                        dxe.diagnosticos_scc_id = ".$this->escInt($iSeguimientoSCCId)."
                      AND
                        dxe.ejes_id = ".$this->escInt($oEjeTematico->getId());

            $db->execSQL($sSQL);
            $db->commit();

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
      public function isEjeTematicoDiagnosticoUsuario($iDiagnosticoId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        seguimientos_scc sd
                      JOIN 
                      	seguimientos s 
                      ON
                      	sd.id = s.id
                      WHERE
                        sd.diagnostico_scc_id = ".$this->escInt($iDiagnosticoId)." AND
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

   public function verificarExisteEjeByDescripcion($sDescripcion, $oArea)
    {
    	try{
            $db = $this->conn;
                        
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                     FROM
                        ejes e 
                     WHERE 
                     e.descripcion = ".$this->escStr($sDescripcion)." 
                      AND 
                     e.areas_id = ".$this->escInt($oArea->getId());
            
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