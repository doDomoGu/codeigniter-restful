<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_Model extends MY_Model {

    public function __construct()
    {
        parent::__construct();

//        $this->load->database();

//        $this->field_map['ID'] = 'id';

        $this->table_name = $this->config->item('rest_keys_table');

        $this->load->model('User_Model', 'user');
    }

    /**
     * 函数 login
     * 使用 账号(account)、密码(password) 验证登录
     *
     * @param string $account 账号
     * @param string $password 密码
     *
     * @return array 数组
     *           boolean is_auth 是否验证成功
     *           string api_key  秘钥
     *           string expired_time 过期时间
     */
    public function login ($account, $password) {
        $is_auth = false;
        $api_key = null;
        $expired_time = null;

        $user = $this->user->get_one_by_account($account);
        if($user && $user['password'] == md5($password)) {
            $is_auth = true;
        }

        if($is_auth) {
            list($result, $key, $expired) = $this->_create_token($user['id']);
            if($result) {
                $api_key = $key;
                $expired_time = $expired;
            }
        }
        return array($is_auth, $api_key, $expired_time);
    }


    /**
     * 函数 _create_token
     * 用户登录验证成功后， 使用用户ID(user_id)创建token
     *
     * @param int $user_id
     *
     * @return array 数组
     *           boolean  result  是否创建成功
     *           string   key     用户秘钥
     *           datetime expired   token有效时间
     */
    private function _create_token ($user_id)
    {
        $result = false;
        $key = null;
        $expired = null;

        $this->load->helper('key');

        do
        {
            $key = base64_encode(md5(generate_code()));
            $one = $this->db->from($this->table_name)->where('api_key',$key)->get()->row_array();
        }
        while( $one != NULL);

        $expired = date('Y-m-d H:i:s',strtotime($this->config->item('rest_api_key_expired_time').' seconds'));
        $data = array(
            'user_id' => $user_id,
            'api_key' => $key,
            'level' => 0, //TODO
            'ignore_limits' => 0, //TODO
            'created_time' => date('Y-m-d H:i:s'),
            'expired_time' => $expired,
        );

        $this->db->set($data);
        $this->db->insert($this->table_name);

        if( $this->db->affected_rows() == 1)
        {
            $result = true;
        }

        return array($result, $key, $expired);
    }



    public function disable_token ($token)
    {

        $one = $this->db->from($this->token_table_name)->where('token',$token)->get()->row_array();

        if($one)
        {

            $this->db->set('expired_time', date('Y-m-d H:i:s',strtotime('-1 second')));
            $this->db->where('id', $one['id']);
            $this->db->update($this->token_table_name);
            if( $this->db->affected_rows() == 1)
            {
                return true;
            }
        }

        return false;

    }

    public function auth_token ($key) {
        $is_auth = false;
        $user_id = 0;

        $one = $this->db->from($this->table_name)->where('api_key',$key)->get()->row_array();

        if($one)
        {
            if($one['expired_time'] > date('Y-m-d H:i:s')){
                $is_auth = true;
                $user_id = $one['user_id'];
            }
        }

        return array($is_auth, $user_id);

    }


    public function check_token ($token) {
        $is_auth = false;
        $expired = null;
        $user = null;

        $result = $this->db->from($this->table_name)->where('api_key',$token)->get()->row_array();

        if($result)
        {
            if($result['expired_time'] > date('Y-m-d H:i:s')){
                $is_auth = true;

                $expired = $result['expired_time'];

                $user = $this->user->get_one($result['user_id']);
            }
        }

        return array($is_auth, $expired, $user);
    }

}