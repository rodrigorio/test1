<?php
/**
 * @author Matias Velilla
 *
 * Si se hace submit del formulario se redirecciona dependiendo si existe o no el codigo de error 401
 * Si existe se redirecciona al request original (la pagina restringida que se solicitaba)
 * Si no existe se redirecciona a la url por defecto que dependera del perfil del usuario que se loguea.
 */
class LoginControllerIndex extends PageControllerAbstract
{
    public function index()
    {
        $this->mostrarFormulario();
    }

    public function procesar()
    {
        //si es ajax me fijo si los post son correctos y devuelvo true o false para q procese el submit del form
        $this->getResponse()->setBody("entro procesar login");
    }

    public function mostrarFormulario()
    {
        //si ya esta logueado cancelo la accion y redirecciono a url por defecto.
        if(SessionAutentificacion::getInstance()->realizoLogin()){
            $pathInfo = true;
            $url = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUrlRedireccionLoginDefecto($pathInfo);
            $this->getRedirectorHelper()->gotoUrl($url); //por defecto redireccion resulta en un inmediato exit() luego de la sentencia.
        }
                
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
        $tituloVista = $nombreSitio.' | '.$parametros->obtener('METATAG_TITLE');
        $descriptionVista = $parametros->obtener('METATAG_DESCRIPTION');
        $keywordsVista = $parametros->obtener('METATAG_KEYWORDS');
        $fileNameLogo = $parametros->obtener('FILE_NAME_LOGO_SITIO');
        $footerContent = "UrBIS . Todos los derechos reservados";

        //Si entro a login por error de permiso guardo la url original donde queria ir el user.
        $nextFormUrl = "";
        if($this->getRequest()->getPathInfo() != '/login'){
            $nextFormUrl = $this->getRequest()->get('REQUEST_URI');
        }
        //se procesa el envio del form en un metodo de esta misma clase.
        $actionFormUrl = "login-procesar";

        $this->getTemplate()->load_file("gui/templates/frameBlog01-03.gui.html", "frame");

        $this->getTemplate()->load_file_section("gui/vistas/index/login.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);

        $this->getTemplate()->set_var("sourceLogoHeader", 'sitio/uploads/design/'.$fileNameLogo);
        //despues tiene que haber un helper de imagenes cuya una de las funciones extraiga estos valores.
        $this->getTemplate()->set_var("heightLogoHeader", '89');
        $this->getTemplate()->set_var("widthLogoHeader", '156');
        $this->getTemplate()->set_var("tituloHeader", $nombreSitio);

        $this->getTemplate()->load_file_section("gui/vistas/index/login.gui.html", "columnaIzquierdaContent", "FormularioBlock");
        $this->getTemplate()->set_var("sFormAction", $actionFormUrl);
        $this->getTemplate()->set_var("sNextUrl", $nextFormUrl);

        $this->getTemplate()->set_var("footerContent", $footerContent);

        //Si vino a Login por error de permiso muestro ficho con advertencia y link a inicio
        if($this->getRequest()->has('msgError') || $this->getRequest()->has('msgInfo')){
            if($this->getRequest()->has('msgError')){
                $tituloMensajeError = $this->getRequest()->getParam('msgError');
                $ficha = "MsgFichaErrorBlock";
            }else{
                $tituloMensajeError = $this->getRequest()->getParam('msgInfo');
                $ficha = "MsgFichaInfoBlock";
            }

            $mensajeInfoError = "La página que solicitaste no se puede mostrar en este momento.
                                 Puede que esté temporalmente fuera de servicio,
                                 que el enlace donde hiciste clic haya expirado o que no tengas permiso para ver esta página.";

            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "columnaDerechaContent", $ficha);
            $this->getTemplate()->set_var("sTituloMsgFicha", $tituloMensajeError);
            $this->getTemplate()->set_var("sMsgFicha", $mensajeInfoError);

            //Link a Inicio
            $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "itemExtraMsgFicha", "MenuVertical02Block");
            $this->getTemplate()->unset_blocks("OpcionesMenu"); //solo uso un link
            $this->getTemplate()->set_var("idOpcion", 'opt1');
            $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/');
            $this->getTemplate()->set_var("sNombreOpcion", "Volver a inicio");
        }
        
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }
}