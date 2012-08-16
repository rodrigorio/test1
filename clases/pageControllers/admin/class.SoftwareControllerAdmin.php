<?php

class SoftwareControllerAdmin extends PageControllerAbstract
{
    private $filtrosFormConfig = array('filtroTitulo' => 'f.titulo',
                                       'filtroApellidoAutor' => 'p.apellido',
                                       'filtroCategoria' => 's.categorias_id',
                                       'filtroFechaDesde' => 'fechaDesde',
                                       'filtroFechaHasta' => 'fechaHasta');

    private $orderByConfig = array('autor' => array('variableTemplate' => 'orderByAutor',
                                                    'orderBy' => 'ap.apellido',
                                                    'order' => 'desc'),
                                   'titulo' => array('variableTemplate' => 'orderByTitulo',
                                                     'orderBy' => 'f.titulo',
                                                     'order' => 'desc'),
                                   'fecha' => array('variableTemplate' => 'orderByFecha',
                                                    'orderBy' => 'f.fecha',
                                                    'order' => 'desc'),
                                   'activo' => array('variableTemplate' => 'orderByActivo',
                                                   'orderBy' => 'f.activo',
                                                   'order' => 'desc'));
    
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/admin/frame01-02.gui.html", "frame");
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index(){
        $this->listar();
    }

    public function listar()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSoftware");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "mainContent", "ListadoSoftwareBlock");

