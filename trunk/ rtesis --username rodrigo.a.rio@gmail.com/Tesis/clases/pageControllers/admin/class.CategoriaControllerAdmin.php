<?php

class CategoriaControllerAdmin extends PageControllerAbstract
{
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "jsContent", "JsContent");
        
        return $this;
    }

    public function index(){
        $this->listarCategorias();
    }

    public function listarCategorias()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "ListadoCategoriasBlock");

            $iRecordsTotal = 0;
            $vCategoria = ComunidadController::getInstance()->obtenerCategoria($filtro = array(), $iRecordsTotal, null, null, null, null);
            if(count($vCategoria)>0){
                foreach ($vCategoria as $oCategoria){

                    $hrefEditarCategoria = $this->getUrlFromRoute("adminCategoriaEditarCategoria", true)."?id=".$oCategoria->getId();

                    $this->getTemplate()->set_var("hrefEditarCategoria", $hrefEditarCategoria);

                    $sDescripcion = (null === $oCategoria->getDescripcion())?" - ":$oCategoria->getDescripcion();

                    $this->getTemplate()->set_var("iCategoriaId", $oCategoria->getId());
                    $this->getTemplate()->set_var("sNombre", $oCategoria->getNombre());
                    $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
                    $this->getTemplate()->parse("CategoriasBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsCategoriasBlock", "");
            }else{
                $this->getTemplate()->set_var("CategoriasBlock", "");
            }
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function nuevaCategoria(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "FormCategoriaBlock");

            $this->getTemplate()->set_var("sTituloForm", "Crear nueva categoria");
            $this->getTemplate()->set_var("SubmitModificarCategoriaBlock", "");
            $this->getTemplate()->set_var("EditarFotoBlock", "");

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw new Exception($e);
        }
    }
    
    public function editarCategoria()
    {
        try{
            $iCategoriaId = $this->getRequest()->getParam('id');

            if(empty($iCategoriaId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "FormCategoriaBlock");

            $this->getTemplate()->set_var("sTituloForm", "Modificar categoría");
            $this->getTemplate()->set_var("SubmitCrearCategoriaBlock", "");

            $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($iCategoriaId);

            $this->getTemplate()->set_var("iCategoriaId", $oCategoria->getId());
            $this->getTemplate()->set_var("sNombre", $oCategoria->getNombre());
            $this->getTemplate()->set_var("sDescripcion", $oCategoria->getDescripcion());

            //editar foto categoria
            if(null !== $oCategoria->getFoto()){
                $oFoto = $oCategoria->getFoto();

                $this->getUploadHelper()->utilizarDirectorioUploadSitio('comunidad');
                $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();

                $this->getTemplate()->set_var("scrFotoActual", $pathFotoServidorMediumSize);
                $this->getTemplate()->set_var("hrefFotoActualAmpliada", $pathFotoServidorBigSize);

                $this->getTemplate()->parse("FotoActualFormBlock");
            }else{
                $this->getTemplate()->unset_blocks("FotoActualFormBlock");
            }

            $this->getUploadHelper()->setTiposValidosFotos();

            $this->getTemplate()->set_var("sTiposPermitidosFoto", $this->getUploadHelper()->getStringTiposValidos());
            $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
            $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
            

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw new Exception($e);
        }
    }
    
    public function verificarUsoDeCategoria()
    {
        $iCategoriaId = $this->getRequest()->getParam('iCategoriaId');
        $sNombre = $this->getRequest()->getParam('sNombre');

        try{
            if(null === $iCategoriaId){
                $oCategoria = new stdClass();
                $oCategoria->sNombre = $sNombre;
                $oCategoria = Factory::getCategoriaInstance($oCategoria);
            }else{
                $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($iCategoriaId);
                //no lo guardo es solo para la comprobacion
                $oCategoria->setNombre($sNombre);
            }

            $dataResult = '0';
            if(AdminController::getInstance()->verificarExisteCategoria($oCategoria)){
                $dataResult = '1';
            }

            $this->getAjaxHelper()->sendHtmlAjaxResponse($dataResult);
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function eliminarCategoria()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iCategoriaId = $this->getRequest()->getParam('iCategoriaId');
        if(empty($iCategoriaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{

            $this->getJsonHelper()->initJsonAjaxResponse();
            try{
                $result = AdminController::getInstance()->eliminarCategoria($iCategoriaId);

                $this->restartTemplate();

                if($result){
                    $msg = "La categoría fue eliminada del sistema";
                    $bloque = 'MsgCorrectoBlockI32';
                    $this->getJsonHelper()->setSuccess(true);
                }

            }catch(Exception $e){
                $msg = "No se pudo eliminar la categoría del sistema. Compruebe que no haya ningún software asociado.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
            $this->getTemplate()->set_var("sMensaje", $msg);
            $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

            $this->getJsonHelper()->sendJsonAjaxResponse();

        }catch(Exception $e){
            throw new Exception($e);
        }
    }
    
    public function procesarCategoria()
    {
        if($this->getRequest()->has('procesarFoto')){
            $this->procesarFoto();
            return;
        }

        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        
        if($this->getRequest()->has('borrarFoto')){
            $this->borrarFoto();
            return;
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sNombre = $this->getRequest()->getPost("nombre");
            $sDescripcion = $this->getRequest()->getPost("descripcion");

            if($this->getRequest()->has('crearCategoria')){
                $oCategoria = new stdClass();
                $oCategoria->sNombre = $sNombre;
                $oCategoria->sDescripcion = $sDescripcion;
                $oCategoria->sUrlToken = $this->getInflectorHelper()->urlize($sNombre);
                $oCategoria = Factory::getCategoriaInstance($oCategoria);

                $accion = "agregarCategoria";
                $mensaje = "Se agrego la categoria al sistema";
            }

            if($this->getRequest()->has('modificarCategoria')){
                $iCategoriaId = $this->getRequest()->getPost("iCategoriaId");
                $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($iCategoriaId);
                $oCategoria->setNombre($sNombre);
                $oCategoria->setUrlToken($this->getInflectorHelper()->urlize($sNombre));
                $oCategoria->setDescripcion($sDescripcion);

                $accion = "modificarCategoria";
                $mensaje = "La categoría se modifico exitosamente";
            }

            if(AdminController::getInstance()->verificarExisteCategoria($oCategoria)){
                $this->getJsonHelper()->setMessage("Ya existe una categoría con ese nombre.");
                $this->getJsonHelper()->setSuccess(false);
            }else{
                AdminController::getInstance()->guardarCategoria($oCategoria);
                $this->getJsonHelper()->setMessage($mensaje);
                $this->getJsonHelper()->setValor('accion', $accion);
                $this->getJsonHelper()->setSuccess(true);
            }

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function procesarFoto()
    {
        try{
            $iCategoriaId = $this->getRequest()->getPost('iCategoriaId');
            if(empty($iCategoriaId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $nombreInputFile = 'fotoUpload';

            $this->getUploadHelper()->setTiposValidosFotos();
            $this->getUploadHelper()->utilizarDirectorioUploadSitio('comunidad');

            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){
                
                $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($iCategoriaId);
                $idItem = $oCategoria->getId();

                //un array con los datos de las fotos                
                $aNombreArchivos = $this->getUploadHelper()->generarFotosSistema($idItem, $nombreInputFile);
                $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);

                try{
                    AdminController::getInstance()->guardarFotoCategoria($aNombreArchivos, $pathServidor, $oCategoria);

                    $oFoto = $oCategoria->getFoto();

                    $this->restartTemplate();
                    $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "contFotoActual", "FotoActualFormBlock");

                    $this->getUploadHelper()->utilizarDirectorioUploadSitio('comunidad');
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("scrFotoActual", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFotoActualAmpliada", $pathFotoServidorBigSize);
                    $this->getTemplate()->set_var("iCategoriaId", $iCategoriaId);

                    $respuesta = "1; ".$this->getTemplate()->pparse('contFotoActual', false);
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                }catch(Exception $e){
                    $respuesta = "0; Error al guardar en base de datos";
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                    return;
                }
            }
        }catch(Exception $e){
            echo $e->getMessage();
            return;
            
            $respuesta = "0; Error al procesar el archivo";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }
    }

    private function borrarFoto()
    {
        $iCategoriaId = $this->getRequest()->getParam('iCategoriaId');

        if(empty($iCategoriaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $this->getUploadHelper()->utilizarDirectorioUploadSitio('comunidad');
            $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($iCategoriaId);

            AdminController::getInstance()->borrarFotoCategoria($oCategoria, $pathServidor);
            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }
}