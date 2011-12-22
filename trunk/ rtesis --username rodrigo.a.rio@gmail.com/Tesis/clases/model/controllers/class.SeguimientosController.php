<?php

/**
 * Description of class
 *
 * @author Matias Velilla
 */
class SeguimientosController
{
    
    /**
     * @var Instancia de DB
     */
    private $db = null;

    private static $instance = null;

    private function __construct(){ }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param DB $db
     */
    public function setDBDriver(DB $db){
        $this->db = $db;
    }

    public function guardarSeguimiento($oSeguimiento){
    	try{
            if($oSeguimiento->getTipoSeguimiento() == "PERSONALIZADO"){
                 $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoPersonalizadoIntermediary($this->db);
            }else{
                 $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoSCCIntermediary($this->db);
            }
            return $oSeguimientoIntermediary->guardar($oSeguimiento);
        }catch(Exception $e){
                echo $e->getMessage();
        }
    }
}