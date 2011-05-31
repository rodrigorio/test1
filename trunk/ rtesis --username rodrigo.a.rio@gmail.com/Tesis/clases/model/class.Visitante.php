<?php

/**
 *
 *
 *
 */
class Visitante extends PerfilAbstract
{
    const PERFIL_VISITANTE_ID = 3;
    const PERFIL_VISITANTE_DESCRIPCION = 'visitante';

    public function __construct(){
        parent::__construct();
        $this->id = self::PERFIL_VISITANTE_ID;
        $this->descripcion = self::PERFIL_VISITANTE_DESCRIPCION;
    }

    /**
     * Los visitantes NUNCA pueden recibir informacion acerca de si pueden o no realizar una accion.
     * Si sos visitante y accedes a una accion en la que tenes que estar logueado vas a parar SI o SI a formulario de login.
     *
     * Me auto corrijo, este metodo debe devolver modulo_controlador_accion correspondiente a la home del sitio.
     * Se puede DESACTIVAR una funcionalidad para que no puedan acceder los visitantes, por lo que no es necesario exigir login.
     *
     * Sin embargo, es correcto pedir siempre login cuando la redireccion es porque el perfil no posee PERMISO PARA UNA ACCION
     * (independientemente si esta activa o no)
     */
    public function getUrlRedireccion401()
    {
        $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
        $soloSistema = true; //devuelve mas rapido

        $homeSitioModulo = $parametros->obtener('HOME_SITIO_MODULO', $soloSistema);
        $homeSitioControlador = $parametros->obtener('HOME_SITIO_CONTROLADOR', $soloSistema);
        $homeSitioAccion = $parametros->obtener('HOME_SITIO_ACCION', $soloSistema);

        return array($modulo, $controlador, $accion);
    }

    /**
     * CONCEPTUALMENTE, cuando el usuario se loguea deja de ser un visitante. Es decir, el visitante no se loguea nunca.
     * La redireccion (la pagina a la que se dirige el sistema luego del login)
     * estara determinada por el tipo de perfil que EFECTIVAMENTE se logueo.
     */
    public function getUrlRedireccionLoginDefecto()
    {
        throw new Exception("ERROR, luego de loguearse un usuario nunca puede quedar en perfil Visitante");
    }
}