            //select filtro categoria
            $aCategorias = ComunidadController::getInstance()->obtenerCategoria();
            foreach ($aCategorias as $oCategoria){
                $value = $oCategoria->getId();
                $text = $oCategoria->getNombre();
                $this->getTemplate()->set_var("iCategoriaId", $value);
                $this->getTemplate()->set_var("sFiltroCategoria", $text);
                $this->getTemplate()->parse("OptionFiltroCategoriaBlock", true);
            }

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $aSoftware = AdminController::getInstance()->buscarSoftwareComunidad($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aSoftware) > 0){

                foreach($aSoftware as $oSoftware){

                    $oUsuario = $oSoftware->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();
                                        
                    $this->getTemplate()->set_var("iSoftwareId", $oFicha->getId());
                    $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                    
                    if($oSoftware->isActivo()){
                        $this->getTemplate()->set_var("sSelectedSoftwareActivo", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedSoftwareDesactivado", "selected='selected'");
                    }

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                    $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());                    
                    $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());

                    $this->getTemplate()->parse("SoftwareBlock", true);
                    $this->getTemplate()->set_var("sSelectedSoftwareActivo", "");
                    $this->getTemplate()->set_var("sSelectedSoftwareDesactivado", "");
                }

                $this->getTemplate()->set_var("NoRecordsSoftwareBlock", "");
                
            }else{
                $this->getTemplate()->set_var("SoftwareBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "noRecords", "NoRecordsSoftwareBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay aplicaciones cargadas en la comunidad");
                $this->getTemplate()->parse("noRecords", false);
            }

            $params[] = "masSoftware=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/software-procesar", "listadoSoftwareResult", $params);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    /**
     * por ahora solo edicion
     */
    public function form()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iSoftwareId = $this->getRequest()->getPost('iSoftwareId');

        if(empty($iSoftwareId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "popUpContent", "FormularioPublicacionBlock");

        $oSoftware = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);

        $this->getTemplate()->set_var("iSoftwareIdForm", $iSoftwareId);

        $sTitulo = $oSoftware->getTitulo();
        $sDescripcionBreve = $oSoftware->getDescripcionBreve();
        $bActivoComentarios = $oSoftware->isActivoComentarios();
        $bActivo = $oSoftware->isActivo();
        $bPublico = $oSoftware->isPublico();
        $sDescripcion = $oSoftware->getDescripcion();
        $sEnlaces = $oSoftware->getEnlaces();
        $iCategoriaId = $oSoftware->getCategoria()->getId();

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

        //select categoria
        $aCategorias = ComunidadController::getInstance()->obtenerCategoria();
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

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcionBreve", $sDescripcionBreve);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("sEnlaces", $sEnlaces);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));

    }

    public function procesar()
    {        
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('masSoftware')){
            $this->masSoftware();
            return;
        }

        if($this->getRequest()->has('masModeraciones')){
            $this->masModeraciones();
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

        if($this->getRequest()->has('modificarSoftware')){
            $this->modificarSoftware();
            return;
        }
        
        if($this->getRequest()->has('ampliarSoftware')){
            $this->ampliar();
            return;
        }

        if($this->getRequest()->has('eliminarComentario')){
            $this->eliminarComentario();
            return;
        }
        
        if($this->getRequest()->has('moderarSoftware')){
            $this->moderarSoftware();
            return;
        }

        //adjuntos en software ampliado
        if($this->getRequest()->has('eliminarArchivo')){
            $this->eliminarArchivo();
            return;
        }
        if($this->getRequest()->has('eliminarFoto')){
            $this->eliminarFoto();
            return;
        }
        if($this->getRequest()->has('formArchivo')){
            $this->formArchivo();
            return;
        }
        if($this->getRequest()->has('formFoto')){
            $this->formFoto();
            return;
        }        
        if($this->getRequest()->has('guardarFoto')){
            $this->guardarFoto();
            return;
        }
        if($this->getRequest()->has('guardarArchivo')){
            $this->guardarArchivo();
            return;
        }
    }

    private function moderarSoftware()
    {        
        $iModeracionId = $this->getRequest()->getParam('iModeracionId');
        $sEstado = $this->getRequest()->getParam('estado');
        $sMensaje = $this->getRequest()->getParam('mensaje');

        if(empty($iModeracionId) || empty($sEstado) || empty($sMensaje)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oModeracion = AdminController::getInstance()->getModeracionById($iModeracionId);

            switch($sEstado)
            {
                case "aprobado": $oModeracion->setEstadoAprobado(); break;
                case "rechazado": $oModeracion->setEstadoRechazado(); break;
            }

            $oModeracion->setMensaje($sMensaje);

            $result = AdminController::getInstance()->guardarModeracion($oModeracion);

            $this->restartTemplate();

            if($result){
                $msg = "La aplicación fue moderada";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha procesado la moderacion en la aplicación";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function eliminarComentario()
    {
        $iComentarioId = $this->getRequest()->getParam('iComentarioId');

        if(empty($iComentarioId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            ComunidadController::getInstance()->borrarComentario($iComentarioId);
            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function ampliar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iSoftwareId = $this->getRequest()->getPost('iSoftwareId');

        if(empty($iSoftwareId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $oSoftware = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);

            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oSoftware->getTitulo());
            $sPermalink = 'comunidad/descargas/'.$oSoftware->getCategoria()->getUrlToken().'/'.$oSoftware->getId()."-".$sTituloUrlizedActual;

            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "popUpContent", "FichaSoftwareBlock");

            $oUsuarioAutor = $oSoftware->getUsuario();
            $sNombreAutor = $oUsuarioAutor->getApellido()." ".$oUsuarioAutor->getNombre();
            $sNombreCategoria = $oSoftware->getCategoria()->getNombre();

            $sActiva = ($oSoftware->isActivo())?"Si":"No";
            $sPrivacidad = ($oSoftware->isPublico())?"El Mundo":"Comunidad";
            $sActivoComentarios = ($oSoftware->isActivoComentarios())?"Si":"No";

            $this->getTemplate()->set_var("sTitulo", $oSoftware->getTitulo());
            $this->getTemplate()->set_var("sFecha", $oSoftware->getFecha(true));            
            $this->getTemplate()->set_var("sAutor", $sNombreAutor);
            $this->getTemplate()->set_var("sCategoria", $sNombreCategoria);
            $this->getTemplate()->set_var("sActiva", $sActiva);
            $this->getTemplate()->set_var("sPrivacidad", $sPrivacidad);
            $this->getTemplate()->set_var("sActivoComentarios", $sActivoComentarios);
            $this->getTemplate()->set_var("sDescripcionBreve", $oSoftware->getDescripcionBreve());
            $this->getTemplate()->set_var("sDescripcion", $oSoftware->getDescripcion(true));
            $this->getTemplate()->set_var("sEnlaces", $oSoftware->getEnlaces(true));
            $this->getTemplate()->set_var("sPermalink", $sPermalink);

            $bloquesValoracion = array('Valoracion0Block', 'Valoracion0_2Block', 'Valoracion1Block',
                                       'Valoracion1_2Block', 'Valoracion2Block', 'Valoracion2_2Block',
                                       'Valoracion3Block', 'Valoracion3_2Block', 'Valoracion4Block',
                                       'Valoracion4_2Block', 'Valoracion5Block');

            if($oSoftware->tieneValoracion()){

                $this->getTemplate()->set_var("NoRatingBlock", "");

                $fRating = $oSoftware->getRating();

                switch($fRating){
                    case ($fRating >= 0 && $fRating < 0.5): $valoracionBloque = 'Valoracion0Block'; break;
                    case ($fRating >= 0.5 && $fRating < 1): $valoracionBloque = 'Valoracion0_2Block'; break;
                    case ($fRating >= 1 && $fRating < 1.5): $valoracionBloque = 'Valoracion1Block'; break;
                    case ($fRating >= 1.5 && $fRating < 2): $valoracionBloque = 'Valoracion1_2Block'; break;
                    case ($fRating >= 2 && $fRating < 2.5): $valoracionBloque = 'Valoracion2Block'; break;
                    case ($fRating >= 2.5 && $fRating < 3): $valoracionBloque = 'Valoracion2_2Block'; break;
                    case ($fRating >= 3 && $fRating < 3.5): $valoracionBloque = 'Valoracion3Block'; break;
                    case ($fRating >= 3.5 && $fRating < 4): $valoracionBloque = 'Valoracion3_2Block'; break;
                    case ($fRating >= 4 && $fRating < 4.5): $valoracionBloque = 'Valoracion4Block'; break;
                    case ($fRating >= 4.5 && $fRating < 5): $valoracionBloque = 'Valoracion4_2Block'; break;
                    case ($fRating >= 5): $valoracionBloque = 'Valoracion5Block'; break;
                    default: $valoracionBloque = 'Valoracion0Block'; break;
                }
            }else{
                $valoracionBloque = "";
            }
            
            $bloquesValoracion = array_diff($bloquesValoracion, array($valoracionBloque));
            $this->getTemplate()->unset_blocks($bloquesValoracion);

            //comentarios asociados
            $aComentarios = $oSoftware->getComentarios();

            if(count($aComentarios)>0){
                $this->getTemplate()->load_file_section("gui/componentes/backEnd/comentarios.gui.html", "comentarios", "ComentariosBlock");
                $this->getTemplate()->set_var("ComentarioBlock", "");
                $this->getTemplate()->set_var("totalComentarios", count($aComentarios));

                foreach($aComentarios as $oComentario){

                    $oUsuario = $oComentario->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $bloquesValoracion = array('Valoracion0Block', 'Valoracion0_2Block', 'Valoracion1Block',
                                               'Valoracion1_2Block', 'Valoracion2Block', 'Valoracion2_2Block',
                                               'Valoracion3Block', 'Valoracion3_2Block', 'Valoracion4Block',
                                               'Valoracion4_2Block', 'Valoracion5Block');
                    
                    //tiene valoracion el comentario?                    
                    if($oComentario->emitioValoracion()){

                        $this->getTemplate()->set_var("NoValoracionBlock", "");

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
                    }else{
                        $valoracionBloque = "";
                    }

                    $bloquesValoracion = array_diff($bloquesValoracion, array($valoracionBloque));
                    $this->getTemplate()->unset_blocks($bloquesValoracion);

                    $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                    $this->getTemplate()->set_var("dFechaComentario", $oComentario->getFecha());
                    $this->getTemplate()->set_var("sComentario", $oComentario->getDescripcion(true));
                    $this->getTemplate()->set_var("iComentarioId", $oComentario->getId());

                    $this->getTemplate()->parse("ComentarioValoracionBlock", true);
                }
            }else{
                $this->getTemplate()->set_var("comentarios", "La aplicación no tiene comentarios");
            }

            //se puede llamar a este metodo porque sirve para cualquier clase que herede de FichaAbstract
            list($cantFotos, $cantVideos, $cantArchivos) = ComunidadController::getInstance()->obtenerCantidadMultimediaFicha($oSoftware->getId());
           
            if($cantFotos > 0 || $cantArchivos > 0){

                $this->getTemplate()->load_file_section("gui/componentes/backEnd/galerias.gui.html", "galeriaAdjuntos", "GaleriaAdjuntosBlock");

                //videos ya se que no voy a tener en software
                $this->getTemplate()->set_var("ThumbnailVideoEditBlock", "");

                if($cantFotos > 0){

                    $aFotos = $oSoftware->getFotos();

                    $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

                    foreach($aFotos as $oFoto){
                        $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                        $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                        $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
                        $this->getTemplate()->set_var("hrefFoto", $pathFotoServidorBigSize);
                        $this->getTemplate()->set_var("tituloFoto", $oFoto->getTitulo());
                        $this->getTemplate()->set_var("descripcionFoto", $oFoto->getDescripcion(true));
                        $this->getTemplate()->set_var("iFotoId", $oFoto->getId());

                        $this->getTemplate()->parse("ThumbnailFotoEditBlock", true);
                    }

                    $this->getTemplate()->set_var("NoRecordsFotosBlock", "");

                }else{
                    $this->getTemplate()->set_var("ThumbnailFotoEditBlock", "");
                }
            
                if($cantArchivos > 0)
                {
                    $aArchivos = $oSoftware->getArchivos();

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
                        $this->getTemplate()->set_var("iArchivoId", $oArchivo->getId());
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

                        $this->getTemplate()->parse("RowArchivoEditBlock", true);

                        $this->getTemplate()->delete_parsed_blocks("InfoArchivoBlock");
                        $this->getTemplate()->delete_parsed_blocks("TituloInfoArchivoBlock");
                        $this->getTemplate()->delete_parsed_blocks("DescripcionInfoArchivoBlock");
                    }
                    $this->getTemplate()->set_var("NoRecordsArchivosBlock", "");
                }else{
                    $this->getTemplate()->set_var("RowArchivoEditBlock", "");
                }

                $this->getTemplate()->set_var("iItemIdForm", $oSoftware->getId());
                $this->getTemplate()->set_var("sTipoItemForm", get_class($oSoftware));
            }else{
                $this->getTemplate()->set_var("galeriaAdjuntos", "La aplicación no tiene adjuntos");
            }

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }                            
    }

    private function modificarSoftware()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iSoftwareIdForm = $this->getRequest()->getPost('softwareIdForm');

            $oSoftware = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);

            $bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;

            $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($this->getRequest()->getPost("categoriaIdForm"));

            $oSoftware->setTitulo($this->getRequest()->getPost("titulo"));
            $oSoftware->setDescripcionBreve($this->getRequest()->getPost("descripcionBreve"));
            $oSoftware->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oSoftware->setEnlaces($this->getRequest()->getPost("keywords"));
            $oSoftware->isActivo($bActivo);
            $oSoftware->isPublico($bPublico);
            $oSoftware->isActivoComentarios($bActivoComentarios);
            $oSoftware->setCategoria($oCategoria);

            ComunidadController::getInstance()->guardarSoftware($oSoftware);
            $this->getJsonHelper()->setMessage("El software se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function masSoftware()
    {
        try{
            $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);

            $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "ajaxGrillaSoftwareBlock", "GrillaSoftwareBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $aSoftware = AdminController::getInstance()->buscarSoftwareComunidad($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aSoftware) > 0){

                foreach($aSoftware as $oSoftware){
                    $oUsuario = $oSoftware->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();

                    $this->getTemplate()->set_var("iSoftwareId", $oFicha->getId());
                    $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());

                    if($oSoftware->isActivo()){
                        $this->getTemplate()->set_var("sSelectedSoftwareActivo", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedSoftwareDesactivado", "selected='selected'");
                    }

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                    $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                    $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());

                    $this->getTemplate()->parse("SoftwareBlock", true);
                    $this->getTemplate()->set_var("sSelectedSoftwareActivo", "");
                    $this->getTemplate()->set_var("sSelectedSoftwareDesactivado", "");
                }

                $this->getTemplate()->set_var("NoRecordsSoftwareBlock", "");

            }else{
                $this->getTemplate()->set_var("SoftwareBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "noRecords", "NoRecordsSoftwareBlock");
                $this->getTemplate()->set_var("sNoRecords", "No se encontraron aplicaciones");
                $this->getTemplate()->parse("noRecords", false);
            }

            $paramsPaginador[] = "masSoftware=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/software-procesar", "listadoSoftwareResult", $paramsPaginador);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
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

        $oFicha = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);
        $oFicha->isActivo($bActivo);
        ComunidadController::getInstance()->guardarSoftware($oFicha);
    }

    private function borrarSoftware()
    {
        $iSoftwareId = $this->getRequest()->getParam('iSoftwareId');

        if(empty($iPublicacionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();

        try{
            $oFicha = ComunidadController::getInstance()->getSoftwareById($iSoftwareId);

            $pathServidorFotos = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $pathServidorArchivos = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

            //polimorfico
            $result = ComunidadController::getInstance()->borrarPublicacion($oFicha, $pathServidorFotos, $pathServidorArchivos);

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
            $msg = "Ocurrio un error, no se ha eliminado la publicación del sistema";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    //adjuntos
    public function formArchivo()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/backEnd/galerias.gui.html", "popUpContent", "FormularioArchivoBlock");

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

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function eliminarArchivo()
    {
        $iArchivoId = $this->getRequest()->getParam('iArchivoId');

        if(empty($iArchivoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
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

    private function formFoto()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/backEnd/galerias.gui.html", "popUpContent", "FormularioFotoBlock");
               
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

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function eliminarFoto()
    {
        $iFotoId = $this->getRequest()->getParam('iFotoId');

        if(empty($iFotoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

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

    public function listarModeraciones()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionModeracion");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "widgetsContent", "HeaderModeracionesBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "mainContent", "ListadoModeracionBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

            $iRecordsTotal = 0;
            $aSoftware = AdminController::getInstance()->buscarSoftwareModeracion($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aSoftware) > 0){

                foreach($aSoftware as $oSoftware){

                    $oUsuario = $oSoftware->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();

                    $this->getTemplate()->set_var("iSoftwareId", $oSoftware->getId());
                    $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());                    
                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                    $this->getTemplate()->set_var("sTitulo", $oSoftware->getTitulo());
                    $this->getTemplate()->set_var("sFecha", $oSoftware->getFecha());
                    
                    $aModeracion = AdminController::getInstance()->obtenerHistorialModeracionesFicha($oSoftware->getId());
                    //al menos 1 porque es un listado de software con moderacion pendiente.
                    foreach($aModeracion as $oModeracion){
                        $this->getTemplate()->set_var("sFechaModeracion", $oModeracion->getFecha(true));
                        $this->getTemplate()->set_var("sEstadoModeracion", $oModeracion->getEstado());

                        $sMensajeModeracion = $oModeracion->getMensaje(true);
                        if(empty($sMensajeModeracion)){ $sMensajeModeracion = " - "; }                      
                        $this->getTemplate()->set_var("sMensaje", $sMensajeModeracion);
                        $this->getTemplate()->set_var("iModeracionId", $oModeracion->getId());
                        
                        $this->getTemplate()->parse("ModeracionHistorialSoftwareBlock", true);
                    }

                    $this->getTemplate()->set_var("sEstadoAprobarValue", "aprobado");
                    $this->getTemplate()->set_var("sEstadoRechazarValue", "rechazado");
                    
                    $this->getTemplate()->parse("SoftwareModerarBlock", true);
                    $this->getTemplate()->set_var("ModeracionHistorialSoftwareBlock", "");
                }

                $this->getTemplate()->set_var("NoRecordsModeracionesBlock", "");

            }else{
                $this->getTemplate()->set_var("SoftwareModerarBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "noRecords", "NoRecordsModeracionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay software pendiente de moderación");
                $this->getTemplate()->parse("noRecords", false);
            }

            $params[] = "masModeraciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/software-procesar", "listadoModeracionesResult", $params);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }        
    }

    private function masModeraciones()
    {
        try{
            $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "ajaxGrillaModeracionesBlock", "GrillaModeracionesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            
            $iRecordsTotal = 0;
            $aSoftware = AdminController::getInstance()->buscarSoftwareModeracion($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aSoftware) > 0){

                foreach($aSoftware as $oSoftware){

                    $oUsuario = $oSoftware->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();

                    $this->getTemplate()->set_var("iSoftwareId", $oSoftware->getId());
                    $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                    $this->getTemplate()->set_var("sTitulo", $oSoftware->getTitulo());
                    $this->getTemplate()->set_var("sFecha", $oSoftware->getFecha());

                    $aModeracion = AdminController::getInstance()->obtenerHistorialModeracionesFicha($oSoftware->getId());
                    //al menos 1 porque es un listado de software con moderacion pendiente.
                    foreach($aModeracion as $oModeracion){
                        $this->getTemplate()->set_var("sFechaModeracion", $oModeracion->getFecha(true));
                        $this->getTemplate()->set_var("sEstadoModeracion", $oModeracion->getEstado());

                        $sMensajeModeracion = $oModeracion->getMensaje(true);
                        if(empty($sMensajeModeracion)){ $sMensajeModeracion = " - "; }
                        $this->getTemplate()->set_var("sMensaje", $sMensajeModeracion);
                        $this->getTemplate()->set_var("iModeracionId", $oModeracion->getId());

                        $this->getTemplate()->parse("ModeracionHistorialSoftwareBlock", true);
                    }

                    $this->getTemplate()->set_var("sEstadoAprobarValue", "aprobado");
                    $this->getTemplate()->set_var("sEstadoRechazarValue", "rechazado");

                    $this->getTemplate()->parse("SoftwareModerarBlock", true);
                    $this->getTemplate()->set_var("ModeracionHistorialSoftwareBlock", "");
                }

                $this->getTemplate()->set_var("NoRecordsModeracionesBlock", "");

            }else{
                $this->getTemplate()->set_var("SoftwareModerarBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/software.gui.html", "noRecords", "NoRecordsModeracionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay software pendiente de moderación");
                $this->getTemplate()->parse("noRecords", false);
            }

            $paramsPaginador[] = "masModeraciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/software-procesar", "listadoModeracionesResult", $paramsPaginador);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaModeracionesBlock', false));
        }catch(Exception $e){
            print_r($e);
        }        
    }
}