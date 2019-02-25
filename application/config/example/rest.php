<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['rest_key_name'] = 'X-TOKEN';  //在header中传递的token键名

$config['rest_keys_table'] = 'api_token';  //Token表名

$config['rest_key_column'] = 'api_key'; //Token表中表示key的键名

$config['rest_logs_table'] = 'api_log'; //Log表名

//不需要验证 key 的uri列表  (单个uri由 [请求方法]|[目录]/[控制其名]/[方法名] 组成)
$config['rest_ignore_uris'] = [
    'post|example/auth/login',
    'post|example/auth/token-verification'
];

//api_key 过期时间
$config['rest_api_key_expired_time'] = 3600 * 24;


$config['check_cors'] = TRUE; //启用跨域

$config['allowed_cors_headers'] = [
    'Origin',
    'X-Requested-With',
    'Content-Type',
    'Accept',
    'Access-Control-Request-Method',
    $config['rest_key_name']
];

$config['allow_any_cors_domain'] = TRUE;