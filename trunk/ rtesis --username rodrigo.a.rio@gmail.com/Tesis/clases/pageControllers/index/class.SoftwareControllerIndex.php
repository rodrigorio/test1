<?php

/**
 * @author Matias Velilla
 */
class SoftwareControllerIndex extends PageControllerAbstract
{    
    private $filtrosFormConfig = array('filtroTitulo' => 'f.titulo', 'filtroCategoria' => 's.categorias_id');
        
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

        $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "jsContent", "JsContent");

        return $this;
    }
    
    public function index(){
        $this->listar();
    }
  
    /**
     * Lista todas las categorias.
     * listado de todos los programas paginados ordenados por fecha de mayor a menor.
     * filtro por titulo.
     */
    private function listar()
    {
        $this->getTemplate()->load_file("gui/templates/index/frame01-02.gui.html", "frame");
        $this->setHeadTag();

        $this->printMsgTop();

        //titulo seccion
        $this->getTemplate()->set_var("sNombreSeccionTopPage", "Descargas");

        $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "columnaIzquierdaContent", "ListadoSoftwareBlock");
        $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "columnaDerechaContent", "BuscarAplicacionesBlock");
        $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "topPageContent", "DescripcionSeccionBlock");

        IndexControllerIndex::setFooter($this->getTemplate());

        $this->listarCategorias();

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aSoftware = ComunidadController::getInstance()->buscarSoftwareVisitantes($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        //lo separo en un metodo privado porque lo reutilizo en el listado por categoria
        $this->listarFichas($aSoftware, $iItemsForPage, $iPage, $iRecordsTotal, $paramsPaginador = array());

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    /**
     * Todo el software para una categoria.
     *
     * si la categoria no existe redirecciona al listado principal.
     */
    public function listarCategoria()
    {        
        $sUrlToken = $this->getRequest()->getParam('sUrlToken');

        $oCategoria = ComunidadController::getInstance()->obtenerCategoriaByUrlToken($sUrlToken);
        if(null === $oCategoria)
        {            
            //el plugin redireccion 404 va a ejecutar el metodo redireccion404 de este controlador
            throw new Exception("", 404);
        }

        $this->getTemplate()->load_file("gui/templates/index/frame01-02.gui.html", "frame");
        $this->setHeadTag();

        $this->printMsgTop();

        //titulo seccion
        $this->getTemplate()->set_var("sNombreSeccionTopPage", "Descargas");

        $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "columnaIzquierdaContent", "ListadoSoftwareBlock");
        $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "columnaDerechaContent", "BuscarAplicacionesBlock");
        $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "topPageContent", "DescripcionSeccionBlock");

        IndexControllerIndex::setFooter($this->getTemplate());

        $this->listarCategorias($oCategoria);
        
        $this->getTemplate()->set_var("iCategoriaIdFiltro", $oCategoria->getId());

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        //en este metodo alcanza con esto porque en principio no hay filtros
        $aSoftware = ComunidadController::getInstance()->obtenerSoftwareCategoriaVisitantes($oCategoria->getId());
        $iRecordsTotal = count($aSoftware);

        $paramsPaginador[] = "filtroCategoria=".$oCategoria->getId();
        $this->listarFichas($aSoftware, $iItemsForPage, $iPage, $iRecordsTotal, $paramsPaginador);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    /**
     * crea listado de categorias en el top del listado de fichas de software
     */
    private function listarCategorias($oCategoriaActual = null)
    {
        $iRecordsTotal = 0;
        $aCategorias = ComunidadController::getInstance()->obtenerCategoria($filtro = array(), $iRecordsTotal, null, null, null, null);
        
        if(null !== $aCategorias){

            foreach($aCategorias as $oCategoria){
                $hrefSofwareCategoria = 'descargas/'.$oCategoria->getUrlToken();
                $this->getTemplate()->set_var("sDescripcionCategoria", $oCategoria->getDescripcion());
                $this->getTemplate()->set_var("hrefSoftwareCategoria", $hrefSofwareCategoria);
                $this->getTemplate()->set_var("sNombreCategoria", $oCategoria->getNombre());

                if(null !== $oCategoriaActual){
                    if($oCategoriaActual->getId() == $oCategoria->getId()){
                        $this->getTemplate()->set_var("categoriaActual", "selected");
                    }
                }

                $this->getTemplate()->parse("CategoriaRowBlock", true);
                
                $this->getTemplate()->set_var("categoriaActual", "");
            }
        }
    }

    private function listarFichas($aSoftware, $iItemsForPage, $iPage, $iRecordsTotal, $paramsPaginador)
    {
        if(count($aSoftware) > 0){

            foreach($aSoftware as $oSoftware){

                $oUsuario = $oSoftware->getUsuario();
                $oCategoria = $oSoftware->getCategoria();
                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();
                
                $this->getTemplate()->set_var("sNombreCategoria", $oCategoria->getNombre());
                $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                $this->getTemplate()->set_var("sTitulo", $oSoftware->getTitulo());
                $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                $this->getTemplate()->set_var("sFecha", $oSoftware->getFecha(true));
                $this->getTemplate()->set_var("sDescripcionBreve", $oSoftware->getDescripcionBreve());

                $ratingActual = "";
                $ratingBloque = "";
                if($oSoftware->tieneValoracion()){
                    $fRating = $oSoftware->getRating();
                    
                    switch($fRating){
                        case ($fRating >= 0 && $fRating < 0.5): $ratingBloque = 'Rating0Block'; break;
                        case ($fRating >= 0.5 && $fRating < 1): $ratingBloque = 'Rating0_2Block'; break;
                        case ($fRating >= 1 && $fRating < 1.5): $ratingBloque = 'Rating1Block'; break;
                        case ($fRating >= 1.5 && $fRating < 2): $ratingBloque = 'Rating1_2Block'; break;
                        case ($fRating >= 2 && $fRating < 2.5): $ratingBloque = 'Rating2Block'; break;
                        case ($fRating >= 2.5 && $fRating < 3): $ratingBloque = 'Rating2_2Block'; break;
                        case ($fRating >= 3 && $fRating < 3.5): $ratingBloque = 'Rating3Block'; break;
                        case ($fRating >= 3.5 && $fRating < 4): $ratingBloque = 'Rating3_2Block'; break;
                        case ($fRating >= 4 && $fRating < 4.5): $ratingBloque = 'Rating4Block'; break;
                        case ($fRating >= 4.5 && $fRating < 5): $ratingBloque = 'Rating4_2Block'; break;
                        case ($fRating >= 5): $ratingBloque = 'Rating5Block'; break;
                        default: $ratingBloque = 'Rating0Block'; break;
                    }

                    $this->getTemplate()->load_file_section("gui/componentes/valoracion.gui.html", "ratingActual", $ratingBloque);

                    $this->getTemplate()->set_var("fRating", $fRating);
                    $this->getTemplate()->set_var("cantValoraciones", $oSoftware->getCantidadValoraciones());
                    $ratingActual = $this->getTemplate()->pparse("ratingActual");
                }
                $this->getTemplate()->set_var("ratingActual", $ratingActual);
                $this->getTemplate()->delete_parsed_blocks($ratingBloque);

                $sTituloUrl = $this->getInflectorHelper()->urlize($oSoftware->getTitulo());
                $this->getTemplate()->set_var("hrefAmpliarSoftware", $this->getRequest()->getBaseUrl().'/descargas/'.$oCategoria->getUrlToken().'/'.$oSoftware->getId()."-".$sTituloUrl);

                $this->thumbDestacadoFicha($oSoftware);

                $this->getTemplate()->parse("SoftwareBlock", true);
            }

            $this->getTemplate()->set_var("NoRecordsSoftwareBlock", "");

            $paramsPaginador[] = "masAplicaciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "descargas/procesar", "listadoSoftwareResult", $paramsPaginador);
        }else{
            $this->getTemplate()->set_var("SoftwareBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "noRecords", "NoRecordsSoftwareBlock");
            $this->getTemplate()->set_var("sNoRecords", "No hay aplicaciones.");
            $this->getTemplate()->parse("noRecords", false);
        }
    }

    private function thumbDestacadoFicha($oSoftware)
    {
        $thumbDestacado = "";
        $oFoto = ComunidadController::getInstance()->getFotoDestacadaFicha($oSoftware->getId());
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
        }
        $this->getTemplate()->set_var("thumbDestacado", $thumbDestacado);        
    }

    public function procesar()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('masAplicaciones')){
            $this->masAplicaciones();
            return;
        }
    }

    private function masAplicaciones()
    {
        $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "ajaxFichasSoftwareBlock", "FichasSoftwareBlock");

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aSoftware = ComunidadController::getInstance()->buscarSoftwareVisitantes($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        $this->listarFichas($aSoftware, $iItemsForPage, $iPage, $iRecordsTotal, $paramsPaginador);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxFichasSoftwareBlock', false));
    }

    /**
     * Este metodo tiene un par de cuestiones.
     *
     * VALIDACION 1.
     * Si el id de publicacion no existe -> la url existe (porque entro al metodo) pero esta incompleta.
     *
     * VALIDACION 2.
     * Si el id existe pero cuando se hace el getById devuelve null quiere decir que la publicacion se elimino,
     * entonces se redirecciona al listado de publicaciones con header 404
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
     * se agrega la misma validacion para el nombre de la categoria
     *
     */
    public function verSoftware()
    {        
        try{
            $iSoftwareId = $this->getRequest()->getParam('iSoftwareId');
            $sTituloUrlized = $this->getRequest()->getParam('sTituloUrlized');
            $sUrlToken = $this->getRequest()->getParam('sUrlToken');
                        
            //validacion 1.
            if(empty($iSoftwareId))
            {
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            //validacion 2.
            $oSoftware = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);
            if(null === $oSoftware || !$oSoftware->isPublico())
            {
                //ojo que si este metodo redireccion404 tiene catch, entonces esta excepcion la tiene que devolver entera.
                //sino el plugin de redireccion 404 no levanta nada 
                throw new Exception("", 404);
            }

            //validacion 3.
            if(!$oSoftware->isActivo() || !$oSoftware->getModeracion()->isAprobado()){
                $this->getRedirectorHelper()->setCode(307);
                $url = $this->getUrlFromRoute("indexSoftwareIndex");
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            //validacion 4.
            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oSoftware->getTitulo());
            $sUrlTokenActual = $oSoftware->getCategoria()->getUrlToken();

            if( ($sTituloUrlized != $sTituloUrlizedActual) || ($sUrlToken != $sUrlTokenActual) ){
                $this->getRedirectorHelper()->setCode(301);
                $url = 'descargas/'.$sUrlTokenActual.'/'.$oSoftware->getId()."-".$sTituloUrlizedActual;
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            $this->getTemplate()->load_file("gui/templates/index/frame01-01.gui.html", "frame");
            $this->setHeadTag();

            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("sNombreSeccionTopPage", "Descargas");
            $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "topPageContent", "DescripcionSeccionBlock");

            IndexControllerIndex::setFooter($this->getTemplate());

            $this->getTemplate()->load_file_section("gui/vistas/index/software.gui.html", "centerPageContent", "AplicacionAmpliadaBlock");

            $oUsuarioAutor = $oSoftware->getUsuario();
            $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuarioAutor->getNombreAvatar();

            $sNombreAutor = $oUsuarioAutor->getApellido()." ".$oUsuarioAutor->getNombre();

            $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
            $this->getTemplate()->set_var("sTitulo", $oSoftware->getTitulo());
            $this->getTemplate()->set_var("sCategoria", $oSoftware->getCategoria()->getNombre());
            $this->getTemplate()->set_var("sFecha", $oSoftware->getFecha(true));
            $this->getTemplate()->set_var("sAutor", $sNombreAutor);
            $this->getTemplate()->set_var("sDescripcionBreve", $oSoftware->getDescripcionBreve());
            $this->getTemplate()->set_var("sDescripcion", $oSoftware->getDescripcion(true));

            //si tiene el mail abierto al publico lo muestro
            $aPrivacidad = $oUsuarioAutor->obtenerPrivacidad();
            if($aPrivacidad['email'] == 'publico' && null !== $oUsuarioAutor->getEmail()){
                $this->getTemplate()->set_var("sEmail", $oUsuarioAutor->getEmail());
                $this->getTemplate()->parse("EmailAutorBlock");
            }else{
                $this->getTemplate()->set_var("EmailAutorBlock", "");
            }

            $ratingActual = "";
            if($oSoftware->tieneValoracion()){                
                $fRating = $oSoftware->getRating();                

                switch($fRating){
                    case ($fRating >= 0 && $fRating < 0.5): $ratingBloque = 'Rating0Block'; break;
                    case ($fRating >= 0.5 && $fRating < 1): $ratingBloque = 'Rating0_2Block'; break;
                    case ($fRating >= 1 && $fRating < 1.5): $ratingBloque = 'Rating1Block'; break;
                    case ($fRating >= 1.5 && $fRating < 2): $ratingBloque = 'Rating1_2Block'; break;
                    case ($fRating >= 2 && $fRating < 2.5): $ratingBloque = 'Rating2Block'; break;
                    case ($fRating >= 2.5 && $fRating < 3): $ratingBloque = 'Rating2_2Block'; break;
                    case ($fRating >= 3 && $fRating < 3.5): $ratingBloque = 'Rating3Block'; break;
                    case ($fRating >= 3.5 && $fRating < 4): $ratingBloque = 'Rating3_2Block'; break;
                    case ($fRating >= 4 && $fRating < 4.5): $ratingBloque = 'Rating4Block'; break;
                    case ($fRating >= 4.5 && $fRating < 5): $ratingBloque = 'Rating4_2Block'; break;
                    case ($fRating >= 5): $ratingBloque = 'Rating5Block'; break;
                    default: $ratingBloque = 'Rating0Block'; break;
                }

                $this->getTemplate()->load_file_section("gui/componentes/valoracion.gui.html", "ratingActual", $ratingBloque);
               
                $this->getTemplate()->set_var("fRating", $fRating);
                $this->getTemplate()->set_var("cantValoraciones", $oSoftware->getCantidadValoraciones());                
                $ratingActual = $this->getTemplate()->pparse("ratingActual");
            }else{
                $ratingActual = "Sin Valoraciones";
            }            
            $this->getTemplate()->set_var("ratingActual", $ratingActual);

            if(null !== $oSoftware->getEnlaces()){
                $this->getTemplate()->set_var("sEnlaces", $oSoftware->getEnlaces(true));
                $this->getTemplate()->parse("EnlacesBlock");
            }else{
                $this->getTemplate()->set_var("EnlacesBlock", "");
            }
            
            $this->agregarGaleriaAdjuntosFicha($oSoftware);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            //esto tiene que quedar asi porque si hay excepcion 404 se devuelve entero para q lo reconozca el plugin
            throw $e;
        }                    
    }

    private function agregarGaleriaAdjuntosFicha($oFicha)
    {
        list($cantFotos, $cantVideos, $cantArchivos) = ComunidadController::getInstance()->obtenerCantidadMultimediaFicha($oFicha->getId());

        if($cantFotos > 0 || $cantArchivos > 0){

            $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "galeriaAdjuntos", "GaleriaAdjuntosBlock");

            //primero borro todos los bloques que ya se que no se usan
            $this->getTemplate()->set_var("TituloItemBlock", "");
            $this->getTemplate()->set_var("MenuGaleriaAdjuntos", "");
            $this->getTemplate()->set_var("ThumbnailFotoEditBlock", "");
            $this->getTemplate()->set_var("NoRecordsFotosBlock", "");
            $this->getTemplate()->set_var("GaleriaAdjuntosVideosBlock", "");
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
            $tituloMensajeError = "No se ha encontrado la aplicación o categoría solicitada.";
            $ficha = "MsgFichaInfoBlock";

            $this->getTemplate()->load_file("gui/templates/index/frame02-01.gui.html", "frame");

            $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
            $this->getTemplate()->set_var("sTituloVista", "La aplicación no existe o fue eliminada");
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

            //listado con las ultimas 10 aplicaciones para que siga navegando el usuario.
            $iRecordsTotal = 0;
            $aSoftware = ComunidadController::getInstance()->buscarSoftwareVisitantes($filtro = null, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iMinLimit = 1, $iItemsForPage = 10);
            if(count($aSoftware) > 0){

                $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "centerPageContent", "UltimasAplicacionesBlock", true);

                foreach($aSoftware as $oSoftware){

                    $this->getTemplate()->set_var("sTitulo", $oSoftware->getTitulo());
                    $this->getTemplate()->set_var("sCategoria", $oSoftware->getCategoria()->getNombre());

                    $sTituloUrl = $this->getInflectorHelper()->urlize($oSoftware->getTitulo());
                    $this->getTemplate()->set_var("hrefAmpliarSoftware", $this->getRequest()->getBaseUrl().'/comunidad/descargas/'.$oSoftware->getCategoria()->getUrlToken().'/'.$oSoftware->getId()."-".$sTituloUrl);

                    $this->getTemplate()->parse("AplicacionRowBlock", true);
                }
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
}