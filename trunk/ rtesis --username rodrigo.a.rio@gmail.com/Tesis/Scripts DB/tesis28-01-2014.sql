/*
SQLyog Enterprise - MySQL GUI v8.18 
MySQL - 5.0.51b-community-nt : Database - tesis
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tesis` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `tesis`;

/*Table structure for table `acciones` */

DROP TABLE IF EXISTS `acciones`;

CREATE TABLE `acciones` (
  `id` int(11) NOT NULL auto_increment,
  `controladores_pagina_id` int(11) NOT NULL,
  `accion` varchar(200) default NULL,
  `grupo` tinyint(2) NOT NULL default '1' COMMENT 'No hay una correspondencia con el id del perfil, hay 5 grupos porque hay 5 perfiles pero podria haber mas. por defecto solo admin. los grupos pueden ser: 1)ADMIN 2)MODERADOR 3)INTEGANTE ACTIVO 4)INTEGANTE INACTIVO 5)VISITANTES',
  `activo` tinyint(1) NOT NULL default '0' COMMENT 'por defecto desactivada',
  PRIMARY KEY  (`id`),
  KEY `FK_acciones_controladores_pagina` (`controladores_pagina_id`),
  KEY `grupo` (`grupo`),
  CONSTRAINT `FK_acciones_controladores_pagina` FOREIGN KEY (`controladores_pagina_id`) REFERENCES `controladores_pagina` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=latin1;

/*Data for the table `acciones` */

insert  into `acciones`(`id`,`controladores_pagina_id`,`accion`,`grupo`,`activo`) values (1,1,'index',5,1),(2,1,'redireccion404',5,1),(3,1,'sitioOffline',5,1),(4,1,'sitioEnConstruccion',5,1),(5,1,'ajaxError',5,1),(6,2,'index',5,1),(7,2,'procesar',5,1),(8,2,'redireccion404',5,1),(9,3,'index',5,1),(10,3,'formulario',5,1),(11,3,'procesar',5,1),(12,3,'redireccion404',5,1),(13,4,'index',2,1),(14,4,'redireccion404',2,1),(15,5,'index',4,1),(16,5,'redireccion404',4,1),(17,6,'index',3,1),(18,6,'redireccion404',3,1),(19,6,'procesar',3,1),(20,6,'formulario',3,1),(21,6,'listado',3,1),(22,7,'index',1,1),(23,7,'redireccion404',1,1),(26,8,'index',5,1),(27,8,'nuevaInstitucion',3,1),(28,8,'listadoInstituciones',5,1),(31,8,'procesar',3,1),(32,9,'index',4,1),(33,9,'formulario',4,1),(34,9,'procesar',4,1),(35,9,'redireccion404',4,1),(36,8,'masInstituciones',4,1),(37,8,'redireccion404',5,1),(38,8,'ampliarInstitucion',4,1),(39,8,'editarInstitucion',3,1),(40,2,'logout',4,1),(41,10,'procesarEspecialidad',1,1),(42,10,'index',1,1),(43,10,'listarEspecialidades',1,1),(44,10,'nuevaEspecialidad',1,1),(45,10,'editarEspecialidad',1,1),(46,10,'eliminarEspecialidad',1,1),(47,10,'verificarUsoDeEspecialidad',1,1),(49,11,'nuevaCategoria',1,1),(50,11,'editarCategoria',1,1),(51,11,'listarCategoria',1,1),(52,11,'eliminarCategoria',1,1),(53,11,'index',1,1),(54,11,'procesarCategoria',1,1),(55,9,'modificarPrivacidadCampo',4,1),(56,12,'index',3,1),(59,14,'nuevoSeguimiento',3,1),(62,12,'buscarDiscapacitados',3,1),(63,14,'procesarSeguimiento',3,1),(64,8,'buscarInstituciones',4,1),(65,5,'descargarArchivo',4,1),(67,14,'index',3,1),(68,14,'redireccion404',3,1),(69,12,'redireccion404',3,1),(70,13,'index',3,1),(71,13,'procesar',3,1),(73,13,'agregar',3,1),(74,13,'redireccion404',3,1),(75,14,'listar',3,1),(76,14,'buscarSeguimientos',3,1),(77,13,'modificar',3,1),(78,13,'ver',3,1),(79,15,'index',2,1),(80,15,'redireccion404',2,1),(81,15,'listarModeracionesPendientes',2,1),(82,15,'procesarModeracion',2,1),(83,15,'procesarPersona',2,1),(84,14,'eliminar',3,1),(85,16,'index',2,1),(86,16,'redireccion404',2,1),(87,16,'procesar',2,1),(88,17,'redireccion404',2,1),(89,17,'index',2,1),(90,17,'procesar',2,1),(91,17,'form',2,1),(92,18,'index',2,1),(93,18,'redireccion404',2,1),(94,18,'procesar',2,1),(95,18,'form',2,1),(96,18,'cambiarPerfil',1,1),(97,18,'cerrarCuenta',1,1),(98,18,'crear',1,1),(99,18,'vistaImpresion',1,1),(101,18,'exportar',1,1),(103,9,'cerrarCuenta',4,1),(104,20,'index',4,1),(105,20,'redireccion404',4,1),(106,20,'misPublicaciones',3,1),(109,20,'guardarPublicacion',3,1),(110,20,'guardarReview',3,1),(111,20,'procesar',3,1),(112,20,'galeriaFotos',3,1),(113,20,'fotosProcesar',3,1),(114,20,'formFoto',3,1),(115,20,'galeriaArchivos',3,1),(116,20,'archivosProcesar',3,1),(117,20,'formArchivo',3,1),(118,20,'galeriaVideos',3,1),(119,20,'videosProcesar',3,1),(120,20,'formVideo',3,1),(121,20,'crearPublicacionForm',3,1),(122,20,'modificarPublicacionForm',3,1),(123,20,'crearReviewForm',3,1),(124,20,'modificarReviewForm',3,1),(125,1,'video',5,1),(126,14,'ver',3,1),(127,14,'cambiarEstadoSeguimientos',3,1),(128,14,'verAdjuntos',3,1),(129,14,'editarAntecedentes',3,1),(130,14,'procesarAntecedentes',3,1),(131,14,'formAdjuntarFoto',3,1),(132,14,'formAdjuntarVideo',3,1),(133,14,'formAdjuntarArchivo',3,1),(134,14,'formEditarAdjunto',3,1),(135,14,'procesarAdjunto',3,1),(136,14,'formModificarSeguimiento',3,1),(137,14,'guardarSeguimiento',3,1),(138,20,'verPublicacion',4,1),(139,20,'verReview',4,1),(141,21,'index',1,1),(142,21,'procesar',1,1),(143,21,'form',1,1),(144,21,'listarModeraciones',2,1),(145,8,'misInstituciones',3,1),(146,1,'provinciasByPais',5,1),(147,1,'ciudadesByProvincia',5,1),(148,8,'guardar',3,1),(149,8,'masMisInstituciones',3,1),(150,16,'listarModeraciones',2,1),(151,16,'form',2,1),(152,16,'listarSolicitudes',2,1),(153,11,'verificarUsoDeCategoria',1,1),(154,22,'index',1,1),(155,22,'procesar',1,1),(156,22,'form',1,1),(157,22,'listarModeraciones',2,1),(158,23,'index',4,1),(159,23,'misAplicaciones',3,1),(160,23,'crearSoftwareForm',3,1),(161,23,'modificarSoftwareForm',3,1),(162,23,'guardarSoftware',3,1),(163,23,'procesar',3,1),(164,23,'galeriaFotos',3,1),(165,23,'fotosProcesar',3,1),(166,23,'formFoto',3,1),(167,23,'galeriaArchivos',3,1),(168,23,'archivosProcesar',3,1),(169,23,'formArchivo',3,1),(170,23,'verSoftware',4,1),(171,23,'listarCategoria',4,1),(172,23,'redireccion404',4,1),(173,24,'index',5,1),(174,24,'ampliarInstitucion',5,1),(175,24,'procesar',5,1),(176,25,'index',5,1),(177,25,'verPublicacion',5,1),(178,25,'verReview',5,1),(179,25,'procesar',5,1),(180,7,'procesar',1,1),(181,7,'form',1,1),(182,26,'index',5,1),(183,26,'listarCategoria',5,1),(184,26,'verSoftware',5,1),(185,26,'procesar',5,1),(188,14,'procesarDiagnostico',3,1),(189,14,'editarDiagnostico',3,1),(190,20,'denunciar',4,1),(191,8,'denunciar',4,1),(192,23,'denunciar',4,1),(193,16,'listarDenuncias',2,1),(194,16,'procesarDenuncias',2,1),(195,21,'procesarDenuncias',2,1),(196,21,'listarDenuncias',2,1),(197,22,'listarDenuncias',2,1),(198,22,'procesarDenuncias',2,1),(199,1,'desactivarNotificacionesMail',5,1),(200,7,'listarParametrosUsuario',1,1),(201,2,'formRecuperarContrasenia',5,1),(202,2,'procesarRecuperarContrasenia',5,1),(203,27,'procesarNivel',1,1),(204,27,'listarNiveles',1,1),(205,27,'formularioNivel',1,1),(206,27,'procesarCiclo',1,1),(207,27,'listarCiclos',1,1),(208,27,'formularioCiclo',1,1),(209,27,'procesarArea',1,1),(210,27,'listarAreas',1,1),(211,27,'formularioArea',1,1),(212,27,'procesarEje',1,1),(213,27,'listarEjes',1,1),(214,27,'formularioEje',1,1),(215,27,'procesarObjetivoAprendizaje',1,1),(216,27,'listarObjetivosAprendizaje',1,1),(217,27,'formularioObjetivoAprendizaje',1,1),(219,28,'index',3,1),(220,28,'formCrearUnidad',3,1),(221,28,'guardarUnidad',3,1),(222,28,'procesar',3,1),(223,28,'formEditarUnidad',3,1),(224,28,'eliminar',3,1),(225,29,'index',3,1),(226,29,'formCrearVariable',3,1),(227,29,'formEditarVariable',3,1),(228,29,'procesar',3,1),(229,29,'guardar',3,1),(230,29,'eliminar',3,1),(231,29,'eliminarModalidad',3,1),(232,14,'administrarObjetivos',3,1),(233,30,'index',3,1),(234,14,'procesar',3,1),(235,14,'procesarObjetivos',3,1),(236,14,'formObjetivo',3,1),(237,14,'guardarObjetivo',3,1),(239,14,'verObjetivo',3,1),(240,14,'editarPronostico',3,1),(241,14,'procesarPronostico',3,1),(242,30,'procesar',3,1),(243,30,'ampliar',3,1),(244,30,'crear',3,1),(245,30,'eliminar',3,1),(246,30,'editar',3,1),(247,30,'guardar',3,1),(248,28,'listarUnidadesPorSeguimiento',3,1),(249,28,'unidadesPorSeguimientoProcesar',3,1),(250,28,'ampliarEsporadica',3,1),(251,30,'entradasUnidadEsporadica',3,1),(252,31,'index',2,1),(253,31,'formCrearUnidad',1,1),(254,31,'formEditarUnidad',1,1),(255,31,'guardarUnidad',1,1),(256,31,'procesar',1,1),(257,31,'eliminar',1,1),(258,32,'index',2,1),(259,32,'formCrearVariable',1,1),(260,32,'formEditarVariable',1,1),(261,32,'procesar',1,1),(262,32,'guardar',1,1),(263,32,'eliminar',1,1),(264,32,'eliminarModalidad',1,1),(265,27,'procesarAnio',1,1),(266,27,'listarAnios',1,1),(267,27,'formularioAnio',1,1),(268,14,'listarCiclosPorNivel',3,1),(269,14,'listarAniosPorCiclo',3,1),(270,14,'listarAreasPorAnio',3,1),(271,14,'listarEjesPorArea',3,1),(272,14,'listarObjetivosAprendizajePorEje',3,1);

/*Table structure for table `acciones_x_perfil` */

DROP TABLE IF EXISTS `acciones_x_perfil`;

CREATE TABLE `acciones_x_perfil` (
  `perfiles_id` int(11) NOT NULL,
  `grupo` tinyint(2) NOT NULL,
  PRIMARY KEY  (`perfiles_id`,`grupo`),
  KEY `FK_acciones_x_perfil` (`grupo`),
  CONSTRAINT `FK_acciones_x_perfil_perfiles` FOREIGN KEY (`perfiles_id`) REFERENCES `perfiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `acciones_x_perfil` */

insert  into `acciones_x_perfil`(`perfiles_id`,`grupo`) values (1,1),(1,2),(5,2),(1,3),(2,3),(5,3),(1,4),(2,4),(3,4),(5,4),(1,5),(2,5),(3,5),(4,5),(5,5);

/*Table structure for table `anios` */

DROP TABLE IF EXISTS `anios`;

CREATE TABLE `anios` (
  `id` int(11) NOT NULL auto_increment,
  `ciclos_id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_anios_ciclos` (`ciclos_id`),
  CONSTRAINT `FK_anios_ciclos` FOREIGN KEY (`ciclos_id`) REFERENCES `ciclos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `anios` */

insert  into `anios`(`id`,`ciclos_id`,`descripcion`) values (2,1,'Segundo Año'),(3,1,'Primer Año'),(4,1,'Tercer Año'),(5,1,'Único');

/*Table structure for table `archivos` */

DROP TABLE IF EXISTS `archivos`;

CREATE TABLE `archivos` (
  `id` int(11) NOT NULL auto_increment,
  `fichas_abstractas_id` int(11) default NULL,
  `seguimientos_id` int(11) default NULL,
  `usuarios_id` int(11) default NULL,
  `nombre` varchar(255) NOT NULL,
  `nombreServidor` varchar(500) NOT NULL,
  `descripcion` varchar(255) default NULL,
  `tipoMime` varchar(50) NOT NULL,
  `tamanio` int(11) default NULL,
  `fechaAlta` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `orden` tinyint(4) unsigned default NULL,
  `titulo` varchar(100) default NULL,
  `tipo` enum('cv','adjunto','antecedentes') NOT NULL default 'adjunto',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombreServidor` (`nombreServidor`),
  KEY `FK_archivos_seguimientos` (`seguimientos_id`),
  KEY `FK_archivos_usuarios` (`usuarios_id`),
  KEY `FK_archivos_fichas_abstractas` (`fichas_abstractas_id`),
  CONSTRAINT `FK_archivos_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_archivos_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_archivos_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `archivos` */

/*Table structure for table `areas` */

DROP TABLE IF EXISTS `areas`;

CREATE TABLE `areas` (
  `id` int(11) NOT NULL auto_increment,
  `anios_id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_areas_ciclos` (`anios_id`),
  CONSTRAINT `FK_areas_anios` FOREIGN KEY (`anios_id`) REFERENCES `anios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

/*Data for the table `areas` */

insert  into `areas`(`id`,`anios_id`,`descripcion`) values (6,3,'Matemática'),(7,2,'Matemática'),(8,4,'Matemática'),(9,5,'Pácticas del lenguaje'),(10,3,'Ciencias Sociales'),(11,3,'Ciencias Naturales'),(12,2,'Ciencias Sociales'),(13,4,'Ciencias Sociales'),(14,2,'Ciencias Naturales'),(15,4,'Ciencias Naturales');

/*Table structure for table `auditorias` */

DROP TABLE IF EXISTS `auditorias`;

CREATE TABLE `auditorias` (
  `id` int(11) NOT NULL auto_increment,
  `usuarios_id` int(11) NOT NULL,
  `fechaHora` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `auditorias` */

/*Table structure for table `categorias` */

DROP TABLE IF EXISTS `categorias`;

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) default NULL,
  `descripcion` text,
  `urlToken` char(50) default NULL COMMENT 'es lo que va a parar a la url. tiene indice porque se realizan busquedas por este campo',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `urlToken` (`urlToken`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `categorias` */

insert  into `categorias`(`id`,`nombre`,`descripcion`,`urlToken`) values (1,'Discapacidad Visual','descripcion discapacidad visual, descripcion discapacidad visual descripcion discapacidad visual descripcion discapacidad visual descripcion discapacidad visual','discapacidad-visual'),(2,'Discapacidad Auditiva','descripcion discapacidad auditiva categoria\ndescripcion discapacidad auditiva categoria\ndescripcion discapacidad auditiva categoria','discapacidad-auditiva'),(3,'Discapacidad Motora','descripcion categoria discapacidad motora','discapacidad-motora'),(4,'Autismo','descripcion categoria autismo','autismo'),(5,'Sindrome de Down','descripcion categoria sindrome de down\ndescripcion categoria sindrome de down\ndescripcion categoria sindrome de down\ndescripcion categoria sindrome de down','sindrome-de-down');

/*Table structure for table `ciclos` */

DROP TABLE IF EXISTS `ciclos`;

CREATE TABLE `ciclos` (
  `id` int(11) NOT NULL auto_increment,
  `niveles_id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_ciclos_niveles` (`niveles_id`),
  CONSTRAINT `FK_ciclos_niveles` FOREIGN KEY (`niveles_id`) REFERENCES `niveles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `ciclos` */

insert  into `ciclos`(`id`,`niveles_id`,`descripcion`) values (1,1,'Primer Ciclo'),(4,1,'Segundo Ciclo');

/*Table structure for table `ciudades` */

DROP TABLE IF EXISTS `ciudades`;

CREATE TABLE `ciudades` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) default NULL,
  `provincia_id` int(11) NOT NULL,
  `latitud` int(11) default NULL,
  `longitud` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_ciudades_provincias` (`provincia_id`),
  CONSTRAINT `FK_ciudades_provincias` FOREIGN KEY (`provincia_id`) REFERENCES `provincias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `ciudades` */

insert  into `ciudades`(`id`,`nombre`,`provincia_id`,`latitud`,`longitud`) values (1,'Mar del Plata',1,NULL,NULL),(2,'Necochea',1,NULL,NULL),(3,'Río de Janeiro',3,NULL,NULL);

/*Table structure for table `comentarios` */

DROP TABLE IF EXISTS `comentarios`;

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL auto_increment,
  `reviews_id` int(11) default NULL,
  `publicaciones_id` int(11) default NULL,
  `software_id` int(11) default NULL,
  `fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `descripcion` text NOT NULL,
  `valoracion` double unsigned default '0' COMMENT '-1 quiere decir que no se emitio valoracion',
  `usuarios_id` int(11) default NULL COMMENT 'En el caso de que un usuario registrado valore se crea la referencia para el vCard',
  `nombreApellido` varchar(100) NOT NULL default 'Anonimo',
  PRIMARY KEY  (`id`),
  KEY `FK_comentarios_usuarios` (`usuarios_id`),
  KEY `FK_comentarios_archivos` (`software_id`),
  KEY `FK_comentarios_publicaciones` (`publicaciones_id`),
  KEY `FK_comentarios_reviews` (`reviews_id`),
  CONSTRAINT `FK_comentarios_publicaciones` FOREIGN KEY (`publicaciones_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_comentarios_reviews` FOREIGN KEY (`reviews_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_comentarios_software` FOREIGN KEY (`software_id`) REFERENCES `software` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_comentarios_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

/*Data for the table `comentarios` */

insert  into `comentarios`(`id`,`reviews_id`,`publicaciones_id`,`software_id`,`fecha`,`descripcion`,`valoracion`,`usuarios_id`,`nombreApellido`) values (3,NULL,NULL,12,'2012-08-15 02:29:01','dfasdfdsfasdfadsf\nasdfads\nfasdfasdfasdfadsf\nasdfasdfadsf',4,63,'Anonimo'),(5,NULL,NULL,12,'2012-08-15 02:33:23','sdfadsfasdfasdfasdfadsf',0,63,'Anonimo'),(6,NULL,NULL,12,'2012-08-16 17:27:18','dfadfsadsfasdfsdf',2,63,'Anonimo'),(7,NULL,NULL,12,'2012-08-16 17:27:34','fasdfasdfasdfadsfasdf',1,63,'Anonimo'),(8,NULL,NULL,14,'2012-08-16 21:12:58','fdasdfadsfdsafadsf',1,63,'Anonimo'),(9,NULL,NULL,14,'2012-08-16 21:15:43','asdfasdfadsfadsf\ndasfads',5,63,'Anonimo'),(10,NULL,NULL,14,'2012-08-16 21:17:03','afsdfadsfads',1,63,'Anonimo'),(11,NULL,NULL,14,'2012-08-16 21:17:45','sadDSS',5,63,'Anonimo'),(12,NULL,NULL,12,'2012-08-16 21:23:09','dafsdfasdf',3,63,'Anonimo'),(13,NULL,8,NULL,'2012-08-27 06:16:07','lkjlkjlkjkl',0,63,'Anonimo'),(14,NULL,11,NULL,'2013-11-04 01:44:42','kjh klj hkj hkj',0,63,'Anonimo'),(15,NULL,11,NULL,'2013-11-04 01:44:51','ñkjlkj lj lkj lh',0,63,'Anonimo');

/*Table structure for table `controladores_pagina` */

DROP TABLE IF EXISTS `controladores_pagina`;

CREATE TABLE `controladores_pagina` (
  `id` int(11) NOT NULL auto_increment,
  `controlador` varchar(200) NOT NULL COMMENT 'Formado por [modulo]_[controlador]. ''system'' se utiliza para referencia a TODO el sistema. No debe asociarse a la tabla acciones',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `controlador` (`controlador`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

/*Data for the table `controladores_pagina` */

insert  into `controladores_pagina`(`id`,`controlador`) values (17,'admin_accionesPerfil'),(11,'admin_categoria'),(10,'admin_especialidad'),(4,'admin_index'),(16,'admin_instituciones'),(27,'admin_objetivosAprendizaje'),(7,'admin_parametros'),(15,'admin_personas'),(21,'admin_publicaciones'),(22,'admin_software'),(31,'admin_unidades'),(18,'admin_usuarios'),(32,'admin_variables'),(9,'comunidad_datosPersonales'),(5,'comunidad_index'),(8,'comunidad_instituciones'),(6,'comunidad_invitaciones'),(20,'comunidad_publicaciones'),(23,'comunidad_software'),(1,'index_index'),(24,'index_instituciones'),(2,'index_login'),(25,'index_publicaciones'),(3,'index_registracion'),(26,'index_software'),(30,'seguimientos_entradas'),(12,'seguimientos_index'),(13,'seguimientos_personas'),(14,'seguimientos_seguimientos'),(28,'seguimientos_unidades'),(29,'seguimientos_variables');

/*Table structure for table `denuncias` */

DROP TABLE IF EXISTS `denuncias`;

CREATE TABLE `denuncias` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `fichas_abstractas_id` int(11) default NULL,
  `instituciones_id` int(11) default NULL,
  `mensaje` varchar(500) default NULL,
  `usuarios_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `razon` enum('informacion_falsa','contenido_inapropiado','propiedad_intelectual','spam') default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_denuncias_fichas_abstractas` (`fichas_abstractas_id`),
  KEY `FK_denuncias_instituciones` (`instituciones_id`),
  KEY `FK_denuncias_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_denuncias_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_denuncias_instituciones` FOREIGN KEY (`instituciones_id`) REFERENCES `instituciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_denuncias_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

/*Data for the table `denuncias` */

insert  into `denuncias`(`id`,`fichas_abstractas_id`,`instituciones_id`,`mensaje`,`usuarios_id`,`fecha`,`razon`) values (4,NULL,59,'info basura !!!',63,'2012-09-07 05:21:50','spam'),(5,21,NULL,'se zarparon locooo',63,'2012-09-07 06:03:06','contenido_inapropiado'),(7,NULL,59,'asdas',61,'2012-09-07 19:30:42','propiedad_intelectual'),(9,NULL,57,'asdfasdfadsfd asjdfh kdasjfh dskahf adsklfh adskljfh ladskfh lakdshf alskdfh lkadsfh kladshf ladksfh klasdhf kladshf kldsh fkdjshf ksdhf kdshf kjdsfh kdshf kjdshf kdsjhf kdsfh kdsfh kdsh fkjsdh fkjdshf \nds flsdf klhds\n\n\nsdfljsd fldsfldslfj sdlfj ds\nfsdl fkjdsl fj dslfj dslkfj dslkfj lskdfj dslf\ndsfldsjfldsjfldsjf ldsfjldsj f',63,'2012-09-09 21:33:14','spam'),(11,NULL,60,'asdasdasdas',61,'2012-09-09 22:03:03','contenido_inapropiado'),(14,NULL,33,'sadasaad asd asds da as',63,'2012-09-09 22:04:38','contenido_inapropiado'),(15,10,NULL,'asdlkj',63,'2012-09-11 08:07:32','informacion_falsa'),(16,10,NULL,'sdfsadfdsf',63,'2012-09-11 08:07:43','propiedad_intelectual'),(17,10,NULL,'asdfasdfadsfa',63,'2012-09-11 08:07:53','spam'),(18,10,NULL,'sdlfj asdfkja sldkfj s',63,'2012-09-11 08:08:12','informacion_falsa'),(20,10,NULL,'asdklfjsd lfashdfk ',63,'2012-09-11 08:23:35','informacion_falsa'),(21,NULL,59,'dfsgs dgfsfdd g',61,'2012-09-12 04:07:31','contenido_inapropiado'),(22,NULL,59,'dfas ddaf afa dfds',61,'2012-09-12 04:07:47','informacion_falsa'),(23,NULL,59,'dasf adfa d',61,'2012-09-12 04:07:57','contenido_inapropiado'),(24,NULL,59,'asdf asdf asdf',61,'2012-09-12 04:08:09','informacion_falsa'),(25,21,NULL,'df daf a',61,'2012-09-12 04:27:21','informacion_falsa'),(26,21,NULL,'dfsf',61,'2012-09-12 04:27:31','contenido_inapropiado'),(27,21,NULL,'ddd',61,'2012-09-12 04:27:37','contenido_inapropiado'),(28,21,NULL,'s',63,'2012-09-12 04:27:54','contenido_inapropiado');

/*Table structure for table `diagnosticos` */

DROP TABLE IF EXISTS `diagnosticos`;

CREATE TABLE `diagnosticos` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos` */

insert  into `diagnosticos`(`id`,`descripcion`) values (2,NULL),(3,NULL),(4,'no mueve los brazos.'),(6,'asdsdaa'),(7,'una descripcion'),(8,'adasda'),(9,'este chico esta en primer ciclo y va a una escuela especial y bleblebleblebleble'),(10,NULL),(11,NULL),(22,NULL),(23,'adsfadsfadsf');

/*Table structure for table `diagnosticos_personalizado` */

DROP TABLE IF EXISTS `diagnosticos_personalizado`;

CREATE TABLE `diagnosticos_personalizado` (
  `id` int(11) NOT NULL,
  `codigo` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  CONSTRAINT `FK_diagnosticos_personalizado_diagnosticos` FOREIGN KEY (`id`) REFERENCES `diagnosticos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos_personalizado` */

insert  into `diagnosticos_personalizado`(`id`,`codigo`) values (6,'222'),(7,'un código'),(10,NULL),(11,NULL),(22,NULL);

/*Table structure for table `diagnosticos_scc` */

DROP TABLE IF EXISTS `diagnosticos_scc`;

CREATE TABLE `diagnosticos_scc` (
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  CONSTRAINT `FK_diagnosticos_scc_diagnosticos` FOREIGN KEY (`id`) REFERENCES `diagnosticos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos_scc` */

insert  into `diagnosticos_scc`(`id`) values (3),(4),(8),(23);

/*Table structure for table `diagnosticos_scc_x_ejes` */

DROP TABLE IF EXISTS `diagnosticos_scc_x_ejes`;

CREATE TABLE `diagnosticos_scc_x_ejes` (
  `diagnosticos_scc_id` int(11) NOT NULL,
  `ejes_id` int(11) NOT NULL,
  `estadoInicial` varchar(500) NOT NULL,
  PRIMARY KEY  (`diagnosticos_scc_id`,`ejes_id`),
  KEY `FK_diagnosticos_scc_x_ejes_ejes` (`ejes_id`),
  CONSTRAINT `FK_diagnosticos_scc_x_ejes_diagnostico_scc` FOREIGN KEY (`diagnosticos_scc_id`) REFERENCES `diagnosticos_scc` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_diagnosticos_scc_x_ejes_ejes` FOREIGN KEY (`ejes_id`) REFERENCES `ejes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos_scc_x_ejes` */

/*Table structure for table `discapacitados` */

DROP TABLE IF EXISTS `discapacitados`;

CREATE TABLE `discapacitados` (
  `id` int(11) NOT NULL,
  `nombreApellidoPadre` varchar(255) default NULL COMMENT 'max 60, encriptado',
  `nombreApellidoMadre` varchar(255) default NULL COMMENT 'max 60, encriptado',
  `fechaNacimientoPadre` date default NULL,
  `fechaNacimientoMadre` date default NULL,
  `ocupacionPadre` varchar(500) default NULL COMMENT 'encriptado',
  `ocupacionMadre` varchar(500) default NULL COMMENT 'encriptado',
  `nombreHermanos` varchar(500) default NULL COMMENT 'encriptado',
  `usuarios_id` int(11) unsigned default NULL COMMENT 'el user que lo dio de alta en el sistema',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `personas_id` (`id`),
  CONSTRAINT `FK_discapacitados_personas` FOREIGN KEY (`id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `discapacitados` */

insert  into `discapacitados`(`id`,`nombreApellidoPadre`,`nombreApellidoMadre`,`fechaNacimientoPadre`,`fechaNacimientoMadre`,`ocupacionPadre`,`ocupacionMadre`,`nombreHermanos`,`usuarios_id`) values (95,'Eduardo Alfredo Velillaa','Evangelina monelloo','2005-06-04','2001-04-16','dsklfjdsfjdsf\nsdfdskljfldskjflskdjflskjdf\nsdflksjdflkdsjflksd\nsdlfksjdflksjdflkjsdddd','dsklfjdsfjdsf\nsdfdskljfldskjflskdjflskjdf\nsdflksjdflkdsjflksd\nsdlfksjdflksjdflkjsddddddd','dsklfjdsfjdsf 233\nsdfdskljfldskjflskdjflskjdf 211\nsdflksjdflkdsjflksd 322\nsdlfksjdflksjdflkjsd 122',61),(122,'¨Ö,âoEêÙ¼ëÿÂ\ZK°0 ˆ`àÛ	ž¼K×üÚª•','|S¦øðe\ré¼‚`³,Ï¼f\Z›Œ¨³vÇ‚´<\0@vÀ2','1995-02-16','2010-02-04','ªÁ¤Àmó§àÎi‰”Üi­+:¤Ê0‹‰(bšiúîûùVØ¼ãã=IÆß\ZG\rk	Ý–ÀG‡«†®üÏrjY¦qdÛƒc¨¼ÿbL#åÖæÁE	`%Œí›þdé¼³Ø†Hžx~€›?T­zÙcbQTú®òÉ\ZU€åˆ[œ¶ÿu’Œ	wsÑ¸fV &Èhá¡ê©j[×o+_æËŽoÆ\ZÈ¹Uy¥“úÙGœ<|…ß5ˆgú9nç°ÿ		¨úü08!ÁÌùüõ16%ù—ÚÀÊFGI¡l!¦¦\ZÄÀÄ=\rH–«hŠGÿ»ÏG¶×6Š€™3ÞbOZ‘µ¦(Ö!é©ÃSD\'/ªìr%”4ÔŒ%”N®@ÎÚ<¾>œn=ŒÝï¡ÍÞ‰ˆäBy×o<Î[ù%Ô)vÇ‡³‰ŠžpÚU´äè†²LÄþS¬lE]åé†ï]‘','ªÁ¤Àmó§àÎi‰”Üi­+:¤Ê0‹‰(bšiúîûùVØ¼ãã=IÆß\ZG\rk	Ý–ÀG‡«†®üÏrjY¦qdÛƒc¨¼ÿbL#åÖæÁE	`%Œí›þdé¼³Ø†Hžx~€›?T­zÙcbQTú®òÉ\ZU€åˆ[œ¶ÿu’Œ	wsÑ¸fV &Èhá¡ê©j[×o+_æËŽoÆ\ZÈ¹Uy¥“úÙGœ<|…ß5ˆgú9nç°ÿ		¨úü08!ÁÌùüõ16%ù—ÚÀÊFGI¡l!¦¦\ZÄÀÄ=\rH–«hŠGÿ»ÏG¶×6Š€™3ÞbOZ‘µ¦(Ö!é©ÃSD\'/ªìr%”4ÔŒ%”N®@ÎÚ<¾>œn=ŒÝï¡ÍÞ‰ˆäBy×o<Î[ù%Ô)vÇ‡³‰ŠžpÚU´äè†²LÄþS¬lE]åé†ï]‘','k…œ³UØ¸ÁÃMÖjÕ³¡¢E1¬>£ž%£Ê!ëº',61),(123,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(124,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(126,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,61),(127,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,61),(128,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(129,'ÈÏó6PÃÛEBQ™/t<f','TÙ ×û«kë*Æ14\'\Z','2003-05-02','2009-04-06','ÿäO°åqjŽjÙÿyÖë','««‹¦I‘|TýTƒOBÙ¤','…³?ª‚-É3!9md+Î',63);

/*Table structure for table `discapacitados_moderacion` */

DROP TABLE IF EXISTS `discapacitados_moderacion`;

CREATE TABLE `discapacitados_moderacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) default NULL COMMENT 'max 50, encriptado',
  `apellido` varchar(200) default NULL COMMENT 'max 50, encriptado',
  `documento_tipos_id` int(11) default NULL,
  `numeroDocumento` int(8) default NULL,
  `sexo` char(1) default NULL,
  `fechaNacimiento` varchar(10) default NULL,
  `email` varchar(200) NOT NULL COMMENT 'max 50, encriptado',
  `telefono` varchar(180) default NULL COMMENT 'max 30, encriptado',
  `celular` varchar(180) default NULL COMMENT 'max 30, encriptado',
  `fax` varchar(180) default NULL COMMENT 'max 30, encriptado',
  `domicilio` varchar(300) default NULL COMMENT 'max 100, encriptado',
  `instituciones_id` int(11) default NULL,
  `ciudades_id` int(11) default NULL,
  `ciudadOrigen` varchar(350) default NULL COMMENT 'max 150, encriptado',
  `codigoPostal` varchar(180) default NULL COMMENT 'max 30, encriptado',
  `empresa` varchar(180) default NULL COMMENT 'max 30, encriptado',
  `universidad` varchar(180) default NULL COMMENT 'max 30, encriptado',
  `secundaria` varchar(180) default NULL COMMENT 'max 30, encriptado',
  `nombreApellidoPadre` varchar(220) default NULL COMMENT 'max 60, encriptado',
  `nombreApellidoMadre` varchar(220) default NULL COMMENT 'max 60, encriptado',
  `fechaNacimientoPadre` date default NULL,
  `fechaNacimientoMadre` date default NULL,
  `ocupacionPadre` varchar(500) default NULL COMMENT 'max 30, encriptado',
  `ocupacionMadre` varchar(500) default NULL COMMENT 'max 30, encriptado',
  `nombreHermanos` varchar(500) default NULL COMMENT 'max 30, encriptado',
  `usuarios_id` int(11) default NULL,
  `nombreBigSize` varchar(255) default NULL,
  `nombreMediumSize` varchar(255) default NULL,
  `nombreSmallSize` varchar(255) default NULL,
  `cambioFoto` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
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
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `documento_tipos` */

insert  into `documento_tipos`(`id`,`nombre`) values (1,'dni'),(2,'ci'),(3,'lc'),(4,'ld');

/*Table structure for table `ejes` */

DROP TABLE IF EXISTS `ejes`;

CREATE TABLE `ejes` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(250) NOT NULL,
  `areas_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_ejes_curriculares_area` (`areas_id`),
  CONSTRAINT `FK_ejes_curriculares_area` FOREIGN KEY (`areas_id`) REFERENCES `areas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;

/*Data for the table `ejes` */

insert  into `ejes`(`id`,`descripcion`,`areas_id`) values (22,'Usar y conocer los números',6),(23,'Usar y conocer los números',7),(24,'Usar y conocer los números',8),(25,'Números de diversa cantidad de cifras',6),(26,'Valor posicional',6),(27,'Suma y resta',6),(28,'Multiplicación y división',6),(29,'Números de diversa cantidad de cifras',7),(30,'Valor posicional',7),(31,'Suma y resta',7),(32,'Multiplicación y división',7),(33,'Números de diversa cantidad de cifras',8),(34,'Valor posicional',8),(35,'Suma y resta',8),(36,'Multiplicación y división',8),(37,'Leer, escuchar leer y comentar diversidad de obras literarias',9),(38,'Leer, escuchar leer y comentar, mientras se reflexiona sobre los géneros, los autores y los recursos empleados para producir ciertos efectos',9),(39,'Escribir textos en torno de lo literario',9),(40,'Buscar y seleccionar información',9),(41,'Profundizar, conservar y organizar el conocimiento',9),(42,'Comunicar lo aprendido',9),(43,'Comenzar a participar en la vida ciudadana',9),(44,'Leer, escribir y tomar la palabra en el contexto de las interacciones institucionales',9),(45,'Comenzar a interpretar los mensajes de los medios de comunicación',9),(46,'Lectura y adquisición del sistema de escritura',9),(47,'Escritura y adquisición del sistema de escritura',9),(48,'La reflexión sobre el lenguaje en el Primer Ciclo',9),(49,'Ortografía: práctica y reflexión',9),(50,'Instituciones de la vida social en contextos culturales y temporales diversos.',10),(51,'Vida familiar y social de distintos grupos Vida familiar y social de distintos grupos sociales en el pasado cercano.',10),(52,'Vida familiar y social en sociedades de la antigüedad. elegir entre: Oriente antiguo, Egipto antiguo, Grecia o Roma clásica',10),(53,'Los trabajos para producir bienes primarios en diferentes contextos.',10),(54,'Los servicios en áreas rurales y urbanas',10),(55,'Formas de organización familiar en contextos culturales diversos.',12),(56,'Vida familiar y relaciones sociales de diferentes grupos en la sociedad colonial.',12),(57,'Cambios y continuidades en las comunicaciones en diferentes contextos histórico',12),(58,'Los trabajos para producir de forma industrial y artesanal.',12),(59,'El transporte de pasajeros en diferentes contextos.',12),(60,'Participación social y política en diferentes contextos históricos.',13),(61,'Formas de vida de los pueblos originarios del actual territorio argentino en el pasado y en el presente',13),(62,'Migraciones hacia la Argentina en diferentes contextos históricos.',13),(63,'Relaciones sociales y económicas entre áreas rurales y urbanas.',13),(64,'La vida social en diferentes contextos.',13),(65,'Los animales. Las partes de su cuerpo',11),(66,'Partes del cuerpo en humanos',11),(67,'Diversidad en el tipo de plantas',11),(68,'Diversidad en las partes de las plantas con flor',11),(69,'Diferencias entre líquidos y sólidos',11),(70,'Diversidad de propiedades en los líquidos',11),(71,'Relaciones entre las propiedades de los sólidos y sus usos',11),(72,'El aire. Presencia del aire en el ambiente',11),(73,'Diversidad de estructuras utilizadas en el desplazamiento por los animales',14),(74,'Relaciones entre las estructuras y el ambiente en el que se desplazan',14),(75,'Diversidad en las formas de dispersión de semillas y frutos',14),(76,'Cambios en humanos desde el nacimiento hasta la edad actual',14),(77,'Cambios en los niños/as a lo largo del año',14),(78,'Cambios en las personas a lo largo de la vida',14),(79,'Propiedades ópticas de diferentes  materiales',14),(80,'Relaciones entre las propiedades ópticas de los materiales y los usos de los objetos fabricados con ellos',14),(81,'Diferentes tipos de movimiento de los cuerpos según la trayectoria que describen y la rapidez del movimiento',14),(82,'Diversidad de dietas y de estructuras utilizadas en la alimentación en los animales',15),(83,'Relaciones entre las dietas y las estructuras utilizadas',15),(84,'Respuestas a cambios ambientales que implican disminución de alimentos',15),(85,'Cambios en las plantas a lo largo del año',15),(86,'Enfermedades contagiosas y no contagiosas',15),(87,'La prevención de las enfermedades contagiosas',15),(88,'Cambios en los materiales por efecto de la variación de la temperatura',15),(89,'Mezclas entre sólidos ,entre líquidos y sólidos y entre líquidos',15),(90,'Métodos de separación de las distintas mezclas',15),(91,'Los seres vivos que habitaron la Tierra millones de años atrás',15),(92,'El cielo visto desde la Tierra',15),(93,'Aproximación al Sistema Solar',15);

/*Table structure for table `embed_videos` */

DROP TABLE IF EXISTS `embed_videos`;

CREATE TABLE `embed_videos` (
  `id` int(11) NOT NULL auto_increment,
  `fichas_abstractas_id` int(11) default NULL,
  `seguimientos_id` int(11) default NULL,
  `codigo` varchar(500) NOT NULL,
  `orden` tinyint(4) unsigned default NULL,
  `titulo` varchar(255) default NULL,
  `descripcion` varchar(500) default NULL,
  `origen` enum('YouTube','YouTube (Playlists)','Google Video','MetaCafe','Vimeo','Clarin','Flickr','JustinTV','LiveLeak','Yahoo Video') NOT NULL default 'YouTube',
  `urlKey` char(64) NOT NULL COMMENT 'para generar la url del link de ampliar video. se utiliza este campo en lugar del id',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `urlKey` (`urlKey`),
  KEY `FK_embed_videos_seguimientos` (`seguimientos_id`),
  KEY `FK_embed_videos_fichas_abstractas` (`fichas_abstractas_id`),
  CONSTRAINT `FK_embed_videos_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_embed_videos_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

/*Data for the table `embed_videos` */

insert  into `embed_videos`(`id`,`fichas_abstractas_id`,`seguimientos_id`,`codigo`,`orden`,`titulo`,`descripcion`,`origen`,`urlKey`) values (20,1,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,NULL,NULL,'YouTube','18eb4fa91b3a41298a9202c94a950d08'),(22,8,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,'dfsafadsf 1','adsfasdfads 1','YouTube','2145a2e07b71444338a1963e56a0881d'),(23,8,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,'sdfasfadsfasd 2','sadfadsfadsf 222','YouTube','352a52072e85aca6360afd0b6a41ca56'),(24,11,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,NULL,NULL,'YouTube','a32851fa34ebf9e6005da190b49b3faf'),(25,3,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,NULL,NULL,'YouTube','000032141f2a32417d18030dad61781c'),(26,NULL,8,'http://www.youtube.com/watch?v=FFOzayDpWoI',NULL,NULL,NULL,'YouTube','528299d90bee1972bc1728b7b43f514c');

/*Table structure for table `entrada_x_contenido_variables` */

DROP TABLE IF EXISTS `entrada_x_contenido_variables`;

CREATE TABLE `entrada_x_contenido_variables` (
  `entradas_id` int(11) NOT NULL,
  `variables_id` int(11) NOT NULL,
  `valorTexto` text,
  `valorNumerico` float default NULL,
  PRIMARY KEY  (`entradas_id`,`variables_id`),
  KEY `FK_seguimiento_x_contenido_variables` (`entradas_id`),
  KEY `FK_seguimiento_x_contenido_variables2` (`variables_id`),
  CONSTRAINT `FK_entrada_x_contenido_variables_entradas` FOREIGN KEY (`entradas_id`) REFERENCES `entradas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_entrada_x_contenido_variables_variables` FOREIGN KEY (`variables_id`) REFERENCES `variables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `entrada_x_contenido_variables` */

insert  into `entrada_x_contenido_variables`(`entradas_id`,`variables_id`,`valorTexto`,`valorNumerico`) values (40,1,NULL,NULL),(40,4,NULL,NULL),(40,6,NULL,NULL),(40,7,NULL,NULL),(40,8,NULL,NULL),(40,35,NULL,17),(40,39,'5tr',NULL),(40,40,NULL,4),(57,1,NULL,NULL),(57,3,NULL,NULL),(57,4,NULL,NULL),(57,6,NULL,NULL),(57,7,NULL,NULL),(57,8,NULL,NULL),(57,35,NULL,0),(57,39,NULL,NULL),(57,40,NULL,NULL);

/*Table structure for table `entrada_x_unidad` */

DROP TABLE IF EXISTS `entrada_x_unidad`;

CREATE TABLE `entrada_x_unidad` (
  `unidades_id` int(11) NOT NULL,
  `entradas_id` int(11) NOT NULL,
  PRIMARY KEY  (`unidades_id`,`entradas_id`),
  KEY `FK_entrada_x_unidad_entradas` (`entradas_id`),
  CONSTRAINT `FK_entrada_x_unidad_entradas` FOREIGN KEY (`entradas_id`) REFERENCES `entradas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_entrada_x_unidad_unidades` FOREIGN KEY (`unidades_id`) REFERENCES `unidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*Data for the table `entrada_x_unidad` */

insert  into `entrada_x_unidad`(`unidades_id`,`entradas_id`) values (1,40),(5,40),(1,57),(5,57),(6,57);

/*Table structure for table `entradas` */

DROP TABLE IF EXISTS `entradas`;

CREATE TABLE `entradas` (
  `id` int(11) NOT NULL auto_increment,
  `seguimientos_id` int(11) NOT NULL,
  `fechaHoraCreacion` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `guardada` tinyint(1) unsigned NOT NULL default '0',
  `fecha` date NOT NULL,
  `tipoEdicion` enum('regular','esporadica') collate utf8_spanish_ci NOT NULL default 'regular',
  PRIMARY KEY  (`id`),
  KEY `FK_entradas_seguimientos` (`seguimientos_id`),
  CONSTRAINT `FK_entradas_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*Data for the table `entradas` */

insert  into `entradas`(`id`,`seguimientos_id`,`fechaHoraCreacion`,`guardada`,`fecha`,`tipoEdicion`) values (40,7,'2013-11-04 20:08:54',1,'2013-10-16','regular'),(57,7,'2013-11-14 18:51:03',0,'2013-10-23','regular');

/*Table structure for table `entrevista_x_pregunta` */

DROP TABLE IF EXISTS `entrevista_x_pregunta`;

CREATE TABLE `entrevista_x_pregunta` (
  `entrevistas_id` int(11) NOT NULL,
  `preguntas_id` int(11) NOT NULL,
  PRIMARY KEY  (`entrevistas_id`,`preguntas_id`),
  CONSTRAINT `FK_entrevista_x_pregunta` FOREIGN KEY (`entrevistas_id`) REFERENCES `entrevistas` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `FK_entrevista_x_pregunta_x_pregunta` FOREIGN KEY (`entrevistas_id`) REFERENCES `preguntas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `entrevista_x_pregunta` */

/*Table structure for table `entrevistas` */

DROP TABLE IF EXISTS `entrevistas`;

CREATE TABLE `entrevistas` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(50) default NULL,
  `borradoLogico` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `entrevistas` */

/*Table structure for table `especialidades` */

DROP TABLE IF EXISTS `especialidades`;

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) default NULL,
  `descripcion` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

/*Data for the table `especialidades` */

insert  into `especialidades`(`id`,`nombre`,`descripcion`) values (9,'Profesor','aaaaaa'),(14,'Terapista ocupacional',NULL),(16,'Educación especial nivel 2',NULL),(17,'Educación especial nivel 3',NULL),(18,'Educación especial nivel 4',NULL),(19,'Educación especial nivel 5',NULL),(22,'Psicólogo pediátrico',NULL),(23,'Nueva Especialidad','dlfkjsldkfjsad\nadsfljasdlfjadslfkjad\nalfkjdslfkjads\n'),(24,'Psicoanalista','descripcion especialidad psicoanalista bleb le ble belble');

/*Table structure for table `fichas_abstractas` */

DROP TABLE IF EXISTS `fichas_abstractas`;

CREATE TABLE `fichas_abstractas` (
  `id` int(11) NOT NULL auto_increment,
  `titulo` varchar(255) default NULL,
  `fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `activo` tinyint(1) unsigned NOT NULL default '1',
  `descripcion` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

/*Data for the table `fichas_abstractas` */

insert  into `fichas_abstractas`(`id`,`titulo`,`fecha`,`activo`,`descripcion`) values (1,'Primer Publicacion','2012-05-18 08:18:15',1,'sdfhaskdfjh adskfh asdkfh asdkfh asdkfh asdkfh asd\nasdkjfh askdfh askdjfh askjfh adskjfh asdfkj \n\naksjdfh askdjfh akdshf aksdfh kasdfh aksjdfh kasdfh kajsdhf akdsjfh asd\nfasdkjfh askdfh akdsfh daksfh askdfh aksdfh aksjdfh asd\nfaskfhdasdfk hasdkfh asdkfjh a\n\naksdfh akdsfh akdsjfh akdsjfh aksjdfh askdjfh \naksjdfh akjdsfh aksdh fakdsjfh aksjdfh adsjkf\nkasdfh akdsjfh akjsdfh adfskh \n\nkajsdfh akdsjfh aksjdh fkjdsh fkjasdhf kjdsh fkjsdfh \nakjfh akdsjfh akjdshf akjdfh sd\naksdjfh aksjdfh kasjdfh akjdsh faksjdhf kajdsh\n\ndsjfh akjdshf akjdfh sd\naksdjfh aksjdfh kasjdfh akjdsh faksjdhf kajdsh\n\nasdfadsfdsfds'),(3,'nueva feria artesanal en mar del plata 123','2012-05-19 05:42:20',1,'dfasdfasdf\nfasdfads\nfasdf\nadsfasd\nfasdf\nadsfa\ndsfasdf\nsdfadskfhaskdjfhaklsjdfhas 123\n'),(5,'sdaf asdfadsfa sdfadsf ','2012-05-30 06:27:59',1,'adsfa dsfadsf\nadsfasdfsdfasdfasdfasd\nfasdfads\n\nasdfjadskl fjas\ndf asldfkja sldkjf ads\nf asldkjf adslkfj sad\nfasdklf jasd'),(8,'Cambio el titulo','2012-05-30 06:29:11',1,'adsfasdf\nads\nfa\nsdf\nasdf\nasd\nf\nasdf\nasd\nfa\nsdf asdfasdfadsf 111 asdfÃ±lkj asdÃ±lfkja dsÃ±lfkj asdfsad\nfas\ndfas\ndf\nasdf\nadsf\nadsf\nasd\nfs adflaskdj fklasjd f\n'),(9,'wterwtwert','2012-07-03 02:11:26',1,'wetert\nwertwer\ntwert\nerwt\newrt'),(10,'rtwertwer terwtwe rt','2012-07-03 02:11:39',1,'wertwert\nerwtwer\ntwer\ntwertwet\newrt\nwet\nwertet'),(11,'asdfasdf','2012-07-03 02:25:40',1,'asdfasdkfasdf\nasdf\nasdf\nasdf\nasdf\nasdf\nasdf\nas\ndf\n\n\nadfsadsfadsf'),(12,'Primer software viejaaa','2012-08-14 21:58:30',1,'djasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\n\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd asdjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\n'),(13,'otra aplicacion','2012-08-16 21:10:44',1,'ladsjf asdlfj asd\nf asdflja sdflkjas df\nas dflasjdf lakdsjf ladsjf ads\nf asldfj lasdfj adsf\nafkl jadlsfj asldfj ladsfj alsdfj alsdjf ladsjf lasdfkj dsa\nf adsklfj aldsjfa\ndsf klasdfj ladsjf lasdj flasjdf alsdkjf asdl'),(14,'3era aplicacion asdjkasld jasldj asldj asldj asldj','2012-08-16 21:11:19',1,'dsfaslfjasdfljasdf\nasdfljasdflkjasd\n'),(15,'ffff','2012-08-16 23:48:37',1,'asdfasdf'),(19,'no tiene q aparecer ','2012-08-17 04:44:41',1,'adsfasf'),(20,'uno mas','2012-08-17 04:45:04',1,'adfads'),(21,'asdlkajsdlkasjdkla moderacion','2012-08-21 23:19:40',1,'sdfadsfadsf\nadsfadsfasdfadsfasdf\nadsfadsfadsfjasdÃ±lkjfas\ndfasdklfjasdlkfjasd\n\ndasdasjdklaskdasd\nasdasd');

/*Table structure for table `fotos` */

DROP TABLE IF EXISTS `fotos`;

CREATE TABLE `fotos` (
  `id` int(11) NOT NULL auto_increment,
  `seguimientos_id` int(11) default NULL,
  `fichas_abstractas_id` int(11) default NULL,
  `personas_id` int(11) default NULL,
  `categorias_id` int(11) default NULL,
  `nombreBigSize` varchar(255) NOT NULL,
  `nombreMediumSize` varchar(255) NOT NULL,
  `nombreSmallSize` varchar(255) NOT NULL,
  `orden` tinyint(4) unsigned default NULL,
  `titulo` varchar(255) default NULL,
  `descripcion` varchar(500) default NULL,
  `tipo` enum('perfil','adjunto') NOT NULL default 'adjunto',
  PRIMARY KEY  (`id`),
  KEY `FK_fotos_categorias` (`categorias_id`),
  KEY `FK_fotos_personas` (`personas_id`),
  KEY `FK_fotos_seguimientos` (`seguimientos_id`),
  KEY `FK_fotos_fichas_abstractas` (`fichas_abstractas_id`),
  CONSTRAINT `FK_fotos_categorias` FOREIGN KEY (`categorias_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_fotos_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_fotos_personas` FOREIGN KEY (`personas_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_fotos_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Data for the table `fotos` */

insert  into `fotos`(`id`,`seguimientos_id`,`fichas_abstractas_id`,`personas_id`,`categorias_id`,`nombreBigSize`,`nombreMediumSize`,`nombreSmallSize`,`orden`,`titulo`,`descripcion`,`tipo`) values (1,NULL,NULL,63,NULL,'63_big_1363414549_290_18071163443_2269_n.jpg','63_medium_1363414549_290_18071163443_2269_n.jpg','63_small_1363414549_290_18071163443_2269_n.jpg',NULL,'Foto de perfil',NULL,'perfil'),(3,NULL,21,NULL,NULL,'21_big_1378011156_IMG-20130530-00939.jpg','21_medium_1378011156_IMG-20130530-00939.jpg','21_small_1378011156_IMG-20130530-00939.jpg',NULL,'ewrewr','werwerewrew\nwerewr\nwerwer','adjunto'),(6,NULL,NULL,129,NULL,'129_big_1378060481_IMG-20130529-00934.jpg','129_medium_1378060481_IMG-20130529-00934.jpg','129_small_1378060481_IMG-20130529-00934.jpg',NULL,'Foto de perfil',NULL,'perfil'),(7,NULL,NULL,128,NULL,'128_big_1378060507_IMG-20130530-00936.jpg','128_medium_1378060507_IMG-20130530-00936.jpg','128_small_1378060507_IMG-20130530-00936.jpg',NULL,'Foto de perfil',NULL,'perfil'),(8,8,NULL,NULL,NULL,'8_big_1378779499_objetivos.png','8_medium_1378779499_objetivos.png','8_small_1378779499_objetivos.png',NULL,NULL,NULL,'adjunto'),(9,NULL,11,NULL,NULL,'11_big_1383540225_1376933568179.jpg','11_medium_1383540225_1376933568179.jpg','11_small_1383540225_1376933568179.jpg',NULL,'hola','una descripcion','adjunto');

/*Table structure for table `institucion_solicitudes` */

DROP TABLE IF EXISTS `institucion_solicitudes`;

CREATE TABLE `institucion_solicitudes` (
  `id` int(11) NOT NULL auto_increment,
  `usuarios_id` int(11) NOT NULL,
  `instituciones_id` int(11) NOT NULL,
  `mensaje` varchar(500) default NULL,
  `fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `FK_institucion_solicitudes_usuarios` (`usuarios_id`),
  KEY `FK_institucion_solicitudes_instituciones` (`instituciones_id`),
  CONSTRAINT `FK_institucion_solicitudes_instituciones` FOREIGN KEY (`instituciones_id`) REFERENCES `instituciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_institucion_solicitudes_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `institucion_solicitudes` */

insert  into `institucion_solicitudes`(`id`,`usuarios_id`,`instituciones_id`,`mensaje`,`fecha`) values (1,63,57,'dsfsdfadsfasdfasdfds','2012-08-29 17:00:51');

/*Table structure for table `instituciones` */

DROP TABLE IF EXISTS `instituciones`;

CREATE TABLE `instituciones` (
  `id` int(11) NOT NULL auto_increment,
  `ciudades_id` int(11) default NULL,
  `nombre` varchar(80) default NULL,
  `descripcion` varchar(500) default NULL,
  `tipoInstitucion_id` int(11) default NULL,
  `direccion` varchar(60) default NULL,
  `email` varchar(50) default NULL,
  `telefono` varchar(50) default NULL,
  `sitioWeb` varchar(60) default NULL,
  `horariosAtencion` varchar(80) default NULL,
  `autoridades` varchar(500) default NULL,
  `cargo` varchar(50) default NULL,
  `personeriaJuridica` varchar(100) default NULL,
  `sedes` varchar(500) default NULL,
  `actividadesMes` text,
  `usuario_id` int(11) default NULL,
  `latitud` varchar(12) default NULL,
  `longitud` varchar(12) default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_instituciones_ciudades` (`ciudades_id`),
  KEY `tipoInstitucion_id` (`tipoInstitucion_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `FK_instituciones_ciudades` FOREIGN KEY (`ciudades_id`) REFERENCES `ciudades` (`id`),
  CONSTRAINT `instituciones_fk_tipos` FOREIGN KEY (`tipoInstitucion_id`) REFERENCES `instituciones_tipos` (`id`),
  CONSTRAINT `instituciones_fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=latin1;

/*Data for the table `instituciones` */

insert  into `instituciones`(`id`,`ciudades_id`,`nombre`,`descripcion`,`tipoInstitucion_id`,`direccion`,`email`,`telefono`,`sitioWeb`,`horariosAtencion`,`autoridades`,`cargo`,`personeriaJuridica`,`sedes`,`actividadesMes`,`usuario_id`,`latitud`,`longitud`) values (33,1,'Universidad FASTA','dasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\n\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh as',1,'Gascon 10293','adsfadsf@dskjfh.com','1324324234','http://www.ufasta.edu.ar','de lunes a viernes 16:00 a 21:00','asdfahskf adfkjadfj\nakdfsjhaksjdh askjdfh akjsdfh \nadfkjah dsf asdfkjh asd\n','Director General bleble','asdfasdf  XXIVV','dsklfajdskfh ads\nf adskjf askdjf\n\ndsklfajdskfh ads\nf adskjf askdjf\ndsklfajdskfh ads\nf adskjf askdjf\ndsklfajdskfh ads\nf adskjf askdjf\n\ndsklfajdskfh ads\nf adskjf askdjf','asdfï¿½lkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh \n\nasdfï¿½lkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh \nasdfï¿½lkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh ',NULL,'-37.30027496','-57.93310474'),(57,1,'LADFSKJ ','sdlfÃ±jasdflk jads\nf asdlfj asdlkfja dslfkja ds\nfasdl fj asdlfkj asldfkj asldfkj asdf\nasdlfkj asdflkj adslfkj asd\nfasdflkj asdfklj',1,'adsf 1234','sadfadsf@sdkjlf.com','12312312',NULL,NULL,NULL,'asdfsdf',NULL,NULL,NULL,NULL,'-38.27268821','-57.93310474'),(58,2,'fgsdfg','sdfgdfg',2,'adsf 123','fasdf@laskjdf.com','1323123123',NULL,NULL,NULL,'asdfadsf',NULL,NULL,NULL,63,'-38.03943857','-57.56506275'),(59,1,'adfsadsfaa dasf asdfa sdf','dfsdflgjdsfklgjsdfgklj\nsfklgjflgjsdlfgkjsdf\ngsdfjlgkjdfslkgjsdflkgjds\nfgjsldfkgjskldfgjdfs\ngjsdflkgjsdflgkjsdf\ngjdfslgkjdflskgjsdfgfs\ndgjlfdkgjkldfs\nsdfjglkdfsjgldfsjglksdfjgsd\nfgjlsdfgkjslkdfg\n',2,'adsfadsf 132123','fkafhsd213@kj.com','23423432',NULL,NULL,NULL,'adsfasdf',NULL,NULL,NULL,63,'-38.09580388','-57.56835937'),(60,3,'dsfasdfas 123','sdfadsfsadf\nasdfa\nsdfas\ndfa\nsdf\nasdf\nafsd',2,'asdfas 2134 adsfas','asdfafs@lkadsjf.com','13123123',NULL,NULL,NULL,'1123sdfdsfasdf',NULL,NULL,NULL,63,'-38.82259066','-59.33935474'),(61,1,'dfsdsf 23423','sdfdsgfdsgdfsgdf',2,'fdsg 234','sdfgdfsg@sdlfk.com','12371928',NULL,NULL,NULL,'sgfdsgdfsg',NULL,NULL,NULL,63,'-37.26530963','-57.88915943');

/*Table structure for table `instituciones_tipos` */

DROP TABLE IF EXISTS `instituciones_tipos`;

CREATE TABLE `instituciones_tipos` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `instituciones_tipos` */

insert  into `instituciones_tipos`(`id`,`nombre`) values (1,'Universidad'),(2,'Hospital');

/*Table structure for table `invitados` */

DROP TABLE IF EXISTS `invitados`;

CREATE TABLE `invitados` (
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `personas_id` (`id`),
  CONSTRAINT `FK_invitados_personas` FOREIGN KEY (`id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `invitados` */

insert  into `invitados`(`id`) values (117),(118),(125);

/*Table structure for table `moderaciones` */

DROP TABLE IF EXISTS `moderaciones`;

CREATE TABLE `moderaciones` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `fichas_abstractas_id` int(11) default NULL,
  `instituciones_id` int(11) default NULL,
  `estado` enum('rechazado','aprobado','pendiente') NOT NULL default 'pendiente',
  `mensaje` varchar(500) default NULL,
  `fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `FK_fichas_abstractas` (`fichas_abstractas_id`),
  KEY `FK_instituciones` (`instituciones_id`),
  CONSTRAINT `FK_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_instituciones` FOREIGN KEY (`instituciones_id`) REFERENCES `instituciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;

/*Data for the table `moderaciones` */

insert  into `moderaciones`(`id`,`fichas_abstractas_id`,`instituciones_id`,`estado`,`mensaje`,`fecha`) values (17,11,NULL,'aprobado','Moderacion automatica por perfil Administrador o Moderador.','2012-07-03 02:25:40'),(18,8,NULL,'aprobado','asdasdasdsa','2012-07-03 19:34:28'),(19,8,NULL,'rechazado','asdasdasdas','2012-07-03 19:34:38'),(20,8,NULL,'pendiente','fdasfsdf','2012-07-03 19:34:43'),(21,3,NULL,'rechazado','adsfadsfasdf','2012-07-03 19:34:51'),(22,3,NULL,'rechazado','fdsfsfdsfdsf','2012-07-03 19:34:58'),(23,3,NULL,'aprobado','adfadfsasdf','2012-07-03 19:35:01'),(33,1,NULL,'aprobado','sdfasdfadsf','2012-07-09 02:19:15'),(34,5,NULL,'aprobado','adsfasdfasdf','2012-07-09 02:19:21'),(35,9,NULL,'aprobado','adfasdfsadf','2012-07-09 02:19:24'),(36,10,NULL,'aprobado','adsfasfasdfasdf','2012-07-09 02:19:28'),(37,11,NULL,'rechazado','adsfadsfsdf','2012-07-09 02:19:30'),(38,NULL,57,'pendiente','sadfasdfsdf','2012-07-09 02:33:15'),(39,NULL,58,'aprobado','adsfdsfdsf','2012-07-09 02:40:01'),(40,NULL,59,'rechazado','dasfasdf','2012-07-09 02:40:29'),(41,NULL,60,'aprobado','adsfadsfsdf','2012-07-09 02:40:57'),(42,NULL,61,'aprobado','adsf','2012-07-09 02:41:40'),(44,NULL,33,'aprobado','Moderacion automatica por perfil Administrador o Moderador.','2012-07-09 07:34:01'),(45,12,NULL,'aprobado','Moderacion automatica por perfil Administrador o Moderador.','2012-08-14 21:58:30'),(46,13,NULL,'pendiente','','2012-08-16 21:10:44'),(47,14,NULL,'aprobado','Moderacion automatica por perfil Administrador o Moderador.','2012-08-16 21:11:19'),(48,15,NULL,'aprobado','Moderacion automatica por perfil Administrador o Moderador.','2012-08-16 23:48:37'),(52,19,NULL,'rechazado','sdfhsdkfjlh sladkfh adskjlfh askldjfh ','2012-08-17 04:44:41'),(53,20,NULL,'aprobado','Moderacion automatica por perfil Administrador o Moderador.','2012-08-17 04:45:04'),(54,21,NULL,'rechazado','sdfgdfgf','2012-08-21 23:19:40'),(55,21,NULL,'aprobado','wqerqw','2012-08-22 02:24:34'),(56,NULL,59,'aprobado','Aprobado automaticamente por moderaciones desactivadas.','2012-09-06 04:59:15');

/*Table structure for table `niveles` */

DROP TABLE IF EXISTS `niveles`;

CREATE TABLE `niveles` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `niveles` */

insert  into `niveles`(`id`,`descripcion`) values (1,'Primaria'),(4,'Secundaria');

/*Table structure for table `objetivo_evolucion` */

DROP TABLE IF EXISTS `objetivo_evolucion`;

CREATE TABLE `objetivo_evolucion` (
  `id` int(11) NOT NULL auto_increment,
  `entradas_id` int(11) NOT NULL,
  `objetivos_personalizados_id` int(11) default NULL,
  `seg_scc_x_obj_apr_obj_id` int(11) default NULL,
  `seg_scc_x_obj_apr_seg_id` int(11) default NULL,
  `progreso` int(11) NOT NULL,
  `comentarios` text,
  PRIMARY KEY  (`id`),
  KEY `FK_objetivo_personalizados` (`objetivos_personalizados_id`),
  KEY `FK_objetivo_evolucion_obj_apr` (`seg_scc_x_obj_apr_obj_id`,`seg_scc_x_obj_apr_seg_id`),
  KEY `FK_objetivo_evolucion_entradas` (`entradas_id`),
  CONSTRAINT `FK_objetivo_evolucion_entradas` FOREIGN KEY (`entradas_id`) REFERENCES `entradas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_objetivo_evolucion_obj_apr` FOREIGN KEY (`seg_scc_x_obj_apr_obj_id`, `seg_scc_x_obj_apr_seg_id`) REFERENCES `seguimiento_scc_x_objetivo_aprendizaje` (`objetivos_aprendizaje_id`, `seguimientos_scc_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_objetivo_personalizados` FOREIGN KEY (`objetivos_personalizados_id`) REFERENCES `objetivos_personalizados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivo_evolucion` */

/*Table structure for table `objetivo_personalizado_ejes` */

DROP TABLE IF EXISTS `objetivo_personalizado_ejes`;

CREATE TABLE `objetivo_personalizado_ejes` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(50) NOT NULL,
  `ejePadre` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

/*Data for the table `objetivo_personalizado_ejes` */

insert  into `objetivo_personalizado_ejes`(`id`,`descripcion`,`ejePadre`) values (1,'Físico',0),(2,'Fisiológico',1),(3,'Psicológico',0),(4,'Social',0),(5,'Atención',3),(6,'Percepción',3),(7,'Aprendizaje',3),(8,'Memoria',3),(9,'Pensamiento',3),(10,'Lenguaje',3),(11,'Motivación',3),(12,'Emoción',3),(13,'Motriz',1),(14,'Asociación',4),(15,'Aceptación',4),(16,'Participación',4),(17,'Seguridad',4),(18,'Estima',4);

/*Table structure for table `objetivo_relevancias` */

DROP TABLE IF EXISTS `objetivo_relevancias`;

CREATE TABLE `objetivo_relevancias` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `objetivo_relevancias` */

insert  into `objetivo_relevancias`(`id`,`descripcion`) values (1,'baja'),(2,'normal'),(3,'alta');

/*Table structure for table `objetivos` */

DROP TABLE IF EXISTS `objetivos`;

CREATE TABLE `objetivos` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(500) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=425 DEFAULT CHARSET=latin1;

/*Data for the table `objetivos` */

insert  into `objetivos`(`id`,`descripcion`) values (4,'Poder contener la orina sda jsldj asldkj asldkj asldkj asldkj asldkja sdlkjas dlkaj sdlkasj dlaksjd alskdj asldkj11'),(9,'Lorem avión ipsum dolor sit amet, consectetur adipiscing elit. Ut in ante placerat, fringilla nibh vitae, facilisis tortor. Cras ut neque nec massa cursus ornare a ut elit. Sed ipsum erat, egestas vel viverra et, tristique vitae tortor. Phasellus a fermentum est. Aliquam at sagittis tortor, ut sollicitudin odio. Donec euismod non nulla vitae dictum. Pellentesque tincidunt sem non adipiscing consectetur. In hac habitasse platea dictumst. Nam tristique vel nunc eu porttitor.'),(11,'no sabe hablar este flaco che '),(12,'la peroasndaso daslkdj alskdj aklsjd '),(13,'Seguir la lectura de quien lee en voz alta\n'),(14,'Seleccionar las obras que se desea leer o escuchar leer\n'),(15,'Adecuar la modalidad de lectura a las características de la obra y de la situación en que se lee\n'),(16,'Expresar los efectos que las obras producen en el lector\n'),(17,'Releer para encontrar pistas que permitan decidir entre interpretaciones diferentes o\ncomprender mejor pasajes o detalles inadvertidos en las primeras lecturas\n'),(18,'Releer para reflexionar acerca de cómo se logran diferentes efectos por medio del lenguaje\n'),(19,'Releer para reconocer las distintas voces que aparecen en el relato\n'),(20,'Reconocer, progresivamente, lo que las obras tienen en común\n'),(21,'Plantearse y sostener un propósito para la escritura y tener en cuenta al destinatario\n'),(22,'Intercambiar y acordar, antes de empezar a escribir, qué y cómo se va a escribir y revisar las\ndecisiones mientras se escribe\n'),(23,'Intercambiar con otros acerca de las decisiones que se van asumiendo mientras se escribe y\nrecurrir a distintas obras para escribir la propia\n'),(25,'Revisar lo que se escribe mientras se escribe y las distintas versiones de lo que se está\nescribiendo hasta alcanzar un texto que se considere bien escrito\n'),(26,'Editar considerando el propósito que generó la escritura, las características del portador, del\ngénero y del destinatario\n'),(27,'Decidir qué materiales sirven para estudiar un tema\n'),(28,'Explorar y localizar informaciones en los materiales seleccionados\n'),(29,'Identificar progresivamente las marcas de organización de los textos que permiten localizar \nla información buscada\n'),(30,'Comentar acerca de la pertinencia de las respuestas halladas y poner en común las estrategias\nutilizadas\n'),(31,'Interpretar los vocablos específicos de los campos y disciplinas de estudio en sus contextos de aparición\n'),(32,'Guardar memoria de las consultas cuando resulta pertinente\n'),(33,'Hacer anticipaciones a partir de los conocimientos previos y verificarlas en el texto\n'),(34,'Releer para aproximarse al significado de los textos cuando no se comprende y resulta imprescindible para avanzar en el tema\n'),(35,'Distinguir los pasajes que se pueden entender de los que presentan dificultades y acudir a distintos medios para resolverlas\n'),(36,'Elegir la mejor manera de registrar o tomar nota de acuerdo con el propósito, el tema, el material consultado y el destino de las notas\n'),(37,'Seleccionar información del texto fuente y registrarla\n'),(38,'Intercambiar saberes con otros para ampliar las posibilidades de comprensión y producción, propias y de los demás\n'),(39,'Ampliar la información obtenida consultando diversas fuentes\n'),(40,'Ampliar la información obtenida consultando diversas fuentes\n'),(41,'Ampliar la información obtenida consultando diversas fuentes\n'),(42,'Adecuar la exposición al propósito y a los destinatarios\n'),(43,'Producir textos para exponer los resultados de las indagaciones, alternando prácticas de planificación, textualización y revisión\n'),(44,'Leer en voz alta y expresar oralmente lo aprendido, alternando prácticas de planificación, preparación, presentación y escucha de exposiciones\n'),(45,'Exponer los resultados de lo estudiado construyendo progresivo dominio sobre las estrategias \ny recursos más adecuados para cada contexto\n'),(48,'Explorar diferentes contextos y funciones de los números en el uso social\n'),(49,'Resolver situaciones de conteo de colecciones de objetos\n'),(50,'Leer, escribir y ordenar números hasta aproximadamente 100 ó 150\n'),(51,'Resolver problemas que permiten retomar la lectura , escritura y orden de los números hasta aproximadamente 100 ó 150\n'),(52,'Leer, escribir y ordenar números hasta aproximadamente 1.000 ó 1.500\n'),(53,'Resolver problemas que permiten retomar la lectura , escritura y orden de los números hasta aproximadamente 1.000 ó 1.500\n'),(54,'Leer, escribir y ordenar números hasta aproximadamente 10.000 ó 15.000\n'),(55,'Explorar las regularidades en la serie oral y escrita en\nnúmeros de diversa cantidad de cifras\n'),(56,'Resolver problemas que involucran el análisis del valor de la \ncifra según la posición que ocupa (en términos de “unos”y “dieces”)\n'),(57,'Resolver problemas de suma y resta que involucran los sentidos\nmás sencillos de estas operaciones: unir, agregar, ganar,\navanzar, quitar, perder, retroceder, por medio de diversos\nprocedimientos –dibujos, marcas, números y cálculos-\n'),(58,'Construir y utilizar estrategias de cálculo mental para resolver sumas y restas\n'),(59,'Explorar estrategias de cálculo aproximado de sumas y  restas\n'),(60,'Investigar cómo funciona la calculadora, usarla para resolver\ncálculos y problemas de suma y resta y verificar resultados\n'),(61,'Seleccionar estrategias de cálculo de suma y resta, de\nacuerdo con la situación y los números involucrados\n'),(62,'Sumar y restar en situaciones que presentan los datos en contextos variados, analizando \ndatos necesarios e innecesarios, pertinencia de las preguntas y cantidad de\nsoluciones del problema\n'),(63,'Explorar las regularidades en la serie oral y escrita en números de diversa cantidad de cifras\n'),(64,'\"\nResolver problemas que involucran el análisis del valor de la cifra según la posición \nque ocupa (en términos de “unos”, “dieces” y “cienes”).\n'),(65,'\"\nResolver problemas de suma y resta que involucran distintos sentidos de estas operaciones: \nunir, agregar, ganar, avanzar, quitar, perder, retroceder, por medio de diversos procedimientos\ny reconociendo los cálculos que permiten resolverlos\n'),(66,'Explorar problemas de suma y resta que involucran otros significados más complejos \nde estas operaciones, por medio de diversos procedimientos\n'),(67,'Construir y utilizar estrategias de cálculo mental para resolver sumas y restas\n'),(68,'Explorar estrategias de cálculo aproximado de sumas y restas\n'),(69,'Utilizar la calculadora para resolver cálculos y problemas de suma y resta y verificar resultados\n'),(70,'Analizar diferentes algoritmos de suma y resta y utilizarlos progresivamente en la resolución de problemas cuando los números lo requieran\n'),(71,'Seleccionar estrategias de cálculo de suma y resta, de acuerdo con la situación y los números involucrados\n'),(72,'Sumar y restar en situaciones que presentan los datos en contextos variados, analizando datos \nnecesarios e innecesarios, pertinencia de las preguntas y cantidad de soluciones del problema\n'),(73,'Resolver problemas de suma y resta que se resuelven con más de un cálculo, \npor medio de diversos procedimientos\n'),(74,'Resolver problemas que involucran algunos sentidos de la multiplicación -series\nproporcionales y organizaciones rectangulares-, inicialmente por medio de diversos\nprocedimientos y luego usando diferentes cálculos que permiten resolverlos\n'),(75,'Comparar problemas de suma y de multiplicación y analizar diferentes cálculos para un mismo problema\n'),(76,'Resolver problemas de reparto y partición, por medio de diversos procedimientos –dibujos, marcas, números y cálculos-\n'),(77,'Construir progresivamente estrategias de cálculo mental para resolver multiplicaciones\n'),(78,'Utilizar la calculadora para resolver multiplicaciones y verificar resultados\n'),(79,'Multiplicar en situaciones que presenten los datos en contextos variados, analizando datos necesarios \ne innecesarios, pertinencia de las preguntas y cantidad de soluciones del problema\n'),(80,'Explorar las regularidades en la serie oral y escrita en números de diversa cantidad de cifras\n'),(81,'Resolver problemas que involucran el análisis del valor de la cifra según la posición que ocupa \n(en términos de “unos”, “dieces”, “cienes” y “miles”).\n\n'),(82,'Resolver problemas de suma y resta que involucran distintos sentidos de estas operaciones: unir, agregar,\nganar, avanzar, quitar, perder, retroceder, reconociendo y utilizando los cálculos que permiten resolverlos\n'),(83,'Explorar problemas de suma y resta que involucran otros significados más complejos de estas operaciones, \npor medio de diversos procedimientos\n'),(84,'Construir y utilizar estrategias de cálculo mental para resolver sumas y restas\n'),(85,'Explorar estrategias de cálculo aproximado de sumas y restas\n'),(86,'Utilizar la calculadora para resolver cálculos y problemas de suma y resta y verificar resultados\n'),(87,'Seleccionar estrategias de cálculo de suma y resta, de acuerdo con la situación y los números involucrados\n'),(88,'Resolver problemas que involucran sumas y restas en situaciones que presenten los datos \nen contextos variados, analizando datos necesarios e innecesarios, pertinencia de\nlas preguntas y cantidad de soluciones\n'),(89,'Resolver problemas de suma y resta que se resuelven con más de un cálculo, por medio\nde diversos procedimientos\n'),(90,'Resolver problemas que involucran diferentes sentidos de la multiplicación -series proporcionales\ny organizaciones rectangulares-, reconociendo y utilizando los cálculos que permiten resolverlos\n'),(91,'Explorar problemas que implican determinar la cantidad que resulta de combinar elementos\nde dos colecciones distintas por medio de diversas estrategias y cálculos\n'),(92,'Resolver problemas de repartos y particiones equitativas, organizaciones rectangulares, \nseries proporcionales, por medio de diversos procedimientos y reconociendo,\nposteriormente, la división como la operación que resuelve este tipo de problemas\n'),(93,'Construir progresivamente un repertorio de cálculos mentales de multiplicación y división,\n a partir del análisis de relaciones entre productos de la tabla pitagórica y posterior memorización\n'),(94,'Construir un repertorio de cálculos mentales de multiplicación y división por la unidad seguida de ceros,\nanalizando regularidades y relaciones con el sistema de numeración\"\n'),(95,'Resolver cálculos mentales de multiplicación y división, a partir del uso de resultados\nconocidos y de diferentes descomposiciones\n'),(96,'Explorar estrategias de cálculo aproximado demultiplicaciones y divisiones\n'),(97,'Utilizar la calculadora para resolver cálculos, para resolver problemas y verificar resultados\n'),(98,'Analizar y usar diferentes algoritmos de la multiplicación por una cifra\n'),(99,'Explorar y usar diferentes algoritmos de división por una cifra\n'),(100,'Seleccionar estrategias de cálculo de multiplicación y división, de acuerdo con la situación y los números involucrados\n'),(101,'Explorar problemas de división que demandan analizar el resto o cuántas veces entra un número dentro de otro,\npor medio de diversos procedimientos y reconociendo la división como la operación que resuelve este tipo de problemas\n'),(102,'Multiplicar y dividir en situaciones que presentan los datos en contextos variados, analizando datos \nnecesarios e innecesarios, pertinencia de las preguntas y cantidad de soluciones del problema\n'),(103,'Resolver problemas que requieran usar varias de las cuatrooperaciones\n'),(104,'Resolver problemas de reparto que implican partir el entero en partes iguales, utilizando mitades o cuartos y\nexplorando la escritura de los números 1/2, 1/4, etc.\n'),(105,'Seguir la lectura de quien lee en voz alta\n'),(106,'Seleccionar las obras que se desea leer o escuchar leer\n'),(107,'Adecuar la modalidad de lectura a las características de la obra y de la situación en que se lee\n'),(108,'Expresar los efectos que las obras producen en el lector\n'),(109,'Releer para encontrar pistas que permitan decidir entre interpretaciones diferentes o\ncomprender mejor pasajes o detalles inadvertidos en las primeras lecturas\n'),(110,'Releer para reflexionar acerca de cómo se logran diferentes efectos por medio del lenguaje\n'),(111,'Releer para reconocer las distintas voces que aparecen en el relato\n'),(112,'Reconocer, progresivamente, lo que las obras tienen en común\n'),(113,'Plantearse y sostener un propósito para la escritura y tener en cuenta al destinatario\n'),(114,'Intercambiar y acordar, antes de empezar a escribir, qué y cómo se va a escribir y revisar las\ndecisiones mientras se escribe\n'),(115,'Intercambiar con otros acerca de las decisiones que se van asumiendo mientras se escribe y\nrecurrir a distintas obras para escribir la propia\n'),(116,'Revisar lo que se escribe mientras se escribe y las distintas versiones de lo que se está\nescribiendo hasta alcanzar un texto que se considere bien escrito\n'),(117,'Editar considerando el propósito que generó la escritura, las características del portador, del\ngénero y del destinatario\n'),(118,'Escuchar relatos e historias de vida que den cuenta del modo en que las instituciones seleccionadas se organizan\n\n'),(119,'Analizar fotografías para describir características del paisaje donde se encuentran los edificios\n'),(120,'Escuchar información leída por el/la docente para conocer las funciones y roles de los distintos actores\n'),(121,'Conocer algunas normas que regulan el funcionamiento de las instituciones elegidas\n'),(122,'Escuchar historias contadas por adultos mayores y observar fotografías para reconocer cambios y continuidades\nen la institución seleccionada\n\n'),(123,'Organizar un museo con objetos que aporten distintos miembros de la comunidad\n'),(124,'Organizar un museo con objetos que aporten distintos miembros de la comunidad\n'),(125,'Producir textos colectivos con ayuda del/la docente para sistematizar los aprendizajes realizados sobre las instituciones\n'),(126,'\"\nVisitar una institución de la localidad y realizar dibujos y escribir palabras en un croquis del edificio para identificar\ndistintos lugares y tareas que se realizan en ellos\n'),(127,'Escuchar relatos e historias para conocer aspectos de la vida familiar\n'),(128,'Analizar fotografías de distintos grupos sociales para conocer cómo se vestían los niños/as, a qué jugaban…\n'),(129,'Participar en conversaciones con los compañeros y con el/la docente sobre las conductas esperables en niños/as y niñas en el pasado cercano y en la actualidad\n'),(130,'Elaborar cuestionarios con ayuda del/la docente para realizar entrevistas a adultos que hayan sido niños/as en la\népoca estudiada\n'),(131,'Organizar encuentros con las familias para jugar juegos tradicionales \n'),(132,'Analizar fotografías para identificar diferentes oficios del pasado y establecer comparaciones con el presente\n'),(133,'\"\nAnalizar fotografías o publicidades de época para establecer conjeturas acerca de cómo se resolvían las necesidades\ncotidianas cuando no existía un artefacto u objeto conocido\n'),(134,'\"\nParticipar en conversaciones para analizar situaciones en las que persisten modos de hacer las cosas similares a los\ndel pasado\n'),(135,'\"\n\n\nRealizar dibujos y comentarios escritos sobre aspectos que les resultan significativos o curiosos de la infancia de\notros tiempos\n'),(136,'\"\nConstruir grupalmente y con ayuda del/la docente líneas de tiempo para ubicar el momento estudiado incorporando\nimágenes y textos breves\n'),(137,'\"\nConstruir grupalmente y con ayuda del/la docente líneas de tiempo para ubicar el momento estudiado incorporando\nimágenes y textos breves\n'),(138,'Escuchar relatos de ficción o leerlos con ayuda del/la docente, observar libros ilustrados para conocer algunos\naspectos de la organización social en la sociedad elegida\n'),(139,'Observar ilustraciones e imágenes para conocer acerca del sistema de escritura de la sociedad elegida\n'),(140,'Analizar imágenes para identificar elementos naturales del paisaje relacionado con la sociedad elegida\n'),(141,'\"\nEscuchar relatos para conocer quiénes gobernaban, qué relación tenían con otros grupos sociales de la\ncomunidad, si gozaban de privilegios\n'),(142,'Consultar libros ilustrados para conocer en detalle cómo eran las viviendas de distintos grupos sociales\n'),(143,'Consultar libros profusamente ilustrados para conocer los trabajos de distintos grupos sociales\n'),(144,'Leer solos o con ayuda del/la docente textos e imágenes para conocer en detalle alguna construcción característica \nde la sociedad elegida\n'),(145,'\"\nEscuchar relatos o mitos para reconocer formas en que la sociedad elegida elaboró explicaciones sobre el origen\n(del mundo, de las cosas, de ellos mismos).\n'),(146,'\"\nObservar fragmentos de películas referidas a la sociedad elegida para identificar aspectos de la vida cotidiana,\nalgunos conflictos entre distintos grupos sociales…\n'),(147,'Realizar dibujos y escrituras que den cuenta de lo aprendido sobre la sociedad elegida\n'),(148,'\"\nAnalizar fotos de paisajes para identificar cuáles son urbanos y cuáles rurales y comparar con dibujos de la propia\nlocalidad realizados por los niños/as\n'),(149,'\"\nAnalizar fotografias de diferentes paisajes para realizar una primera caracterización de la base natural más\no menos modificada por la acción humana y la diversidad de objetos construidos por la sociedad.\n'),(150,'Escuchar al maestro/a y leer información de diversas fuentes para conocer distintos trabajos en distintos contextos\n'),(151,'Conocer en profundidad a través del relato del/la docente y de la lectura de imágenes cómo se produce un bien primario\n'),(152,'Ananalizar fotografías e identificar diversos elementos de la naturaleza y elementos construidos por la sociedad\n'),(153,'Localizar en mapas los países o provincias de los ejemplos seleccionados\n'),(154,'Observar fotografías para identificar y describir maquinarias y/o herramientas que se utilizan\n'),(155,'Realizar visitas a lugares en los que se realizan trabajos para producir bienes primarios\n'),(156,'Realizar registros a través de dibujos o croquis para dar cuenta de los aprendizajes realizados\n'),(157,'\"\nProducir escrituras grupales e individuales con ayuda del/la docente para sistematizar lo aprendido sobre\ntrabajos que se realizan en distintos contextos para producir bienes primarios\n'),(158,'\"\nRealizar intercambios orales y dibujos para poner en común lo que los alumnos/as conocen (de su propia localidad o\nde localidades cercanas)\n'),(159,'\"\nRealizar recorridos por la propia localidad o analizar fotografías para identificar diversas construcciones\nvinculadas con la prestación del servicio elegido\n'),(160,'Escuchar relatos del/la docente y analizar fotografías para conocer como es la vida de las personas en lugares\ndonde la prestación de servicios es diferente.\n'),(161,'Conversar con los compañeros y con el/la docente para intercambiar ideas acerca de las razones por las cuales\nen cada localidad la prestación del servicio es diferente\n'),(162,'Realizar encuestas a diferentes personas para conocer las opiniones de los usuarios en relación con el servicio y\nestablecer algunas relaciones con las observaciones realizadas en las recorridas\n'),(163,'Leer con ayuda del/la docente artículos periodísticos de diarios locales para informarse de algunos problemas en\nla prestación del servicio\n'),(164,'Realizar entrevistas a diferentes trabajadores para relevar el tipo de trabajos que se requieren para el\nfuncionamiento del servicio\n'),(165,'Analizar fotografías, escuchar relatos, canciones de cuna para conocer las formas de crianza de los niños/as de\nacuerdo con las tradiciones y posibilidades de las familias\n'),(166,'Leer textos y revistas con ayuda del/la docente para reconocer las tareas y roles desempeñados por hombres y\nmujeres, quiénes están al frente de las familias, los modos en que se transita la niñez o la juventud, el trabajo de\nlos niños/as y de los jóvenes, los espacios para la formación y la educación, etc\n'),(167,'Utilizar mapas y planos para localizar los lugares relacionados con las familias seleccionadas\n'),(168,'Realizar -asistidos por el/la docente -consultas en bibliotecas y buscar fotografías en Internet, para indagar sobre las \nformas de vestir y el arreglo personal, los materiales utilizados y la organización del espacio en las viviendas\n'),(169,'Escuchar música tradicional para ampliar los conocimientos sobre las manifestaciones culturales de las familias\nseleccionadas\n'),(170,'Realizar intercambios orales entre los compañeros y con el maestro/a sobre la información obtenida para establecer\nrelaciones entre las costumbres, los valores, las creencias y las formas de vida de las familias seleccionadas\ny valorar la diversidad como atributo positivo de las sociedades.\n'),(171,'Participar en conversaciones con los compañeros y con el/la docente para analizar situaciones en las que costumbres, valores y creencias\n'),(172,'Identificar a partir de la información obtenida de diversas fuentes elementos del modo de vida de las familias\nseleccionadas\n'),(173,'Realizar intercambios orales para inferir algunas razones de los cambios y las permanencias en los modos de vida\nde las familias estudiadas y expresar algunas inferencias\n'),(174,'Realizar listados y cuadros comparativos para sistematizar algunos cambios y continuidades\n'),(175,'Dibujar y escribir sobre diversos aspectos de las culturas estudiadas para organizar la información recogida\n'),(176,'Escuchar relatos y leer textos, mirar imágenes para conocer costumbres, actividades productivas, prácticas religiosas,\nformas de recreación de distintos grupos sociales y étnicos\n'),(177,'Participar de intercambios orales para expresar sentimientos, ideas y opiniones sobre la heterogeneidad y\ndesigualdad social y comparar con el presente\n'),(178,'Leer con ayuda del/la docente fuentes de época para conocer cuál era lugar de los esclavos y las mujeres,\nencontrar ejemplos de conflictos y su forma de resolución\n'),(179,'Participar en conversaciones sobre los derechos de distintos grupos sociales y compararlos con el presente para\navanzar en la comprensión de que el acceso a los derechos es una construcción histórica de las sociedades\n'),(180,'Escuchar relatos e historias de vida para conocer aspectos de la vida familiar y efectuar comparaciones con el presente\n'),(181,'Comparar planos de viviendas de distintos grupos sociales para establecer relaciones entre quienes las habitaban\ny las actividades que se realizaban en ellas\n'),(182,'Participar en conversaciones para intercambiar ideas y establecer conjeturas acerca de cómo se resolvían las\nnecesidades cuando no existían artefactos de la vida actual\n'),(183,'Analizar pinturas, litografías, imágenes que representen diferentes oficios del pasado y quiénes los desempeñaban\npara establecer relaciones entre las jerarquías sociales y el mundo del trabajo en la sociedad colonial\n'),(184,'Participar en conversaciones para reconocer cambios y continuidades en las formas de hacer las cosas (formas\nde cocinar, de conservar, de calentar los ambientes, etc.).\n'),(185,'Visitar museos para analizar lugares y objetos y establecer algunas relaciones con los grupos sociales a los que\npertenecieron\n'),(186,'Escuchar relatos e historias para reconocer algunos conflictos entre grupos sociales vinculados con la Revolución\nde Mayo\n'),(187,'Participar en fiestas y celebraciones escolares y/o comunitarias vinculadas con acontecimientos del pasado colonial\ny criollo para reconocer cambios y continuidades en las formas de celebrar\n'),(188,'Participar en conversaciones con el/la docente y los compañeros y realizar dibujos para recuperar las ideas y\nconocimientos que tienen los niños/as acerca de cómo se comunicaban las personas en el pasado\n'),(189,'Observar ilustraciones sobre chasquis incas para analizar características de la vestimenta e identificar y describir\ncómo eran las construcciones donde se realizaba el cambio de mensajero (tambos/postas).\n'),(190,'Observar mapas donde se represente el territorio controlado por el imperio inca para indentificar a grandes\nrasgos las distancias que recorrían los chasquis.\n'),(191,'Escuchar relatos de viajeros para conocer los cambios ocurridos en las comunicaciones a partir de la conquista\neuropea y establecer comparaciones\n'),(192,'Visitar museos, analizar imágenes, escuchar relatos e historias sobre la vida cotidiana en las postas bonaerenses\nen tiempos de la colonia para conocer quiénes y cómo vivían, qué servicios brindaban\n'),(193,'Observar mapas del Virreinato donde se representen las rutas en tiempos coloniales para identificar a grandes\nrasgos los recorridos y las regiones que conectaban\n'),(194,'Escuchar relatos, realizar entrevistas y analizar imágenes para conocer algunos cambios significativos para las\ncomunicaciones entre las personas a partir de la incorporación del teléfono\n'),(195,'Realizar visitas a alguna dependencia del correo para conocer acerca de las condiciones del servicio, algunas\nnormas relacionadas con la comunicación via correo\n'),(196,'Entrevistar a personas para conocer cómo se comunican en la actualidad, qué medios prefieren, cuáles son las\nventajas\n'),(197,'Realizar dibujos y escribir breves explicaciones en una línea de tiempo para organizar la información obtenida\nacerca de cambios y continuidades en las comunicaciones\n'),(198,'Realizar intercambios orales y dibujos que permitan recuperar los conocimientos e ideas de los niños/as sobre la\nproducción seleccionada\n'),(199,'Conocer a través de la consulta de materiales diversos cómo se realiza el producto en forma industrial\n'),(200,'Consultar textos, fotografías, videos para conocer los trabajos de las personas, identificar tareas y materiales\nsobre los que se trabaja\n'),(201,'Consultar distintas fuentes de información para saber acerca de las tecnologías empleadas para fabricar el mismo\nproducto de manera artesanal y establecer comparaciones\n'),(202,'Realizar visitas a un establecimiento industrial para conocer mediante la observación directa: mecanización o\ninformatización de alguna o de todas las etapas del proceso\n'),(203,'Entrevistar a personal de la fábrica para conocer acerca de las tareas que realiza, los conocimientos que posee,\nlas normas de seguridad y protección en el trabajo, horarios\n'),(204,'Realizar visitas a talleres artesanales para conocer cómo y con qué trabajan las personas para fabricar productos\n'),(205,'Trabajar con croquis sencillos para interpretar cómo se organiza el espacio en la fábrica y en el taller artesanal y establecer relaciones  entre los procesos y las instalaciones.\n'),(206,'Elaborar un cuadro para comparar tecnologías y características de productos en modos de fabricación industrial\ny artesanal\n'),(207,'Reconocer las normas de higiene presentes en el proceso de fabricación\n'),(208,'Participar en conversaciones para anticipar posibles situaciones generadas por la falta o incumplimiento de\ndeterminadas normas\n'),(209,'Intercambiar ideas acerca del valor de las normas que regulan la fabricación de productos para reconocer los\nderechos de las personas a productos que no pongan en riesgo su salud, y a normas que resguardan derechos\nde las personas que trabajan\n'),(210,'Identificar posibles riesgos ambientales derivados del proceso de producción\n'),(211,'Participar en conversaciones para intercambiar ideas acerca del valor de producir en condiciones respetuosas del cuidado del ambiente\n'),(212,'Realizar intercambios orales para poner en relación los conocimientos obtenidos a través de los textos y demás fuentesy la experiencia personal de los alumnos/as.\n'),(213,'Localizar en planos los establecimientos visitados o reconocidos\n'),(214,'Realizar intercambios orales y dibujos que permitan recuperar los conocimientos de los niños/as sobre los transportes\nde acuerdo a su experiencia personal.\n'),(215,'Conocer a través del relato del/la docente y de la lectura cómo viajan las personas en diferentes lugares.\n'),(216,'Observar fotografías de las localidades a las que refieren los ejemplos para identificar a partir de ciertos indicadores:\náreas urbanas o rurales, las redes físicas que conectan unas localidades con otras, la diversidad de medios disponibles.\n'),(217,'Buscar información en revistas especializadas, en folletos turísticos, en Internet para conocer cómo viaja la gente en otros lugares del mundo y establecer comparaciones con los ejemplos presentados.\n'),(218,'Analizar fotografías para reconocer características del paisaje y las adaptaciones del transporte a distintos tipos de\nsuelo y la energía utilizada.\n'),(219,'Usar mapas de Argentina y provinciales para localizar los lugares en los que se desarrollan los ejemplos seleccionados; planos de la localidad para identificar terminales de ferrocarril o de micros, puentes, caminos.\n'),(220,'Analizar un mapa de las principales rutas terrestres de la Argentina para identificar lugares más y menos conectados,\nrutas nacionales, provinciales, caminos de tierra y caminos pavimentados.\n'),(221,'Realizar entrevistas a usuarios del transporte de pasajeros de la propia localidad para comparar con los ejemplos\nestudiados.\n'),(222,'Construir cuadros para comparar la información de la propia localidad con la de los casos estudiados\n'),(223,'Conocer a través de la lectura de folletos informativos y disposiciones municipales las normas de circulación en la\npropia localidad.\n'),(224,'Participar en conversaciones entre compañeros y con el/la docente para analizar su comportamiento como transeúntes y usuarios de diferentes medios de transporte y practicar el diálogo, la argumentación y la deliberación.\n\n'),(225,'Intercambiar ideas sobre la propia circulación en el medio local para tomar conciencia de sus propios derechos y\nresponsabilidades\n'),(226,'Realizar salidas a lugares cercanos a la escuela para analizar el cumplimiento de las normas vinculadas con la\ncirculación, efectuar registros y producir escrituras para informar a la comunidad.\n'),(227,'Participar en campañas escolares y/o acciones comunitarias vinculadas con el sistema de transporte y las normas que lo regulan para ejercitarse en una ciudadanía responsable, participativa e inclusiva.\n'),(228,'Realizar intercambios orales o producciones escritas para poner en común la información de los alumnos/as respecto\nal conflicto a estudiar, explicitar relaciones con otros conflictos similares del medio local o provincial.\n'),(229,'Leer diarios y páginas de Internet seleccionadas por el maestro/a para buscar información sobre los motivos del\nconflicto, su estado actual.\n'),(230,'Leer o escuchar testimonios de diversos actores involucrados para comprender el modo en que el problema planteado\nlos afecta\n'),(231,'Realizar intercambios orales para reflexionar sobre las formas de participación de la ciudadanía en el problema planteado\n'),(232,'Utilizar mapas para localizar los lugares vinculados con el conflicto analizado\n'),(233,'Escribir solos o con ayuda del/la docente textos explicativos sobre los temas analizados para elaborar síntesis que\nincluyan los principales elementos del problema y su posible resolución.\n'),(234,'Intervenir con los compañeros y con ayuda del/la docente en diversas actividades escolares y/o comunitarias como\nun modo de ejercer el derecho a la participación y ejercitarse en prácticas de convivencia democrática.\n'),(235,'Entrevistar a familiares y vecinos para indagar acerca de los modos en que participan en la vida política\n'),(236,'Leer solos y con ayuda del/la docente textos para conocer sobre la participación social y política de distintos grupos\nsociales en la sociedad colonial, durante la Revolución de Mayo y guerras de independencia…\n'),(237,'Entrevistar mujeres para conocer cambios en el acceso a los derechos políticos en el último siglo y a personas que\npertenecen a distintas comunidades para conocer formas de participación en diversas culturas.\n'),(238,'Construir una línea de tiempo para incluir acontecimientos que den cuenta de cómo se amplió la participación política\nde las personas y grupos en las distintas épocas abordadas.\n'),(239,'Realizar visitas a instituciones del gobierno local (Concejo Deliberante, instituciones municipales) y entrevistar un funcionario para indagar acerca de las tareas que realiza, con quiénes trabaja, las dificultades que enfrenta, sus\nvínculos con sus representados.\n'),(240,'Entrevistar a ciudadanos del medio local para conocer sus opiniones con respecto a la obra de gobierno y …\n'),(241,'Escribir solos y con ayuda del/la docente textos que expliquen cuáles son las principales instituciones del medio local, qué funciones cumplen, qué tareas realizan los funcionarios y qué problemas centrales afectan a los ciudadanos.\n'),(242,'Realizar intercambios orales para explicitar las ideas y conocimientos de los niños/as respecto a quiénes habitaban\nel actual territorio argentino hace miles de años\n'),(243,'Participar de conversaciones que permitan a los niños/as expresar su pertenencia étnica o cultural para valorar su\norigen y reconocer el derecho a adscribir a múltiples identidades.\n'),(244,'Leer solos y con ayuda del/la docente información respecto de dos pueblos originarios diferenciados para comparar\nlos modos de procurarse el sustento, la organización de las tareas, las viviendas y establecer algunas relaciones.\n'),(245,'Analizar imágenes de los lugares que habitaban, y localizar los grupos en mapas físicos para establecer algunas relacionesentre las condiciones naturales y los modos de vida de los pueblos.\n'),(246,'Escuchar relatos y leer solos o con ayuda del/la docente textos que informen sobre las diferentes formas de organización social y política para conocer diferentes formas de organizar el poder en las sociedades estudiadas.\n'),(247,'Escuchar y leer mitos y leyendas para conocer acerca de algunas creencias y explicaciones de diversos fenómenos y\nvalorarlas como modos de transmisión en una cultura.\n'),(248,'Realizar visitas a museos para analizar objetos de la vida cotidiana y valorar los conocimientos puestos en juego en\nsu realización\n'),(249,'Participar en conversaciones para reconocer técnicas de los pueblos originarios en la actualidad (tejido, cestería,\nalfarería, etc)y los modos de transmisión de las mismas.\n'),(250,'Reconocer en distintas manifestaciones de la vida cotidiana el aporte de los pueblos originarios (palabras de uso\ncorriente, creencias, técnicas, música, gastronomía, etc.).\n'),(251,'Realizar indagaciones para localizar grupos de diferentes comunidades originarias en la localidad y en otros lugares\nen la Provincia de Buenos Aires  y en el país\n\n'),(252,'Realizar entrevistas o intercambios epistolares con miembros de las comunidades para conocer acerca de sus condiciones de vida, expectativas, reivindicaciones, aspectos de las tradiciones que se conservan y que han cambiado, etc.\n'),(253,'Conocer y participar en celebraciones y/o conmemoraciones de los pueblos originarios para enriquecer los\nconocimientos sobre las formas diversas de celebrar en la comunidad, en la Provincia y en el país.\n'),(254,'Realizar intercambios orales para recuperar la experiencia personal de los alumnos/as sobre las migraciones.\n'),(255,'Elaborar preguntas para realizar una encuesta en las casas y obtener datos para analizarlos y sacar conclusiones.\nsobre el origen de los niños/as y sus familias.\n'),(256,'Localizar en mapas planisferio los lugares de origen de los niños/as y sus familias\n'),(257,'Entrevistar a personas que hayan migrado para conocer los motivos que los llevaron a abandonar sus países de origen\ny conocer sus sentimientos, expectativas, anhelos y frustraciones'),(258,'Escuchar relatos e historias para conocer las vicisitudes de los inmigrantes que llegaron a la Argentina en la segunda\nmitad del siglo XIX y en la actualidad\n'),(259,'Leer textos (solos o con ayuda del/la docente) para obtener información sobre los países de los que provenían y los\nmotivos por los cuales decidieron migrar y establecer comparaciones con las migraciones actuales.\n'),(260,'Leer textos (solos o con ayuda del/la docente) para obtener información sobre los países de los que provenían y los\nmotivos por los cuales decidieron migrar y establecer comparaciones con las migraciones actuales.\n'),(261,'Analizar fotografías para conocer quiénes venían, en qué lo hacían, adónde llegaban para establecer relaciones con\nla información que brindan los textos.\n'),(262,'Leer cartas de inmigrantes para conocer sus sentimientos, expectativas y frustraciones y establecer relaciones con la\ninformación que brindan otras fuentes.\n'),(263,'Escuchar historias, leer textos, visitar museos para conocer algunos aspectos de la historia de la Pcia. de Buenos Airesrelacionados con los inmigrantes.\n'),(264,'Realizar indagaciones en la localidad para establecer relaciones entre la historia local y procesos migratorios en el\npasado y en el presente.\n'),(265,'Indagar en las propias familias sobre costumbres y festividades relevantes y organizar un calendario de fiestas y\ncelebraciones que incluya a diversas comunidades para enriquecer los acontecimientos que se evocan , recuerdan o\ncelebran.\n'),(266,'Participar con los compañeros en el festejo del Día del Inmigrante a través de representaciones, muestras y/o exposiciones para comunicar a la comunidad los conocimientos aprendidos sobre los inmigrantes.\n'),(267,'Participar en conversaciones con el/la docente para analizar actitudes discriminatorias hacia los inmigrantes en el\npasado y en el presente.\n'),(268,'Buscar en distintos medios de comunicación información sobre situaciones de discriminación a inmigrantes para\nllegar a acuerdos sobre la importancia de valorar la diversidad como atributo positivo de las sociedades\n'),(269,'Leer solos o con ayuda del/la docente textos para conocer los Derechos del niño/a vinculados con el derecho a la\nidentidad y a la protección respecto de prácticas discriminatorias raciales, religiosas, étnicas.\n'),(270,'Realizar intercambios orales o dibujos que permitan recuperar los conocimientos y la experiencia personal de los\nalumnos/as sobre la producción, circulación y comercialización del ejemplo seleccionado\n'),(271,'Observar fotografías, láminas, dibujos y planos para describir las transformaciones realizadas vinculadas con el\ncircuito productivo\n'),(272,'Leer textos, solos y con ayuda del/la docente, para conocer y analizar las distintas etapas del circuito productivo \nseleccionado, identificar los actores intervinientes y reconocer algunas relaciones\n'),(273,'Consultar bibliotecas de la escuela o de la localidad y suplementos rurales de periódicos para ubicar y seleccionar información sobre algún aspecto del circuito elegido, establecer relaciones entre texto lingüístico e imágenes,\nampliar y confrontar la información de esas fuentes con otras.\n'),(274,'Analizar videos y programas de divulgación para conocer con mayor profundidad alguna de las etapas del circuito\nproductivo (agraria, industrial o comercial)\n'),(275,'Utilizar planos y mapas a diferente escala, para localizar las etapas del proceso productivo trabajado\n'),(276,'Localizar información en textos -solos y con ayuda del/la docente- para conocer cómo se produce con distintas\ntecnologías y efectuar comparaciones\n'),(277,'Realizar visitas a establecimientos agropecuarios, a comercios, establecimientos industriales, depósitos, etc.,\npara recoger datos, realizar entrevistas, tomar notas y efectuar comparaciones con la información obtenida de\notras fuentes\n'),(278,'Realizar dibujos, esquemas, cuadros sencillos de doble entrada para registrar y sistematizar las observaciones\nrealizadas en las salidas\n'),(279,'Leer y comentar oralmente notas de revistas y periódicos para recoger información sobre diferentes problemas\nambientales en los espacios rurales y urbanos seleccionados\n'),(280,'Producir textos en forma grupal e individual para integrar la información recogida usando vocabulario específico\nvinculado con el proceso productivo estudiado\n'),(281,'Realizar dibujos y escribir textos que describan y caractericen los ámbitos en los que se desenvuelve la vida cotidiana\nde los niños/as para recuperar la experiencia en relación con los lugares que habitan.\n'),(282,'Establecer correspondencia por carta o via e-mail con niños/as de otras localidades para conocer cómo es la vida de las personas en diferentes lugares del país y del mundo.\n'),(283,'Leer textos, solos y con ayuda del/la docente, para conocer, analizar y comparar las características de la vida en\náreas rurales, grandes ciudades, medianas y pequeñas de distintos lugares del mundo en relación con los tipos\nde trabajos, vivienda, servicios, esparcimiento\n'),(284,'Observar fotografías y analizar planos para identificar las características de las localidades trabajadas en los\ntextos y establecer relaciones entre diversas fuentes.\n'),(285,'Realizar observaciones, analizar fotografías para establecer relaciones entre construcciones, movimiento de personas\ny actividades en las localidades analizadas\n'),(286,'Realizar entrevistas a vecinos de la localidad para conocer algunos de los trabajos en los que se desempeñan las\npersonas y vincularlos con las áreas estudiadas.\n'),(287,'Realizar entrevistas a personas mayores para conocer los cambios ocurridos en la localidad en los últimos años\n(cambios en la producción, migraciones, etc.).\n'),(288,'Consultar diarios locales y nacionales para identificar problemas comunes de las personas que viven en distintos\ncontextos y formas de solucionarlos\n'),(289,'Consultar diarios locales, realizar entrevistas y/o salidas en el medio local para identificar problemáticas\nambientales y la manera en que afectan la vida de sus habitantes.\n'),(290,'Participar en conversaciones entre los compañeros y con el/la docente para reconocer distintos niveles de\nresponsabilidad en la prevención de los problemas ambientales detectados.\n'),(291,'Leer normas y reglamentaciones para conocer regulaciones en la organización del espacio en distintos contextos\n(zonas protegidas, permisos de edificación, circulación del tránsito, etc)\n'),(292,'\"\nRealizar un cuadro para comparar normas en diferentes localidades (un área rural, una gran ciudad y una\npequeña localidad).\n'),(293,'Leer textos, analizar fotos, o realizar visitas para establecer relaciones entre algunos lugares emblemáticos y\nacontecimientos del pasado o del presente significativos para la comunidad.\n'),(294,'Analizar planos de la localidad para identificar la localización de lugares emblemáticos\n'),(295,'Utilizar planos y croquis para orientarse y ubicarse en caminos y recorridos de zonas conocidas utilizando\nreferencias y establecer relaciones entre el espacio real y su representación.\n'),(296,'Entrevistar a informantes calificados y a vecinos del lugar para conocer las razones por las cuales ciertos lugares\nde la localidad constituyen parte del patrimonio reconocido e informarse sobre situaciones en las que estos\nlugares se encuentran amenazados.\n'),(297,'Escribir cartas a las autoridades, participar en campañas que ayuden a generar actitudes responsables respecto\ndel cuidado y conservación del patrimonio de la localidad.\n'),(298,'Realizar observaciones directas de animales y/o de imágenes acompañadasde descripciones orales y dibujos, para obtener información sobre las partes del cuerpo de diferentes animales.\n'),(299,'Registrar y organizar la información en fichas o cuadros diseñados por el/la docente.\n'),(300,'Elaborar clasificaciones según criterios sugeridos por el maestro/a o propuestos por los alumnos/as y a partir de ellas establecer generalizaciones en cuanto a las partes que forman el cuerpo de animales muy diversos.\n'),(301,'Obtener información para identificar a los invertebrados como animales mediante la comparación con otros en cuanto a las partes del cuerpo, reconociendo aspectos comunes y diferencias.\n'),(302,'Elaborar generalizaciones sobre las semejanzas y las diferencias entre ambos grupos; plasmar los resultados en un texto producido colectivamente.\n'),(303,'Intercambiar ideas sobre las semejanzas y diferencias entre ambos grupos (animales y humanos).\n'),(304,'Realizar descripciones exhaustivas de las partes del cuerpo y enriquecerlas con la lectura de información aportada por enciclopedias, libros y videos, así como consultas con médicos.\n'),(305,'Organizar la información recopilada y compararla con la sistematizada en instancias anteriores, en especial la referida a vertebrados mamíferos\n'),(306,'Elaborar generalizaciones sobre los principales rasgos compartidos entre humanos y otros animales, y sobre las diferencias.\n'),(307,'Describir y comparar las imágenes o las plantas según las características del tallo y otras partes, la forma de la planta, el tamaño, y en el caso de la observación directa, acompañar las descripciones con dibujos realistas.\n'),(308,'Organizar la información agrupando las plantas que presentan aspectos comunes respecto de los atributos en estudio.\n'),(309,'Comunicar los agrupamientos realizados justificando los aspectos considerados en cada caso.\n'),(310,'Elaborar de manera conjunta generalizaciones sencillas sobre las características diferenciales entre árboles, arbustos y plantas herbáceas.\n'),(311,'Agrupar las diferentes partes según la solicitud del maestro/a y justificar dicha selección, poniendo en juego lo que saben.\n'),(312,'Observar las partes, describirlas y compararlas en cuanto a los aspectos compartidos y las diferencias.\n'),(313,'Buscar e interpretar información mediante la lectura de textos e imágenes para ampliar lo aprendido durante las observaciones.\n'),(314,'Organizar la información en cuadros y comunicarla a la clase.\n'),(315,'Elaborar generalizaciones sencillas sobre las similitudes y diferencias entre las partes de las plantas.\n'),(316,'Agrupar los materiales en líquidos y sólidos según sus saberes y fundamentar la clasificación realizada.\n'),(317,'Realizar exploraciones de las características de los materiales líquidos, siguiendo las orientaciones del docente, para reconocer aspectos compartidos por todos ellos.\n'),(318,'Registrar y organizar la información resultante de la exploración a través de dibujos y cuadros.\n'),(319,'Intercambiar ideas y comparar los materiales sólidos con los líquidos en relación con las características identificadas en los líquidos en las exploraciones anteriores.\n'),(320,'Elaborar generalizaciones sobre las diferencias entre los sólidos y los líquidos.\n'),(321,'Realizar exploraciones y comparar las características de losdistintos líquidos.\n'),(322,'Registrar y organizar los datos obtenidos en un cuadro de doble entrada.\n'),(323,'Leer e interpretar la información consignada con la finalidad que los niños/as elaboren conclusiones sobre las diferencias entre líquidos.\n'),(324,'Observar y describir características de diferentes materiales sólidos en relación con la pertinencia para ser utilizados con determinados fines.\n'),(325,'Leer textos sobre las propiedades de los materiales analizados para ampliar la información, conceptualizar sobre sus características e incorporar nuevos términos.\n'),(326,'Realizar diferentes actividades con su propio cuerpo que les permitan poner en evidencia la presencia de aire a su alrededor.\n'),(327,'Explorar los cambios experimentados por diferentes objetos inflables al introducir aire en su interior. Registrar las conclusiones a través de dibujos y la producción de textos con la colaboración del docente.\n'),(328,'Construir objetos y realizar exploraciones con ellos que pongan en evidencia la presencia del aire.\n'),(329,'Sistematizar la información obtenida en las exploraciones a través de intercambios orales y en cuadros de doble entrada, y elaborar conclusiones.\n'),(330,'Expresar sus saberes sobre qué es el viento y profundizar sus ideas a través de la exploración del  funcionamiento de instrumentos sencillos, construidos por ellos con la colaboración de los adultos.\n'),(331,'Buscar información en diferentes fuentes sobre las variaciones en la intensidad del viento a lo largo del día o de varios días. Leer e interpretar dichos datos estableciendo relaciones entre la intensidad del viento y el movimiento de diferentes objetos.\n'),(332,'Realizar una búsqueda de información sobre las estructuras utilizadas en el desplazamiento por los animales, a través de la lectura de imágenes y de textos, la observación directa y la realización de dibujos en zoológicos, parques, reservas, museos.\n'),(333,'Organizar la información referida a los animales estudiados mediante imágenes o dibujos con referencias.\n'),(334,'Comunicar la información a través de presentaciones orales y/o lectura de los registros producidos.\n'),(335,'Elaborar un texto, individual o colectivo, sobre la diversidad de estructuras de desplazamiento.\n'),(336,'Agrupar los animales según el ambiente en el que se desplazan y analizar la información así sistematizada estableciendo relaciones entre las particularidades de las estructuras de desplazamiento en diferentes animales y el ambiente en el que se desplazan.\n'),(337,'Elaborar generalizaciones sencillas sobre dicha relación.\n'),(338,'Buscar, leer e interpretar información referida al fenómeno de la dispersión y a la diversidad de estructuras presentes en frutos y semillas relacionadas con el mismo.\n'),(339,'Formular nuevas preguntas que enriquezcan los conocimientos sobre el tema.\n'),(340,'Comunicar lo aprendido a partir de intercambios orales organizados en torno a preguntas planteadas por el/la docente.\n'),(341,'Sistematizar la información obtenida en un cuadro de doble entrada.\n'),(342,'Diseñar y construir modelos de frutos y semillas acompañados de producciones escritas, que les permitan poner en juego lo aprendido,.\n'),(343,'Intercambiar ideas sobre los cambios físicos, en los gustos e intereses desde que eran pequeños hasta la edad actual.\n'),(344,'Observar objetos, fotos y videos para obtener información sobre los cambios y las permanencias en relación con los modos de desplazarse, de alimentarse, de vestirse, en las actividades que realizaban, en los cuidados que recibían, en el tamaño del cuerpo.\n'),(345,'Organizar las fotos y los objetos correspondientes a diferentes etapas de crecimiento y desarrollo y compararlas tomando en cuenta los cambios ocurridos.\n'),(346,'Leer y sistematizar datos sobre el crecimiento y el desarrollo de los niños/as desde el nacimiento hasta la edad actual.\n'),(347,'Entrevistar a una mamá para indagar sobre las peculiaridades de la  vida de un bebé.\n'),(348,'Producir un texto con las conclusiones a las que arribaron.\n'),(349,'Formular anticipaciones sobre posibles cambios físicos en ellos mismos en los meses subsiguientes.\n'),(350,'Realizar observaciones, mediciones y registros de la talla, el tamaño de las manos y de los pies, en dos momentos del año.\n'),(351,'Organizar los registros de las mediciones y compararlos para elaborar conclusiones sobre las modificaciones experimentadas por los niños/as de cada pequeño grupo.\n'),(352,'Organizar en una serie los datos correspondientes a cada medición de la talla, analizar comparativamente los datos de ambas mediciones. Establecer relaciones entre los datos y la fecha de nacimiento y elaborarconclusiones sobre la independencia de la edad y la altura.\n'),(353,'Realizar observaciones y registros de los cambios en la dentición en dos momentos del año, compararlas y elaborar conclusiones sobre esteaspecto del crecimiento.\n'),(354,'Buscar información mediante la lectura de textos acerca de los cambios en la boca durante este período de la vida.\n'),(355,'Formular preguntas para realizar a especialistas médicos y/u odontólogos (invitados por la escuela) sobre los cambios en la dentición y sobre cuidados referidos a la salud bucal y para un buen crecimiento.\n'),(356,'Organizar en un folleto la información recabada en la entrevista con los especialistas para distribuir entre los niños/as de la escuela.\n'),(357,'Observar las imágenes y leer los textos para obtener información sobre el crecimiento y desarrollo de las personas a lo largo de la vida.\n'),(358,'Elaborar textos sencillos sobre los cambios en las personas en una determinada etapa, asignada por el/la docente y comunicarlo a los compañeros.\n'),(359,'Organizar colectivamente la información recabada en una secuencia temporal y elaborar generalizaciones sobre los cambios experimentados  por ellos a lo largo del año, y de las personas a lo largo de la vida.\n'),(360,'Explorar la producción de sombras con su propio cuerpo para identificar las condiciones necesarias para que se produzcan.\n'),(361,'Elaborar anticipaciones sobre la posibilidad de producir sombra de una variedad de objetos construidos con materiales opacos, translúcidos y transparentes, realizar exploraciones y explicar el comportamiento de cada uno de ellos. Registrar y organizar los datos en un cuadro.\n'),(362,'Analizar y comunicar los resultados de las exploraciones y elaborar generalizaciones sencillas sobre las propiedades ópticas de diferentes materiales.\n'),(363,'Observar y describir las características de objetos fabricados con materiales con diferentes propiedades ópticas y establecer relaciones entre sus propiedades y el uso que las personas hacen de ellos.\n'),(364,'Acordar entre todos las normas del juego. Participar activamente y a la vez analizar los recorridos seguidos por ellos durante las carreras y la rapidez en los desplazamientos.\n'),(365,'Realizar descripciones orales y representaciones gráficas de las trayectorias de diferentes cuerpos en situaciones variadas de movimiento.\n'),(366,'Comparar diferentes trayectorias con el propósito de sistematizarlas e identificar variaciones en la rapidez en los desplazamientos.\n'),(367,'Interpretar los esquemas para identificar a qué objeto corresponde, y describir su movimiento atendiendo a las características de sus trayectorias y su rapidez.\n'),(368,'Comparar las diversas trayectorias y comunicar oralmente los resultados del análisis.\n'),(369,'Realizar una búsqueda de información sobre las dietas consumidas y las estructuras utilizadas en la alimentación por los animales seleccionados, a través de la lectura de imágenes y textos.\n'),(370,'Organizar la información en fichas provistas por el/la docente.\n'),(371,'Comunicar la información organizada en la fichas a través de la lectura individual o en parejas de copias distribuidas por el/la docente.\n'),(372,'Sistematizar la información recabada en un cuadro de doble entrada.\n'),(373,'Analizar la información consignada en el cuadro y organizar a los animales en grupos bajo el criterio “tipo de dieta” o “estructura/sempleada/s en la alimentación”.\n'),(374,'Registrar y comunicar la clasificación elaborada mediante la confección de paneles.\n'),(375,'Analizar la información sistematizada en la actividad anterior, estableciendo relaciones entre el tipo de dieta consumida y las estructuras  utilizadas, y reconocer regularidades.\n'),(376,'Elaborar generalizaciones sencillas sobre dicha relación y registrar la información a través de textos producidos con la colaboración del/la docente.\n'),(377,'Realizar una búsqueda de información en pequeños grupos sobre las respuestas de los animales a cambios regulares en el ambiente y registrarla en notas en borrador.\n'),(378,'Comunicar oralmente de la búsqueda, apoyados en las notas tomadas con anterioridad.\n'),(379,'Elaborar generalizaciones sobre las respuestas de los animales a cambios regulares en el ambiente y plasmarlas en una producción escrita.\n'),(380,'Intercambiar ideas acerca de la diversidad de cambios que se producen en las plantas a lo largo de su vida y del año.\n'),(381,'Realizar varias observaciones directas de las mismas plantas a lo largo de un periodo y registrar datos sobre sus características y las condiciones ambientales. Organizar la información recabada en cuadros o fichas.\n'),(382,'Comunicar el resultado de cada observación y sistematizar en cuadros de doble entrada la información aportada por los grupos. Organizar los registros producidos a lo largo del período de estudio de las plantas, analizar los cambios ocurridos y establecer diversas relaciones.\n'),(383,'Formular preguntas sobre aspectos que quedaron pendientes, buscar información mediante la lectura de textos y/o la entrevista a idóneos en el tema.\n'),(384,'Elaborar generalizaciones sobre los cambios ocurridos en especies anuales y en especies perennes a lo largo del año y producir un texto con las ideas a las que arribaron.\n'),(385,'Intercambiar ideas acerca de diversos aspectos relacionados con el cuidado de la salud.\n'),(386,'Plantear preguntas y organizarlas (con la colaboración del/ la docente) de modo que orienten la búsqueda de información\nen una diversidad de fuentes.\n'),(387,'Participar activamente en la elaboración y realización de entrevistas a especialistas.\n'),(388,'Leer, interpretar y organizar información específica seleccionada por el/la docente\n'),(389,'Formular anticipaciones acerca de los cuidados personales para prevenir enfermedades y reflexionar sobre su importancia.\n'),(390,'Elaborar generalizaciones referidas a enfermedades contagiosas y no contagiosas y a las acciones de prevención y tratamiento.\n'),(391,'Dar a conocer e intercambiar sus ideas acerca de los cambios de estado que se estudiarán.\n'),(392,'Formular anticipaciones y realizar exploraciones acerca de las condiciones necesarias para el cambio de estado de\nmateriales sólidos y líquidos.\n'),(393,'Registrar y organizar la información sobre las características de los materiales antes y después someterlos a cambios\nde temperatura.\n'),(394,'Analizar la información registrada y elaborar generalizaciones sencillas sobre el fenómeno en estudio.\n'),(395,'Formular anticipaciones sobre posibles resultados al mezclar dos o más materiales.\n'),(396,'Realizar exploraciones acerca de la mezcla de distintos materiales y describir los resultados según la posibilidad de identificar o no los componentes de la mezcla.\n'),(397,'Formular anticipaciones sobre los métodos e instrumentos para separar los materiales de cada mezcla y fundamentar la elección apelando a las características de los materiales.\n'),(398,'Realizar las separaciones y contrastar con las anticipaciones. Organizar la información en cuadros de doble entrada para comunicarla al grupo.\n'),(399,'Elaborar generalizaciones sobre la diversidad en el tipo de mezclas y sobre los métodos para separar los materiales.\n'),(400,'Poner en común sus ideas sobre la diversidad de seres vivos que existieron en el pasado.\n'),(401,'Buscar información mediante la lectura de textos, la observación de videos, la realización de visitas y entrevistas que les permita distinguir a los dinosaurios de otros animales del     pasado, y profundizar el estudio de los dinosaurios.\n'),(402,'Sistematizar la información en cuadros o fichas y comunicar los resultados de la búsqueda.\n'),(403,'Elaborar, con la colaboración del docente, generalizaciones sobre la amplia diversidad de animales que habitaron en el pasado y las principales características de los dinosaurios.\n'),(404,'Comparar las imágenes con animales y plantas actuales y reconocer aspectos en común.\n'),(405,'Conocer a través del relato del docente sobre el parentesco  entre animales y plantas extinguidas y los actuales.\n'),(406,'Expresar y registrar sus ideas sobre las características del cielo diurno y nocturno, los astros presentes en el cielo y los instrumentos empleados por los astrónomos para estudiar el cielo\n'),(407,'Realizar observaciones directas (orientadas por el/la docente) y registros gráficos del cielo diurno.\n'),(408,'Comunicar los resultados de las observaciones y elaborar conclusiones sobre las permanencias, regularidades y cambios identificados en el cielo diurno.\n'),(409,'Buscar información a través de la lectura de textos sobre el cielo nocturno.\n'),(410,'Organizar y comunicar la información relevada.\n'),(411,'Realizar observaciones sistemáticas y registros del cielo nocturno.\n'),(412,'Comunicar y analizar la información registrada reconociendo aspectos comunes y diferencias en las observaciones realizadas.\n'),(413,'Elaborar conclusiones sobre las características del cielo nocturno.\n'),(414,'Realizar observaciones sistemáticas y registros de los cambios en la forma   en que se ve la luna a lo largo de un mes.\n'),(415,'Analizar y organizar los registros, confrontarlos con información aportada por libros o calendarios.\n'),(416,'Elaborar conclusiones sobre los cambios en la forma visible de la luna a lo largo de un mes.\n'),(417,'Observar las imágenes e identificar las que pertenecen a un mismo momento del día.\n'),(418,'Analizar las mismas imágenes utilizando información aportada por el/la docente y organizarlas según se trate del crepúsculo matutino o vespertino, fundamentando dicha organización\n'),(419,'Elaborar generalizaciones sobre las características de los crepúsculos reconociendo la posición del sol respecto del horizonte en cada caso, así como las variaciones en los colores del cielo.\n'),(420,'Contrastar las producciones elaboradas a lo largo de toda la secuencia y elaborar conclusiones.\n'),(421,'Formular preguntas sobre los aspectos a investigar.\n'),(422,'Buscar información mediante la lectura de textos y la observación de videos para dar respuesta a los interrogantes planteados, así como incluir nuevos aspectos.\n'),(423,'Organizar la información para comunicarla, mediante la producción de paneles con imágenes y textos elaborados grupalmente.\n'),(424,'Elaborar generalizaciones sobre las particularidades de diferentes tipos de   astros y la amplia diversidad de instrumentos empleados para su estudio.\n');

/*Table structure for table `objetivos_aprendizaje` */

DROP TABLE IF EXISTS `objetivos_aprendizaje`;

CREATE TABLE `objetivos_aprendizaje` (
  `ejes_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `objetivos_id` (`id`),
  KEY `FK_objetivos_curriculares_areas` (`ejes_id`),
  CONSTRAINT `FK_objetivos_aprendizaje_ejes` FOREIGN KEY (`ejes_id`) REFERENCES `ejes` (`id`),
  CONSTRAINT `FK_objetivos_curriculares_objetivos` FOREIGN KEY (`id`) REFERENCES `objetivos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivos_aprendizaje` */

insert  into `objetivos_aprendizaje`(`ejes_id`,`id`) values (22,48),(22,49),(22,50),(23,51),(23,52),(24,53),(24,54),(25,55),(26,56),(27,57),(27,58),(27,59),(27,60),(27,61),(27,62),(29,63),(30,64),(31,65),(31,66),(31,67),(31,68),(31,69),(31,70),(31,71),(31,72),(31,73),(32,74),(32,75),(32,76),(32,77),(32,78),(32,79),(33,80),(34,81),(35,82),(35,83),(35,84),(35,85),(35,86),(35,87),(35,88),(35,89),(36,90),(36,91),(36,92),(36,93),(36,94),(36,95),(36,96),(36,97),(36,98),(36,99),(36,100),(36,101),(36,102),(36,103),(36,104),(37,105),(37,106),(37,107),(37,108),(38,109),(38,110),(38,111),(38,112),(39,113),(39,114),(39,115),(39,116),(39,117),(50,118),(50,119),(50,120),(50,121),(50,122),(50,123),(50,124),(50,125),(50,126),(51,127),(51,128),(51,129),(51,130),(51,131),(51,132),(51,133),(51,134),(51,135),(51,136),(51,137),(52,138),(52,139),(52,140),(52,141),(52,142),(52,143),(52,144),(52,145),(52,146),(52,147),(53,148),(53,149),(53,150),(53,151),(53,152),(53,153),(53,154),(53,155),(53,156),(53,157),(54,158),(54,159),(54,160),(54,161),(54,162),(54,163),(54,164),(55,165),(55,166),(55,167),(55,168),(55,169),(55,170),(55,171),(55,172),(55,173),(55,174),(55,175),(56,176),(56,177),(56,178),(56,179),(56,180),(56,181),(56,182),(56,183),(56,184),(56,185),(56,186),(56,187),(57,188),(57,189),(57,190),(57,191),(57,192),(57,193),(57,194),(57,195),(57,196),(57,197),(58,198),(58,199),(58,200),(58,201),(58,202),(58,203),(58,204),(58,205),(58,206),(58,207),(58,208),(58,209),(58,210),(58,211),(58,212),(58,213),(59,214),(59,215),(59,216),(59,217),(59,218),(59,219),(59,220),(59,221),(59,222),(59,223),(59,224),(59,225),(59,226),(59,227),(60,228),(60,229),(60,230),(60,231),(60,232),(60,233),(60,234),(60,235),(60,236),(60,237),(60,238),(60,239),(60,240),(60,241),(61,242),(61,243),(61,244),(61,245),(61,246),(61,247),(61,248),(61,249),(61,250),(61,251),(61,252),(61,253),(62,254),(62,255),(62,256),(62,257),(62,258),(62,259),(62,260),(62,261),(62,262),(62,263),(62,264),(62,265),(62,266),(62,267),(62,268),(62,269),(63,270),(63,271),(63,272),(63,273),(63,274),(63,275),(63,276),(63,277),(63,278),(63,279),(63,280),(64,281),(64,282),(64,283),(64,284),(64,285),(64,286),(64,287),(64,288),(64,289),(64,290),(64,291),(64,292),(64,293),(64,294),(64,295),(64,296),(64,297),(65,298),(65,299),(65,300),(65,301),(65,302),(66,303),(66,304),(66,305),(66,306),(67,307),(67,308),(67,309),(67,310),(68,311),(68,312),(68,313),(68,314),(68,315),(69,316),(69,317),(69,318),(69,319),(69,320),(70,321),(70,322),(70,323),(71,324),(71,325),(72,326),(72,327),(72,328),(72,329),(72,330),(72,331),(73,332),(73,333),(73,334),(73,335),(73,374),(74,336),(74,337),(75,338),(75,339),(75,340),(75,341),(75,342),(76,343),(76,344),(76,345),(76,346),(76,347),(76,348),(77,349),(77,350),(77,351),(77,352),(77,353),(77,354),(77,355),(77,356),(78,357),(78,358),(78,359),(79,360),(79,361),(79,362),(80,363),(81,364),(81,365),(81,366),(81,367),(81,368),(82,369),(82,370),(82,371),(82,372),(82,373),(83,375),(83,376),(83,377),(84,378),(84,379),(85,380),(85,381),(85,382),(85,383),(85,384),(86,385),(86,386),(87,387),(87,388),(87,389),(87,390),(88,391),(88,392),(88,393),(88,394),(89,395),(90,396),(90,397),(90,398),(90,399),(90,401),(90,402),(90,403),(90,404),(90,405),(91,400),(92,406),(92,407),(92,408),(92,409),(92,410),(92,411),(92,412),(92,413),(92,414),(92,415),(92,416),(92,417),(92,418),(92,419),(92,420),(93,421),(93,422),(93,423),(93,424);

/*Table structure for table `objetivos_personalizados` */

DROP TABLE IF EXISTS `objetivos_personalizados`;

CREATE TABLE `objetivos_personalizados` (
  `id` int(11) NOT NULL,
  `seguimientos_personalizados_id` int(11) NOT NULL,
  `objetivo_personalizado_ejes_id` int(11) NOT NULL,
  `objetivo_relevancias_id` int(11) NOT NULL,
  `estimacion` date NOT NULL,
  `activo` tinyint(1) NOT NULL default '1',
  `fechaCreacion` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `fechaDesactivado` datetime default NULL COMMENT 'indica fecha en la que se desactivo el objetivo',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `objetivos_id` (`id`),
  KEY `FK_objetivos_personalizados_objetivo_ejes` (`objetivo_personalizado_ejes_id`),
  KEY `FK_objetivos_personalizados_objetivo_relevancia` (`objetivo_relevancias_id`),
  KEY `FK_objetivos_personalizados_seguimiento_personalizado` (`seguimientos_personalizados_id`),
  CONSTRAINT `FK_objetivos_personalizados_ejes` FOREIGN KEY (`objetivo_personalizado_ejes_id`) REFERENCES `objetivo_personalizado_ejes` (`id`),
  CONSTRAINT `FK_objetivos_personalizados_objetivos` FOREIGN KEY (`id`) REFERENCES `objetivos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_objetivos_personalizados_objetivo_relevancia` FOREIGN KEY (`objetivo_relevancias_id`) REFERENCES `objetivo_relevancias` (`id`),
  CONSTRAINT `FK_objetivos_personalizados_seguimiento_personalizado` FOREIGN KEY (`seguimientos_personalizados_id`) REFERENCES `seguimientos_personalizados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivos_personalizados` */

insert  into `objetivos_personalizados`(`id`,`seguimientos_personalizados_id`,`objetivo_personalizado_ejes_id`,`objetivo_relevancias_id`,`estimacion`,`activo`,`fechaCreacion`,`fechaDesactivado`) values (4,7,11,2,'2013-11-16',0,'2013-09-01 00:00:00','2013-10-24 03:33:19'),(9,7,5,3,'2013-10-30',1,'2013-09-01 00:00:00',NULL),(11,7,10,1,'2013-11-20',1,'2013-10-30 15:53:43',NULL),(12,7,18,3,'2014-03-12',1,'2013-10-31 18:32:23',NULL);

/*Table structure for table `paises` */

DROP TABLE IF EXISTS `paises`;

CREATE TABLE `paises` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) default NULL,
  `codigo` varchar(2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `paises` */

insert  into `paises`(`id`,`nombre`,`codigo`) values (1,'Argentina','AR'),(2,'Brasil','BR');

/*Table structure for table `parametro_x_controlador_pagina` */

DROP TABLE IF EXISTS `parametro_x_controlador_pagina`;

CREATE TABLE `parametro_x_controlador_pagina` (
  `parametros_id` int(11) NOT NULL,
  `controladores_pagina_id` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY  (`parametros_id`,`controladores_pagina_id`),
  KEY `FK_parametros_x_controladores_pagina_controladores_pagina` (`controladores_pagina_id`),
  CONSTRAINT `FK_parametros_x_controladores_pagina_controladores_pagina` FOREIGN KEY (`controladores_pagina_id`) REFERENCES `controladores_pagina` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_parametros_x_controladores_pagina_parametros` FOREIGN KEY (`parametros_id`) REFERENCES `parametros` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametro_x_controlador_pagina` */

insert  into `parametro_x_controlador_pagina`(`parametros_id`,`controladores_pagina_id`,`valor`) values (3,20,'15'),(5,1,'Comunidad de profesionales dedicados al trabajo para la ayuda de personas con capacidades diferentes.'),(5,2,'Identificarse como integrante de la comunidad de profesionales.'),(9,8,'1'),(9,20,'1'),(9,23,'1'),(11,1,'comunidad, discapacitados, seguimientos'),(11,2,'identificarse, login, iniciar sesion'),(12,1,'Comunidad de profesionales abocados a la ayuda de personas discapacitadas'),(12,2,'Autentificarse para ingresar a la comunidad');

/*Table structure for table `parametro_x_usuario` */

DROP TABLE IF EXISTS `parametro_x_usuario`;

CREATE TABLE `parametro_x_usuario` (
  `parametros_id` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  `valor` varchar(255) default NULL,
  PRIMARY KEY  (`parametros_id`,`usuarios_id`),
  KEY `FK_parametro_x_usuario_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_parametro_x_usuario_parametros` FOREIGN KEY (`parametros_id`) REFERENCES `parametros` (`id`),
  CONSTRAINT `FK_parametro_x_usuario_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametro_x_usuario` */

insert  into `parametro_x_usuario`(`parametros_id`,`usuarios_id`,`valor`) values (4,61,'1'),(4,63,'1'),(4,117,'1'),(4,118,'1'),(4,119,'1'),(4,121,'1');

/*Table structure for table `parametros` */

DROP TABLE IF EXISTS `parametros`;

CREATE TABLE `parametros` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(255) default NULL,
  `tipo` enum('string','numeric','boolean') NOT NULL default 'string',
  `namespace` varchar(60) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

/*Data for the table `parametros` */

insert  into `parametros`(`id`,`descripcion`,`tipo`,`namespace`) values (1,'para usar en metatags, firmas de mail, etc','string','NOMBRE_SITIO'),(3,'cantidad de fichas o elementos en los distintos listados del sitio','numeric','CANTIDAD_LISTADO'),(4,'activar o desactivar notificaciones por mail','boolean','NOTIFICACIONES_MAIL'),(5,'metatag description para el header de las vistas del sistema.','string','METATAG_DESCRIPTION'),(9,'Si el parametro esta desactivado entonces no se hace alta de moderacion.','boolean','ACTIVAR_MODERACIONES'),(11,'el campo keywords en los metatags de las vistas','string','METATAG_KEYWORDS'),(12,'la idea es que el title de las vistas tengan el nombre del sitio acompañado de la descripcion de este metatag','string','METATAG_TITLE'),(13,'Cantidad maxima de denuncias que tiene que recibir una entidad para ser descartada de los listados generales.','numeric','CANT_MAX_DENUNCIAS'),(14,'Mail de contacto para los integrantes y visitantes de la comunidad','string','EMAIL_SITIO_CONTACTO'),(16,'Cantidad de dias que permanecera activa una invitacion.','numeric','CANT_DIAS_EXPIRACION_INVITACION'),(17,'Cantidad de dias que se mantiene activo un link de password temporal generado desde el formulario de recuperar contraseña','numeric','CANT_DIAS_EXPIRACION_REC_PASS'),(18,'Plazo dentro del cual se permite editar una entrada antigua en un Seguimiento. Vencido el plazo la edición ya no es posible y todas las variables o unidades asociadas solo se eliminan logicamente, protegiendo el historial.','numeric','CANT_DIAS_EDICION_SEGUIMIENTOS');

/*Table structure for table `parametros_sistema` */

DROP TABLE IF EXISTS `parametros_sistema`;

CREATE TABLE `parametros_sistema` (
  `parametros_id` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY  (`parametros_id`),
  CONSTRAINT `FK_parametros_sistema_parametros` FOREIGN KEY (`parametros_id`) REFERENCES `parametros` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametros_sistema` */

insert  into `parametros_sistema`(`parametros_id`,`valor`) values (1,'SGPAPD'),(9,'1'),(13,'5'),(14,'matiasvelillamdq@gmail.com'),(16,'5'),(17,'2'),(18,'7');

/*Table structure for table `parametros_usuario` */

DROP TABLE IF EXISTS `parametros_usuario`;

CREATE TABLE `parametros_usuario` (
  `parametros_id` int(11) NOT NULL,
  `valorDefecto` varchar(255) NOT NULL COMMENT 'valor por defecto asignado al parametro cuando se asigna al usuario por primera vez',
  PRIMARY KEY  (`parametros_id`),
  CONSTRAINT `FK_parametros_usuario_parametros` FOREIGN KEY (`parametros_id`) REFERENCES `parametros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametros_usuario` */

insert  into `parametros_usuario`(`parametros_id`,`valorDefecto`) values (4,'1');

/*Table structure for table `perfiles` */

DROP TABLE IF EXISTS `perfiles`;

CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `perfiles` */

insert  into `perfiles`(`id`,`descripcion`) values (1,'administrador'),(2,'integrante activo'),(3,'integrante inactivo'),(4,'visitante'),(5,'moderador');

/*Table structure for table `personas` */

DROP TABLE IF EXISTS `personas`;

CREATE TABLE `personas` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(200) default NULL COMMENT 'max 50 car, Encriptado',
  `apellido` varchar(200) default NULL COMMENT 'max 50 car, Encriptado',
  `documento_tipos_id` int(11) default NULL,
  `numeroDocumento` int(8) default NULL,
  `sexo` char(1) default NULL,
  `fechaNacimiento` varchar(10) default NULL,
  `email` varchar(200) default NULL COMMENT 'max 50 car, Encriptado',
  `telefono` varchar(200) default NULL COMMENT 'max 30 car, Encriptado',
  `celular` varchar(200) default NULL COMMENT 'max 30 car, Encriptado',
  `fax` varchar(200) default NULL COMMENT 'max 30 car, Encriptado',
  `domicilio` varchar(200) default NULL COMMENT 'max 100 car, Encriptado',
  `instituciones_id` int(11) default NULL,
  `ciudades_id` int(11) default NULL,
  `ciudadOrigen` varchar(400) default NULL COMMENT 'max 150 car, Encriptado',
  `codigoPostal` varchar(80) default NULL COMMENT 'max 20 car, Encriptado',
  `empresa` varchar(200) default NULL COMMENT 'max 30 car, Encriptado',
  `universidad` varchar(200) default NULL COMMENT 'max 30 car, Encriptado',
  `secundaria` varchar(200) default NULL COMMENT 'max 30 car, Encriptado',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `numeroDocumento` (`numeroDocumento`),
  KEY `FK_personas` (`documento_tipos_id`),
  KEY `FK_personas_institucion` (`instituciones_id`),
  KEY `FK_personas_ciudades` (`ciudades_id`),
  CONSTRAINT `FK_personas_ciudades` FOREIGN KEY (`ciudades_id`) REFERENCES `ciudades` (`id`),
  CONSTRAINT `FK_personas_documento_tipos` FOREIGN KEY (`documento_tipos_id`) REFERENCES `documento_tipos` (`id`),
  CONSTRAINT `FK_personas_instituciones` FOREIGN KEY (`instituciones_id`) REFERENCES `instituciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=latin1;

/*Data for the table `personas` */

insert  into `personas`(`id`,`nombre`,`apellido`,`documento_tipos_id`,`numeroDocumento`,`sexo`,`fechaNacimiento`,`email`,`telefono`,`celular`,`fax`,`domicilio`,`instituciones_id`,`ciudades_id`,`ciudadOrigen`,`codigoPostal`,`empresa`,`universidad`,`secundaria`) values (61,'x\0y\ns…²«ýêœ\"§15','Cå“ƒ\"ö#®Å£ré',1,31821428,'m','1983-02-16','ÎÐcØ²QÉ²~}¬¶¢EÉÞx¶ÃÓ+DÆHw½†','°Táp²›Å“q€q`8','>	`ÊñªÝEüd‘}Nvù','¼*Q5ßËßr)‘','Õ€„\nìÒ#@rIW)Vh\"',33,1,'8yÛ±g¯vþKº_”wL','Œñö¹QÌÐñ]£t§è‘','>˜n4ùÃ*àë\"{','_Ó3ƒë‹.ÒSf†Æ¾x','øÇàêà#ÀÚÀŠ.êU'),(63,'øØÔÏ‘äTDòý=˜í\'š','«˜ìL’<“°ª<’,FØë',1,31821427,'m','1985-10-06','¡&“(Wxrl%ZoÙ¶Œ†Ëôø™å47w$Vtó(á','ˆ´‘´™[•žÑ·¬ª=ÀQ','I3]eÉmŸRZ/íXâ','YLB2R¨î•	Æ½‘?|','.ÑóEçYªá\"Åx÷ô',33,1,'Æ7\r‚lÁ\Z™ÊE>°q«','Œñö¹QÌÐñ]£t§è‘','6-Ó<C(%\0†¹{NpÐ','[8Q…+”\rûÁ£F¾JÜ+','r\n9ÜòÜ¾žJ¶TfcÚ:O'),(95,'Mirtaa','Gilardi',1,31821426,'m','2006-05-08','','91287319288',NULL,NULL,'sdfhsdkjh 2311',NULL,1,NULL,NULL,NULL,NULL,NULL),(117,'Evangelina','Monello',1,12345678,'f','1995-05-17','evangelinamonello@hotmail.com','21312312',NULL,NULL,'funesd 2q3',NULL,1,NULL,'7600',NULL,NULL,'asdads ad'),(118,'Ã‰zè3ìSä­:Šó$','«˜ìL’<“°ª<’,FØë',1,88888888,'m','1996-09-15','ëù?Q‘GF†í[Z¿—Œ¸ö‘ùÝˆøt¨Ø$ÿ\n','UuÂ¶–ë	¨3çd³',NULL,NULL,'*CøF&¦I§–­â\'„»”',33,1,NULL,'Œñö¹QÌÐñ]£t§è‘',NULL,NULL,'cFj°EìèWÃ^Õ¿çó'),(119,'þ] ¯G†TJl(«iWm','àeàºÚ‹ü@o­å+Óè',1,21871182,'m','1970-09-16','ãåX‘fâ=v_lký_²¥­ë:ÏÄ\"IË3‹ÆQ¼¡”ô',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(121,'R0µ\n-ão§î	øÐ¶å{¯Xjœ=e¿¨ª	ÔCs¤ÍòÆ','úIKo½,¨h@\0Ü2ŽÿÑ	Ž—QýÝbüFKHês',1,99912392,'m','1997-01-07','½œüaŸVÃ+«	šÿ/í^TeØ»Ç%ÆÊF','A:V«b=q\rCØÎÜS','Q\ZYéaÅk¤}\"}–+Ð','”\0ˆ]ûfˆ¼•à[S\nT5(eùå/×n™¶×','\'ËIWdV&é¼UŒPÆk²32»:cw-<™eßŠvã¢',33,1,'”Ôù(Úp¿#RÔ÷«dç~','Œñö¹QÌÐñ]£t§è‘','’¹ß|†M†I¸õ8,RV','.%§}’èwxuáü8â‹¥Î|ÜåÕ3Z¦Ïl…','ÜMkÉ÷©ûF<¢X'),(122,'N‹¥©d8–©Ë«2Ð¯M?¡','°õèW¶íÒB,ãònW¯',1,98789878,'m','1975-06-03',NULL,'ô(ÀÃåÊí%j?ù„¸',NULL,NULL,'yé”ù«5ÃêIw¥L:',33,1,NULL,NULL,NULL,NULL,NULL),(123,'julio ','sanchez',1,12312312,'m','2007-05-05',NULL,'1231',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL),(124,'Roberto Maximiliano','sanchez',1,1312312,'m','1998-05-17',NULL,'12312',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL),(125,NULL,NULL,1,31821429,NULL,NULL,'andres_delfino@hotmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(126,'ÆSøË<6¯vÑÛL§´‚®','°Ÿ8‚D|„kcéc+°†',1,29999666,'m','1982-10-15',NULL,'Q{ZRþž#¼ð0?©èõ¿W',NULL,NULL,NULL,33,1,NULL,NULL,NULL,NULL,NULL),(127,'u¼„\'xù¬ã\'Ÿ|h¾á','@¹Ž‘:È#ˆü ß\"R1‡',1,33888999,'f','1987-01-02',NULL,'ãdßo<<à|Ç˜Q\0a€',NULL,NULL,NULL,33,1,NULL,NULL,NULL,NULL,NULL),(128,'ûà&ÀøJË•Ïˆ\'¡G\n‹','§´‘%ö8Žæ²s-iM1€',1,31821231,'m','2008-03-03',NULL,'dÕìÒžž¾‚µ²ÇS\">',NULL,NULL,'-ÿ_N•‰¡ùvçB\ZõR,',NULL,1,NULL,NULL,NULL,NULL,NULL),(129,'ÎÀíŠUú(Çê«õ^i','‹Å¿‘@)BK9¿d',1,31234565,'f','2000-06-03',NULL,'yFøy\Z^ÆŒŽ<H',NULL,NULL,'ð¡Ñîë©gV¦@½W',33,2,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `practicas` */

DROP TABLE IF EXISTS `practicas`;

CREATE TABLE `practicas` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `practicas` */

insert  into `practicas`(`id`,`nombre`) values (1,'Grupal'),(2,'Individual'),(3,'Pareja');

/*Table structure for table `pregunta_x_opcion_x_seguimiento` */

DROP TABLE IF EXISTS `pregunta_x_opcion_x_seguimiento`;

CREATE TABLE `pregunta_x_opcion_x_seguimiento` (
  `preguntas_id` int(11) NOT NULL,
  `preguntas_opciones_id` int(11) NOT NULL,
  `seguimientos_id` int(11) NOT NULL,
  PRIMARY KEY  (`preguntas_id`,`preguntas_opciones_id`,`seguimientos_id`),
  KEY `FK_pregunta_x_opcion_x_seguimiento_seguimientos` (`seguimientos_id`),
  CONSTRAINT `FK_pregunta_x_opcion_x_seguimiento_preguntas` FOREIGN KEY (`preguntas_id`) REFERENCES `preguntas` (`id`),
  CONSTRAINT `FK_pregunta_x_opcion_x_seguimiento_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `pregunta_x_opcion_x_seguimiento` */

/*Table structure for table `preguntas` */

DROP TABLE IF EXISTS `preguntas`;

CREATE TABLE `preguntas` (
  `id` int(10) NOT NULL auto_increment,
  `descripcion` tinytext NOT NULL,
  `tipo` tinytext NOT NULL,
  `entrevistas_id` int(10) default NULL,
  `borradoLogico` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `FK_preguntas_x_entrevista` (`entrevistas_id`),
  CONSTRAINT `FK_preguntas_x_entrevista` FOREIGN KEY (`entrevistas_id`) REFERENCES `entrevistas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `preguntas` */

/*Table structure for table `preguntas_opciones` */

DROP TABLE IF EXISTS `preguntas_opciones`;

CREATE TABLE `preguntas_opciones` (
  `preguntas_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `borradoLogico` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `FK_preguntas_opciones_preguntas` (`preguntas_id`),
  CONSTRAINT `FK_preguntas_opciones_preguntas` FOREIGN KEY (`preguntas_id`) REFERENCES `preguntas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `preguntas_opciones` */

/*Table structure for table `preguntas_simples_respuestas` */

DROP TABLE IF EXISTS `preguntas_simples_respuestas`;

CREATE TABLE `preguntas_simples_respuestas` (
  `preguntas_id` int(11) NOT NULL,
  `respuesta` text,
  `seguimientos_id` int(11) NOT NULL,
  PRIMARY KEY  (`preguntas_id`,`seguimientos_id`),
  KEY `FK_preguntas_simples_respuestas_seguimientos` (`seguimientos_id`),
  CONSTRAINT `FK_preguntas_simples_respuestas_preguntas` FOREIGN KEY (`preguntas_id`) REFERENCES `preguntas` (`id`),
  CONSTRAINT `FK_preguntas_simples_respuestas_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `preguntas_simples_respuestas` */

/*Table structure for table `privacidad` */

DROP TABLE IF EXISTS `privacidad`;

CREATE TABLE `privacidad` (
  `usuarios_id` int(11) NOT NULL,
  `email` enum('comunidad','publico') default 'publico',
  `telefono` enum('comunidad','privado') default 'comunidad',
  `celular` enum('comunidad','privado') default 'comunidad',
  `fax` enum('comunidad','privado') default 'comunidad',
  `curriculum` enum('comunidad','privado') default 'comunidad',
  PRIMARY KEY  (`usuarios_id`),
  CONSTRAINT `FK_privacidad usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `privacidad` */

insert  into `privacidad`(`usuarios_id`,`email`,`telefono`,`celular`,`fax`,`curriculum`) values (61,'publico','privado','comunidad','comunidad','comunidad'),(63,'comunidad','privado','comunidad','privado','privado'),(117,'publico','comunidad','comunidad','comunidad','comunidad'),(118,'publico','comunidad','comunidad','comunidad','comunidad'),(119,'publico','comunidad','comunidad','comunidad','comunidad'),(121,'publico','comunidad','comunidad','comunidad','comunidad');

/*Table structure for table `provincias` */

DROP TABLE IF EXISTS `provincias`;

CREATE TABLE `provincias` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) default NULL,
  `paises_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
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
  `publico` tinyint(1) unsigned NOT NULL default '0',
  `activoComentarios` tinyint(1) unsigned NOT NULL default '1',
  `descripcionBreve` varchar(100) default NULL,
  `keywords` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_publicaciones_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_publicaciones_fichas_abstractas` FOREIGN KEY (`id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_publicaciones_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `publicaciones` */

insert  into `publicaciones`(`id`,`usuarios_id`,`publico`,`activoComentarios`,`descripcionBreve`,`keywords`) values (1,63,1,1,'esta es una descripcion tan breve no ? 123 132','uno dos tres cuatro cinco cinco seis siete'),(5,63,1,1,'a sdfads fadsfa sdfasd qdsad asd','asdfads asdfadsf asdfsdf'),(8,63,1,1,'asdfasdf adsf asdfds fdsf 33','1 2 3 4'),(9,63,1,1,'weterwtwertwert','wr'),(10,63,1,1,'wret erwtwer twret','134324324324'),(11,63,0,1,'sdafadsfadsf adsfadf adfs dfdf adsfads','123');

/*Table structure for table `respuestas` */

DROP TABLE IF EXISTS `respuestas`;

CREATE TABLE `respuestas` (
  `id` int(10) NOT NULL auto_increment,
  `respuesta` tinytext NOT NULL,
  `preguntas_id` int(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_respuestas` (`preguntas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `respuestas` */

/*Table structure for table `reviews` */

DROP TABLE IF EXISTS `reviews`;

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL COMMENT 'reviewer. optional. hCard.',
  `publico` tinyint(1) unsigned NOT NULL default '0',
  `descripcionBreve` varchar(100) NOT NULL COMMENT 'Se utiliza tambien en el MetaTag description en vista ampliada.',
  `keywords` varchar(255) NOT NULL COMMENT 'Meta tag keywords en vista ampliada. (en el hreview en la ficha se situa en la parte de tags)',
  `activoComentarios` tinyint(1) unsigned NOT NULL default '1',
  `itemType` enum('product','business','event','person','place','website','url') default NULL COMMENT 'This optional property provides the type of the item being reviewed',
  `itemName` varchar(255) NOT NULL COMMENT 'ITEM must have at a minimum the name',
  `itemEventSummary` varchar(255) default NULL COMMENT 'an event item must have the "summary" subproperty inside the respective hCalendar "vevent"',
  `itemUrl` varchar(500) default NULL COMMENT 'should provide at least one URI ("url") for the item',
  `rating` double default NULL COMMENT 'The rating is a fixed point integer (one decimal point of precision) from 1.0 to 5.0',
  `fuenteOriginal` varchar(500) default NULL COMMENT 'URL de la fuente de donde se extrajo informacion',
  PRIMARY KEY  (`id`),
  KEY `FK_reviews_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_reviews_fichas_abstractas` FOREIGN KEY (`id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_reviews_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `reviews` */

insert  into `reviews`(`id`,`usuarios_id`,`publico`,`descripcionBreve`,`keywords`,`activoComentarios`,`itemType`,`itemName`,`itemEventSummary`,`itemUrl`,`rating`,`fuenteOriginal`) values (3,63,1,'sdfasdfklasj fhaklsjdfh lakjdsh flakjdsfh akldshf aldskjfh askjldf  123','sdf sdf sd sdf sfds  123',1,'product','Feria arte Sheraton 123','Feria artesanal Sheraton Mar del Plata, la 4ta de mar del plata','http://www.ldfkjdsk2123lfj.com',NULL,'http://www.lasdkfjda123slkfj.com');

/*Table structure for table `seguimiento_scc_x_objetivo_aprendizaje` */

DROP TABLE IF EXISTS `seguimiento_scc_x_objetivo_aprendizaje`;

CREATE TABLE `seguimiento_scc_x_objetivo_aprendizaje` (
  `objetivos_aprendizaje_id` int(11) NOT NULL,
  `seguimientos_scc_id` int(11) NOT NULL,
  `objetivo_relevancias_id` int(11) NOT NULL,
  `estimacion` date NOT NULL,
  `activo` tinyint(1) NOT NULL default '1',
  `fechaCreacion` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `fechaDesactivado` date default NULL,
  PRIMARY KEY  (`objetivos_aprendizaje_id`,`seguimientos_scc_id`),
  KEY `FK_seguimiento_x_objetivo_curricular_objetivo_relevancias` (`objetivo_relevancias_id`),
  KEY `FK_seguimientos_scc` (`seguimientos_scc_id`),
  CONSTRAINT `FK_seguimientos_scc` FOREIGN KEY (`seguimientos_scc_id`) REFERENCES `seguimientos_scc` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_scc_x_objetivo_aprendizaje` FOREIGN KEY (`objetivos_aprendizaje_id`) REFERENCES `objetivos_aprendizaje` (`id`),
  CONSTRAINT `FK_seguimiento_x_objetivo_curricular_objetivo_relevancias` FOREIGN KEY (`objetivo_relevancias_id`) REFERENCES `objetivo_relevancias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_scc_x_objetivo_aprendizaje` */

/*Table structure for table `seguimiento_x_entrevista` */

DROP TABLE IF EXISTS `seguimiento_x_entrevista`;

CREATE TABLE `seguimiento_x_entrevista` (
  `seguimientos_id` int(11) NOT NULL,
  `entrevistas_id` int(11) NOT NULL,
  `fechaHora` timestamp NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`seguimientos_id`,`entrevistas_id`),
  KEY `FK_entrevista_x_seguimiento_entrevistas` (`entrevistas_id`),
  CONSTRAINT `FK_entrevista_x_seguimiento_entrevistas` FOREIGN KEY (`entrevistas_id`) REFERENCES `entrevistas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_entrevista_x_seguimiento_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_x_entrevista` */

/*Table structure for table `seguimiento_x_unidad` */

DROP TABLE IF EXISTS `seguimiento_x_unidad`;

CREATE TABLE `seguimiento_x_unidad` (
  `unidades_id` int(11) NOT NULL,
  `seguimientos_id` int(11) NOT NULL,
  `fechaHora` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`unidades_id`,`seguimientos_id`),
  UNIQUE KEY `NewIndex1` (`unidades_id`,`seguimientos_id`),
  KEY `FK_seguimiento_x_unidades2` (`seguimientos_id`),
  CONSTRAINT `FK_seguimiento_x_unidad_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_x_unidad_unidades` FOREIGN KEY (`unidades_id`) REFERENCES `unidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_x_unidad` */

insert  into `seguimiento_x_unidad`(`unidades_id`,`seguimientos_id`,`fechaHora`) values (1,6,'2013-04-23 20:08:47'),(1,7,'2013-06-01 20:46:23'),(1,10,'2013-09-09 23:53:00'),(1,11,'2013-09-09 23:55:26'),(1,22,'2013-10-02 17:48:14'),(1,23,'2013-10-02 17:52:39'),(5,7,'2013-11-03 01:34:59'),(6,7,'2013-11-04 20:18:35'),(13,7,'2013-11-12 21:08:48');

/*Table structure for table `seguimientos` */

DROP TABLE IF EXISTS `seguimientos`;

CREATE TABLE `seguimientos` (
  `id` int(11) NOT NULL auto_increment,
  `discapacitados_id` int(11) NOT NULL,
  `frecuenciaEncuentros` varchar(100) default NULL,
  `diaHorario` varchar(100) default NULL,
  `practicas_id` int(11) default NULL,
  `usuarios_id` int(11) NOT NULL,
  `antecedentes` text COMMENT 'encriptado',
  `pronostico` text COMMENT 'encriptado',
  `fechaCreacion` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `estado` enum('activo','detenido') NOT NULL default 'activo',
  PRIMARY KEY  (`id`),
  KEY `FK_seguimientos_personas` (`discapacitados_id`),
  KEY `FK_seguimientos` (`usuarios_id`),
  KEY `FK_seguimientos_practica` (`practicas_id`),
  CONSTRAINT `FK_seguimientos_discapacitados` FOREIGN KEY (`discapacitados_id`) REFERENCES `discapacitados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimientos_practicas` FOREIGN KEY (`practicas_id`) REFERENCES `practicas` (`id`),
  CONSTRAINT `FK_seguimientos_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos` */

insert  into `seguimientos`(`id`,`discapacitados_id`,`frecuenciaEncuentros`,`diaHorario`,`practicas_id`,`usuarios_id`,`antecedentes`,`pronostico`,`fechaCreacion`,`estado`) values (3,126,'3 dias','l m v 12 hs',2,61,NULL,NULL,'2013-04-23 19:16:03','activo'),(4,127,'1 vez por mes','1er lunes de cada mes',2,61,NULL,NULL,'2013-04-23 19:48:26','activo'),(6,127,'nunca','domingos 7 am',1,61,NULL,NULL,'2013-04-23 20:08:46','activo'),(7,128,'3 veces por semana','12hs',2,63,'hdaksdh askjd aksdh aksdh aksdh askjdh askdh aan\nasdlkajdklasjd\nasdasjdlajsldjasldjasldj',NULL,'2013-06-01 20:46:22','activo'),(8,128,'das dasd as','a dsa da s',3,63,'lkajdlaksjd alskdj \nasdlkjasdlkjasdlasjd \nasdlkaj dlaskjd  11\n\n',NULL,'2013-08-21 21:49:14','activo'),(10,128,'vxv','df sdfds',1,61,NULL,NULL,'2013-09-09 23:53:00','activo'),(11,128,'lkjghkjlh','adsfasdf',2,118,NULL,NULL,'2013-09-09 23:55:25','activo'),(22,122,'asd','asd',2,63,NULL,NULL,'2013-10-02 17:48:14','activo'),(23,128,'adasd','adsasd',1,63,'dfadsfadsfadsf',NULL,'2013-10-02 17:52:39','activo');

/*Table structure for table `seguimientos_personalizados` */

DROP TABLE IF EXISTS `seguimientos_personalizados`;

CREATE TABLE `seguimientos_personalizados` (
  `id` int(11) NOT NULL,
  `diagnosticos_personalizado_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_seguimientos_personalizados_diagnostico_personalizado` (`diagnosticos_personalizado_id`),
  CONSTRAINT `FK_seguimientos_personalizados_diagnostico_pers` FOREIGN KEY (`diagnosticos_personalizado_id`) REFERENCES `diagnosticos_personalizado` (`id`),
  CONSTRAINT `FK_seguimientos_personalizados_seguimientos` FOREIGN KEY (`id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos_personalizados` */

insert  into `seguimientos_personalizados`(`id`,`diagnosticos_personalizado_id`) values (6,6),(7,7),(10,10),(11,11),(22,22);

/*Table structure for table `seguimientos_scc` */

DROP TABLE IF EXISTS `seguimientos_scc`;

CREATE TABLE `seguimientos_scc` (
  `id` int(11) NOT NULL,
  `diagnosticos_scc_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_seguimientos_scc_diagnostico_scc` (`diagnosticos_scc_id`),
  CONSTRAINT `FK_seguimientos_scc_diagnostico_scc` FOREIGN KEY (`diagnosticos_scc_id`) REFERENCES `diagnosticos_scc` (`id`),
  CONSTRAINT `FK_seguimientos_scc_seguimientos` FOREIGN KEY (`id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos_scc` */

insert  into `seguimientos_scc`(`id`,`diagnosticos_scc_id`) values (3,3),(4,4),(8,8),(23,23);

/*Table structure for table `software` */

DROP TABLE IF EXISTS `software`;

CREATE TABLE `software` (
  `id` int(11) NOT NULL auto_increment,
  `usuarios_id` int(11) NOT NULL,
  `categorias_id` int(11) NOT NULL,
  `publico` tinyint(1) unsigned NOT NULL,
  `activoComentarios` tinyint(1) unsigned NOT NULL,
  `descripcionBreve` varchar(100) default NULL,
  `enlaces` varchar(500) default NULL COMMENT 'por si se quieren adjuntar mirrors de enlaces a descarga directa',
  PRIMARY KEY  (`id`),
  KEY `FK_software_usuarios` (`usuarios_id`),
  KEY `FK_software_categorias` (`categorias_id`),
  CONSTRAINT `FK_software_categorias` FOREIGN KEY (`categorias_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `FK_software_fichas_abstractas` FOREIGN KEY (`id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_software_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

/*Data for the table `software` */

insert  into `software`(`id`,`usuarios_id`,`categorias_id`,`publico`,`activoComentarios`,`descripcionBreve`,`enlaces`) values (12,63,2,1,1,'esta es una descripcion muy breve del primer software que se da de alta en el catalogo ','<a target=\'_blank\' href=\'http://www.fsdlfkjdskj.com\'>http://www.fsdlfkjdskj.com</a><br><a target=\'_blank\' href=\'http://www.aslkdjaslkdjaskldj.com/dalskjdaklsj?asljdk=123\'>http://www.aslkdjaslkdjaskldj.com/dalskjdaklsj?asljdk=123</a><br><a target=\'_blank\' href=\'http://www.asdfadsfasdf.com\'>http://www.asdfadsfasdf.com</a><br>'),(13,63,2,1,1,'asdfdsfadsf dfsas df asdfa sdfadsf dsfasdfadsfads sf adsfaf adsfadsf df',NULL),(14,63,2,1,1,'asfadsfadsf',NULL),(15,63,2,1,1,'asdfdasfasf',NULL),(19,63,2,1,1,'asdfadsf',NULL),(20,63,2,1,1,'adsfads','<a target=\'_blank\' href=\'http://www.ldksakldjas.com\'>http://www.ldksakldjas.com</a><br>'),(21,63,3,1,1,'dfqdsdsafasd fkadsjf laÃ±dskjf aÃ±ldskjf Ã±ladskj fÃ±ladsjf ladsÃ±jf kladsjfadsklfj ','<a target=\'_blank\' href=\'http://www.askdhkjfds.com\'>http://www.askdhkjfds.com</a><br>');

/*Table structure for table `unidades` */

DROP TABLE IF EXISTS `unidades`;

CREATE TABLE `unidades` (
  `id` int(11) NOT NULL auto_increment,
  `usuarios_id` int(11) default NULL COMMENT 'si es distinto de null indica una unidad creada por integrante para seguimientos personalizados',
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `preCargada` tinyint(1) NOT NULL default '0' COMMENT 'pre cargada en el sistema, solo puede editarse desde administrador',
  `fechaHora` timestamp NULL default CURRENT_TIMESTAMP,
  `asociacionAutomatica` tinyint(1) NOT NULL default '0' COMMENT 'se asocia automaticamente en la creacion de un seguimiento',
  `borradoLogico` tinyint(1) unsigned NOT NULL default '0',
  `tipoEdicion` enum('regular','esporadica') NOT NULL default 'regular',
  `fechaBorradoLogico` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_unidades_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_unidades_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

/*Data for the table `unidades` */

insert  into `unidades`(`id`,`usuarios_id`,`nombre`,`descripcion`,`preCargada`,`fechaHora`,`asociacionAutomatica`,`borradoLogico`,`tipoEdicion`,`fechaBorradoLogico`) values (1,NULL,'Información Básica','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed condimentum pharetra ligula, at accumsan turpis condimentum sit amet. Ut quis odio nibh. Proin accumsan tellus id tellus fringilla dictum. Quisque vel aliquam justo, et posuere dolor. Etiam malesuada, nisl eu accumsan condimentum, lorem nisl rutrum eros, eu scelerisque mi lectus non justo. Pellentesque adipiscing consectetur nibh eget rhoncus. Integer iaculis nulla pharetra, semper tellus sed, luctus dui.\r\n\r\nNulla consectetur ipsum nec blandit ultrices. In nec ipsum et est aliquam lacinia. Donec sollicitudin blandit elit, vitae vulputate dui porta nec. Maecenas nec iaculis tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis interdum, enim non posuere aliquet, eros purus tristique dui, malesuada pharetra leo velit et libero. Donec vulputate tincidunt leo, ut faucibus tellus pharetra vitae. Ut blandit felis ac diam elementum ultrices. Donec eget dui nisl. Nulla vestibulum, nibh id tincidunt adipiscing, magna sem vulputate nunc, a pellentesque dolor sem sed erat. Etiam sed mauris neque. Aenean in nisl auctor, hendrerit tellus et, rhoncus dolor. Donec sed nulla nec augue vestibulum ullamcorper quis vel diam. Morbi dui eros, sodales ut mollis sit amet, rhoncus ac risus. Phasellus lobortis aliquam turpis, et suscipit felis laoreet a. Maecenas purus diam, egestas et condimentum vitae, feugiat eu tellus.',0,'2012-01-01 00:00:00',1,0,'regular',NULL),(5,63,'Estado Animico','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed condimentum pharetra ligula, at accumsan turpis condimentum sit amet. Ut quis odio nibh. Proin accumsan tellus id tellus fringilla dictum. Quisque vel aliquam justo, et posuere dolor. Etiam malesuada, nisl eu accumsan condimentum, lorem nisl rutrum eros, eu scelerisque mi lectus non justo. Pellentesque adipiscing consectetur nibh eget rhoncus. Integer iaculis nulla pharetra, semper tellus sed, luctus dui.\r\n\r\nNulla consectetur ipsum nec blandit ultrices. In nec ipsum et est aliquam lacinia. Donec sollicitudin blandit elit, vitae vulputate dui porta nec. Maecenas nec iaculis tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis interdum, enim non posuere aliquet, eros purus tristique dui, malesuada pharetra leo velit et libero. Donec vulputate tincidunt leo, ut faucibus tellus pharetra vitae. Ut blandit felis ac diam elementum ultrices. Donec eget dui nisl. Nulla vestibulum, nibh id tincidunt adipiscing, magna sem vulputate nunc, a pellentesque dolor sem sed erat. Etiam sed mauris neque. Aenean in nisl auctor, hendrerit tellus et, rhoncus dolor. Donec sed nulla nec augue vestibulum ullamcorper quis vel diam. Morbi dui eros, sodales ut mollis sit amet, rhoncus ac risus. Phasellus lobortis aliquam turpis, et suscipit felis laoreet a. Maecenas purus diam, egestas et condimentum vitae, feugiat eu tellus.',0,'2013-04-07 04:39:43',0,0,'regular',NULL),(6,63,'Comportamiento en Clase','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed condimentum pharetra ligula, at accumsan',0,'2013-04-07 07:25:21',0,0,'regular',NULL),(10,63,'probando C','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed condimentum pharetra ligula, at accumsan turpis condimentum sit amet. Ut quis odio nibh. Proin accumsan tellus id tellus fringilla dictum. Quisque vel aliquam justo, et posuere dolor. Etiam malesuada, nisl eu accumsan condimentum, lorem nisl rutrum eros, eu scelerisque mi lectus non justo. Pellentesque adipiscing consectetur nibh eget rhoncus. Integer iaculis nulla pharetra, semper tellus sed, luctus dui.\n\nNulla consectetur ipsum nec blandit ultrices. In nec ipsum et est aliquam lacinia. Donec sollicitudin blandit elit, vitae vulputate dui porta nec. Maecenas nec iaculis tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis interdum, enim non posuere aliquet, eros purus tristique dui, malesuada pharetra leo velit et libero. Donec vulputate tincidunt leo, ut faucibus tellus pharetra vitae. Ut blandit felis ac diam elementum ultrices. Donec eget dui nisl. Nulla vestibulum, nibh id tincidunt adipiscing, magna sem vulputate nunc, a pellentesque dolor sem sed erat. Etiam sed mauris neque. Aenean in nisl auctor, hendrerit tellus et, rhoncus dolor. Donec sed nulla nec augue vestibulum ullamcorper quis vel diam. Morbi dui eros, sodales ut mollis sit amet, rhoncus ac risus. Phasellus lobortis aliquam turpis, et suscipit felis laoreet a. Maecenas purus diam, egestas et condimentum vitae, feugiat eu tellus. ',0,'2013-10-30 01:41:46',0,0,'regular',NULL),(13,63,'TEST Figura Humana','este es un test para medir bleble \nsadflaskjdf asdkljf klsdj flasdj fldsjf ldsjfldsjf sad\nfadslfj asldkjflk alskdj alsdj wqoeuqw08e zxupioc qwpeqwlj xlkcj \nqwwq0eqo\n\nalkj czxlkjc lkjqwe oqwoeuqweoiu xlckj xlzkcj lkajd laskjd qw\nasldkja ldj\n\n\nasldkj lk2wj qldj lskdj alskjd wldj ldjlwjd alwkjd awd',0,'2013-10-31 17:30:45',0,0,'esporadica',NULL),(14,NULL,'precargada 1','dsadsfasdfsdfasdfadsf',1,'2013-11-12 20:59:27',0,0,'regular',NULL),(17,NULL,'NAP Lengua','Núcleos de Aprendizaje Prioritarios de Lengua',1,'2013-11-17 20:51:06',0,0,'esporadica',NULL),(19,NULL,'NAP Matemática','Núcleos de Aprendizaje Prioritario de Matemática',1,'2013-11-17 20:52:19',0,0,'esporadica',NULL);

/*Table structure for table `usuario_passwords_temporales` */

DROP TABLE IF EXISTS `usuario_passwords_temporales`;

CREATE TABLE `usuario_passwords_temporales` (
  `usuarios_id` int(11) NOT NULL,
  `contraseniaNueva` varchar(64) default NULL,
  `token` varchar(100) default NULL,
  `fecha` timestamp NULL default CURRENT_TIMESTAMP,
  `email` varchar(50) NOT NULL,
  UNIQUE KEY `token` (`token`),
  KEY `FK_usuario_passwords_temporales_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_usuario_passwords_temporales_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `usuario_passwords_temporales` */

/*Table structure for table `usuario_x_invitado` */

DROP TABLE IF EXISTS `usuario_x_invitado`;

CREATE TABLE `usuario_x_invitado` (
  `usuarios_id` int(11) NOT NULL,
  `invitados_id` int(11) NOT NULL,
  `relacion` varchar(500) default NULL,
  `fecha` timestamp NULL default CURRENT_TIMESTAMP,
  `estado` enum('aceptada','pendiente') default 'pendiente',
  `token` varchar(200) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  PRIMARY KEY  (`usuarios_id`,`invitados_id`),
  UNIQUE KEY `token` (`token`),
  KEY `FK_usuario_x_invitado_invitados` (`invitados_id`),
  CONSTRAINT `FK_usuario_x_invitado_invitados` FOREIGN KEY (`invitados_id`) REFERENCES `invitados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_usuario_x_invitado_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `usuario_x_invitado` */

insert  into `usuario_x_invitado`(`usuarios_id`,`invitados_id`,`relacion`,`fecha`,`estado`,`token`,`nombre`,`apellido`) values (63,117,'asdfadsfjdsfads\nfdsf\nasdf\ndsf','2012-09-15 08:22:10','aceptada','7dde8ba57614529a7b679a4d3b876f55','Evangelina','Monello'),(63,118,'sadfsadf\ndasfalsdkfjaksldfjal','2012-09-15 08:38:52','aceptada','dfb59af3275910cd55683cdecdd7b237','Eduardo','Velilla'),(119,125,'amigo','2012-10-02 23:19:06','pendiente','cad0b01f963640587738c2a83bc96ca3','Andres','Delfino');

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `sitioWeb` varchar(200) default NULL COMMENT '50 varchar original, campo encriptado',
  `especialidades_id` int(11) default NULL,
  `perfiles_id` int(11) NOT NULL,
  `cargoInstitucion` varchar(40) default NULL,
  `biografia` text COMMENT 'campo encriptado',
  `nombre` varchar(255) NOT NULL COMMENT 'campo encriptado',
  `contrasenia` char(64) default NULL,
  `fechaAlta` timestamp NULL default CURRENT_TIMESTAMP,
  `activo` tinyint(1) unsigned NOT NULL default '1' COMMENT 'si 0 entonces esta suspendido',
  `invitacionesDisponibles` int(3) default '5',
  `universidadCarrera` varchar(50) default NULL,
  `carreraFinalizada` tinyint(1) default NULL,
  `moderado` tinyint(1) NOT NULL default '0',
  `urlTokenKey` varchar(200) NOT NULL COMMENT 'Para generar links accedidos sin que el usuario haya iniciado sesion. Como por ejemplo links en los mails etc.',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `personas_id` (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `FK_usuarios` (`perfiles_id`),
  KEY `FK_usuarios_especialidades` (`especialidades_id`),
  CONSTRAINT `FK_usuarios_especialidades` FOREIGN KEY (`especialidades_id`) REFERENCES `especialidades` (`id`),
  CONSTRAINT `FK_usuarios_perfiles` FOREIGN KEY (`perfiles_id`) REFERENCES `perfiles` (`id`),
  CONSTRAINT `FK_usuarios_personas` FOREIGN KEY (`id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`sitioWeb`,`especialidades_id`,`perfiles_id`,`cargoInstitucion`,`biografia`,`nombre`,`contrasenia`,`fechaAlta`,`activo`,`invitacionesDisponibles`,`universidadCarrera`,`carreraFinalizada`,`moderado`,`urlTokenKey`) values (61,'ªEƒOC”ù0]åÃL!¨•A½Ùèâ oÄ…Â]ËÓ',17,1,'jefe','Ý˜ÿf¾1³š¤…~7Å¹îx\nT5(eùå/×n™¶×','rrio','e10adc3949ba59abbe56e057f20f883e','2011-06-28 02:14:43',1,4,'dddd',0,0,'51c50f52501cfc75dc1110dde6700aee'),(63,NULL,14,1,'Director',NULL,'matias.velilla','e10adc3949ba59abbe56e057f20f883e','2011-09-05 20:18:35',1,-2,'Lic en Sistemas',0,0,'51c50f52501cfc75dc1110dde6700aee'),(117,NULL,14,2,NULL,NULL,'Evangelina_Monello_117','e10adc3949ba59abbe56e057f20f883e','2012-09-18 19:32:05',1,5,NULL,1,0,'c4b931bff69c2aac5844e6fc6a355fef'),(118,NULL,17,2,'jefe catedra',NULL,'eduardo_velilla','e10adc3949ba59abbe56e057f20f883e','2012-09-18 07:36:19',1,5,NULL,0,0,'51c50f52501cfc75dc1110dde6700aee'),(119,NULL,NULL,2,NULL,NULL,'andresdelfino','e10adc3949ba59abbe56e057f20f883e','2012-09-18 21:29:08',1,4,NULL,0,0,'51c50f52501cfc75dc1110dde6700aee'),(121,'http://www.alfareria.com',14,2,'limpiador de alfonbras','soy encargado de limpiezas de inodoros y bidets','sanchesdebusta','e10adc3949ba59abbe56e057f20f883e','2012-09-25 21:11:57',1,5,'alfareria',0,0,'241febc7a31a7bab14869759420c1b38');

/*Table structure for table `variable_cualitativa_modalidades` */

DROP TABLE IF EXISTS `variable_cualitativa_modalidades`;

CREATE TABLE `variable_cualitativa_modalidades` (
  `id` int(11) NOT NULL auto_increment,
  `variables_id` int(11) NOT NULL,
  `modalidad` varchar(50) NOT NULL,
  `orden` int(2) unsigned default '1',
  `borradoLogico` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `FK_variable_modalidades` (`variables_id`),
  CONSTRAINT `FK_variable_modalidades` FOREIGN KEY (`variables_id`) REFERENCES `variables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;

/*Data for the table `variable_cualitativa_modalidades` */

insert  into `variable_cualitativa_modalidades`(`id`,`variables_id`,`modalidad`,`orden`,`borradoLogico`) values (10,35,'azul',1,0),(11,35,'negro',2,0),(16,35,'blanco',0,0),(17,35,'rojo',0,0),(18,43,'Opcion 1',0,0),(19,43,'Opcion 2',2,0),(20,43,'Opcion 3',3,0),(21,44,'No logrado',1,0),(22,44,'Medianamente logrado',2,0),(23,44,'Bien logrado',3,0),(24,44,'Muy bien logrado',4,0),(25,44,'Excelentemente logrado',5,0),(26,45,'No logrado',1,0),(27,45,'Medianamente logrado',2,0),(28,45,'Bien logrado',3,0),(29,45,'Muy bien logrado',4,0),(30,45,'Excelentemente logrado',5,0),(31,46,'No logrado',1,0),(32,46,'Medianamente logrado',2,0),(33,46,'Bien logrado',3,0),(34,46,'Muy bien logrado',4,0),(35,46,'Excelentemente logrado',5,0),(36,47,'No logrado',1,0),(37,47,'Medianamente logrado',2,0),(38,47,'Bien logrado',3,0),(39,47,'Muy bien logrado',4,0),(40,47,'Excelentemente logrado',5,0),(41,48,'No logrado',1,0),(42,48,'Medianamente logrado',2,0),(43,48,'Bien logrado',3,0),(44,48,'Muy bien logrado',4,0),(45,48,'Excelentemente logrado',5,0),(46,49,'No logrado',1,0),(47,49,'Medianamente logrado',2,0),(48,49,'Bien logrado',3,0),(49,49,'Muy bien logrado',4,0),(50,49,'Excelentemente logrado',5,0);

/*Table structure for table `variables` */

DROP TABLE IF EXISTS `variables`;

CREATE TABLE `variables` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('VariableNumerica','VariableTexto','VariableCualitativa') NOT NULL,
  `descripcion` text,
  `unidad_id` int(11) NOT NULL,
  `fechaHora` timestamp NULL default CURRENT_TIMESTAMP,
  `borradoLogico` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `FK_variables` (`unidad_id`),
  CONSTRAINT `FK_variables_unidades` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;

/*Data for the table `variables` */

insert  into `variables`(`id`,`nombre`,`tipo`,`descripcion`,`unidad_id`,`fechaHora`,`borradoLogico`) values (1,'Block de notas','VariableTexto',NULL,1,NULL,0),(3,'Una prueba','VariableTexto','asdas\r\ndasd\r\nasd\r\nasdasdasdas',6,'2013-04-07 07:26:19',0),(4,'adsfadsf 1234','VariableNumerica','adfsadsfads 1234',5,'2013-04-07 07:26:58',0),(6,'una nueva variable de texto1','VariableTexto','asdlkasjdlas\nasldjasldjasldjlakdjalsdjalsdj\nasldjasldkjaskld\n\nalskdjalsdjalskdjlaskjdlaskd1',5,'2013-04-11 02:42:03',0),(7,'nueva variable ','VariableTexto','adsfkladsjf lakdsj fadsf\ndasflj adslfkj adsf\nadlfkj adslfjadlskjf \ndalfkj adslfk aldskjf ads\n',5,'2013-04-11 03:03:27',0),(8,'nueva variable numerica','VariableNumerica','sdfads',5,'2013-04-11 03:08:08',0),(35,'TEST PIPIPI Fotografia 1','VariableCualitativa','colores que ve cuando se le muestra la figura blebleble',5,'2013-05-04 21:22:43',0),(39,'esta la cree hoy 30 asi q aparece','VariableTexto','joojojojooo',5,'2013-10-30 02:45:44',0),(40,'numerica de hoy 30 !','VariableNumerica','esta tambien la cree hoy 30 es numerica y viene sin numero',5,'2013-10-30 02:49:23',0),(41,'Ficha 1','VariableTexto','sadlfk jasdkflj sadklfj askldf jasd\nfasdlkfj asldfj alsdkjf aslkdjf as\ndfadslfj asdlfkj asdlkfj asldk fjasldkfj adsklfj \nasldfkj askldfj asdlkfj asldkjf \nasdlfk jasdfklj asdlkfj asdlkfj asdlfkj asdf',13,'2013-10-31 17:31:24',0),(42,'Ficha 2','VariableNumerica','asdflkj adsfkj asldkfj asldkfj a\nsd fklads jflkadsj fklasjd flkasj dfklasdjflasjdk ',13,'2013-10-31 17:31:47',0),(43,'Ficha 3','VariableCualitativa','haslkdjaslkd jaslkdj aslkdj aslkdj aslkdj asldjasld ',13,'2013-10-31 17:39:45',0),(44,'Interés por ampliar su conocimiento  a través de la lectura dentro y fuera de la escuela','VariableCualitativa','Interés por ampliar su conocimiento y acceder a otros mundos posibles a través de la lectura dentro y fuera de la escuela.\n\nEn la evolución de la variable los valores logrados tienen implicitamente la forma y la calidad en la solución o el alcance del resultado.\nBien logrado se puede tomar como equivalente a un 8 en una escala numérica. \nBien logrado significa que alcanza el valor esperado, por consiguiente los valores superiores expresan una superación.\n',17,'2013-11-17 22:13:46',0),(45,'Respeto y el interés por las producciones orales y escritas de otros','VariableCualitativa','En la evolución de la variable los valores logrados tienen implicitamente la forma y la calidad en la solución o el alcance del resultado.\nBien logrado se puede tomar como equivalente a un 8 en una escala numérica. \nBien logrado significa que alcanza el valor esperado, por consiguiente los valores superiores expresan una superación.',17,'2013-11-17 22:25:31',0),(46,'Interés por expresar y compartir experiencias','VariableCualitativa','Interés por expresar y compartir experiencias, ideas y sentimientos a través de intercambios orales y escritos.\n\nEn la evolución de la variable los valores logrados tienen implicitamente la forma y la calidad en la solución o el alcance del resultado.\nBien logrado se puede tomar como equivalente a un 8 en una escala numérica. \nBien logrado significa que alcanza el valor esperado, por consiguiente los valores superiores expresan una superación.\n',17,'2013-11-17 22:29:28',0),(47,'Confianza en las propias posibilidades para resolver problemas y formularse interrogantes.','VariableCualitativa','En la evolución de la variable los valores logrados tienen implicitamente la forma y la calidad en la solución o el alcance del resultado.\nBien logrado se puede tomar como equivalente a un 8 en una escala numérica. \nBien logrado significa que alcanza el valor esperado, por consiguiente los valores superiores expresan una superación.',19,'2013-11-17 22:35:56',0),(48,'Defender sus propios puntos de vista','VariableCualitativa','Disposición para defender sus propios puntos de vista, considerar ideas y opiniones de otros, debatirlas y elaborar conclusiones.\nEn la evolución de la variable los valores logrados tienen implicitamente la forma y la  calidad en la solución o el alcance del resultado.\nBien logrado se puede tomar como equivalente a un 8 en una escala numérica. \nBien logrado significa que alcanza el valor esperado, por consiguiente los valores superiores expresan una superación.',19,'2013-11-17 22:38:18',0),(49,'Exploración de la validez de afirmaciones propias y ajenas','VariableCualitativa','En la evolución de la variable los valores logrados tienen implicitamente la forma y la calidad en la solución o el alcance del resultado.\nBien logrado se puede tomar como equivalente a un 8 en una escala numérica. \nBien logrado significa que alcanza el valor esperado, por consiguiente los valores superiores expresan una superación.',19,'2013-11-17 22:40:45',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
