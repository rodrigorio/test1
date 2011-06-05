<?php

/**
 * 	Action Controller Publicaciones
 */
class PublicacionesControllerAdmin extends PageControllerAbstract
{
    public function index()
    {
        $this->listarPublicaciones();
    }

    public function listarPublicaciones()
    {
        echo "entro a listar publicaciones admin";
    }
}