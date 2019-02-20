<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//$config['rest_key_name'] = 'X-API-KEY';

/*
CREATE TABLE `api_keys` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(20) NOT NULL,
    `key` VARCHAR(40) NOT NULL,
    `level` INT(2) NOT NULL,
    `ignore_limits` TINYINT(1) NOT NULL DEFAULT '0',
    `date_created` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

$config['rest_keys_table'] = 'api_keys';

/*
CREATE TABLE `api_logs` (
   `id` INT(11) NOT NULL AUTO_INCREMENT,
   `uri` VARCHAR(255) NOT NULL,
   `method` VARCHAR(6) NOT NULL,
   `params` TEXT DEFAULT NULL,
   `api_key` VARCHAR(40) DEFAULT NULL,
   `ip_address` VARCHAR(45) NOT NULL,
   `time` INT(11) NOT NULL,
   `rtime` FLOAT DEFAULT NULL,
   `authorized` VARCHAR(1) DEFAULT NULL,
   `response_data` TEXT DEFAULT NULL,
   `response_code` smallint(3) DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

$config['rest_logs_table'] = 'api_logs';