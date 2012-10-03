/*
SQLyog Enterprise - MySQL GUI v8.18 
MySQL - 5.5.8-log : Database - tesis
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
) ENGINE=InnoDB AUTO_INCREMENT=212 DEFAULT CHARSET=latin1;

/*Data for the table `acciones` */

insert  into `acciones`(`id`,`controladores_pagina_id`,`accion`,`grupo`,`activo`) values (1,1,'index',5,1),(2,1,'redireccion404',5,1),(3,1,'sitioOffline',5,1),(4,1,'sitioEnConstruccion',5,1),(5,1,'ajaxError',5,1),(6,2,'index',5,1),(7,2,'procesar',5,1),(8,2,'redireccion404',5,1),(9,3,'index',5,1),(10,3,'formulario',5,1),(11,3,'procesar',5,1),(12,3,'redireccion404',5,1),(13,4,'index',2,1),(14,4,'redireccion404',2,1),(15,5,'index',4,1),(16,5,'redireccion404',4,1),(17,6,'index',3,1),(18,6,'redireccion404',3,1),(19,6,'procesar',3,1),(20,6,'formulario',3,1),(21,6,'listado',3,1),(22,7,'index',1,1),(23,7,'redireccion404',1,1),(26,8,'index',5,1),(27,8,'nuevaInstitucion',3,1),(28,8,'listadoInstituciones',5,1),(31,8,'procesar',3,1),(32,9,'index',4,1),(33,9,'formulario',4,1),(34,9,'procesar',4,1),(35,9,'redireccion404',4,1),(36,8,'masInstituciones',4,1),(37,8,'redireccion404',5,1),(38,8,'ampliarInstitucion',4,1),(39,8,'editarInstitucion',3,1),(40,2,'logout',4,1),(41,10,'procesarEspecialidad',1,1),(42,10,'index',1,1),(43,10,'listarEspecialidades',1,1),(44,10,'nuevaEspecialidad',1,1),(45,10,'editarEspecialidad',1,1),(46,10,'eliminarEspecialidad',1,1),(47,10,'verificarUsoDeEspecialidad',1,1),(49,11,'nuevaCategoria',1,1),(50,11,'editarCategoria',1,1),(51,11,'listarCategoria',1,1),(52,11,'eliminarCategoria',1,1),(53,11,'index',1,1),(54,11,'procesarCategoria',1,1),(55,9,'modificarPrivacidadCampo',4,1),(56,12,'index',3,1),(59,14,'nuevoSeguimiento',3,1),(62,12,'buscarDiscapacitados',3,1),(63,14,'procesarSeguimiento',3,1),(64,8,'buscarInstituciones',4,1),(65,5,'descargarArchivo',4,1),(67,14,'index',3,1),(68,14,'redireccion404',3,1),(69,12,'redireccion404',3,1),(70,13,'index',3,1),(71,13,'procesar',3,1),(73,13,'agregar',3,1),(74,13,'redireccion404',3,1),(75,14,'listar',3,1),(76,14,'buscarSeguimientos',3,1),(77,13,'modificar',3,1),(78,13,'ver',3,1),(79,15,'index',2,1),(80,15,'redireccion404',2,1),(81,15,'listarModeracionesPendientes',2,1),(82,15,'procesarModeracion',2,1),(83,15,'procesarPersona',2,1),(84,14,'eliminar',3,1),(85,16,'index',2,1),(86,16,'redireccion404',2,1),(87,16,'procesar',2,1),(88,17,'redireccion404',2,1),(89,17,'index',2,1),(90,17,'procesar',2,1),(91,17,'form',2,1),(92,18,'index',2,1),(93,18,'redireccion404',2,1),(94,18,'procesar',2,1),(95,18,'form',2,1),(96,18,'cambiarPerfil',1,1),(97,18,'cerrarCuenta',1,1),(98,18,'crear',1,1),(99,18,'vistaImpresion',1,1),(101,18,'exportar',1,1),(103,9,'cerrarCuenta',4,1),(104,20,'index',4,1),(105,20,'redireccion404',4,1),(106,20,'misPublicaciones',3,1),(109,20,'guardarPublicacion',3,1),(110,20,'guardarReview',3,1),(111,20,'procesar',3,1),(112,20,'galeriaFotos',3,1),(113,20,'fotosProcesar',3,1),(114,20,'formFoto',3,1),(115,20,'galeriaArchivos',3,1),(116,20,'archivosProcesar',3,1),(117,20,'formArchivo',3,1),(118,20,'galeriaVideos',3,1),(119,20,'videosProcesar',3,1),(120,20,'formVideo',3,1),(121,20,'crearPublicacionForm',3,1),(122,20,'modificarPublicacionForm',3,1),(123,20,'crearReviewForm',3,1),(124,20,'modificarReviewForm',3,1),(125,1,'video',5,1),(126,14,'ver',3,1),(127,14,'cambiarEstadoSeguimientos',3,1),(128,14,'verAdjuntos',3,1),(129,14,'editarAntecedentes',3,1),(130,14,'procesarAntecedentes',3,1),(131,14,'formAdjuntarFoto',3,1),(132,14,'formAdjuntarVideo',3,1),(133,14,'formAdjuntarArchivo',3,1),(134,14,'formEditarAdjunto',3,1),(135,14,'procesarAdjunto',3,1),(136,14,'formModificarSeguimiento',3,1),(137,14,'guardarSeguimiento',3,1),(138,20,'verPublicacion',4,1),(139,20,'verReview',4,1),(141,21,'index',1,1),(142,21,'procesar',1,1),(143,21,'form',1,1),(144,21,'listarModeraciones',2,1),(145,8,'misInstituciones',3,1),(146,1,'provinciasByPais',5,1),(147,1,'ciudadesByProvincia',5,1),(148,8,'guardar',3,1),(149,8,'masMisInstituciones',3,1),(150,16,'listarModeraciones',2,1),(151,16,'form',2,1),(152,16,'listarSolicitudes',2,1),(153,11,'verificarUsoDeCategoria',1,1),(154,22,'index',1,1),(155,22,'procesar',1,1),(156,22,'form',1,1),(157,22,'listarModeraciones',2,1),(158,23,'index',4,1),(159,23,'misAplicaciones',3,1),(160,23,'crearSoftwareForm',3,1),(161,23,'modificarSoftwareForm',3,1),(162,23,'guardarSoftware',3,1),(163,23,'procesar',3,1),(164,23,'galeriaFotos',3,1),(165,23,'fotosProcesar',3,1),(166,23,'formFoto',3,1),(167,23,'galeriaArchivos',3,1),(168,23,'archivosProcesar',3,1),(169,23,'formArchivo',3,1),(170,23,'verSoftware',4,1),(171,23,'listarCategoria',4,1),(172,23,'redireccion404',4,1),(173,24,'index',5,1),(174,24,'ampliarInstitucion',5,1),(175,24,'procesar',5,1),(176,25,'index',5,1),(177,25,'verPublicacion',5,1),(178,25,'verReview',5,1),(179,25,'procesar',5,1),(180,7,'procesar',1,1),(181,7,'form',1,1),(182,26,'index',5,1),(183,26,'listarCategoria',5,1),(184,26,'verSoftware',5,1),(185,26,'procesar',5,1),(186,14,'listarAreasPorCiclos',3,1),(187,14,'listarCiclosPorNiveles',3,1),(188,14,'procesarDiagnostico',3,1),(189,14,'editarDiagnostico',3,1),(190,20,'denunciar',4,1),(191,8,'denunciar',4,1),(192,23,'denunciar',4,1),(193,16,'listarDenuncias',2,1),(194,16,'procesarDenuncias',2,1),(195,21,'procesarDenuncias',2,1),(196,21,'listarDenuncias',2,1),(197,22,'listarDenuncias',2,1),(198,22,'procesarDenuncias',2,1),(199,1,'desactivarNotificacionesMail',5,1),(200,7,'listarParametrosUsuario',1,1),(201,2,'formRecuperarContrasenia',5,1),(202,2,'procesarRecuperarContrasenia',5,1),(203,27,'procesarNivel',1,1),(204,27,'listarNiveles',1,1),(205,27,'formularioNivel',1,1),(206,27,'procesarCiclo',1,1),(207,27,'listarCiclos',1,1),(208,27,'formularioCiclo',1,1),(209,27,'procesarArea',1,1),(210,27,'listarAreas',1,1),(211,27,'formularioArea',1,1);

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
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=latin1;

