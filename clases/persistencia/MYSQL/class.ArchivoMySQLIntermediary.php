<?php

class ArchivoMySQLIntermediary extends ArchivoIntermediary
{
    public function guardarCurriculumVitae($oUsuario)
    {
        $iIdUsuario = $oUsuario->getId();
        $oArchivo = $oUsuario->getCurriculumVitae();

        if(null !== $oArchivo->getId()){
            $this->actualizar($oArchivo);
        }else{
            $this->insertarAsociado($oArchivo, $iIdUsuario, get_class($oUsuario));
        }
    }    

    public function borrar($aArchivos)
    {
        try{
            $db = $this->conn;

            if(is_array($aArchivos)){
                $db->begin_transaction();
                foreach($aArchivos as $oArchivo){
                    $db->execSQL("DELETE FROM archivos WHERE id = ".$this->escInt($oArchivo->getId()));
                }
                $db->commit();
                return true;
            }else{
                $db->execSQL("DELETE FROM archivos WHERE id = ".$this->escInt($oArchivo->getId()));
                return true;
            }
            
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }        
    }

    public function actualizar($oArchivo){
        try{
            $db = $this->conn;

            $moderado = $oArchivo->isModerado() ? "1" : "0";
            $activo = $oArchivo->isActivo() ? "1" : "0";
            $publico = $oArchivo->isPublico() ? "1" : "0";
            $activoComentarios = $oArchivo->isActivoComentarios() ? "1" : "0";

            $sSQL = " update archivos " .
                    " set nombre = ".$this->escStr($oArchivo->getNombre()).", " .
                    " nombreServidor = ".$this->escStr($oArchivo->getNombreServidor()).", " .
                    " descripcion = ".$this->escStr($oArchivo->getDescripcion()).", ".
                    " tipoMime = ".$this->escStr($oArchivo->getTipoMime()).", " .
                    " tamanio = ".$this->escInt($oArchivo->getTamanio()).", " .
                    " fechaAlta = '".$oArchivo->getFechaAlta()."', ".
                    " orden = ".$this->escInt($oArchivo->getOrden()).", " .
                    " titulo = ".$this->escStr($oArchivo->getTitulo()).", " .
                    " tipo = ".$this->escStr($oArchivo->getTipo()).", " .
                    " moderado = ".$this->escInt($moderado).", " .
                    " activo = ".$this->escInt($activo).", " .
                    " publico = ".$this->escInt($publico).", " .
                    " activoComentarios = ".$this->escInt($activoComentarios).
                    " WHERE id = ".$this->escInt($oArchivo->getId());

            $db->execSQL($sSQL);

            return true;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Inserta un archivo en DB, recibe 2 parametros extra para guardar el id del
     * objeto al que se asocia. Esto solo se precisa al insertar un nuevo archivo.
     *
     * @param Archivo $oArchivo
     * @param integer $iIdItem
     * @param string $sObjetoAsociado El nombre de la clase del objeto que tiene asociado el archivo. (seguimiento, usuario, categoria, etc)
     */
    public function insertarAsociado($oArchivo, $iIdItem, $sObjetoAsociado)
    {
        try{
            $db = $this->conn;
            $iIdItem = $this->escInt($iIdItem);

            $moderado = $oArchivo->isModerado() ? "1" : "0";
            $activo = $oArchivo->isActivo() ? "1" : "0";
            $publico = $oArchivo->isPublico() ? "1" : "0";
            $activoComentarios = $oArchivo->isActivoComentarios() ? "1" : "0";

            $sSQL = " INSERT INTO archivos SET ";

            switch($sObjetoAsociado){
                case "Publicacion": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "Review": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "SeguimientoSCC": $sSQL .= "seguimientos_id = ".$iIdItem.", "; break;
                case "SeguimientoPersonalizado": $sSQL .= "seguimientos_id = ".$iIdItem.", "; break;
                case "Usuario": $sSQL .= "usuarios_id = ".$iIdItem.", "; break;
                case "Categoria": $sSQL .= "categorias_id = ".$iIdItem.", "; break;
            }

            $sSQL .= " nombre = ".$this->escStr($oArchivo->getNombre()).", " .
            " nombreServidor = ".$this->escStr($oArchivo->getNombreServidor()).", " .
            " descripcion = ".$this->escStr($oArchivo->getDescripcion()).", ".
            " tipoMime = ".$this->escStr($oArchivo->getTipoMime()).", " .
            " tamanio = ".$this->escInt($oArchivo->getTamanio()).", " .
            " fechaAlta = '".$oArchivo->getFechaAlta()."', ".
            " orden = ".$this->escInt($oArchivo->getOrden()).", " .
            " titulo = ".$this->escStr($oArchivo->getTitulo()).", " .
            " tipo = ".$this->escStr($oArchivo->getTipo()).", " .
            " moderado = ".$this->escInt($moderado).", " .
            " activo = ".$this->escInt($activo).", " .
            " publico = ".$this->escInt($publico).", " .
            " activoComentarios = ".$this->escInt($activoComentarios)." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();

            $oArchivo->setId($iLastId);

            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }        
    }
   
    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        
    }

    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($objects){}
    public function guardar($object){}
    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
}