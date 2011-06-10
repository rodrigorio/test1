<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classUsuarioIntermediary
 *
 * @author Andres
 */
class UsuarioIntermediary extends Intermediary{
   
    public  abstract function insertarUsuario(Usuario $oUsuario);

    public abstract function obtenerUsuario($id);
    public abstract function obtenerListaUsuario($id);

    public abstract function guardarUsuario($id);
}
?>