/*Data for the table `archivos` */

insert  into `archivos`(`id`,`fichas_abstractas_id`,`seguimientos_id`,`usuarios_id`,`nombre`,`nombreServidor`,`descripcion`,`tipoMime`,`tamanio`,`fechaAlta`,`orden`,`titulo`,`tipo`) values (45,NULL,NULL,63,'unArchivoDePrueba.pdf','63_curriculum_1336183420_unArchivoDePrueba.pdf',NULL,'application/pdf',84665,'2012-05-04 23:03:40',1,NULL,'cv'),(51,8,NULL,NULL,'Un Nuevo Archivo.pdf','8_publicacion_1339535582_Un_Nuevo_Archivo.pdf','una descripcion 122','application/pdf',280317,'2012-06-12 18:13:02',122,'un titulo 122','adjunto'),(52,8,NULL,NULL,'Un Nuevo Archivo.pdf','8_publicacion_1339535588_Un_Nuevo_Archivo.pdf','dsfasdfasdfads 2222','application/pdf',280317,'2012-06-12 18:13:08',NULL,'sadfasdfads 2','adjunto'),(65,21,NULL,NULL,'asdNuevo_Archivo.zip','21_software_1346124912_asdnuevo-archivo-zip',NULL,'application/x-zip-compressed',242755,'2012-08-28 00:35:12',NULL,NULL,'adjunto'),(66,3,NULL,NULL,'asdNuevo_Archivo.zip','3_publicacion_1346125454_asdnuevo-archivo-zip',NULL,'application/x-zip-compressed',242755,'2012-08-28 00:44:14',NULL,NULL,'adjunto'),(67,3,NULL,NULL,'asdNuevo_Archivo.zip','3_publicacion_1346125459_asdnuevo-archivo-zip',NULL,'application/x-zip-compressed',242755,'2012-08-28 00:44:19',NULL,NULL,'adjunto');

