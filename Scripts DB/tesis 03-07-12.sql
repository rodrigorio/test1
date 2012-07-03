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

insert  into `archivos`(`id`,`fichas_abstractas_id`,`seguimientos_id`,`usuarios_id`,`categorias_id`,`nombre`,`nombreServidor`,`descripcion`,`tipoMime`,`tamanio`,`fechaAlta`,`orden`,`titulo`,`tipo`,`moderado`,`activo`,`publico`,`activoComentarios`) values (45,NULL,NULL,63,NULL,'unArchivoDePrueba.pdf','63_curriculum_1336183420_unArchivoDePrueba.pdf',NULL,'application/pdf',84665,'2012-05-04 23:03:40',1,NULL,'cv',0,1,0,0),(49,NULL,28,NULL,NULL,'Un Nuevo Archivo.pdf','28_seguimiento_1339515986_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 12:46:26',NULL,NULL,'adjunto',0,1,0,0),(50,NULL,28,NULL,NULL,'Un Nuevo Archivo.pdf','28_seguimiento_1339516139_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 12:48:59',NULL,NULL,'adjunto',0,1,0,0),(51,8,NULL,NULL,NULL,'Un Nuevo Archivo.pdf','8_publicacion_1339535582_Un_Nuevo_Archivo.pdf','una descripcion 122','application/pdf',280317,'2012-06-12 18:13:02',122,'un titulo 122','adjunto',0,1,0,0),(52,8,NULL,NULL,NULL,'Un Nuevo Archivo.pdf','8_publicacion_1339535588_Un_Nuevo_Archivo.pdf','dsfasdfasdfads 2222','application/pdf',280317,'2012-06-12 18:13:08',NULL,'sadfasdfads 2','adjunto',0,1,0,0),(54,NULL,28,NULL,NULL,'Un Nuevo Archivo.pdf','28_seguimiento_1339535709_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 18:15:09',NULL,NULL,'adjunto',0,1,0,0),(55,NULL,28,NULL,NULL,'Un Nuevo Archivo.pdf','28_seguimiento_1339535712_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 18:15:12',NULL,NULL,'adjunto',0,1,0,0),(56,NULL,28,NULL,NULL,'Un Nuevo Archivo.pdf','28_seguimiento_1339535715_Un_Nuevo_Archivo.pdf',NULL,'application/pdf',280317,'2012-06-12 18:15:15',NULL,NULL,'adjunto',0,1,0,0),(64,NULL,29,NULL,NULL,'Archivoasd.pdf','63_antecedentes_1339645910_Archivoasd.pdf',NULL,'application/pdf',280317,'2012-06-14 00:51:50',NULL,NULL,'antecedentes',0,1,0,0);

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

/*Data for the table `comentarios` */

insert  into `comentarios`(`id`,`reviews_id`,`publicaciones_id`,`archivos_id`,`fecha`,`descripcion`,`valoracion`,`usuarios_id`,`nombreApellido`) values (5,NULL,8,NULL,'2012-06-16 15:06:52','sadasdasdas',0,63,'Anonimo'),(7,NULL,8,NULL,'2012-06-16 15:08:56','adsafdsfdsfds',0,63,'Anonimo'),(8,NULL,8,NULL,'2012-06-17 15:59:17','a\na\na\na\na\na\na\na\na\n',0,63,'Anonimo'),(9,3,NULL,NULL,'2012-06-17 20:15:52','Che muy groso muy bueno !!!',0,63,'Anonimo'),(12,NULL,1,NULL,'2012-06-17 20:20:41','adsalksjdaskld',0,63,'Anonimo'),(13,3,NULL,NULL,'2012-06-18 14:03:47','werwerqr',0,63,'Anonimo'),(14,3,NULL,NULL,'2012-06-21 19:36:08','rewrwerew',0,63,'Anonimo');

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

/*Data for the table `embed_videos` */

insert  into `embed_videos`(`id`,`fichas_abstractas_id`,`seguimientos_id`,`codigo`,`orden`,`titulo`,`descripcion`,`origen`,`urlKey`) values (20,1,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,NULL,NULL,'YouTube','18eb4fa91b3a41298a9202c94a950d08'),(21,NULL,28,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,'fklashdflkjashd','sladfhasldkfhakjdslfads','YouTube','f5a17b8a696c423d8e9da64171e03150'),(22,8,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,'dfsafadsf 1','adsfasdfads 1','YouTube','2145a2e07b71444338a1963e56a0881d'),(23,8,NULL,'http://www.youtube.com/watch?v=ikTxfIDYx6Q',NULL,'sdfasfadsfasd 2','sadfadsfadsf 222','YouTube','352a52072e85aca6360afd0b6a41ca56');

/*Table structure for table `fichas_abstractas` */

DROP TABLE IF EXISTS `fichas_abstractas`;

