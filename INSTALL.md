# 安装说明

## 多环境判断
`index.php` 有一行
```php
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
```
设置`$_SERVER['CI_ENV']`来改变环境运行参数
以nginx为例
```
server {
    listen 80;
    server_name abc.com;
    root /data/www/vue-pro/api;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    #location /index.php {
    location ~ \.php($|/) {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_split_path_info ^(.+\.php)(.*)$;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param  SCRIPT_FILENAME   $document_root$fastcgi_script_name;

        fastcgi_param  CI_ENV   'production';  #添加这一行
        
        include        fastcgi_params;
    } 
}
```

## 配置项
配置文件在 `application/config` 下
> 以生产环境配置来举例   

如果 `$_SERVER['CI_ENV']`的值为 `production`，则会读取 `application/config/production` 下的配置，有选择的覆盖基础配置   
将`application/config/example` 下的文件拷贝至 `application/config/production`


## 数据库 (初始化构建表)

### User表 
```
CREATE TABLE `user` (
    `id` int(20) NOT NULL AUTO_INCREMENT,
    `account` varchar(100) CHARACTER SET utf8 NOT NULL,
    `password` varchar(100) CHARACTER SET utf8 NOT NULL,
    `name` varchar(100) CHARACTER SET utf8 NOT NULL,
    `role_id` tinyint(1) NOT NULL DEFAULT '0',
    `status` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
### 新建管理员账号  (账号:admim,密码:admin,密码使用md5加密)
```
INSERT INTO `user` VALUES(default, 'admin', md5('admin'), 'Admin', 1, 1);
```

### Api_token表 （根据 'rest_keys_table'配置对应表名) 
```
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
```

### Api_log表 （根据 'rest_logs_table'配置对应表名) 

```
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
```