<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class MY_Controller extends REST_Controller {

    public $_code = 0;

    public $_data = null;

    public $_msg = null;

    public $user_id = 0;

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

        //options的请求直接结束
        if ($this->input->method() !== 'options') {

            //记录结束时间，计算运行时间
            $this->_end_log();
        }
    }


    private function _log($response, $http_code){

        $data = array(
            'uri' => $this->uri->uri_string(), //site_url($this->uri->uri_string())
            'method' => $this->request->method,
            'params' => json_encode($this->{$this->request->method}()),
            $this->config->item('rest_key_column') => $this->_key ,
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
        $method = $this->input->method();//获取请求类型

        $dir = $this->router->fetch_directory();//获取目录
        $ctrl = $this->router->fetch_class();//获取控制器名
        $act = $this->router->fetch_method();//获取方法名

        $RTR =& load_class('Router', 'core', isset($routing) ? $routing : NULL);
        $ctrl = $RTR->translate_uri_dashes === TRUE ? str_replace('_', '-', $ctrl) : $ctrl;
        $act = $RTR->translate_uri_dashes === TRUE ? str_replace('_', '-', $act) : $act;

        $uri = $method . '|' . $dir . $ctrl .'/'. $act;

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

            $this->load->model('Auth_Model', 'auth');

            list($this->_authorized, $user_id) = $this->auth->auth_token($this->_key);

            if(!$this->_authorized){

                $this->send_error('API KEY WRONG', REST_Controller::HTTP_UNAUTHORIZED);
            } else {
                $this->user_id = $user_id;
//
//                $this->load->model('User_Model','user');
//
//                $user = $this->user->get_one($user_id);
//
//                $this->is_admin = (int) $user['role_id'] === $this->user->admin_role_id;
//
//                $this->is_reporter = (int) $user['role_id'] === $this->user->reporter_role_id;
//
//                $this->is_media = (int) $user['role_id'] === $this->user->media_role_id;
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