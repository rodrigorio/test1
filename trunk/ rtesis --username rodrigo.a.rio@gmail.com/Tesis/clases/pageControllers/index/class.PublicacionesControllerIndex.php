<?php

/**
 * 	Action Controller Publicaciones
 */
class PublicacionesControllerIndex extends PageControllerAbstract
{		

    private function setFrameTemplate()
    {
        $this->getTemplate()->load_file("gui/templates/frameBlog01-02.gui.html", "frame");
        return $this;
    }

    private function setHeadTemplate()
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
        $tituloVista = $nombreSitio.' | '.$parametros->obtener('METATAG_TITLE');
        $descriptionVista = $parametros->obtener('METATAG_DESCRIPTION');
        $keywordsVista = $parametros->obtener('METATAG_KEYWORDS');
        
        $this->getTemplate()->load_file_section("gui/vistas/index/publicacion-ampliada.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);
        return $this;
    }

    /**
     * @return PublicacionesControllerIndex
     * @hace falta que se saquen los valores de los parametros.
     */
    private function setCabeceraTemplate()
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
        $subtituloSitio = $parametros->obtener('SUBTITULO_SITIO');
        $fileNameLogo = $parametros->obtener('FILE_NAME_LOGO_SITIO');
        $this->getTemplate()->set_var("sourceLogoHeader", 'sitio/uploads/design/'.$fileNameLogo);
        //hacer un metodo para extraer medidas desde foto.
        $this->getTemplate()->set_var("heightLogoHeader", '89');
        $this->getTemplate()->set_var("widthLogoHeader", '156');
        $this->getTemplate()->set_var("tituloHeader", $nombreSitio);
        $this->getTemplate()->set_var("subtituloHeader", $subtituloSitio);
        return $this;
    }

    private function setMenuTemplate()
    {
        $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "menuHeader", "MenuHorizontal01Block");

        //Opcion1
        $this->getTemplate()->set_var("idOpcion", 'opt1');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/admin/');
        $this->getTemplate()->set_var("sNombreOpcion", "Administrador");
        $this->getTemplate()->parse("OpcionesMenu", true);
        //Opcion2
        $this->getTemplate()->set_var("idOpcion", 'opt2');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/about/');
        $this->getTemplate()->set_var("sNombreOpcion", "Acerca de");
        $this->getTemplate()->parse("OpcionesMenu", true);
        //Ultima Opcion
        $this->getTemplate()->set_var("idOpcion", 'opt3');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/contacto/');
        $this->getTemplate()->set_var("sNombreOpcion", "Contacto");
        $this->getTemplate()->parse("OpcionMenuLastOpt", false);
        return $this;
    }

    private function setColumnaIzqTemplate()
    {
        //pruebo insertando las publicidades falsas, despues codificar el modulo adsense (metodos devuelven publicidad por sector)
        $adsense = "<div class='as_160_600 baco2 mabo2'>Publicidad Adsense 160x600</div>
                    <div class='as_160_600 baco2'>Publicidad Adsense 160x600</div>";

        $this->getTemplate()->set_var("columnaIzquierdaContent", $adsense);
        return $this;
    }

    private function setFooterTemplate()
    {
        $footerContent = "UrBIS . Todos los derechos reservados";
        $this->getTemplate()->set_var("footerContent", $footerContent);
        return $this;
    }

    private function setBloqueValoracionPublicacionTemplate($valor)
    {
        $bloquesValoracion = array('Valoracion0Block', 'Valoracion0_2Block', 'Valoracion1Block',
                                   'Valoracion1_2Block', 'Valoracion2Block', 'Valoracion2_2Block',
                                   'Valoracion3Block', 'Valoracion3_2Block', 'Valoracion4Block',
                                   'Valoracion4_2Block', 'Valoracion5Block');
        switch($valor){
            case ($valor >= 0 && $valor < 0.5): $valoracionBloque = 'Valoracion0Block'; break;
            case ($valor >= 0.5 && $valor < 1): $valoracionBloque = 'Valoracion0_2Block'; break;
            case ($valor >= 1 && $valor < 1.5): $valoracionBloque = 'Valoracion1Block'; break;
            case ($valor >= 1.5 && $valor < 2): $valoracionBloque = 'Valoracion1_2Block'; break;
            case ($valor >= 2 && $valor < 2.5): $valoracionBloque = 'Valoracion2Block'; break;
            case ($valor >= 2.5 && $valor < 3): $valoracionBloque = 'Valoracion2_2Block'; break;
            case ($valor >= 3 && $valor < 3.5): $valoracionBloque = 'Valoracion3Block'; break;
            case ($valor >= 3.5 && $valor < 4): $valoracionBloque = 'Valoracion3_2Block'; break;
            case ($valor >= 4 && $valor < 4.5): $valoracionBloque = 'Valoracion4Block'; break;
            case ($valor >= 4.5 && $valor < 5): $valoracionBloque = 'Valoracion4_2Block'; break;
            case ($valor >= 5): $valoracionBloque = 'Valoracion5Block'; break;
            default: $valoracionBloque = 'Valoracion0Block'; break;
        }

        //elimino el bloque que tengo que dejar y llamo a la funcion de Template para elimine el resto de los bloques
        $bloquesValoracion = array_diff($bloquesValoracion, array($valoracionBloque));
        $this->getTemplate()->unset_blocks($bloquesValoracion);
    }

    private function setGaleriaFotosPublicacionTemplate(array $fotos)
    {
        if(!empty($fotos)){
            //genero thumbnails por cada foto del array. el ultimo thumbnail es otro bloque
            foreach($fotos as $foto){
                //codificar
            }
        }
        return $this;
    }

    private function setGaleriaVideosPublicacionTemplate(array $videos)
    {
        return $this;
    }

    private function setGaleriaArchivosPublicacionTemplate(array $archivos)
    {
        return $this;
    }

    private function setComentariosPublicacionTemplate(array $comentarios)
    {
        return $this;
    }

    /**
     * Si no existe ultima publicacion muestra mensaje 'no hay publicaciones cargadas'
     */
    public function index()
    {
        $publicacion = BlogController::getInstance()->obtenerUltimaPublicacion();
        if (null === $publicacion){
            $this->sinPublicaciones();
        } else {
            $this->ampliarPublicacion($publicacion);
        }
    }

    public function ampliarPublicacion(Publicacion $publicacion = null)
    {
        //si el objeto no se paso por parametro trata de obtenerlo desde los argumentos que paso el distpatcher
        if(null === $publicacion){
            try {
                $publicacion = BlogController::getInstance()->obtenerPublicacion($this->getInvokeParam('publicacionId'));
            }catch(Exception $e){
                throw new Exception("La publicacion no existe o fue eliminada", 404);
                return;
            }
        }

        //Inicio el frame y el header, despues agrego los componentes por defecto de la vista
        try{
            $this->setFrameTemplate()
                 ->setHeadTemplate()
                 ->setCabeceraTemplate()
                 ->setMenuTemplate()
                 ->setColumnaIzqTemplate()
                 ->setFooterTemplate();
        }catch(Exception $e){
            throw new Exception('Error Template');
            return;
        }

        //si no se redirecciono a esta vista con mensaje esta funcion no agrega nada.
        $this->printMsgTop();

        $cantidadCriticas = $publicacion->getCantidadCriticas();
        $activarValoracion = true; //desp tmb checkear parametro que active valoracion
        if(!empty($cantidadCriticas) && $activarValoracion){
            $this->getTemplate()->load_file_section("gui/vistas/index/publicacion-ampliada.gui.html",
                                                    "columnaDerechaContent",
                                                    "PublicacionEncabezadoValoracionBlock");

            //obtengo valoracion y proceso los bloques para quedarme con el que corresponde
            $valoracion = $publicacion->getValoracion();
            $this->setBloqueValoracionPublicacionTemplate($valoracion);

            $this->getTemplate()->set_var("iCantCriticas", $cantidadCriticas);
        }else{
            $this->getTemplate()->load_file_section("gui/vistas/index/publicacion-ampliada.gui.html",
                                                    "columnaDerechaContent",
                                                    "PublicacionEncabezadoBlock");
        }

        $this->getTemplate()->set_var("hrefShareFacebook", 'http://www.facebook.com');
        $this->getTemplate()->set_var("hrefShareTwitter", 'http://www.twitter.com');
        $this->getTemplate()->set_var("hrefShareRss", $this->getRequest()->getBaseUrl().'/rss/publicaciones.xml');

        //concateno al mismo bloque (columnaDerechaContent) el bloque principal, se indica con el ultimo parametro.
        $this->getTemplate()->load_file_section("gui/vistas/index/publicacion-ampliada.gui.html",
                                                "columnaDerechaContent",
                                                "PublicacionBlock", true);

        $this->getTemplate()->set_var("sTituloPublicacion", $publicacion->getTitulo());
        $this->getTemplate()->set_var("dFechaAlta", $publicacion->getFechaAlta());
        $this->getTemplate()->set_var("sAutor", $publicacion->getAutor()->getNombreUsuario());
        $this->getTemplate()->set_var("sDescripcion", $publicacion->getDescripcion());

        //si el metodo para esta vista, y esta posic devuelve Ad.
        $adSense = "<div class='as_336_280 baco2 mabo2'>Publicidad Adsense 160x600</div>";
        $this->getTemplate()->set_var("publicidad", $adSense);

        //extraigo array de adjuntos y los agrego a continuacion de la publicacion
        $fotos = $publicacion->getFotos();
        $videos = $publicacion->getVideos();
        $archivos = $publicacion->getArchivos();

        if(!empty($fotos)||!empty($videos)||!empty($archivos)){
            $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html",
                                                    "columnaDerechaContent",
                                                    "TituloGaleriaBlock",true);
            $this->getTemplate()->set_var("tituloGaleria", "Galer&iacute;a");

            $this->setGaleriaFotosPublicacionTemplate($fotos)
                 ->setGaleriaVideosPublicacionTemplate($videos)
                 ->setGaleriaArchivosPublicacionTemplate($archivos);
        }

        $this->getTemplate()->load_file_section("gui/vistas/index/publicacion-ampliada.gui.html",
                                                "columnaDerechaContent",
                                                "PublicacionPie", true);

        $urlImprimir = $this->getRequest()->getBaseUrl().'/imprimir/'.$publicacion->getId().'-'.$publicacion->getTitulo().'.html';
        $this->getTemplate()->set_var("hrefImprimir", $urlImprimir);

        //formulario para nuevo comentario
        $this->getTemplate()->load_file_section("gui/componentes/comentarios.gui.html",
                                                "columnaDerechaContent",
                                                "NuevoComentarioBlock", true);

        $comentarios = $publicacion->getComentarios();
        $this->setComentariosPublicacionTemplate($comentarios);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    /**
     * Este metodo no es una redireccion !!!
     * Solo se usa cuando no hay ninguna publicacion en el sitio.
     * Si la situacion es: 'no se encuentra el id de publicacion' se usa el metodo redireccion404();
     */
    public function sinPublicaciones()
    {
        $this->getTemplate()->load_file("gui/templates/frameBlog01-01.gui.html", "frame");

        $this->setHeadTemplate()
             ->setCabeceraTemplate()
             ->setMenuTemplate()
             ->setFooterTemplate();

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html",
                                                "columnaCentralContent",
                                                "MsgInfoBlock");

        $this->getTemplate()->set_var("sMensaje", "Por el momento el sitio no tiene publicaciones cargadas.");
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }
}