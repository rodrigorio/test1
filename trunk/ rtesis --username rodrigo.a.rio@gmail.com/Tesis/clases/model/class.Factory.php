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
     * @return Pais|null
     */
    public static function &getPaisInstance(stdClass $obj){
        $oPais = new Pais($obj);
        return $oPais;
    }
    /**
     * @param stdClass $obj
     * @return Provincia|null
     */
    public static function &getProvinciaInstance(stdClass $obj){
        $oProvincia = new Provincia($obj);
        return $oProvincia;
    }
    /**
     * @param stdClass $obj
     * @return Ciudad|null
     */
    public static function &getCiudadInstance(stdClass $obj){
        $oCiudad = new Ciudad($obj);
        return $oCiudad;
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