/*Table structure for table `areas` */

DROP TABLE IF EXISTS `areas`;

CREATE TABLE `areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ciclos_id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_areas_ciclos` (`ciclos_id`),
  CONSTRAINT `FK_areas_ciclo` FOREIGN KEY (`ciclos_id`) REFERENCES `ciclos` (`id`)
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

/*Data for the table `controladores_pagina` */

insert  into `controladores_pagina`(`id`,`controlador`) values (17,'admin_accionesPerfil'),(11,'admin_categoria'),(10,'admin_especialidad'),(4,'admin_index'),(16,'admin_instituciones'),(27,'admin_objetivosCurriculares'),(7,'admin_parametros'),(15,'admin_personas'),(21,'admin_publicaciones'),(22,'admin_software'),(18,'admin_usuarios'),(9,'comunidad_datosPersonales'),(5,'comunidad_index'),(8,'comunidad_instituciones'),(6,'comunidad_invitaciones'),(20,'comunidad_publicaciones'),(23,'comunidad_software'),(1,'index_index'),(24,'index_instituciones'),(2,'index_login'),(25,'index_publicaciones'),(3,'index_registracion'),(26,'index_software'),(12,'seguimientos_index'),(13,'seguimientos_personas'),(14,'seguimientos_seguimientos');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos` */

insert  into `diagnosticos`(`id`,`descripcion`) values (2,NULL);

/*Table structure for table `diagnosticos_personalizado` */

DROP TABLE IF EXISTS `diagnosticos_personalizado`;

CREATE TABLE `diagnosticos_personalizado` (
  `id` int(11) NOT NULL,
  `codigo` varchar(100) DEFAULT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_diagnosticos_personalizado_diagnosticos` FOREIGN KEY (`id`) REFERENCES `diagnosticos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos_personalizado` */

insert  into `diagnosticos_personalizado`(`id`,`codigo`,`descripcion`) values (2,NULL,'');

/*Table structure for table `diagnosticos_scc` */

DROP TABLE IF EXISTS `diagnosticos_scc`;

CREATE TABLE `diagnosticos_scc` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_diagnosticos_scc_diagnosticos` FOREIGN KEY (`id`) REFERENCES `diagnosticos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos_scc` */

/*Table structure for table `diagnosticos_scc_x_ejes` */

DROP TABLE IF EXISTS `diagnosticos_scc_x_ejes`;

CREATE TABLE `diagnosticos_scc_x_ejes` (
  `diagnosticos_scc_id` int(11) NOT NULL,
  `ejes_id` int(11) NOT NULL,
  PRIMARY KEY (`diagnosticos_scc_id`,`ejes_id`),
  KEY `FK_diagnosticos_scc_x_ejes_ejes` (`ejes_id`),
  CONSTRAINT `FK_diagnosticos_scc_x_ejes_ejes` FOREIGN KEY (`ejes_id`) REFERENCES `ejes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_diagnosticos_scc_x_ejes_diagnostico_scc` FOREIGN KEY (`diagnosticos_scc_id`) REFERENCES `diagnosticos_scc` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `diagnosticos_scc_x_ejes` */

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

insert  into `discapacitados`(`id`,`nombreApellidoPadre`,`nombreApellidoMadre`,`fechaNacimientoPadre`,`fechaNacimientoMadre`,`ocupacionPadre`,`ocupacionMadre`,`nombreHermanos`,`usuarios_id`) values (95,'Eduardo Alfredo Velillaa','Evangelina monelloo','2005-06-04','2001-04-16','dsklfjdsfjdsf\nsdfdskljfldskjflskdjflskjdf\nsdflksjdflkdsjflksd\nsdlfksjdflksjdflkjsdddd','dsklfjdsfjdsf\nsdfdskljfldskjflskdjflskjdf\nsdflksjdflkdsjflksd\nsdlfksjdflksjdflkjsddddddd','dsklfjdsfjdsf 233\nsdfdskljfldskjflskdjflskjdf 211\nsdflksjdflkdsjflksd 322\nsdlfksjdflksjdflkjsd 122',61),(108,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(109,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(110,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,63),(111,NULL,NULL,'0000-00-00','0000-00-00','dsafjashdkfjshdkfasd\nfasdfkjhasdkjfhasd\nfasdfkjhaskdfhakjdfshkajsfdsf\n',NULL,'sdfasdfadsfadsf',63),(120,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,119),(122,NULL,NULL,'0000-00-00','0000-00-00',NULL,NULL,NULL,61);

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
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `contenidos` varchar(500) DEFAULT NULL,
  `areas_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ejes_curriculares_area` (`areas_id`),
  CONSTRAINT `FK_ejes_curriculares_area` FOREIGN KEY (`areas_id`) REFERENCES `areas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `ejes` */

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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

/*Data for the table `embed_videos` */

