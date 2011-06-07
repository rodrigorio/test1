<?php


/**
 * Description of classIntegranteInactivo
 *
 * @author Andres
 */
class IntegranteInactivo extends PerfilAbstract
{
    const PERFIL_INTEGRANTE_INACTIVO_ID = 4;
    const PERFIL_INTEGRANTE_INACTIVO_DESCRIPCION = 'integrante inactivo';

    public function __construct(){
        parent::__construct();
        $this->id = self::PERFIL_INTEGRANTE_INACTIVO_ID;
        $this->descripcion = self::PERFIL_INTEGRANTE_INACTIVO_DESCRIPCION;
    }
}
?>
