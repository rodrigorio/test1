<?php

/**
 * esta clase es bastante simple porque no existe un objeto 'tipo documento'
 * asi que el array se genera a mano.
 */
class DocumentoTiposMySQLIntermediary extends DocumentoTiposIntermediary
{
    private static $instance = null;

    protected function __construct($conn) {
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return GroupMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    public function obtenerTiposDocumentos(){
        try{
            $db = $this->conn;
            $sSQL = "SELECT 
                        dt.id, dt.nombre
                     FROM
                        documento_tipos dt";
            
            $db->query($sSQL);
            return $db->getDBArrayQuery($sSQL);
	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
    public function existe($filtro){}
    public function actualizar($object){}
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($objects){}
    public function guardar($object){}
    public function borrar($objects){}
}