insert  into `embed_videos`(`id`,`fichas_abstractas_id`,`seguimientos_id`,`codigo`,`orden`,`titulo`,`descripcion`,`origen`,`urlKey`) values (20,1,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,NULL,NULL,'YouTube','18eb4fa91b3a41298a9202c94a950d08'),(22,8,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,'dfsafadsf 1','adsfasdfads 1','YouTube','2145a2e07b71444338a1963e56a0881d'),(23,8,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,'sdfasfadsfasd 2','sadfadsfadsf 222','YouTube','352a52072e85aca6360afd0b6a41ca56'),(24,11,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,NULL,NULL,'YouTube','a32851fa34ebf9e6005da190b49b3faf'),(25,3,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,NULL,NULL,'YouTube','000032141f2a32417d18030dad61781c');

/*Table structure for table `especialidades` */

DROP TABLE IF EXISTS `especialidades`;

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

/*Data for the table `especialidades` */

insert  into `especialidades`(`id`,`nombre`,`descripcion`) values (9,'Profesor','aaaaaa'),(14,'Terapista ocupacional',NULL),(16,'Educacion especial nivel 2',NULL),(17,'Educacion especial nivel 3',NULL),(18,'Educacion especial nivel 4',NULL),(19,'Educacion especial nivel 5',NULL),(22,'Psicologo pediatrico',NULL),(23,'Nueva Especialidad','dlfkjsldkfjsad\nadsfljasdlfjadslfkjad\nalfkjdslfkjads\n'),(24,'Psicoanalista','descripcion especialidad psicoanalista bleb le ble belble');

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
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=latin1;

/*Data for the table `fotos` */

insert  into `fotos`(`id`,`seguimientos_id`,`fichas_abstractas_id`,`personas_id`,`categorias_id`,`nombreBigSize`,`nombreMediumSize`,`nombreSmallSize`,`orden`,`titulo`,`descripcion`,`tipo`) values (63,NULL,NULL,111,NULL,'111_big_1340233668_IMG-20111217-00016.jpg','111_medium_1340233668_IMG-20111217-00016.jpg','111_small_1340233668_IMG-20111217-00016.jpg',NULL,'Foto de perfil',NULL,'perfil'),(67,NULL,8,NULL,NULL,'8_big_1340337927_IMG-20111209-00008.jpg','8_medium_1340337927_IMG-20111209-00008.jpg','8_small_1340337927_IMG-20111209-00008.jpg',11,'sdfgsdfg 1111','wefa sfads 1 asdfljadskfol adsf\nasdf asdkfj adslkfj adsf\nadsf lkadsj flkadsjf lakdsjf asd\nf asdlkfj adslkfj adsklfj ads\nfasldkjf asdlkfj adsklfj ads\nf asldkjf asldkfj aldskfj laskdfj asldkfj asd\nfasldfkj adsklfj  1111','adjunto'),(68,NULL,8,NULL,NULL,'8_big_1340337933_IMG-20111209-00010.jpg','8_medium_1340337933_IMG-20111209-00010.jpg','8_small_1340337933_IMG-20111209-00010.jpg',NULL,'adsfasdf 2','asdas das 222','adjunto'),(70,NULL,NULL,NULL,NULL,'15_big_1345177518_IMG-20111209-00009.jpg','15_medium_1345177518_IMG-20111209-00009.jpg','15_small_1345177518_IMG-20111209-00009.jpg',NULL,NULL,NULL,'adjunto'),(71,NULL,NULL,NULL,NULL,'15_big_1345177527_IMG-20111217-00013.jpg','15_medium_1345177527_IMG-20111217-00013.jpg','15_small_1345177527_IMG-20111217-00013.jpg',NULL,NULL,NULL,'adjunto'),(72,NULL,15,NULL,NULL,'15_big_1345178149_IMG-20111217-00013.jpg','15_medium_1345178149_IMG-20111217-00013.jpg','15_small_1345178149_IMG-20111217-00013.jpg',11,'corona1','ayuda a mantener el frioo\nsadflakjds flkasdf\nasdlfkj 1','adjunto'),(73,NULL,NULL,63,NULL,'63_big_1345952199_msn.png','63_medium_1345952199_msn.png','63_small_1345952199_msn.png',NULL,'Foto de perfil',NULL,'perfil'),(75,NULL,3,NULL,NULL,'3_big_1346123489_IMG-20120505-00316.jpg','3_medium_1346123489_IMG-20120505-00316.jpg','3_small_1346123489_IMG-20120505-00316.jpg',NULL,NULL,NULL,'adjunto'),(76,NULL,3,NULL,NULL,'3_big_1346123498_IMG-20120505-00317.jpg','3_medium_1346123498_IMG-20120505-00317.jpg','3_small_1346123498_IMG-20120505-00317.jpg',NULL,NULL,NULL,'adjunto'),(78,NULL,NULL,110,NULL,'110_big_1348559291_General Pueyrredon-20120522-00341.jpg','110_medium_1348559291_General Pueyrredon-20120522-00341.jpg','110_small_1348559291_General Pueyrredon-20120522-00341.jpg',NULL,'Foto de perfil',NULL,'perfil'),(79,NULL,11,NULL,NULL,'11_big_1348846527_General Pueyrredon-20120603-00353.jpg','11_medium_1348846527_General Pueyrredon-20120603-00353.jpg','11_small_1348846527_General Pueyrredon-20120603-00353.jpg',NULL,'garfield deforme','hola soy un garfield deformeeee','adjunto');

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

