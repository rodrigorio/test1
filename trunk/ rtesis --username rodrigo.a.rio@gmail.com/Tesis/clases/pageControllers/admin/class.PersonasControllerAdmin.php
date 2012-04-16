<?php
class PersonasControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/personas.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index(){
        $this->listarPersonas();
    }

    public function listarPersonas()
    {
        
    }

    public function listarModeracionesPendientes()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionModeracion");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/personas.gui.html", "widgetsContent", "HeaderBlock");

            $this->getTemplate()->load_file_section("gui/vistas/admin/personas.gui.html", "mainContent", "ListadoModeracionesBlock");

            $filtro = array();
            $iRecordPerPage = 5;
            $iPage = $this->getRequest()->getPost("iPage");
            $iPage = strlen($iPage) ? $iPage : 1;
            $iItemsForPage = $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
            $iMinLimit = ($iPage-1) * $iItemsForPage;
            $sOrderBy = null;
            $sOrder = null;
            $iRecordsTotal = 0;

            $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
            
            //array con objetos discapacitados desde discapacitados_moderacion (datos sin aprobar).
            $aDiscapacitadosMod = AdminController::getInstance()->obtenerModeracionesDiscapacitados($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            if(count($aDiscapacitadosMod) > 0){
            	$i=0;
                foreach($aDiscapacitadosMod as $oDiscapacitadoMod){      
                    
                    $oDiscapacitado = SeguimientosController::getInstance()->getDiscapacitadoById($oDiscapacitadoMod->getId());
                    $oUsuarioAsignado = $oDiscapacitado->getUsuario();
                    $oUsuarioSolicita = $oDiscapacitadoMod->getUsuario();

                    $sNombreDiscapacitado = $oDiscapacitado->getNombre()." ".$oDiscapacitado->getApellido();
                    $sNombreDiscapacitadoMod = $oDiscapacitadoMod->getNombre()." ".$oDiscapacitadoMod->getApellido();
                    $sNombreUsuarioAsignado = $oUsuarioAsignado->getNombre()." ".$oUsuarioAsignado->getApellido();
                    $sNombreUsuarioSolicita = $oUsuarioSolicita->getNombre()." ".$oUsuarioSolicita->getApellido();

                    $this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");

                    $this->getTemplate()->set_var("sNombreDiscapacitado", $sNombreDiscapacitado);
                    $this->getTemplate()->set_var("sNombreUsuarioAsignado", $sNombreUsuarioAsignado);
                    $this->getTemplate()->set_var("sNombreUsuarioSolicita", $sNombreUsuarioSolicita);
                    $this->getTemplate()->set_var("personaId", $oDiscapacitado->getId());

                    //Lleno la ficha con los datos actuales y los datos sin moderar. Marco en rojo los que sufrieron modificaciones

                    //discapacitado actual
                    $iPaisId = "";
                    $iProvinciaId = "";
                    $iCiudadId = "";
                    $sUbicacion = "";
                    if(null != $oDiscapacitado->getCiudad()){
                        $iCiudadId = $oDiscapacitado->getCiudad()->getId();
                        $sUbicacion .= $oDiscapacitado->getCiudad()->getNombre();
                        if(null != $oDiscapacitado->getCiudad()->getProvincia()){
                            $iProvinciaId = $oDiscapacitado->getCiudad()->getProvincia()->getId();
                            $sUbicacion .= " ".$oDiscapacitado->getCiudad()->getProvincia()->getNombre();
                            if(null != $oDiscapacitado->getCiudad()->getProvincia()->getPais()){
                                $iPaisId = $oDiscapacitado->getCiudad()->getProvincia()->getPais()->getId();
                                $sUbicacion .= " ".$oDiscapacitado->getCiudad()->getProvincia()->getPais()->getNombre();
                            }
                        }
                    }

                    $sDomicilio = $oDiscapacitado->getDomicilio();
                    $sTelefono = $oDiscapacitado->getTelefono();
                    $sNombreApellidoPadre = $oDiscapacitado->getNombreApellidoPadre();
                    $sNombreApellidoMadre = $oDiscapacitado->getNombreApellidoMadre();
                    $sOcupacionPadre = $oDiscapacitado->getOcupacionPadre();
                    $sOcupacionMadre = $oDiscapacitado->getOcupacionMadre();
                    $sNombreHermanos = $oDiscapacitado->getNombreHermanos();

                    $iInstitucionId = "";
                    $sInstitucion = "";
                    if(null != $oDiscapacitado->getInstitucion()){
                        $iInstitucionId = $oDiscapacitado->getInstitucion()->getId();
                        $sInstitucion = $oDiscapacitado->getInstitucion()->getNombre();
                    }

                    //foto de perfil actual                    
                    if(null != $oDiscapacitado->getFotoPerfil()){
                        $oFoto = $oDiscapacitado->getFotoPerfil();
                        $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                        $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    }else{
                        $pathFotoServidorMediumSize=$pathFotoServidorBigSize=$this->getUploadHelper()->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar(true);
                    }

                    $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
                    $sDocumento = $aTiposDocumentos[$oDiscapacitado->getTipoDocumento()]." ".$oDiscapacitado->getNumeroDocumento();

                    $sSexo = ($oDiscapacitado->getSexo() == 'm')?"Masculino":"Femenino";

                    $sFechaNacimiento = Utils::fechaFormateada($oDiscapacitado->getFechaNacimiento(), "d/m/Y");
                    $sNacimientoPadre = Utils::fechaFormateada($oDiscapacitado->getFechaNacimientoPadre(), "d/m/Y");
                    $sNacimientoMadre = Utils::fechaFormateada($oDiscapacitado->getFechaNacimientoMadre(),"d/m/Y");

                    //los textarea si estan vacios le pongo un guion para que quede bien la vista
                    if(empty($sOcupacionPadre)){$sOcupacionPadre = " - ";}
                    if(empty($sOcupacionMadre)){$sOcupacionMadre = " - ";}
                    if(empty($sNombreHermanos)){$sNombreHermanos = " - ";}

                    //discapacitado sin moderar
                    $iPaisId = "";
                    $iProvinciaId = "";
                    $iCiudadId = "";
                    $sUbicacionMod = "";
                    if(null != $oDiscapacitadoMod->getCiudad()){
                        $iCiudadId = $oDiscapacitadoMod->getCiudad()->getId();
                        $sUbicacionMod .= $oDiscapacitadoMod->getCiudad()->getNombre();
                        if(null != $oDiscapacitadoMod->getCiudad()->getProvincia()){
                            $iProvinciaId = $oDiscapacitadoMod->getCiudad()->getProvincia()->getId();
                            $sUbicacionMod .= " ".$oDiscapacitadoMod->getCiudad()->getProvincia()->getNombre();
                            if(null != $oDiscapacitadoMod->getCiudad()->getProvincia()->getPais()){
                                $iPaisId = $oDiscapacitadoMod->getCiudad()->getProvincia()->getPais()->getId();
                                $sUbicacionMod .= " ".$oDiscapacitadoMod->getCiudad()->getProvincia()->getPais()->getNombre();
                            }
                        }
                    }

                    $sDomicilioMod = $oDiscapacitadoMod->getDomicilio();
                    $sTelefonoMod = $oDiscapacitadoMod->getTelefono();
                    $sNombreApellidoPadreMod = $oDiscapacitadoMod->getNombreApellidoPadre();
                    $sNombreApellidoMadreMod = $oDiscapacitadoMod->getNombreApellidoMadre();
                    $sOcupacionPadreMod = $oDiscapacitadoMod->getOcupacionPadre();
                    $sOcupacionMadreMod = $oDiscapacitadoMod->getOcupacionMadre();
                    $sNombreHermanosMod = $oDiscapacitadoMod->getNombreHermanos();

                    $iInstitucionIdMod = "";
                    $sInstitucionMod = "";
                    if(null != $oDiscapacitadoMod->getInstitucion()){
                        $iInstitucionIdMod = $oDiscapacitadoMod->getInstitucion()->getId();
                        $sInstitucionMod = $oDiscapacitadoMod->getInstitucion()->getNombre();
                    }

                    //foto de perfil actual
                    if(null != $oDiscapacitadoMod->getFotoPerfil()){                        
                        $oFotoMod = $oDiscapacitadoMod->getFotoPerfil();                        
                        $pathFotoServidorMediumSizeMod = $this->getUploadHelper()->getDirectorioUploadFotos().$oFotoMod->getNombreMediumSize();
                        $pathFotoServidorBigSizeMod = $this->getUploadHelper()->getDirectorioUploadFotos().$oFotoMod->getNombreBigSize();
                    }else{
                        $pathFotoServidorMediumSizeMod=$pathFotoServidorBigSizeMod=$this->getUploadHelper()->getDirectorioUploadFotos().$oDiscapacitadoMod->getNombreAvatar(true);
                    }

                    $sDocumentoMod = $aTiposDocumentos[$oDiscapacitadoMod->getTipoDocumento()]." ".$oDiscapacitadoMod->getNumeroDocumento();

                    $sSexoMod = ($oDiscapacitadoMod->getSexo() == 'm')?"Masculino":"Femenino";

                    $sFechaNacimientoMod = Utils::fechaFormateada($oDiscapacitadoMod->getFechaNacimiento(), "d/m/Y");
                    $sNacimientoPadreMod = Utils::fechaFormateada($oDiscapacitadoMod->getFechaNacimientoPadre(), "d/m/Y");
                    $sNacimientoMadreMod = Utils::fechaFormateada($oDiscapacitadoMod->getFechaNacimientoMadre(),"d/m/Y");

                    //los textarea si estan vacios le pongo un guion para que quede bien la vista
                    if(empty($sOcupacionPadreMod)){$sOcupacionPadreMod = " - ";}
                    if(empty($sOcupacionMadreMod)){$sOcupacionMadreMod = " - ";}
                    if(empty($sNombreHermanosMod)){$sNombreHermanosMod = " - ";}

                    //si coinciden los valores destaco los campos en las dos fichas.    
                    $this->getTemplate()->set_var("sNombreMod", $sNombreDiscapacitadoMod);
                    $this->getTemplate()->set_var("sNombre", $sNombreDiscapacitado);
                    if($sNombreDiscapacitadoMod != $sNombreDiscapacitado){
                        $this->getTemplate()->set_var("destacadoNombre", "destacado");
                    }

                    $this->getTemplate()->set_var("sDocumento", $sDocumento);
                    $this->getTemplate()->set_var("sDocumentoMod", $sDocumentoMod);
                    if($sDocumento != $sDocumentoMod){
                        $this->getTemplate()->set_var("destacadoDocumento", "destacado");
                    }

                    $this->getTemplate()->set_var("sSexo", $sSexo);
                    $this->getTemplate()->set_var("sSexoMod", $sSexoMod);
                    if($sSexo != $sSexoMod){
                        $this->getTemplate()->set_var("destacadoSexo", "destacado");
                    }

                    $this->getTemplate()->set_var("sFechaNacimiento", $sFechaNacimiento);
                    $this->getTemplate()->set_var("sFechaNacimientoMod", $sFechaNacimientoMod);
                    if($sFechaNacimiento != $sFechaNacimientoMod){
                        $this->getTemplate()->set_var("destacadoFechaNacimiento", "destacado");
                    }

                    $this->getTemplate()->set_var("sUbicacion", $sUbicacion);
                    $this->getTemplate()->set_var("sUbicacionMod", $sUbicacionMod);
                    if($sUbicacion != $sUbicacionMod){
                        $this->getTemplate()->set_var("destacadoUbicacion", "destacado");
                    }

                    $this->getTemplate()->set_var("sTelefono", $sTelefono);
                    $this->getTemplate()->set_var("sTelefonoMod", $sTelefonoMod);
                    if($sTelefono != $sTelefonoMod){
                        $this->getTemplate()->set_var("destacadoTelefono", "destacado");
                    }

                    $this->getTemplate()->set_var("sDomicilio", $sDomicilio);
                    $this->getTemplate()->set_var("sDomicilioMod", $sDomicilioMod);
                    if($sDomicilio != $sDomicilioMod){
                        $this->getTemplate()->set_var("destacadoDomicilio", "destacado");
                    }

                    $this->getTemplate()->set_var("sNombreApellidoPadre", $sNombreApellidoPadre);
                    $this->getTemplate()->set_var("sNombreApellidoPadreMod", $sNombreApellidoPadreMod);
                    if($sNombreApellidoPadre != $sNombreApellidoPadreMod){
                        $this->getTemplate()->set_var("destacadoNombrePadre", "destacado");
                    }

                    $this->getTemplate()->set_var("sOcupacionPadre", $sOcupacionPadre);
                    $this->getTemplate()->set_var("sOcupacionPadreMod", $sOcupacionPadreMod);
                    if($sOcupacionPadre != $sOcupacionPadreMod){
                        $this->getTemplate()->set_var("destacadoOcupacionPadre", "destacado");
                    }

                    $this->getTemplate()->set_var("sNacimientoPadre", $sNacimientoPadre);
                    $this->getTemplate()->set_var("sNacimientoPadreMod", $sNacimientoPadreMod);
                    if($sNacimientoPadre != $sNacimientoPadreMod){
                        $this->getTemplate()->set_var("destacadoNacimientoPadre", "destacado");
                    }

                    $this->getTemplate()->set_var("sNombreApellidoMadre", $sNombreApellidoMadre);
                    $this->getTemplate()->set_var("sNombreApellidoMadreMod", $sNombreApellidoMadreMod);
                    if($sNombreApellidoMadre != $sNombreApellidoMadreMod){
                        $this->getTemplate()->set_var("destacadoNombreMadre", "destacado");
                    }

                    $this->getTemplate()->set_var("sOcupacionMadre", $sOcupacionMadre);
                    $this->getTemplate()->set_var("sOcupacionMadreMod", $sOcupacionMadreMod);
                    if($sOcupacionMadre != $sOcupacionMadreMod){
                        $this->getTemplate()->set_var("destacadoOcupacionMadre", "destacado");
                    }

                    $this->getTemplate()->set_var("sNacimientoMadre", $sNacimientoMadre);
                    $this->getTemplate()->set_var("sNacimientoMadreMod", $sNacimientoMadreMod);
                    if($sNacimientoMadre != $sNacimientoMadreMod){
                        $this->getTemplate()->set_var("destacadoNacimientoMadre", "destacado");
                    }

                    $this->getTemplate()->set_var("sNombreHermanos", $sNombreHermanos);
                    $this->getTemplate()->set_var("sNombreHermanosMod", $sNombreHermanosMod);
                    if($sNombreHermanos != $sNombreHermanosMod){
                        $this->getTemplate()->set_var("destacadoNombreHermanos", "destacado");
                    }

                    $this->getTemplate()->set_var("iInstitucionId", $iInstitucionId);
                    $this->getTemplate()->set_var("iInstitucionIdMod", $iInstitucionIdMod);
                    $this->getTemplate()->set_var("sNombreInstitucion", $sInstitucion);
                    $this->getTemplate()->set_var("sNombreInstitucionMod", $sInstitucionMod);
                    if($iInstitucionId != $iInstitucionIdMod){
                        $this->getTemplate()->set_var("destacadoInstitucion", "destacado");
                    }

                    $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliadaMod",$pathFotoServidorBigSizeMod);
                    $this->getTemplate()->set_var("scrFotoPerfilActualMod",$pathFotoServidorMediumSizeMod);
                    $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada",$pathFotoServidorBigSize);
                    $this->getTemplate()->set_var("scrFotoPerfilActual",$pathFotoServidorMediumSize);
                    if($pathFotoServidorBigSizeMod != $pathFotoServidorBigSize){
                        $this->getTemplate()->set_var("destacadoFotoPerfil", "destacado");
                    }
                    
                    $this->getTemplate()->parse("PersonasModeradasBlock", true);
                    $i++;
                }
                $this->getTemplate()->set_var("NoRecordsPersonasModeradasBlock", "");
            }else{
                $this->getTemplate()->set_var("PersonasModeradasBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/personas.gui.html", "noRecords", "NoRecordsDiscapacitadosModBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay moderaciones pendientes");
                $this->getTemplate()->parse("noRecords", false);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    /**
     * Con una misma accion puedo aprobar o rechazar dependiendo un parametro.
     * Se puede porque el que tiene permiso para rechazar tiene permiso para aprobar cambios.
     */
    public function procesarModeracion()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('rechazar')){
            $this->rechazarCambiosModeracion();
            return;
        }

        if($this->getRequest()->has('aprobar')){
            $this->aprobarCambiosModeracion();
            return;
        }        
    }

    private function aprobarCambiosModeracion()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iPersonaIdForm = $this->getRequest()->getParam('personaId');
        if(empty($iPersonaIdForm)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();

        try{
            $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
            $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $result = AdminController::getInstance()->aprobarModeracionDiscapacitado($iPersonaIdForm, $pathServidor);

            $this->restartTemplate();

            if($result){
                $msg = "Los cambios se guardaron con exito";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se guardaron los cambios";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);                
            }
            
        }catch(Exception $e){
            $msg = "Ocurrio un error, no se guardaron los cambios";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();   
    }
    
    private function rechazarCambiosModeracion()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iPersonaIdForm = $this->getRequest()->getParam('personaId');
        if(empty($iPersonaIdForm)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();

        try{
            $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
            $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $result = AdminController::getInstance()->rechazarModeracionDiscapacitado($iPersonaIdForm, $pathServidor);

            $this->restartTemplate();

            if($result){
                $msg = "La accion se proceso con exito";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se proceso la accion";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se proceso la accion";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();           
    }
}