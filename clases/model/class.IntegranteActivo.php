<?php


/**
 * Description of classIntegranteActivo
 *
 * 
 */
class IntegranteActivo extends PerfilAbstract
{
    const PERFIL_INTEGRANTE_ACTIVO_ID = 5;
    const PERFIL_INTEGRANTE_ACTIVO_DESCRIPCION = 'visitante';

    public function __construct(){
        parent::__construct();
        $this->id = self::PERFIL_INTEGRANTE_ACTIVO_ID;
        $this->descripcion = self::PERFIL_INTEGRANTE_ACTIVO_DESCRIPCION;
    }
}
?>
