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
    public function getContrasenia()
    {
        return $this->contrasenia;
    }

    public function setContrasenia($contrasenia)
    {
        $this->contrasenia = $contrasenia;
        return $this;
    }
    

}

?>