<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Treenode extends MY_Controller {
    public $type_arr = [
        'C' => 'campaign',
        'O' => 'order',
        'S' => 'solution',
        'B' => 'banner'
    ];

    public $tablename_arr = [
        'C' => '_campaign',
        'O' => '_order',
        'S' => '_solution',
        'B' => '_banner'
    ];

    public $num = [];

    public function __construct() {
        parent::__construct();
        $this->num = $this->generate_test_num();
    }

    private function generate_test_num(){

        //活动
        $c = [];
        $c['start_id'] = 100001; //开始ID
        $c['num'] = [
            'small'     => 100,   //小数据个数
            'middle'    => 1000,  //中数据个数 (包含小数据）
            'big'       => 3000,  //大数据个数 (包含小数据和中数据）
        ];
        $c['end_id'] = [
            'small'     => $c['start_id'] + $c['num']['small'] - 1,    //小数据结束ID
            'middle'    => $c['start_id'] + $c['num']['middle'] - 1,   //中数据结束ID
            'big'       => $c['start_id'] + $c['num']['big'] - 1,      //大数据结束ID
        ];

        //订单
        $o = [];
        $o['start_id'] = 200001; //开始ID
        $o['num'] = [
            'small'     => 300,   //小数据个数
            'middle'    => 1800,   //中数据个数
            'big'       => 7800,  //大数据个数
        ];
        $o['end_id'] = [
            'small'     => $o['start_id'] + $o['num']['small'] - 1,    //小数据结束ID
            'middle'    => $o['start_id'] + $o['num']['middle'] - 1,   //中数据结束ID
            'big'       => $o['start_id'] + $o['num']['big'] - 1,      //大数据结束ID
        ];

        //投放
        $s = [];
        $s['start_id'] = 300001; //开始ID
        $s['num'] = [
            'small'     => 600,   //小数据个数
            'middle'    => 3000,   //中数据个数
            'big'       => 15000,  //大数据个数
        ];
        $s['end_id'] = [
            'small'     => $s['start_id'] + $s['num']['small'] - 1,    //小数据结束ID
            'middle'    => $s['start_id'] + $s['num']['middle'] - 1,   //中数据结束ID
            'big'       => $s['start_id'] + $s['num']['big'] - 1,      //大数据结束ID
        ];

        //创意
        $b = [];
        $b['start_id'] = 400001; //开始ID
        $b['num'] = [
            'small'     => 1000,   //小数据个数
            'middle'    => 8000,   //中数据个数
            'big'       => 80000,  //大数据个数
        ];
        $b['end_id'] = [
            'small'     => $b['start_id'] + $b['num']['small'] - 1,    //小数据结束ID
            'middle'    => $b['start_id'] + $b['num']['middle'] - 1,   //中数据结束ID
            'big'       => $b['start_id'] + $b['num']['big'] - 1,      //大数据结束ID
        ];

        return ['c'=>$c,'o'=>$o,'s'=>$s,'b'=>$b];
    }

    public function index_get() {

        $type = $this->get('type');

        $dataType = $this->get('data_type', 'small');


        if($type && isset($this->type_arr[$type])){

            $this->db->select(['id', 'name']);

            $this->db->where(['id <= '=>$this->num['c']['end_id']['small']]);

            $this->_data = $this->db->get($this->tablename_arr[$type])->result_array();
        }else{
            $this->_code = 10003;
            $this->_msg = '获取信息失败';
        }

        $this->send_response();
    }


    //初始化数据方法  会删除 【$tablename_arr】这里面的表并重建
    public function data_init_post(){
        $this->load->dbforge();
        //删除表
        $this->dbforge->drop_table($this->tablename_arr['C'], TRUE);
        $this->dbforge->drop_table($this->tablename_arr['O'], TRUE);
        $this->dbforge->drop_table($this->tablename_arr['S'], TRUE);
        $this->dbforge->drop_table($this->tablename_arr['B'], TRUE);
        // 创建表
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'enable' => array(
                'type' =>'TINYINT',
                'constraint' => '1',
                'default' => '1',
            )
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tablename_arr['C'], FALSE, ['ENGINE'=>'MyISAM']);

        $fields2 = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'p_id' => array(
                'type' =>'INT',
                'constraint' => '20',
                'unsigned' =>TRUE
            ),
            'enable' => array(
                'type' =>'TINYINT',
                'constraint' => '1',
                'unsigned' =>TRUE,
                'default' => '1',
            )
        );

        $this->dbforge->add_field($fields2);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tablename_arr['O'], FALSE, ['ENGINE'=>'MyISAM']);

        $this->dbforge->add_field($fields2);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tablename_arr['S'], FALSE, ['ENGINE'=>'MyISAM']);

        $this->dbforge->add_field($fields2);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tablename_arr['B'], FALSE, ['ENGINE'=>'MyISAM']);

        //插入数据

        //活动
        $cName = '活动2019';

        //活动 - 小数据
        $cData = [];
        for($i = 0; $i < $this->num['c']['num']['small'] ;$i++){
            $cData[] = [
                'id' => $this->num['c']['start_id'] + $i,
                'name' => $cName . ($i>0?'-'.($i+1):''),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['C'], $cData);

        //活动 - 中数据
        $cData = [];
        for($i = $this->num['c']['num']['small']; $i < $this->num['c']['num']['middle'] ;$i++){
            $cData[] = [
                'id' => $this->num['c']['start_id'] + $i,
                'name' => $cName . ($i>0?'-'.($i+1):''),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['C'], $cData);

        //活动 - 大数据
        $cData = [];
        for($i = $this->num['c']['num']['middle']; $i < $this->num['c']['num']['big'] ;$i++){
            $cData[] = [
                'id' => $this->num['c']['start_id'] + $i,
                'name' => $cName . ($i>0?'-'.($i+1):''),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['C'], $cData);

        //订单
        $name = '订单2019';

        //订单 - 小数据
        $data = [];
        for($i = 0; $i < $this->num['o']['num']['small'] ;$i++){
            $data[] = [
                'id' => $this->num['o']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['c']['start_id'], $this->num['c']['end_id']['small']),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['O'], $data);

        //订单 - 中数据
        $data = [];
        for($i = $this->num['o']['num']['small']; $i < $this->num['o']['num']['middle'] ;$i++){
            $data[] = [
                'id' => $this->num['o']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['c']['start_id'], $this->num['c']['end_id']['middle']),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['O'], $data);

        //订单 - 大数据
        $data = [];
        for($i = $this->num['o']['num']['middle']; $i < $this->num['o']['num']['big'] ;$i++){
            $data[] = [
                'id' => $this->num['o']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['c']['start_id'], $this->num['c']['end_id']['big']),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['O'], $data);


        //投放
        $name = '投放2019';

        //投放 - 小数据
        $data = [];
        for($i = 0; $i < $this->num['s']['num']['small'] ;$i++){
            $data[] = [
                'id' => $this->num['s']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['o']['start_id'], $this->num['o']['end_id']['small']),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['S'], $data);

        //投放 - 中数据
        $data = [];
        for($i = $this->num['s']['num']['small']; $i < $this->num['s']['num']['middle'] ;$i++){
            $data[] = [
                'id' => $this->num['s']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['o']['start_id'], $this->num['o']['end_id']['middle']),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['S'], $data);

        //投放 - 大数据
        $data = [];
        for($i = $this->num['s']['num']['middle']; $i < $this->num['s']['num']['big'] ;$i++){
            $data[] = [
                'id' => $this->num['s']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['o']['start_id'], $this->num['o']['end_id']['big']),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['S'], $data);


        //创意
        $name = '创意2019';

        //创意 - 小数据
        $data = [];
        for($i = 0; $i < $this->num['b']['num']['small'] ;$i++){
            $data[] = [
                'id' => $this->num['b']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['s']['start_id'], $this->num['s']['end_id']['small']),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['B'], $data);

        //创意 - 中数据
        $data = [];
        for($i = $this->num['b']['num']['small']; $i < $this->num['b']['num']['middle'] ;$i++){
            $data[] = [
                'id' => $this->num['b']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['s']['start_id'], $this->num['s']['end_id']['middle']),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['B'], $data);

        //创意 - 大数据
        $data = [];
        for($i = $this->num['b']['num']['middle']; $i < $this->num['b']['num']['big'] ;$i++){
            $data[] = [
                'id' => $this->num['b']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['s']['start_id'], $this->num['s']['end_id']['big']),
                'enable' => 1
            ];
        }
        $this->db->insert_batch($this->tablename_arr['B'], $data);

    }

}
