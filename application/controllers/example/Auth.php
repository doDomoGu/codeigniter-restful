<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

    const CODE_AUTH_FAILED = 10001;
    const CODE_TOKEN_CREATE_FAILED = 10002;
    const CODE_TOKEN_AUTH_FAILED = 10003;
    const CODE_USER_LOGOUT_FAILED = 10004;
    const CODE_USER_NOT_FOUND = 10005;

    public function __construct() {

        parent::__construct();

        $this->load->model('Auth_Model','auth');
    }

    //用户登录
    public function login_post() {
        $account = trim($this->post('account'));
        $password = trim($this->post('password'));

        list($is_auth, $api_key , $expired_time) = $this->auth->login($account, $password);

        if($is_auth) {
            if(!empty($api_key)) {
                $this->_data = array(
                    $this->config->item('rest_key_column') => $api_key,
                    //'expired_time'  => $expired_time
                );
            } else {
                $this->_code = self::CODE_TOKEN_CREATE_FAILED;
                $this->_msg = 'Token创建失败';
            }
        } else {
            $this->_code = self::CODE_AUTH_FAILED;
            $this->_msg = '用户验证错误';
        }

        $this->send_response();
    }

    //验证token （用于刷新页面时，使用LocalStorage中储存的key请求服务器验证，验证通过后自动登录）
    public function token_verification_post() {
        $key = trim($this->post('key'));

        list($is_auth, $expired, $user) = $this->auth->check_token($key);

        if (!$is_auth) {
            $this->_code = self::CODE_TOKEN_AUTH_FAILED;
            $this->_msg = '验证失败';
        }

        $this->send_response();


    }

    //获取当前用户信息
    public function info_get() {
        $one = $this->user->get_one($this->user_id);
        if ($one) {
            $data = array(
                'id' => (int) $one['id'],
                'name' => $one['name'],
                'roles' => array($this->user->role_list[$one['role_id']])
            );
            $this->_data = $data;
        } else {
            $this->_code = 10003;
            $this->_msg = '获取用户信息失败';
        }
        $this->send_response();
    }

    //用户退出
    public function logout_delete()
    {
        $token = $this->input->get_request_header($this->config->item('rest_key_name'),true);

        $result = $this->auth->disable_token($token);

        if (!$result)
        {
            $this->_code = self::CODE_USER_LOGOUT_FAILED;
            $this->_msg = '用户退出失败';
        }

        $this->send_response();
    }

}
