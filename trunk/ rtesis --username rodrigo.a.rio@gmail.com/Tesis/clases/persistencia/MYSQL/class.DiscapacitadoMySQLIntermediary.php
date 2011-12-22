<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classpersonaMySQLIntermediary
 *
 * @author Andres
 */
class DiscapacitadoMySQLIntermediary extends DiscapacitadoIntermediary
{
	private static $instance = null;

	protected function __construct( $conn) {
		parent::__construct($conn);
	}

	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return InstitucionMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
	
	public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        p.id as iId, p.nombre as sNombre, p.apellido as sApellido,
                        #p.nacionalidad as sNacionalidad,
                        p.sexo as sSexo, p.fechaNacimiento as dFechaNacimiento,
                        p.email as sEmail, p.telefono as sTelefono, p.celular as sCelular,
                        p.fax as sFax, p.domicilio as sDomicilio, p.ciudadOrigen as sCiudadOrigen,
                        p.codigoPostal as sCodigoPostal, p.empresa as sEmpresa,
                        p.universidad as sUniversidad, p.secundaria as sSecundaria,

                        d.nombreApellidoPadre as sNombreApellidoPadre, d.nombreApellidoMadre as sNombreApellidoMadre,
                        d.fechaNacimientoPadre as dFechaNacimientoPadre,
                        d.fechaNacimientoMadre as dFechaNacimientoMadre,
                        d.ocupacionPadre as sOcupacionPadre, d.ocupacionMadre as sOcupacionMadre,
                        d.nombreHermanos as sNombreHermanos
                    FROM
                        personas p 
                    JOIN discapacitados d ON p.id = d.id ";
                    if(!empty($filtro)){
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aDiscapacitado = array();
            while($oObj = $db->oNextRecord()){
                $oDiscapacitado 				= new stdClass();
                $oDiscapacitado->iId 			= $oObj->iId;
                $oDiscapacitado->sNombre 		= $oObj->sNombre;
                $oDiscapacitado->sApellido 	= $oObj->sApellido;
            //    $oDiscapacitado->sNacionalidad 	= $oObj->sNacionalidad;
                $oDiscapacitado->sSexo 		= $oObj->sSexo;
                $oDiscapacitado->dFechaNacimiento = $oObj->dFechaNacimiento;
                $oDiscapacitado->sEmail 		= $oObj->sEmail;
                $oDiscapacitado->sTelefono 	= $oObj->sTelefono;
                $oDiscapacitado->sCelular	 	= $oObj->sCelular;
                $oDiscapacitado->sFax 		= $oObj->sFax;
                $oDiscapacitado->sDomicilio 	= $oObj->sDomicilio;
                $oDiscapacitado->oCiudad 		= null;
                $oDiscapacitado->sCiudadOrigen= $oObj->sCiudadOrigen;
                $oDiscapacitado->sCodigoPostal= $oObj->sCodigoPostal;
                $oDiscapacitado->sEmpresa		= $oObj->sEmpresa;
                $oDiscapacitado->sUniversidad = $oObj->sUniversidad;
                $oDiscapacitado->sSecundaria 	= $oObj->sSecundaria;
                $oDiscapacitado->sNombreApellidoPadre	= $oObj->sNombreApellidoPadre;
                $oDiscapacitado->sNombreApellidoMadre	= $oObj->sNombreApellidoMadre;
                $oDiscapacitado->dFechaNacimientoPadre = $oObj->dFechaNacimientoPadre;
                $oDiscapacitado->dFechaNacimientoMadre 	= $oObj->dFechaNacimientoMadre;
                $oDiscapacitado->sOcupacionPadre = $oObj->sOcupacionPadre;
                $oDiscapacitado->sOcupacionMadre = $oObj->sOcupacionMadre;
                $oDiscapacitado->sNombreHermanos = $oObj->sNombreHermanos;
                //creo el discapacitado
                $oDiscapacitado = Factory::getDiscapacitadoInstance($oDiscapacitado);
				$aDiscapacitado[] = $oDiscapacitado;
		   /////hasta aca 10 de julio 2011            
            }

            //si es solo un elemento devuelvo el objeto si hay mas de un elemento devuelvo el array.
            if(count($aDiscapacitado) == 1){
                return $aDiscapacitado[0];
            }else{
                return $aDiscapacitado;
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        personas p 
                    JOIN 
                    	discapacitados d ON p.id = d.id
					WHERE ".$this->crearCondicionSimple($filtro,"",false,"OR");

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

    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}

	 public  function actualizar($oDiscapacitado)
    {
        try{
			$db = $this->conn;
					
			if($oDiscapacitado->getCiudad()!= null){
				$ciudadId = $oDiscapacitado->getCiudad()->getId();
			}else {
				$ciudadId = null;
			}
        	if($oDiscapacitado->getInstitucion()!= null){
				$institucionId = $oDiscapacitado->getInstitucion()->getId();
			}else {
				$institucionId = null;
			}
			
            $db->begin_transaction();
            $sSQL = " update personas " .
                    " set nombre =".$db->escape($oDiscapacitado->getNombre(),true).", " .
                    " apellido =".$db->escape($oDiscapacitado->getApellido(),true).", " .
            		" nacionalidad =".$db->escape($oDiscapacitado->getNacionalidad(),true).", " .
					" documento_tipos_id =".$db->escape($oDiscapacitado->getDocumentoId(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oDiscapacitado->getNumeroDocumento(),true).", " .
                    " sexo =".$db->escape($oDiscapacitado->getSexo(),true).", " .
                    " fechaNacimiento= ".$db->escape($oDiscapacitado->getFechaNacimiento(), false,MYSQL_TYPE_DATE).", ".
                    " email =".$db->escape($oDiscapacitado->getEmail(),true).", " .
                    " telefono =".$db->escape($oDiscapacitado->getTelefono(),true).", " .
                    " celular =".$db->escape($oDiscapacitado->getCelular(),true).", " .
                    " fax =".$db->escape($oDiscapacitado->getFax(),true).", " .
                    " domicilio =".$db->escape($oDiscapacitado->getDomicilio(),true).", " .
                    " instituciones_id =".$institucionId.", ".
                    " ciudades_id =".$ciudadId.", ".
					" ciudadOrigen =".$db->escape($oDiscapacitado->getCiudadOrigen(),true).", " .
                    " codigoPostal =".$db->escape($oDiscapacitado->getCodigoPostal(),true).", " .
                    " empresa =".$db->escape($oDiscapacitado->getEmpresa(),true).", " .
                    " universidad =".$db->escape($oDiscapacitado->getUniversidad(),true).", " .
                    " secundaria =".$db->escape($oDiscapacitado->getSecundaria(),true)." ".
                    " WHERE id = ".$db->escape($oDiscapacitado->getId(),false,MYSQL_TYPE_INT)." ";


			 $db->execSQL($sSQL);

             $sSQL =" update discapacitados ".
                    " set nombreApellidoPadre=".$db->escape($oDiscapacitado->getNombreApellidoPadre(),true).", " .
					" nombreApellidoMadre =".$db->escape($oDiscapacitado->getNombreApellidoMadre(),true).", ".
                    " fechaNacimientoPadre =".$db->escape($oDiscapacitado->getFechaNacimientoPadre(),false,MYSQL_TYPE_DATE).", ".
                    " fechaNacimientoMadre =".$db->escape($oDiscapacitado->getFechaNacimientoMadre(),false,MYSQL_TYPE_DATE).", ".
					" ocupacionPadre =".$db->escape($oDiscapacitado->getOcupacionPadre(),true).", " .
                    " ocupacionMadre =".$db->escape($oDiscapacitado->getOcupacionMadre(),true).", " .
			        " nombreHermanos =".$db->escape($oDiscapacitado->getNombreHermanos(),true)." " .
                    " WHERE id = ".$db->escape($oDiscapacitado->getId(),false,MYSQL_TYPE_INT)." ";
			 $db->execSQL($sSQL);
			 $db->commit();


		}catch(Exception $e){
            $db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }

    public function guardar($oDiscapacitado)
    {
        try{
			if($oDiscapacitado->getId() != null){
            	return $this->actualizar($oDiscapacitado);
            }else{
				return $this->insertar($oDiscapacitado);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
    public  function insertar($oDiscapacitado)
   {
		try{
			$db = $this->conn;
			if($oDiscapacitado->getCiudad()!= null){
				$ciudadId = $oDiscapacitado->getCiudad()->getId();
			}else {
				$ciudadId = null;
			}
        	if($oDiscapacitado->getInstitucion()!= null){
				$institucionId = $oDiscapacitado->getInstitucion()->getId();
			}else {
				$institucionId = null;
			}
			
			
			
			$db->begin_transaction();
			$sSQL =	" insert into personas ".
                    " set nombre =".$db->escape($oDiscapacitado->getNombre(),true).", " .
                    " apellido =".$db->escape($oDiscapacitado->getApellido(),true).", " .
					" nacionalidad =".$db->escape($oDiscapacitado->getNacionalidad(),true).", " .
					" documento_tipos_id =".$db->escape($oDiscapacitado->getTipoDocumento(),false,MYSQL_TYPE_INT).", ".
                    " numeroDocumento =".$db->escape($oDiscapacitado->getNumeroDocumento(),false,MYSQL_TYPE_INT).", " .
                    " sexo =".$db->escape($oDiscapacitado->getSexo(),true).", " .
                    " fechaNacimiento= ".$db->escape($oDiscapacitado->getFechaNacimiento(), true,MYSQL_TYPE_DATE).", " .
                    " email =".$db->escape($oDiscapacitado->getEmail(),true).", " .
                    " telefono =".$db->escape($oDiscapacitado->getTelefono(),true).", " .
                    " celular =".$db->escape($oDiscapacitado->getCelular(),true).", " .
                    " fax =".$db->escape($oDiscapacitado->getFax(),true).", " .
                    " domicilio =".$db->escape($oDiscapacitado->getDomicilio(),true).", " .//revisar esto
                    " instituciones_id =".$db->escape($institucionId,true).", ".
                    " ciudades_id =".$db->escape($ciudadId,true).", ".
					" ciudadOrigen =".$db->escape($oDiscapacitado->getCiudadOrigen(),true).", " .
                    " codigoPostal =".$db->escape($oDiscapacitado->getCodigoPostal(),true).", " .
                    " empresa =".$db->escape($oDiscapacitado->getEmpresa(),true).", " .
                    " universidad =".$db->escape($oDiscapacitado->getUniversidad(),true).", " .
                    " secundaria =".$db->escape($oDiscapacitado->getSecundaria(),true)." ";

			$db->execSQL($sSQL);
			$iLastId = $db->insert_id();
			
			$sSQL =" insert into discapacitados set ".
                    " id=".$db->escape($iLastId,false).", " .
                    " nombreApellidoPadre=".$db->escape($oDiscapacitado->getNombreApellidoPadre(),true).", " .
					" nombreApellidoMadre =".$db->escape($oDiscapacitado->getNombreApellidoMadre(),true).", ".
                    " fechaNacimientoPadre =".$db->escape($oDiscapacitado->getFechaNacimientoPadre(),false,MYSQL_TYPE_DATE).", ".
                    " fechaNacimientoMadre =".$db->escape($oDiscapacitado->getFechaNacimientoMadre(),false,MYSQL_TYPE_DATE).", ".
					" ocupacionPadre =".$db->escape($oDiscapacitado->getOcupacionPadre(),true).", " .
                    " ocupacionMadre =".$db->escape($oDiscapacitado->getOcupacionMadre(),true).", " .
			        " nombreHermanos =".$db->escape($oDiscapacitado->getNombreHermanos(),true)." " ;
					
			 $db->execSQL($sSQL);
			 $db->commit();
			 return true;

		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}

   
    public function borrar($oDiscapacitado) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from usuarios where id=".$db->escape($oDiscapacitado->getId(),false,MYSQL_TYPE_INT));
            $db->execSQL("delete from discapacitados where id=".$db->escape($oDiscapacitado->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
}
?>