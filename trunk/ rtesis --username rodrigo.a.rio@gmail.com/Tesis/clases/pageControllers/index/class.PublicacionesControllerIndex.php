<?php

/**
 * @author Matias Velilla
 */
class PublicacionesControllerIndex extends PageControllerAbstract
{
    //el de fecha se hace automatico en la funcion initFiltrosForm
    private $filtrosFormConfig = array('filtroTitulo' => 'f.titulo',
                                       'filtroApellidoAutor' => 'ap.apellido',
                                       'filtroFechaDesde' => 'fechaDesde');
        
    /**
     * Setea el Head para las vistas de Instituciones
     */
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

        $this->getTemplate()->load_file_section("gui/vistas/index/publicaciones.gui.html", "jsContent", "JsContent");

        return $this;
    }
    
    public function index(){
        $this->listar();
    }
  
    private function listar()
    {
        $this->getTemplate()->load_file("gui/templates/index/frame01-02.gui.html", "frame");        
        $this->setHeadTag();

        $this->printMsgTop();

        //titulo seccion
        $this->getTemplate()->set_var("sNombreSeccionTopPage", "Publicaciones Comunidad");
        
        $this->getTemplate()->load_file_section("gui/vistas/index/publicaciones.gui.html", "columnaIzquierdaContent", "ListadoPublicacionesBlock");
        $this->getTemplate()->load_file_section("gui/vistas/index/publicaciones.gui.html", "columnaDerechaContent", "BuscarPublicacionesBlock");
        $this->getTemplate()->load_file_section("gui/vistas/index/publicaciones.gui.html", "topPageContent", "DescripcionSeccionBlock");

        IndexControllerIndex::setFooter($this->getTemplate());
                 
        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aFichas = ComunidadController::getInstance()->buscarPublicacionesVisitantes($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aFichas) > 0){

            foreach($aFichas as $oFicha){

                $oUsuario = $oFicha->getUsuario();
                $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();
                $sTipoPublicacion = (get_class($oFicha) == "Publicacion")?"Publicación":"Review";

                $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());
                $this->getTemplate()->set_var("sTipoPublicacion", $sTipoPublicacion);                
                $this->getTemplate()->set_var("sDescripcionBreve", $oFicha->getDescripcionBreve());

                $sTituloUrl = $this->getInflectorHelper()->urlize($oFicha->getTitulo());
                if(get_class($oFicha) == 'Publicacion'){
                    $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/publicaciones/'.$oFicha->getId()."-".$sTituloUrl);
                }else{
                    $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/reviews/'.$oFicha->getId()."-".$sTituloUrl);
                }

                $this->thumbDestacadoFicha($oFicha);
                $this->getTemplate()->parse("PublicacionBlock", true);
            }
            
            $this->getTemplate()->set_var("NoRecordsPublicacionesBlock", "");

            $params[] = "masPublicaciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "publicaciones/procesar", "listadoPublicacionesResult", $params);
        }else{
            $this->getTemplate()->set_var("PublicacionBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "Por el momento no hay publicaciones disponibles");
            $this->getTemplate()->parse("NoRecordsPublicacionesBlock");
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    private function thumbDestacadoFicha($oFicha)
    {
        //thumb destacado. (muestro foto o video con menor numero de orden)
        $thumbDestacado = "";
        $oFoto = ComunidadController::getInstance()->getFotoDestacadaFicha($oFicha->getId());
        if(null !== $oFoto){
            //agrego thumbnail foto
            $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "thumbFoto", "ThumbnailFotoSingleBlock");
            $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
            $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
            $this->getTemplate()->set_var("hrefFoto", $pathFotoServidorBigSize);
            $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
            $this->getTemplate()->set_var("tituloFoto", $oFoto->getTitulo());
            $this->getTemplate()->set_var("descripcionFoto", $oFoto->getDescripcion(true));

            $thumbDestacado = $this->getTemplate()->pparse("thumbFoto");
        }else{
            $oEmbedVideo = ComunidadController::getInstance()->getEmbedVideoDestacadoFicha($oFicha->getId());
            if(null !== $oEmbedVideo){
                //agrego thumbnail video
                $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "thumbVideo", "ThumbnailVideoSingleBlock");
                $urlFotoThumbnail = $this->getEmbedVideoHelper()->getEmbedVideoThumbnail($oEmbedVideo);
                $hrefAmpliarVideo = $this->getUrlFromRoute("indexIndexVideoAmpliar", true)."?id=".$oEmbedVideo->getId()."&v=".$oEmbedVideo->getUrlKey();
                $this->getTemplate()->set_var("hrefAmpliarVideo", $hrefAmpliarVideo);
                $this->getTemplate()->set_var("urlFoto", $urlFotoThumbnail);
                $this->getTemplate()->set_var("tituloVideo", $oEmbedVideo->getTitulo());
                $this->getTemplate()->set_var("descripcionVideo", $oEmbedVideo->getDescripcion());
                $thumbDestacado = $this->getTemplate()->pparse("thumbVideo");
            }
        }
        $this->getTemplate()->set_var("thumbDestacado", $thumbDestacado);        
    }

    public function procesar()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('masPublicaciones')){
            $this->masPublicaciones();
            return;
        }
    }

    private function masPublicaciones()
    {
        $this->getTemplate()->load_file_section("gui/vistas/index/publicaciones.gui.html", "ajaxFichasPublicacionesBlock", "FichasPublicacionesBlock");

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aFichas = ComunidadController::getInstance()->buscarPublicacionesVisitantes($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aFichas) > 0){

            $this->getTemplate()->set_var("NoRecordsPublicacionesBlock", "");

            foreach($aFichas as $oFicha){

                $oUsuario = $oFicha->getUsuario();
                $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();
                $sTipoPublicacion = (get_class($oFicha) == "Publicacion")?"Publicación":"Review";

                $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());
                $this->getTemplate()->set_var("sTipoPublicacion", $sTipoPublicacion);
                $this->getTemplate()->set_var("sDescripcionBreve", $oFicha->getDescripcionBreve());

                $sTituloUrl = $this->getInflectorHelper()->urlize($oFicha->getTitulo());
                if(get_class($oFicha) == 'Publicacion'){
                    $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/publicaciones/'.$oFicha->getId()."-".$sTituloUrl);
                }else{
                    $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/reviews/'.$oFicha->getId()."-".$sTituloUrl);
                }

                $this->thumbDestacadoFicha($oFicha);
                $this->getTemplate()->parse("PublicacionBlock", true);
            }

            $paramsPaginador[] = "masPublicaciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "publicaciones/procesar", "listadoPublicacionesResult", $paramsPaginador);
        }else{
            $this->getTemplate()->set_var("PublicacionBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No se encontraron resultados");
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxFichasPublicacionesBlock', false));
    }

    /**
     * Este metodo tiene un par de cuestiones.
     *
     * VALIDACION 1.
     * Si el id de publicacion no existe -> la url existe (porque entro al metodo) pero esta incompleta.
     *
     * VALIDACION 2.
     * Si el id existe pero cuando se hace el getById devuelve null quiere decir que la publicacion se elimino,
     * entonces se redirecciona al listado de publicaciones con header 404.
     * Si la publicacion existe pero no esta marcada como publica entonces la direccion tampoco existe.
     *
     * VALIDACION 3.
     * Si el id existe y cuando se hace el getById la publicacion esta 'desactivada'
     * se redirecciona al listado de publicaciones con header de redireccion temporal.
     *
     * VALIDACION 4.
     * Lo mismo que la validacion anterior pero la publicacion paso a tener como estado
     * de moderacion 'rechazado'
     *
     * VALIDACION 5.
     * Si el id existe, se obtiene la publicacion y cuando se compara el titulo en formato url con el parametro del titulo
     * en formato url devuelve que son distintos entonces quiere decir que el link de la url amigable cambio.
     * Por lo que se tiene que hacer una redireccion y recargar la pagina con el link nuevo.
     *
     * por ejemplo si el titulo de una publicacion era "novedades de enero" y el ID era 20
     * el link es www.dominio.com/publicaciones/20-novedades-de-enero
     * si luego el titulo cambia a "novedades para este verano" el link que empieza a circular es
     * www.dominio.com/publicaciones/20-novedades-para-este-verano.
     * Si alguien guardo el link viejo se compara en este metodo y se redirecciona a la url nueva
     * con el header de redireccion.
     *
     */
    public function verPublicacion()
    {        
        try{
            $iPublicacionId = $this->getRequest()->getParam('iPublicacionId');
            $sTituloUrlized = $this->getRequest()->getParam('sTituloUrlized');

            //validacion 1.
            if(empty($iPublicacionId))
            {
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            //validacion 2.
            $oPublicacion = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
            if(null === $oPublicacion || !$oPublicacion->isPublico())
            {
                throw new Exception("", 404);
            }

            //validacion 3.
            if(!$oPublicacion->isActivo() || !$oPublicacion->getModeracion()->isAprobado()){
                $this->getRedirectorHelper()->setCode(307);
                $url = $this->getUrlFromRoute("indexPublicacionesIndex");
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            //validacion 4.
            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oPublicacion->getTitulo());

            if($sTituloUrlized != $sTituloUrlizedActual){
                $this->getRedirectorHelper()->setCode(301);
                $url = 'publicaciones/'.$oPublicacion->getId()."-".$sTituloUrlizedActual;
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            //paso todas las validaciones muestro la vista

            $this->getTemplate()->load_file("gui/templates/index/frame01-01.gui.html", "frame");
            $this->setHeadTag();

            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("sNombreSeccionTopPage", "Publicaciones Comunidad");

            $this->getTemplate()->load_file_section("gui/vistas/index/publicaciones.gui.html", "columnaIzquierdaContent", "ListadoPublicacionesBlock");
            $this->getTemplate()->load_file_section("gui/vistas/index/publicaciones.gui.html", "columnaDerechaContent", "BuscarPublicacionesBlock");
            $this->getTemplate()->load_file_section("gui/vistas/index/publicaciones.gui.html", "topPageContent", "DescripcionSeccionBlock");

            IndexControllerIndex::setFooter($this->getTemplate());

            $this->getTemplate()->load_file_section("gui/vistas/index/publicaciones.gui.html", "centerPageContent", "PublicacionAmpliadaBlock");

            $oUsuarioAutor = $oPublicacion->getUsuario();
            $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuarioAutor->getNombreAvatar();

            $sNombreAutor = $oUsuarioAutor->getApellido()." ".$oUsuarioAutor->getNombre();
            $sTipoPublicacion = "Publicacion";

            $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
            $this->getTemplate()->set_var("sTitulo", $oPublicacion->getTitulo());
            $this->getTemplate()->set_var("sFecha", $oPublicacion->getFecha());
            $this->getTemplate()->set_var("sAutor", $sNombreAutor);
            $this->getTemplate()->set_var("sTipoPublicacion", $sTipoPublicacion);
            $this->getTemplate()->set_var("sDescripcionBreve", $oPublicacion->getDescripcionBreve());
            $this->getTemplate()->set_var("sDescripcion", $oPublicacion->getDescripcion(true));

            //si tiene el mail abierto al publico lo muestro
            $aPrivacidad = $oUsuarioAutor->obtenerPrivacidad();
            if($aPrivacidad['email'] == 'publico' && null !== $oUsuarioAutor->getEmail()){
                $this->getTemplate()->set_var("sEmail", $oUsuarioAutor->getEmail());
                $this->getTemplate()->parse("EmailAutorBlock");
            }else{
                $this->getTemplate()->set_var("EmailAutorBlock", "");
            }
           
            $this->agregarGaleriaAdjuntosFicha($oPublicacion);
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
            
        }catch(Exception $e){
            //esto tiene que quedar asi porque si hay excepcion 404 se devuelve entero para q lo reconozca el plugin
            throw $e;
        }            
    }

    public function verReview()
    {
        try{
            $iReviewId = $this->getRequest()->getParam('iReviewId');
            $sTituloUrlized = $this->getRequest()->getParam('sTituloUrlized');

            //validacion 1.
            if(empty($iReviewId))
            {
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            //validacion 2.
            $oReview = ComunidadController::getInstance()->getReviewById($iReviewId);
            if(null === $oReview)
            {
                throw new Exception("", 404);
            }

            //validacion 3.
            if(!$oReview->isActivo()){
                $this->getRedirectorHelper()->setCode(307);
                $url = $this->getUrlFromRoute("comunidadPublicacionesIndex");
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            //validacion 4.
            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oReview->getTitulo());

            if($sTituloUrlized != $sTituloUrlizedActual){
                $this->getRedirectorHelper()->setCode(301);
                $url = 'comunidad/reviews/'.$oReview->getId()."-".$sTituloUrlizedActual;
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            $this->setFrameTemplate()
                 ->setHeadTag()
                 ->setMenuDerecha();

            IndexControllerComunidad::setCabecera($this->getTemplate());
            IndexControllerComunidad::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Publicaciones Comunidad");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "pageRightInnerMainCont", "ReviewAmpliadaBlock");

            $oUsuarioAutor = $oReview->getUsuario();
            $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuarioAutor->getNombreAvatar();

            $sNombreAutor = $oUsuarioAutor->getApellido()." ".$oUsuarioAutor->getNombre();
            $sTipoPublicacion = "Review";

            $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
            $this->getTemplate()->set_var("sTitulo", $oReview->getTitulo());
            $this->getTemplate()->set_var("sFecha", $oReview->getFecha());
            $this->getTemplate()->set_var("sAutor", $sNombreAutor);
            $this->getTemplate()->set_var("sTipoPublicacion", $sTipoPublicacion);
            $this->getTemplate()->set_var("sDescripcionBreve", $oReview->getDescripcionBreve());
            $this->getTemplate()->set_var("sDescripcion", $oReview->getDescripcion(true));

            //detalles review
            $this->getTemplate()->set_var("sItemName", $oReview->getItemName());
            
            if(null !== $oReview->getItemType()){
                $sItemType = "";

                switch($oReview->getItemType()){
                    case "product":
                        $sItemType = "producto"; break;
                    case "business":
                        $sItemType = "negocio"; break;
                    case "event":
                        $sItemType = "evento"; break;
                    case "person":
                        $sItemType = "persona"; break;
                    case "place":
                        $sItemType = "lugar"; break;
                    case "website":
                        $sItemType = "sitio web"; break;
                    case "url":
                        $sItemType = "link"; break;
                }

                $this->getTemplate()->set_var("sItemType", $sItemType);
                $this->getTemplate()->parse("ItemTypeBlock");
            }else{
                $this->getTemplate()->set_var("ItemTypeBlock", "");
            }
            
            if(null !== $oReview->getItemEventSummary()){
                $this->getTemplate()->set_var("sItemEventSummary", $oReview->getItemEventSummary());
                $this->getTemplate()->parse("ItemEventSummaryBlock");
            }else{
                $this->getTemplate()->set_var("ItemEventSummaryBlock", "");
            }

            if(null !== $oReview->getItemUrl()){
                $this->getTemplate()->set_var("hrefItemUrl", $oReview->getItemUrl());
                $this->getTemplate()->set_var("sItemUrl", $oReview->getItemUrl());
                $this->getTemplate()->parse("ItemUrlBlock");
            }else{
                $this->getTemplate()->set_var("ItemUrlBlock", "");
            }

            $valoracion = "";
            if(null !== $oReview->getRating()){

                $fValoracion = $oReview->getRating();

                switch($fValoracion){
                    case ($fValoracion >= 0 && $fValoracion < 0.5): $valoracionBloque = 'Valoracion0Block'; break;
                    case ($fValoracion >= 0.5 && $fValoracion < 1): $valoracionBloque = 'Valoracion0_2Block'; break;
                    case ($fValoracion >= 1 && $fValoracion < 1.5): $valoracionBloque = 'Valoracion1Block'; break;
                    case ($fValoracion >= 1.5 && $fValoracion < 2): $valoracionBloque = 'Valoracion1_2Block'; break;
                    case ($fValoracion >= 2 && $fValoracion < 2.5): $valoracionBloque = 'Valoracion2Block'; break;
                    case ($fValoracion >= 2.5 && $fValoracion < 3): $valoracionBloque = 'Valoracion2_2Block'; break;
                    case ($fValoracion >= 3 && $fValoracion < 3.5): $valoracionBloque = 'Valoracion3Block'; break;
                    case ($fValoracion >= 3.5 && $fValoracion < 4): $valoracionBloque = 'Valoracion3_2Block'; break;
                    case ($fValoracion >= 4 && $fValoracion < 4.5): $valoracionBloque = 'Valoracion4Block'; break;
                    case ($fValoracion >= 4.5 && $fValoracion < 5): $valoracionBloque = 'Valoracion4_2Block'; break;
                    case ($fValoracion >= 5): $valoracionBloque = 'Valoracion5Block'; break;
                    default: $valoracionBloque = 'Valoracion0Block'; break;
                }

                $this->getTemplate()->load_file_section("gui/componentes/valoracion.gui.html", "valoracion", $valoracionBloque);
                $valoracion = $this->getTemplate()->pparse("valoracion");
            }
            $this->getTemplate()->set_var("valoracion", $valoracion);
           
            if(null !== $oReview->getFuenteOriginal()){
                $this->getTemplate()->set_var("hrefFuenteUriginal", $oReview->getFuenteOriginal());
                $this->getTemplate()->set_var("sFuenteOriginal", $oReview->getFuenteOriginal());
                $this->getTemplate()->parse("FuenteOriginalBlock");
            }else{
                $this->getTemplate()->set_var("FuenteOriginalBlock", "");
            }

            $this->agregarGaleriaAdjuntosFicha($oReview);

            if($oReview->isActivoComentarios()){
                $this->listarComentariosFicha($oReview);

                $this->getTemplate()->set_var("iItemIdForm", $oReview->getId());
                $this->getTemplate()->set_var("sTipoItemForm", get_class($oReview));
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            //esto tiene que quedar asi porque si hay excepcion 404 se devuelve entero para q lo reconozca el plugin
            throw $e;
        }                    
    }

    private function agregarGaleriaAdjuntosFicha($oFicha)
    {
        list($cantFotos, $cantVideos, $cantArchivos) = ComunidadController::getInstance()->obtenerCantidadMultimediaFicha($oFicha->getId());

        if($cantFotos > 0 || $cantVideos > 0 || $cantArchivos > 0){

            $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "galeriaAdjuntos", "GaleriaAdjuntosBlock");

            //primero borro todos los bloques que ya se que no se usan
            $this->getTemplate()->set_var("TituloItemBlock", "");
            $this->getTemplate()->set_var("MenuGaleriaAdjuntos", "");
            $this->getTemplate()->set_var("ThumbnailFotoEditBlock", "");
            $this->getTemplate()->set_var("NoRecordsFotosBlock", "");
            $this->getTemplate()->set_var("ThumbnailVideoEditBlock", "");
            $this->getTemplate()->set_var("NoRecordsVideosBlock", "");
            $this->getTemplate()->set_var("RowArchivoEditBlock", "");
            $this->getTemplate()->set_var("NoRecordsArchivosBlock", "");

            if($cantFotos > 0){

                $aFotos = $oFicha->getFotos();

                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

                foreach($aFotos as $oFoto){
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFoto", $pathFotoServidorBigSize);
                    $this->getTemplate()->set_var("descripcionFoto", $oFoto->getDescripcion(true));
                    $this->getTemplate()->set_var("tituloFoto", $oFoto->getTitulo());
                    $this->getTemplate()->parse("ThumbnailFotoBlock", true);
                }

            }else{
                $this->getTemplate()->set_var("GaleriaAdjuntosFotosBlock", "");
            }

            if($cantVideos > 0)
            {
                $aEmbedVideos = $oFicha->getEmbedVideos();

                foreach($aEmbedVideos as $oEmbedVideo){

                    $urlFotoThumbnail = $this->getEmbedVideoHelper()->getEmbedVideoThumbnail($oEmbedVideo);
                    $hrefAmpliarVideo = $this->getUrlFromRoute("indexIndexVideoAmpliar", true)."?id=".$oEmbedVideo->getId()."&v=".$oEmbedVideo->getUrlKey();

                    $this->getTemplate()->set_var("hrefAmpliarVideo", $hrefAmpliarVideo);
                    $this->getTemplate()->set_var("urlFoto", $urlFotoThumbnail);
                    $this->getTemplate()->set_var("tituloVideo", $oEmbedVideo->getTitulo());
                    $this->getTemplate()->set_var("descripcionVideo", $oEmbedVideo->getDescripcion());
                    $this->getTemplate()->parse("ThumbnailVideoBlock", true);
                }

            }else{
                $this->getTemplate()->set_var("GaleriaAdjuntosVideosBlock", "");
            }

            if($cantArchivos > 0)
            {
                $aArchivos = $oFicha->getArchivos();

                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

                foreach($aArchivos as $oArchivo){

                    $nombreArchivo = $oArchivo->getTitulo();
                    if(empty($nombreArchivo)){
                        $nombreArchivo = $oArchivo->getNombre();
                    }

                    $hrefDescargar = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();

                    $this->getTemplate()->set_var("sNombreArchivo", $nombreArchivo);
                    $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                    $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                    $this->getTemplate()->set_var("hrefDescargar", $hrefDescargar);

                    $sTitulo = $oArchivo->getTitulo();
                    $sDescripcion = $oArchivo->getDescripcion();
                    if(empty($sTitulo) && empty($sDescripcion)){
                        $this->getTemplate()->set_var("TituloInfoArchivoBlock", "");
                        $this->getTemplate()->set_var("DescripcionInfoArchivoBlock", "");
                    }else{
                        if(empty($sTitulo)){
                            $this->getTemplate()->set_var("TituloInfoArchivoBlock", "");
                        }else{
                            $this->getTemplate()->set_var("tituloArchivo", $sTitulo);
                        }

                        if(empty($sDescripcion)){
                            $this->getTemplate()->set_var("DescripcionInfoArchivoBlock", "");
                        }else{
                            $this->getTemplate()->set_var("descripcionArchivo", $sDescripcion);
                        }                        
                    }

                    $this->getTemplate()->parse("RowArchivoBlock", true);

                    $this->getTemplate()->delete_parsed_blocks("InfoArchivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("TituloInfoArchivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("DescripcionInfoArchivoBlock");
                }
            }else{
                $this->getTemplate()->set_var("GaleriaAdjuntosArchivosBlock", "");
            }
        }
    }

    /**
     * Agrega la funcionalidad de mostrar las ultimas publicaciones en la comunidad
     */
    public function redireccion404()
    {
        try{            
            $tituloMensajeError = "No se ha encontrado la publicación solicitada.";
            $ficha = "MsgFichaInfoBlock";

            $this->getTemplate()->load_file("gui/templates/index/frame02-01.gui.html", "frame");

            $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
            $this->getTemplate()->set_var("sTituloVista", "La publicacion no existe");
            $this->getTemplate()->set_var("sMetaDescription", "");
            $this->getTemplate()->set_var("sMetaKeywords", "");

            $this->getTemplate()->load_file_section("gui/vistas/index/redireccion404.gui.html", "centerPageContent", "TituloBlock");

            $mensajeInfoError = "Puedes que hayas ingresado un enlace caducado o que hayas escrito mal la dirección.
                                 En algunas direcciones web se distingue entre mayúsculas y minúsculas.";

            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "centerPageContent", $ficha, true);
            $this->getTemplate()->set_var("sTituloMsgFicha", $tituloMensajeError);
            $this->getTemplate()->set_var("sMsgFicha", $mensajeInfoError);

            //Link a Inicio y pagina anterior
            $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "itemExtraMsgFicha", "MenuVertical02Block");
            $this->getTemplate()->set_var("idOpcion", 'opt1');
            $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/');
            $this->getTemplate()->set_var("sNombreOpcion", "Volver a inicio");
            $this->getTemplate()->parse("OpcionesMenu", true);

            $this->getTemplate()->set_var("idOpcion", 'opt1');
            $this->getTemplate()->set_var("hrefOpcion", "javascript:history.go(-1)");
            $this->getTemplate()->set_var("sNombreOpcion", "Volver a la página anterior");
            $this->getTemplate()->parse("OpcionMenuLastOpt");

            //listado con las ultimas 10 publicaciones para que siga navegando el usuario.
            $iRecordsTotal = 0;
            $aFichas = ComunidadController::getInstance()->buscarPublicacionesComunidad($filtro = null, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iMinLimit = 1, $iItemsForPage = 10);
            if(count($aFichas) > 0){

                $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "centerPageContent", "UltimasPublicacionesBlock", true);

                foreach($aFichas as $oFicha){

                    $oUsuario = $oFicha->getUsuario();
                    $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();

                    $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                    $this->getTemplate()->set_var("sAutor", $sNombreUsuario);

                    $sTituloUrl = $this->getInflectorHelper()->urlize($oFicha->getTitulo());
                    if(get_class($oFicha) == 'Publicacion'){
                        $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/comunidad/publicaciones/'.$oFicha->getId()."-".$sTituloUrl);
                    }else{
                        $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/comunidad/reviews/'.$oFicha->getId()."-".$sTituloUrl);
                    }

                    $this->getTemplate()->parse("PublicacionRowBlock", true);
                }
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
}