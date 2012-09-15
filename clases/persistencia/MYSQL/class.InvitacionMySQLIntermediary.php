<?php

class InvitacionMySQLIntermediary extends InvitacionIntermediary
{
    private static $instance = null;

    protected function __construct($conn) {
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return InvitacionMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        usuario_x_invitado ui
                    JOIN
                    	invitados i ON ui.invitados_id = i.id
                    JOIN
                        personas p ON i.id = p.id ";

            $WHERE = array();
            
            if(isset($filtro['ui.usuarios_id']) && $filtro['ui.usuarios_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('ui.usuarios_id', $filtro['ui.usuarios_id']);
            }            
            if(isset($filtro['p.email']) && $filtro['p.email']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.email', $filtro['p.email']);
            }
            if(isset($filtro['expiracion'])){
                $WHERE[] = " DATE_SUB(ui.fecha,INTERVAL ".$filtro['expiracion']." DAY) <= now() ";
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

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

    public function insertar($oInvitacion)
    {
        try{
            $db = $this->conn;

            $db->begin_transaction();
            
            //inserto en personas solo si la persona es invitada por primera vez.
            if($oInvitacion->getInvitado()->getId() === null){
                $sSQL = "INSERT INTO personas SET email = ".$this->escStr($oInvitacion->getInvitado()->getEmail())." ";
                $db->execSQL($sSQL);
                $oInvitacion->getInvitado()->setId($db->insert_id());

                $db->execSQL("INSERT INTO invitados SET id = '".$db->insert_id()."'");
            }

            //guardo la invitacion propiamente
            $time = time();
            $token = md5($time."invitacion");

            $sSQL = " INSERT INTO usuario_x_invitado SET ".
                    " usuarios_id = '".$oInvitacion->getUsuario()->getId()."', ".
                    " invitados_id = '".$oInvitacion->getInvitado()->getId()."', ".
                    " relacion = '".$oInvitacion->getRelacion()."', ".
                    " nombre = '".$oInvitacion->getInvitado()->getNombre()."', ".
                    " apellido = '".$oInvitacion->getInvitado()->getApellido()."', ".
                    " token = '".$token."' ";
            
            $db->execSQL($sSQL);
            
            $sSQL = " UPDATE usuarios SET ".
                    " invitacionesDisponibles = invitacionesDisponibles-1 ".
                    " WHERE id = '".$oInvitacion->getUsuario()->getId()."' ";
            
            $db->execSQL($sSQL);

            $db->commit();

            $oInvitacion->setToken($token);
            $oInvitacion->setFecha(date("Y/m/d"));
            $iInvitacionesDisponibles = $oInvitacion->getUsuario()->getInvitacionesDisponibles() - 1;
            $oInvitacion->getUsuario()->setInvitacionesDisponibles($iInvitacionesDisponibles);

            return  true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw $e;
        }
    }

    public function obtenerInvitados($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT
                        p.id AS iId,
                        p.email as sEmail
                    FROM
                        invitados i JOIN personas p ON i.id = p.id ";

            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aInvitados = array();
            while($oObj = $db->oNextRecord()){
                $oInvitado = new stdClass();
                $oInvitado->iId = $oObj->iId;
                $oInvitado->sEmail = $oObj->sEmail;

                $oInvitado = Factory::getInvitadoInstance($oInvitado);

                $aInvitados[] = $oInvitado;
            }

            return $aInvitados;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        } 
    }

    public function borrarInvitacionesExpiradasUsuario($iUsuarioId, $iDiasExpiracion)
    {
        try{
            $db = $this->conn;
            
            $db->begin_transaction();

            //borro las invitaciones expiradas
            $sSQL = "DELETE FROM usuario_x_invitado 
                     WHERE usuarios_id = ".$iUsuarioId." 
                     AND DATE_SUB(fecha,INTERVAL ".$iDiasExpiracion." DAY) > now() ";

            $db->execSQL($sSQL);
            
            //borro los invitados que quedaron sin invitaciones, si es que quedaron
            $sSQL = "DELETE FROM personas WHERE id IN
                     (SELECT i.id
                      FROM invitados i LEFT JOIN usuario_x_invitado ui ON i.id = ui.invitados_id
                      WHERE ui.fecha IS NULL) ";

            $db->execSQL($sSQL);

            $db->commit();
            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
    public function actualizar($object){}
    public function actualizarCampoArray($objects, $cambios){}
    public function guardar($object){}
    public function borrar($objects){}
}