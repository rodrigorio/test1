<?php

/**
 * @author Matias Velilla
 */
class SoftwareControllerComunidad extends PageControllerAbstract
{    
    private $filtrosFormConfig = array('filtroTitulo' => 'f.titulo', 'filtroCategoria' => 's.categorias_id');

    private $orderByConfig = array('titulo' => array('variableTemplate' => 'orderByTitulo',
                                                     'orderBy' => 'f.titulo',
                                                     'order' => 'desc'),
                                   'fecha' => array('variableTemplate' => 'orderByFecha',
                                                    'orderBy' => 'f.fecha',
                                                    'order' => 'desc'),
                                   'categoria' => array('variableTemplate' => 'orderByCategoria',
                                                   'orderBy' => 's.categorias_id',
                                                   'order' => 'desc'),
                                   'activo' => array('variableTemplate' => 'orderByActivo',
                                                   'orderBy' => 'f.activo',
                                                   'order' => 'desc'));
    
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        return $this;
    }
    
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

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setHeadTagSoftware($oSoftware)
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');

        $tituloVista = $nombreSitio.' | '.$oSoftware->getTitulo();
        $descriptionVista = $oSoftware->getDescripcionBreve();
        $keywordsVista = $oSoftware->getCategoria()->getNombre();

        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");

        $this->getTemplate()->set_var("hrefListadoSoftware", $this->getUrlFromRoute("comunidadSoftwareIndex", true));
        $this->getTemplate()->set_var("hrefMisAplicaciones", $this->getUrlFromRoute("comunidadSoftwareMisAplicaciones", true));
        $this->getTemplate()->set_var("hrefCrearSoftware", $this->getUrlFromRoute("comunidadSoftwareCrearSoftwareForm", true));

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
        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();
       
        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Catálogo descargas comunidad");

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "pageRightInnerMainCont", "ListadoSoftwareBlock");

        $this->getTemplate()->set_var("CategoriaActualBlock", "");
        $this->getTemplate()->set_var("ListadoCategoriasInitCollapsed", "");

        $this->listarCategorias();

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aSoftware = ComunidadController::getInstance()->buscarSoftwareComunidad($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

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

        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Catálogo descargas comunidad");

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "pageRightInnerMainCont", "ListadoSoftwareBlock");        
        $this->getTemplate()->set_var("ListadoCategoriasInitExpanded", "");
        
        $this->getTemplate()->set_var("iCategoriaIdFiltro", $oCategoria->getId());

        //bloque categoria actual
        if(null === $oCategoria->getFoto()){
            $urlFotoCategoria = $this->getUploadHelper()->getDirectorioImagenesSitio().$oCategoria->getNombreAvatar(true);
        }else{
            $this->getUploadHelper()->utilizarDirectorioUploadSitio('comunidad');
            $urlFotoCategoria = $this->getUploadHelper()->getDirectorioUploadFotos().$oCategoria->getNombreAvatar(true);
        }
        $this->getTemplate()->set_var("sDescripcionCategoria", $oCategoria->getDescripcion(true));
        $this->getTemplate()->set_var("sNombreCategoria", $oCategoria->getNombre());
        $this->getTemplate()->set_var("urlFotoCategoria", $urlFotoCategoria);
        $this->getTemplate()->parse("CategoriaActualBlock");

        $this->listarCategorias();

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        //en este metodo alcanza con esto porque en principio no hay filtros
        $aSoftware = ComunidadController::getInstance()->obtenerSoftwareCategoria($oCategoria->getId());
        $iRecordsTotal = count($aSoftware);

        $paramsPaginador[] = "filtroCategoria=".$oCategoria->getId();
        $this->listarFichas($aSoftware, $iItemsForPage, $iPage, $iRecordsTotal, $paramsPaginador);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    /**
     * crea listado de categorias en el top del listado de fichas de software
     */
    private function listarCategorias()
    {
        $iRecordsTotal = 0;
        $aCategorias = ComunidadController::getInstance()->obtenerCategoria($filtro = array(), $iRecordsTotal, null, null, null, null);
        
        if(null !== $aCategorias){

            foreach($aCategorias as $oCategoria){
                $hrefSofwareCategoria = 'comunidad/descargas/'.$oCategoria->getUrlToken();

                if(null === $oCategoria->getFoto()){
                    $urlFotoCategoria = $this->getUploadHelper()->getDirectorioImagenesSitio().$oCategoria->getNombreAvatar(true);
                }else{
                    $this->getUploadHelper()->utilizarDirectorioUploadSitio('comunidad');
                    $urlFotoCategoria = $this->getUploadHelper()->getDirectorioUploadFotos().$oCategoria->getNombreAvatar(true);
                }

                $this->getTemplate()->set_var("sDescripcionCategoria", $oCategoria->getDescripcion());
                $this->getTemplate()->set_var("hrefSoftwareCategoria", $hrefSofwareCategoria);
                $this->getTemplate()->set_var("sNombreCategoria", $oCategoria->getNombre());
                $this->getTemplate()->set_var("urlFotoCategoria", $urlFotoCategoria);

                $this->getTemplate()->parse("ThumbCategoriaBlock", true);
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

                $this->getTemplate()->set_var("iSoftwareId", $oSoftware->getId());
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

                //'comunidad/descargas/nombre-categoria/23-titulo-software'
                $sTituloUrl = $this->getInflectorHelper()->urlize($oSoftware->getTitulo());
                $this->getTemplate()->set_var("hrefAmpliarSoftware", $this->getRequest()->getBaseUrl().'/comunidad/descargas/'.$oCategoria->getUrlToken().'/'.$oSoftware->getId()."-".$sTituloUrl);

                $this->thumbDestacadoFicha($oSoftware);
                $this->comentariosFicha($oSoftware);

                $this->getTemplate()->parse("SoftwareBlock", true);
            }

            $this->getTemplate()->set_var("NoRecordsSoftwareBlock", "");

            $paramsPaginador[] = "masAplicaciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/descargas/procesar", "listadoSoftwareResult", $paramsPaginador);
        }else{
            $this->getTemplate()->set_var("SoftwareBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "noRecords", "NoRecordsSoftwareBlock");
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

    private function comentariosFicha($oSoftware)
    {
        $comentarios = "";
        
        if($oSoftware->isActivoComentarios()){
            $aComentarios = $oSoftware->getComentarios();
            $iCantidad = count($aComentarios);
            if($iCantidad > 0){
                $this->getTemplate()->load_file_section("gui/componentes/comentarios.gui.html", "listaComentarios", "ComentariosBlock");
                $this->getTemplate()->set_var("totalComentarios", $iCantidad);

                //solo muestro los ultimos 3
                if($iCantidad > 3){ $i = $iCantidad - 3; }else{ $i = 0; }
                for($i; $i < $iCantidad; $i++){
                    $oComentario = $aComentarios[$i];
                    $oUsuario = $oComentario->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                    $this->getTemplate()->set_var("dFechaComentario", $oComentario->getFecha());
                    $this->getTemplate()->set_var("sComentario", $oComentario->getDescripcion(true));

                    $valoracion = "";
                    $valoracionBloque = "";
                    if($oComentario->emitioValoracion()){

                        $fValoracion = $oComentario->getValoracion();

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
                    $this->getTemplate()->parse("ComentarioBlock", true);
                    $this->getTemplate()->delete_parsed_blocks($valoracionBloque);                    
                }

                $comentarios = $this->getTemplate()->pparse("listaComentarios");
            }            
        }
        
        $this->getTemplate()->set_var("comentarios", $comentarios);
        
        $this->getTemplate()->delete_parsed_blocks("ComentarioBlock");
        $this->getTemplate()->delete_parsed_blocks("ComentariosBlock");
    }

    public function misAplicaciones()
    {
        try{

            $this->setFrameTemplate()
                 ->setHeadTag()
                 ->setMenuDerecha();

            IndexControllerComunidad::setCabecera($this->getTemplate());
            IndexControllerComunidad::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Mis Aplicaciones");

            $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "pageRightInnerMainCont", "ListadoMisAplicacionesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $aSoftware = ComunidadController::getInstance()->buscarSoftwareUsuario($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            if(count($aSoftware) > 0){

                $this->getTemplate()->set_var("NoRecordsMisAplicacionesBlock", "");

                foreach($aSoftware as $oSoftware){

                    //'comunidad/descargas/nombre-categoria/23-titulo-software'
                    $sTituloUrl = $this->getInflectorHelper()->urlize($oSoftware->getTitulo());
                    $this->getTemplate()->set_var("hrefAmpliarSoftware", $this->getRequest()->getBaseUrl().'/comunidad/descargas/'.$oSoftware->getCategoria()->getUrlToken().'/'.$oSoftware->getId()."-".$sTituloUrl);
                    
                    $hrefEditarFotos = "comunidad/descargas/galeria-fotos";
                    $hrefEditarArchivos = "comunidad/descargas/galeria-archivos";

                    $bPublico = $oSoftware->isPublico();
                    $sPublico = ($bPublico)?"El Mundo":"Solo Comunidad";
                    $sActivoComentarios = ($oSoftware->isActivoComentarios())?"Sí":"No";

                    $this->getTemplate()->set_var("iSoftwareId", $oSoftware->getId());
                    $this->getTemplate()->set_var("hrefGaleriaFotos", $hrefEditarFotos);
                    $this->getTemplate()->set_var("hrefGaleriaArchivos", $hrefEditarArchivos);

                    if($oSoftware->isActivo()){
                        $this->getTemplate()->set_var("sSelectedSoftwareActivo", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedSoftwareDesactivado", "selected='selected'");
                    }

                    $this->getTemplate()->set_var("sTitulo", $oSoftware->getTitulo());
                    $this->getTemplate()->set_var("sCategoria", $oSoftware->getCategoria()->getNombre());
                    $this->getTemplate()->set_var("sFecha", $oSoftware->getFecha(true));
                    $this->getTemplate()->set_var("sPublico", $sPublico);

                    //los mensajes pueden ser por moderacion y por acumulacion de denuncias.
                    //si esta marcada como publica muestro cartel segun moderacion
                    $sMensajesSoftware = "";
                    if($bPublico){
                        if($oSoftware->getModeracion()->isPendiente()){
                            $cartelModeracion = "MsgFichaInfoBlock";
                            $tituloModeracion = "Moderación Pendiente";
                            $mensajeModeracion = "La aplicación esta marcada como visible para visitantes fuera de la comunidad, solo será visible por usuarios del sistema mientras se encuentre pendiente de moderación.";
                        }

                        if($oSoftware->getModeracion()->isRechazado()){
                            $cartelModeracion = "MsgFichaErrorBlock";
                            $tituloModeracion = "Publicación Rechazada";
                            $mensajeModeracion = "Causa: ".$oSoftware->getModeracion()->getMensaje(true);
                        }

                        if($oSoftware->getModeracion()->isAprobado()){
                            $cartelModeracion = "MsgFichaCorrectoBlock";
                            $tituloModeracion = "Aplicación Moderada";
                            $mensajeModeracion = "La aplicación esta marcada como visible para visitantes fuera de la comunidad, su contenido esta aprobado.";
                        }

                        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajeSoftwareModeracion", $cartelModeracion);
                        $this->getTemplate()->set_var("sTituloMsgFicha", $tituloModeracion);
                        $this->getTemplate()->set_var("sMsgFicha", $mensajeModeracion);
                        $this->getTemplate()->set_var("class", "mabo");
                        $sMensajesSoftware = $this->getTemplate()->pparse("sMensajeSoftwareModeracion");
                    }

                    //puede agregarse un mensaje por acumulacion de denuncias.
                    $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
                    $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');
                    if(count($oSoftware->getDenuncias()) >= $iCantMaxDenuncias){
                        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajeSoftwareDenuncias", "MsgFichaErrorBlock");
                        $this->getTemplate()->set_var("sTituloMsgFicha", "Acumulación de denuncias");
                        $this->getTemplate()->set_var("sMsgFicha", "La aplicación se ha quitado de los listados temporalmente por acumulación de denuncias.");
                        $sMensajesSoftware .= $this->getTemplate()->pparse("sMensajeSoftwareDenuncias");
                    }

                    $this->getTemplate()->set_var("sMensajeSoftware", $sMensajesSoftware);


                    $this->getTemplate()->set_var("sActivoComentarios", $sActivoComentarios);

                    //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
                    list($cantFotos, $cantVideos, $cantArchivos) = ComunidadController::getInstance()->obtenerCantidadMultimediaFicha($oSoftware->getId());
                    $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
                    $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

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
                    }else{
                        $ratingActual = "Sin Valoraciones";
                    }
                    $this->getTemplate()->set_var("ratingActual", $ratingActual);
                    $this->getTemplate()->delete_parsed_blocks($ratingBloque);

                    $this->getTemplate()->parse("MiAplicacionBlock", true);

                    $this->getTemplate()->set_var("sSelectedSoftwareActivo","");
                    $this->getTemplate()->set_var("sSelectedSoftwareDesactivado","");
                    $this->getTemplate()->set_var("sMensajeSoftware","");
                }
                
                $params[] = "masMisAplicaciones=1";
                $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/descargas/procesar", "listadoMisAplicacionesResult", $params);
                
            }else{
                $this->getTemplate()->set_var("MiAplicacionBlock", "");
                $this->getTemplate()->set_var("sNoRecords", "Todavía no hay aplicaciones creadas.");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            echo $e->getMessage();
            throw new Exception($e->getMessage());
        }
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

        if($this->getRequest()->has('masMisAplicaciones')){
            $this->masMisAplicaciones();
            return;
        }
        
        if($this->getRequest()->has('cambiarEstado')){
            $this->cambiarEstadoSoftware();
            return;
        }

        if($this->getRequest()->has('borrarSoftware')){
            $this->borrarSoftware();
            return;
        }

        if($this->getRequest()->has('comentar')){
            $this->agregarComentarioSoftware();
            return;
        }
    }

    private function agregarComentarioSoftware()
    {
        $iSoftwareId = $this->getRequest()->getPost('iSoftwareId');
        
        if(empty($iSoftwareId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $oSoftware = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);

            if(!$oSoftware->isActivoComentarios())
            {
                $this->getJsonHelper()->setMessage("Se desactivaron los comentarios para esta aplicacion");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            //no puede valorar 2 veces un mismo software
            if(ComunidadController::getInstance()->usuarioEmitioValoracionSoftware($iSoftwareId))
            {
                $this->getJsonHelper()->setMessage("Puede valorar la aplicación solo una vez.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }
            
            $oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $oComentario = new stdClass();
            $oComentario = Factory::getComentarioInstance($oComentario);

            $oComentario->setUsuario($oUsuario);
            $oComentario->setDescripcion($this->getRequest()->getPost("comentario"));

            $fValoracion = $this->getRequest()->getPost("valoracion");
            if(!empty($fValoracion)){
                $oComentario->setValoracion($fValoracion);
            }

            $oSoftware->addComentario($oComentario);

            ComunidadController::getInstance()->guardarComentariosFicha($oSoftware);
           
            //devuelvo la ficha del nuevo comentario
            $this->restartTemplate();
            $this->getTemplate()->load_file_section("gui/componentes/comentarios.gui.html", "ajaxComentario", "ComentarioBlock");

            $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

            $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();

            $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
            $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
            $this->getTemplate()->set_var("dFechaComentario", $oComentario->getFecha());
            $this->getTemplate()->set_var("sComentario", $oComentario->getDescripcion(true));

            $valoracion = "";
            if($oComentario->emitioValoracion()){

                $fValoracion = $oComentario->getValoracion();

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
                                    
            $this->getJsonHelper()->setMessage("El comentario se agrego satisfactoriamente");
            $this->getJsonHelper()->setValor('html', $this->getTemplate()->pparse('ajaxComentario', false));
            $this->getJsonHelper()->setSuccess(true);
            
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();            
    }
    
    private function borrarSoftware()
    {        
        $iSoftwareId = $this->getRequest()->getPost('iSoftwareId');

        if(empty($iSoftwareId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $oSoftware = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSoftware->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para borrar esta aplicacion", 401);
            }              

            $pathServidorFotos = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $pathServidorArchivos = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

            //polimorfico
            $result = ComunidadController::getInstance()->borrarPublicacion($oSoftware, $pathServidorFotos, $pathServidorArchivos);

            $this->restartTemplate();

            if($result){
                $msg = "La aplicación fue eliminada del sistema";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha eliminado la aplicación del sistema";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la aplicación del sistema";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function masAplicaciones()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "ajaxFichasSoftwareBlock", "FichasSoftwareBlock");

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aSoftware = ComunidadController::getInstance()->buscarSoftwareComunidad($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        $this->listarFichas($aSoftware, $iItemsForPage, $iPage, $iRecordsTotal, $paramsPaginador);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxFichasSoftwareBlock', false));
    }

    private function masMisAplicaciones()
    {        
        try{
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "ajaxGrillaMisAplicacionesBlock", "GrillaMisAplicacionesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $aSoftware = ComunidadController::getInstance()->buscarSoftwareUsuario($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            if(count($aSoftware) > 0){

                $this->getTemplate()->set_var("NoRecordsMisAplicacionesBlock", "");

                foreach($aSoftware as $oSoftware){

                    //'comunidad/descargas/nombre-categoria/23-titulo-software'
                    $sTituloUrl = $this->getInflectorHelper()->urlize($oSoftware->getTitulo());
                    $this->getTemplate()->set_var("hrefAmpliarSoftware", $this->getRequest()->getBaseUrl().'/comunidad/descargas/'.$oSoftware->getCategoria()->getUrlToken().'/'.$oSoftware->getId()."-".$sTituloUrl);

                    $hrefEditarFotos = "comunidad/descargas/galeria-fotos";
                    $hrefEditarArchivos = "comunidad/descargas/galeria-archivos";

                    $bPublico = $oSoftware->isPublico();
                    $sPublico = ($bPublico)?"El Mundo":"Solo Comunidad";
                    $sActivoComentarios = ($oSoftware->isActivoComentarios())?"Sí":"No";

                    $this->getTemplate()->set_var("iSoftwareId", $oSoftware->getId());
                    $this->getTemplate()->set_var("hrefGaleriaFotos", $hrefEditarFotos);
                    $this->getTemplate()->set_var("hrefGaleriaArchivos", $hrefEditarArchivos);

                    if($oSoftware->isActivo()){
                        $this->getTemplate()->set_var("sSelectedSoftwareActivo", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedSoftwareDesactivado", "selected='selected'");
                    }

                    $this->getTemplate()->set_var("sTitulo", $oSoftware->getTitulo());
                    $this->getTemplate()->set_var("sCategoria", $oSoftware->getCategoria()->getNombre());
                    $this->getTemplate()->set_var("sFecha", $oSoftware->getFecha(true));
                    $this->getTemplate()->set_var("sPublico", $sPublico);

                    //si esta marcada como publica muestro cartel segun moderacion
                    if($bPublico){
                        if($oSoftware->getModeracion()->isPendiente()){
                            $cartelModeracion = "MsgFichaInfoBlock";
                            $tituloModeracion = "Moderación Pendiente";
                            $mensajeModeracion = "La aplicación esta marcada como visible para visitantes fuera de la comunidad, solo será visible por usuarios del sistema mientras se encuentre pendiente de moderación.";
                        }

                        if($oSoftware->getModeracion()->isRechazado()){
                            $cartelModeracion = "MsgFichaErrorBlock";
                            $tituloModeracion = "Publicación Rechazada";
                            $mensajeModeracion = "Causa: ".$oSoftware->getModeracion()->getMensaje(true);
                        }

                        if($oSoftware->getModeracion()->isAprobado()){
                            $cartelModeracion = "MsgFichaCorrectoBlock";
                            $tituloModeracion = "Aplicación Moderada";
                            $mensajeModeracion = "La aplicación esta marcada como visible para visitantes fuera de la comunidad, su contenido esta aprobado.";
                        }

                        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajeSoftware", $cartelModeracion);
                        $this->getTemplate()->set_var("sTituloMsgFicha", $tituloModeracion);
                        $this->getTemplate()->set_var("sMsgFicha", $mensajeModeracion);
                        $this->getTemplate()->set_var("class", "mabo");
                        $this->getTemplate()->parse("sMensajeSoftware", false);
                    }

                    $this->getTemplate()->set_var("sActivoComentarios", $sActivoComentarios);

                    //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
                    list($cantFotos, $cantVideos, $cantArchivos) = ComunidadController::getInstance()->obtenerCantidadMultimediaFicha($oSoftware->getId());
                    $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
                    $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

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
                    }else{
                        $ratingActual = "Sin Valoraciones";
                    }
                    $this->getTemplate()->set_var("ratingActual", $ratingActual);
                    $this->getTemplate()->delete_parsed_blocks($ratingBloque);

                    $this->getTemplate()->parse("MiAplicacionBlock", true);

                    $this->getTemplate()->set_var("sSelectedSoftwareActivo","");
                    $this->getTemplate()->set_var("sSelectedSoftwareDesactivado","");
                    $this->getTemplate()->set_var("sMensajeSoftware","");
                }

                $paramsPaginador[] = "masMisAplicaciones=1";
                $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/descargas/procesar", "listadoMisAplicacionesResult", $paramsPaginador);

            }else{
                $this->getTemplate()->set_var("MiAplicacionBlock", "");
                $this->getTemplate()->set_var("sNoRecords", "No se encontraron aplicaciones");
            }

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaMisAplicacionesBlock', false));
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    private function cambiarEstadoSoftware()
    {
        $iSoftwareId = $this->getRequest()->getParam('iSoftwareId');
        $estadoSoftware = $this->getRequest()->getParam('estadoSoftware');

        if(empty($iSoftwareId) || !$this->getRequest()->has('estadoSoftware')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $bActivo = ($estadoSoftware == "1") ? true : false;

        $oSoftware = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSoftware->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para modificar esta aplicacion", 401);
        }

        $oSoftware->isActivo($bActivo);
        ComunidadController::getInstance()->guardarSoftware($oSoftware);
    }

    public function crearSoftwareForm()
    {
        $this->mostrarFormularioSoftwarePopUp();
    }
    
    public function modificarSoftwareForm()
    {
        $this->mostrarFormularioSoftwarePopUp();
    }

    private function mostrarFormularioSoftwarePopUp()
    {               
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "popUpContent", "FormularioSoftwareBlock");

        //AGREGAR SOFTWARE
        if($this->getRequest()->getActionName() == "crearSoftwareForm"){
           
            $this->getTemplate()->unset_blocks("SubmitModificarSoftwareBlock");

            $sTituloForm = "Agregar una nueva aplicación";

            //valores por defecto en el agregar
            $oSoftware = null;
            $iSoftwareIdForm = "";
            $iCategoriaId = "";
            $sTitulo = "";
            $sDescripcionBreve = "";
            $bActivoComentarios = true;
            $bActivo = true;
            $bPublico = false;
            $sDescripcion = "";
            $sEnlaces = "";

        //MODIFICAR SOFTWARE
        }else{
            
            $iSoftwareIdForm = $this->getRequest()->getParam('iSoftwareId');
            if(empty($iSoftwareIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $sTituloForm = "Modificar aplicación";

            $oSoftware = ComunidadController::getInstance()->getSoftwareById($iSoftwareIdForm);
            
            $this->getTemplate()->unset_blocks("SubmitCrearSoftwareBlock");

            $this->getTemplate()->set_var("iSoftwareIdForm", $iSoftwareIdForm);

            $sTitulo = $oSoftware->getTitulo();
            $sDescripcionBreve = $oSoftware->getDescripcionBreve();
            $bActivoComentarios = $oSoftware->isActivoComentarios();
            $bActivo = $oSoftware->isActivo();
            $bPublico = $oSoftware->isPublico();
            $sDescripcion = $oSoftware->getDescripcion();
            $iCategoriaId = $oSoftware->getCategoria()->getId();
            $sEnlaces = $oSoftware->getEnlaces();
        }

        if($bActivo){
            $this->getTemplate()->set_var("sSelectedActivo", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedDesactivado", "selected='selected'");
        }

        if($bPublico){
            $this->getTemplate()->set_var("sSelectedPublico", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedComunidad", "selected='selected'");
        }

        if($bActivoComentarios){
            $this->getTemplate()->set_var("sSelectedActivoComentarios", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedDesactivadoComentarios", "selected='selected'");
        }

        $iRecordsTotal = 0;
        $aCategorias = ComunidadController::getInstance()->obtenerCategoria($filtro = array(), $iRecordsTotal, null, null, null, null);        
        foreach ($aCategorias as $oCategoria){
            $value = $oCategoria->getId();
            $text = $oCategoria->getNombre();
            $this->getTemplate()->set_var("iCategoriaId", $value);
            $this->getTemplate()->set_var("sCategoria", $text);
            if($iCategoriaId == $value){
                $this->getTemplate()->set_var("sSelectedCategoria", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionCategoriaBlock", true);
            $this->getTemplate()->set_var("sSelectedCategoria", "");
        }

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcionBreve", $sDescripcionBreve);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("sEnlaces", $sEnlaces);
                              
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    public function guardarSoftware()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('crearSoftware')){
            $this->crearSoftware();
            return;
        }

        if($this->getRequest()->has('modificarSoftware')){
            $this->modificarSoftware();
            return;
        }        
    }

    private function crearSoftware()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oSoftware = new stdClass();

            $oSoftware->sTitulo = $this->getRequest()->getPost("titulo");
            $oSoftware->sDescripcionBreve = $this->getRequest()->getPost("descripcionBreve");
            $oSoftware->bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $oSoftware->bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $oSoftware->bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;
            $oSoftware->sDescripcion = $this->getRequest()->getPost("descripcion");
            $oSoftware->sEnlaces = $this->getRequest()->getPost("enlaces");
            $oSoftware->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $oSoftware->oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($this->getRequest()->getPost("categoria"));
                    
            $oSoftware = Factory::getSoftwareInstance($oSoftware);

            ComunidadController::getInstance()->guardarSoftware($oSoftware);

            $this->getJsonHelper()->setValor("agregarSoftware", "1");
            $this->getJsonHelper()->setMessage("La aplicación se ha creado con éxito. Puede agregar fotos y los archivos adjuntos del software desde 'Mis Aplicaciones'");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarSoftware()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iSoftwareIdForm = $this->getRequest()->getPost('softwareIdForm');
            $oSoftware = ComunidadController::getInstance()->getSoftwareById($iSoftwareIdForm);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSoftware->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar esta aplicacion", 401);
            }

            $bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;

            $oSoftware->setTitulo($this->getRequest()->getPost("titulo"));
            $oSoftware->setDescripcionBreve($this->getRequest()->getPost("descripcionBreve"));
            $oSoftware->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oSoftware->setEnlaces($this->getRequest()->getPost("enlaces"));
            $oSoftware->isActivo($bActivo);
            $oSoftware->isPublico($bPublico);
            $oSoftware->isActivoComentarios($bActivoComentarios);

            $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($this->getRequest()->getPost("categoria"));
            $oSoftware->setCategoria($oCategoria);

            ComunidadController::getInstance()->guardarSoftware($oSoftware);
            $this->getJsonHelper()->setMessage("La aplicacion se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarSoftware", "1");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
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
     *
     * VALIDACION 3.
     * Si el id existe y cuando se hace el getById la publicacion esta 'desactivada' o suspendida por acumulacion de denuncias
     * se redirecciona al listado de publicaciones con header de redireccion temporal.
     *
     * VALIDACION 4.
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
            if(null === $oSoftware)
            {
                //ojo que si este metodo redireccion404 tiene catch, entonces esta excepcion la tiene que devolver entera.
                //sino el plugin de redireccion 404 no levanta nada 
                throw new Exception("", 404);
            }

            //validacion 3.
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');
            if(!$oSoftware->isActivo() || (count($oSoftware->getDenuncias()) >= $iCantMaxDenuncias)){
                $this->getRedirectorHelper()->setCode(307);
                $url = $this->getUrlFromRoute("comunidadSoftwareIndex");
                $this->getRedirectorHelper()->gotoUrl($url);
            }
            
            //validacion 4.
            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oSoftware->getTitulo());
            $sUrlTokenActual = $oSoftware->getCategoria()->getUrlToken();

            if( ($sTituloUrlized != $sTituloUrlizedActual) || ($sUrlToken != $sUrlTokenActual) ){
                $this->getRedirectorHelper()->setCode(301);
                $url = 'comunidad/descargas/'.$sUrlTokenActual.'/'.$oSoftware->getId()."-".$sTituloUrlizedActual;
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            $this->setFrameTemplate()
                 ->setHeadTagSoftware($oSoftware)
                 ->setMenuDerecha();

            IndexControllerComunidad::setCabecera($this->getTemplate());
            IndexControllerComunidad::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Descargas Comunidad");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/software.gui.html", "pageRightInnerMainCont", "AplicacionAmpliadaBlock");

            $oUsuarioAutor = $oSoftware->getUsuario();
            $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuarioAutor->getNombreAvatar();

            $sNombreAutor = $oUsuarioAutor->getApellido()." ".$oUsuarioAutor->getNombre();

            $this->getTemplate()->set_var("iSoftwareId", $oSoftware->getId());
            $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
            $this->getTemplate()->set_var("sTitulo", $oSoftware->getTitulo());
            $this->getTemplate()->set_var("sCategoria", $oSoftware->getCategoria()->getNombre());
            $this->getTemplate()->set_var("sFecha", $oSoftware->getFecha(true));
            $this->getTemplate()->set_var("sAutor", $sNombreAutor);
            $this->getTemplate()->set_var("sDescripcionBreve", $oSoftware->getDescripcionBreve());
            $this->getTemplate()->set_var("sDescripcion", $oSoftware->getDescripcion(true));

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

            if($oSoftware->isActivoComentarios()){
                $this->listarComentariosFicha($oSoftware);

                $this->getTemplate()->set_var("iItemIdForm", $oSoftware->getId());
                $this->getTemplate()->set_var("sTipoItemForm", get_class($oSoftware));
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

    private function listarComentariosFicha($oFicha)
    {
        try{
            $this->getTemplate()->load_file_section("gui/componentes/comentarios.gui.html", "comentarios", "NuevoComentarioBlock");

            $aComentarios = $oFicha->getComentarios();

            if(count($aComentarios)>0){
                $this->getTemplate()->load_file_section("gui/componentes/comentarios.gui.html", "comentarios", "ComentariosBlock", true);
                $this->getTemplate()->set_var("totalComentarios", count($aComentarios));

                foreach($aComentarios as $oComentario){

                    $oUsuario = $oComentario->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                    $this->getTemplate()->set_var("dFechaComentario", $oComentario->getFecha());
                    $this->getTemplate()->set_var("sComentario", $oComentario->getDescripcion(true));
                 
                    $valoracion = "";
                    $valoracionBloque = "";
                    if($oComentario->emitioValoracion()){

                        $fValoracion = $oComentario->getValoracion();

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
                    $this->getTemplate()->parse("ComentarioBlock", true);                                                           
                    $this->getTemplate()->delete_parsed_blocks($valoracionBloque);
                }
            }
        }catch(Exception $e){
            print($e->getMessage());
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
            $aSoftware = ComunidadController::getInstance()->buscarSoftwareComunidad($filtro = null, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iMinLimit = 1, $iItemsForPage = 10);
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

    public function galeriaFotos()
    {
        $iSoftwareId = $this->getRequest()->getParam('iSoftwareId');

        if(empty($iSoftwareId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();

        $oFicha = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);
       
        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Mis Aplicaciones");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "pageRightInnerMainCont", "GaleriaFotosBlock");

        $this->getTemplate()->set_var("sTipoItemForm", get_class($oFicha));
        $this->getTemplate()->set_var("sTipoItem", get_class($oFicha));
        $this->getTemplate()->set_var("iItemIdForm", $oFicha->getId());
        $this->getTemplate()->set_var("sTituloItem", $oFicha->getTitulo());        
        
        $iRecordsTotal = 0;
        $aFotos = $oFicha->getFotos();
        
        if(count($aFotos) > 0){

            $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

            foreach($aFotos as $oFoto){
                
                $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                $hrefFoto = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
                $this->getTemplate()->set_var("hrefFoto", $hrefFoto);
                $this->getTemplate()->set_var("tituloFoto", $oFoto->getTitulo());
                $this->getTemplate()->set_var("descripcionFoto", $oFoto->getDescripcion(true));
                $this->getTemplate()->set_var("iFotoId", $oFoto->getId());
                
                $this->getTemplate()->parse("ThumbnailFotoEditBlock", true);
            }

            $this->getTemplate()->set_var("NoRecordsFotosBlock", "");
        }else{         
            $this->getTemplate()->set_var("ThumbnailFotoEditBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No hay fotos cargadas para la aplicación");
        }

        //aca despues hay que usar el parametros max fotos publicacion
        if(count($aFotos) >= 12){
            $this->getTemplate()->set_var("FormularioCrearFotoBlock", "");
        }else{
            $this->getTemplate()->set_var("MensajeLimiteFotosBlock", "");
            
            $this->getUploadHelper()->setTiposValidosFotos();
            $this->getTemplate()->set_var("sTiposPermitidosFoto", $this->getUploadHelper()->getStringTiposValidos());
            $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
            $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function fotosProcesar()
    {
        if($this->getRequest()->has('agregarFoto')){
            $this->agregarFotoSoftware();
            return;
        }    
        
        //este if va abajo porque los form de upload de archivo son con iframe oculto no con ajax
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('guardarFoto')){
            $this->guardarFoto();
            return;
        }
        
        if($this->getRequest()->has('eliminarFoto')){
            $this->eliminarFoto();
            return;
        }
    }

    private function agregarFotoSoftware()
    {
        try{
            $iSoftwareId = $this->getRequest()->getParam('iSoftwareId');

            if(empty($iSoftwareId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oFicha = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);
            
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oFicha->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar fotos a esta aplicación", 401);
            }
                        
            $nombreInputFile = 'fotoGaleria';

            $this->getUploadHelper()->setTiposValidosFotos();

            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){

                $idItem = $oFicha->getId();

                //un array con los datos de las fotos
                $aNombreArchivos = $this->getUploadHelper()->generarFotosSistema($idItem, $nombreInputFile);
                $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);

                try{
                    $oFoto = new stdClass();
                    $oFoto->sNombreBigSize = $aNombreArchivos['nombreFotoGrande'];
                    $oFoto->sNombreMediumSize = $aNombreArchivos['nombreFotoMediana'];
                    $oFoto->sNombreSmallSize = $aNombreArchivos['nombreFotoChica'];

                    $oFoto = Factory::getFotoInstance($oFoto);

                    $oFoto->setTitulo('');
                    $oFoto->setDescripcion('');
                    $oFoto->setTipoAdjunto();
                    
                    $oFicha->addFoto($oFoto);
                    
                    ComunidadController::getInstance()->guardarFotoFicha($oFicha, $pathServidor);

                    $this->restartTemplate();

                    //creo el thumbnail para agregar a la galeria
                    $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "ajaxThumbnailFoto", "ThumbnailFotoEditBlock");

                    $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFoto", $pathFotoServidorBigSize);
                    $this->getTemplate()->set_var("tituloFoto", $oFoto->getTitulo());
                    $this->getTemplate()->set_var("descripcionFoto", $oFoto->getDescripcion(true));
                    $this->getTemplate()->set_var("iFotoId", $oFoto->getId());

                    //OJO QUE SI TIENE UN ';' EL HTML QUE DEVUELVO Y HAGO UN SPLIT EN EL JS SE ROMPE TODO !!
                    $respuesta = "1;; ".$this->getTemplate()->pparse('ajaxThumbnailFoto', false);
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                }catch(Exception $e){
                    $respuesta = "0;; Error al guardar en base de datos";
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                    return;
                }
            }
        }catch(Exception $e){
            $respuesta = "0; Error al procesar el archivo";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }
    }

    public function formFoto()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "FormularioFotoBlock");

        $iFotoId = $this->getRequest()->getParam('iFotoId');
        if(empty($iFotoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oFoto = IndexController::getInstance()->getFotoById($iFotoId);

        $this->getTemplate()->set_var("iFotoId", $iFotoId);

        $sTitulo = $oFoto->getTitulo();
        $sDescripcion = $oFoto->getDescripcion();
        $iOrden = $oFoto->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    private function eliminarFoto()
    {
        $iFotoId = $this->getRequest()->getParam('iFotoId');

        if(empty($iFotoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            //devuelve si la foto es de una publicacion creada por el usuario que esta logueado
            $bFotoUsuario = ComunidadController::getInstance()->isFotoPublicacionUsuario($iFotoId);
            if(!$bFotoUsuario){
                throw new Exception("No tiene permiso para borrar esta foto", 401);
            }

            $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);                        
            $oFoto = IndexController::getInstance()->getFotoById($iFotoId);
            
            IndexController::getInstance()->borrarFoto($oFoto, $pathServidor);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function guardarFoto()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iFotoId = $this->getRequest()->getPost('iFotoIdForm');

            if(empty($iFotoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $bFotoUsuario = ComunidadController::getInstance()->isFotoPublicacionUsuario($iFotoId);
            if(!$bFotoUsuario){
                throw new Exception("No tiene permiso para editar esta foto", 401);
            }
            
            $oFoto = IndexController::getInstance()->getFotoById($iFotoId);
            
            $oFoto->setOrden($this->getRequest()->getPost("orden"));
            $oFoto->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oFoto->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarFoto($oFoto);

            $this->getJsonHelper()->setMessage("La foto se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    public function galeriaArchivos()
    {
        $iSoftwareId = $this->getRequest()->getParam('iSoftwareId');

        if(empty($iSoftwareId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();

        $oFicha = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);

        //titulo seccion
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "pageRightInnerMainCont", "GaleriaArchivosBlock");

        $this->getTemplate()->set_var("tituloSeccion", "Mis Aplicaciones");
        $this->getTemplate()->set_var("sTipoItemForm", get_class($oFicha));
        $this->getTemplate()->set_var("sTipoItem", get_class($oFicha));
        $this->getTemplate()->set_var("iItemIdForm", $oFicha->getId());
        $this->getTemplate()->set_var("sTituloItem", $oFicha->getTitulo());

        $iRecordsTotal = 0;
        $aArchivos = $oFicha->getArchivos();

        if(count($aArchivos) > 0){

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
                $this->getTemplate()->set_var("iArchivoId", $oArchivo->getId());

                $this->getTemplate()->parse("RowArchivoEditBlock", true);
            }

            $this->getTemplate()->set_var("NoRecordsArchivosBlock", "");
        }else{
            $this->getTemplate()->set_var("RowArchivoEditBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No hay archivos cargados para la aplicación");
        }

        //aca despues hay que usar el parametros max fotos 
        if(count($aArchivos) >= 12){
            $this->getTemplate()->set_var("FormularioCrearArchivoBlock", "");
        }else{
            $this->getTemplate()->set_var("MensajeLimiteArchivosBlock", "");

            $this->getUploadHelper()->setTiposValidosCompresiones();
            $this->getTemplate()->set_var("sTiposPermitidosArchivo", $this->getUploadHelper()->getStringTiposValidos());
            $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
            $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function archivosProcesar()
    {
        if($this->getRequest()->has('agregarArchivo')){
            $this->agregarArchivoSoftware();
            return;
        }

        //este if va abajo porque los form de upload de archivo son con iframe oculto no con ajax
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('guardarArchivo')){
            $this->guardarArchivo();
            return;
        }

        if($this->getRequest()->has('eliminarArchivo')){
            $this->eliminarArchivo();
            return;
        }
    }

    private function agregarArchivoSoftware()
    {
        try{
            $iSoftwareId = $this->getRequest()->getParam('iSoftwareId');

            if(empty($iSoftwareId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oFicha = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oFicha->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar archivos a esta aplicación", 401);
            }

            $nombreInputFile = 'archivoGaleria';

            $this->getUploadHelper()->setTiposValidosCompresiones();

            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){

                $idItem = $oFicha->getId();

                list($nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo) = $this->getUploadHelper()->generarArchivoSistema($idItem, 'software', $nombreInputFile);
                $pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

                try{
                    $oArchivo = new stdClass();

                    $oArchivo->sNombre = $nombreArchivo;
                    $oArchivo->sNombreServidor = $nombreServidorArchivo;
                    $oArchivo->sTipoMime = $tipoMimeArchivo;
                    $oArchivo->iTamanio = $tamanioArchivo;
                    $oArchivo = Factory::getArchivoInstance($oArchivo);
                    $oArchivo->setTipoAdjunto();
                    $oFicha->addArchivo($oArchivo);

                    ComunidadController::getInstance()->guardarArchivoFicha($oFicha, $pathServidor);

                    $this->restartTemplate();

                    //creo el thumbnail para agregar a la galeria
                    $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "ajaxRowArchivo", "RowArchivoEditBlock");
                    
                    $nombreArchivo = $oArchivo->getTitulo();
                    if(empty($nombreArchivo)){
                        $nombreArchivo = $oArchivo->getNombre();
                    }

                    $hrefDescargar = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();
                    
                    $this->getTemplate()->set_var("sNombreArchivo", $nombreArchivo);
                    $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                    $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                    $this->getTemplate()->set_var("hrefDescargar", $hrefDescargar);
                    $this->getTemplate()->set_var("iArchivoId", $oArchivo->getId());
                    
                    $respuesta = "1;; ".$this->getTemplate()->pparse('ajaxRowArchivo', false);
                    
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                }catch(Exception $e){
                    
                    $respuesta = "0;; Error al guardar en base de datos";
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                    return;
                }
            }
        }catch(Exception $e){
            
            $respuesta = "0;; Error al procesar el archivo, revise que su archivo tenga una extension permitida.";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }
    }

    public function formArchivo()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "FormularioArchivoBlock");

        $iArchivoId = $this->getRequest()->getParam('iArchivoId');
        if(empty($iArchivoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

        $this->getTemplate()->set_var("iArchivoId", $iArchivoId);

        $sTitulo = $oArchivo->getTitulo();
        $sDescripcion = $oArchivo->getDescripcion();
        $iOrden = $oArchivo->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    private function eliminarArchivo()
    {
        $iArchivoId = $this->getRequest()->getParam('iArchivoId');

        if(empty($iArchivoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            //devuelve si el archivo es de una publicacion creada por el usuario que esta logueado
            $bArchivoUsuario = ComunidadController::getInstance()->isArchivoPublicacionUsuario($iArchivoId);
            if(!$bArchivoUsuario){
                throw new Exception("No tiene permiso para borrar esta archivo", 401);
            }

            $pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);
            $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

            IndexController::getInstance()->borrarArchivo($oArchivo, $pathServidor);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function guardarArchivo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iArchivoId = $this->getRequest()->getParam('iArchivoIdForm');

            if(empty($iArchivoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $bArchivoUsuario = ComunidadController::getInstance()->isArchivoPublicacionUsuario($iArchivoId);
            if(!$bArchivoUsuario){
                throw new Exception("No tiene permiso para editar este archivo", 401);
            }

            $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

            $oArchivo->setOrden($this->getRequest()->getPost("orden"));
            $oArchivo->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oArchivo->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarArchivo($oArchivo);

            $this->getJsonHelper()->setMessage("El archivo se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function denunciar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('enviarDenuncia')){
            $this->procesarDenuncia();
            return;
        }

        $iSoftwareId = $this->getRequest()->getParam('iSoftwareId');
        if(empty($iSoftwareId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/formularios.gui.html", "popUpContent", "FormularioDenunciarBlock");

        //select razones denuncias
        $aRazones = ComunidadController::getInstance()->obtenerRazonesDenuncia();
        while($sRazon = current($aRazones)){
            $this->getTemplate()->set_var("sRazonValue", key($aRazones));
            $this->getTemplate()->set_var("sRazon", $sRazon);
            $this->getTemplate()->parse("OptionRazonBlock", true);
            next($aRazones);
        }

        $this->getTemplate()->set_var("iItemId", $iSoftwareId);
        $this->getTemplate()->set_var("sTipoItem", "Software");

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function procesarDenuncia()
    {
        $iSoftwareId = $this->getRequest()->getParam('iItemIdFormDenuncia');
        if(empty($iSoftwareId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            //no se puede denunciar 2 veces la misma institucion
            if(ComunidadController::getInstance()->usuarioEnvioDenunciaFicha($iSoftwareId)){
                $msg = "Su denuncia ya fue enviada. No puede denunciar dos veces la misma aplicación.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
                $this->getTemplate()->set_var("sMensaje", $msg);
                $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $oUsuario = $perfil->getUsuario();

            $oDenuncia = new stdClass();

            $oDenuncia->sMensaje = $this->getRequest()->getPost('mensaje');
            $oDenuncia->sRazon = $this->getRequest()->getPost('razon');
            $oDenuncia->oUsuario = $oUsuario;

            $oDenuncia = Factory::getDenunciaInstance($oDenuncia);

            $oSoftware = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);
            $oSoftware->addDenuncia($oDenuncia);
            $result = ComunidadController::getInstance()->guardarDenuncias($oSoftware);

            $this->restartTemplate();

            if($result){
                $msg = "Su denuncia fue enviada con éxito.";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha podido enviar su denuncia.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha podido enviar su denuncia.";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}