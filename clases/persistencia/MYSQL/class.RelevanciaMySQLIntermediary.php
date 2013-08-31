<?php
 
class RelevanciaMySQLIntermediary extends RelevanciaIntermediary
{

    const RELEVANCIA_ALTA = 3;
    const RELEVANCIA_NORMAL = 2;
    const RELEVANCIA_BAJA = 1;
        
    private static $instance = null;

    protected function __construct( $conn) {
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return EspecialidadMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn)
    {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }
    
    public function obtenerRelevancias()
    {
        return array('alta' => self::RELEVANCIA_ALTA,
                     'normal' => self::RELEVANCIA_NORMAL,
                     'baja' => self::RELEVANCIA_BAJA);
    }

    public final function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT
                        objr.id as iId,
                        objr.descripcion as sDescripcion
                     FROM
                        objetivo_relevancias objr ";

            if(!empty($filtro)){
                $sSQL .= " WHERE ".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null;}

            $aRelevancias = array();
            while($oObj = $db->oNextRecord()){
                $oRelevancia = new stdClass();
                $oRelevancia->iId = $oObj->iId;
                $oRelevancia->sDescripcion = $oObj->sDescripcion;
                $aRelevancias[] = Factory::getRelevanciaInstance($oRelevancia);
            }

            return $aRelevancias;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public  function insertar($oEspecialidad){}
    public  function actualizar($oEspecialidad){}
    public function guardar($oEspecialidad){}
    public function borrar($iEspecialidadId){}
    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
}