/*
SQLyog Enterprise - MySQL GUI v8.18 
MySQL - 5.5.24-log : Database - tesis
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tesis` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci */;

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
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=latin1;

/*Data for the table `acciones` */

insert  into `acciones`(`id`,`controladores_pagina_id`,`accion`,`grupo`,`activo`) values (1,1,'index',5,1),(2,1,'redireccion404',5,1),(3,1,'sitioOffline',5,1),(4,1,'sitioEnConstruccion',5,1),(5,1,'ajaxError',5,1),(6,2,'index',5,1),(7,2,'procesar',5,1),(8,2,'redireccion404',5,1),(9,3,'index',5,1),(10,3,'formulario',5,1),(11,3,'procesar',5,1),(12,3,'redireccion404',5,1),(13,4,'index',2,1),(14,4,'redireccion404',2,1),(15,5,'index',4,1),(16,5,'redireccion404',4,1),(17,6,'index',3,1),(18,6,'redireccion404',3,1),(19,6,'procesar',3,1),(20,6,'formulario',3,1),(21,6,'listado',3,1),(22,7,'index',1,1),(23,7,'redireccion404',1,1),(26,8,'index',5,1),(27,8,'nuevaInstitucion',3,1),(28,8,'listadoInstituciones',5,1),(31,8,'procesar',3,1),(32,9,'index',4,1),(33,9,'formulario',4,1),(34,9,'procesar',4,1),(35,9,'redireccion404',4,1),(36,8,'masInstituciones',4,1),(37,8,'redireccion404',5,1),(38,8,'ampliarInstitucion',4,1),(39,8,'editarInstitucion',3,1),(40,2,'logout',4,1),(41,10,'procesarEspecialidad',1,1),(42,10,'index',1,1),(43,10,'listarEspecialidades',1,1),(44,10,'nuevaEspecialidad',1,1),(45,10,'editarEspecialidad',1,1),(46,10,'eliminarEspecialidad',1,1),(47,10,'verificarUsoDeEspecialidad',1,1),(49,11,'nuevaCategoria',1,1),(50,11,'editarCategoria',1,1),(51,11,'listarCategoria',1,1),(52,11,'eliminarCategoria',1,1),(53,11,'index',1,1),(54,11,'procesarCategoria',1,1),(55,9,'modificarPrivacidadCampo',4,1),(56,12,'index',3,1),(59,14,'nuevoSeguimiento',3,1),(62,12,'buscarDiscapacitados',3,1),(63,14,'procesarSeguimiento',3,1),(64,8,'buscarInstituciones',4,1),(65,5,'descargarArchivo',4,1),(67,14,'index',3,1),(68,14,'redireccion404',3,1),(69,12,'redireccion404',3,1),(70,13,'index',3,1),(71,13,'procesar',3,1),(73,13,'agregar',3,1),(74,13,'redireccion404',3,1),(75,14,'listar',3,1),(76,14,'buscarSeguimientos',3,1),(77,13,'modificar',3,1),(78,13,'ver',3,1),(79,15,'index',2,1),(80,15,'redireccion404',2,1),(81,15,'listarModeracionesPendientes',2,1),(82,15,'procesarModeracion',2,1),(83,15,'procesarPersona',2,1),(84,14,'eliminar',3,1),(85,16,'index',2,1),(86,16,'redireccion404',2,1),(87,16,'procesar',2,1),(88,17,'redireccion404',2,1),(89,17,'index',2,1),(90,17,'procesar',2,1),(91,17,'form',2,1),(92,18,'index',2,1),(93,18,'redireccion404',2,1),(94,18,'procesar',2,1),(95,18,'form',2,1),(96,18,'cambiarPerfil',1,1),(97,18,'cerrarCuenta',1,1),(98,18,'crear',1,1),(99,18,'vistaImpresion',1,1),(101,18,'exportar',1,1),(103,9,'cerrarCuenta',4,1),(104,20,'index',4,1),(105,20,'redireccion404',4,1),(106,20,'misPublicaciones',3,1),(109,20,'guardarPublicacion',3,1),(110,20,'guardarReview',3,1),(111,20,'procesar',3,1),(112,20,'galeriaFotos',3,1),(113,20,'fotosProcesar',3,1),(114,20,'formFoto',3,1),(115,20,'galeriaArchivos',3,1),(116,20,'archivosProcesar',3,1),(117,20,'formArchivo',3,1),(118,20,'galeriaVideos',3,1),(119,20,'videosProcesar',3,1),(120,20,'formVideo',3,1),(121,20,'crearPublicacionForm',3,1),(122,20,'modificarPublicacionForm',3,1),(123,20,'crearReviewForm',3,1),(124,20,'modificarReviewForm',3,1),(125,1,'video',5,1),(126,14,'ver',3,1),(127,14,'cambiarEstadoSeguimientos',3,1),(128,14,'verAdjuntos',3,1),(129,14,'editarAntecedentes',3,1),(130,14,'procesarAntecedentes',3,1),(131,14,'formAdjuntarFoto',3,1),(132,14,'formAdjuntarVideo',3,1),(133,14,'formAdjuntarArchivo',3,1),(134,14,'formEditarAdjunto',3,1),(135,14,'procesarAdjunto',3,1),(136,14,'formModificarSeguimiento',3,1),(137,14,'guardarSeguimiento',3,1),(138,20,'verPublicacion',4,1),(139,20,'verReview',4,1),(141,21,'index',1,1),(142,21,'procesar',1,1),(143,21,'form',1,1),(144,21,'listarModeraciones',2,1),(145,8,'misInstituciones',3,1),(146,1,'provinciasByPais',5,1),(147,1,'ciudadesByProvincia',5,1),(148,8,'guardar',3,1),(149,8,'masMisInstituciones',3,1),(150,16,'listarModeraciones',2,1),(151,16,'form',2,1),(152,16,'listarSolicitudes',2,1),(153,11,'verificarUsoDeCategoria',1,1),(154,22,'index',1,1),(155,22,'procesar',1,1),(156,22,'form',1,1),(157,22,'listarModeraciones',2,1),(158,23,'index',4,1),(159,23,'misAplicaciones',3,1),(160,23,'crearSoftwareForm',3,1),(161,23,'modificarSoftwareForm',3,1),(162,23,'guardarSoftware',3,1),(163,23,'procesar',3,1),(164,23,'galeriaFotos',3,1),(165,23,'fotosProcesar',3,1),(166,23,'formFoto',3,1),(167,23,'galeriaArchivos',3,1),(168,23,'archivosProcesar',3,1),(169,23,'formArchivo',3,1),(170,23,'verSoftware',4,1),(171,23,'listarCategoria',4,1),(172,23,'redireccion404',4,1),(173,24,'index',5,1),(174,24,'ampliarInstitucion',5,1),(175,24,'procesar',5,1),(176,25,'index',5,1),(177,25,'verPublicacion',5,1),(178,25,'verReview',5,1),(179,25,'procesar',5,1),(180,7,'procesar',1,1),(181,7,'form',1,1),(182,26,'index',5,1),(183,26,'listarCategoria',5,1),(184,26,'verSoftware',5,1),(185,26,'procesar',5,1),(186,14,'listarAreasPorCiclos',3,1),(187,14,'listarCiclosPorNiveles',3,1),(188,14,'procesarDiagnostico',3,1),(189,14,'editarDiagnostico',3,1),(190,20,'denunciar',4,1),(191,8,'denunciar',4,1),(192,23,'denunciar',4,1),(193,16,'listarDenuncias',2,1),(194,16,'procesarDenuncias',2,1),(195,21,'procesarDenuncias',2,1),(196,21,'listarDenuncias',2,1),(197,22,'listarDenuncias',2,1),(198,22,'procesarDenuncias',2,1),(199,1,'desactivarNotificacionesMail',5,1),(200,7,'listarParametrosUsuario',1,1),(201,2,'formRecuperarContrasenia',5,1),(202,2,'procesarRecuperarContrasenia',5,1),(203,27,'procesarNivel',1,1),(204,27,'listarNiveles',1,1),(205,27,'formularioNivel',1,1),(206,27,'procesarCiclo',1,1),(207,27,'listarCiclos',1,1),(208,27,'formularioCiclo',1,1),(209,27,'procesarArea',1,1),(210,27,'listarAreas',1,1),(211,27,'formularioArea',1,1),(212,27,'procesarEje',1,1),(213,27,'listarEjes',1,1),(214,27,'formularioEje',1,1),(215,27,'procesarObjetivoAprendizaje',1,1),(216,27,'listarObjetivosAprendizaje',1,1),(217,27,'formularioObjetivoAprendizaje',1,1),(218,14,'listarEjesPorArea',3,1),(219,28,'index',3,1),(220,28,'formCrearUnidad',3,1),(221,28,'guardarUnidad',3,1),(222,28,'procesar',3,1),(223,28,'formEditarUnidad',3,1),(224,28,'eliminar',3,1),(225,29,'index',3,1),(226,29,'formCrearVariable',3,1),(227,29,'formEditarVariable',3,1),(228,29,'procesar',3,1),(229,29,'guardar',3,1),(230,29,'eliminar',3,1),(231,29,'eliminarModalidad',3,1),(232,14,'administrarObjetivos',3,1),(233,30,'index',3,1),(234,14,'procesar',3,1),(235,14,'procesarObjetivos',3,1),(236,14,'formObjetivo',3,1),(237,14,'guardarObjetivo',3,1),(238,14,'listarObjetivosAprendizajePorEje',3,1),(239,14,'verObjetivo',3,1),(240,14,'editarPronostico',3,1),(241,14,'procesarPronostico',3,1),(242,30,'procesar',3,1);

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
  `nombre` varchar(255) NOT NULL,
  `nombreServidor` varchar(500) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipoMime` varchar(50) NOT NULL,
  `tamanio` int(11) DEFAULT NULL,
  `fechaAlta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `orden` tinyint(4) unsigned DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `tipo` enum('cv','adjunto','antecedentes') NOT NULL DEFAULT 'adjunto',
  PRIMARY KEY (`id`),
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ciclos_id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_areas_ciclos` (`ciclos_id`),
  CONSTRAINT `FK_areas_ciclo` FOREIGN KEY (`ciclos_id`) REFERENCES `ciclos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `areas` */

insert  into `areas`(`id`,`ciclos_id`,`descripcion`) values (1,1,'Matemática'),(2,1,'Sociales'),(3,1,'Lengua');

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
  `urlToken` char(50) DEFAULT NULL COMMENT 'es lo que va a parar a la url. tiene indice porque se realizan busquedas por este campo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlToken` (`urlToken`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `categorias` */

