# CodeIgniter-RESTful

### 一个基于CodeIgniter的RESTful风格的API框架  [CI的文档手册](http://codeigniter.org.cn/user_guide/general/welcome.html)

> 本项目使用了CI的v3.1.10版本

## 目录结构

* application 项目应用层代码
  * config 配置目录 
    * development 开发环境 （同名的配置文件会覆盖，其下文件不上传至git库）
    * production 生产环境 （同上）
    * example  样例 
* system CI的源代码（一般不要去动）
* index.php 项目入口文件
<!-- * composer.json: 声明所需要依赖的PHP代码库，需要执行composer install安装依赖。
* vendor: composer依赖包的目录（不用管，不用进git库） -->


## 美化URL (nginx配置)

使url地址变成 [domain]/控制器名(类名)/方法名 的显示方式   
例如：
> http://***.com/product/create      
> http://***.com/product/update


```
# nginx.conf

server {
    listen 80;
    server_name ***;
    root ***/codeigniter-restful;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location /index.php {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_split_path_info ^(.+\.php)(.*)$;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param  SCRIPT_FILENAME   $document_root$fastcgi_script_name;
        include        fastcgi_params;
    } 
}
```

## RESTful

### 引入 REST_Controller 类
```
application/config/rest.php   - 配置文件

application/libraries/REST_Controller.php  - REST控制器类

application/libraries/Format.php - 格式化工具包
```


## 多环境配置

[文档参考](http://codeigniter.org.cn/user_guide/libraries/config.html#config-environments)

* 新建目录 application/config/production/
* 将已有的 config.php 文件拷贝到该目录
* 编辑 application/config/production/config.php 文件，使用生产环境下配置


## 接口

> 以下省略了 /example 目录， 如 /auth/login 实际访问地址为 /example/auth/login
> 如果api有版本需求建议目录结构为 /v1/auth/login 和 /v2/auth/login 这样

* 鉴权 (Auth)
  * POST &nbsp; &nbsp; &nbsp; /auth/login &nbsp; &nbsp; &nbsp; 用户登录 &nbsp;（header无需token)
  * POST &nbsp; &nbsp; &nbsp; /auth/token-verification &nbsp; &nbsp; &nbsp; Token验证 &nbsp; (header无需token)
  * DELETE &nbsp; &nbsp; &nbsp; /auth/logout &nbsp; &nbsp; &nbsp; 用户退出登录
  * GET &nbsp; &nbsp; &nbsp; /auth/info &nbsp; &nbsp; &nbsp; 获得登录用户信息

* 用户 (User)


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