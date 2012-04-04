<?php
class PracticaMySQLIntermediary extends PracticaIntermediary
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
    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    }
    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($objects){}
    public function guardar($object){}
    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
    public function borrar($oPractica) {}
    public function actualizar($oPractica){}
}
