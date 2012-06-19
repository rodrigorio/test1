/*
SQLyog Enterprise - MySQL GUI v8.18 
MySQL - 5.1.36-community-log : Database - tesis
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tesis` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `tesis`;

/*Table structure for table `acciones` */

DROP TABLE IF EXISTS `acciones`;

CREATE TABLE `acciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controladores_pagina_id` int(11) NOT NULL,
  `accion` varchar(200) DEFAULT NULL,
  `grupo` tinyint(2) NOT NULL DEFAULT '1' COMMENT 'No hay una correspondencia con el id del perfil, hay 5 grupos porque hay 5 perfiles pero podria haber mas. por defecto solo admin. los grupos pueden ser: 1)ADMIN 2)MODERADOR 3)INTEGANTE ACTIVO 4)INTEGANTE INACTIVO 5)VISITANTES',
  `activo` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'por defecto desactivada',
  PRIMARY KEY (`id`),
  KEY `FK_acciones_controladores_pagina` (`controladores_pagina_id`),
  KEY `grupo` (`grupo`),
  CONSTRAINT `FK_acciones_controladores_pagina` FOREIGN KEY (`controladores_pagina_id`) REFERENCES `controladores_pagina` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=latin1;

/*Data for the table `acciones` */

insert  into `acciones`(`id`,`controladores_pagina_id`,`accion`,`grupo`,`activo`) values (1,1,'index',5,1),(2,1,'redireccion404',5,1),(3,1,'sitioOffline',5,1),(4,1,'sitioEnConstruccion',5,1),(5,1,'ajaxError',5,1),(6,2,'index',5,1),(7,2,'procesar',5,1),(8,2,'redireccion404',5,1),(9,3,'index',5,1),(10,3,'formulario',5,1),(11,3,'procesar',5,1),(12,3,'redireccion404',5,1),(13,4,'index',2,1),(14,4,'redireccion404',2,1),(15,5,'index',4,1),(16,5,'redireccion404',4,1),(17,6,'index',3,1),(18,6,'redireccion404',3,1),(19,6,'procesar',3,1),(20,6,'formulario',3,1),(21,6,'listado',3,1),(22,7,'index',1,1),(23,7,'redireccion404',1,1),(24,2,'recuperarContrasenia',5,1),(25,2,'confirmarContrasenia',5,1),(26,8,'index',5,1),(27,8,'nuevaInstitucion',5,1),(28,8,'listadoInstituciones',5,1),(29,8,'provinciasByPais',5,1),(30,8,'ciudadesByProvincia',5,1),(31,8,'procesar',5,1),(32,9,'index',4,1),(33,9,'formulario',4,1),(34,9,'procesar',4,1),(35,9,'redireccion404',4,1),(36,8,'masInstituciones',5,1),(37,8,'redireccion404',5,1),(38,8,'ampliarInstitucion',5,1),(39,8,'editarInstitucion',5,1),(40,2,'logout',4,1),(41,10,'procesarEspecialidad',1,1),(42,10,'index',1,1),(43,10,'listarEspecialidades',1,1),(44,10,'nuevaEspecialidad',1,1),(45,10,'editarEspecialidad',1,1),(46,10,'eliminarEspecialidad',1,1),(47,10,'verificarUsoDeEspecialidad',1,1),(48,10,'buscarEspecialidad',1,1),(49,11,'nuevaCategoria',1,1),(50,11,'editarCategoria',1,1),(51,11,'listarCategoria',1,1),(52,11,'eliminarCategoria',1,1),(53,11,'index',1,1),(54,11,'procesarCategoria',1,1),(55,9,'modificarPrivacidadCampo',4,1),(56,12,'index',3,1),(59,14,'nuevoSeguimiento',3,1),(62,12,'buscarDiscapacitados',3,1),(63,14,'procesarSeguimiento',3,1),(64,8,'buscarInstituciones',4,1),(65,5,'descargarArchivo',4,1),(67,14,'index',3,1),(68,14,'redireccion404',3,1),(69,12,'redireccion404',3,1),(70,13,'index',3,1),(71,13,'procesar',3,1),(73,13,'agregar',3,1),(74,13,'redireccion404',3,1),(75,14,'listar',3,1),(76,14,'buscarSeguimientos',4,1),(77,13,'modificar',3,1),(78,13,'ver',3,1),(79,15,'index',2,1),(80,15,'redireccion404',2,1),(81,15,'listarModeracionesPendientes',2,1),(82,15,'procesarModeracion',2,1),(83,15,'procesarPersona',2,1),(84,14,'eliminar',3,1),(85,16,'index',2,1),(86,16,'redireccion404',2,1),(87,16,'procesar',2,1),(88,17,'redireccion404',2,1),(89,17,'index',2,1),(90,17,'procesar',2,1),(91,17,'form',2,1),(92,18,'index',2,1),(93,18,'redireccion404',2,1),(94,18,'procesar',2,1),(95,18,'form',2,1),(96,18,'cambiarPerfil',1,1),(97,18,'cerrarCuenta',1,1),(98,18,'crear',1,1),(99,18,'vistaImpresion',1,1),(101,18,'exportar',1,1),(103,9,'cerrarCuenta',4,1),(104,20,'index',4,1),(105,20,'redireccion404',4,1),(106,20,'misPublicaciones',3,1),(109,20,'guardarPublicacion',3,1),(110,20,'guardarReview',3,1),(111,20,'procesar',3,1),(112,20,'galeriaFotos',3,1),(113,20,'fotosProcesar',3,1),(114,20,'formFoto',3,1),(115,20,'galeriaArchivos',3,1),(116,20,'archivosProcesar',3,1),(117,20,'formArchivo',3,1),(118,20,'galeriaVideos',3,1),(119,20,'videosProcesar',3,1),(120,20,'formVideo',3,1),(121,20,'crearPublicacionForm',3,1),(122,20,'modificarPublicacionForm',3,1),(123,20,'crearReviewForm',3,1),(124,20,'modificarReviewForm',3,1),(125,1,'video',5,1),(126,14,'ver',3,1),(127,14,'cambiarEstadoSeguimientos',3,1),(128,14,'verAdjuntos',3,1),(129,14,'editarAntecedentes',3,1),(130,14,'procesarAntecedentes',3,1),(131,14,'formAdjuntarFoto',3,1),(132,14,'formAdjuntarVideo',3,1),(133,14,'formAdjuntarArchivo',3,1),(134,14,'formEditarAdjunto',3,1),(135,14,'procesarAdjunto',3,1),(136,14,'formModificarSeguimiento',3,1),(137,14,'guardarSeguimiento',3,1),(138,20,'verPublicacion',4,1),(139,20,'verReview',4,1),(140,1,'foto',5,1);

/*Table structure for table `acciones_x_perfil` */

DROP TABLE IF EXISTS `acciones_x_perfil`;

