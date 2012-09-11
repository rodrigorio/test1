<?php

/**
 * @author Matias Velilla
 */
class PublicacionesControllerComunidad extends PageControllerAbstract
{
    //el de fecha se hace automatico en la funcion initFiltrosForm
    private $filtrosFormConfig = array('filtroTitulo' => 'f.titulo',
                                       'filtroApellidoAutor' => 'ap.apellido',
                                       'filtroFechaDesde' => 'fechaDesde',
                                       'filtroFechaHasta' => 'fechaHasta');

    private $orderByConfig = array('titulo' => array('variableTemplate' => 'orderByTitulo',
                                                     'orderBy' => 'f.titulo',
                                                     'order' => 'desc'),
                                   'fecha' => array('variableTemplate' => 'orderByFecha',
                                                    'orderBy' => 'f.fecha',
                                                    'order' => 'desc'),
                                   'tipo' => array('variableTemplate' => 'orderByTipo',
                                                   'orderBy' => 'sObjType',
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

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");

        $this->getTemplate()->set_var("hrefListadoPublicaciones", $this->getUrlFromRoute("comunidadPublicacionesIndex", true));
        $this->getTemplate()->set_var("hrefMisPublicaciones", $this->getUrlFromRoute("comunidadPublicacionesMisPublicaciones", true));
        $this->getTemplate()->set_var("hrefCrearPublicacion", $this->getUrlFromRoute("comunidadPublicacionesCrearPublicacionForm", true));
        $this->getTemplate()->set_var("hrefCrearReview", $this->getUrlFromRoute("comunidadPublicacionesCrearReviewForm", true));

        return $this;
    }
    
    public function index(){
        $this->listar();
    }
  
    /**
     * Lista todas las publicaciones de integrantes para la comunidad.
     * Son fichas miniatura una abajo de la otra paginadas y con posibilidad de filtros.
     *
     * @todo
     *      - que si es moderador o administrador aparezca.
     *      - que si ponen el mouse arriba del autor aparezca algunos datos basicos y mail
     *      - la foto destacada o la primer foto de la galeria de fotos en el contenido
     *      - los ultimos 3 comentarios si la publicacion tiene
     *      - la url a la vista completa de la publicacion ampliada.
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
        $this->getTemplate()->set_var("tituloSeccion", "Publicaciones Comunidad");

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "pageRightInnerMainCont", "ListadoPublicacionesBlock");

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aFichas = ComunidadController::getInstance()->buscarPublicacionesComunidad($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aFichas) > 0){

            foreach($aFichas as $oFicha){

                $oUsuario = $oFicha->getUsuario();
                $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();
                $sTipoPublicacion = (get_class($oFicha) == "Publicacion")?"Publicación":"Review";

                $this->getTemplate()->set_var("iPublicacionId", $oFicha->getId());
                $this->getTemplate()->set_var("sTipoPublicacionClass", get_class($oFicha));
                $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());
                $this->getTemplate()->set_var("sTipoPublicacion", $sTipoPublicacion);                
                $this->getTemplate()->set_var("sDescripcionBreve", $oFicha->getDescripcionBreve());

                /*
                 * la url de publicacion ampliada es diferente segun el tipo
                 *
                 * http://domain.com/comunidad/publicaciones/32-Nombre de la publicacion
                 * http://domain.com/comunidad/reviews/32-Nombre del review
                 */
                $sTituloUrl = $this->getInflectorHelper()->urlize($oFicha->getTitulo());
                if(get_class($oFicha) == 'Publicacion'){
                    $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/comunidad/publicaciones/'.$oFicha->getId()."-".$sTituloUrl);
                }else{
                    $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/comunidad/reviews/'.$oFicha->getId()."-".$sTituloUrl);
                }

                $this->thumbDestacadoFicha($oFicha);
                $this->comentariosFicha($oFicha);

                $this->getTemplate()->parse("PublicacionBlock", true);
            }
            
            $this->getTemplate()->set_var("NoRecordsPublicacionesBlock", "");

