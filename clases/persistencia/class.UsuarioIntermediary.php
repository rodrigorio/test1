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
   
    public  abstract function insert(Usuario $oUsuario);

    public abstract function obtenerUsuario($id);
}
?>
