<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

    public function __construct() {

        parent::__construct();

        $this->load->model('User_Model','user');
    }

    //获取单个
    public function index_get() {
        $id = (int) $this->get('id');
        $one = $this->user->get_one($id);
        if ($one) {
            unset($one['password']);
            $this->_data = array(
                'user_info' => $one
            );
        } else {
            $this->_code = 10003;
            $this->_msg = '获取用户信息失败';
        }
        $this->send_response();
    }

    //创建单个
    public function index_post() {
        //TODO
    }

    //更新单个
    public function index_put() {
        //TODO
    }

    //删除单个
    public function index_delete() {
        //TODO
    }

    //获取列表（多个）
    public function list_get() {
        $search = array(); //TODO 实现search

        $orderProp = (string) $this->get('order_prop');

        $orderType = (string) $this->get('order_type');

        if( in_array($orderProp, array('id')) && in_array($orderType, array('asc','desc')))
        {
            $orderBy = $orderProp . ' ' . $orderType;
        }
        else
        {
            $orderBy = '';
        }

        $page = (int) $this->get('page');

        $pageSize = (int) $this->get('page_size');

        $count = $this->user->get_count($search);

        $list = $this->user->get_list($search, $orderBy, $page, $pageSize);

        $this->_data = array(
            'total' => $count,
            'list'  => $list
        );

        $this->send_response();
    }

}
