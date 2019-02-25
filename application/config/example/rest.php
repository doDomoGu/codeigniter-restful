<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['rest_key_name'] = 'X-TOKEN';

/*
CREATE TABLE `api_token` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(20) NOT NULL,
    `api_key` VARCHAR(100) NOT NULL,
    `level` INT(2) NOT NULL,
    `ignore_limits` TINYINT(1) NOT NULL DEFAULT '0',
    `created_time` datetime NOT NULL,
    `expired_time` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

$config['rest_keys_table'] = 'api_token';

$config['rest_key_column'] = 'api_key';

/*
CREATE TABLE `api_log` (
   `id` INT(11) NOT NULL AUTO_INCREMENT,
   `uri` VARCHAR(255) NOT NULL,
   `method` VARCHAR(6) NOT NULL,
   `params` TEXT DEFAULT NULL,
   `api_key` VARCHAR(100) DEFAULT NULL,
   `ip_address` VARCHAR(45) NOT NULL,
   `time` datetime NOT NULL,
   `rtime` FLOAT DEFAULT NULL,
   `authorized` VARCHAR(1) DEFAULT NULL,
   `response_data` TEXT DEFAULT NULL,
   `response_code` smallint(3) DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

$config['rest_logs_table'] = 'api_log';

//不需要验证 key 的uri列表
$config['rest_ignore_uris'] = [
    'post|example/auth/login',
    'post|example/auth/token-verification'
];

//api_key 过期时间
$config['rest_api_key_expired_time'] = 3600 * 24;


$config['check_cors'] = TRUE;

$config['allowed_cors_headers'] = [
    'Origin',
    'X-Requested-With',
    'Content-Type',
    'Accept',
    'Access-Control-Request-Method',
    $config['rest_key_name']
];

$config['allow_any_cors_domain'] = TRUE;