            $params[] = "masPublicaciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/publicaciones/procesar", "listadoPublicacionesResult", $params);
        }else{
            $this->getTemplate()->set_var("PublicacionBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "noRecords", "NoRecordsPublicacionesBlock");
            $this->getTemplate()->set_var("sNoRecords", "No hay publicaciones cargados en la comunidad");
            $this->getTemplate()->parse("noRecords", false);
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

    private function comentariosFicha($oFicha)
    {
        $comentarios = "";
        
        if($oFicha->isActivoComentarios()){            
            $aComentarios = $oFicha->getComentarios();
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

                    $this->getTemplate()->parse("ComentarioBlock", true);
                }

                $comentarios = $this->getTemplate()->pparse("listaComentarios");
            }            
        }
        
        $this->getTemplate()->set_var("comentarios", $comentarios);
        
        $this->getTemplate()->delete_parsed_blocks("ComentarioBlock");
        $this->getTemplate()->delete_parsed_blocks("ComentariosBlock");
    }

    public function misPublicaciones()
    {
        try{

            $this->setFrameTemplate()
                 ->setHeadTag()
                 ->setMenuDerecha();

            IndexControllerComunidad::setCabecera($this->getTemplate());
            IndexControllerComunidad::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Mis Publicaciones");

            $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "pageRightInnerMainCont", "ListadoMisPublicacionesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $aFichas = ComunidadController::getInstance()->buscarPublicacionesUsuario($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            if(count($aFichas) > 0){

                $this->getTemplate()->set_var("NoRecordsMisPublicacionesBlock", "");

                foreach($aFichas as $oFicha){

                    $sTituloUrl = $this->getInflectorHelper()->urlize($oFicha->getTitulo());
                    if(get_class($oFicha) == 'Publicacion'){
                        $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/comunidad/publicaciones/'.$oFicha->getId()."-".$sTituloUrl);
                    }else{
                        $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/comunidad/reviews/'.$oFicha->getId()."-".$sTituloUrl);
                    }
                    
                    $hrefEditarFotos = "comunidad/publicaciones/galeria-fotos";
                    $hrefEditarVideos = "comunidad/publicaciones/galeria-videos";
                    $hrefEditarArchivos = "comunidad/publicaciones/galeria-archivos";

                    $bPublico = $oFicha->isPublico();
                    $sPublico = ($bPublico)?"El Mundo":"Solo Comunidad";
                    $sTipoPublicacion = (get_class($oFicha) == "Publicacion")?"publicacion":"review";
                    $sActivoComentarios = ($oFicha->isActivoComentarios())?"Sí":"No";

                    $this->getTemplate()->set_var("iPublicacionId", $oFicha->getId());                    
                    $this->getTemplate()->set_var("hrefGaleriaFotos", $hrefEditarFotos);
                    $this->getTemplate()->set_var("hrefGaleriaVideos", $hrefEditarVideos);
                    $this->getTemplate()->set_var("hrefGaleriaArchivos", $hrefEditarArchivos);

                    if($oFicha->isActivo()){
                        $this->getTemplate()->set_var("sSelectedPublicacionActivo", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedPublicacionDesactivado", "selected='selected'");
                    }

                    $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                    $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());
                    $this->getTemplate()->set_var("sTipo", $sTipoPublicacion);
                    $this->getTemplate()->set_var("sPublico", $sPublico);

                    //si esta marcada como publica muestro cartel segun moderacion
                    if($bPublico){
                        if($oFicha->getModeracion()->isPendiente()){
                            $cartelModeracion = "MsgFichaInfoBlock";
                            $tituloModeracion = "Moderación Pendiente";
                            $mensajeModeracion = "La publicación esta marcada como visible para visitantes fuera de la comunidad, solo será visible por usuarios del sistema mientras se encuentre pendiente de moderación.";
                        }

                        if($oFicha->getModeracion()->isRechazado()){
                            $cartelModeracion = "MsgFichaErrorBlock";
                            $tituloModeracion = "Publicación Rechazada";
                            $mensajeModeracion = "Causa: ".$oFicha->getModeracion()->getMensaje(true);
                        }

                        if($oFicha->getModeracion()->isAprobado()){
                            $cartelModeracion = "MsgFichaCorrectoBlock";
                            $tituloModeracion = "Publicación Moderada";
                            $mensajeModeracion = "La publicación esta marcada como visible para visitantes fuera de la comunidad, su contenido esta aprobado.";
                        }

                        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajePublicacion", $cartelModeracion);
                        $this->getTemplate()->set_var("sTituloMsgFicha", $tituloModeracion);
                        $this->getTemplate()->set_var("sMsgFicha", $mensajeModeracion);
                        $this->getTemplate()->parse("sMensajePublicacion", false);
                    }

                    $this->getTemplate()->set_var("sActivoComentarios", $sActivoComentarios);

                    //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
                    list($cantFotos, $cantVideos, $cantArchivos) = ComunidadController::getInstance()->obtenerCantidadMultimediaFicha($oFicha->getId());
                    $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
                    $this->getTemplate()->set_var("iCantidadVideos", $cantVideos);
                    $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

                    $this->getTemplate()->parse("MiPublicacionBlock", true);

                    $this->getTemplate()->set_var("sSelectedPublicacionActivo","");
                    $this->getTemplate()->set_var("sSelectedPublicacionDesactivado","");
                    $this->getTemplate()->set_var("sMensajePublicacion","");
                }
                
                $params[] = "masMisPublicaciones=1";
                $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/publicaciones/procesar", "listadoMisPublicacionesResult", $params);
                
            }else{
                $this->getTemplate()->set_var("MiPublicacionBlock", "");
                $this->getTemplate()->set_var("sNoRecords", "Todavía no hay publicaciones creadas.");
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

        if($this->getRequest()->has('masPublicaciones')){
            $this->masPublicaciones();
            return;
        }

        if($this->getRequest()->has('masMisPublicaciones')){
            $this->masMisPublicaciones();
            return;
        }
        
        if($this->getRequest()->has('cambiarEstado')){
            $this->cambiarEstadoPublicacion();
            return;
        }

        if($this->getRequest()->has('borrarPublicacion')){
            $this->borrarPublicacion();
            return;
        }

        if($this->getRequest()->has('comentar')){
            $this->agregarComentarioPublicacion();
            return;
        }
    }

    private function agregarComentarioPublicacion()
    {
        $iPublicacionId = $this->getRequest()->getPost('iPublicacionId');
        $objType = $this->getRequest()->getPost('objType');

        if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            switch($objType)
            {
                case "Publicacion":
                    $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                    break;
                case "Review":
                    $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                    break;
            }

            if(!$oFicha->isActivoComentarios())
            {
                $this->getJsonHelper()->setMessage("Se desactivaron los comentarios para esta publicacion");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $oComentario = new stdClass();
            $oComentario = Factory::getComentarioInstance($oComentario);

            $oComentario->setDescripcion($this->getRequest()->getPost("comentario"));           
            $oComentario->setUsuario($oUsuario);

            $oFicha->addComentario($oComentario);

            ComunidadController::getInstance()->guardarComentariosFicha($oFicha);
           
            //devuelvo la ficha del nuevo comentario
            $this->restartTemplate();
            $this->getTemplate()->load_file_section("gui/componentes/comentarios.gui.html", "ajaxComentario", "ComentarioBlock");

            $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

            $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();

            $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
            $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
            $this->getTemplate()->set_var("dFechaComentario", $oComentario->getFecha());
            $this->getTemplate()->set_var("sComentario", $oComentario->getDescripcion(true));
                        
            $this->getJsonHelper()->setMessage("El comentario se agrego satisfactoriamente");
            $this->getJsonHelper()->setValor('html', $this->getTemplate()->pparse('ajaxComentario', false));
            $this->getJsonHelper()->setSuccess(true);
            
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();            
    }
    
    private function borrarPublicacion()
    {        
        $iPublicacionId = $this->getRequest()->getPost('iPublicacionId');
        $objType = $this->getRequest()->getPost('objType');
        
        if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            switch($objType)
            {
                case "publicacion":
                    $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                    break;
                case "review":
                    $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                    break;
            }

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oFicha->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para borrar esta publicacion", 401);
            }              

            $pathServidorFotos = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $pathServidorArchivos = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

            //polimorfico
            $result = ComunidadController::getInstance()->borrarPublicacion($oFicha, $pathServidorFotos, $pathServidorArchivos);

            $this->restartTemplate();

            if($result){
                $msg = "La publicación fue eliminada del sistema";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha eliminado la publicación del sistema";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la publicación del sistema";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function masPublicaciones()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "ajaxFichasPublicacionesBlock", "FichasPublicacionesBlock");

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aFichas = ComunidadController::getInstance()->buscarPublicacionesComunidad($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aFichas) > 0){

            $this->getTemplate()->set_var("NoRecordsPublicacionesBlock", "");

            foreach($aFichas as $oFicha){

                $sTituloUrl = $this->getInflectorHelper()->urlize($oFicha->getTitulo());
                if(get_class($oFicha) == 'Publicacion'){
                    $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/comunidad/publicaciones/'.$oFicha->getId()."-".$sTituloUrl);
                }else{
                    $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/comunidad/reviews/'.$oFicha->getId()."-".$sTituloUrl);
                }

                $oUsuario = $oFicha->getUsuario();
                $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();
                $sTipoPublicacion = (get_class($oFicha) == "Publicacion")?"Publicación":"Review";

                $this->getTemplate()->set_var("iPublicacionId", $oFicha->getId());
                $this->getTemplate()->set_var("sTipoPublicacionClass", get_class($oFicha));
                $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());
                $this->getTemplate()->set_var("sTipoPublicacion", $sTipoPublicacion);                
                $this->getTemplate()->set_var("sDescripcionBreve", $oFicha->getDescripcionBreve());

                $this->thumbDestacadoFicha($oFicha);
                $this->comentariosFicha($oFicha);

                $this->getTemplate()->parse("PublicacionBlock", true);
            }

            $paramsPaginador[] = "masPublicaciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/publicaciones/procesar", "listadoPublicacionesResult", $paramsPaginador);
        }else{
            $this->getTemplate()->set_var("PublicacionBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No se encontraron resultados");
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxFichasPublicacionesBlock', false));
    }

    private function masMisPublicaciones()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "ajaxGrillaPublicacionesBlock", "GrillaMisPublicacionesBlock");

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
        $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);
               
        $iRecordsTotal = 0;
        $aFichas = ComunidadController::getInstance()->buscarPublicacionesUsuario($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);
        
        if(count($aFichas) > 0){

            $this->getTemplate()->set_var("NoRecordsMisPublicacionesBlock", "");

            foreach($aFichas as $oFicha){

                $sTituloUrl = $this->getInflectorHelper()->urlize($oFicha->getTitulo());
                if(get_class($oFicha) == 'Publicacion'){
                    $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/comunidad/publicaciones/'.$oFicha->getId()."-".$sTituloUrl);
                }else{
                    $this->getTemplate()->set_var("hrefAmpliarPublicacion", $this->getRequest()->getBaseUrl().'/comunidad/reviews/'.$oFicha->getId()."-".$sTituloUrl);
                }
                
                $hrefEditarFotos = "comunidad/publicaciones/galeria-fotos";
                $hrefEditarVideos = "comunidad/publicaciones/galeria-videos";
                $hrefEditarArchivos = "comunidad/publicaciones/galeria-archivos";

                $bPublico = $oFicha->isPublico();
                $sPublico = ($bPublico)?"El Mundo":"Solo Comunidad";
                $sTipoPublicacion = (get_class($oFicha) == "Publicacion")?"publicacion":"review";
                $sActivoComentarios = ($oFicha->isActivoComentarios())?"Sí":"No";

                $this->getTemplate()->set_var("iPublicacionId", $oFicha->getId());
                $this->getTemplate()->set_var("hrefGaleriaFotos", $hrefEditarFotos);
                $this->getTemplate()->set_var("hrefGaleriaVideos", $hrefEditarVideos);
                $this->getTemplate()->set_var("hrefGaleriaArchivos", $hrefEditarArchivos);

                if($oFicha->isActivo()){
                    $this->getTemplate()->set_var("sSelectedPublicacionActivo", "selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sSelectedPublicacionDesactivado", "selected='selected'");
                }

                $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());
                $this->getTemplate()->set_var("sTipo", $sTipoPublicacion);
                $this->getTemplate()->set_var("sPublico", $sPublico);

                //si esta marcada como publica muestro cartel segun moderacion
                if($bPublico){
                    if($oFicha->getModeracion()->isPendiente()){
                        $cartelModeracion = "MsgFichaInfoBlock";
                        $tituloModeracion = "Moderación Pendiente";
                        $mensajeModeracion = "La publicación esta marcada como visible para visitantes fuera de la comunidad, solo será visible por usuarios del sistema mientras se encuentre pendiente de moderación.";
                    }

                    if($oFicha->getModeracion()->isRechazado()){
                        $cartelModeracion = "MsgFichaErrorBlock";
                        $tituloModeracion = "Publicación Rechazada";
                        $mensajeModeracion = "Causa: ".$oFicha->getModeracion()->getMensaje(true);
                    }

                    if($oFicha->getModeracion()->isAprobado()){
                        $cartelModeracion = "MsgFichaCorrectoBlock";
                        $tituloModeracion = "Publicación Moderada";
                        $mensajeModeracion = "La publicación esta marcada como visible para visitantes fuera de la comunidad, su contenido esta aprobado.";
                    }

                    $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajePublicacion", $cartelModeracion);
                    $this->getTemplate()->set_var("sTituloMsgFicha", $tituloModeracion);
                    $this->getTemplate()->set_var("sMsgFicha", $mensajeModeracion);
                    $this->getTemplate()->parse("sMensajePublicacion", false);
                }

                $this->getTemplate()->set_var("sActivoComentarios", $sActivoComentarios);

                //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
                list($cantFotos, $cantVideos, $cantArchivos) = ComunidadController::getInstance()->obtenerCantidadMultimediaFicha($oFicha->getId());
                $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
                $this->getTemplate()->set_var("iCantidadVideos", $cantVideos);
                $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

                $this->getTemplate()->parse("MiPublicacionBlock", true);

                //limpio para la publicacion que sigue
                $this->getTemplate()->set_var("sSelectedPublicacionActivo","");
                $this->getTemplate()->set_var("sSelectedPublicacionDesactivado","");
                $this->getTemplate()->set_var("sMensajePublicacion","");
            }

            $paramsPaginador[] = "masMisPublicaciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/publicaciones/procesar", "listadoMisPublicacionesResult", $paramsPaginador);
        }else{
            $this->getTemplate()->set_var("MiPublicacionBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "Todavía no hay publicaciones creadas.");
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaPublicacionesBlock', false));
    }

    private function cambiarEstadoPublicacion()
    {
        $iPublicacionId = $this->getRequest()->getParam('iPublicacionId');
        $estadoPublicacion = $this->getRequest()->getParam('estadoPublicacion');
        $objType = $this->getRequest()->getParam('objType');

        if(empty($iPublicacionId) || !$this->getRequest()->has('estadoPublicacion') ||
            !$this->getRequest()->has('objType')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $bActivo = ($estadoPublicacion == "1") ? true : false;
        switch($objType)
        {
            case "publicacion":
                $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);

                $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
                $iUsuarioId = $perfil->getUsuario()->getId();
                if($oFicha->getUsuarioId() != $iUsuarioId){
                    throw new Exception("No tiene permiso para modificar esta publicacion", 401);
                }

                $oFicha->isActivo($bActivo);
                ComunidadController::getInstance()->guardarPublicacion($oFicha);
                break;
            case "review":
                $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);

                $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
                $iUsuarioId = $perfil->getUsuario()->getId();
                if($oFicha->getUsuarioId() != $iUsuarioId){
                    throw new Exception("No tiene permiso para modificar esta publicacion", 401);
                }

                $oFicha->isActivo($bActivo);
                ComunidadController::getInstance()->guardarReview($oFicha);
                break;
        }       
    }

    public function crearPublicacionForm()
    {
        $this->mostrarFormularioPublicacionPopUp();
    }
    
    public function modificarPublicacionForm()
    {
        $this->mostrarFormularioPublicacionPopUp();               
    }

    private function mostrarFormularioPublicacionPopUp()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "popUpContent", "FormularioPublicacionBlock");

        //AGREGAR PUBLICACION
        if($this->getRequest()->getActionName() == "crearPublicacionForm"){
                        
            $this->getTemplate()->unset_blocks("SubmitModificarPublicacionBlock");

            $sTituloForm = "Agregar una nueva publicación";

            //valores por defecto en el agregar
            $oPublicacion = null;
            $iPublicacionIdForm = "";
            $sTitulo = "";
            $sDescripcionBreve = "";
            $bActivoComentarios = true;
            $bActivo = true;
            $bPublico = false;
            $sDescripcion = "";
            $sKeywords = "";

        //MODIFICAR PUBLICACION
        }else{            
            $iPublicacionIdForm = $this->getRequest()->getParam('publicacionId');
            if(empty($iPublicacionIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $sTituloForm = "Modificar publicación";

            $oPublicacion = ComunidadController::getInstance()->getPublicacionById($iPublicacionIdForm);

            $this->getTemplate()->unset_blocks("SubmitCrearPublicacionBlock");

            $this->getTemplate()->set_var("iPublicacionIdForm", $iPublicacionIdForm);

            $sTitulo = $oPublicacion->getTitulo();
            $sDescripcionBreve = $oPublicacion->getDescripcionBreve();
            $bActivoComentarios = $oPublicacion->isActivoComentarios();
            $bActivo = $oPublicacion->isActivo();
            $bPublico = $oPublicacion->isPublico();
            $sDescripcion = $oPublicacion->getDescripcion();
            $sKeywords = $oPublicacion->getKeywords();
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

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcionBreve", $sDescripcionBreve);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("sKeywords", $sKeywords);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function guardarPublicacion()
    {        
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }
        
        if($this->getRequest()->has('crearPublicacion')){
            $this->crearPublicacion();
            return;
        }

        if($this->getRequest()->has('modificarPublicacion')){
            $this->modificarPublicacion();
            return;
        }
    }

    private function crearPublicacion()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oPublicacion = new stdClass();

            $oPublicacion->sTitulo = $this->getRequest()->getPost("titulo");
            $oPublicacion->sDescripcionBreve = $this->getRequest()->getPost("descripcionBreve");
            $oPublicacion->bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $oPublicacion->bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $oPublicacion->bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;
            $oPublicacion->sDescripcion = $this->getRequest()->getPost("descripcion");
            $oPublicacion->sKeywords = $this->getRequest()->getPost("keywords");            
            $oPublicacion->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();

            $oPublicacion = Factory::getPublicacionInstance($oPublicacion);

            ComunidadController::getInstance()->guardarPublicacion($oPublicacion);

            $this->getJsonHelper()->setValor("agregarPublicacion", "1");
            $this->getJsonHelper()->setMessage("La publicación se ha creado con éxito. Puede agregar fotos, archivos y videos desde 'Mis Publicaciones'");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarPublicacion()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iPublicacionIdForm = $this->getRequest()->getPost('publicacionIdForm');
            $oPublicacion = ComunidadController::getInstance()->getPublicacionById($iPublicacionIdForm);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oPublicacion->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar esta publicacion", 401);
            }

            $bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;

            $oPublicacion->setTitulo($this->getRequest()->getPost("titulo"));
            $oPublicacion->setDescripcionBreve($this->getRequest()->getPost("descripcionBreve"));
            $oPublicacion->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oPublicacion->setKeywords($this->getRequest()->getPost("keywords"));
            $oPublicacion->isActivo($bActivo);
            $oPublicacion->isPublico($bPublico);
            $oPublicacion->isActivoComentarios($bActivoComentarios);
            
            ComunidadController::getInstance()->guardarPublicacion($oPublicacion);
            $this->getJsonHelper()->setMessage("La publicación se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarPersona", "1");
            $this->getJsonHelper()->setSuccess(true);
            
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function crearReviewForm()
    {
        $this->mostrarFormularioReviewPopUp();
    }

    public function modificarReviewForm()
    {
        $this->mostrarFormularioReviewPopUp();
    }

    private function mostrarFormularioReviewPopUp()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "popUpContent", "FormularioReviewBlock");

        //AGREGAR REVIEW
        if($this->getRequest()->getActionName() == "crearReviewForm"){
           
            $this->getTemplate()->unset_blocks("SubmitModificarReviewBlock");

            $sTituloForm = "Agregar un nuevo Review";

            //valores por defecto en el agregar
            $oReview = null;
            $iReviewIdForm = "";
            $sTitulo = "";
            $sDescripcionBreve = "";
            $bActivoComentarios = true;
            $bActivo = true;
            $bPublico = false;
            $sDescripcion = "";
            $sKeywords = "";
            $sItemType = "";
            $sItemName = "";
            $sItemEventSummary = "";
            $sItemUrl = "";
            $fRating = "";
            $sFuenteOriginal = "";

        //MODIFICAR REVIEW
        }else{
            $iReviewIdForm = $this->getRequest()->getParam('publicacionId');
            if(empty($iReviewIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $sTituloForm = "Modificar Review";

            $oReview = ComunidadController::getInstance()->getReviewById($iReviewIdForm);
            
            $this->getTemplate()->unset_blocks("SubmitCrearReviewBlock");

            $this->getTemplate()->set_var("iReviewIdForm", $iReviewIdForm);

            $sTitulo = $oReview->getTitulo();
            $sDescripcionBreve = $oReview->getDescripcionBreve();
            $bActivoComentarios = $oReview->isActivoComentarios();
            $bActivo = $oReview->isActivo();
            $bPublico = $oReview->isPublico();
            $sDescripcion = $oReview->getDescripcion();
            $sKeywords = $oReview->getKeywords();

            $sItemType = $oReview->getItemType();
            $sItemName = $oReview->getItemName();
            $sItemEventSummary = $oReview->getItemEventSummary();
            $sItemUrl = $oReview->getItemUrl();
            $fRating = $oReview->getRating();
            $sFuenteOriginal = $oReview->getFuenteOriginal();
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

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcionBreve", $sDescripcionBreve);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("sKeywords", $sKeywords);
        
        switch($sItemType){
            case "product": 
                $this->getTemplate()->set_var("sSelectedProduct", "selected='selected'");
                break;
            case "business":
                $this->getTemplate()->set_var("sSelectedBusiness", "selected='selected'");
                break;
            case "event":
                $this->getTemplate()->set_var("sSelectedEvent", "selected='selected'");
                break;
            case "person":
                $this->getTemplate()->set_var("sSelectedPerson", "selected='selected'");
                break;
            case "place":
                $this->getTemplate()->set_var("sSelectedPlace", "selected='selected'");
                break;
            case "website":
                $this->getTemplate()->set_var("sSelectedWebsite", "selected='selected'");
                break;
            case "url":
                $this->getTemplate()->set_var("sSelectedUrl", "selected='selected'");
                break;            
        }
        
        switch($fRating){
            case ($fRating >= 0 && $fRating < 0.5): 
                $this->getTemplate()->set_var("sSelected_0", "selected='selected'");
                break;
            case ($fRating >= 0.5 && $fRating < 1):
                $this->getTemplate()->set_var("sSelected_05", "selected='selected'");
                break;
            case ($fRating >= 1 && $fRating < 1.5):
                $this->getTemplate()->set_var("sSelected_1", "selected='selected'");
                break;
            case ($fRating >= 1.5 && $fRating < 2):
                $this->getTemplate()->set_var("sSelected_15", "selected='selected'");
                break;
            case ($fRating >= 2 && $fRating < 2.5):
                $this->getTemplate()->set_var("sSelected_2", "selected='selected'");
                break;
            case ($fRating >= 2.5 && $fRating < 3):
                $this->getTemplate()->set_var("sSelected_25", "selected='selected'");
                break;
            case ($fRating >= 3 && $fRating < 3.5):
                $this->getTemplate()->set_var("sSelected_3", "selected='selected'");
                break;
            case ($fRating >= 3.5 && $fRating < 4):
                $this->getTemplate()->set_var("sSelected_35", "selected='selected'");
                break;
            case ($fRating >= 4 && $fRating < 4.5):
                $this->getTemplate()->set_var("sSelected_4", "selected='selected'");
                break;
            case ($fRating >= 4.5 && $fRating < 5):
                $this->getTemplate()->set_var("sSelected_45", "selected='selected'");
                break;
            case ($fRating >= 5):
                $this->getTemplate()->set_var("sSelected_5", "selected='selected'");
                break;
        }

        $this->getTemplate()->set_var("sItemEventSummary", $sItemEventSummary);
        $this->getTemplate()->set_var("sItemName", $sItemName);
        $this->getTemplate()->set_var("sItemUrl", $sItemUrl);
        $this->getTemplate()->set_var("sFuenteOriginal", $sFuenteOriginal);
              
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    public function guardarReview()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('crearReview')){
            $this->crearReview();
            return;
        }

        if($this->getRequest()->has('modificarReview')){
            $this->modificarReview();
            return;
        }        
    }

    private function crearReview()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oReview = new stdClass();

            $oReview->sTitulo = $this->getRequest()->getPost("titulo");
            $oReview->sDescripcionBreve = $this->getRequest()->getPost("descripcionBreve");
            $oReview->bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $oReview->bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $oReview->bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;
            $oReview->sDescripcion = $this->getRequest()->getPost("descripcion");
            $oReview->sKeywords = $this->getRequest()->getPost("keywords");
            $oReview->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();

            //porque 0 en realidad es un valor real (valoracion 0 para el review)
            //por eso hay que tener cuidado de que si no se utilizo el campo en el form asignarle null al atributo de la clase.
            $fRating = $this->getRequest()->getPost("rating");
            if(empty($fRating)){ $fRating = null; }

            $oReview->sItemType = $this->getRequest()->getPost("itemType");
            $oReview->sItemName = $this->getRequest()->getPost("item");
            $oReview->sItemEventSummary = $this->getRequest()->getPost("itemEventSummary");
            $oReview->sItemUrl = $this->getRequest()->getPost("itemUrl");
            $oReview->fRating = $fRating;
            $oReview->sFuenteOriginal = $this->getRequest()->getPost("fuenteOriginal");
                    
            $oReview = Factory::getReviewInstance($oReview);

            ComunidadController::getInstance()->guardarReview($oReview);

            $this->getJsonHelper()->setValor("agregarReview", "1");
            $this->getJsonHelper()->setMessage("El Review se ha creado con éxito. Puede agregar fotos, archivos y videos desde 'Mis Publicaciones'");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarReview()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iReviewIdForm = $this->getRequest()->getPost('reviewIdForm');
            $oReview = ComunidadController::getInstance()->getReviewById($iReviewIdForm);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oReview->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar esta publicacion", 401);
            }

            $bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;

            $oReview->setTitulo($this->getRequest()->getPost("titulo"));
            $oReview->setDescripcionBreve($this->getRequest()->getPost("descripcionBreve"));
            $oReview->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oReview->setKeywords($this->getRequest()->getPost("keywords"));
            $oReview->isActivo($bActivo);
            $oReview->isPublico($bPublico);
            $oReview->isActivoComentarios($bActivoComentarios);

            $fRating = $this->getRequest()->getPost("rating");
            if(empty($fRating)){ $fRating = null; }

            $oReview->setItemType($this->getRequest()->getPost("itemType"));
            $oReview->setItemName($this->getRequest()->getPost("item"));
            $oReview->setItemEventSummary($this->getRequest()->getPost("itemEventSummary"));
            $oReview->setItemUrl($this->getRequest()->getPost("itemUrl"));
            $oReview->setRating($fRating);
            $oReview->setFuenteOriginal($this->getRequest()->getPost("fuenteOriginal"));

            ComunidadController::getInstance()->guardarReview($oReview);
            $this->getJsonHelper()->setMessage("El review se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarReview", "1");
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
     * por ejemplo si el titulo de una publicacion era "novedades de enero" y el ID era 20
     * el link es www.dominio.com/comunidad/publicaciones/20-novedades-de-enero
     * si luego el titulo cambia a "novedades para este verano" el link que empieza a circular es
     * www.dominio.com/comunidad/publicaciones/20-novedades-para-este-verano.
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
            if(null === $oPublicacion)
            {
                throw new Exception("", 404);
            }

            //validacion 3.
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');            
            if(!$oPublicacion->isActivo() || (count($oPublicacion->getDenuncias()) >= $iCantMaxDenuncias)){
                $this->getRedirectorHelper()->setCode(307);
                $url = $this->getUrlFromRoute("comunidadPublicacionesIndex");
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            //validacion 4.
            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oPublicacion->getTitulo());

            if($sTituloUrlized != $sTituloUrlizedActual){
                $this->getRedirectorHelper()->setCode(301);
                $url = 'comunidad/publicaciones/'.$oPublicacion->getId()."-".$sTituloUrlizedActual;
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
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "pageRightInnerMainCont", "PublicacionAmpliadaBlock");

            $sTipoPublicacion = "Publicacion";
            $this->getTemplate()->set_var("iPublicacionId", $oPublicacion->getId());
            $this->getTemplate()->set_var("sTipoPublicacionClass", get_class($oPublicacion));
            $this->getTemplate()->set_var("sTitulo", $oPublicacion->getTitulo());
            $this->getTemplate()->set_var("sFecha", $oPublicacion->getFecha());
            $this->getTemplate()->set_var("sTipoPublicacion", $sTipoPublicacion);
            $this->getTemplate()->set_var("sDescripcionBreve", $oPublicacion->getDescripcionBreve());
            $this->getTemplate()->set_var("sDescripcion", $oPublicacion->getDescripcion(true));

            $this->agregarGaleriaAdjuntosFicha($oPublicacion);

            $this->setAutorFichaAmpliada($oPublicacion);
                        
            if($oPublicacion->isActivoComentarios()){
                $this->listarComentariosFicha($oPublicacion);

                $this->getTemplate()->set_var("iItemIdForm", $oPublicacion->getId());
                $this->getTemplate()->set_var("sTipoItemForm", get_class($oPublicacion));
            }
            
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
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');
            if(!$oReview->isActivo() || (count($oReview->getDenuncias()) >= $iCantMaxDenuncias)){
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

            $sTipoPublicacion = "Review";

            $this->getTemplate()->set_var("iPublicacionId", $oReview->getId());
            $this->getTemplate()->set_var("sTipoPublicacionClass", get_class($oReview));
            $this->getTemplate()->set_var("sTitulo", $oReview->getTitulo());
            $this->getTemplate()->set_var("sFecha", $oReview->getFecha());
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

            $this->setAutorFichaAmpliada($oReview);

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

    private function setAutorFichaAmpliada($oFicha)
    {
        $oUsuario = $oFicha->getUsuario();

        $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
        $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();
        if(null != $oUsuario->getFotoPerfil()){
            $oFoto = $oUsuario->getFotoPerfil();
            $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
            $this->getTemplate()->set_var("hrefFotoPerfil", $pathFotoServidorBigSize);
        }else{
            $this->getTemplate()->set_var("hrefFotoPerfil", $scrAvatarAutor);
        }
        $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);

        $sNombreUsuario = $oUsuario->getNombre()." ".$oUsuario->getApellido();
        $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
        $this->getTemplate()->set_var("sEmail", $oUsuario->getEmail());

        $aPrivacidad = $oUsuario->obtenerPrivacidad();

        if($aPrivacidad['telefono'] == 'comunidad' && null !== $oUsuario->getTelefono()){
            $this->getTemplate()->set_var("sTelefono", $oUsuario->getTelefono());
            $this->getTemplate()->parse("TelefonoBlock");
        }else{
            $this->getTemplate()->set_var("TelefonoBlock", "");
        }

        if($aPrivacidad['celular'] == 'comunidad' && null !== $oUsuario->getCelular()){
            $this->getTemplate()->set_var("sCelular", $oUsuario->getCelular());
            $this->getTemplate()->parse("CelularBlock");
        }else{
            $this->getTemplate()->set_var("CelularBlock", "");
        }

        if($aPrivacidad['fax'] == 'comunidad' && null !== $oUsuario->getFax()){
            $this->getTemplate()->set_var("sFax", $oUsuario->getFax());
            $this->getTemplate()->parse("FaxBlock");
        }else{
            $this->getTemplate()->set_var("FaxBlock", "");
        }

        if($aPrivacidad['curriculum'] == 'comunidad' && null !== $oUsuario->getCurriculumVitae()){
            $hrefDescargarCv = "";
            $oArchivo = $oUsuario->getCurriculumVitae();
            $hrefDescargarCv = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();
            $this->getTemplate()->set_var("hrefDescargarCv", $hrefDescargarCv);
            $this->getTemplate()->parse("CvBlock");
        }else{
            $this->getTemplate()->set_var("CvBlock", "");
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

    private function listarComentariosFicha($oFicha)
    {
        try{
            $this->getTemplate()->load_file_section("gui/componentes/comentarios.gui.html", "comentarios", "NuevoComentarioBlock");
            $this->getTemplate()->set_var("PuntuarBlock", "");

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

                    $this->getTemplate()->parse("ComentarioBlock", true);
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

    public function galeriaFotos()
    {
        $iPublicacionId = $this->getRequest()->getParam('iPublicacionId');
        $objType = $this->getRequest()->getParam('objType');

        if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();

        switch($objType)
        {
            case "publicacion":
                $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                break;
            case "review":
                $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                break;
        }
       
        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Mis Publicaciones");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "pageRightInnerMainCont", "GaleriaFotosBlock");

        $this->getTemplate()->set_var("tituloSeccion", "Mis Publicaciones");
        $this->getTemplate()->set_var("sTipoItem", $objType);
        $this->getTemplate()->set_var("sTituloItem", $oFicha->getTitulo());
        
        $this->getTemplate()->set_var("iItemIdForm", $oFicha->getId());
        $this->getTemplate()->set_var("sTipoItemForm", $objType);
        
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
            $this->getTemplate()->set_var("sNoRecords", "No hay fotos cargadas para la publicación");
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

    /**
     * Tiene que validar que la publicacion sea efectivamente una que haya creado el usuario que esta en sesion.
     */
    public function fotosProcesar()
    {
        if($this->getRequest()->has('agregarFoto')){
            $this->agregarFotoPublicacion();
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

    private function agregarFotoPublicacion()
    {
        try{
            $iPublicacionId = $this->getRequest()->getParam('iPublicacionId');
            $objType = $this->getRequest()->getParam('objType');

            if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            switch($objType)
            {
                case "publicacion":
                    $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                    break;
                case "review":
                    $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                    break;
            }
            
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oFicha->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar fotos a esta publicación", 401);
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

                    //OJO QUE SI TIENE UN ';' EL HTML Y HAGO UN SPLIT EN EL JS SE ROMPE TODO !!
                    $respuesta = "1; ".$this->getTemplate()->pparse('ajaxThumbnailFoto', false);
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                }catch(Exception $e){
                    $respuesta = "0; Error al guardar en base de datos";
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

    public function galeriaVideos()
    {
        $iPublicacionId = $this->getRequest()->getParam('iPublicacionId');
        $objType = $this->getRequest()->getParam('objType');

        if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();

        switch($objType)
        {
            case "publicacion":
                $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                break;
            case "review":
                $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                break;
        }

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Mis Publicaciones");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "pageRightInnerMainCont", "GaleriaVideosBlock");

        $this->getTemplate()->set_var("tituloSeccion", "Mis Publicaciones");
        $this->getTemplate()->set_var("sTipoItem", $objType);
        $this->getTemplate()->set_var("sTituloItem", $oFicha->getTitulo());

        $this->getTemplate()->set_var("iItemIdForm", $oFicha->getId());
        $this->getTemplate()->set_var("sTipoItemForm", $objType);

        $iRecordsTotal = 0;
        $aEmbedVideos = $oFicha->getEmbedVideos();

        if(count($aEmbedVideos) > 0){

            foreach($aEmbedVideos as $oEmbedVideo){

                $urlFotoThumbnail = $this->getEmbedVideoHelper()->getEmbedVideoThumbnail($oEmbedVideo);
                $hrefAmpliarVideo = $this->getUrlFromRoute("indexIndexVideoAmpliar", true)."?id=".$oEmbedVideo->getId()."&v=".$oEmbedVideo->getUrlKey();

                $this->getTemplate()->set_var("hrefAmpliarVideo", $hrefAmpliarVideo);
                $this->getTemplate()->set_var("urlFoto", $urlFotoThumbnail);
                $this->getTemplate()->set_var("tituloVideo", $oEmbedVideo->getTitulo());
                $this->getTemplate()->set_var("descripcionVideo", $oEmbedVideo->getDescripcion());
                $this->getTemplate()->set_var("iEmbedVideoId", $oEmbedVideo->getId());

                $this->getTemplate()->parse("ThumbnailVideoEditBlock", true);
            }

            $this->getTemplate()->set_var("NoRecordsVideosBlock", "");
        }else{
            $this->getTemplate()->set_var("ThumbnailVideoEditBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No hay videos cargados para la publicación");
        }

        //aca despues hay que usar el parametros max videos publicacion
        if(count($aEmbedVideos) >= 12){
            $this->getTemplate()->set_var("FormularioCrearEmbedVideoBlock", "");
        }else{
            $this->getTemplate()->set_var("MensajeLimiteEmbedVideosBlock", "");
            $this->getTemplate()->set_var("sServidoresPermitidos", $this->getEmbedVideoHelper()->getStringServidoresValidos());
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    public function videosProcesar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }
        
        if($this->getRequest()->has('agregarVideo')){
            $this->agregarVideoPublicacion();
            return;
        }

        if($this->getRequest()->has('guardarVideo')){
            $this->guardarVideo();
            return;
        }

        if($this->getRequest()->has('eliminarVideo')){
            $this->eliminarVideo();
            return;
        }
    }

    private function agregarVideoPublicacion()
    {
        try{
            $iPublicacionId = $this->getRequest()->getParam('iPublicacionId');
            $objType = $this->getRequest()->getParam('objType');

            if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }
            
            switch($objType)
            {
                case "publicacion":
                    $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                    break;
                case "review":
                    $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                    break;
            }

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oFicha->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar videos a esta publicación", 401);
            }

            $this->getJsonHelper()->initJsonAjaxResponse();

            if(!$this->getEmbedVideoHelper()->canBeParsed($this->getRequest()->getPost('codigo'))){
                $this->getJsonHelper()->setMessage("No se encontro un video para insertar desde la url ingresada. (o el servidor no es soportado)");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;                
            }
            
            try{
                $oEmbedVideo = new stdClass();               
                $oEmbedVideo = Factory::getEmbedVideoInstance($oEmbedVideo);
                $oEmbedVideo->setCodigo($this->getRequest()->getPost('codigo'));

                $servidorOrigen = $this->getEmbedVideoHelper()->getServidor($oEmbedVideo);
                $oEmbedVideo->setOrigen($servidorOrigen);

                $oFicha->addEmbedVideo($oEmbedVideo);

                ComunidadController::getInstance()->guardarEmbedVideosFicha($oFicha);

                $this->restartTemplate();

                //creo el thumbnail para agregar a la galeria
                $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "ajaxThumbnailVideo", "ThumbnailVideoEditBlock");

                $urlFotoThumbnail = $this->getEmbedVideoHelper()->getEmbedVideoThumbnail($oEmbedVideo);
                $hrefAmpliarVideo = $this->getUrlFromRoute("indexIndexVideoAmpliar", true)."?id=".$oEmbedVideo->getId()."&v=".$oEmbedVideo->getUrlKey();

                $this->getTemplate()->set_var("hrefAmpliarVideo", $hrefAmpliarVideo);
                $this->getTemplate()->set_var("urlFoto", $urlFotoThumbnail);
                $this->getTemplate()->set_var("tituloVideo", $oEmbedVideo->getTitulo());
                $this->getTemplate()->set_var("descripcionVideo", $oEmbedVideo->getDescripcion());
                $this->getTemplate()->set_var("iEmbedVideoId", $oEmbedVideo->getId());

                $this->getJsonHelper()->setMessage("El video fue agregado con éxito en la publicación");
                $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('ajaxThumbnailVideo', false));
                $this->getJsonHelper()->setSuccess(true);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                                                
            }catch(Exception $e){
                $this->getJsonHelper()->setMessage("Error al guardar en base de datos.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

        }catch(Exception $e){
            $this->getJsonHelper()->setMessage("Error al procesar el video");
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }
    }

    public function formVideo()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "FormularioVideoBlock");

        $iEmbedVideoId = $this->getRequest()->getParam('iEmbedVideoId');
        if(empty($iEmbedVideoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

        $this->getTemplate()->set_var("iEmbedVideoId", $iEmbedVideoId);

        $sTitulo = $oEmbedVideo->getTitulo();
        $sDescripcion = $oEmbedVideo->getDescripcion();
        $iOrden = $oEmbedVideo->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    private function guardarVideo()
    {        
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iEmbedVideoId = $this->getRequest()->getPost('iEmbedVideoId');

            if(empty($iEmbedVideoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $bVideoUsuario = ComunidadController::getInstance()->isEmbedVideoPublicacionUsuario($iEmbedVideoId);
            if(!$bVideoUsuario){
                throw new Exception("No tiene permiso para editar este video", 401);
            }

            $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

            $oEmbedVideo->setOrden($this->getRequest()->getPost("orden"));
            $oEmbedVideo->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oEmbedVideo->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarEmbedVideo($oEmbedVideo);

            $this->getJsonHelper()->setMessage("El video se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();                
    }

    private function eliminarVideo()
    {        
        $iEmbedVideoId = $this->getRequest()->getParam('iEmbedVideoId');

        if(empty($iEmbedVideoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $bVideoUsuario = ComunidadController::getInstance()->isEmbedVideoPublicacionUsuario($iEmbedVideoId);
            if(!$bVideoUsuario){
                throw new Exception("No tiene permiso para editar este video", 401);
            }

            $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

            IndexController::getInstance()->borrarEmbedVideo($oEmbedVideo);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();                
    }

    public function galeriaArchivos()
    {
        $iPublicacionId = $this->getRequest()->getParam('iPublicacionId');
        $objType = $this->getRequest()->getParam('objType');

        if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();

        switch($objType)
        {
            case "publicacion":
                $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                break;
            case "review":
                $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                break;
        }

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Mis Publicaciones");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "pageRightInnerMainCont", "GaleriaArchivosBlock");

        $this->getTemplate()->set_var("tituloSeccion", "Mis Publicaciones");
        $this->getTemplate()->set_var("sTipoItem", $objType);
        $this->getTemplate()->set_var("sTituloItem", $oFicha->getTitulo());

        $this->getTemplate()->set_var("iItemIdForm", $oFicha->getId());
        $this->getTemplate()->set_var("sTipoItemForm", $objType);

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
            $this->getTemplate()->set_var("sNoRecords", "No hay archivos cargados para la publicación");
        }

        //aca despues hay que usar el parametros max fotos publicacion
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

    /**
     * Tiene que validar que la publicacion sea efectivamente una que haya creado el usuario que esta en sesion.
     */
    public function archivosProcesar()
    {
        if($this->getRequest()->has('agregarArchivo')){
            $this->agregarArchivoPublicacion();
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

    private function agregarArchivoPublicacion()
    {
        try{
            $iPublicacionId = $this->getRequest()->getParam('iPublicacionId');
            $objType = $this->getRequest()->getParam('objType');

            if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            switch($objType)
            {
                case "publicacion":
                    $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                    break;
                case "review":
                    $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                    break;
            }

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oFicha->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar archivos a esta publicación", 401);
            }

            $nombreInputFile = 'archivoGaleria';

            $this->getUploadHelper()->setTiposValidosCompresiones();

            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){

                $idItem = $oFicha->getId();

                list($nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo) = $this->getUploadHelper()->generarArchivoSistema($idItem, 'publicacion', $nombreInputFile);
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
                    $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "ajaxRowFoto", "RowArchivoEditBlock");
                    
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
                    
                    $respuesta = "1;; ".$this->getTemplate()->pparse('ajaxRowFoto', false);
                    
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                }catch(Exception $e){
                    
                    $respuesta = "0;; Error al guardar en base de datos";
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                    return;
                }
            }
        }catch(Exception $e){
            
            $respuesta = "0;; Error al procesar el archivo";
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

        $iPublicacionId = $this->getRequest()->getParam('iPublicacionId');
        $objType = $this->getRequest()->getParam('objType');

        if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
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

        $this->getTemplate()->set_var("iItemId", $iPublicacionId);
        $this->getTemplate()->set_var("sTipoItem", $objType);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function procesarDenuncia()
    {        
        $iPublicacionId = $this->getRequest()->getParam('iItemIdFormDenuncia');
        $objType = $this->getRequest()->getParam('sTipoItemFormDenuncia');

        if(empty($iPublicacionId) || empty($objType)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            //no se puede denunciar 2 veces la misma institucion
            if(ComunidadController::getInstance()->usuarioEnvioDenunciaFicha($iPublicacionId)){
                $msg = "Su denuncia ya fue enviada. No puede denunciar dos veces la misma publicación.";
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

            switch($objType)
            {
                case "Publicacion":
                    $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                    break;
                case "Review":
                    $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                    break;
            }
            
            $oFicha->addDenuncia($oDenuncia);
            $result = ComunidadController::getInstance()->guardarDenuncias($oFicha);

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