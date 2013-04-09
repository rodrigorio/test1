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
     * @return $oPractica|null
     */
    public static function &getPracticaInstance(stdClass $obj){
        $oPractica = new Practica($obj);
        return $oPractica;
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
    /**
     * @param stdClass $obj
     * @return Practica|null
     */
    public static function &getTipoPracticaSeguimientoInstance(stdClass $obj){
        $oPractica = new TipoPracticasSeguimiento($obj);
        return $oPractica;
    }
    /**
     * @param stdClass $obj
     * @return Accion|null
     */
    public static function &getAccionInstance(stdClass $obj){
        $oAccion = new Accion($obj);
        return $oAccion;
    }
    /**
     * @param stdClass $obj
     * @return Publicacion|null
     */
    public static function &getPublicacionInstance(stdClass $obj){
        $oPublicacion = new Publicacion($obj);
        return $oPublicacion;
    }
    /**
     * @param stdClass $obj
     * @return Review|null
     */
    public static function &getReviewInstance(stdClass $obj){
        $oReview = new Review($obj);
        return $oReview;
    }
    /**
     * @param stdClass $obj
     * @return EmbedVideo|null
     */
    public static function &getEmbedVideoInstance(stdClass $obj){
        $oEmbedVideo = new EmbedVideo($obj);
        return $oEmbedVideo;
    }
    /**
     * @param stdClass $obj
     * @return Comentario|null
     */
    public static function &getComentarioInstance(stdClass $obj){
        $oComentario = new Comentario($obj);
        return $oComentario;
    }
    
    /**
     * @param stdClass $obj
     * @return DiagnosticoPersonalizado|null
     */
    public static function &getDiagnosticoPersonalizadoInstance(stdClass $obj){
        $oDiagnosticoPersonalizado = new DiagnosticoPersonalizado($obj);
        return $oDiagnosticoPersonalizado;
    }
    /**
     * @param stdClass $obj
     * @return DiagnosticoSCC|null
     */
    public static function &getDiagnosticoSCCInstance(stdClass $obj){
        $oDiagnosticoSCC = new DiagnosticoSCC($obj);
        return $oDiagnosticoSCC;
    }    
   /**
     * @param stdClass $obj
     * @return Area|null
     */
    public static function &getAreaInstance(stdClass $obj){
        $oArea = new Area($obj);
        return $oArea;
    }
    /**
     * @param stdClass $obj
     * @return Nivel|null
     */
    public static function &getNivelInstance(stdClass $obj){
        $oNivel = new Nivel($obj);
        return $oNivel;
    }
    /**
     * @param stdClass $obj
     * @return Ciclo|null
     */
    public static function &getCicloInstance(stdClass $obj){
        $oCiclo = new Ciclo($obj);
        return $oCiclo;
    }
    /**
     * @param stdClass $obj
     * @return EjeTematico|null
     */
    public static function &getEjeTematicoInstance(stdClass $obj){
        $oEjeTematico = new EjeTematico($obj);
        return $oEjeTematico;
    }
    /**
     * @param stdClass $obj
     * @return ObjetivoAprendizaje|null
     */
    public static function &getObjetivoAprendizajeInstance(stdClass $obj){
        $oObjetivoAprendizaje = new ObjetivoAprendizaje($obj);
        return $oObjetivoAprendizaje;
    }

    public static function &getObjetivoPersonalizadoInstance(stdClass $obj){
        $oObjetivoPersonalizado = new ObjetivoPersonalizado($obj);
        return $oObjetivoPersonalizado;
    }

    public static function &getObjetivoPersonalizadoEjeInstance(stdClass $obj){
        $oObjetivoPersonalizadoEje = new ObjetivoPersonalizadoEje($obj);
        return $oObjetivoPersonalizadoEje;
    }

    public static function &getObjetivoRelevanciaInstance(stdClass $obj){
        $oObjetivoRelevancia = new ObjetivoRelevancia($obj);
        return $oObjetivoRelevancia;
    }
  
    public static function &getModeracionInstance(stdClass $obj){
        $oModeracion = new Moderacion($obj);
        return $oModeracion;
    }    
    
    public static function &getSolicitudInstance(stdClass $obj){
        $oSolicitud = new Solicitud($obj);
        return $oSolicitud;
    }
    
    public static function &getSoftwareInstance(stdClass $obj){
        $oSoftware = new Software($obj);
        return $oSoftware;
    }
    /**
     * @param stdClass $obj
     * @return VariableTexto|null
     */
    public static function &getVariableTextoInstance(stdClass $obj){
        $oVariableTexto = new VariableTexto($obj);
        return $oVariableTexto;
    }
    /**
     * @param stdClass $obj
     * @return VariableNumerica|null
     */
    public static function &getVariableNumericaInstance(stdClass $obj){
        $oVariableNumerica = new VariableNumerica($obj);
        return $oVariableNumerica;
    }
    /**
     * @param stdClass $obj
     * @return VariableCualitativa|null
     */
    public static function &getVariableCualitativaInstance(stdClass $obj){
        $oVariableCualitativa = new VariableCualitativa($obj);
        return $oVariableCualitativa;
    }
    /**
     * @param stdClass $obj
     * @return Modalidad|null
     */
    public static function &getModalidadInstance(stdClass $obj){
        $oModalidad = new Modalidad($obj);
        return $oModalidad;
    }
   /**
     * @param stdClass $obj
     * @return Unidad|null
     */
    public static function &getUnidadInstance(stdClass $obj){
        $oUnidad = new Unidad($obj);
        return $oUnidad;
    }

   /**
     * @param stdClass $obj
     * @return Parametro|null
     */
    public static function &getParametroInstance(stdClass $obj){
        $oParametro = new Parametro($obj);
        return $oParametro;
    }
   /**
     * @param stdClass $obj
     * @return ParametroControlador|null
     */
    public static function &getParametroControladorInstance(stdClass $obj){
        $oParametroControlador = new ParametroControlador($obj);
        return $oParametroControlador;
    }
   /**
     * @param stdClass $obj
     * @return ParametroSistema|null
     */
    public static function &getParametroSistemaInstance(stdClass $obj){
        $oParametroSistema = new ParametroSistema($obj);
        return $oParametroSistema;
    }
   /**
     * @param stdClass $obj
     * @return ParametroUsuario|null
     */
    public static function &getParametroUsuarioInstance(stdClass $obj){
        $oParametroUsuario = new ParametroUsuario($obj);
        return $oParametroUsuario;
    }
   /**
     * @param stdClass $obj
     * @return ControladorPagina|null
     */
    public static function &getControladorPaginaInstance(stdClass $obj){
        $oControladorPagina = new ControladorPagina($obj);
        return $oControladorPagina;
    }

    /**
     * @param stdClass $obj
     * @return ControladorPagina|null
     */
    public static function &getDenunciaInstance(stdClass $obj){
        $oDenuncia = new Denuncia($obj);
        return $oDenuncia;
    }
    
    /**
     * @param stdClass $obj
     * @return Invitacion|null
     */
    public static function &getInvitacionInstance(stdClass $obj){
        $oInvitacion = new Invitacion($obj);
        return $oInvitacion;
    }

    /**
     * @param stdClass $obj
     * @return PasswordTemporal|null
     */
    public static function &getPasswordTemporalInstance(stdClass $obj){
        $oPasswordTemporal = new PasswordTemporal($obj);
        return $oPasswordTemporal;
    }
}