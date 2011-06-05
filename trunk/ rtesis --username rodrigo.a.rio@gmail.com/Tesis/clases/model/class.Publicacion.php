<?php

/**
 *
 *
 *
 */
class Publicacion
{
    private $id = null;

    /**
     * En la db es un campo calculado, aca simplemente se extrae.
     */
    private $valoracion = null;

    private $cantidadCriticas = 0;

    private $titulo;

    private $fechaAlta;

    /**
     * objeto PerfilAbstract
     */
    private $autor;

    private $descripcion;

    /**
     * objetos Foto. (Proxy, se pide a demanda)
     * @var array Objetos clase Foto
     */
    private $fotos = array();

    /**
     * objetos Videos. (Proxy, se pide a demanda)
     */
    private $videos = array();

    /**
     * objetos Archivos. (Proxy, se pide a demanda)
     */
    private $archivos = array();

    /**
     * objetos Comentario. (Proxy, se pide a demanda)
     */
    private $comentarios = array();

    private function obtenerFotos()
    {
        //$this->fotos = BlogController::getInstance()->obtenerFotosPublicacion($this->id);
    }

    private function obtenerVideos()
    {

    }

    private function obtenerArchivos()
    {

    }

    private function obtenerComentarios()
    {

    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setValoracion($valoracion)
    {
        //checkear que sea int y que sea entre 0 y 5 sino retorna error
        $this->valoracion = $valoracion;
        return $this;
    }

    public function getValoracion()
    {
        return $this->valoracion;
    }

    public function setCantidadCriticas($cantidadCriticas)
    {
        $this->cantidadCriticas = $cantidadCriticas;
        return $this;
    }

    public function getCantidadCriticas()
    {
        return $this->cantidadCriticas;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
        return $this;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setFechaAlta($fechaAlta)
    {
        //comprobar formato date
        $this->fechaAlta = $fechaAlta;
        return $this;
    }

    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }

    public function setAutor(PerfilAbstract $autor)
    {
        //comprobar clase sino tira error
        $this->autor = $autor;
        return $this;
    }

    public function getAutor()
    {
        return $this->autor;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getFotos()
    {
        if(empty($this->fotos)){ $this->obtenerFotos(); }
        return $this->fotos;
    }

    public function getCantidadFotos()
    {
        if(empty($this->fotos)){ $this->obtenerFotos(); }
        return count($this->fotos);
    }

    public function getVideos()
    {
        if(empty($this->videos)){ $this->obtenerVideos(); }
        return $this->videos;
    }

    public function getCantidadVideos()
    {
        if(empty($this->videos)){ $this->obtenerVideos(); }
        return count($this->videos);
    }

    public function getArchivos()
    {
        if(empty($this->archivos)){ $this->obtenerArchivos(); }
        return $this->archivos;
    }

    public function getCantidadArchivos()
    {
        if(empty($this->fotos)){ $this->obtenerArchivos(); }
        return count($this->fotos);
    }

    public function getComentarios()
    {
        if(empty($this->comentarios)){ $this->obtenerComentarios(); }
        return $this->comentarios;
    }

    public function getCantidadComentarios()
    {
        if(empty($this->comentarios)){ $this->obtenerComentarios(); }
        return count($this->comentarios);
    }
}
?>
