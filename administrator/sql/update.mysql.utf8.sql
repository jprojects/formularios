ALTER TABLE `#__formularios_fields` ADD `field_msg_success` VARCHAR(150) NOT NULL DEFAULT 'COM_FORMULARIOS_VALIDATION_SUCCESS_MSG' AFTER `field_column`;

ALTER TABLE `#__formularios_fields` ADD `field_msg_error` VARCHAR(150) NOT NULL DEFAULT 'COM_FORMULARIOS_VALIDATION_ERROR_MSG' AFTER `field_msg_success`;

ALTER TABLE `#__formularios_fields` ADD `field_readonly` TINYINT(1) NOT NULL DEFAULT 0 AFTER `field_msg_error`;

ALTER TABLE `#__formularios_fields` ADD `field_disabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `field_readonly`;
