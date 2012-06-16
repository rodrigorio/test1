<?php
/**
 * Esto es para tener el cuidado de que reviews y publicaciones se mantengan sin errores en los metodos polimorficos
 * 
 * @author Matias Velilla
 */
interface PublicacionesInterface
{
    public function setUsuarioId($iUsuarioId);
    public function setUsuario($oUsuario);
    public function isModerado($flag = null);
    public function isPublico($flag = null);
    public function isActivoComentarios($flag = null);
    public function setDescripcionBreve($sDescripcionBreve);
    public function setKeywords($sKeywords);

    public function getUsuario();
    public function getUsuarioId();
    public function getDescripcionBreve();
    public function getKeywords();

    public function getComentarios();
    public function setComentarios($aComentarios);
    public function addComentario($oComentario);
}