insert  into `categorias`(`id`,`nombre`,`descripcion`,`urlToken`) values (1,'Discapacidad Visual','descripcion discapacidad visual, descripcion discapacidad visual descripcion discapacidad visual descripcion discapacidad visual descripcion discapacidad visual','discapacidad-visual'),(2,'Discapacidad Auditiva','descripcion discapacidad auditiva categoria\ndescripcion discapacidad auditiva categoria\ndescripcion discapacidad auditiva categoria','discapacidad-auditiva'),(3,'Discapacidad Motora','descripcion categoria discapacidad motora','discapacidad-motora'),(4,'Autismo','descripcion categoria autismo','autismo'),(5,'Sindrome de Down','descripcion categoria sindrome de down\ndescripcion categoria sindrome de down\ndescripcion categoria sindrome de down\ndescripcion categoria sindrome de down','sindrome-de-down');

/*Table structure for table `ciclos` */

DROP TABLE IF EXISTS `ciclos`;

CREATE TABLE `ciclos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `niveles_id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ciclos_niveles` (`niveles_id`),
  CONSTRAINT `FK_ciclos_niveles` FOREIGN KEY (`niveles_id`) REFERENCES `niveles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `ciclos` */

insert  into `ciclos`(`id`,`niveles_id`,`descripcion`) values (1,1,'Primer Ciclo'),(4,1,'Segundo Ciclo');

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

insert  into `ciudades`(`id`,`nombre`,`provincia_id`,`latitud`,`longitud`) values (1,'Mar del Plata',1,NULL,NULL),(2,'Necochea',1,NULL,NULL),(3,'Río de Janeiro',3,NULL,NULL);

/*Table structure for table `comentarios` */

DROP TABLE IF EXISTS `comentarios`;

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reviews_id` int(11) DEFAULT NULL,
  `publicaciones_id` int(11) DEFAULT NULL,
  `software_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descripcion` text NOT NULL,
  `valoracion` double unsigned DEFAULT '0' COMMENT '-1 quiere decir que no se emitio valoracion',
  `usuarios_id` int(11) DEFAULT NULL COMMENT 'En el caso de que un usuario registrado valore se crea la referencia para el vCard',
  `nombreApellido` varchar(100) NOT NULL DEFAULT 'Anonimo',
  PRIMARY KEY (`id`),
  KEY `FK_comentarios_usuarios` (`usuarios_id`),
  KEY `FK_comentarios_archivos` (`software_id`),
  KEY `FK_comentarios_publicaciones` (`publicaciones_id`),
  KEY `FK_comentarios_reviews` (`reviews_id`),
  CONSTRAINT `FK_comentarios_publicaciones` FOREIGN KEY (`publicaciones_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_comentarios_reviews` FOREIGN KEY (`reviews_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_comentarios_software` FOREIGN KEY (`software_id`) REFERENCES `software` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_comentarios_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

/*Data for the table `comentarios` */

insert  into `comentarios`(`id`,`reviews_id`,`publicaciones_id`,`software_id`,`fecha`,`descripcion`,`valoracion`,`usuarios_id`,`nombreApellido`) values (3,NULL,NULL,12,'2012-08-15 02:29:01','dfasdfdsfasdfadsf\nasdfads\nfasdfasdfasdfadsf\nasdfasdfadsf',4,63,'Anonimo'),(5,NULL,NULL,12,'2012-08-15 02:33:23','sdfadsfasdfasdfasdfadsf',0,63,'Anonimo'),(6,NULL,NULL,12,'2012-08-16 17:27:18','dfadfsadsfasdfsdf',2,63,'Anonimo'),(7,NULL,NULL,12,'2012-08-16 17:27:34','fasdfasdfasdfadsfasdf',1,63,'Anonimo'),(8,NULL,NULL,14,'2012-08-16 21:12:58','fdasdfadsfdsafadsf',1,63,'Anonimo'),(9,NULL,NULL,14,'2012-08-16 21:15:43','asdfasdfadsfadsf\ndasfads',5,63,'Anonimo'),(10,NULL,NULL,14,'2012-08-16 21:17:03','afsdfadsfads',1,63,'Anonimo'),(11,NULL,NULL,14,'2012-08-16 21:17:45','sadDSS',5,63,'Anonimo'),(12,NULL,NULL,12,'2012-08-16 21:23:09','dafsdfasdf',3,63,'Anonimo'),(13,NULL,8,NULL,'2012-08-27 06:16:07','lkjlkjlkjkl',0,63,'Anonimo');

/*Table structure for table `controladores_pagina` */

DROP TABLE IF EXISTS `controladores_pagina`;

CREATE TABLE `controladores_pagina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controlador` varchar(200) NOT NULL COMMENT 'Formado por [modulo]_[controlador]. ''system'' se utiliza para referencia a TODO el sistema. No debe asociarse a la tabla acciones',
  PRIMARY KEY (`id`),
  UNIQUE KEY `controlador` (`controlador`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

/*Data for the table `controladores_pagina` */

insert  into `controladores_pagina`(`id`,`controlador`) values (17,'admin_accionesPerfil'),(11,'admin_categoria'),(10,'admin_especialidad'),(4,'admin_index'),(16,'admin_instituciones'),(27,'admin_objetivosAprendizaje'),(7,'admin_parametros'),(15,'admin_personas'),(21,'admin_publicaciones'),(22,'admin_software'),(18,'admin_usuarios'),(9,'comunidad_datosPersonales'),(5,'comunidad_index'),(8,'comunidad_instituciones'),(6,'comunidad_invitaciones'),(20,'comunidad_publicaciones'),(23,'comunidad_software'),(1,'index_index'),(24,'index_instituciones'),(2,'index_login'),(25,'index_publicaciones'),(3,'index_registracion'),(26,'index_software'),(30,'seguimientos_entradas'),(12,'seguimientos_index'),(13,'seguimientos_personas'),(14,'seguimientos_seguimientos'),(28,'seguimientos_unidades'),(29,'seguimientos_variables');

/*Table structure for table `denuncias` */

DROP TABLE IF EXISTS `denuncias`;

CREATE TABLE `denuncias` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fichas_abstractas_id` int(11) DEFAULT NULL,
  `instituciones_id` int(11) DEFAULT NULL,
  `mensaje` varchar(500) DEFAULT NULL,
  `usuarios_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `razon` enum('informacion_falsa','contenido_inapropiado','propiedad_intelectual','spam') DEFAULT NULL,
  PRIMARY KEY (`id`),
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos` */

insert  into `diagnosticos`(`id`,`descripcion`) values (2,NULL),(3,NULL),(4,'no mueve los brazos.'),(6,'asdsdaa'),(7,'una descripcion'),(8,'adasda'),(9,'este chico esta en primer ciclo y va a una escuela especial y bleblebleblebleble'),(10,NULL),(11,NULL),(22,NULL),(23,'adsfadsfadsf');

/*Table structure for table `diagnosticos_personalizado` */

DROP TABLE IF EXISTS `diagnosticos_personalizado`;

