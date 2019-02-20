<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

    const PAGE_SIZE = 20;  //默认每页显示数

    const SCENARIO_DEFAULT  = 'default';     //默认场景
    const SCENARIO_CREATE   = 'create';       //创建数据场景
    const SCENARIO_UPDATE   = 'update';       //更新数据场景

    protected $field_map = array();         //数组字段映射
    protected $table_name = '';             //Model对应的表名
    protected $pk = '';                     //主键名

    protected function _field_map(){
        if(!empty($this->field_map)){
            $select = '*';
            foreach($this->field_map as $k => $v){
                $select .= ' , `'.$k.'` AS `'.$v.'` ';
            }
            $this->db->select($select);
        }
    }
}