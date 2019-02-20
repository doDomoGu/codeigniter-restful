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

        list($is_auth, $user , $token_info) = $this->auth->login($account, $password);

        if ($is_auth)
        {
            if(!empty($token_info)){
                $this->_data = array(
                    'user_info'  => $user,
                    'key'      => $token_info['key'],
                    'expired'    => $token_info['expired']
                );
            }else{
                $this->_code = self::CODE_TOKEN_CREATE_FAILED;
                $this->_msg = 'Token创建失败';
            }
        }
        else
        {
            if($user){
                $this->_code = self::CODE_AUTH_FAILED;
                $this->_msg = '用户验证错误';
            }else{
                $this->_code = self::CODE_USER_NOT_FOUND;
                $this->_msg = '用户不存在,请联系管理员';
            }
        }

        $this->send_response();
    }

    public function key_get() {


    }

}
