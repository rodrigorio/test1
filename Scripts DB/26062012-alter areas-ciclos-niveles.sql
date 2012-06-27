ALTER TABLE `tesis`.`areas`     ADD COLUMN `descripcion` VARCHAR(50) NOT NULL AFTER `ciclos_id`;
ALTER TABLE `tesis`.`ciclos`     ADD COLUMN `descripcion` VARCHAR(50) NOT NULL AFTER `niveles_id`;
ALTER TABLE `tesis`.`niveles`     ADD COLUMN `descripcion` VARCHAR(50) NOT NULL AFTER `id`;