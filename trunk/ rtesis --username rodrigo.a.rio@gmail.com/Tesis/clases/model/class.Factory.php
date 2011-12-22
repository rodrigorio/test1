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
     * @return Administrador|null Perfil Blogger que hereda de perfil abstract
     * @throws exception si hubo error en el constructor de la clase que se desea instanciar.
     */
    public static function &getAdministradorInstance(stdClass $obj){
        $oAdministrador = new Administrador($obj);
        return $oAdministrador;
    }
    /**
     * @param stdClass $obj
     * @return Moderador|null Perfil Blogger que hereda de perfil abstract
     * @throws exception si hubo error en el constructor de la clase que se desea instanciar.
     */
    public static function &getModeradorInstance(stdClass $obj){
        $oModerador = new Moderador($obj);
        return $oModerador;
    }
    /**
     * @param stdClass $obj
     * @return Administrador|null Perfil Blogger que hereda de perfil abstract
     * @throws exception si hubo error en el constructor de la clase que se desea instanciar.
     */
    public static function &getIntegranteActivoInstance(stdClass $obj){
        $oIntegranteActivo = new IntegranteActivo($obj);
        return $oIntegranteActivo;
    }
    /**
     * @param stdClass $obj
     * @return Administrador|null Perfil Blogger que hereda de perfil abstract
     * @throws exception si hubo error en el constructor de la clase que se desea instanciar.
     */
    public static function &getIntegranteInactivoInstance(stdClass $obj){
        $oIntegranteInactivo = new IntegranteInactivo($obj);
        return $oIntegranteInactivo;
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
     * @return Invitado|null
     */
    public static function &getInvitadoInstance(stdClass $obj){
        $oInvitado = new Invitado($obj);
        return $oInvitado;
    }
    /**
     * @param stdClass $obj
     * @return Usuario|null Usuario que hereda de Persona, luego se asigna a un objeto perfil
     */
    public static function &getUsuarioInstance(stdClass $obj){
        $oUsuario = new Usuario($obj);
        return $oUsuario;
    }
    /**
     * @param stdClass $obj
     * @return $oInstitucion|null
     */
    public static function &getInstitucionInstance(stdClass $obj){
        $oInstitucion = new Institucion($obj);
        return $oInstitucion;
    }
    /**
     * @param stdClass $obj
     * @return $oEspecialidad|null
     */
    public static function &getEspecialidadInstance(stdClass $obj){
        $oEspecialidad = new Especialidad($obj);
        return $oEspecialidad;
    }
    /**
     * @param stdClass $obj
     * @return $oCatgoria|null
     */
    public static function &getCategoriaInstance(stdClass $obj){
        $oCategoria = new Categoria($obj);
        return $oCategoria;
    }
    /**
     * @param stdClass $obj
     * @return $oTipoSeguimiento|null
     */
    public static function &getTipoSeguimientoInstance(stdClass $obj){
        $oTipoSeguimiento = new TipoSeguimiento($obj);
        return $oTipoSeguimiento;
    }
    /**
     * @param stdClass $obj
     * @return $oTipoPracticasSeguimiento|null
     */
    public static function &getTipoPracticasSeguimientoInstance(stdClass $obj){
        $oTipoPracticasSeguimiento = new TipoPracticasSeguimiento($obj);
        return $oTipoPracticasSeguimiento;
    }

    /**
     * @param stdClass $obj
     * @return Archivo|null
     */
    public static function &getArchivoInstance(stdClass $obj){
        $oArchivo = new Archivo($obj);
        return $oArchivo;
    }
    
    /**
     * @param stdClass $obj
     * @return Foto|null
     */
    public static function &getFotoInstance(stdClass $obj){
        $oFoto = new Foto($obj);
        return $oFoto;
    }
    /**
     * @param stdClass $obj
     * @return SeguimientoSCC|null
     */
    public static function &getSeguimientoSCCInstance(stdClass $obj){
        $oSeguimientoSCC = new SeguimientoSCC($obj);
        return $oSeguimientoSCC ;
    }
    /**
     * @param stdClass $obj
     * @return SeguimientoPersonalizado|null
     */
    public static function &getSeguimientoPersonalizadoInstance(stdClass $obj){
        $oSeguimientoPersonalizado = new SeguimientoPersonalizado($obj);
        return $oSeguimientoPersonalizado ;
    }
    /**
     * @param stdClass $obj
     * @return Discapacitado|null
     */
    public static function &getDiscapacitadoInstance(stdClass $obj){
        $oDiscapacitado= new Discapacitado($obj);
        return $oDiscapacitado;
    }
}