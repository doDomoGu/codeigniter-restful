# CodeIgniter-RESTful

### 一个基于CodeIgniter的RESTful风格的API框架  [CI的文档手册](http://codeigniter.org.cn/user_guide/general/welcome.html)

> 本项目使用了CI的v3.1.10版本

## 目录结构

* application: 项目应用层代码
* system: CI的源代码（一般不要去动）
* index.php: 项目入口文件
<!-- * composer.json: 声明所需要依赖的PHP代码库，需要执行composer install安装依赖。
* vendor: composer依赖包的目录（不用管，不用进git库） -->


## 美化URL (nginx)

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
