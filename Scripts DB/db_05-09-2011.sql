/*
SQLyog Enterprise - MySQL GUI v8.18 
MySQL - 5.1.36-community-log : Database - new_tesis
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`new_tesis` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `new_tesis`;

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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

/*Data for the table `acciones` */

insert  into `acciones`(`id`,`controladores_pagina_id`,`accion`,`grupo`,`activo`) values (1,1,'index',5,1),(2,1,'redireccion404',5,1),(3,1,'sitioOffline',5,1),(4,1,'sitioEnConstruccion',5,1),(5,1,'ajaxError',5,1),(6,2,'index',5,1),(7,2,'procesar',5,1),(8,2,'redireccion404',5,1),(9,3,'index',5,1),(10,3,'formulario',5,1),(11,3,'procesar',5,1),(12,3,'redireccion404',5,1),(13,4,'index',2,1),(14,4,'redireccion404',2,1),(15,5,'index',4,1),(16,5,'redireccion404',4,1),(17,6,'index',3,1),(18,6,'redireccion404',3,1),(19,6,'procesar',3,1),(20,6,'formulario',3,1),(21,6,'listado',3,1),(22,7,'index',1,1),(23,7,'redireccion404',1,1),(24,2,'recuperarContrasenia',5,1),(25,2,'confirmarContrasenia',5,1),(26,8,'index',5,1),(27,8,'nuevaInstitucion',5,1),(28,8,'listadoInstituciones',5,1),(29,8,'provinciasByPais',5,1),(30,8,'ciudadesByProvincia',5,1),(31,8,'procesar',5,1),(32,9,'index',4,1),(33,9,'formulario',4,1),(34,9,'procesar',4,1),(35,9,'redireccion404',4,1);

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
  `descripcion` varchar(255) DEFAULT NULL,
  `tipoMime` varchar(50) NOT NULL,
  `tamanio` int(11) DEFAULT NULL,
  `fechaAlta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `orden` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `titulo` varchar(100) DEFAULT NULL,
  `tipo` enum('cv','adjunto') NOT NULL DEFAULT 'adjunto',
  `moderado` tinyint(1) unsigned DEFAULT NULL,
  `activo` tinyint(1) unsigned DEFAULT NULL,
  `publico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `activoComentarios` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_archivos_seguimientos` (`seguimientos_id`),
  KEY `FK_archivos_categorias` (`categorias_id`),
  KEY `FK_archivos_usuarios` (`usuarios_id`),
  KEY `FK_archivos_fichas_abstractas` (`fichas_abstractas_id`),
  CONSTRAINT `FK_archivos_categorias` FOREIGN KEY (`categorias_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `categorias` */

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `ciudades` */