CREATE TABLE `acciones_x_perfil` (
  `perfiles_id` int(11) NOT NULL,
  `grupo` tinyint(2) NOT NULL,
  PRIMARY KEY (`perfiles_id`,`grupo`),
  KEY `FK_acciones_x_perfil` (`grupo`),
  CONSTRAINT `FK_acciones_x_perfil_perfiles` FOREIGN KEY (`perfiles_id`) REFERENCES `perfiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `acciones_x_perfil` */

insert  into `acciones_x_perfil`(`perfiles_id`,`grupo`) values (1,1),(1,2),(5,2),(1,3),(2,3),(5,3),(1,4),(2,4),(3,4),(5,4),(1,5),(2,5),(3,5),(4,5),(5,5);

/*Table structure for table `archivos` */

DROP TABLE IF EXISTS `archivos`;

CREATE TABLE `archivos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fichas_abstractas_id` int(11) DEFAULT NULL,
  `seguimientos_id` int(11) DEFAULT NULL,
  `usuarios_id` int(11) DEFAULT NULL,
  `categorias_id` int(11) DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `nombreServidor` varchar(500) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipoMime` varchar(50) NOT NULL,
  `tamanio` int(11) DEFAULT NULL,
  `fechaAlta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `orden` tinyint(4) unsigned DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `tipo` enum('cv','adjunto','antecedentes') NOT NULL DEFAULT 'adjunto',
  `moderado` tinyint(1) unsigned DEFAULT NULL,
  `activo` tinyint(1) unsigned DEFAULT NULL,
  `publico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `activoComentarios` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombreServidor` (`nombreServidor`),
  KEY `FK_archivos_seguimientos` (`seguimientos_id`),
  KEY `FK_archivos_categorias` (`categorias_id`),
  KEY `FK_archivos_usuarios` (`usuarios_id`),
  KEY `FK_archivos_fichas_abstractas` (`fichas_abstractas_id`),
  CONSTRAINT `FK_archivos_categorias` FOREIGN KEY (`categorias_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_archivos_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_archivos_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_archivos_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;

/*Data for the table `archivos` */

insert  into `archivos`(`id`,`fichas_abstractas_id`,`seguimientos_id`,`usuarios_id`,`categorias_id`,`nombre`,`nombreServidor`,`descripcion`,`tipoMime`,`tamanio`,`fechaAlta`,`orden`,`titulo`,`tipo`,`moderado`,`activo`,`publico`,`activoComentarios`) values (45,NULL,NULL,63,NULL,'unArchivoDePrueba.pdf','63_curriculum_1336183420_unArchivoDePrueba.pdf',NULL,'application/pdf',84665,'2012-05-04 23:03:40',1,NULL,'cv',0,1,0,0),(49,NULL,28,NULL,NULL,'Un Nuevo Archivo.pdf','28_seguimiento_1339515986_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 12:46:26',NULL,NULL,'adjunto',0,1,0,0),(50,NULL,28,NULL,NULL,'Un Nuevo Archivo.pdf','28_seguimiento_1339516139_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 12:48:59',NULL,NULL,'adjunto',0,1,0,0),(51,8,NULL,NULL,NULL,'Un Nuevo Archivo.pdf','8_publicacion_1339535582_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 18:13:02',NULL,NULL,'adjunto',0,1,0,0),(52,8,NULL,NULL,NULL,'Un Nuevo Archivo.pdf','8_publicacion_1339535588_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 18:13:08',NULL,NULL,'adjunto',0,1,0,0),(53,8,NULL,NULL,NULL,'Un Nuevo Archivo.pdf','8_publicacion_1339535592_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 18:13:12',NULL,NULL,'adjunto',0,1,0,0),(54,NULL,28,NULL,NULL,'Un Nuevo Archivo.pdf','28_seguimiento_1339535709_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 18:15:09',NULL,NULL,'adjunto',0,1,0,0),(55,NULL,28,NULL,NULL,'Un Nuevo Archivo.pdf','28_seguimiento_1339535712_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 18:15:12',NULL,NULL,'adjunto',0,1,0,0),(56,NULL,28,NULL,NULL,'Un Nuevo Archivo.pdf','28_seguimiento_1339535715_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 18:15:15',NULL,NULL,'adjunto',0,1,0,0),(64,NULL,29,NULL,NULL,'Archivoasd.pdf','63_antecedentes_1339645910_Archivoasd.pdf',NULL,'application/pdf',280317,'2012-06-14 00:51:50',NULL,NULL,'antecedentes',0,1,0,0);

/*Table structure for table `areas` */

DROP TABLE IF EXISTS `areas`;

CREATE TABLE `areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ciclos_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_areas_ciclos` (`ciclos_id`),
  CONSTRAINT `FK_areas_ciclos` FOREIGN KEY (`ciclos_id`) REFERENCES `ciclos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `areas` */

/*Table structure for table `auditorias` */

DROP TABLE IF EXISTS `auditorias`;

CREATE TABLE `auditorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(11) NOT NULL,
  `fechaHora` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `auditorias` */

/*Table structure for table `categorias` */

DROP TABLE IF EXISTS `categorias`;

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `categorias` */

insert  into `categorias`(`id`,`nombre`,`descripcion`) values (1,'categoria 1','probando cat1'),(2,'categoria2','descr 2aaaaa');

/*Table structure for table `ciclos` */

DROP TABLE IF EXISTS `ciclos`;

CREATE TABLE `ciclos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `niveles_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ciclos_niveles` (`niveles_id`),
  CONSTRAINT `FK_ciclos_niveles` FOREIGN KEY (`niveles_id`) REFERENCES `niveles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `ciclos` */

/*Table structure for table `ciudades` */

DROP TABLE IF EXISTS `ciudades`;

CREATE TABLE `ciudades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `provincia_id` int(11) NOT NULL,
  `latitud` int(11) DEFAULT NULL,
  `longitud` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ciudades_provincias` (`provincia_id`),
  CONSTRAINT `FK_ciudades_provincias` FOREIGN KEY (`provincia_id`) REFERENCES `provincias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `ciudades` */

insert  into `ciudades`(`id`,`nombre`,`provincia_id`,`latitud`,`longitud`) values (1,'Mar del Plata',1,NULL,NULL),(2,'Necochea',1,NULL,NULL),(3,'Rio de Janeiro',3,NULL,NULL);

/*Table structure for table `comentarios` */

DROP TABLE IF EXISTS `comentarios`;

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reviews_id` int(11) DEFAULT NULL,
  `publicaciones_id` int(11) DEFAULT NULL,
  `archivos_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descripcion` text NOT NULL,
  `valoracion` double unsigned DEFAULT NULL,
  `usuarios_id` int(11) DEFAULT NULL COMMENT 'En el caso de que un usuario registrado valore se crea la referencia para el vCard',
  `nombreApellido` varchar(100) NOT NULL DEFAULT 'Anonimo',
  PRIMARY KEY (`id`),
  KEY `FK_comentarios_usuarios` (`usuarios_id`),
  KEY `FK_comentarios_archivos` (`archivos_id`),
  KEY `FK_comentarios_publicaciones` (`publicaciones_id`),
  KEY `FK_comentarios_reviews` (`reviews_id`),
  CONSTRAINT `FK_comentarios_archivos` FOREIGN KEY (`archivos_id`) REFERENCES `archivos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_comentarios_publicaciones` FOREIGN KEY (`publicaciones_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_comentarios_reviews` FOREIGN KEY (`reviews_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_comentarios_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Data for the table `comentarios` */

insert  into `comentarios`(`id`,`reviews_id`,`publicaciones_id`,`archivos_id`,`fecha`,`descripcion`,`valoracion`,`usuarios_id`,`nombreApellido`) values (1,NULL,8,NULL,'2011-05-19 07:10:10','asdasdasdasdasd',NULL,63,''),(4,NULL,8,NULL,'2012-06-16 15:06:09','fadsfadsf',0,63,'Anonimo'),(5,NULL,8,NULL,'2012-06-16 15:06:52','sadasdasdas',0,63,'Anonimo'),(6,NULL,8,NULL,'2012-06-16 15:08:05','asdfadsfasd',0,63,'Anonimo'),(7,NULL,8,NULL,'2012-06-16 15:08:56','adsafdsfdsfds',0,63,'Anonimo'),(8,NULL,8,NULL,'2012-06-17 15:59:17','a\na\na\na\na\na\na\na\na\n',0,63,'Anonimo'),(9,3,NULL,NULL,'2012-06-17 20:15:52','Che muy groso muy bueno !!!',0,63,'Anonimo'),(10,NULL,8,NULL,'2012-06-17 20:16:25','lcjzxkcklxjzcklzjdxclkjzcx',0,63,'Anonimo'),(11,NULL,6,NULL,'2012-06-17 20:19:09','adsasdasd',0,63,'Anonimo'),(12,NULL,1,NULL,'2012-06-17 20:20:41','adsalksjdaskld',0,63,'Anonimo');

/*Table structure for table `controladores_pagina` */

DROP TABLE IF EXISTS `controladores_pagina`;

CREATE TABLE `controladores_pagina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controlador` varchar(200) NOT NULL COMMENT 'Formado por [modulo]_[controlador]. ''system'' se utiliza para referencia a TODO el sistema. No debe asociarse a la tabla acciones',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

/*Data for the table `controladores_pagina` */

insert  into `controladores_pagina`(`id`,`controlador`) values (1,'index_index'),(2,'index_login'),(3,'index_registracion'),(4,'admin_index'),(5,'comunidad_index'),(6,'comunidad_invitaciones'),(7,'admin_parametros'),(8,'comunidad_instituciones'),(9,'comunidad_datosPersonales'),(10,'admin_especialidad'),(11,'admin_categoria'),(12,'seguimientos_index'),(13,'seguimientos_personas'),(14,'seguimientos_seguimientos'),(15,'admin_personas'),(16,'admin_instituciones'),(17,'admin_accionesPerfil'),(18,'admin_usuarios'),(20,'comunidad_publicaciones');

/*Table structure for table `diagnosticos` */

DROP TABLE IF EXISTS `diagnosticos`;

CREATE TABLE `diagnosticos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos` */

/*Table structure for table `diagnosticos_personalizado` */

DROP TABLE IF EXISTS `diagnosticos_personalizado`;

CREATE TABLE `diagnosticos_personalizado` (
  `id` int(11) NOT NULL,
  `codigo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_diagnosticos_personalizado_diagnosticos` FOREIGN KEY (`id`) REFERENCES `diagnosticos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos_personalizado` */

/*Table structure for table `diagnosticos_scc` */

DROP TABLE IF EXISTS `diagnosticos_scc`;

CREATE TABLE `diagnosticos_scc` (
  `id` int(11) NOT NULL,
  `areas_id` int(11) NOT NULL COMMENT 'estado inicial',
  PRIMARY KEY (`id`),
  KEY `FK_diagnosticos_scc_areas` (`areas_id`),
  CONSTRAINT `FK_diagnosticos_scc_areas` FOREIGN KEY (`areas_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_diagnosticos_scc_diagnosticos` FOREIGN KEY (`id`) REFERENCES `diagnosticos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos_scc` */

/*Table structure for table `discapacitados` */

DROP TABLE IF EXISTS `discapacitados`;

CREATE TABLE `discapacitados` (
  `id` int(11) NOT NULL,
  `nombreApellidoPadre` varchar(60) DEFAULT NULL,
  `nombreApellidoMadre` varchar(60) DEFAULT NULL,
  `fechaNacimientoPadre` date DEFAULT NULL,
  `fechaNacimientoMadre` date DEFAULT NULL,
  `ocupacionPadre` varchar(500) DEFAULT NULL,
  `ocupacionMadre` varchar(500) DEFAULT NULL,
  `nombreHermanos` varchar(500) DEFAULT NULL,
  `usuarios_id` int(11) unsigned DEFAULT NULL COMMENT 'el user que lo dio de alta en el sistema',
  PRIMARY KEY (`id`),
  UNIQUE KEY `personas_id` (`id`),
  CONSTRAINT `FK_discapacitados_personas` FOREIGN KEY (`id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `discapacitados` */

insert  into `discapacitados`(`id`,`nombreApellidoPadre`,`nombreApellidoMadre`,`fechaNacimientoPadre`,`fechaNacimientoMadre`,`ocupacionPadre`,`ocupacionMadre`,`nombreHermanos`,`usuarios_id`) values (95,'Eduardo Alfredo Velillaa','Evangelina monelloo','2005-06-04','2001-04-16','dsklfjdsfjdsf\nsdfdskljfldskjflskdjflskjdf\nsdflksjdflkdsjflksd\nsdlfksjdflksjdflkjsdddd','dsklfjdsfjdsf\nsdfdskljfldskjflskdjflskjdf\nsdflksjdflkdsjflksd\nsdlfksjdflksjdflkjsddddddd','dsklfjdsfjdsf 233\nsdfdskljfldskjflskdjflskjdf 211\nsdflksjdflkdsjflksd 322\nsdlfksjdflksjdflkjsd 122',61),(108,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(109,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(110,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63);

/*Table structure for table `discapacitados_moderacion` */

DROP TABLE IF EXISTS `discapacitados_moderacion`;

CREATE TABLE `discapacitados_moderacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `documento_tipos_id` int(11) DEFAULT NULL,
  `numeroDocumento` int(8) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `fechaNacimiento` varchar(10) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `celular` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `domicilio` varchar(100) DEFAULT NULL,
  `instituciones_id` int(11) DEFAULT NULL,
  `ciudades_id` int(11) DEFAULT NULL,
  `ciudadOrigen` varchar(150) DEFAULT NULL,
  `codigoPostal` varchar(20) DEFAULT NULL,
  `empresa` varchar(30) DEFAULT NULL,
  `universidad` varchar(30) DEFAULT NULL,
  `secundaria` varchar(30) DEFAULT NULL,
  `nombreApellidoPadre` varchar(60) DEFAULT NULL,
  `nombreApellidoMadre` varchar(60) DEFAULT NULL,
  `fechaNacimientoPadre` date DEFAULT NULL,
  `fechaNacimientoMadre` date DEFAULT NULL,
  `ocupacionPadre` varchar(200) DEFAULT NULL,
  `ocupacionMadre` varchar(200) DEFAULT NULL,
  `nombreHermanos` varchar(200) DEFAULT NULL,
  `usuarios_id` int(11) DEFAULT NULL,
  `nombreBigSize` varchar(255) DEFAULT NULL,
  `nombreMediumSize` varchar(255) DEFAULT NULL,
  `nombreSmallSize` varchar(255) DEFAULT NULL,
  `cambioFoto` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `numeroDocumento` (`numeroDocumento`),
  UNIQUE KEY `numeroDocumento_2` (`numeroDocumento`),
  KEY `FK_personas` (`documento_tipos_id`),
  KEY `FK_personas_institucion` (`instituciones_id`),
  KEY `FK_personas_ciudades` (`ciudades_id`),
  CONSTRAINT `FK_discapacitados_moderacion_personas` FOREIGN KEY (`id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `discapacitados_moderacion` */

/*Table structure for table `documento_tipos` */

DROP TABLE IF EXISTS `documento_tipos`;

CREATE TABLE `documento_tipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `documento_tipos` */

insert  into `documento_tipos`(`id`,`nombre`) values (1,'dni'),(2,'ci'),(3,'lc'),(4,'ld');

/*Table structure for table `embed_videos` */

DROP TABLE IF EXISTS `embed_videos`;

CREATE TABLE `embed_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fichas_abstractas_id` int(11) DEFAULT NULL,
  `seguimientos_id` int(11) DEFAULT NULL,
  `codigo` varchar(500) NOT NULL,
  `orden` tinyint(4) unsigned DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `origen` enum('YouTube','YouTube (Playlists)','Google Video','MetaCafe','Vimeo','Clarin','Flickr','JustinTV','LiveLeak','Yahoo Video') NOT NULL DEFAULT 'YouTube',
  `urlKey` char(64) NOT NULL COMMENT 'para generar la url del link de ampliar video. se utiliza este campo en lugar del id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlKey` (`urlKey`),
  KEY `FK_embed_videos_seguimientos` (`seguimientos_id`),
  KEY `FK_embed_videos_fichas_abstractas` (`fichas_abstractas_id`),
  CONSTRAINT `FK_embed_videos_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_embed_videos_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

/*Data for the table `embed_videos` */

/*Table structure for table `especialidades` */

DROP TABLE IF EXISTS `especialidades`;

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

/*Data for the table `especialidades` */

insert  into `especialidades`(`id`,`nombre`,`descripcion`) values (9,'Profesor','aaaaaa'),(13,'Kinesiologo',NULL),(14,'Terapista ocupacional',NULL),(15,'Educacion especial nivel 1',NULL),(16,'Educacion especial nivel 2',NULL),(17,'Educacion especial nivel 3',NULL),(18,'Educacion especial nivel 4',NULL),(19,'Educacion especial nivel 5',NULL),(20,'Enfermera/o',NULL),(21,'Psicologo pediatrico',NULL),(22,'Psicologo pediatrico',NULL);

/*Table structure for table `fichas_abstractas` */

DROP TABLE IF EXISTS `fichas_abstractas`;

CREATE TABLE `fichas_abstractas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Data for the table `fichas_abstractas` */

insert  into `fichas_abstractas`(`id`,`titulo`,`fecha`,`activo`,`descripcion`) values (1,'Primer Publicacion','2012-05-18 08:18:15',1,'sdfhaskdfjh adskfh asdkfh asdkfh asdkfh asdkfh asd\nasdkjfh askdfh askdjfh askjfh adskjfh asdfkj \n\naksjdfh askdjfh akdshf aksdfh kasdfh aksjdfh kasdfh kajsdhf akdsjfh asd\nfasdkjfh askdfh akdsfh daksfh askdfh aksdfh aksjdfh asd\nfaskfhdasdfk hasdkfh asdkfjh a\n\naksdfh akdsfh akdsjfh akdsjfh aksjdfh askdjfh \naksjdfh akjdsfh aksdh fakdsjfh aksjdfh adsjkf\nkasdfh akdsjfh akjsdfh adfskh \n\nkajsdfh akdsjfh aksjdh fkjdsh fkjasdhf kjdsh fkjsdfh \nakjfh akdsjfh akjdshf akjdfh sd\naksdjfh aksjdfh kasjdfh akjdsh faksjdhf kajdsh'),(3,'nueva feria artesanal en mar del plata','2012-05-19 05:42:20',0,'dfasdfasdf\nfasdfads\nfasdf\nadsfasd\nfasdf\nadsfa\ndsfasdf\nsdfadskfhaskdjfhaklsjdfhas\n'),(5,'sdaf asdfadsfa sdfadsf ','2012-05-30 06:27:59',1,'adsfa dsfadsf\nadsfasdfsdfasdfasdfasd\nfasdfads'),(6,'adsfasdfasd adsfa dsf','2012-05-30 06:28:19',1,'asdfdsfasdfa\ndsfas\ndfa\nsdf\nasdf\nasdf\nads\nf'),(8,'adsfads fasf asdfa dsfasf','2012-05-30 06:29:11',1,'adsfasdf\nads\nfa\nsdf\nasdf\nasd\nf\nasdf\nasd\nfa\nsdf');

/*Table structure for table `fotos` */

DROP TABLE IF EXISTS `fotos`;

CREATE TABLE `fotos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seguimientos_id` int(11) DEFAULT NULL,
  `fichas_abstractas_id` int(11) DEFAULT NULL,
  `personas_id` int(11) DEFAULT NULL,
  `categorias_id` int(11) DEFAULT NULL,
  `nombreBigSize` varchar(255) NOT NULL,
  `nombreMediumSize` varchar(255) NOT NULL,
  `nombreSmallSize` varchar(255) NOT NULL,
  `orden` tinyint(4) unsigned DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `tipo` enum('perfil','adjunto') NOT NULL DEFAULT 'adjunto',
  `urlKey` char(64) NOT NULL COMMENT 'Para generar la url de foto ampliada',
  PRIMARY KEY (`id`),
  UNIQUE KEY `NewIndex1` (`urlKey`),
  KEY `FK_fotos_categorias` (`categorias_id`),
  KEY `FK_fotos_personas` (`personas_id`),
  KEY `FK_fotos_seguimientos` (`seguimientos_id`),
  KEY `FK_fotos_fichas_abstractas` (`fichas_abstractas_id`),
  CONSTRAINT `FK_fotos_categorias` FOREIGN KEY (`categorias_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_fotos_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_fotos_personas` FOREIGN KEY (`personas_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_fotos_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

/*Data for the table `fotos` */

/*Table structure for table `instituciones` */

DROP TABLE IF EXISTS `instituciones`;

CREATE TABLE `instituciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ciudades_id` int(11) DEFAULT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `moderado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `descripcion` varchar(100) DEFAULT NULL,
  `tipoInstitucion_id` int(11) DEFAULT NULL,
  `direccion` varchar(60) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `sitioWeb` varchar(60) DEFAULT NULL,
  `horariosAtencion` varchar(40) DEFAULT NULL,
  `autoridades` varchar(50) DEFAULT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `personeriaJuridica` varchar(50) DEFAULT NULL,
  `sedes` varchar(50) DEFAULT NULL,
  `actividadesMes` text,
  `usuario_id` int(11) DEFAULT NULL,
  `latitud` varchar(12) DEFAULT NULL,
  `longitud` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_instituciones_ciudades` (`ciudades_id`),
  KEY `tipoInstitucion_id` (`tipoInstitucion_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `FK_instituciones_ciudades` FOREIGN KEY (`ciudades_id`) REFERENCES `ciudades` (`id`),
  CONSTRAINT `instituciones_fk_tipos` FOREIGN KEY (`tipoInstitucion_id`) REFERENCES `instituciones_tipos` (`id`),
  CONSTRAINT `instituciones_fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;

/*Data for the table `instituciones` */

insert  into `instituciones`(`id`,`ciudades_id`,`nombre`,`moderado`,`descripcion`,`tipoInstitucion_id`,`direccion`,`email`,`telefono`,`sitioWeb`,`horariosAtencion`,`autoridades`,`cargo`,`personeriaJuridica`,`sedes`,`actividadesMes`,`usuario_id`,`latitud`,`longitud`) values (33,1,'Fasta',0,'Colegio y universidad',1,'Gascon 2332','rio_rodrigo@hotmail.com','123456','www.ufasta.edu.ar','de 8 a 22','roberto','Decano','Rodrigo Rio','Bariloche','Misa los domingos',61,'-37.9940139','-57.5495844'),(49,1,'IAC',0,'Institutu de educacion',1,'Colon 2323','rodrigo.a.rio@gmail.com','123456',NULL,NULL,NULL,'Estudiante',NULL,NULL,NULL,61,'-38.0048712','-57.54728499');

/*Table structure for table `instituciones_tipos` */

DROP TABLE IF EXISTS `instituciones_tipos`;

CREATE TABLE `instituciones_tipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `instituciones_tipos` */

insert  into `instituciones_tipos`(`id`,`nombre`) values (1,'Universidad'),(2,'Hospital');

/*Table structure for table `invitados` */

DROP TABLE IF EXISTS `invitados`;

CREATE TABLE `invitados` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personas_id` (`id`),
  CONSTRAINT `FK_invitados_personas` FOREIGN KEY (`id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `invitados` */

/*Table structure for table `niveles` */

DROP TABLE IF EXISTS `niveles`;

CREATE TABLE `niveles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `niveles` */

/*Table structure for table `objetivo_ejes` */

DROP TABLE IF EXISTS `objetivo_ejes`;

CREATE TABLE `objetivo_ejes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivo_ejes` */

/*Table structure for table `objetivo_relevancias` */

DROP TABLE IF EXISTS `objetivo_relevancias`;

CREATE TABLE `objetivo_relevancias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivo_relevancias` */

/*Table structure for table `objetivos` */

DROP TABLE IF EXISTS `objetivos`;

CREATE TABLE `objetivos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivos` */

/*Table structure for table `objetivos_curriculares` */

DROP TABLE IF EXISTS `objetivos_curriculares`;

CREATE TABLE `objetivos_curriculares` (
  `areas_id` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `objetivos_id` (`id`),
  KEY `FK_objetivos_curriculares_areas` (`areas_id`),
  CONSTRAINT `FK_objetivos_curriculares_areas` FOREIGN KEY (`areas_id`) REFERENCES `areas` (`id`),
  CONSTRAINT `FK_objetivos_curriculares_objetivos` FOREIGN KEY (`id`) REFERENCES `objetivos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivos_curriculares` */

/*Table structure for table `objetivos_personalizados` */

DROP TABLE IF EXISTS `objetivos_personalizados`;

CREATE TABLE `objetivos_personalizados` (
  `id` int(11) NOT NULL,
  `objetivo_ejes_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `objetivos_id` (`id`),
  KEY `FK_objetivos_personalizados_objetivo_ejes` (`objetivo_ejes_id`),
  CONSTRAINT `FK_objetivos_personalizados_objetivos` FOREIGN KEY (`id`) REFERENCES `objetivos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_objetivos_personalizados_objetivo_ejes` FOREIGN KEY (`objetivo_ejes_id`) REFERENCES `objetivo_ejes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivos_personalizados` */

/*Table structure for table `paises` */

DROP TABLE IF EXISTS `paises`;

CREATE TABLE `paises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `codigo` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `paises` */

insert  into `paises`(`id`,`nombre`,`codigo`) values (1,'Argentina','AR'),(2,'Brasil','BR');

/*Table structure for table `parametros` */

DROP TABLE IF EXISTS `parametros`;

CREATE TABLE `parametros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` enum('string','numeric','boolean') NOT NULL DEFAULT 'string',
  `namespace` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametros` */

/*Table structure for table `parametros_x_controladores_pagina` */

DROP TABLE IF EXISTS `parametros_x_controladores_pagina`;

CREATE TABLE `parametros_x_controladores_pagina` (
  `parametros_id` int(11) NOT NULL,
  `controladores_pagina_id` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY (`parametros_id`,`controladores_pagina_id`),
  KEY `FK_parametros_x_controladores_pagina_controladores_pagina` (`controladores_pagina_id`),
  CONSTRAINT `FK_parametros_x_controladores_pagina_controladores_pagina` FOREIGN KEY (`controladores_pagina_id`) REFERENCES `controladores_pagina` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_parametros_x_controladores_pagina_parametros` FOREIGN KEY (`parametros_id`) REFERENCES `parametros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametros_x_controladores_pagina` */

/*Table structure for table `perfiles` */

DROP TABLE IF EXISTS `perfiles`;

CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `perfiles` */

insert  into `perfiles`(`id`,`descripcion`) values (1,'administrador'),(2,'integrante activo'),(3,'integrante inactivo'),(4,'visitante'),(5,'moderador');

/*Table structure for table `personas` */

DROP TABLE IF EXISTS `personas`;

CREATE TABLE `personas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `documento_tipos_id` int(11) DEFAULT NULL,
  `numeroDocumento` int(8) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `fechaNacimiento` varchar(10) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `celular` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `domicilio` varchar(100) DEFAULT NULL,
  `instituciones_id` int(11) DEFAULT NULL,
  `ciudades_id` int(11) DEFAULT NULL,
  `ciudadOrigen` varchar(150) DEFAULT NULL,
  `codigoPostal` varchar(20) DEFAULT NULL,
  `empresa` varchar(30) DEFAULT NULL,
  `universidad` varchar(30) DEFAULT NULL,
  `secundaria` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `numeroDocumento` (`numeroDocumento`),
  KEY `FK_personas` (`documento_tipos_id`),
  KEY `FK_personas_institucion` (`instituciones_id`),
  KEY `FK_personas_ciudades` (`ciudades_id`),
  CONSTRAINT `FK_personas_ciudades` FOREIGN KEY (`ciudades_id`) REFERENCES `ciudades` (`id`),
  CONSTRAINT `FK_personas_documento_tipos` FOREIGN KEY (`documento_tipos_id`) REFERENCES `documento_tipos` (`id`),
  CONSTRAINT `FK_personas_instituciones` FOREIGN KEY (`instituciones_id`) REFERENCES `instituciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=latin1;

/*Data for the table `personas` */

insert  into `personas`(`id`,`nombre`,`apellido`,`documento_tipos_id`,`numeroDocumento`,`sexo`,`fechaNacimiento`,`email`,`telefono`,`celular`,`fax`,`domicilio`,`instituciones_id`,`ciudades_id`,`ciudadOrigen`,`codigoPostal`,`empresa`,`universidad`,`secundaria`) values (61,'Rodrigo','Rio',1,30061066,'m','1983-02-16','rio_rodrigo@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'dddd','ddd','dddd'),(63,'Matias','Velilla',1,31821427,'m','1985-10-06','matiasvelillamdq@gmail.com','4740327','2235820524','317928372','funes 2862 pa',33,1,'Mar del Plata','7600','Urbis','FASTA','EET N3'),(95,'Mirtaa','Gilardi',1,31821426,'m','2006-05-08','','91287319288',NULL,NULL,'sdfhsdkjh 2311',NULL,1,NULL,NULL,NULL,NULL,NULL),(108,'mfacud','sdfdsf',1,23424234,'m','2002-04-14',NULL,'3453453',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL),(109,'Juan','Perez',1,12312312,'f','1998-04-15',NULL,'34324324234',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL),(110,'Carla','Sanchez',1,31323545,'f','1997-04-12',NULL,'123123',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `practicas` */

DROP TABLE IF EXISTS `practicas`;

CREATE TABLE `practicas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `practicas` */

insert  into `practicas`(`id`,`nombre`) values (1,'Grupal'),(2,'Individual'),(3,'Pareja');

/*Table structure for table `privacidad` */

DROP TABLE IF EXISTS `privacidad`;

CREATE TABLE `privacidad` (
  `usuarios_id` int(11) NOT NULL,
  `email` enum('comunidad','publico') DEFAULT 'publico',
  `telefono` enum('comunidad','privado') DEFAULT 'comunidad',
  `celular` enum('comunidad','privado') DEFAULT 'comunidad',
  `fax` enum('comunidad','privado') DEFAULT 'comunidad',
  `curriculum` enum('comunidad','privado') DEFAULT 'comunidad',
  PRIMARY KEY (`usuarios_id`),
  CONSTRAINT `FK_privacidad usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `privacidad` */

insert  into `privacidad`(`usuarios_id`,`email`,`telefono`,`celular`,`fax`,`curriculum`) values (61,'comunidad','privado','comunidad','comunidad','comunidad'),(63,'comunidad','privado','comunidad','comunidad','comunidad');

/*Table structure for table `provincias` */

DROP TABLE IF EXISTS `provincias`;

CREATE TABLE `provincias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `paises_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_provincias` (`paises_id`),
  CONSTRAINT `FK_provincias_paises` FOREIGN KEY (`paises_id`) REFERENCES `paises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `provincias` */

insert  into `provincias`(`id`,`nombre`,`paises_id`) values (1,'Buenos Aires',1),(2,'San Luis',1),(3,'San Pablo',2);

/*Table structure for table `publicaciones` */

DROP TABLE IF EXISTS `publicaciones`;

CREATE TABLE `publicaciones` (
  `id` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  `moderado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `publico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `activoComentarios` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `descripcionBreve` varchar(100) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_publicaciones_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_publicaciones_fichas_abstractas` FOREIGN KEY (`id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_publicaciones_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `publicaciones` */

insert  into `publicaciones`(`id`,`usuarios_id`,`moderado`,`publico`,`activoComentarios`,`descripcionBreve`,`keywords`) values (1,63,0,0,1,'esta es una descripcion tan breve no ?','uno dos tres cuatro cinco cinco seis'),(5,63,0,0,1,'a sdfads fadsfa sdfasd','asdfads asdfadsf asdfsdf'),(6,63,0,0,1,'adfasfadsfasf','dasfsdf sdf sdf sdf sdf'),(8,63,0,0,1,'asdfasdf adsf asdfds fdsf','dd d d d d d');

/*Table structure for table `reviews` */

DROP TABLE IF EXISTS `reviews`;

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL COMMENT 'reviewer. optional. hCard.',
  `moderado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `publico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `descripcionBreve` varchar(100) NOT NULL COMMENT 'Se utiliza tambien en el MetaTag description en vista ampliada.',
  `keywords` varchar(255) NOT NULL COMMENT 'Meta tag keywords en vista ampliada. (en el hreview en la ficha se situa en la parte de tags)',
  `activoComentarios` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `itemType` enum('product','business','event','person','place','website','url') DEFAULT NULL COMMENT 'This optional property provides the type of the item being reviewed',
  `itemName` varchar(255) NOT NULL COMMENT 'ITEM must have at a minimum the name',
  `itemEventSummary` varchar(255) DEFAULT NULL COMMENT 'an event item must have the "summary" subproperty inside the respective hCalendar "vevent"',
  `itemUrl` varchar(500) DEFAULT NULL COMMENT 'should provide at least one URI ("url") for the item',
  `rating` double DEFAULT NULL COMMENT 'The rating is a fixed point integer (one decimal point of precision) from 1.0 to 5.0',
  `fuenteOriginal` varchar(500) DEFAULT NULL COMMENT 'URL de la fuente de donde se extrajo informacion',
  PRIMARY KEY (`id`),
  KEY `FK_reviews_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_reviews_fichas_abstractas` FOREIGN KEY (`id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_reviews_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `reviews` */

insert  into `reviews`(`id`,`usuarios_id`,`moderado`,`publico`,`descripcionBreve`,`keywords`,`activoComentarios`,`itemType`,`itemName`,`itemEventSummary`,`itemUrl`,`rating`,`fuenteOriginal`) values (3,63,0,1,'sdfasdfklasj fhaklsjdfh lakjdsh flakjdsfh akldshf aldskjfh askjldf ','sdf sdf sd sdf sfds ',1,'event','Feria arte Sheraton','Feria artesanal Sheraton Mar del Plata, la 4ta de mar del plata','http://www.ldfkjdsklfj.com',2.5,'http://www.lasdkfjdaslkfj.com');

/*Table structure for table `seguimiento_personalizado_x_objetivo_personalizado` */

DROP TABLE IF EXISTS `seguimiento_personalizado_x_objetivo_personalizado`;

CREATE TABLE `seguimiento_personalizado_x_objetivo_personalizado` (
  `seguimientos_personalizados_id` int(11) NOT NULL,
  `objetivos_personalizados_id` int(11) NOT NULL,
  `evolucion` double DEFAULT NULL,
  `objetivo_relevancias_id` int(11) NOT NULL,
  `estimacion` date DEFAULT NULL,
  PRIMARY KEY (`seguimientos_personalizados_id`,`objetivos_personalizados_id`),
  KEY `FK_seguimiento_objetivos` (`seguimientos_personalizados_id`),
  KEY `FK_seguimiento_x_objetivo_personalizado_objetivo_relevancias` (`objetivo_relevancias_id`),
  KEY `FK_seguimiento_x_objetivo_personalizado_objetivos_personalizados` (`objetivos_personalizados_id`),
  CONSTRAINT `FK_seguimientos_personalizados` FOREIGN KEY (`seguimientos_personalizados_id`) REFERENCES `seguimientos_personalizados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_x_objetivo_personalizado_objetivos_personalizados` FOREIGN KEY (`objetivos_personalizados_id`) REFERENCES `objetivos_personalizados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_x_objetivo_personalizado_objetivo_relevancias` FOREIGN KEY (`objetivo_relevancias_id`) REFERENCES `objetivo_relevancias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_personalizado_x_objetivo_personalizado` */

/*Table structure for table `seguimiento_scc_x_objetivo_curricular` */

DROP TABLE IF EXISTS `seguimiento_scc_x_objetivo_curricular`;

CREATE TABLE `seguimiento_scc_x_objetivo_curricular` (
  `objetivos_curriculares_id` int(11) NOT NULL,
  `seguimientos_scc_id` int(11) NOT NULL,
  `evolucion` double DEFAULT NULL,
  `objetivo_relevancias_id` int(11) NOT NULL,
  `estimacion` date DEFAULT NULL,
  PRIMARY KEY (`objetivos_curriculares_id`,`seguimientos_scc_id`),
  KEY `FK_seguimiento_x_objetivo_curricular_objetivo_relevancias` (`objetivo_relevancias_id`),
  KEY `FK_seguimientos_scc` (`seguimientos_scc_id`),
  CONSTRAINT `FK_seguimientos_scc` FOREIGN KEY (`seguimientos_scc_id`) REFERENCES `seguimientos_scc` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_x_objetivo_curricular_objetivos_curriculares` FOREIGN KEY (`objetivos_curriculares_id`) REFERENCES `objetivos_curriculares` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_x_objetivo_curricular_objetivo_relevancias` FOREIGN KEY (`objetivo_relevancias_id`) REFERENCES `objetivo_relevancias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_scc_x_objetivo_curricular` */

/*Table structure for table `seguimiento_x_contenido_variables` */

DROP TABLE IF EXISTS `seguimiento_x_contenido_variables`;

CREATE TABLE `seguimiento_x_contenido_variables` (
  `seguimiento_id` int(11) NOT NULL,
  `variable_id` int(11) NOT NULL,
  `valor` text,
  `fechaHora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `FK_seguimiento_x_contenido_variables` (`seguimiento_id`),
  KEY `FK_seguimiento_x_contenido_variables2` (`variable_id`),
  CONSTRAINT `FK_seguimiento_x_contenido_variables` FOREIGN KEY (`seguimiento_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_x_contenido_variables2` FOREIGN KEY (`variable_id`) REFERENCES `variables` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_x_contenido_variables` */

/*Table structure for table `seguimiento_x_unidades` */

DROP TABLE IF EXISTS `seguimiento_x_unidades`;

CREATE TABLE `seguimiento_x_unidades` (
  `unidad_id` int(11) NOT NULL,
  `seguimiento_id` int(11) NOT NULL,
  `fechaHora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `NewIndex1` (`unidad_id`,`seguimiento_id`),
  KEY `FK_seguimiento_x_unidades2` (`seguimiento_id`),
  CONSTRAINT `FK_seguimiento_x_unidades` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`),
  CONSTRAINT `FK_seguimiento_x_unidades2` FOREIGN KEY (`seguimiento_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_x_unidades` */

insert  into `seguimiento_x_unidades`(`unidad_id`,`seguimiento_id`,`fechaHora`) values (1,23,'2012-06-09 11:33:09'),(1,24,'2012-06-09 11:33:34'),(1,26,'2012-06-09 11:35:57'),(1,27,'2012-06-11 01:28:34');

/*Table structure for table `seguimientos` */

DROP TABLE IF EXISTS `seguimientos`;

CREATE TABLE `seguimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discapacitados_id` int(11) NOT NULL,
  `frecuenciaEncuentros` varchar(100) DEFAULT NULL,
  `diaHorario` varchar(100) DEFAULT NULL,
  `practicas_id` int(11) DEFAULT NULL,
  `usuarios_id` int(11) NOT NULL,
  `antecedentes` text,
  `pronostico` text,
  `fechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('activo','detenido') NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id`),
  KEY `FK_seguimientos_personas` (`discapacitados_id`),
  KEY `FK_seguimientos` (`usuarios_id`),
  KEY `FK_seguimientos_practica` (`practicas_id`),
  CONSTRAINT `FK_seguimientos_discapacitados` FOREIGN KEY (`discapacitados_id`) REFERENCES `discapacitados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimientos_practicas` FOREIGN KEY (`practicas_id`) REFERENCES `practicas` (`id`),
  CONSTRAINT `FK_seguimientos_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos` */

insert  into `seguimientos`(`id`,`discapacitados_id`,`frecuenciaEncuentros`,`diaHorario`,`practicas_id`,`usuarios_id`,`antecedentes`,`pronostico`,`fechaCreacion`,`estado`) values (20,95,'asdasdasdasd','asdasdasdasdas',2,63,NULL,NULL,'2012-06-09 11:05:05','activo'),(22,108,'dfasdfs','adsfasdf',2,63,NULL,NULL,'2012-06-09 11:32:49','activo'),(23,108,'adsa','asdasd',3,63,NULL,NULL,'2012-06-09 11:33:09','activo'),(24,95,'asfas','asfaf',2,63,NULL,NULL,'2012-06-09 11:33:34','detenido'),(26,109,'sdf','dsf',2,63,NULL,NULL,'2012-06-09 11:35:57','activo'),(27,110,'2 veces x semana',NULL,2,63,'fgdgdfgdf',NULL,'2012-06-11 01:28:34','activo'),(28,110,'asdlkaj sdlaksj dklasj d','asdkash dkjh asdkjs ',1,63,NULL,NULL,'2012-06-11 02:21:11','activo'),(29,109,NULL,NULL,1,63,'asdfkljasdfkjhasd\nfadsfjhaskdjfads\n\n\nfasdjfhaskjdfhkajdfshkjadfsh\n',NULL,'2012-06-12 20:28:11','activo');

/*Table structure for table `seguimientos_personalizados` */

DROP TABLE IF EXISTS `seguimientos_personalizados`;

CREATE TABLE `seguimientos_personalizados` (
  `id` int(11) NOT NULL,
  `diagnostico_personalizado_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_seguimientos_personalizados_diagnosticos_personalizados` (`diagnostico_personalizado_id`),
  CONSTRAINT `FK_seguimientos_personalizados_diagnosticos_personalizados` FOREIGN KEY (`diagnostico_personalizado_id`) REFERENCES `diagnosticos_personalizado` (`id`),
  CONSTRAINT `FK_seguimientos_personalizados_seguimientos` FOREIGN KEY (`id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos_personalizados` */

insert  into `seguimientos_personalizados`(`id`,`diagnostico_personalizado_id`) values (23,NULL),(24,NULL),(26,NULL),(27,NULL);

/*Table structure for table `seguimientos_scc` */

DROP TABLE IF EXISTS `seguimientos_scc`;

CREATE TABLE `seguimientos_scc` (
  `id` int(11) NOT NULL,
  `diagnostico_scc_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_seguimientos_scc_diagnosticos_scc` (`diagnostico_scc_id`),
  CONSTRAINT `FK_seguimientos_scc_diagnosticos_scc` FOREIGN KEY (`diagnostico_scc_id`) REFERENCES `diagnosticos_scc` (`id`),
  CONSTRAINT `FK_seguimientos_scc_seguimientos` FOREIGN KEY (`id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos_scc` */

insert  into `seguimientos_scc`(`id`,`diagnostico_scc_id`) values (20,NULL),(22,NULL),(28,NULL),(29,NULL);

/*Table structure for table `tratamientos` */

DROP TABLE IF EXISTS `tratamientos`;

CREATE TABLE `tratamientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fechaHorario` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `tratamientos` */

/*Table structure for table `unidades` */

DROP TABLE IF EXISTS `unidades`;

CREATE TABLE `unidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `editable` tinyint(1) NOT NULL DEFAULT '0',
  `fechaHora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `porDefecto` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `unidades` */

insert  into `unidades`(`id`,`nombre`,`descripcion`,`editable`,`fechaHora`,`porDefecto`) values (1,'unidad por defecto','Variables de texto asignadas al seguimiento al crearse.',0,NULL,1);

/*Table structure for table `usuario_x_invitado` */

DROP TABLE IF EXISTS `usuario_x_invitado`;

CREATE TABLE `usuario_x_invitado` (
  `usuarios_id` int(11) NOT NULL,
  `invitados_id` int(11) NOT NULL,
  `relacion` varchar(255) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('aceptada','pendiente') DEFAULT 'pendiente',
  `token` varchar(200) NOT NULL,
  PRIMARY KEY (`usuarios_id`,`invitados_id`),
  UNIQUE KEY `token` (`token`),
  KEY `FK_usuario_x_invitado_invitados` (`invitados_id`),
  CONSTRAINT `FK_usuario_x_invitado_invitados` FOREIGN KEY (`invitados_id`) REFERENCES `invitados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_usuario_x_invitado_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `usuario_x_invitado` */

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `sitioWeb` varchar(50) DEFAULT NULL,
  `especialidades_id` int(11) DEFAULT NULL,
  `perfiles_id` int(11) NOT NULL,
  `cargoInstitucion` varchar(40) DEFAULT NULL,
  `biografia` text,
  `nombre` varchar(255) NOT NULL,
  `contrasenia` char(64) DEFAULT NULL,
  `fechaAlta` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'si 0 entonces esta suspendido',
  `invitacionesDisponibles` int(3) DEFAULT '5',
  `universidadCarrera` varchar(50) DEFAULT NULL,
  `carreraFinalizada` tinyint(1) DEFAULT NULL,
  `moderado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `personas_id` (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `FK_usuarios` (`perfiles_id`),
  KEY `FK_usuarios_especialidades` (`especialidades_id`),
  CONSTRAINT `FK_usuarios_especialidades` FOREIGN KEY (`especialidades_id`) REFERENCES `especialidades` (`id`),
  CONSTRAINT `FK_usuarios_perfiles` FOREIGN KEY (`perfiles_id`) REFERENCES `perfiles` (`id`),
  CONSTRAINT `FK_usuarios_personas` FOREIGN KEY (`id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`sitioWeb`,`especialidades_id`,`perfiles_id`,`cargoInstitucion`,`biografia`,`nombre`,`contrasenia`,`fechaAlta`,`activo`,`invitacionesDisponibles`,`universidadCarrera`,`carreraFinalizada`,`moderado`) values (61,NULL,NULL,5,'jefe','dasasd','rrio','51c50f52501cfc75dc1110dde6700aee','2011-06-28 02:14:43',1,4,'dddd',0,0),(63,'http://www.facebook.com',14,1,'Director','sdfsdfdsfsd\nfsd\nfsd\nfsd\nfds\nfsdfdsfsdfsdfdsfsdfds','matias.velilla','e10adc3949ba59abbe56e057f20f883e','2011-09-05 20:18:35',1,5,'Lic en Sistemas',0,0);

/*Table structure for table `usuarios_datos_temp` */

DROP TABLE IF EXISTS `usuarios_datos_temp`;

CREATE TABLE `usuarios_datos_temp` (
  `id` int(11) NOT NULL,
  `contraseniaNueva` varchar(64) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `toker` (`token`),
  UNIQUE KEY `token` (`token`),
  KEY `id` (`id`),
  CONSTRAINT `usuarios_datos_temp_fk` FOREIGN KEY (`id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `usuarios_datos_temp` */

insert  into `usuarios_datos_temp`(`id`,`contraseniaNueva`,`token`,`fecha`) values (61,'4e83da81564f98d026b01f51c63fcea4','1679bd62526a4274d289fbec09aa2a8f','2011-07-21 00:00:00'),(61,'41fa4b82608bafebc44c463fb6952d11','93c5ca80e5ddf4544c2e761d4ffeca53','2011-07-15 00:00:00');

/*Table structure for table `variables` */

DROP TABLE IF EXISTS `variables`;

CREATE TABLE `variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo` int(2) NOT NULL COMMENT '0:string,1:int,2:boolean',
  `descripcion` text,
  `unidad_id` int(11) NOT NULL,
  `fechaHora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_variables` (`unidad_id`),
  CONSTRAINT `FK_variables` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `variables` */

insert  into `variables`(`id`,`nombre`,`tipo`,`descripcion`,`unidad_id`,`fechaHora`) values (1,'Texto descripcion',0,NULL,1,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