CREATE TABLE `fichas_abstractas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

/*Data for the table `fichas_abstractas` */

insert  into `fichas_abstractas`(`id`,`titulo`,`fecha`,`activo`,`descripcion`) values (1,'Primer Publicacion','2012-05-18 08:18:15',1,'sdfhaskdfjh adskfh asdkfh asdkfh asdkfh asdkfh asd\nasdkjfh askdfh askdjfh askjfh adskjfh asdfkj \n\naksjdfh askdjfh akdshf aksdfh kasdfh aksjdfh kasdfh kajsdhf akdsjfh asd\nfasdkjfh askdfh akdsfh daksfh askdfh aksdfh aksjdfh asd\nfaskfhdasdfk hasdkfh asdkfjh a\n\naksdfh akdsfh akdsjfh akdsjfh aksjdfh askdjfh \naksjdfh akjdsfh aksdh fakdsjfh aksjdfh adsjkf\nkasdfh akdsjfh akjsdfh adfskh \n\nkajsdfh akdsjfh aksjdh fkjdsh fkjasdhf kjdsh fkjsdfh \nakjfh akdsjfh akjdshf akjdfh sd\naksdjfh aksjdfh kasjdfh akjdsh faksjdhf kajdsh\n\ndsjfh akjdshf akjdfh sd\naksdjfh aksjdfh kasjdfh akjdsh faksjdhf kajdsh\n\nasdfadsfdsfds'),(3,'nueva feria artesanal en mar del plata 123','2012-05-19 05:42:20',1,'dfasdfasdf\nfasdfads\nfasdf\nadsfasd\nfasdf\nadsfa\ndsfasdf\nsdfadskfhaskdjfhaklsjdfhas 123\n'),(5,'sdaf asdfadsfa sdfadsf ','2012-05-30 06:27:59',1,'adsfa dsfadsf\nadsfasdfsdfasdfasdfasd\nfasdfads\n\nasdfjadskl fjas\ndf asldfkja sldkjf ads\nf asldkjf adslkfj sad\nfasdklf jasd'),(8,'Cambio el titulo','2012-05-30 06:29:11',1,'adsfasdf\nads\nfa\nsdf\nasdf\nasd\nf\nasdf\nasd\nfa\nsdf asdfasdfadsf 111 asdfÃ±lkj asdÃ±lfkja dsÃ±lfkj asdfsad\nfas\ndfas\ndf\nasdf\nadsf\nadsf\nasd\nfs adflaskdj fklasjd f\n'),(9,'wterwtwert','2012-07-03 02:11:26',1,'wetert\nwertwer\ntwert\nerwt\newrt'),(10,'rtwertwer terwtwe rt','2012-07-03 02:11:39',1,'wertwert\nerwtwer\ntwer\ntwertwet\newrt\nwet\nwertet'),(11,'asdfasdf','2012-07-03 02:25:40',1,'asdfasdkfasdf\nasdf\nasdf\nasdf\nasdf\nasdf\nasdf\nas\ndf\n\n\nadfsadsfadsf');

/*Table structure for table `instituciones` */

DROP TABLE IF EXISTS `instituciones`;

CREATE TABLE `instituciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ciudades_id` int(11) DEFAULT NULL,
  `nombre` varchar(80) DEFAULT NULL,
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

insert  into `instituciones`(`id`,`ciudades_id`,`nombre`,`descripcion`,`tipoInstitucion_id`,`direccion`,`email`,`telefono`,`sitioWeb`,`horariosAtencion`,`autoridades`,`cargo`,`personeriaJuridica`,`sedes`,`actividadesMes`,`usuario_id`,`latitud`,`longitud`) values (33,1,'Fasta','Colegio y universidad',1,'Gascon 2332','rio_rodrigo@hotmail.com','123456','www.ufasta.edu.ar','de 8 a 22','roberto','Decano','Rodrigo Rio','Bariloche','Misa los domingos',61,'-37.9940139','-57.5495844'),(49,1,'IAC','Institutu de educacion',1,'Colon 2323','rodrigo.a.rio@gmail.com','123456',NULL,NULL,NULL,'Estudiante',NULL,NULL,NULL,61,'-38.0048712','-57.54728499');

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
  CONSTRAINT `FK_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Data for the table `moderaciones` */

insert  into `moderaciones`(`id`,`fichas_abstractas_id`,`instituciones_id`,`estado`,`mensaje`,`fecha`) values (17,11,NULL,'aprobado','Moderacion automatica por perfil Administrador o Moderador.','2012-07-03 02:25:40');

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

insert  into `publicaciones`(`id`,`usuarios_id`,`publico`,`activoComentarios`,`descripcionBreve`,`keywords`) values (1,63,0,1,'esta es una descripcion tan breve no ? 123 132','uno dos tres cuatro cinco cinco seis siete'),(5,63,0,1,'a sdfads fadsfa sdfasd qdsad asd','asdfads asdfadsf asdfsdf'),(8,63,0,1,'asdfasdf adsf asdfds fdsf 33','1 2 3 4'),(9,63,0,1,'weterwtwertwert','wr'),(10,63,0,1,'wret erwtwer twret','134324324324'),(11,63,1,1,'sdafadsfadsf adsfadf adfs dfdf adsfads','123');

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

insert  into `reviews`(`id`,`usuarios_id`,`publico`,`descripcionBreve`,`keywords`,`activoComentarios`,`itemType`,`itemName`,`itemEventSummary`,`itemUrl`,`rating`,`fuenteOriginal`) values (3,63,0,'sdfasdfklasj fhaklsjdfh lakjdsh flakjdsfh akldshf aldskjfh askjldf  123','sdf sdf sd sdf sfds  123',1,'product','Feria arte Sheraton 123','Feria artesanal Sheraton Mar del Plata, la 4ta de mar del plata','http://www.ldfkjdsk2123lfj.com',3,'http://www.lasdkfjda123slkfj.com');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
