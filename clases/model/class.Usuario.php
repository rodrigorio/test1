<?php

/**
 *
 *
 *
 */
class Usuario extends PersonaAbstract
{
    private $fechaAlta;
    private $sitioWeb;
    private $nombreUsuario;
    private $contrasenia;

    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;
        return $this;
    }
    

}

?>