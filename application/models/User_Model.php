<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
CREATE TABLE `user` (
    `id` int(20) NOT NULL AUTO_INCREMENT,
    `account` varchar(100) CHARACTER SET utf8 NOT NULL,
    `password` varchar(100) CHARACTER SET utf8 NOT NULL,
    `name` varchar(100) CHARACTER SET utf8 NOT NULL,
    `role_id` tinyint(1) NOT NULL DEFAULT '0',
    `status` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
*/



class User_Model extends MY_Model {

//    public $table_name = 'user';

    public $account;
    public $password;
    public $name;

    public $admin_role_id = 1;  //管理员角色权限
    public $user_role_id = 2;   //普通用户角色权限
    public $media_role_id = 3;  //媒介角色权限

    public $admin_role_name = 'admin';
    public $user_role_name = 'user';
    public $media_role_name = 'media';

    public $role_list = array();

    public function __construct() {
        parent::__construct();

        $this->role_list = array(
            $this->admin_role_id => $this->admin_role_name,
            $this->user_role_id  => $this->user_role_name,
            $this->media_role_id => $this->media_role_name
        );

        $this->table_name = 'user';
    }

    public function get_list ( $search = array(), $orderBy = 'id ASC', $page = 1, $pageSize = self::PAGE_SIZE )
    {
        if(empty($orderBy)){
            $orderBy = 'id ASC';
        }

        if(empty($page)){
            $page = 1;
        }

        if(empty($pageSize)){
            $pageSize = self::PAGE_SIZE;
        }

        $this->_field_map();

        $this->db->limit($pageSize, $pageSize * ($page - 1));

        $this->db->order_by($orderBy);

        return $this->db->get($this->table_name)->result_array();

    }


    public function get_count( $search = array() )
    {
        return $this->db->count_all_results($this->table_name);
    }

    public function get_all(){

        return $this->db->get($this->table_name)->result();

    }

    public function get_one($id){

        $this->db->where('id', $id);

        return $this->db->get($this->table_name)->row_array();

    }

    public function create($data){

        $this->db->insert($this->table_name, $data);

        return $this->db->insert_id();

    }


    public function update($id, $data) {

        unset($data['id']);

        $this->db->where('id', $id);

        $this->db->update($this->table_name, $data);

    }

    public function update_role($data) {

        $this->db->where('id', $data['user_id']);

        $this->db->update($this->table_name, array('role_id'=>$data['role_id']));
    }

    public function get_one_by_account ($account) {

        $this->db->where('account', $account);

        return $this->db->get($this->table_name)->row_array();

    }


//    public function validation($data, $scenario = 'defalut'){
//        //$this->load->helper(array('form', 'url'));
//        //加载CI表单验证库
//        //$this->load->library('MY_Form_validation','form_validation');
//        //$this->load->library('form_validation');
//
//        $this->form_validation->set_data($data);
//
//        //----------------------------------------
//        #  验证规则及错误信息代码放在这里
//
//        //$id = isset($data['id']) ? $data['id'] : 0;
//
//        $config = array();
//        $configLabel = array();
//        $configRules = array();
//
//        $configLabel['id'] = '活动ID';
//        $configLabel['name'] = '活动名称';
//        $configLabel['frequency'] = '最大播放次数';
//
//
//        $configRules['name'][] = 'required';
//        $configRules['frequency'][] = 'is_natural_no_zero';
//        $configRules['frequency'][] = 'required';
//
//        if($scenario === self::SCENARIO_CREATE){
//            $configRules['name'][] = 'is_unique_campaign';
//        }else if($scenario === self::SCENARIO_CREATE_BY_SAMMAN){
//            $configRules['name'][] = 'is_unique_campaign';
//        }else if ($scenario === self::SCENARIO_UPDATE){
//            $configRules['id'][] = 'is_natural_no_zero';
//            $configRules['id'][] = 'required';
//            $configRules['id'][] = 'chudian_campaign_status';
//
//            $configRules['name'][] = 'is_unique_campaign_on_update['.$data['id'].']';
//        }
//
//        foreach($configLabel as $field => $label){
//            if(!empty($configRules[$field])){
//                $config[] = array(
//                    'field' => $field,
//                    'label' => $label,
//                    'rules' => $configRules[$field]
//                );
//            }
//        }
//
//        $this->form_validation->set_rules($config);
//
//        //----------------------------------------
//
//        if ($this->form_validation->run() === FALSE){
//
//            return $this->form_validation->error_array();
//
//        }else{
//            return true;
//
//        }
//
//    }

}