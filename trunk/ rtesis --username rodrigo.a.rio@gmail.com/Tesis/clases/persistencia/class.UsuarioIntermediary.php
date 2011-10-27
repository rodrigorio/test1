<?php

/**
 * En esta clase va toda la interfaz a METODOS y ATRIBUTOS que sean unicos a MYSQL de la clase Usuarios.
 * (Por ejemplo, sacar un promedio de la cantidad de registrados por mes)
 *
 * @author Andres
 */
abstract class UsuarioIntermediary extends Intermediary{
    abstract public function obtenerPrivacidadCampo($filtro, $nombreCampo);
    abstract public function obtenerPrivacidad($filtro);
    abstract public function updatePrivacidadCampo($filtro, $nombreCampo, $valorPrivacidad);
    abstract public function existeMailDb($email, $userId);
    abstract public function obtenerPerfil($oUsuario);
    abstract public function guardarPerfil($oPerfil);
}