CREATE TABLE `diagnosticos_personalizado` (
  `id` int(11) NOT NULL,
  `codigo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_diagnosticos_personalizado_diagnosticos` FOREIGN KEY (`id`) REFERENCES `diagnosticos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos_personalizado` */

insert  into `diagnosticos_personalizado`(`id`,`codigo`) values (6,'222'),(7,'un código'),(10,NULL),(11,NULL),(22,NULL);

/*Table structure for table `diagnosticos_scc` */

DROP TABLE IF EXISTS `diagnosticos_scc`;

CREATE TABLE `diagnosticos_scc` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
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
  PRIMARY KEY (`diagnosticos_scc_id`,`ejes_id`),
  KEY `FK_diagnosticos_scc_x_ejes_ejes` (`ejes_id`),
  CONSTRAINT `FK_diagnosticos_scc_x_ejes_diagnostico_scc` FOREIGN KEY (`diagnosticos_scc_id`) REFERENCES `diagnosticos_scc` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_diagnosticos_scc_x_ejes_ejes` FOREIGN KEY (`ejes_id`) REFERENCES `ejes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos_scc_x_ejes` */

insert  into `diagnosticos_scc_x_ejes`(`diagnosticos_scc_id`,`ejes_id`,`estadoInicial`) values (4,3,'cuenta los primos '),(8,2,'asdasdasd'),(8,3,'sadasdasd adsa asd asd 1'),(8,4,'sdkaljs dlkasj dlajs dlakj ds 1'),(8,5,'asd adas dad !!!! sadjkal 1'),(23,5,'sdfasdfadsfasfasdfa');

/*Table structure for table `discapacitados` */

DROP TABLE IF EXISTS `discapacitados`;

CREATE TABLE `discapacitados` (
  `id` int(11) NOT NULL,
  `nombreApellidoPadre` varchar(255) DEFAULT NULL COMMENT 'max 60, encriptado',
  `nombreApellidoMadre` varchar(255) DEFAULT NULL COMMENT 'max 60, encriptado',
  `fechaNacimientoPadre` date DEFAULT NULL,
  `fechaNacimientoMadre` date DEFAULT NULL,
  `ocupacionPadre` varchar(500) DEFAULT NULL COMMENT 'encriptado',
  `ocupacionMadre` varchar(500) DEFAULT NULL COMMENT 'encriptado',
  `nombreHermanos` varchar(500) DEFAULT NULL COMMENT 'encriptado',
  `usuarios_id` int(11) unsigned DEFAULT NULL COMMENT 'el user que lo dio de alta en el sistema',
  PRIMARY KEY (`id`),
  UNIQUE KEY `personas_id` (`id`),
  CONSTRAINT `FK_discapacitados_personas` FOREIGN KEY (`id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `discapacitados` */

insert  into `discapacitados`(`id`,`nombreApellidoPadre`,`nombreApellidoMadre`,`fechaNacimientoPadre`,`fechaNacimientoMadre`,`ocupacionPadre`,`ocupacionMadre`,`nombreHermanos`,`usuarios_id`) values (95,'Eduardo Alfredo Velillaa','Evangelina monelloo','2005-06-04','2001-04-16','dsklfjdsfjdsf\nsdfdskljfldskjflskdjflskjdf\nsdflksjdflkdsjflksd\nsdlfksjdflksjdflkjsdddd','dsklfjdsfjdsf\nsdfdskljfldskjflskdjflskjdf\nsdflksjdflkdsjflksd\nsdlfksjdflksjdflkjsddddddd','dsklfjdsfjdsf 233\nsdfdskljfldskjflskdjflskjdf 211\nsdflksjdflkdsjflksd 322\nsdlfksjdflksjdflkjsd 122',61),(122,'¨Ö,âoEêÙ¼ëÿÂ\ZK°0 ˆ`àÛ	ž¼K×üÚª•','|S¦øðe\ré¼‚`³,Ï¼f\Z›Œ¨³vÇ‚´<\0@vÀ2','1995-02-16','2010-02-04','ªÁ¤Àmó§àÎi‰”Üi­+:¤Ê0‹‰(bšiúîûùVØ¼ãã=IÆß\ZG\rk	Ý–ÀG‡«†®üÏrjY¦qdÛƒc¨¼ÿbL#åÖæÁE	`%Œí›þdé¼³Ø†Hžx~€›?T­zÙcbQTú®òÉ\ZU€åˆ[œ¶ÿu’Œ	wsÑ¸fV &Èhá¡ê©j[×o+_æËŽoÆ\ZÈ¹Uy¥“úÙGœ<|…ß5ˆgú9nç°ÿ		¨úü08!ÁÌùüõ16%ù—ÚÀÊFGI¡l!¦¦\ZÄÀÄ=\rH–«hŠGÿ»ÏG¶×6Š€™3ÞbOZ‘µ¦(Ö!é©ÃSD\'/ªìr%”4ÔŒ%”N®@ÎÚ<¾>œn=ŒÝï¡ÍÞ‰ˆäBy×o<Î[ù%Ô)vÇ‡³‰ŠžpÚU´äè†²LÄþS¬lE]åé†ï]‘','ªÁ¤Àmó§àÎi‰”Üi­+:¤Ê0‹‰(bšiúîûùVØ¼ãã=IÆß\ZG\rk	Ý–ÀG‡«†®üÏrjY¦qdÛƒc¨¼ÿbL#åÖæÁE	`%Œí›þdé¼³Ø†Hžx~€›?T­zÙcbQTú®òÉ\ZU€åˆ[œ¶ÿu’Œ	wsÑ¸fV &Èhá¡ê©j[×o+_æËŽoÆ\ZÈ¹Uy¥“úÙGœ<|…ß5ˆgú9nç°ÿ		¨úü08!ÁÌùüõ16%ù—ÚÀÊFGI¡l!¦¦\ZÄÀÄ=\rH–«hŠGÿ»ÏG¶×6Š€™3ÞbOZ‘µ¦(Ö!é©ÃSD\'/ªìr%”4ÔŒ%”N®@ÎÚ<¾>œn=ŒÝï¡ÍÞ‰ˆäBy×o<Î[ù%Ô)vÇ‡³‰ŠžpÚU´äè†²LÄþS¬lE]åé†ï]‘','k…œ³UØ¸ÁÃMÖjÕ³¡¢E1¬>£ž%£Ê!ëº',61),(123,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(124,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(126,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,61),(127,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,61),(128,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(129,'ÈÏó6PÃÛEBQ™/t<f','TÙ ×û«kë*Æ14\'\Z','2003-05-02','2009-04-06','ÿäO°åqjŽjÙÿyÖë','««‹¦I‘|TýTƒOBÙ¤','…³?ª‚-É3!9md+Î',63);

/*Table structure for table `discapacitados_moderacion` */

DROP TABLE IF EXISTS `discapacitados_moderacion`;

CREATE TABLE `discapacitados_moderacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL COMMENT 'max 50, encriptado',
  `apellido` varchar(200) DEFAULT NULL COMMENT 'max 50, encriptado',
  `documento_tipos_id` int(11) DEFAULT NULL,
  `numeroDocumento` int(8) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `fechaNacimiento` varchar(10) DEFAULT NULL,
  `email` varchar(200) NOT NULL COMMENT 'max 50, encriptado',
  `telefono` varchar(180) DEFAULT NULL COMMENT 'max 30, encriptado',
  `celular` varchar(180) DEFAULT NULL COMMENT 'max 30, encriptado',
  `fax` varchar(180) DEFAULT NULL COMMENT 'max 30, encriptado',
  `domicilio` varchar(300) DEFAULT NULL COMMENT 'max 100, encriptado',
  `instituciones_id` int(11) DEFAULT NULL,
  `ciudades_id` int(11) DEFAULT NULL,
  `ciudadOrigen` varchar(350) DEFAULT NULL COMMENT 'max 150, encriptado',
  `codigoPostal` varchar(180) DEFAULT NULL COMMENT 'max 30, encriptado',
  `empresa` varchar(180) DEFAULT NULL COMMENT 'max 30, encriptado',
  `universidad` varchar(180) DEFAULT NULL COMMENT 'max 30, encriptado',
  `secundaria` varchar(180) DEFAULT NULL COMMENT 'max 30, encriptado',
  `nombreApellidoPadre` varchar(220) DEFAULT NULL COMMENT 'max 60, encriptado',
  `nombreApellidoMadre` varchar(220) DEFAULT NULL COMMENT 'max 60, encriptado',
  `fechaNacimientoPadre` date DEFAULT NULL,
  `fechaNacimientoMadre` date DEFAULT NULL,
  `ocupacionPadre` varchar(500) DEFAULT NULL COMMENT 'max 30, encriptado',
  `ocupacionMadre` varchar(500) DEFAULT NULL COMMENT 'max 30, encriptado',
  `nombreHermanos` varchar(500) DEFAULT NULL COMMENT 'max 30, encriptado',
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

/*Table structure for table `ejes` */

DROP TABLE IF EXISTS `ejes`;

CREATE TABLE `ejes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  `contenidos` text,
  `areas_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ejes_curriculares_area` (`areas_id`),
  CONSTRAINT `FK_ejes_curriculares_area` FOREIGN KEY (`areas_id`) REFERENCES `areas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `ejes` */

insert  into `ejes`(`id`,`descripcion`,`contenidos`,`areas_id`) values (2,'Números naturales','leer y escribir numeros de 100 a 250.',1),(3,'Números primos','saber cuales son los numeros primos',1),(4,'Abecedario','saber el abecedario completo',3),(5,'Historia Argentina 1','saber acerca de los grandes fundadores de la nacion argentina',2);

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

/*Data for the table `embed_videos` */

insert  into `embed_videos`(`id`,`fichas_abstractas_id`,`seguimientos_id`,`codigo`,`orden`,`titulo`,`descripcion`,`origen`,`urlKey`) values (20,1,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,NULL,NULL,'YouTube','18eb4fa91b3a41298a9202c94a950d08'),(22,8,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,'dfsafadsf 1','adsfasdfads 1','YouTube','2145a2e07b71444338a1963e56a0881d'),(23,8,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,'sdfasfadsfasd 2','sadfadsfadsf 222','YouTube','352a52072e85aca6360afd0b6a41ca56'),(24,11,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,NULL,NULL,'YouTube','a32851fa34ebf9e6005da190b49b3faf'),(25,3,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,NULL,NULL,'YouTube','000032141f2a32417d18030dad61781c'),(26,NULL,8,'http://www.youtube.com/watch?v=FFOzayDpWoI',NULL,NULL,NULL,'YouTube','528299d90bee1972bc1728b7b43f514c');

/*Table structure for table `entrevista_x_pregunta` */

DROP TABLE IF EXISTS `entrevista_x_pregunta`;

CREATE TABLE `entrevista_x_pregunta` (
  `entrevistas_id` int(11) NOT NULL,
  `preguntas_id` int(11) NOT NULL,
  PRIMARY KEY (`entrevistas_id`,`preguntas_id`),
  CONSTRAINT `FK_entrevista_x_pregunta` FOREIGN KEY (`entrevistas_id`) REFERENCES `entrevistas` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `FK_entrevista_x_pregunta_x_pregunta` FOREIGN KEY (`entrevistas_id`) REFERENCES `preguntas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `entrevista_x_pregunta` */

/*Table structure for table `entrevistas` */

DROP TABLE IF EXISTS `entrevistas`;

CREATE TABLE `entrevistas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) DEFAULT NULL,
  `borradoLogico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `entrevistas` */

/*Table structure for table `especialidades` */

DROP TABLE IF EXISTS `especialidades`;

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

/*Data for the table `especialidades` */

insert  into `especialidades`(`id`,`nombre`,`descripcion`) values (9,'Profesor','aaaaaa'),(14,'Terapista ocupacional',NULL),(16,'Educación especial nivel 2',NULL),(17,'Educación especial nivel 3',NULL),(18,'Educación especial nivel 4',NULL),(19,'Educación especial nivel 5',NULL),(22,'Psicólogo pediátrico',NULL),(23,'Nueva Especialidad','dlfkjsldkfjsad\nadsfljasdlfjadslfkjad\nalfkjdslfkjads\n'),(24,'Psicoanalista','descripcion especialidad psicoanalista bleb le ble belble');

/*Table structure for table `fichas_abstractas` */

DROP TABLE IF EXISTS `fichas_abstractas`;

CREATE TABLE `fichas_abstractas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

/*Data for the table `fichas_abstractas` */

insert  into `fichas_abstractas`(`id`,`titulo`,`fecha`,`activo`,`descripcion`) values (1,'Primer Publicacion','2012-05-18 08:18:15',1,'sdfhaskdfjh adskfh asdkfh asdkfh asdkfh asdkfh asd\nasdkjfh askdfh askdjfh askjfh adskjfh asdfkj \n\naksjdfh askdjfh akdshf aksdfh kasdfh aksjdfh kasdfh kajsdhf akdsjfh asd\nfasdkjfh askdfh akdsfh daksfh askdfh aksdfh aksjdfh asd\nfaskfhdasdfk hasdkfh asdkfjh a\n\naksdfh akdsfh akdsjfh akdsjfh aksjdfh askdjfh \naksjdfh akjdsfh aksdh fakdsjfh aksjdfh adsjkf\nkasdfh akdsjfh akjsdfh adfskh \n\nkajsdfh akdsjfh aksjdh fkjdsh fkjasdhf kjdsh fkjsdfh \nakjfh akdsjfh akjdshf akjdfh sd\naksdjfh aksjdfh kasjdfh akjdsh faksjdhf kajdsh\n\ndsjfh akjdshf akjdfh sd\naksdjfh aksjdfh kasjdfh akjdsh faksjdhf kajdsh\n\nasdfadsfdsfds'),(3,'nueva feria artesanal en mar del plata 123','2012-05-19 05:42:20',1,'dfasdfasdf\nfasdfads\nfasdf\nadsfasd\nfasdf\nadsfa\ndsfasdf\nsdfadskfhaskdjfhaklsjdfhas 123\n'),(5,'sdaf asdfadsfa sdfadsf ','2012-05-30 06:27:59',1,'adsfa dsfadsf\nadsfasdfsdfasdfasdfasd\nfasdfads\n\nasdfjadskl fjas\ndf asldfkja sldkjf ads\nf asldkjf adslkfj sad\nfasdklf jasd'),(8,'Cambio el titulo','2012-05-30 06:29:11',1,'adsfasdf\nads\nfa\nsdf\nasdf\nasd\nf\nasdf\nasd\nfa\nsdf asdfasdfadsf 111 asdfÃ±lkj asdÃ±lfkja dsÃ±lfkj asdfsad\nfas\ndfas\ndf\nasdf\nadsf\nadsf\nasd\nfs adflaskdj fklasjd f\n'),(9,'wterwtwert','2012-07-03 02:11:26',1,'wetert\nwertwer\ntwert\nerwt\newrt'),(10,'rtwertwer terwtwe rt','2012-07-03 02:11:39',1,'wertwert\nerwtwer\ntwer\ntwertwet\newrt\nwet\nwertet'),(11,'asdfasdf','2012-07-03 02:25:40',1,'asdfasdkfasdf\nasdf\nasdf\nasdf\nasdf\nasdf\nasdf\nas\ndf\n\n\nadfsadsfadsf'),(12,'Primer software viejaaa','2012-08-14 21:58:30',1,'djasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\n\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\ndjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd asdjasldjasldkjas ldjasl dasldj askldj aslkdj aslkdjaskldj alsdjaskljd as\n'),(13,'otra aplicacion','2012-08-16 21:10:44',1,'ladsjf asdlfj asd\nf asdflja sdflkjas df\nas dflasjdf lakdsjf ladsjf ads\nf asldfj lasdfj adsf\nafkl jadlsfj asldfj ladsfj alsdfj alsdjf ladsjf lasdfkj dsa\nf adsklfj aldsjfa\ndsf klasdfj ladsjf lasdj flasjdf alsdkjf asdl'),(14,'3era aplicacion asdjkasld jasldj asldj asldj asldj','2012-08-16 21:11:19',1,'dsfaslfjasdfljasdf\nasdfljasdflkjasd\n'),(15,'ffff','2012-08-16 23:48:37',1,'asdfasdf'),(19,'no tiene q aparecer ','2012-08-17 04:44:41',1,'adsfasf'),(20,'uno mas','2012-08-17 04:45:04',1,'adfads'),(21,'asdlkajsdlkasjdkla moderacion','2012-08-21 23:19:40',1,'sdfadsfadsf\nadsfadsfasdfadsfasdf\nadsfadsfadsfjasdÃ±lkjfas\ndfasdklfjasdlkfjasd\n\ndasdasjdklaskdasd\nasdasd');

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
  PRIMARY KEY (`id`),
  KEY `FK_fotos_categorias` (`categorias_id`),
  KEY `FK_fotos_personas` (`personas_id`),
  KEY `FK_fotos_seguimientos` (`seguimientos_id`),
  KEY `FK_fotos_fichas_abstractas` (`fichas_abstractas_id`),
  CONSTRAINT `FK_fotos_categorias` FOREIGN KEY (`categorias_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_fotos_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_fotos_personas` FOREIGN KEY (`personas_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_fotos_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Data for the table `fotos` */

insert  into `fotos`(`id`,`seguimientos_id`,`fichas_abstractas_id`,`personas_id`,`categorias_id`,`nombreBigSize`,`nombreMediumSize`,`nombreSmallSize`,`orden`,`titulo`,`descripcion`,`tipo`) values (1,NULL,NULL,63,NULL,'63_big_1363414549_290_18071163443_2269_n.jpg','63_medium_1363414549_290_18071163443_2269_n.jpg','63_small_1363414549_290_18071163443_2269_n.jpg',NULL,'Foto de perfil',NULL,'perfil'),(3,NULL,21,NULL,NULL,'21_big_1378011156_IMG-20130530-00939.jpg','21_medium_1378011156_IMG-20130530-00939.jpg','21_small_1378011156_IMG-20130530-00939.jpg',NULL,'ewrewr','werwerewrew\nwerewr\nwerwer','adjunto'),(6,NULL,NULL,129,NULL,'129_big_1378060481_IMG-20130529-00934.jpg','129_medium_1378060481_IMG-20130529-00934.jpg','129_small_1378060481_IMG-20130529-00934.jpg',NULL,'Foto de perfil',NULL,'perfil'),(7,NULL,NULL,128,NULL,'128_big_1378060507_IMG-20130530-00936.jpg','128_medium_1378060507_IMG-20130530-00936.jpg','128_small_1378060507_IMG-20130530-00936.jpg',NULL,'Foto de perfil',NULL,'perfil'),(8,8,NULL,NULL,NULL,'8_big_1378779499_objetivos.png','8_medium_1378779499_objetivos.png','8_small_1378779499_objetivos.png',NULL,NULL,NULL,'adjunto');

/*Table structure for table `institucion_solicitudes` */

DROP TABLE IF EXISTS `institucion_solicitudes`;

CREATE TABLE `institucion_solicitudes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(11) NOT NULL,
  `instituciones_id` int(11) NOT NULL,
  `mensaje` varchar(500) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ciudades_id` int(11) DEFAULT NULL,
  `nombre` varchar(80) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `tipoInstitucion_id` int(11) DEFAULT NULL,
  `direccion` varchar(60) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `sitioWeb` varchar(60) DEFAULT NULL,
  `horariosAtencion` varchar(80) DEFAULT NULL,
  `autoridades` varchar(500) DEFAULT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `personeriaJuridica` varchar(100) DEFAULT NULL,
  `sedes` varchar(500) DEFAULT NULL,
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
  CONSTRAINT `instituciones_fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=latin1;

/*Data for the table `instituciones` */

insert  into `instituciones`(`id`,`ciudades_id`,`nombre`,`descripcion`,`tipoInstitucion_id`,`direccion`,`email`,`telefono`,`sitioWeb`,`horariosAtencion`,`autoridades`,`cargo`,`personeriaJuridica`,`sedes`,`actividadesMes`,`usuario_id`,`latitud`,`longitud`) values (33,1,'Universidad FASTA','dasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\n\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh as',1,'Gascon 10293','adsfadsf@dskjfh.com','1324324234','http://www.ufasta.edu.ar','de lunes a viernes 16:00 a 21:00','asdfahskf adfkjadfj\nakdfsjhaksjdh askjdfh akjsdfh \nadfkjah dsf asdfkjh asd\n','Director General bleble','asdfasdf  XXIVV','dsklfajdskfh ads\nf adskjf askdjf\n\ndsklfajdskfh ads\nf adskjf askdjf\ndsklfajdskfh ads\nf adskjf askdjf\ndsklfajdskfh ads\nf adskjf askdjf\n\ndsklfajdskfh ads\nf adskjf askdjf','asdfï¿½lkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh \n\nasdfï¿½lkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh \nasdfï¿½lkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh ',NULL,'-37.30027496','-57.93310474'),(57,1,'LADFSKJ ','sdlfÃ±jasdflk jads\nf asdlfj asdlkfja dslfkja ds\nfasdl fj asdlfkj asldfkj asldfkj asdf\nasdlfkj asdflkj adslfkj asd\nfasdflkj asdfklj',1,'adsf 1234','sadfadsf@sdkjlf.com','12312312',NULL,NULL,NULL,'asdfsdf',NULL,NULL,NULL,NULL,'-38.27268821','-57.93310474'),(58,2,'fgsdfg','sdfgdfg',2,'adsf 123','fasdf@laskjdf.com','1323123123',NULL,NULL,NULL,'asdfadsf',NULL,NULL,NULL,63,'-38.03943857','-57.56506275'),(59,1,'adfsadsfaa dasf asdfa sdf','dfsdflgjdsfklgjsdfgklj\nsfklgjflgjsdlfgkjsdf\ngsdfjlgkjdfslkgjsdflkgjds\nfgjsldfkgjskldfgjdfs\ngjsdflkgjsdflgkjsdf\ngjdfslgkjdflskgjsdfgfs\ndgjlfdkgjkldfs\nsdfjglkdfsjgldfsjglksdfjgsd\nfgjlsdfgkjslkdfg\n',2,'adsfadsf 132123','fkafhsd213@kj.com','23423432',NULL,NULL,NULL,'adsfasdf',NULL,NULL,NULL,63,'-38.09580388','-57.56835937'),(60,3,'dsfasdfas 123','sdfadsfsadf\nasdfa\nsdfas\ndfa\nsdf\nasdf\nafsd',2,'asdfas 2134 adsfas','asdfafs@lkadsjf.com','13123123',NULL,NULL,NULL,'1123sdfdsfasdf',NULL,NULL,NULL,63,'-38.82259066','-59.33935474'),(61,1,'dfsdsf 23423','sdfdsgfdsgdfsgdf',2,'fdsg 234','sdfgdfsg@sdlfk.com','12371928',NULL,NULL,NULL,'sgfdsgdfsg',NULL,NULL,NULL,63,'-37.26530963','-57.88915943');

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

insert  into `invitados`(`id`) values (117),(118),(125);

/*Table structure for table `moderaciones` */

DROP TABLE IF EXISTS `moderaciones`;

CREATE TABLE `moderaciones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fichas_abstractas_id` int(11) DEFAULT NULL,
  `instituciones_id` int(11) DEFAULT NULL,
  `estado` enum('rechazado','aprobado','pendiente') NOT NULL DEFAULT 'pendiente',
  `mensaje` varchar(500) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `niveles` */

insert  into `niveles`(`id`,`descripcion`) values (1,'Primaria'),(4,'Secundaria');

/*Table structure for table `objetivo_evolucion` */

DROP TABLE IF EXISTS `objetivo_evolucion`;

CREATE TABLE `objetivo_evolucion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objetivos_personalizados_id` int(11) DEFAULT NULL,
  `seg_scc_x_obj_apr_obj_id` int(11) DEFAULT NULL,
  `seg_scc_x_obj_apr_seg_id` int(11) DEFAULT NULL,
  `progreso` int(11) NOT NULL,
  `comentarios` text,
  `fechaHora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_objetivo_personalizados` (`objetivos_personalizados_id`),
  KEY `FK_objetivo_evolucion_obj_apr` (`seg_scc_x_obj_apr_obj_id`,`seg_scc_x_obj_apr_seg_id`),
  CONSTRAINT `FK_objetivo_evolucion_obj_apr` FOREIGN KEY (`seg_scc_x_obj_apr_obj_id`, `seg_scc_x_obj_apr_seg_id`) REFERENCES `seguimiento_scc_x_objetivo_aprendizaje` (`objetivos_aprendizaje_id`, `seguimientos_scc_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_objetivo_personalizados` FOREIGN KEY (`objetivos_personalizados_id`) REFERENCES `objetivos_personalizados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*Data for the table `objetivo_evolucion` */

insert  into `objetivo_evolucion`(`id`,`objetivos_personalizados_id`,`seg_scc_x_obj_apr_obj_id`,`seg_scc_x_obj_apr_seg_id`,`progreso`,`comentarios`,`fechaHora`) values (1,4,NULL,NULL,10,'un comentario acerca de la actualizacion de progreso','2013-09-07 18:40:57'),(2,4,NULL,NULL,30,'el chico mejoro mucho','2013-09-07 18:41:20'),(3,4,NULL,NULL,50,'va en un 50% falta un poco todavia','2013-09-07 18:41:49'),(4,4,NULL,NULL,60,'se cumplio con el objetivo','2013-09-07 19:10:53'),(5,4,NULL,NULL,90,'cayo un poquito','2013-09-07 19:17:18'),(6,4,NULL,NULL,95,'lo volvimos a lograr !!!','2013-09-08 14:44:42');

/*Table structure for table `objetivo_personalizado_ejes` */

DROP TABLE IF EXISTS `objetivo_personalizado_ejes`;

CREATE TABLE `objetivo_personalizado_ejes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  `ejePadre` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

/*Data for the table `objetivo_personalizado_ejes` */

insert  into `objetivo_personalizado_ejes`(`id`,`descripcion`,`ejePadre`) values (1,'Físico',0),(2,'Fisiológico',1),(3,'Psicológico',0),(4,'Social',0),(5,'Atención',3),(6,'Percepción',3),(7,'Aprendizaje',3),(8,'Memoria',3),(9,'Pensamiento',3),(10,'Lenguaje',3),(11,'Motivación',3),(12,'Emoción',3),(13,'Motriz',1),(14,'Asociación',4),(15,'Aceptación',4),(16,'Participación',4),(17,'Seguridad',4),(18,'Estima',4);

/*Table structure for table `objetivo_relevancias` */

DROP TABLE IF EXISTS `objetivo_relevancias`;

CREATE TABLE `objetivo_relevancias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `objetivo_relevancias` */

insert  into `objetivo_relevancias`(`id`,`descripcion`) values (1,'baja'),(2,'normal'),(3,'alta');

/*Table structure for table `objetivos` */

DROP TABLE IF EXISTS `objetivos`;

CREATE TABLE `objetivos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/*Data for the table `objetivos` */

insert  into `objetivos`(`id`,`descripcion`) values (1,'realizar operaciones basicas. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut in ante placerat, fringilla nibh vitae, facilisis tortor. Cras ut neque nec massa cursus ornare a ut elit. Sed ipsum erat, egestas vel viverra et, tristique vitae tortor. Phasellus a fermentum est. Aliquam at sagittis tortor, ut sollicitudin odio. Donec euismod non nulla vitae dictum.'),(2,'reconocer un numero primo de entre 1 y 100. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut in ante placerat, fringilla nibh vitae, facilisis tortor. Cras ut neque nec massa cursus ornare a ut elit. Sed ipsum erat, egestas vel viverra et, tristique vitae tortor. Phasellus a fermentum est. Aliquam at sagittis tortor, ut sollicitudin odio. Donec euismod non nulla vitae dictum.'),(4,'Poder contener la orina sda jsldj asldkj asldkj asldkj asldkj asldkja sdlkjas dlkaj sdlkasj dlaksjd alskdj asldkj11'),(9,'Lorem avión ipsum dolor sit amet, consectetur adipiscing elit. Ut in ante placerat, fringilla nibh vitae, facilisis tortor. Cras ut neque nec massa cursus ornare a ut elit. Sed ipsum erat, egestas vel viverra et, tristique vitae tortor. Phasellus a fermentum est. Aliquam at sagittis tortor, ut sollicitudin odio. Donec euismod non nulla vitae dictum. Pellentesque tincidunt sem non adipiscing consectetur. In hac habitasse platea dictumst. Nam tristique vel nunc eu porttitor.'),(10,'otro objetivo aprendizaje para numeros naturales. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut in ante placerat, fringilla nibh vitae, facilisis tortor. Cras ut neque nec massa cursus ornare a ut elit. Sed ipsum erat, egestas vel viverra et, tristique vitae tortor. Phasellus a fermentum est. Aliquam at sagittis tortor, ut sollicitudin odio. Donec euismod non nulla vitae dictum.');

/*Table structure for table `objetivos_aprendizaje` */

DROP TABLE IF EXISTS `objetivos_aprendizaje`;

CREATE TABLE `objetivos_aprendizaje` (
  `ejes_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `objetivos_id` (`id`),
  KEY `FK_objetivos_curriculares_areas` (`ejes_id`),
  CONSTRAINT `FK_objetivos_aprendizaje_ejes` FOREIGN KEY (`ejes_id`) REFERENCES `ejes` (`id`),
  CONSTRAINT `FK_objetivos_curriculares_objetivos` FOREIGN KEY (`id`) REFERENCES `objetivos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivos_aprendizaje` */

insert  into `objetivos_aprendizaje`(`ejes_id`,`id`) values (2,1),(2,10),(3,2);

/*Table structure for table `objetivos_personalizados` */

DROP TABLE IF EXISTS `objetivos_personalizados`;

CREATE TABLE `objetivos_personalizados` (
  `id` int(11) NOT NULL,
  `seguimientos_personalizados_id` int(11) NOT NULL,
  `objetivo_personalizado_ejes_id` int(11) NOT NULL,
  `objetivo_relevancias_id` int(11) NOT NULL,
  `estimacion` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `fechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaDesactivado` date DEFAULT NULL COMMENT 'indica fecha en la que se desactivo el objetivo',
  PRIMARY KEY (`id`),
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

insert  into `objetivos_personalizados`(`id`,`seguimientos_personalizados_id`,`objetivo_personalizado_ejes_id`,`objetivo_relevancias_id`,`estimacion`,`activo`,`fechaCreacion`,`fechaDesactivado`) values (4,7,11,2,'2013-11-16',1,'2013-09-01 00:00:00',NULL),(9,7,5,3,'2013-09-11',1,'2013-09-01 00:00:00','0000-00-00');

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

/*Table structure for table `parametro_x_controlador_pagina` */

DROP TABLE IF EXISTS `parametro_x_controlador_pagina`;

CREATE TABLE `parametro_x_controlador_pagina` (
  `parametros_id` int(11) NOT NULL,
  `controladores_pagina_id` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY (`parametros_id`,`controladores_pagina_id`),
  KEY `FK_parametros_x_controladores_pagina_controladores_pagina` (`controladores_pagina_id`),
  CONSTRAINT `FK_parametros_x_controladores_pagina_controladores_pagina` FOREIGN KEY (`controladores_pagina_id`) REFERENCES `controladores_pagina` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_parametros_x_controladores_pagina_parametros` FOREIGN KEY (`parametros_id`) REFERENCES `parametros` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametro_x_controlador_pagina` */

insert  into `parametro_x_controlador_pagina`(`parametros_id`,`controladores_pagina_id`,`valor`) values (3,20,'15'),(5,1,'Comunidad de profesionales dedicados al trabajo para la ayuda de personas con capacidades diferentes.'),(5,2,'Identificarse como integrante de la comunidad de profesionales.'),(9,8,'1'),(9,20,'0'),(9,23,'1'),(11,1,'comunidad, discapacitados, seguimientos'),(11,2,'identificarse, login, iniciar sesion'),(12,1,'Comunidad de profesionales abocados a la ayuda de personas discapacitadas'),(12,2,'Autentificarse para ingresar a la comunidad');

/*Table structure for table `parametro_x_usuario` */

DROP TABLE IF EXISTS `parametro_x_usuario`;

CREATE TABLE `parametro_x_usuario` (
  `parametros_id` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  `valor` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`parametros_id`,`usuarios_id`),
  KEY `FK_parametro_x_usuario_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_parametro_x_usuario_parametros` FOREIGN KEY (`parametros_id`) REFERENCES `parametros` (`id`),
  CONSTRAINT `FK_parametro_x_usuario_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametro_x_usuario` */

insert  into `parametro_x_usuario`(`parametros_id`,`usuarios_id`,`valor`) values (4,61,'1'),(4,63,'1'),(4,117,'1'),(4,118,'1'),(4,119,'1'),(4,121,'1');

/*Table structure for table `parametros` */

DROP TABLE IF EXISTS `parametros`;

CREATE TABLE `parametros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` enum('string','numeric','boolean') NOT NULL DEFAULT 'string',
  `namespace` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

/*Data for the table `parametros` */

insert  into `parametros`(`id`,`descripcion`,`tipo`,`namespace`) values (1,'para usar en metatags, firmas de mail, etc','string','NOMBRE_SITIO'),(3,'cantidad de fichas o elementos en los distintos listados del sitio','numeric','CANTIDAD_LISTADO'),(4,'activar o desactivar notificaciones por mail','boolean','NOTIFICACIONES_MAIL'),(5,'metatag description para el header de las vistas del sistema.','string','METATAG_DESCRIPTION'),(9,'Si el parametro esta desactivado entonces no se hace alta de moderacion.','boolean','ACTIVAR_MODERACIONES'),(11,'el campo keywords en los metatags de las vistas','string','METATAG_KEYWORDS'),(12,'la idea es que el title de las vistas tengan el nombre del sitio acompañado de la descripcion de este metatag','string','METATAG_TITLE'),(13,'Cantidad maxima de denuncias que tiene que recibir una entidad para ser descartada de los listados generales.','numeric','CANT_MAX_DENUNCIAS'),(14,'Mail de contacto para los integrantes y visitantes de la comunidad','string','EMAIL_SITIO_CONTACTO'),(16,'Cantidad de dias que permanecera activa una invitacion.','numeric','CANT_DIAS_EXPIRACION_INVITACION'),(17,'Cantidad de dias que se mantiene activo un link de password temporal generado desde el formulario de recuperar contraseña','numeric','CANT_DIAS_EXPIRACION_REC_PASS'),(18,'Plazo dentro del cual se permite editar una entrada antigua en un Seguimiento. Vencido el plazo la edición ya no es posible y todas las variables o unidades asociadas solo se eliminan logicamente, protegiendo el historial.','numeric','CANT_DIAS_EDICION_SEGUIMIENTOS');

/*Table structure for table `parametros_sistema` */

DROP TABLE IF EXISTS `parametros_sistema`;

CREATE TABLE `parametros_sistema` (
  `parametros_id` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY (`parametros_id`),
  CONSTRAINT `FK_parametros_sistema_parametros` FOREIGN KEY (`parametros_id`) REFERENCES `parametros` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametros_sistema` */

insert  into `parametros_sistema`(`parametros_id`,`valor`) values (1,'SGPAPD'),(9,'1'),(13,'5'),(14,'matiasvelillamdq@gmail.com'),(16,'5'),(17,'2'),(18,'90');

/*Table structure for table `parametros_usuario` */

DROP TABLE IF EXISTS `parametros_usuario`;

CREATE TABLE `parametros_usuario` (
  `parametros_id` int(11) NOT NULL,
  `valorDefecto` varchar(255) NOT NULL COMMENT 'valor por defecto asignado al parametro cuando se asigna al usuario por primera vez',
  PRIMARY KEY (`parametros_id`),
  CONSTRAINT `FK_parametros_usuario_parametros` FOREIGN KEY (`parametros_id`) REFERENCES `parametros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametros_usuario` */

insert  into `parametros_usuario`(`parametros_id`,`valorDefecto`) values (4,'1');

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
  `nombre` varchar(200) DEFAULT NULL COMMENT 'max 50 car, Encriptado',
  `apellido` varchar(200) DEFAULT NULL COMMENT 'max 50 car, Encriptado',
  `documento_tipos_id` int(11) DEFAULT NULL,
  `numeroDocumento` int(8) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `fechaNacimiento` varchar(10) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL COMMENT 'max 50 car, Encriptado',
  `telefono` varchar(200) DEFAULT NULL COMMENT 'max 30 car, Encriptado',
  `celular` varchar(200) DEFAULT NULL COMMENT 'max 30 car, Encriptado',
  `fax` varchar(200) DEFAULT NULL COMMENT 'max 30 car, Encriptado',
  `domicilio` varchar(200) DEFAULT NULL COMMENT 'max 100 car, Encriptado',
  `instituciones_id` int(11) DEFAULT NULL,
  `ciudades_id` int(11) DEFAULT NULL,
  `ciudadOrigen` varchar(400) DEFAULT NULL COMMENT 'max 150 car, Encriptado',
  `codigoPostal` varchar(80) DEFAULT NULL COMMENT 'max 20 car, Encriptado',
  `empresa` varchar(200) DEFAULT NULL COMMENT 'max 30 car, Encriptado',
  `universidad` varchar(200) DEFAULT NULL COMMENT 'max 30 car, Encriptado',
  `secundaria` varchar(200) DEFAULT NULL COMMENT 'max 30 car, Encriptado',
  PRIMARY KEY (`id`),
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `practicas` */

insert  into `practicas`(`id`,`nombre`) values (1,'Grupal'),(2,'Individual'),(3,'Pareja');

/*Table structure for table `pregunta_x_opcion_x_seguimiento` */

DROP TABLE IF EXISTS `pregunta_x_opcion_x_seguimiento`;

CREATE TABLE `pregunta_x_opcion_x_seguimiento` (
  `preguntas_id` int(11) NOT NULL,
  `preguntas_opciones_id` int(11) NOT NULL,
  `seguimientos_id` int(11) NOT NULL,
  PRIMARY KEY (`preguntas_id`,`preguntas_opciones_id`,`seguimientos_id`),
  KEY `FK_pregunta_x_opcion_x_seguimiento_seguimientos` (`seguimientos_id`),
  CONSTRAINT `FK_pregunta_x_opcion_x_seguimiento_preguntas` FOREIGN KEY (`preguntas_id`) REFERENCES `preguntas` (`id`),
  CONSTRAINT `FK_pregunta_x_opcion_x_seguimiento_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `pregunta_x_opcion_x_seguimiento` */

/*Table structure for table `preguntas` */

DROP TABLE IF EXISTS `preguntas`;

CREATE TABLE `preguntas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `descripcion` tinytext NOT NULL,
  `tipo` tinytext NOT NULL,
  `entrevistas_id` int(10) DEFAULT NULL,
  `borradoLogico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
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
  `borradoLogico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
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
  PRIMARY KEY (`preguntas_id`,`seguimientos_id`),
  KEY `FK_preguntas_simples_respuestas_seguimientos` (`seguimientos_id`),
  CONSTRAINT `FK_preguntas_simples_respuestas_preguntas` FOREIGN KEY (`preguntas_id`) REFERENCES `preguntas` (`id`),
  CONSTRAINT `FK_preguntas_simples_respuestas_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `preguntas_simples_respuestas` */

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

insert  into `privacidad`(`usuarios_id`,`email`,`telefono`,`celular`,`fax`,`curriculum`) values (61,'publico','privado','comunidad','comunidad','comunidad'),(63,'comunidad','privado','comunidad','privado','privado'),(117,'publico','comunidad','comunidad','comunidad','comunidad'),(118,'publico','comunidad','comunidad','comunidad','comunidad'),(119,'publico','comunidad','comunidad','comunidad','comunidad'),(121,'publico','comunidad','comunidad','comunidad','comunidad');

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

insert  into `publicaciones`(`id`,`usuarios_id`,`publico`,`activoComentarios`,`descripcionBreve`,`keywords`) values (1,63,1,1,'esta es una descripcion tan breve no ? 123 132','uno dos tres cuatro cinco cinco seis siete'),(5,63,1,1,'a sdfads fadsfa sdfasd qdsad asd','asdfads asdfadsf asdfsdf'),(8,63,1,1,'asdfasdf adsf asdfds fdsf 33','1 2 3 4'),(9,63,1,1,'weterwtwertwert','wr'),(10,63,1,1,'wret erwtwer twret','134324324324'),(11,63,0,1,'sdafadsfadsf adsfadf adfs dfdf adsfads','123');

/*Table structure for table `respuestas` */

DROP TABLE IF EXISTS `respuestas`;

CREATE TABLE `respuestas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `respuesta` tinytext NOT NULL,
  `preguntas_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_respuestas` (`preguntas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `respuestas` */

/*Table structure for table `reviews` */

DROP TABLE IF EXISTS `reviews`;

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL COMMENT 'reviewer. optional. hCard.',
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

insert  into `reviews`(`id`,`usuarios_id`,`publico`,`descripcionBreve`,`keywords`,`activoComentarios`,`itemType`,`itemName`,`itemEventSummary`,`itemUrl`,`rating`,`fuenteOriginal`) values (3,63,1,'sdfasdfklasj fhaklsjdfh lakjdsh flakjdsfh akldshf aldskjfh askjldf  123','sdf sdf sd sdf sfds  123',1,'product','Feria arte Sheraton 123','Feria artesanal Sheraton Mar del Plata, la 4ta de mar del plata','http://www.ldfkjdsk2123lfj.com',NULL,'http://www.lasdkfjda123slkfj.com');

/*Table structure for table `seguimiento_scc_x_objetivo_aprendizaje` */

DROP TABLE IF EXISTS `seguimiento_scc_x_objetivo_aprendizaje`;

CREATE TABLE `seguimiento_scc_x_objetivo_aprendizaje` (
  `objetivos_aprendizaje_id` int(11) NOT NULL,
  `seguimientos_scc_id` int(11) NOT NULL,
  `objetivo_relevancias_id` int(11) NOT NULL,
  `estimacion` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `fechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaDesactivado` date DEFAULT NULL,
  PRIMARY KEY (`objetivos_aprendizaje_id`,`seguimientos_scc_id`),
  KEY `FK_seguimiento_x_objetivo_curricular_objetivo_relevancias` (`objetivo_relevancias_id`),
  KEY `FK_seguimientos_scc` (`seguimientos_scc_id`),
  CONSTRAINT `FK_seguimientos_scc` FOREIGN KEY (`seguimientos_scc_id`) REFERENCES `seguimientos_scc` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_scc_x_objetivo_aprendizaje` FOREIGN KEY (`objetivos_aprendizaje_id`) REFERENCES `objetivos_aprendizaje` (`id`),
  CONSTRAINT `FK_seguimiento_x_objetivo_curricular_objetivo_relevancias` FOREIGN KEY (`objetivo_relevancias_id`) REFERENCES `objetivo_relevancias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_scc_x_objetivo_aprendizaje` */

insert  into `seguimiento_scc_x_objetivo_aprendizaje`(`objetivos_aprendizaje_id`,`seguimientos_scc_id`,`objetivo_relevancias_id`,`estimacion`,`activo`,`fechaCreacion`,`fechaDesactivado`) values (1,3,2,'2014-02-02',1,'2013-09-20 00:00:00',NULL),(1,4,1,'2014-02-03',1,'2013-09-20 00:00:00',NULL),(1,23,2,'2013-10-30',1,'2013-10-03 19:24:04',NULL),(2,23,1,'2013-10-25',1,'2013-10-03 19:24:40',NULL);

/*Table structure for table `seguimiento_x_contenido_variables` */

DROP TABLE IF EXISTS `seguimiento_x_contenido_variables`;

CREATE TABLE `seguimiento_x_contenido_variables` (
  `seguimiento_id` int(11) NOT NULL,
  `variable_id` int(11) NOT NULL,
  `valorTexto` text,
  `valorNumerico` float DEFAULT NULL,
  `fechaHora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`seguimiento_id`,`variable_id`,`fechaHora`),
  KEY `FK_seguimiento_x_contenido_variables` (`seguimiento_id`),
  KEY `FK_seguimiento_x_contenido_variables2` (`variable_id`),
  CONSTRAINT `FK_seguimiento_x_contenido_variables` FOREIGN KEY (`seguimiento_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_x_contenido_variables2` FOREIGN KEY (`variable_id`) REFERENCES `variables` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_x_contenido_variables` */

insert  into `seguimiento_x_contenido_variables`(`seguimiento_id`,`variable_id`,`valorTexto`,`valorNumerico`,`fechaHora`) values (7,1,'Suspendisse dictum at libero sit amet dictum. Nam elementum vestibulum ante, non hendrerit lorem accumsan vitae. Proin blandit venenatis placerat. Fusce a convallis leo. Mauris ut gravida nisi, sit amet tristique felis. Nunc in dolor at turpis luctus ullamcorper. Aliquam sodales dui at metus posuere vehicula. Nulla vestibulum dolor et placerat dapibus. Vestibulum ornare, leo vitae mollis adipiscing, tellus mi aliquet enim, eu consequat nisl orci luctus est. Suspendisse quis tortor accumsan, placerat nisl sit amet, malesuada eros. Nullam luctus ligula eget mi tempus, ut tincidunt ante interdum. Praesent condimentum elit at lacus rutrum, quis interdum nisi laoreet. Donec ut ipsum massa. Nullam in erat consequat purus fermentum eleifend non id quam. Suspendisse condimentum massa sed sagittis ultrices.\r\n\r\nInteger ut nulla ultrices, laoreet magna fermentum, commodo lacus. Fusce varius nec dolor eu placerat. Fusce ullamcorper nulla tortor, a rutrum lacus mollis a. In rutrum non tortor eu interdum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin ac ante non nisl tristique rutrum. In ac nulla erat. Integer ligula elit, iaculis et placerat in, tincidunt eget nunc. Donec varius rhoncus nibh, ac cursus ligula. Duis a aliquet ipsum. Pellentesque ornare, nisi quis tempor ultricies, lacus orci vulputate nibh, non accumsan orci nibh et est. Curabitur malesuada gravida nulla eu sollicitudin. Maecenas eleifend velit ac dolor semper, ac consequat diam tristique. Donec ullamcorper, nisi nec adipiscing bibendum, tortor urna scelerisque risus, non vehicula risus enim eu tellus.',NULL,'2013-08-02 17:14:06'),(7,1,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam varius a erat sed iaculis. Nam sodales sodales dui ut tempor. Maecenas aliquam mollis vestibulum. Mauris porta tortor ac nisi suscipit tristique. Suspendisse laoreet nisl arcu, sit amet scelerisque nibh mollis at. Aenean id libero et sapien gravida varius in id diam. Nam ullamcorper lacus sed eleifend mollis. Morbi facilisis, arcu sed elementum imperdiet, mi orci pellentesque risus, id interdum magna ipsum et elit. Fusce pulvinar ligula quis tellus accumsan sodales. Aenean feugiat, neque nec pretium placerat, eros felis faucibus nisi, sed consectetur est risus a odio. Maecenas gravida mi quis sollicitudin commodo. Nunc auctor id nisi non placerat. Aliquam nec odio tellus. Sed eleifend nulla at feugiat pharetra.\r\n\r\nNulla iaculis ipsum at sollicitudin pretium. In mollis vulputate nulla, eget porttitor lectus eleifend venenatis. Nullam a erat in lacus vestibulum vestibulum. Etiam porttitor neque a massa consectetur hendrerit. In ut aliquam tellus. Donec sed elementum magna. Vivamus mauris sem, tincidunt nec nunc porta, mattis convallis nisl.',NULL,'2013-08-10 17:13:41'),(7,1,'Integer ut nulla ultrices, laoreet magna fermentum, commodo lacus. Fusce varius nec dolor eu placerat. Fusce ullamcorper nulla tortor, a rutrum lacus mollis a. In rutrum non tortor eu interdum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin ac ante non nisl tristique rutrum. In ac nulla erat. Integer ligula elit, iaculis et placerat in, tincidunt eget nunc. Donec varius rhoncus nibh, ac cursus ligula. Duis a aliquet ipsum. Pellentesque ornare, nisi quis tempor ultricies, lacus orci vulputate nibh, non accumsan orci nibh et est. Curabitur malesuada gravida nulla eu sollicitudin. Maecenas eleifend velit ac dolor semper, ac consequat diam tristique. Donec ullamcorper, nisi nec adipiscing bibendum, tortor urna scelerisque risus, non vehicula risus enim eu tellus.\r\n\r\nMorbi sed purus a nibh vestibulum lacinia. Maecenas auctor elit nibh, at faucibus arcu consectetur quis. Cras dapibus sed lacus eu volutpat. Aenean orci felis, luctus ac sem ut, blandit vehicula tortor. Praesent in nulla non nibh ullamcorper dignissim. Praesent erat turpis, accumsan a dapibus id, adipiscing eget quam. Donec ullamcorper mauris a eleifend convallis. Nullam imperdiet magna at bibendum aliquet. Vivamus rutrum dictum laoreet. Cras molestie tempus suscipit. Duis mollis vehicula diam consequat volutpat. Integer lobortis ullamcorper ligula, sed rutrum quam ornare nec. Sed tempor, lorem id luctus ultricies, libero lorem pretium urna, quis viverra massa magna id arcu.',NULL,'2013-08-20 17:13:15'),(7,1,'Integer ut nulla ultrices, laoreet magna fermentum, commodo lacus. Fusce varius nec dolor eu placerat. Fusce ullamcorper nulla tortor, a rutrum lacus mollis a. In rutrum non tortor eu interdum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin ac ante non nisl tristique rutrum. In ac nulla erat. Integer ligula elit, iaculis et placerat in, tincidunt eget nunc. Donec varius rhoncus nibh, ac cursus ligula. Duis a aliquet ipsum. Pellentesque ornare, nisi quis tempor ultricies, lacus orci vulputate nibh, non accumsan orci nibh et est. Curabitur malesuada gravida nulla eu sollicitudin. Maecenas eleifend velit ac dolor semper, ac consequat diam tristique. Donec ullamcorper, nisi nec adipiscing bibendum, tortor urna scelerisque risus, non vehicula risus enim eu tellus.',NULL,'2013-09-01 17:12:50'),(7,1,'una descripcion aldkjsk\r\n\r\nNulla iaculis ipsum at sollicitudin pretium. In mollis vulputate nulla, eget porttitor lectus eleifend venenatis. Nullam a erat in lacus vestibulum vestibulum. Etiam porttitor neque a massa consectetur hendrerit. In ut aliquam tellus. Donec sed elementum magna. Vivamus mauris sem, tincidunt nec nunc porta, mattis convallis nisl.',NULL,'2013-09-10 17:11:27'),(7,1,'Suspendisse dictum at libero sit amet dictum. Nam elementum vestibulum ante, non hendrerit lorem accumsan vitae. Proin blandit venenatis placerat. Fusce a convallis leo. Mauris ut gravida nisi, sit amet tristique felis. Nunc in dolor at turpis luctus ullamcorper. Aliquam sodales dui at metus posuere vehicula. Nulla vestibulum dolor et placerat dapibus. Vestibulum ornare, leo vitae mollis adipiscing, tellus mi aliquet enim, eu consequat nisl orci luctus est. Suspendisse quis tortor accumsan, placerat nisl sit amet, malesuada eros. Nullam luctus ligula eget mi tempus, ut tincidunt ante interdum. Praesent condimentum elit at lacus rutrum, quis interdum nisi laoreet. Donec ut ipsum massa. Nullam in erat consequat purus fermentum eleifend non id quam. Suspendisse condimentum massa sed sagittis ultrices.',NULL,'2013-09-15 17:11:59'),(7,1,'asdasdasdasd',NULL,'2013-09-16 23:41:16'),(7,3,'Suspendisse dictum at libero sit amet dictum. Nam elementum vestibulum ante, non hendrerit lorem accumsan vitae. Proin blandit venenatis placerat. Fusce a convallis leo. Mauris ut gravida nisi, sit amet tristique felis. Nunc in dolor at turpis luctus ullamcorper. Aliquam sodales dui at metus posuere vehicula. Nulla vestibulum dolor et placerat dapibus. Vestibulum ornare, leo vitae mollis adipiscing, tellus mi aliquet enim, eu consequat nisl orci luctus est. Suspendisse quis tortor accumsan, placerat nisl sit amet, malesuada eros. Nullam luctus ligula eget mi tempus, ut tincidunt ante interdum. Praesent condimentum elit at lacus rutrum, quis interdum nisi laoreet. Donec ut ipsum massa. Nullam in erat consequat purus fermentum eleifend non id quam. Suspendisse condimentum massa sed sagittis ultrices.\r\n\r\nInteger ut nulla ultrices, laoreet magna fermentum, commodo lacus. Fusce varius nec dolor eu placerat. Fusce ullamcorper nulla tortor, a rutrum lacus mollis a. In rutrum non tortor eu interdum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin ac ante non nisl tristique rutrum. In ac nulla erat. Integer ligula elit, iaculis et placerat in, tincidunt eget nunc. Donec varius rhoncus nibh, ac cursus ligula. Duis a aliquet ipsum. Pellentesque ornare, nisi quis tempor ultricies, lacus orci vulputate nibh, non accumsan orci nibh et est. Curabitur malesuada gravida nulla eu sollicitudin. Maecenas eleifend velit ac dolor semper, ac consequat diam tristique. Donec ullamcorper, nisi nec adipiscing bibendum, tortor urna scelerisque risus, non vehicula risus enim eu tellus.',NULL,'2013-09-10 17:11:27'),(7,3,'Suspendisse dictum at libero sit amet dictum. Nam elementum vestibulum ante, non hendrerit lorem accumsan vitae. Proin blandit venenatis placerat. Fusce a convallis leo. Mauris ut gravida nisi, sit amet tristique felis. Nunc in dolor at turpis luctus ullamcorper. Aliquam sodales dui at metus posuere vehicula. Nulla vestibulum dolor et placerat dapibus. Vestibulum ornare, leo vitae mollis adipiscing, tellus mi aliquet enim, eu consequat nisl orci luctus est. Suspendisse quis tortor accumsan, placerat nisl sit amet, malesuada eros. Nullam luctus ligula eget mi tempus, ut tincidunt ante interdum. Praesent condimentum elit at lacus rutrum, quis interdum nisi laoreet. Donec ut ipsum massa. Nullam in erat consequat purus fermentum eleifend non id quam. Suspendisse condimentum massa sed sagittis ultrices.',NULL,'2013-09-15 17:11:59'),(7,3,'qweqweqweqewwe',NULL,'2013-09-16 23:41:03'),(7,38,'asdasdasdasd\r\nasdasd\r\nasd\r\nasd\r\nasdasdasd',NULL,'2013-09-16 20:05:08'),(23,1,'asdasdasda\r\nsdasd asdjas dlkaj sdlkj asd\r\nasdlaskj dlaskjd\r\nad alsdj askldj lasjdl aksjd laskjd laskjd aslkdj \r\n\r\nlkjsdklas\r\ndasdlja sldja sldjasldk jaslkdj as\r\n\r\n\r\nadskljas ldkj asd\r\nasdas kjdlasj dlasj dlasjd laskj daskldj lasj d',NULL,'2013-11-01 19:38:35');

/*Table structure for table `seguimiento_x_entrevista` */

DROP TABLE IF EXISTS `seguimiento_x_entrevista`;

CREATE TABLE `seguimiento_x_entrevista` (
  `seguimientos_id` int(11) NOT NULL,
  `entrevistas_id` int(11) NOT NULL,
  `fechaHora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`seguimientos_id`,`entrevistas_id`),
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
  `fechaHora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`unidades_id`,`seguimientos_id`),
  UNIQUE KEY `NewIndex1` (`unidades_id`,`seguimientos_id`),
  KEY `FK_seguimiento_x_unidades2` (`seguimientos_id`),
  CONSTRAINT `FK_seguimiento_x_unidad_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_x_unidad_unidades` FOREIGN KEY (`unidades_id`) REFERENCES `unidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_x_unidad` */

insert  into `seguimiento_x_unidad`(`unidades_id`,`seguimientos_id`,`fechaHora`) values (1,6,'2013-04-23 20:08:47'),(1,7,'2013-06-01 20:46:23'),(1,10,'2013-09-09 23:53:00'),(1,11,'2013-09-09 23:55:26'),(1,22,'2013-10-02 17:48:14'),(1,23,'2013-10-02 17:52:39'),(5,6,'2013-09-16 23:03:35'),(5,7,'2013-09-15 23:38:42'),(5,10,'2013-09-16 23:03:20'),(5,11,'2013-09-16 23:03:14'),(6,7,'2013-09-15 17:16:15'),(9,7,'2013-09-16 01:13:48');

/*Table structure for table `seguimientos` */

DROP TABLE IF EXISTS `seguimientos`;

CREATE TABLE `seguimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discapacitados_id` int(11) NOT NULL,
  `frecuenciaEncuentros` varchar(100) DEFAULT NULL,
  `diaHorario` varchar(100) DEFAULT NULL,
  `practicas_id` int(11) DEFAULT NULL,
  `usuarios_id` int(11) NOT NULL,
  `antecedentes` text COMMENT 'encriptado',
  `pronostico` text COMMENT 'encriptado',
  `fechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('activo','detenido') NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id`),
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
  PRIMARY KEY (`id`),
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
  PRIMARY KEY (`id`),
  KEY `FK_seguimientos_scc_diagnostico_scc` (`diagnosticos_scc_id`),
  CONSTRAINT `FK_seguimientos_scc_diagnostico_scc` FOREIGN KEY (`diagnosticos_scc_id`) REFERENCES `diagnosticos_scc` (`id`),
  CONSTRAINT `FK_seguimientos_scc_seguimientos` FOREIGN KEY (`id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos_scc` */

insert  into `seguimientos_scc`(`id`,`diagnosticos_scc_id`) values (3,3),(4,4),(8,8),(23,23);

/*Table structure for table `software` */

DROP TABLE IF EXISTS `software`;

CREATE TABLE `software` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(11) NOT NULL,
  `categorias_id` int(11) NOT NULL,
  `publico` tinyint(1) unsigned NOT NULL,
  `activoComentarios` tinyint(1) unsigned NOT NULL,
  `descripcionBreve` varchar(100) DEFAULT NULL,
  `enlaces` varchar(500) DEFAULT NULL COMMENT 'por si se quieren adjuntar mirrors de enlaces a descarga directa',
  PRIMARY KEY (`id`),
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(11) DEFAULT NULL COMMENT 'si es distinto de null indica una unidad creada por integrante para seguimientos personalizados',
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `preCargada` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'pre cargada en el sistema, solo puede editarse desde administrador',
  `fechaHora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `asociacionAutomatica` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'se asocia automaticamente en la creacion de un seguimiento',
  `borradoLogico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `tipoEdicion` enum('regular','esporadica') NOT NULL DEFAULT 'regular',
  PRIMARY KEY (`id`),
  KEY `FK_unidades_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_unidades_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Data for the table `unidades` */

insert  into `unidades`(`id`,`usuarios_id`,`nombre`,`descripcion`,`preCargada`,`fechaHora`,`asociacionAutomatica`,`borradoLogico`,`tipoEdicion`) values (1,NULL,'Información Básica','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed condimentum pharetra ligula, at accumsan turpis condimentum sit amet. Ut quis odio nibh. Proin accumsan tellus id tellus fringilla dictum. Quisque vel aliquam justo, et posuere dolor. Etiam malesuada, nisl eu accumsan condimentum, lorem nisl rutrum eros, eu scelerisque mi lectus non justo. Pellentesque adipiscing consectetur nibh eget rhoncus. Integer iaculis nulla pharetra, semper tellus sed, luctus dui.\r\n\r\nNulla consectetur ipsum nec blandit ultrices. In nec ipsum et est aliquam lacinia. Donec sollicitudin blandit elit, vitae vulputate dui porta nec. Maecenas nec iaculis tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis interdum, enim non posuere aliquet, eros purus tristique dui, malesuada pharetra leo velit et libero. Donec vulputate tincidunt leo, ut faucibus tellus pharetra vitae. Ut blandit felis ac diam elementum ultrices. Donec eget dui nisl. Nulla vestibulum, nibh id tincidunt adipiscing, magna sem vulputate nunc, a pellentesque dolor sem sed erat. Etiam sed mauris neque. Aenean in nisl auctor, hendrerit tellus et, rhoncus dolor. Donec sed nulla nec augue vestibulum ullamcorper quis vel diam. Morbi dui eros, sodales ut mollis sit amet, rhoncus ac risus. Phasellus lobortis aliquam turpis, et suscipit felis laoreet a. Maecenas purus diam, egestas et condimentum vitae, feugiat eu tellus.',0,'2012-01-01 00:00:00',1,0,'regular'),(5,63,'Una nueva Unidad','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed condimentum pharetra ligula, at accumsan turpis condimentum sit amet. Ut quis odio nibh. Proin accumsan tellus id tellus fringilla dictum. Quisque vel aliquam justo, et posuere dolor. Etiam malesuada, nisl eu accumsan condimentum, lorem nisl rutrum eros, eu scelerisque mi lectus non justo. Pellentesque adipiscing consectetur nibh eget rhoncus. Integer iaculis nulla pharetra, semper tellus sed, luctus dui.\r\n\r\nNulla consectetur ipsum nec blandit ultrices. In nec ipsum et est aliquam lacinia. Donec sollicitudin blandit elit, vitae vulputate dui porta nec. Maecenas nec iaculis tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis interdum, enim non posuere aliquet, eros purus tristique dui, malesuada pharetra leo velit et libero. Donec vulputate tincidunt leo, ut faucibus tellus pharetra vitae. Ut blandit felis ac diam elementum ultrices. Donec eget dui nisl. Nulla vestibulum, nibh id tincidunt adipiscing, magna sem vulputate nunc, a pellentesque dolor sem sed erat. Etiam sed mauris neque. Aenean in nisl auctor, hendrerit tellus et, rhoncus dolor. Donec sed nulla nec augue vestibulum ullamcorper quis vel diam. Morbi dui eros, sodales ut mollis sit amet, rhoncus ac risus. Phasellus lobortis aliquam turpis, et suscipit felis laoreet a. Maecenas purus diam, egestas et condimentum vitae, feugiat eu tellus.',0,'2013-04-07 04:39:43',0,0,'regular'),(6,63,'haciendo una prueba','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed condimentum pharetra ligula, at accumsan',0,'2013-04-07 07:25:21',0,0,'regular'),(9,63,'para borrar','adsasdsad',0,'2013-09-15 23:55:56',0,1,'regular');

/*Table structure for table `usuario_passwords_temporales` */

DROP TABLE IF EXISTS `usuario_passwords_temporales`;

CREATE TABLE `usuario_passwords_temporales` (
  `usuarios_id` int(11) NOT NULL,
  `contraseniaNueva` varchar(64) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
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
  `relacion` varchar(500) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('aceptada','pendiente') DEFAULT 'pendiente',
  `token` varchar(200) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  PRIMARY KEY (`usuarios_id`,`invitados_id`),
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
  `sitioWeb` varchar(200) DEFAULT NULL COMMENT '50 varchar original, campo encriptado',
  `especialidades_id` int(11) DEFAULT NULL,
  `perfiles_id` int(11) NOT NULL,
  `cargoInstitucion` varchar(40) DEFAULT NULL,
  `biografia` text COMMENT 'campo encriptado',
  `nombre` varchar(255) NOT NULL COMMENT 'campo encriptado',
  `contrasenia` char(64) DEFAULT NULL,
  `fechaAlta` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'si 0 entonces esta suspendido',
  `invitacionesDisponibles` int(3) DEFAULT '5',
  `universidadCarrera` varchar(50) DEFAULT NULL,
  `carreraFinalizada` tinyint(1) DEFAULT NULL,
  `moderado` tinyint(1) NOT NULL DEFAULT '0',
  `urlTokenKey` varchar(200) NOT NULL COMMENT 'Para generar links accedidos sin que el usuario haya iniciado sesion. Como por ejemplo links en los mails etc.',
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

insert  into `usuarios`(`id`,`sitioWeb`,`especialidades_id`,`perfiles_id`,`cargoInstitucion`,`biografia`,`nombre`,`contrasenia`,`fechaAlta`,`activo`,`invitacionesDisponibles`,`universidadCarrera`,`carreraFinalizada`,`moderado`,`urlTokenKey`) values (61,'ªEƒOC”ù0]åÃL!¨•A½Ùèâ oÄ…Â]ËÓ',17,1,'jefe','Ý˜ÿf¾1³š¤…~7Å¹îx\nT5(eùå/×n™¶×','rrio','e10adc3949ba59abbe56e057f20f883e','2011-06-28 02:14:43',1,4,'dddd',0,0,'51c50f52501cfc75dc1110dde6700aee'),(63,NULL,14,1,'Director',NULL,'matias.velilla','e10adc3949ba59abbe56e057f20f883e','2011-09-05 20:18:35',1,-2,'Lic en Sistemas',0,0,'51c50f52501cfc75dc1110dde6700aee'),(117,NULL,14,2,NULL,NULL,'Evangelina_Monello_117','e10adc3949ba59abbe56e057f20f883e','2012-09-18 19:32:05',1,5,NULL,1,0,'c4b931bff69c2aac5844e6fc6a355fef'),(118,NULL,17,2,'jefe catedra',NULL,'eduardo_velilla','e10adc3949ba59abbe56e057f20f883e','2012-09-18 07:36:19',1,5,NULL,0,0,'51c50f52501cfc75dc1110dde6700aee'),(119,NULL,NULL,2,NULL,NULL,'andresdelfino','e10adc3949ba59abbe56e057f20f883e','2012-09-18 21:29:08',1,4,NULL,0,0,'51c50f52501cfc75dc1110dde6700aee'),(121,'http://www.alfareria.com',14,2,'limpiador de alfonbras','soy encargado de limpiezas de inodoros y bidets','sanchesdebusta','e10adc3949ba59abbe56e057f20f883e','2012-09-25 21:11:57',1,5,'alfareria',0,0,'241febc7a31a7bab14869759420c1b38');

/*Table structure for table `variable_cualitativa_modalidades` */

DROP TABLE IF EXISTS `variable_cualitativa_modalidades`;

CREATE TABLE `variable_cualitativa_modalidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variables_id` int(11) NOT NULL,
  `modalidad` varchar(50) NOT NULL,
  `orden` int(2) unsigned DEFAULT '1',
  `borradoLogico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_variable_modalidades` (`variables_id`),
  CONSTRAINT `FK_variable_modalidades` FOREIGN KEY (`variables_id`) REFERENCES `variables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Data for the table `variable_cualitativa_modalidades` */

insert  into `variable_cualitativa_modalidades`(`id`,`variables_id`,`modalidad`,`orden`,`borradoLogico`) values (10,35,'azul',1,0),(11,35,'negro',2,0),(16,35,'blanco',0,0),(17,35,'rojo',0,0);

/*Table structure for table `variables` */

DROP TABLE IF EXISTS `variables`;

CREATE TABLE `variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('VariableNumerica','VariableTexto','VariableCualitativa') NOT NULL,
  `descripcion` text,
  `unidad_id` int(11) NOT NULL,
  `fechaHora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `borradoLogico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_variables` (`unidad_id`),
  CONSTRAINT `FK_variables_unidades` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;

/*Data for the table `variables` */

insert  into `variables`(`id`,`nombre`,`tipo`,`descripcion`,`unidad_id`,`fechaHora`,`borradoLogico`) values (1,'Block de notas','VariableTexto',NULL,1,NULL,0),(3,'Una prueba','VariableTexto','asdas\r\ndasd\r\nasd\r\nasdasdasdas',6,'2013-04-07 07:26:19',0),(4,'adsfadsf 1234','VariableNumerica','adfsadsfads 1234',5,'2013-04-07 07:26:58',0),(6,'una nueva variable de texto1','VariableTexto','asdlkasjdlas\nasldjasldjasldjlakdjalsdjalsdj\nasldjasldkjaskld\n\nalskdjalsdjalskdjlaskjdlaskd1',5,'2013-04-11 02:42:03',0),(7,'nueva variable ','VariableTexto','adsfkladsjf lakdsj fadsf\ndasflj adslfkj adsf\nadlfkj adslfjadlskjf \ndalfkj adslfk aldskjf ads\n',5,'2013-04-11 03:03:27',0),(8,'nueva variable numerica','VariableNumerica','sdfads',5,'2013-04-11 03:08:08',0),(35,'TEST PIPIPI Fotografia 1','VariableCualitativa','colores que ve cuando se le muestra la figura blebleble',5,'2013-05-04 21:22:43',0),(38,'asjdhaskjldh','VariableTexto','asdasdasd',9,'2013-09-16 20:04:50',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
