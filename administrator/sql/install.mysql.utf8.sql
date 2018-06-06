CREATE TABLE IF NOT EXISTS `#__formularios_forms` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`name` VARCHAR(150)  NOT NULL ,
`email` VARCHAR(150)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`miscelanea` TEXT  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__formularios_fields` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`formId` int(11)  NOT NULL ,
`field_name` VARCHAR(150)  NOT NULL ,
`field_type` VARCHAR(150)  NOT NULL ,
`field_long` VARCHAR(150)  NOT NULL ,
`field_required` VARCHAR(150)  NOT NULL ,
`field_column` INT(5)  NOT NULL DEFAULT 12,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__formularios_stored` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`formId` int(11)  NOT NULL ,
`data_missatge` DATETIME  NOT NULL ,
`message` TEXT  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;
