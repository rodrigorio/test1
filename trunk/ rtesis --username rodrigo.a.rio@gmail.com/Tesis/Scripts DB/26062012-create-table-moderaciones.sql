CREATE TABLE `moderaciones` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fichas_abstractas_id` INT(11) DEFAULT NULL,
  `estado` ENUM('rechazado','aprobado','pendiente') NOT NULL DEFAULT 'pendiente',
  `mensaje` VARCHAR(500) DEFAULT NULL,
  `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_fichas_abstractas` (`fichas_abstractas_id`),
  CONSTRAINT `FK_fichas_abstractas` FOREIGN KEY (`fichas_abstractas_id`) REFERENCES `fichas_abstractas` (`id`) ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1