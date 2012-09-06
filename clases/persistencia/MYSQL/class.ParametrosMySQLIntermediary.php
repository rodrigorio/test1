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

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by p.namespace ";
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

            $sSQL = "SELECT parametros_dinamicos.* FROM

                    ((SELECT
                        p.id AS iId,
                        p.descripcion AS sDescripcion,
                        p.tipo AS sTipo,
                        p.namespace AS sNamespace,
                        NULL AS sValor,
                        NULL AS iGrupoId,
                        NULL AS sGrupo,
                        NULL AS tipoAsociacion
                        FROM parametros p
                        LEFT JOIN parametros_sistema ps ON ps.parametros_id = p.id
                        LEFT JOIN parametro_x_controlador_pagina pc ON pc.parametros_id = p.id
                        LEFT JOIN parametro_x_usuario pu ON pu.parametros_id = p.id
                        WHERE ps.valor IS NULL AND pc.valor IS NULL AND pu.valor IS NULL)

                    UNION

                    (SELECT
                        p.id AS iId,
                        p.descripcion AS sDescripcion,
                        p.tipo AS sTipo,
                        p.namespace AS sNamespace,
                        ps.valor AS sValor,
                        NULL AS iGrupoId,
                        NULL AS sGrupo,
                        'sistema' AS tipoAsociacion
                        FROM parametros p
                        JOIN parametros_sistema ps ON ps.parametros_id = p.id)

                    UNION

                    (SELECT
                        p.id AS iId,
                        p.descripcion AS sDescripcion,
                        p.tipo AS sTipo,
                        p.namespace AS sNamespace,
                        pc.valor AS sValor,
                        pc.controladores_pagina_id AS iGrupoId,
                        cp.controlador AS sGrupo,
                        'controlador' AS tipoAsociacion
                        FROM parametros p
                        JOIN parametro_x_controlador_pagina pc ON pc.parametros_id = p.id 
                        JOIN controladores_pagina cp ON cp.id = pc.controladores_pagina_id)

                    UNION

                    (SELECT
                        p.id AS iId,
                        p.descripcion AS sDescripcion,
                        p.tipo AS sTipo,
                        p.namespace AS sNamespace,
                        pu.valor AS sValor,
                        pu.usuarios_id AS iGrupoId,
                        CONCAT(pe.nombre,' ',pe.apellido) AS sGrupo,
                        'usuario' AS tipoAsociacion
                        FROM parametros p
                        JOIN parametro_x_usuario pu ON pu.parametros_id = p.id
                        JOIN personas pe ON pe.id = pu.usuarios_id))
                        AS parametros_dinamicos ";

            $WHERE = array();
            if(isset($filtro['iId']) && $filtro['iId']!=""){
                $WHERE[] = $this->crearFiltroSimple('iId', $filtro['iId'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['iControladorId']) && $filtro['iControladorId']!=""){
                $WHERE[] = " iGrupoId = '".$filtro['iControladorId']."' AND tipoAsociacion = 'controlador' ";
            }
            if(isset($filtro['iUsuarioId']) && $filtro['iUsuarioId']!=""){
                $WHERE[] = " iGrupoId = '".$filtro['iUsuarioId']."' AND tipoAsociacion = 'usuario' ";
            }
            if(isset($filtro['sistema'])){
                $WHERE[] = " tipoAsociacion = 'sistema' ";
            }
            if(isset($filtro['sNamespace']) && $filtro['sNamespace']!=""){
                $WHERE[] = $this->crearFiltroSimple('sNamespace', $filtro['sNamespace']);
            }
            if(isset($filtro['sControlador']) && $filtro['sControlador']!=""){
                $WHERE[] = " sGrupo = '".$filtro['sControlador']."' AND tipoAsociacion = 'controlador' ";
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);
            
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aParametros = array();
            while($oObj = $db->oNextRecord()){

                //dependiendo el row puede ser Parametro/ParametroSistema/ParametroControlador/ParametroUsuario

                //parametro sistema
                if($oObj->tipoAsociacion == 'sistema'){
                    $oParametroSistema = new stdClass();
                    $oParametroSistema->iId = $oObj->iId;
                    $oParametroSistema->sDescripcion = $oObj->sDescripcion;
                    $oParametroSistema->sNamespace = $oObj->sNamespace;
                    $oParametroSistema->sValor = $oObj->sValor;

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
                if($oObj->tipoAsociacion == 'controlador'){
                    $oParametroControlador = new stdClass();
                    $oParametroControlador->iId = $oObj->iId;
                    $oParametroControlador->sDescripcion = $oObj->sDescripcion;
                    $oParametroControlador->sNamespace = $oObj->sNamespace;
                    $oParametroControlador->sValor = $oObj->sValor;
                    $oParametroControlador->iGrupoId = $oObj->iGrupoId;
                    $oParametroControlador->sGrupo = $oObj->sGrupo;

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
                if($oObj->tipoAsociacion == 'usuario'){
                    $oParametroUsuario = new stdClass();
                    $oParametroUsuario->iId = $oObj->iId;
                    $oParametroUsuario->sDescripcion = $oObj->sDescripcion;
                    $oParametroUsuario->sNamespace = $oObj->sNamespace;
                    $oParametroUsuario->sValor = $oObj->sValor;
                    $oParametroUsuario->iGrupoId = $oObj->iGrupoId;
                    $oParametroUsuario->sGrupo = $oObj->sGrupo;

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
                if(null === $oObj->tipoAsociacion){
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

    public function guardar($oParametro)
    {
        try{
            if($oParametro->getId() != null){
                return $this->actualizar($oParametro);
            }else{
                return $this->insertar($oParametro);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public  function insertar($oParametro)
    {
        try{
            $db = $this->conn;

            $sSQL = " INSERT INTO parametros ".
                    " SET namespace = ".$this->escStr($oParametro->getNamespace()).", ".
                    " descripcion = ".$this->escStr($oParametro->getDescripcion()).", ".
                    " tipo = ".$this->escStr($oParametro->getTipo())." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();

            $db->commit();
            $oParametro->setId($iLastId);
            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public  function actualizar($oParametro)
    {
        try{
            $db = $this->conn;

            $sSQL = " UPDATE parametros ".
                    " SET namespace = ".$this->escStr($oParametro->getNamespace()).", ".
                    " descripcion = ".$this->escStr($oParametro->getDescripcion()).", ".
                    " tipo = ".$this->escStr($oParametro->getTipo())." ".
                    " WHERE id = '".$oParametro->getId()."' ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($iParametroId)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $db->execSQL("delete from parametros where id = '".$iParametroId."'");

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
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        parametros p
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

    public function existeParametroSistema($filtro)
    {
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        parametros_sistema ps
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

    public function existeParametroControlador($filtro)
    {
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        parametro_x_controlador_pagina pc
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

    public function existeParametroUsuario($filtro)
    {
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        parametro_x_usuario pu
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

    public function guardarParametroSistema($oParametroSistema)
    {
        try{
            $filtro = array("ps.parametros_id" => $oParametroSistema->getId());
            if($this->existeParametroSistema($filtro)){
                return $this->actualizarParametroSistema($oParametroSistema);
            }else{
                return $this->insertarParametroSistema($oParametroSistema);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardarParametroControlador($oParametroControlador)
    {
        try{
            $filtro = array("pc.parametros_id" => $oParametroControlador->getId(),
                            "pc.controladores_pagina_id" => $oParametroControlador->getGrupoId());
            
            if($this->existeParametroControlador($filtro)){
                return $this->actualizarParametroControlador($oParametroControlador);
            }else{
                return $this->insertarParametroControlador($oParametroControlador);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardarParametroUsuario($oParametroUsuario)
    {
        try{
            $filtro = array("pu.parametros_id" => $oParametroUsuario->getId(),
                            "pu.usuarios_id" => $oParametroUsuario->getGrupoId());

            if($this->existeParametroUsuario($filtro)){
                return $this->actualizarParametroUsuario($oParametroUsuario);
            }else{
                return $this->insertarParametroUsuario($oParametroUsuario);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertarParametroSistema($oParametroSistema)
    {
        try{
            $db = $this->conn;

            $sSQL = " INSERT INTO parametros_sistema ".
                    " SET parametros_id = ".$this->escInt($oParametroSistema->getId()).", ".
                    " valor = ".$this->escStr($oParametroSistema->getValor())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }
    public function actualizarParametroSistema($oParametroSistema)
    {
        try{
            $db = $this->conn;

            $sSQL = " UPDATE parametros_sistema ".
                    " SET valor = ".$this->escStr($oParametroSistema->getValor())." ".
                    " WHERE parametros_id = ".$this->escInt($oParametroSistema->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertarParametroControlador($oParametroControlador)
    {
        try{
            $db = $this->conn;

            $sSQL = " INSERT INTO parametro_x_controlador_pagina ".
                    " SET parametros_id = ".$this->escInt($oParametroControlador->getId()).", ".
                    " controladores_pagina_id = ".$this->escInt($oParametroControlador->getGrupoId()).", ".
                    " valor = ".$this->escStr($oParametroControlador->getValor())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }
    public function actualizarParametroControlador($oParametroControlador)
    {
        try{
            $db = $this->conn;

            $sSQL = " UPDATE parametro_x_controlador_pagina ".
                    " SET valor = ".$this->escStr($oParametroControlador->getValor())." ".
                    " WHERE parametros_id = ".$this->escInt($oParametroControlador->getId())." ".
                    " AND controladores_pagina_id = ".$this->escInt($oParametroControlador->getGrupoId())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertarParametroUsuario($oParametroUsuario)
    {
        try{
            $db = $this->conn;

            $sSQL = " INSERT INTO parametro_x_usuario ".
                    " SET parametros_id = ".$this->escInt($oParametroUsuario->getId()).", ".
                    " usuarios_id = ".$this->escInt($oParametroUsuario->getGrupoId()).", ".
                    " valor = ".$this->escStr($oParametroUsuario->getValor())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }
    public function actualizarParametroUsuario($oParametroUsuario)
    {
        try{
            $db = $this->conn;

            $sSQL = " UPDATE parametro_x_usuario ".
                    " SET valor = ".$this->escStr($oParametroUsuario->getValor())." ".
                    " WHERE parametros_id = ".$this->escInt($oParametroUsuario->getId())." ".
                    " AND usuarios_id = ".$this->escInt($oParametroUsuario->getGrupoId())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrarParametroSistema($oParametroSistema)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $db->execSQL("delete from parametros_sistema where parametros_id = '".$oParametroSistema->getId()."'");

            $db->commit();
            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    public function borrarParametroControlador($oParametroControlador)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $db->execSQL("delete from parametro_x_controlador_pagina 
                          where parametros_id = '".$oParametroControlador->getId()."'
                          and controladores_pagina_id = '".$oParametroControlador->getGrupoId()."'");

            $db->commit();
            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    public function borrarParametroUsuario($oParametroUsuario)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $db->execSQL("delete from parametro_x_usuario
                          where parametros_id = '".$oParametroUsuario->getId()."'
                          and usuarios_id = '".$oParametroUsuario->getGrupoId()."'");

            $db->commit();
            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function obtenerArrayParametrosSistema()
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        p.namespace, ps.valor
                        from parametros p
                        join parametros_sistema ps ON ps.parametros_id = p.id ";

            $db->query($sSQL);
            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

            return $db->getDBArrayQuery($sSQL);

        }catch(Exception $e){

            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function obtenerArrayParametrosControlador($controlador)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        p.namespace, pc.valor
                        from parametros p
                        join parametro_x_controlador_pagina pc ON pc.parametros_id = p.id
                        join controladores_pagina cp ON cp.id = pc.controladores_pagina_id
                        where cp.controlador = ".$this->escStr($controlador)." ";

            $db->query($sSQL);
            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

            return $db->getDBArrayQuery($sSQL);

        }catch(Exception $e){

            throw new Exception($e->getMessage(), 0);
        }
    }

    public function obtenerArrayParametrosUsuario($iUsuarioId)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        p.namespace, pu.valor
                        from parametros p
                        join parametro_x_usuario pu ON pu.parametros_id = p.id
                        where pu.usuarios_id = '".$iUsuarioId."' ";

            $db->query($sSQL);
            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

            return $db->getDBArrayQuery($sSQL);

        }catch(Exception $e){

            throw new Exception($e->getMessage(), 0);
        }
    }
                        
    public function actualizarCampoArray($objects, $cambios){}
}