insert  into `instituciones`(`id`,`ciudades_id`,`nombre`,`descripcion`,`tipoInstitucion_id`,`direccion`,`email`,`telefono`,`sitioWeb`,`horariosAtencion`,`autoridades`,`cargo`,`personeriaJuridica`,`sedes`,`actividadesMes`,`usuario_id`,`latitud`,`longitud`) values (33,1,'Universidad FASTA','dasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\n\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh as',1,'Gascon 10293','adsfadsf@dskjfh.com','1324324234','http://www.ufasta.edu.ar','de lunes a viernes 16:00 a 21:00','asdfahskf adfkjadfj\nakdfsjhaksjdh askjdfh akjsdfh \nadfkjah dsf asdfkjh asd\n','Director General bleble','asdfasdf  XXIVV','dsklfajdskfh ads\nf adskjf askdjf\n\ndsklfajdskfh ads\nf adskjf askdjf\ndsklfajdskfh ads\nf adskjf askdjf\ndsklfajdskfh ads\nf adskjf askdjf\n\ndsklfajdskfh ads\nf adskjf askdjf','asdfï¿½lkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh \n\nasdfï¿½lkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh \nasdfï¿½lkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh ',NULL,'-37.30027496','-57.93310474'),(57,1,'LADFSKJ ','sdlfÃ±jasdflk jads\nf asdlfj asdlkfja dslfkja ds\nfasdl fj asdlfkj asldfkj asldfkj asdf\nasdlfkj asdflkj adslfkj asd\nfasdflkj asdfklj',1,'adsf 1234','sadfadsf@sdkjlf.com','12312312',NULL,NULL,NULL,'asdfsdf',NULL,NULL,NULL,NULL,'-38.27268821','-57.93310474'),(58,2,'fgsdfg','sdfgdfg',2,'adsf 123','fasdf@laskjdf.com','1323123123',NULL,NULL,NULL,'asdfadsf',NULL,NULL,NULL,63,'-38.03943857','-57.56506275'),(59,1,'adfsadsfaa dasf asdfa sdf','dfsdflgjdsfklgjsdfgklj\nsfklgjflgjsdlfgkjsdf\ngsdfjlgkjdfslkgjsdflkgjds\nfgjsldfkgjskldfgjdfs\ngjsdflkgjsdflgkjsdf\ngjdfslgkjdflskgjsdfgfs\ndgjlfdkgjkldfs\nsdfjglkdfsjgldfsjglksdfjgsd\nfgjlsdfgkjslkdfg\n',2,'adsfadsf 132123','fkafhsd213@kj.com','23423432',NULL,NULL,NULL,'adsfasdf',NULL,NULL,NULL,63,'-37.97533524','-57.60685211'),(60,3,'dsfasdfas 123','sdfadsfsadf\nasdfa\nsdfas\ndfa\nsdf\nasdf\nafsd',2,'asdfas 2134 adsfas','asdfafs@lkadsjf.com','13123123',NULL,NULL,NULL,'1123sdfdsfasdf',NULL,NULL,NULL,63,'-38.82259066','-59.33935474'),(61,1,'dfsdsf 23423','sdfdsgfdsgdfsgdf',2,'fdsg 234','sdfgdfsg@sdlfk.com','12371928',NULL,NULL,NULL,'sgfdsgdfsg',NULL,NULL,NULL,63,'-37.26530963','-57.88915943');

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

insert  into `invitados`(`id`) values (117),(118);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `niveles` */

/*Table structure for table `objetivo_personalizado_ejes` */

DROP TABLE IF EXISTS `objetivo_personalizado_ejes`;

CREATE TABLE `objetivo_personalizado_ejes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivo_personalizado_ejes` */

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
  `descripcion` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `objetivos` */

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

/*Table structure for table `objetivos_personalizados` */

DROP TABLE IF EXISTS `objetivos_personalizados`;

