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

    public function login_post() {
        $account = trim($this->post('account'));
        $password = trim($this->post('password'));

        list($is_auth, $api_key , $expired_time) = $this->auth->login($account, $password);

        if($is_auth) {
            if(!empty($api_key)) {
                $this->_data = array(
                    'api_key'       => $api_key,
                    'expired_time'  => $expired_time
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

    public function check_token_post() {
        $token = trim($this->post('token'));

        list($is_auth, $expired, $user) = $this->auth->check_token($token);

        if ($is_auth)
        {
            $this->_data = array(
                'user_info'  =>  $user,
                'expired'    => $expired
            );
        }
        else
        {
            $this->_code = self::CODE_TOKEN_AUTH_FAILED;
            $this->_msg = '验证失败';
        }

        $this->send_response();


    }

    //获取当前用户信息
    public function user_info_get() {
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

}
