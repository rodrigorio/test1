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

            $WHERE = array();
            if(isset($filtro['p.id']) && $filtro['p.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.id', $filtro['p.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['pc.controladores_pagina_id']) && $filtro['pc.controladores_pagina_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('pc.controladores_pagina_id', $filtro['pc.controladores_pagina_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['pu.usuarios_id']) && $filtro['pu.usuarios_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('pu.usuarios_id', $filtro['pu.usuarios_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['sistema'])){
                $WHERE[] = " ps.valor <> '' ";
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);
            
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
            if($this->existeParemtroSistema($filtro)){
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
            
            if($this->existeParemtroControlador($filtro)){
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

            if($this->existeParemtroUsuario($filtro)){
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
                    " controladores_pagina_id = ".$this->escInt($oParametroControlador->getGrupoId()).", ";
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
                    " usuarios_id = ".$this->escInt($oParametroUsuario->getGrupoId()).", ";
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
                        
    public function actualizarCampoArray($objects, $cambios){}
}