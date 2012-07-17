<?php

class CategoriaMySQLIntermediary extends CategoriaIntermediary
{
    private static $instance = null;

    protected function __construct( $conn) {
        parent::__construct($conn);
    }


    /**
     * Singleton
     *
     * @param mixed $conn
     * @return CategoriaMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn){
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }
	
	
    public function insertar($oCategoria)
    {
        try{
            $db = $this->conn;

            $sSQL = " INSERT INTO categorias SET ".
                    " nombre = ".$db->escape($oCategoria->getNombre(),true).",".
                    " descripcion = ".$db->escape($oCategoria->getDescripcion(),true)." ";
                    			 
            $db->execSQL($sSQL);
            $db->commit();

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public  function actualizar($oCategoria)
    {
        try{
            $db = $this->conn;

            $sSQL = " UPDATE categorias SET ".
                    " nombre = ".$db->escape($oCategoria->getNombre(),true).", " .
                    " descripcion = ".$db->escape($oCategoria->getDescripcion(),true)." " .
                    " WHERE id = ".$db->escape($oCategoria->getId(),false,MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;
          
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function guardar($oCategoria)
    {
        try{
            if($oCategoria->getId() != null){
                return $this->actualizar($oCategoria);
            }else{
                return $this->insertar($oCategoria);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        c.id as iId, c.nombre as sNombre, c.descripcion as sDescripcion, 

                        f.id as iFotoId, f.nombreBigSize as sFotoNombreBigSize,
                        f.nombreMediumSize as sFotoNombreMediumSize, f.nombreSmallSize as sFotoNombreSmallSize,
                        f.orden as iFotoOrden, f.titulo as sFotoTitulo,
                        f.descripcion as sFotoDescripcion, f.tipo as sFotoTipo 
                     FROM
                        categorias c
                     LEFT JOIN
                        fotos f ON f.categorias_id = c.id ";

            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aCategorias = array();
            while($oObj = $db->oNextRecord()){
            	$oCategoria 		= new stdClass();
            	$oCategoria->iId 	= $oObj->iId;
            	$oCategoria->sNombre = $oObj->sNombre;
            	$oCategoria->sDescripcion = $oObj->sDescripcion;

                if(null !== $oObj->iFotoId){
                    $oFoto = new stdClass();
                    $oFoto->iId = $oObj->iFotoId;
                    $oFoto->sNombreBigSize = $oObj->sFotoNombreBigSize;
                    $oFoto->sNombreMediumSize = $oObj->sFotoNombreMediumSize;
                    $oFoto->sNombreSmallSize = $oObj->sFotoNombreSmallSize;
                    $oFoto->iOrden = $oObj->iFotoOrden;
                    $oFoto->sTitulo = $oObj->sFotoTitulo;
                    $oFoto->sDescripcion = $oObj->sFotoDescripcion;
                    $oFoto->sTipo = $oObj->sFotoTipo;
                    
                    $oCategoria->oFoto = Factory::getFotoInstance($oFoto);
                }

            	$aCategorias[] = Factory::getCategoriaInstance($oCategoria);
            }

            return $aCategorias;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
            
    public function borrar($iCategoriaId)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from categorias where id = ".$db->escape($iCategoriaId, false, MYSQL_TYPE_INT));
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
            
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        categorias c ";

            $WHERE = array();

            if(isset($filtro['c.id']) && $filtro['c.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.id', $filtro['c.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['no_c.id']) && $filtro['no_c.id'] != ""){
                $WHERE[] = " c.id <> ".$filtro['no_c.id']." ";
            }
            if(isset($filtro['c.nombre']) && $filtro['c.nombre']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.nombre', $filtro['c.nombre']);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);
            
            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){
            	return false;
            }
            
            return true;
    	}catch(Exception $e){
            throw new Exception($e);
        }
    }
       
    public function actualizarCampoArray($objects, $cambios){}
}