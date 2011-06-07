<?php


/**
 * Description of classModerador
 *
 * @author Andres
 */
class Moderador extends PerfilAbstract
{
    const PERFIL_MODERADOR_ID = 2;
    const PERFIL_MODERADOR_DESCRIPCION = 'moderador';

    public function __construct(){
        parent::__construct();
        $this->id = self::PERFIL_MODERADOR_ID;
        $this->descripcion = self::PERFIL_MODERADOR_DESCRIPCION;
}
 
}
?>
