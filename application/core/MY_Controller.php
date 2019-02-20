<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class MY_Controller extends REST_Controller {

    public $_code = 0;

    public $_data = null;

    public $_msg = null;

    private $_key = NULL;

    private $_authorized = NULL;

    private $_log_insert_id = 0;

    function __construct() {

        //记录开始时间
        $this->_start_log();

        parent::__construct();

        $this->load->database();

        //权限验证
        $this->check_authorization();

    }

    //重写 rest_controller 的 destruct
    function __destruct() {
        //parent::__destruct();

        //记录结束时间，计算运行时间
        $this->_end_log();


    }


    /*
           CREATE TABLE `logs` (
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

    private function _log($response, $http_code){

        $data = array(
            'uri' => $this->uri->uri_string(), //site_url($this->uri->uri_string())
            'method' => $this->request->method,
            'params' => json_encode($this->{$this->request->method}()),
            'api_key' => $this->_key ,
            'ip_address' => $this->input->ip_address(),
            'time' => date('Y-m-d H:i:s'),
            'authorized' => $this->_authorized,
            'response_data' => json_encode($response),
            'response_code' => $http_code
        );

        $this->db->insert(
            $this->config->item('rest_logs_table'),
            $data
        );

        $this->_log_insert_id = $this->db->insert_id();

    }

    private function _start_log(){

        $this->_start_rtime = microtime(TRUE);

    }

    private function _end_log(){

        $this->_end_rtime = microtime(TRUE);
        //更新log的runtime
        $this->db->update(
            $this->config->item('rest_logs_table'),
            [
                'rtime' => $this->_end_rtime - $this->_start_rtime
            ],
            [
                'id' => $this->_log_insert_id
            ]
        );
    }

    protected function send_response(){
        $response = array();

        $response['code'] = $this->_code;
        $response['data'] = $this->_data;
        $response['msg'] = $this->_msg;

        $this->set_response($response, REST_Controller::HTTP_OK);

        $this->_log($response, REST_Controller::HTTP_OK);

    }

    protected function send_error($err_msg, $http_code){
        $response = array();

        $response['msg'] = $err_msg;

        $this->set_response($response, $http_code);

        $this->_log($response, $http_code);

        //发生错误信息，直接调用结束流程
        $this->__destruct();

        $this->output->_display();

        exit;
    }


    // 不用检查权限的地址
    private function _is_uri_ignore(){
        $dir = $this->router->fetch_directory();//获取目录
        $ctrl = $this->router->fetch_class();//获取控制器名
        $act = $this->router->fetch_method();//获取方法名

        $uri = $dir . $ctrl .'/'. $act;

        //不需要检查api-key的地址
        $ignoreUris = $this->config->item('rest_ignore_uris');

        foreach($ignoreUris as $u) {
            if($u == $uri){
                return true;
            }
        }

        return false;
    }

    protected function check_authorization(){

        if(!$this->_is_uri_ignore()){
            $this->_key = $this->input->get_request_header($this->config->item('rest_key_name'),true);

            $this->_authorized = $this->_key == '101010';

            if(!$this->_authorized){

                $this->send_error('API KEY WRONG', REST_Controller::HTTP_UNAUTHORIZED);
            }
        }

    }

//    protected function _check_token(){
//
//
//        $token = $this->input->get_request_header('X-Token',true);
//
//        $this->load->model('Auth_Model','auth');
//
//        list($is_auth, $user_id) = $this->auth->auth_token($token);
//
//        if($is_auth)
//        {
//            $this->user_id = $user_id;
//
//            $this->load->model('User_Model','user');
//
//            $user = $this->user->get_one($user_id);
//
//            $this->is_admin = (int) $user['role_id'] === $this->user->admin_role_id;
//
//            $this->is_reporter = (int) $user['role_id'] === $this->user->reporter_role_id;
//
//            $this->is_media = (int) $user['role_id'] === $this->user->media_role_id;
//
//        }
//        else
//        {
//            $this->response([
//                'err_msg'=>'wrong token'
//            ], self::HTTP_UNAUTHORIZED);
//        }
//    }
}