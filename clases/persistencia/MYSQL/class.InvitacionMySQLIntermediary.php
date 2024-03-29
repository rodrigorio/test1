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

    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT
                        p.id AS iId,
                        p.email as sEmail,

                        ui.usuarios_id as iUsuarioId,
                        ui.relacion as sRelacion,
                        ui.fecha as dFecha,
                        ui.estado as sEstado,
                        ui.token as sToken,
                        ui.nombre as sNombre,
                        ui.apellido as sApellido
                    FROM
                        usuario_x_invitado ui JOIN invitados i ON i.id = ui.invitados_id
                        JOIN personas p ON i.id = p.id ";

            $WHERE = array();

            if(isset($filtro['ui.token']) && $filtro['ui.token'] != ""){
                $WHERE[] = $this->crearFiltroSimple('ui.token', $filtro['ui.token']);
            }            
            if(isset($filtro['expiracion']) && $filtro['expiracion'] != ""){
                $WHERE[] = " TO_DAYS(NOW()) - TO_DAYS(ui.fecha) <= ".$filtro['expiracion']." ";
            }
            
            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aInvitacion = array();
            while($oObj = $db->oNextRecord()){

                //invitado y usuario
                $oUsuario = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);

                $oInvitado = new stdClass();
                $oInvitado->iId = $oObj->iId;
                $oInvitado->sNombre = $oObj->sNombre;
                $oInvitado->sApellido = $oObj->sApellido;
                $oInvitado->sEmail = $oObj->sEmail;
                $oInvitado = Factory::getInvitadoInstance($oInvitado);

                $oInvitacion = new stdClass();
                $oInvitacion->oInvitado = $oInvitado;
                $oInvitacion->oUsuario = $oUsuario;
                $oInvitacion->dFecha = $oObj->dFecha;
                $oInvitacion->sToken = $oObj->sToken;
                $oInvitacion->sRelacion = $oObj->sRelacion;
                $oInvitacion->sEstado = $oObj->sEstado;
                $oInvitacion = Factory::getInvitacionInstance($oInvitacion);

                $aInvitacion[] = $oInvitacion;
            }

            return $aInvitacion;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        } 
    }    

    public function existe($filtro){
    	try{
            $db = $this->conn;
            
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        usuario_x_invitado ui
                    JOIN
                    	invitados i ON ui.invitados_id = i.id
                    JOIN
                        personas p ON i.id = p.id ";

            $WHERE = array();

            if(isset($filtro['ui.usuarios_id']) && $filtro['ui.usuarios_id'] != ""){
                $WHERE[] = $this->crearFiltroSimple('ui.usuarios_id', $filtro['ui.usuarios_id'], MYSQL_TYPE_INT);
            }            
            if(isset($filtro['p.email']) && $filtro['p.email']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.email', $filtro['p.email']);
            }
            if(isset($filtro['expiracion'])){
                $WHERE[] = " TO_DAYS(NOW()) - TO_DAYS(ui.fecha) <= ".$filtro['expiracion']." ";
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
                        personas p JOIN invitados i ON i.id = p.id ";

            $WHERE = array();

            if(isset($filtro['p.id']) && $filtro['p.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.id', $filtro['p.id']);
            }
            if(isset($filtro['p.email']) && $filtro['p.email']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.email', $filtro['p.email']);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

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
                     AND TO_DAYS(NOW()) - TO_DAYS(fecha) >= ".$iDiasExpiracion." ";
                        
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

    public function actualizar($object){}
    public function actualizarCampoArray($objects, $cambios){}
    public function guardar($object){}
    public function borrar($objects){}
}