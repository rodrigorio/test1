CREATE TABLE `diagnosticos_scc` (
  `id` int(11) NOT NULL,
  `areas_id` int(11) DEFAULT NULL COMMENT 'estado inicial',
  PRIMARY KEY (`id`),
  KEY `FK_diagnosticos_scc_areas` (`areas_id`),
  CONSTRAINT `FK_diagnosticos_scc_areas` FOREIGN KEY (`areas_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_diagnosticos_scc_diagnosticos` FOREIGN KEY (`id`) REFERENCES `diagnosticos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;