<?php
/**
 * Factoria de Modelo
 */
class Factory
{
    private function  ___construct(){}

    /**
     * @param stdClass $obj
     * @return Group
     */
    public static function &getVisitanteInstance(stdClass $obj){
        $oVisitante = new Visitante($obj);
        return $oVisitante;
    }

    /**
     * @param stdClass $obj
     * @return Blogger|null Perfil Blogger que hereda de perfil abstract
     */
    public static function &getAdministradorInstance(stdClass $obj){
        $oAdministrador = new Administrador($obj);
        return $oAdministrador;
    }

    /**
     * @param stdClass $obj
     * @return Blogger|null Perfil Blogger que hereda de perfil abstract
     */
    public static function &getBloggerInstance(stdClass $obj){
        $oBlogger = new Blogger($obj);
        return $oBlogger;
    }

    /**
     * @param stdClass $obj
     * @return Usuario|null Usuario que hereda de Persona, luego se asigna a un objeto perfil
     */
    public static function &getUsuarioInstance(stdClass $obj){
        $oUsuario = new Usuario($obj);
        return $oUsuario;
    }
}