insert  into `ciudades`(`id`,`nombre`,`provincia_id`,`latitud`,`longitud`) values (1,'Mar del Plata',1,NULL,NULL),(2,'Necochea',1,NULL,NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `comentarios` */

insert  into `comentarios`(`id`,`reviews_id`,`publicaciones_id`,`archivos_id`,`fecha`,`descripcion`,`valoracion`,`usuarios_id`,`nombreApellido`) values (1,NULL,NULL,NULL,'2011-05-19 07:10:10','asdasdasdasdasd',NULL,NULL,'Mat?as Velilla');

/*Table structure for table `controladores_pagina` */

DROP TABLE IF EXISTS `controladores_pagina`;

CREATE TABLE `controladores_pagina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controlador` varchar(200) NOT NULL COMMENT 'Formado por [modulo]_[controlador]. ''system'' se utiliza para referencia a TODO el sistema. No debe asociarse a la tabla acciones',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Data for the table `controladores_pagina` */

insert  into `controladores_pagina`(`id`,`controlador`) values (1,'index_index'),(2,'index_login'),(3,'index_registracion'),(4,'admin_index'),(5,'comunidad_index'),(6,'comunidad_invitaciones'),(7,'admin_parametros'),(8,'comunidad_instituciones'),(9,'comunidad_datosPersonales');

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
  `ocupacionPadre` varchar(100) DEFAULT NULL,
  `ocupacionMadre` varchar(100) DEFAULT NULL,
  `nombreHermanos` varchar(150) DEFAULT NULL,
  `moderado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `personas_id` (`id`),
  CONSTRAINT `FK_discapacitados_personas` FOREIGN KEY (`id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `discapacitados` */

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
  `orden` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `origen` enum('youTube') NOT NULL DEFAULT 'youTube',
  PRIMARY KEY (`id`),
  KEY `FK_embed_videos_seguimientos` (`seguimientos_id`),
  KEY `FK_embed_videos_fichas_abstractas` (`fichas_abstractas_id`),
  CONSTRAINT `FK_embed_videos_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_embed_videos_seguimientos` FOREIGN KEY (`seguimientos_id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `embed_videos` */

/*Table structure for table `especialidades` */

DROP TABLE IF EXISTS `especialidades`;

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `especialidades` */

/*Table structure for table `fichas_abstractas` */

DROP TABLE IF EXISTS `fichas_abstractas`;

CREATE TABLE `fichas_abstractas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `fichas_abstractas` */

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
  `orden` tinyint(4) unsigned NOT NULL DEFAULT '1',
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`id`),
  KEY `FK_instituciones_ciudades` (`ciudades_id`),
  KEY `tipoInstitucion_id` (`tipoInstitucion_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `FK_instituciones_ciudades` FOREIGN KEY (`ciudades_id`) REFERENCES `ciudades` (`id`),
  CONSTRAINT `instituciones_fk_tipos` FOREIGN KEY (`tipoInstitucion_id`) REFERENCES `instituciones_tipos` (`id`),
  CONSTRAINT `instituciones_fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

/*Data for the table `instituciones` */

insert  into `instituciones`(`id`,`ciudades_id`,`nombre`,`moderado`,`descripcion`,`tipoInstitucion_id`,`direccion`,`email`,`telefono`,`sitioWeb`,`horariosAtencion`,`autoridades`,`cargo`,`personeriaJuridica`,`sedes`,`actividadesMes`,`usuario_id`) values (1,1,'nbvb',0,'nbvnbvn',1,'bvn','bn','nbvn','bvnbv','undefined','bvn','vbnbvn','bvnbvn','bvnbvn','vbn',61),(2,1,'nbvb',0,'nbvnbvn',1,'bvn','bn','nbvn','bvnbv','undefined','bvn','vbnbvn','bvnbvn','bvnbvn','vbn',61),(3,1,'nbvb',0,'nbvnbvn',1,'bvn','bn','nbvn','bvnbv','undefined','bvn','vbnbvn','bvnbvn','bvnbvn','vbn',61),(4,1,'nbvb',0,'nbvnbvn',1,'bvn','bn','nbvn','bvnbv','undefined','bvn','vbnbvn','bvnbvn','bvnbvn','vbn',61),(5,1,'ssssssss',0,'121',1,'321','321','321','32','undefined','321','1321','12','1','321',61),(6,1,'1231',0,'321',1,'2231','321','32','132','undefined','sdfsd','32','132','132','321',61),(7,1,'321Âº231Âº32',0,'21',1,'321','321','321','Sitio web','undefined','321','21','32','321','32',61),(8,1,'CAECE',0,'Universidad caece',1,'Olavarria 2323','caece@info.com','12341234','www.caece.com.ar','undefined','Autoridades','Decano','Jorge pepe','Sedes','Actividades del mes',61),(9,1,'aaa',0,'aaa',2,'aaa','aaa','123123','Sitio web','undefined','Autoridades','aa','aaa','Sedes','Actividades del mes',61),(10,1,'lk',0,'jklj',1,'asdasd','asdasd','asd','asd','undefined','Autoridades','asd','asdas','Sedes','Actividades del mes',61),(11,1,'asdasdkjh',0,'ds',2,'sdf','zsdfsd','sdf','sdf','undefined','Autoridades','asd','asd','Sedes','Actividades del mes',61),(12,1,'asdasdkjh',0,'ds',2,'sdf','zsdfsd','sdf','sdf','undefined','Autoridades','asd','asd','Sedes','Actividades del mes',61),(13,1,'asdasdkjh',0,'ds',2,'sdf','zsdfsd','sdf','sdf','undefined','Autoridades','asd','asd','Sedes','Actividades del mes',61),(14,1,'asdasdkjh',0,'ds',2,'sdf','zsdfsd','sdf','sdf','undefined','Autoridades','asd','asd','Sedes','Actividades del mes',61),(15,1,'asdasdkjh',0,'ds',2,'sdf','zsdfsd','sdf','sdf','undefined','Autoridades','asd','asd','Sedes','Actividades del mes',61),(16,1,'asd',0,'asd',2,'asd','asd','asd','asd','undefined','asd','asd','asd','asd','asd',61),(17,1,'asd',0,'asd',2,'asd','asd','asd','asd','undefined','asd','asd','asd','asd','asd',61),(18,1,'asd',0,'asd',2,'asd','asd','asd','asd','undefined','asd','asd','asd','asd','asd',61),(19,1,'asd',0,'asd',2,'asd','asd','asd','asd','undefined','asd','asd','asd','asd','asd',61),(20,1,'dfgdfg',0,'asd',1,'lkj','sdf','sdfsdf','sdaf','undefined','Autoridades','asd','ij','Sedes','Actividades del mes',61),(21,1,'dfgdfg',0,'asd',1,'lkj','sdf','sdfsdf','sdaf','undefined','Autoridades','asd','ij','Sedes','Actividades del mes',61),(22,2,'dfg',0,'dfg',2,'dfg','df','asf','asedf','undefined','sdf','dfg','dfg','sdf','sdf',61);

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
  `nacionalidad` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `numeroDocumento` (`numeroDocumento`),
  UNIQUE KEY `numeroDocumento_2` (`numeroDocumento`),
  KEY `FK_personas` (`documento_tipos_id`),
  KEY `FK_personas_institucion` (`instituciones_id`),
  KEY `FK_personas_ciudades` (`ciudades_id`),
  CONSTRAINT `FK_personas_ciudades` FOREIGN KEY (`ciudades_id`) REFERENCES `ciudades` (`id`),
  CONSTRAINT `FK_personas_documento_tipos` FOREIGN KEY (`documento_tipos_id`) REFERENCES `documento_tipos` (`id`),
  CONSTRAINT `FK_personas_instituciones` FOREIGN KEY (`instituciones_id`) REFERENCES `instituciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;

/*Data for the table `personas` */

insert  into `personas`(`id`,`nombre`,`apellido`,`documento_tipos_id`,`numeroDocumento`,`sexo`,`fechaNacimiento`,`email`,`telefono`,`celular`,`fax`,`domicilio`,`instituciones_id`,`ciudades_id`,`ciudadOrigen`,`codigoPostal`,`empresa`,`universidad`,`secundaria`,`nacionalidad`) values (61,'Rodrigo','Rio',1,30061066,'2','1983-2-16 ','rio_rodrigo@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(62,'Rodrigo','Rio',1,30061061,'2','1983-2-16 ','rio_rodrigo1@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(63,'Matias','Velilla',1,31821427,'2','1985-10-06','matiasvelillamdq@gmail.com','4740327','2236818777','317928372','funes 2862 pa',NULL,NULL,'Mar del Plata','7600','Urbis','FASTA','EET N3','Argentina');

/*Table structure for table `practicas` */

DROP TABLE IF EXISTS `practicas`;

CREATE TABLE `practicas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `practicas` */

/*Table structure for table `provincias` */

DROP TABLE IF EXISTS `provincias`;

CREATE TABLE `provincias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `paises_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_provincias` (`paises_id`),
  CONSTRAINT `FK_provincias_paises` FOREIGN KEY (`paises_id`) REFERENCES `paises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `provincias` */

insert  into `provincias`(`id`,`nombre`,`paises_id`) values (1,'Buenos Aires',1),(2,'San Luis',1);

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

/*Table structure for table `reviews` */

DROP TABLE IF EXISTS `reviews`;

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL COMMENT 'reviewer. optional. hCard.',
  `moderado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `publico` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `descripcionBreve` varchar(100) NOT NULL COMMENT 'Se utiliza tambien en el MetaTag description en vista ampliada.',
  `keywords` varchar(255) NOT NULL COMMENT 'Meta tag keywords en vista ampliada.',
  `activoComentarios` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `itemType` enum('product','business','event','person','place','website','url') DEFAULT NULL COMMENT 'This optional property provides the type of the item being reviewed',
  `itemName` varchar(255) NOT NULL COMMENT 'ITEM must have at a minimum the name',
  `itemEventSummary` varchar(255) DEFAULT NULL COMMENT 'an event item must have the "summary" subproperty inside the respective hCalendar "vevent"',
  `itemUrl` varchar(500) DEFAULT NULL COMMENT 'should provide at least one URI ("url") for the item',
  `rating` double DEFAULT NULL COMMENT 'The rating is a fixed point integer (one decimal point of precision) from 1.0 to 5.0',
  `tags` varchar(500) DEFAULT NULL COMMENT 'OPCIONAL. Se guardan valores tipo  "comida:4/10, ambiente:8/10" y luego se generan los tags para el review. Si no se llena se utilizan los keywords para generar los tags.',
  `fuenteOriginal` varchar(500) DEFAULT NULL COMMENT 'URL de la fuente de donde se extrajo informacion',
  PRIMARY KEY (`id`),
  KEY `FK_reviews_usuarios` (`usuarios_id`),
  CONSTRAINT `FK_reviews_fichas_abstractas` FOREIGN KEY (`id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_reviews_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `reviews` */

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

/*Table structure for table `seguimientos` */

DROP TABLE IF EXISTS `seguimientos`;

CREATE TABLE `seguimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discapacitados_id` int(11) NOT NULL,
  `frecuenciaEncuentros` varchar(50) DEFAULT NULL,
  `diaHorario` datetime DEFAULT NULL,
  `practicas_id` int(11) DEFAULT NULL,
  `usuarios_id` int(11) NOT NULL,
  `antecendentes` text,
  `pronostico` text,
  PRIMARY KEY (`id`),
  KEY `FK_seguimientos_personas` (`discapacitados_id`),
  KEY `FK_seguimientos` (`usuarios_id`),
  KEY `FK_seguimientos_practica` (`practicas_id`),
  CONSTRAINT `FK_seguimientos_discapacitados` FOREIGN KEY (`discapacitados_id`) REFERENCES `discapacitados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_seguimientos_practicas` FOREIGN KEY (`practicas_id`) REFERENCES `practicas` (`id`),
  CONSTRAINT `FK_seguimientos_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos` */

/*Table structure for table `seguimientos_personalizados` */

DROP TABLE IF EXISTS `seguimientos_personalizados`;

CREATE TABLE `seguimientos_personalizados` (
  `id` int(11) NOT NULL,
  `diagnostico_personalizado_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_seguimientos_personalizados_diagnosticos_personalizados` (`diagnostico_personalizado_id`),
  CONSTRAINT `FK_seguimientos_personalizados_diagnosticos_personalizados` FOREIGN KEY (`diagnostico_personalizado_id`) REFERENCES `diagnosticos_personalizado` (`id`),
  CONSTRAINT `FK_seguimientos_personalizados_seguimientos` FOREIGN KEY (`id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos_personalizados` */

/*Table structure for table `seguimientos_scc` */

DROP TABLE IF EXISTS `seguimientos_scc`;

CREATE TABLE `seguimientos_scc` (
  `id` int(11) NOT NULL,
  `diagnostico_scc_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_seguimientos_scc_diagnosticos_scc` (`diagnostico_scc_id`),
  CONSTRAINT `FK_seguimientos_scc_diagnosticos_scc` FOREIGN KEY (`diagnostico_scc_id`) REFERENCES `diagnosticos_scc` (`id`),
  CONSTRAINT `FK_seguimientos_scc_seguimientos` FOREIGN KEY (`id`) REFERENCES `seguimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `seguimientos_scc` */

/*Table structure for table `tratamientos` */

DROP TABLE IF EXISTS `tratamientos`;

CREATE TABLE `tratamientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fechaHorario` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `tratamientos` */

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
  `nombre` varchar(255) NOT NULL,
  `contrasenia` varchar(64) DEFAULT NULL,
  `fechaAlta` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `suspendido` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `invitacionesDisponibles` int(3) DEFAULT '5',
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

insert  into `usuarios`(`id`,`sitioWeb`,`especialidades_id`,`perfiles_id`,`nombre`,`contrasenia`,`fechaAlta`,`suspendido`,`invitacionesDisponibles`) values (61,NULL,NULL,3,'rrio','202cb962ac59075b964b07152d234b70','2011-06-28 02:14:43',0,4),(62,NULL,NULL,3,'rrio2','202cb962ac59075b964b07152d234b70','2011-06-28 02:20:31',0,5),(63,NULL,NULL,1,'matias.velilla','51c50f52501cfc75dc1110dde6700aee','2011-09-05 20:18:35',0,5);

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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