CREATE TABLE `objetivos_personalizados` (
  `id` int(11) NOT NULL,
  `objetivo_personalizado_ejes_id` int(11) NOT NULL,
  `evolucion` double DEFAULT NULL,
  `objetivo_relevancias_id` int(11) NOT NULL,
  `estimacion` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `objetivos_id` (`id`),
  KEY `FK_objetivos_personalizados_objetivo_ejes` (`objetivo_personalizado_ejes_id`),
  KEY `FK_objetivos_personalizados_objetivo_relevancia` (`objetivo_relevancias_id`),
  CONSTRAINT `FK_objetivos_personalizados_ejes` FOREIGN KEY (`objetivo_personalizado_ejes_id`) REFERENCES `objetivo_personalizado_ejes` (`id`),
  CONSTRAINT `FK_objetivos_personalizados_objetivos` FOREIGN KEY (`id`) REFERENCES `objetivos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_objetivos_personalizados_objetivo_relevancia` FOREIGN KEY (`objetivo_relevancias_id`) REFERENCES `objetivo_relevancias` (`id`)
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Data for the table `parametros` */

insert  into `parametros`(`id`,`descripcion`,`tipo`,`namespace`) values (1,'para usar en metatags, firmas de mail, etc','string','NOMBRE_SITIO'),(3,'cantidad de fichas o elementos en los distintos listados del sitio','numeric','CANTIDAD_LISTADO'),(4,'activar o desactivar notificaciones por mail','boolean','NOTIFICACIONES_MAIL'),(5,'metatag description para el header de las vistas del sistema.','string','METATAG_DESCRIPTION'),(9,'Si el parametro esta desactivado entonces no se hace alta de moderacion.','boolean','ACTIVAR_MODERACIONES'),(11,'el campo keywords en los metatags de las vistas','string','METATAG_KEYWORDS'),(12,'la idea es que el title de las vistas tengan el nombre del sitio acompaÃ±ado de la descripcion de este metatag','string','METATAG_TITLE'),(13,'Cantidad maxima de denuncias que tiene que recibir una entidad para ser descartada de los listados generales.','numeric','CANT_MAX_DENUNCIAS'),(14,'Mail de contacto para los integrantes y visitantes de la comunidad','string','EMAIL_SITIO_CONTACTO'),(16,'Cantidad de dias que permanecera activa una invitacion.','numeric','CANT_DIAS_EXPIRACION_INVITACION'),(17,'Cantidad de dias que se mantiene activo un link de password temporal generado desde el formulario de recuperar contraseÃ±a','numeric','CANT_DIAS_EXPIRACION_REC_PASS');

/*Table structure for table `parametros_sistema` */

DROP TABLE IF EXISTS `parametros_sistema`;

CREATE TABLE `parametros_sistema` (
  `parametros_id` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY (`parametros_id`),
  CONSTRAINT `FK_parametros_sistema_parametros` FOREIGN KEY (`parametros_id`) REFERENCES `parametros` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `parametros_sistema` */

insert  into `parametros_sistema`(`parametros_id`,`valor`) values (1,'SGPAPD'),(9,'1'),(13,'5'),(14,'matiasvelillamdq@gmail.com'),(16,'5'),(17,'2');

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
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=latin1;

/*Data for the table `personas` */

insert  into `personas`(`id`,`nombre`,`apellido`,`documento_tipos_id`,`numeroDocumento`,`sexo`,`fechaNacimiento`,`email`,`telefono`,`celular`,`fax`,`domicilio`,`instituciones_id`,`ciudades_id`,`ciudadOrigen`,`codigoPostal`,`empresa`,`universidad`,`secundaria`) values (61,'x\0y\ns…²«ýêœ\"§15','Cå“ƒ\"ö#®Å£ré',1,30061066,'m','1983-02-16','ÎÐcØ²QÉ²~}¬¶¢EÉÞx¶ÃÓ+DÆHw½†','°Táp²›Å“q€q`8','>	`ÊñªÝEüd‘}Nvù','¼*Q5ßËßr)‘','Õ€„\nìÒ#@rIW)Vh\"',33,1,'8yÛ±g¯vþKº_”wL','Œñö¹QÌÐñ]£t§è‘','>˜n4ùÃ*àë\"{','_Ó3ƒë‹.ÒSf†Æ¾x','øÇàêà#ÀÚÀŠ.êU'),(63,'øØÔÏ‘äTDòý=˜í\'š','«˜ìL’<“°ª<’,FØë',1,31821427,'m','1985-10-06','¡&“(Wxrl%ZoÙ¶Œ†Ëôø™å47w$Vtó(á','ˆ´‘´™[•žÑ·¬ª=ÀQ','I3]eÉmŸRZ/íXâ','YLB2R¨î•	Æ½‘?|','.ÑóEçYªá\"Åx÷ô',33,1,'Æ7\r‚lÁ\Z™ÊE>°q«','Œñö¹QÌÐñ]£t§è‘','6-Ó<C(%\0†¹{NpÐ','[8Q…+”\rûÁ£F¾JÜ+','r\n9ÜòÜ¾žJ¶TfcÚ:O'),(95,'Mirtaa','Gilardi',1,31821426,'m','2006-05-08','','91287319288',NULL,NULL,'sdfhsdkjh 2311',NULL,1,NULL,NULL,NULL,NULL,NULL),(108,'mfacud','sdfdsf',1,23424234,'m','2002-04-14',NULL,'3453453',NULL,NULL,NULL,33,1,NULL,NULL,NULL,NULL,NULL),(109,'Juan','Perez',1,12312312,'f','1998-04-15',NULL,'34324324234',NULL,NULL,NULL,33,1,NULL,NULL,NULL,NULL,NULL),(110,'Carla','Sanchez',1,31323545,'f','1997-04-12',NULL,'123123',NULL,NULL,NULL,33,1,NULL,NULL,NULL,NULL,NULL),(111,'Carla','Gutierrez',1,32413243,'f','2010-03-04',NULL,'123123123',NULL,NULL,NULL,33,2,NULL,NULL,NULL,NULL,NULL),(117,'Evangelina','Monello',1,12345678,'f','1995-05-17','evangelinamonello@hotmail.com','21312312',NULL,NULL,'funesd 2q3',NULL,1,NULL,'7600',NULL,NULL,'asdads ad'),(118,'Eduardo','Velilla',1,88888888,'m','1996-09-15','electrovh@hotmail.com','4740327',NULL,NULL,'funes 2862',33,1,NULL,'7600',NULL,NULL,'escuela de educacion tecnica 3'),(119,'þ] ¯G†TJl(«iWm','àeàºÚ‹ü@o­å+Óè',1,21871182,'m','1970-09-16','ãåX‘fâ=v_lký_²¥­ë:ÏÄ\"IË3‹ÆQ¼¡”ô',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120,'tomas','Delfino',1,44555666,'m','2003-10-09',NULL,'155555555',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL),(121,'R0µ\n-ão§î	øÐ¶å{¯Xjœ=e¿¨ª	ÔCs¤ÍòÆ','úIKo½,¨h@\0Ü2ŽÿÑ	Ž—QýÝbüFKHês',1,99912392,'m','1997-01-07','½œüaŸVÃ+«	šÿ/í^TeØ»Ç%ÆÊF','A:V«b=q\rCØÎÜS','Q\ZYéaÅk¤}\"}–+Ð','”\0ˆ]ûfˆ¼•à[S\nT5(eùå/×n™¶×','\'ËIWdV&é¼UŒPÆk²32»:cw-<™eßŠvã¢',33,1,'”Ôù(Úp¿#RÔ÷«dç~','Œñö¹QÌÐñ]£t§è‘','’¹ß|†M†I¸õ8,RV','.%§}’èwxuáü8â‹¥Î|ÜåÕ3Z¦Ïl…','ÜMkÉ÷©ûF<¢X'),(122,'N‹¥©d8–©Ë«2Ð¯M?¡','°õèW¶íÒB,ãònW¯',1,98789878,'m','1975-06-03',NULL,'ô(ÀÃåÊí%j?ù„¸',NULL,NULL,'yé”ù«5ÃêIw¥L:',NULL,1,NULL,NULL,NULL,NULL,NULL);

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

insert  into `privacidad`(`usuarios_id`,`email`,`telefono`,`celular`,`fax`,`curriculum`) values (61,'publico','privado','comunidad','comunidad','comunidad'),(63,'publico','privado','comunidad','comunidad','privado'),(117,'publico','comunidad','comunidad','comunidad','comunidad'),(118,'publico','comunidad','comunidad','comunidad','comunidad'),(119,'publico','comunidad','comunidad','comunidad','comunidad'),(121,'publico','comunidad','comunidad','comunidad','comunidad');

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

insert  into `reviews`(`id`,`usuarios_id`,`publico`,`descripcionBreve`,`keywords`,`activoComentarios`,`itemType`,`itemName`,`itemEventSummary`,`itemUrl`,`rating`,`fuenteOriginal`) values (3,63,1,'sdfasdfklasj fhaklsjdfh lakjdsh flakjdsfh akldshf aldskjfh askjldf  123','sdf sdf sd sdf sfds  123',1,'product','Feria arte Sheraton 123','Feria artesanal Sheraton Mar del Plata, la 4ta de mar del plata','http://www.ldfkjdsk2123lfj.com',3,'http://www.lasdkfjda123slkfj.com');

/*Table structure for table `seguimiento_scc_x_objetivo_aprendizaje` */

DROP TABLE IF EXISTS `seguimiento_scc_x_objetivo_aprendizaje`;

CREATE TABLE `seguimiento_scc_x_objetivo_aprendizaje` (
  `objetivos_aprendizaje_id` int(11) NOT NULL,
  `seguimientos_scc_id` int(11) NOT NULL,
  `evolucion` double DEFAULT NULL,
  `objetivo_relevancias_id` int(11) NOT NULL,
  `estimacion` date DEFAULT NULL,
  PRIMARY KEY (`objetivos_aprendizaje_id`,`seguimientos_scc_id`),
  KEY `FK_seguimiento_x_objetivo_curricular_objetivo_relevancias` (`objetivo_relevancias_id`),
  KEY `FK_seguimientos_scc` (`seguimientos_scc_id`),
  CONSTRAINT `FK_seguimiento_scc_x_objetivo_aprendizaje` FOREIGN KEY (`objetivos_aprendizaje_id`) REFERENCES `objetivos_aprendizaje` (`id`),
  CONSTRAINT `FK_seguimientos_scc` FOREIGN KEY (`seguimientos_scc_id`) REFERENCES `seguimientos_scc` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimiento_x_objetivo_curricular_objetivo_relevancias` FOREIGN KEY (`objetivo_relevancias_id`) REFERENCES `objetivo_relevancias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimiento_scc_x_objetivo_aprendizaje` */

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

insert  into `seguimiento_x_unidades`(`unidad_id`,`seguimiento_id`,`fechaHora`) values (1,32,'2012-10-02 21:36:38');

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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos` */

insert  into `seguimientos`(`id`,`discapacitados_id`,`frecuenciaEncuentros`,`diaHorario`,`practicas_id`,`usuarios_id`,`antecedentes`,`pronostico`,`fechaCreacion`,`estado`) values (32,95,NULL,NULL,1,63,NULL,NULL,'2012-10-02 21:36:37','activo');

/*Table structure for table `seguimientos_personalizados` */

DROP TABLE IF EXISTS `seguimientos_personalizados`;

CREATE TABLE `seguimientos_personalizados` (
  `id` int(11) NOT NULL,
  `diagnostico_personalizado_id` int(11) DEFAULT NULL,
  `objetivos_personalizados_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_seguimientos_personalizados_objetivo_personalizado` (`objetivos_personalizados_id`),
  KEY `FK_seguimientos_personalizados_diagnostico_personalizado` (`diagnostico_personalizado_id`),
  CONSTRAINT `FK_seguimientos_personalizados_diagnostico_personalizado` FOREIGN KEY (`diagnostico_personalizado_id`) REFERENCES `diagnosticos_personalizado` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_seguimientos_personalizados_objetivo_personalizado` FOREIGN KEY (`objetivos_personalizados_id`) REFERENCES `objetivos_personalizados` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_seguimientos_personalizados_seguimientos` FOREIGN KEY (`id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos_personalizados` */

insert  into `seguimientos_personalizados`(`id`,`diagnostico_personalizado_id`,`objetivos_personalizados_id`) values (32,2,NULL);

/*Table structure for table `seguimientos_scc` */

DROP TABLE IF EXISTS `seguimientos_scc`;

CREATE TABLE `seguimientos_scc` (
  `id` int(11) NOT NULL,
  `diagnosticos_scc_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_seguimientos_scc_diagnostico_scc` (`diagnosticos_scc_id`),
  CONSTRAINT `FK_seguimientos_scc_diagnostico_scc` FOREIGN KEY (`diagnosticos_scc_id`) REFERENCES `diagnosticos_scc` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_seguimientos_scc_seguimientos` FOREIGN KEY (`id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos_scc` */

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

insert  into `usuario_x_invitado`(`usuarios_id`,`invitados_id`,`relacion`,`fecha`,`estado`,`token`,`nombre`,`apellido`) values (63,117,'asdfadsfjdsfads\nfdsf\nasdf\ndsf','2012-09-15 08:22:10','aceptada','7dde8ba57614529a7b679a4d3b876f55','Evangelina','Monello'),(63,118,'sadfsadf\ndasfalsdkfjaksldfjal','2012-09-15 08:38:52','aceptada','dfb59af3275910cd55683cdecdd7b237','Eduardo','Velilla');

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

insert  into `usuarios`(`id`,`sitioWeb`,`especialidades_id`,`perfiles_id`,`cargoInstitucion`,`biografia`,`nombre`,`contrasenia`,`fechaAlta`,`activo`,`invitacionesDisponibles`,`universidadCarrera`,`carreraFinalizada`,`moderado`,`urlTokenKey`) values (61,'ªEƒOC”ù0]åÃL!¨•A½Ùèâ oÄ…Â]ËÓ',17,1,'jefe','Ý˜ÿf¾1³š¤…~7Å¹îx\nT5(eùå/×n™¶×','rrio','e10adc3949ba59abbe56e057f20f883e','2011-06-28 02:14:43',1,4,'dddd',0,0,'51c50f52501cfc75dc1110dde6700aee'),(63,'http://www.facebook.com',14,1,'Director','sdfsdfdsfsd\nfsd\nfsd\nfsd\nfds\nfsdfdsfsdfsdfdsfsdfds','matias.velilla','e10adc3949ba59abbe56e057f20f883e','2011-09-05 20:18:35',1,-2,'Lic en Sistemas',0,0,'51c50f52501cfc75dc1110dde6700aee'),(117,NULL,14,2,NULL,NULL,'Evangelina_Monello_117','e10adc3949ba59abbe56e057f20f883e','2012-09-18 19:32:05',1,5,NULL,1,0,'c4b931bff69c2aac5844e6fc6a355fef'),(118,NULL,16,3,NULL,NULL,'eduardo_velilla','e10adc3949ba59abbe56e057f20f883e','2012-09-18 07:36:19',1,5,NULL,0,0,'7ee09fe2e34e750dbace1079e3f48033'),(119,NULL,NULL,2,NULL,NULL,'andresdelfino','e10adc3949ba59abbe56e057f20f883e','2012-09-18 21:29:08',1,5,NULL,0,0,'331aba3e814a8ec69ca2fd749d8804b3'),(121,'http://www.alfareria.com',14,2,'limpiador de alfonbras','soy encargado de limpiezas de inodoros y bidets','sanchesdebusta','e10adc3949ba59abbe56e057f20f883e','2012-09-25 21:11:57',1,5,'alfareria',0,0,'241febc7a31a7bab14869759420c1b38');

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
