<?php

/**
 * Description of class ComentarioMySQLIntermediary
 *
 * @author 
 */
class ComentarioMySQLIntermediary extends ComentarioIntermediary
{
    private static $instance = null;

    protected function __construct( $conn) {
            parent::__construct($conn);
    }


    /**
     * Singleton
     *
     * @param mixed $conn
     * @return ComentarioMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    public function guardarComentariosFicha($oFicha)
    {
        if(null !== $oFicha->getComentarios()){
            foreach($oFicha->getComentarios() as $oComentario){
                if(null !== $oComentario->getId()){
                    return $this->actualizar($oComentario);
                }else{
                    $iId = $oFicha->getId();
                    return $this->insertarAsociado($oComentario, $iId, get_class($oFicha));
                }
            }
        }                    
    }

    public function insertarAsociado($oComentario, $iIdItem, $sObjetoAsociado)
    {
        try{
            $db = $this->conn;
            $iIdItem = $this->escInt($iIdItem);

            $sSQL = " INSERT INTO comentarios SET ";

            switch($sObjetoAsociado){
                case "Publicacion": $sSQL .= "publicaciones_id = ".$iIdItem.", "; break;
                case "Review": $sSQL .= "reviews_id = ".$iIdItem.", "; break;
            }

            $iUsuarioId = $oComentario->getUsuario()->getId();

            $sSQL .= " descripcion = ".$this->escStr($oComentario->getDescripcion()).", " .
                     " valoracion = '".$oComentario->getValoracion()."', " .
                     " usuarios_id = '".$iUsuarioId."' ";
            
            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            $db->commit();

            $oComentario->setId($iLastId);
            $oComentario->setFecha(date("d/m/Y"));

            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
	public function actualizar($oComentario)
   {
		try{
			$db = $this->conn;
		        
			$sSQL =	" update comentarios ".
                    " set fecha = '".$oComentario->getFecha()."', ".
                    " descripcion =".$db->escape($oComentario->getDescripcion(),true).", " .
			        " valoracion =".$db->escape($oComentario->getValoracion(),false,MYSQL_TYPE_FLOAT).", " .
                    " where id =".$db->escape($oComentario->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

    public function guardar($oComentario)
    {
        try{
			if($oComentario->getId() != null){
            	return $this->actualizar($oComentario);
            }else{
				return $this->insertar($oComentario);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
	public function borrar($oComentario) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from comentarios where id=".$db->escape($oComentario->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
	
    public function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null) {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        c.id as iId,
                        c.fecha as dFecha,
                        c.descripcion as sDescripcion,
                        c.valoracion as fValoracion,
                        c.usuarios_id as iUsuarioId                        
                    FROM
                        comentarios c ";

            $WHERE = array();

            if(isset($filtro['c.reviews_id']) && $filtro['c.reviews_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.reviews_id', $filtro['c.reviews_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['c.publicaciones_id']) && $filtro['c.publicaciones_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('c.publicaciones_id', $filtro['c.publicaciones_id'], MYSQL_TYPE_INT);
            }                    
            
            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }
            
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aComentario = array();
            while($oObj = $db->oNextRecord()){
                $oComentario                       = new stdClass();
                $oComentario->iId                  = $oObj->iId;
                $oComentario->dFecha               = $oObj->dFecha;
                $oComentario->sDescripcion         = $oObj->sDescripcion;
                $oComentario->fValoracion          = $oObj->fValoracion;
                $oComentario->iUsuarioId           = $oObj->iUsuarioId;
                                
                $aComentarios[] = Factory::getComentarioInstance($oComentario);
            }

            return $aComentarios;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
	
	
    public function actualizarCampoArray($objects, $cambios){}
    
    public function existe($filtro){}
}