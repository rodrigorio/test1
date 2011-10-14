<?php

/**
 * Page Controller para las vistas basicas del modulo seguimientos.
 *
 * Es Singleton para que se pueda reutilizar los pedazos del header y el footer.
 */
class IndexControllerSeguimientos extends PageControllerAbstract
{    
   private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/seguimientos/frame01-01.gui.html", "frame");
        return $this;
    }

    private function setHeadTag()
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
        $tituloVista = $nombreSitio.' | '.$parametros->obtener('METATAG_TITLE');
        $descriptionVista = $parametros->obtener('METATAG_DESCRIPTION');
        $keywordsVista = $parametros->obtener('METATAG_KEYWORDS');

        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);

        //js de home
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/home.gui.html", "jsContent", "JsContent");
        
        return $this;
    }

    /**
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     *
     */
    static function setCabecera(Templates &$template)
    {
        $request = FrontController::getInstance()->getRequest();

        //links menu top comunidad
        $template->set_var("topHeaderMenuHrefComunidad", $request->getBaseUrl().'/comunidad/home');
        $template->set_var("topHeaderMenuHrefPublicaciones", $request->getBaseUrl().'/comunidad/publicaciones');
        $template->set_var("topHeaderMenuHrefInstituciones", $request->getBaseUrl().'/comunidad/instituciones');
        $template->set_var("topHeaderMenuHrefDescargas", $request->getBaseUrl().'/comunidad/descargas');
        $template->set_var("topHeaderMenuHrefSeguimientos", $request->getBaseUrl().'/seguimientos/home');
        $template->set_var("topHeaderMenuHrefDatosPersonales", $request->getBaseUrl().'/comunidad/datos-personales');
        $template->set_var("topHeaderMenuHrefInvitaciones", $request->getBaseUrl().'/comunidad/invitaciones');
        $template->set_var("topHeaderMenuHrefSoporte", $request->getBaseUrl().'/comunidad/soporte');
        $template->set_var("topHeaderMenuHrefCerrarSesion", $request->getBaseUrl().'/logout');
        $template->set_var("topHeaderMenuHrefPreferencias", $request->getBaseUrl().'/comunidad/preferencias');
    }

    /**
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     *
     */
    static function setCenterHeader(Templates &$template){
        $request = FrontController::getInstance()->getRequest();
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $perfilDesc = $perfil->getDescripcion();
        $nombreUsuario = $perfil->getNombreUsuario();

        $template->set_var("nombreUsuarioLogged", $nombreUsuario);
        $template->set_var("hrefEditarPerfil", $request->getBaseUrl().'/comunidad/datos-personales');
        $template->set_var("hrefAdministrador", $request->getBaseUrl().'/admin/home');
        //si no es moderador o admin quito el boton al administrador
        if($perfilDesc != 'administrador' && $perfilDesc != 'moderador'){
            $template->set_var("AdministradorButton", "");
        }        
    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            $this->setCabecera($this->getTemplate());
            $this->setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Seguimientos - Inicio");
            $this->getTemplate()->set_var("hrefCrearSeguimientos", "seguimientos/nuevo-seguimiento");
            
            //contenido ppal home comunidad
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/home.gui.html", "pageRightInnerMainCont", "PageRightInnerMainContBlock");


            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));            
         }catch(Exception $e){
            print_r($e);
        }
    }    
    public function nuevoSeguimiento(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            $this->setCabecera($this->getTemplate());
            $this->setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Seguimientos - Inicio");
            $this->getTemplate()->set_var("hrefCrearSeguimientos", "seguimientos/nuevo-seguimiento");

            //contenido ppal home comunidad
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "FormularioBlock");
            $listaTiposSeguimiento = array();
            $obj = new stdClass();
            $oTipoSeg = Factory::getTipoSeguimientoInstance($obj);
            $listaTiposSeguimiento = $oTipoSeg->getLista();
            foreach ($listaTiposSeguimiento as $key=>$value){
                $this->getTemplate()->set_var("iSeguimientoTiposId", $key);
                $this->getTemplate()->set_var("sSeguimientoTiposNombre", $value);
                $this->getTemplate()->parse("ListaTipoDeSeguimientosBlock", true);
            }
            $oTipoPractica = Factory::getTipoPracticasSeguimientoInstance($obj);
            $listaTiposPracticaSeguimiento = $oTipoPractica->getLista();
            foreach ($listaTiposPracticaSeguimiento as $key=>$value){
                $this->getTemplate()->set_var("iSeguimientoTiposPracticaId", $key);
                $this->getTemplate()->set_var("sSeguimientoTiposPracticaNombre", $value);
                $this->getTemplate()->parse("ListaTipoDePracticaSeguimientosBlock", true);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }
    public function buscarUsuarios(){
        //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();
            $iRecordsTotal = 0;
            $sOrderBy=$sOrder=$iIniLimit=$iRecordCount=null;
            $filtro = array("p.numeroDocumento"=>$this->getRequest()->get('str'));
            $vUsuarios = SysController::getInstance()->buscarUsuarios($filtro, $iRecordsTotal,$sOrderBy,$sOrder,$iIniLimit,$iRecordCount);
            $vResult = array();
            if(count($vUsuarios)>0){
                foreach($vUsuarios as $oUsuario){
                    $obj        = new stdClass();
                    $obj->iId   = $oUsuario->getId();
                    $obj->sNombre   = $oUsuario->getUsuario()->getNombre() . " " . $oUsuario->getUsuario()->getApellido();
                    $vResult[] = $obj;
                }
            }
            //agrega una url para que el js redireccione
            $this->getJsonHelper()->setSuccess(true)->setValor("usuarios",$vResult);
         }catch(Exception $e){
            print_r($e);
        }
        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    public function procesarSeguimiento(){
        try{
            $iTipoSeguimiento = $this->getRequest()->getPost('tipoSeguimiento');
            $iPersona       = $this->getRequest()->getPost('persona');
            $sFrecuencias   = $this->getRequest()->getPost('frecuencias');
            $sDiaHorario    = $this->getRequest()->getPost('diaHorario');
            $iTipoPractica  = $this->getRequest()->getPost('tipoPractica');
            $obj = new stdClass();
            $oTipoSeg = Factory::getTipoSeguimientoInstance($obj);
            $sTipoSeguimiento = $oTipoSeg->getTipoById($iTipoSeguimiento);
            if($sTipoSeguimiento == "SCC" ){

            }elseif( $sTipoSeguimiento == "PERSONALIZADO"){

            }
         }catch(Exception $e){
            print_r($e);
        }
    }
}