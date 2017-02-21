<?php
return "CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ ."video` (
    `id_video` INT(11) NOT NULL AUTO_INCREMENT,
    `id_product` INT(11) NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_video`),
    UNIQUE (`id_product`)
    ) ENGINE=InnoDB;";
