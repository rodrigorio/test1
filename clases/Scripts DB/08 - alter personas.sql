ALTER TABLE `personas` CHANGE `email` `email` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
ALTER TABLE `personas` CHANGE `documento_tipos_id` `documento_tipos_id` INT( 11 ) NULL ,
CHANGE `numeroDocumento` `numeroDocumento` VARCHAR( 30 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ;