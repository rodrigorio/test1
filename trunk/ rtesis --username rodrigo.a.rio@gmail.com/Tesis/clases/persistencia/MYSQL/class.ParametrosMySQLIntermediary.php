<?php

class ParametrosMySQLIntermediary extends ParametrosIntermediary
{    
    private static $instance = null;

    protected function __construct( $conn) {
            parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return ParametrosMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
            if (null === self::$instance){
        self::$instance = new self($conn);
    }
    return self::$instance;
    }

    /**
     * Solo devuelve objetos Parametro de la tabla parametros, no busca en las tablas asociativas
     * como el metodo buscar de esta misma clase.
     */
    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT
                        p.id AS iId,
                        p.descripcion AS sDescripcion,
                        p.tipo AS sTipo,
                        p.namespace AS sNamespace
                    FROM
                        parametros p ";

            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aParametros = array();
            while($oObj = $db->oNextRecord()){
                $oParametro = new stdClass();
                $oParametro->iId = $oObj->iId;
                $oParametro->sDescripcion = $oObj->sDescripcion;
                $oParametro->sNamespace = $oObj->sNamespace;

                $oParametro = Factory::getParametroInstance($oParametro);

                switch($oObj->sTipo){
                    case "string": $oParametro->setTipoCadena(); break;
                    case "boolean": $oParametro->setTipoBooleano(); break;
                    case "numeric": $oParametro->setTipoNumerico(); break;
                }

                $aParametros[] = $oParametro;                
            }
            
            return $aParametros;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }                      
    }

    /**
     * Este devuelve un array de objetos Parametros o que extienden a la clase Parametro
     * (ParametrosSistema, ParametrosControlador, ParametrosUsuario)
     *
     * Como son varias tablas se hace una unificacion.
     */
    public function buscar($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT
                        p.id AS iId,
                        p.descripcion AS sDescripcion,
                        p.tipo AS sTipo,
                        p.namespace AS sNamespace,

                        ps.valor AS sValorS,
                        pc.valor AS sValorC, pc.controladores_pagina_id AS iGrupoIdC,
                        pu.valor AS sValorU, pu.usuarios_id AS iGrupoIdU,

                        cp.controlador AS sGrupoC,
                        CONCAT(pe.nombre, ' ', pe.apellido) AS sGrupoU
                    FROM
                        parametros p
                        LEFT JOIN parametros_sistema ps ON ps.parametros_id = p.id
                        LEFT JOIN parametro_x_controlador_pagina pc ON pc.parametros_id = p.id
                        LEFT JOIN controladores_pagina cp ON pc.controladores_pagina_id = cp.id
                        LEFT JOIN parametro_x_usuario pu ON pu.parametros_id = p.id
                        LEFT JOIN personas pe ON pu.usuarios_id = pe.id ";

            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aParametros = array();
            while($oObj = $db->oNextRecord()){

                //dependiendo el row puede ser Parametro/ParametroSistema/ParametroControlador/ParametroUsuario

                //parametro sistema
                if(null !== $oObj->sValorS){
                    $oParametroSistema = new stdClass();
                    $oParametroSistema->iId = $oObj->iId;
                    $oParametroSistema->sDescripcion = $oObj->sDescripcion;
                    $oParametroSistema->sNamespace = $oObj->sNamespace;
                    $oParametroSistema->sValor = $oObj->sValorS;

                    $oParametroSistema = Factory::getParametroSistemaInstance($oParametroSistema);
                    
                    switch($oObj->sTipo){
                        case "string": $oParametroSistema->setTipoCadena(); break;
                        case "boolean": $oParametroSistema->setTipoBooleano(); break;
                        case "numeric": $oParametroSistema->setTipoNumerico(); break;                        
                    }

                     $aParametros[] = $oParametroSistema;
                     continue;
                }

                //parametro controlador
                if(null !== $oObj->sValorC){
                    $oParametroControlador = new stdClass();
                    $oParametroControlador->iId = $oObj->iId;
                    $oParametroControlador->sDescripcion = $oObj->sDescripcion;
                    $oParametroControlador->sNamespace = $oObj->sNamespace;
                    $oParametroControlador->sValor = $oObj->sValorC;
                    $oParametroControlador->iGrupoId = $oObj->iGrupoIdC;
                    $oParametroControlador->sGrupo = $oObj->sGrupoC;

                    $oParametroControlador = Factory::getParametroControladorInstance($oParametroControlador);

                    switch($oObj->sTipo){
                        case "string": $oParametroControlador->setTipoCadena(); break;
                        case "boolean": $oParametroControlador->setTipoBooleano(); break;
                        case "numeric": $oParametroControlador->setTipoNumerico(); break;
                    }

                     $aParametros[] = $oParametroControlador;
                     continue;
                }
                
                //parametro usuario
                if(null !== $oObj->sValorU){
                    $oParametroUsuario = new stdClass();
                    $oParametroUsuario->iId = $oObj->iId;
                    $oParametroUsuario->sDescripcion = $oObj->sDescripcion;
                    $oParametroUsuario->sNamespace = $oObj->sNamespace;
                    $oParametroUsuario->sValor = $oObj->sValorU;
                    $oParametroUsuario->iGrupoId = $oObj->iGrupoIdU;
                    $oParametroUsuario->sGrupo = $oObj->sGrupoU;

                    $oParametroUsuario = Factory::getParametroUsuarioInstance($oParametroUsuario);

                    switch($oObj->sTipo){
                        case "string": $oParametroUsuario->setTipoCadena(); break;
                        case "boolean": $oParametroUsuario->setTipoBooleano(); break;
                        case "numeric": $oParametroUsuario->setTipoNumerico(); break;
                    }

                     $aParametros[] = $oParametroUsuario;
                     continue;
                }

                //obj parametro simple sin asociar
                if(null === $oObj->sValorU && null === $oObj->sValorC && null === $oObj->sValorS){
                    $oParametro = new stdClass();
                    $oParametro->iId = $oObj->iId;
                    $oParametro->sDescripcion = $oObj->sDescripcion;
                    $oParametro->sNamespace = $oObj->sNamespace;

                    $oParametro = Factory::getParametroInstance($oParametro);

                    switch($oObj->sTipo){
                        case "string": $oParametro->setTipoCadena(); break;
                        case "boolean": $oParametro->setTipoBooleano(); break;
                        case "numeric": $oParametro->setTipoNumerico(); break;
                    }

                    $aParametros[] = $oParametro;
                    continue;
                }
            }

            return $aParametros;

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