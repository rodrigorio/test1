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