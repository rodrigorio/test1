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

/*Table structure for table `institucion_solicitudes` */

DROP TABLE IF EXISTS `institucion_solicitudes`;

CREATE TABLE `institucion_solicitudes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(11) NOT NULL,
  `instituciones_id` int(11) NOT NULL,
  `mensaje` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_institucion_solicitudes_usuarios` (`usuarios_id`),
  KEY `FK_institucion_solicitudes_instituciones` (`instituciones_id`),
  CONSTRAINT `FK_institucion_solicitudes_instituciones` FOREIGN KEY (`instituciones_id`) REFERENCES `instituciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_institucion_solicitudes_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `institucion_solicitudes` */

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
  CONSTRAINT `instituciones_fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_instituciones_ciudades` FOREIGN KEY (`ciudades_id`) REFERENCES `ciudades` (`id`),
  CONSTRAINT `instituciones_fk_tipos` FOREIGN KEY (`tipoInstitucion_id`) REFERENCES `instituciones_tipos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=latin1;

/*Data for the table `instituciones` */

insert  into `instituciones`(`id`,`ciudades_id`,`nombre`,`descripcion`,`tipoInstitucion_id`,`direccion`,`email`,`telefono`,`sitioWeb`,`horariosAtencion`,`autoridades`,`cargo`,`personeriaJuridica`,`sedes`,`actividadesMes`,`usuario_id`,`latitud`,`longitud`) values (33,1,'Universidad FASTA','dasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\n\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh askjfh asd\nfa sdkfjha sdkfjha skdfh askfh askjdf\ndasfasd fkjash dfkjadsh faskjdfh aklsdjfh as',1,'Gascon 10293','adsfadsf@dskjfh.com','1324324234','http://www.ufasta.edu.ar','de lunes a viernes 16:00 a 21:00','asdfahskf adfkjadfj\nakdfsjhaksjdh askjdfh akjsdfh \nadfkjah dsf asdfkjh asd\n','Director General bleble','asdfasdf  XXIVV','dsklfajdskfh ads\nf adskjf askdjf\n\ndsklfajdskfh ads\nf adskjf askdjf\ndsklfajdskfh ads\nf adskjf askdjf\ndsklfajdskfh ads\nf adskjf askdjf\n\ndsklfajdskfh ads\nf adskjf askdjf','asdfñlkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh \n\nasdfñlkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh \nasdfñlkjas dflkja sdflkjash dflkash \nasdkjlf fdkjhsk fdjhds kfs\ndf ksjdh fkdsjfh ',63,NULL,NULL),(57,1,'LADFSKJ ','sdlfÃ±jasdflk jads\nf asdlfj asdlkfja dslfkja ds\nfasdl fj asdlfkj asldfkj asldfkj asdf\nasdlfkj asdflkj adslfkj asd\nfasdflkj asdfklj',1,'adsf 1234','sadfadsf@sdkjlf.com','12312312',NULL,NULL,'','asdfsdf',NULL,'','',63,NULL,NULL),(58,2,'fgsdfg','sdfgdfg',2,'adsf 123','fasdf@laskjdf.com','1323123123',NULL,NULL,NULL,'asdfadsf',NULL,NULL,NULL,63,NULL,NULL),(59,1,'adfsadsfaa dasf asdfa sdf','adsfadsf\nasdfas\ndfasdf\nasdf\nasdf',2,'adsfadsf 132123','fkafhsd213@kj.com','23423432',NULL,NULL,NULL,'adsfasdf',NULL,NULL,NULL,63,NULL,NULL),(60,3,'dsfasdfas 123','sdfadsfsadf\nasdfa\nsdfas\ndfa\nsdf\nasdf\nafsd',2,'asdfas 2134 adsfas','asdfafs@lkadsjf.com','13123123',NULL,NULL,NULL,'1123sdfdsfasdf',NULL,NULL,NULL,63,NULL,NULL),(61,1,'dfsdsf 23423','sdfdsgfdsgdfsgdf',2,'fdsg 234','sdfgdfsg@sdlfk.com','12371928',NULL,NULL,NULL,'sgfdsgdfsg',NULL,NULL,NULL,63,NULL,NULL);

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
  CONSTRAINT `FK_instituciones` FOREIGN KEY (`instituciones_id`) REFERENCES `instituciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;

/*Data for the table `moderaciones` */

insert  into `moderaciones`(`id`,`fichas_abstractas_id`,`instituciones_id`,`estado`,`mensaje`,`fecha`) values (17,11,NULL,'aprobado','Moderacion automatica por perfil Administrador o Moderador.','2012-07-03 02:25:40'),(18,8,NULL,'rechazado','asdasdasdsa','2012-07-03 19:34:28'),(19,8,NULL,'aprobado','asdasdasdas','2012-07-03 19:34:38'),(20,8,NULL,'pendiente',NULL,'2012-07-03 19:34:43'),(21,3,NULL,'aprobado','adasdasdas','2012-07-03 19:34:51'),(22,3,NULL,'aprobado','fdsfsfdsfdsf','2012-07-03 19:34:58'),(23,3,NULL,'pendiente',NULL,'2012-07-03 19:35:01'),(33,1,NULL,'pendiente',NULL,'2012-07-09 02:19:15'),(34,5,NULL,'pendiente',NULL,'2012-07-09 02:19:21'),(35,9,NULL,'pendiente',NULL,'2012-07-09 02:19:24'),(36,10,NULL,'pendiente',NULL,'2012-07-09 02:19:28'),(37,11,NULL,'aprobado','adsfadsfsdf','2012-07-09 02:19:30'),(38,NULL,57,'pendiente',NULL,'2012-07-09 02:33:15'),(39,NULL,58,'pendiente',NULL,'2012-07-09 02:40:01'),(40,NULL,59,'pendiente',NULL,'2012-07-09 02:40:29'),(41,NULL,60,'pendiente',NULL,'2012-07-09 02:40:57'),(42,NULL,61,'pendiente',NULL,'2012-07-09 02:41:40'),(44,NULL,33,'aprobado','Moderacion automatica por perfil Administrador o Moderador.','2012-07-09 07:34:01');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
