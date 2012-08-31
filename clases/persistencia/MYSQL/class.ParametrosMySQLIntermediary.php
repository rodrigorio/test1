<?php

class PermisosMySQLIntermediary extends PermisosIntermediary
{    
    private static $instance = null;


    protected function __construct( $conn) {
            parent::__construct($conn);
    }


    /**
     * Singleton
     *
     * @param mixed $conn
     * @return PermisosMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
            if (null === self::$instance){
        self::$instance = new self($conn);
    }
    return self::$instance;
    }

    /**
     *  El array se arma con las tablas 'controladores_pagina', 'acciones', 'acciones_x_perfil' y 'perfiles'
     *
     *  En la tabla controladores aparecen los diferentes page controllers del sistema. La cadena tiene el formato "modulo_controlador"
     *
     *  En la tabla acciones se relacionan los controladores x accion y a cada accion se le asigna un grupo.
     *
     *  Los id de grupos posibles para las acciones son:
     *      1)ADMIN 2)MODERADOR 3)INTEGANTE ACTIVO 4)INTEGANTE INACTIVO 5)VISITANTES
     *
     */
    public function permisosPorPerfil($iIdPerfil){
        try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        CONCAT_WS('_',cp.`controlador`,a.`accion`),
                        a.`activo`
                        from `perfiles` p
                        join `acciones_x_perfil` ap ON ap.`perfiles_id` = p.`id`
                        join `acciones` a on a.`grupo` =  ap.`grupo`
                        join `controladores_pagina` cp on cp.`id` = a.`controladores_pagina_id`
                        WHERE p.`id` = $iIdPerfil";

            $db->query($sSQL);
            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

            return $db->getDBArrayQuery($sSQL);

        }catch(Exception $e){

            throw new Exception($e->getMessage(), 0);
        }
    }

    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT
                        a.id AS iId,
                        a.controladores_pagina_id AS iControladorId,
                        cp.controlador AS moduloControlador,
                        a.accion AS sNombre,
                        a.grupo AS iGrupoPerfilId,
                        a.activo AS bActivo
                    FROM
                        acciones a JOIN controladores_pagina cp ON cp.id = a.controladores_pagina_id ";

            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aAcciones = array();
            while($oObj = $db->oNextRecord()){
                list($sModulo, $sControlador) = explode("_", $oObj->moduloControlador);

                $oAccion = new stdClass();
                $oAccion->iId = $oObj->iId;
                $oAccion->sModulo = $sModulo;
                $oAccion->sControlador = $sControlador;
                $oAccion->iControladorId = $oObj->iControladorId;
                $oAccion->sNombre = $oObj->sNombre;
                $oAccion->iGrupoPerfilId = $oObj->iGrupoPerfilId;
                $oAccion->bActivo = ($oObj->bActivo == "1") ? true : false;

                $aAcciones[] = Factory::getAccionInstance($oAccion);
            }

            return $aAcciones;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oAccion)
    {
        try{
            if($oAccion->getId() != null){
                return $this->actualizar($oAccion);
            }else{
                return $this->insertar($oAccion);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public  function insertar($oAccion){
        try{
            $db = $this->conn;

            $db->begin_transaction();

            //si el controlador no existe lo creo, sino capturo el id para agregarlo al registro de la accion
            $sSQL = "SELECT id as iControladorId FROM controladores_pagina WHERE controlador = '".$oAccion->getModulo()."_".$oAccion->getControlador()."'";
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){
                //no existia el controlador. lo creo
                $sSQL = "insert into controladores_pagina set controlador = '".$oAccion->getModulo()."_".$oAccion->getControlador()."'";
                $db->execSQL($sSQL);
                $iLastId = $db->insert_id();
                $oAccion->setControladorId($iLastId);
            }else{
                $result = $db->oNextRecord();
                $oAccion->setControladorId($result->iControladorId);
            }

            $activo = ($oAccion->isActivo())?"1":"0";
            $sSQL =	" insert into acciones ".
                    " set controladores_pagina_id = '".$oAccion->getControladorId()."', ".
                    " accion = ".$db->escape($oAccion->getNombre(),true).", ".
                    " grupo = ".$db->escape($oAccion->getGrupoPerfilId(),false,MYSQL_TYPE_INT).", ".
                    " activo = ".$db->escape($activo, false, MYSQL_TYPE_INT)." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();

            $oAccion->setId($iLastId);

            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }

    public  function actualizar($oAccion)
    {
        try{
            $db = $this->conn;

            $activo = ($oAccion->isActivo())?"1":"0";

            //el controlador se mantiene. se actualiza solo la accion
            $sSQL =	" update acciones ".
                    " set ".
                    " accion = ".$db->escape($oAccion->getNombre(),true).", ".
                    " grupo = ".$db->escape($oAccion->getGrupoPerfilId(),false,MYSQL_TYPE_INT).", ".
                    " activo = ".$db->escape($activo, false, MYSQL_TYPE_INT)." ".
                    " where id = ".$db->escape($oAccion->getId(),false,MYSQL_TYPE_INT)." ";

             $db->execSQL($sSQL);
             $db->commit();

             return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }

    public function borrar($oAccion)
    {
        try{
            $db = $this->conn;

            $db->begin_transaction();

            $iControladorId = $oAccion->getControladorId();

            $db->execSQL("delete from acciones where id = '".$oAccion->getId()."'");

            //si era la ultima accion del controlador lo borro. (checkear el borrado en cascada de parametros en db)
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        controladores_pagina cp
                    JOIN
                        acciones a ON cp.id = a.controladores_pagina_id
                    WHERE cp.id = '".$iControladorId."'";

            $db->query($sSQL);
            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){
                $db->execSQL("delete from controladores_pagina where id = '".$iControladorId."'");
            }

            $db->commit();
            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }

    public function existe($filtro)
    {
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        acciones a
                    JOIN
                        controladores_pagina cp ON a.controladores_pagina_id = cp.id
                    WHERE ".$this->crearCondicionSimple($filtro);

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

    public function actualizarCampoArray($objects, $cambios){}
}