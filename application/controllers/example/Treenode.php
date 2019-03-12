<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Treenode extends MY_Controller {
    public $default_page_size = 20;
    public $default_page = 1;

    /*public $type_arr = [
        'C' => 'campaign',
        'O' => 'order',
        'S' => 'solution',
        'B' => 'banner'
    ];*/

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
        $c['start_id'] = 10000001; //开始ID
        $c['num'] = [
            'small'     => 50,   //小数据个数
            'middle'    => 100,  //中数据个数 (包含小数据）
            'big'       => 200,  //大数据个数 (包含小数据和中数据）
        ];
        $c['end_id'] = [
            'small'     => $c['start_id'] + $c['num']['small'] - 1,    //小数据结束ID
            'middle'    => $c['start_id'] + $c['num']['middle'] - 1,   //中数据结束ID
            'big'       => $c['start_id'] + $c['num']['big'] - 1,      //大数据结束ID
        ];

        //订单
        $o = [];
        $o['start_id'] = 20000001; //开始ID
        $o['num'] = [
            'small'     => 1200,   //小数据个数
            'middle'    => 4400,   //中数据个数
            'big'       => 8800,  //大数据个数
        ];
        $o['end_id'] = [
            'small'     => $o['start_id'] + $o['num']['small'] - 1,    //小数据结束ID
            'middle'    => $o['start_id'] + $o['num']['middle'] - 1,   //中数据结束ID
            'big'       => $o['start_id'] + $o['num']['big'] - 1,      //大数据结束ID
        ];

        //投放
        $s = [];
        $s['start_id'] = 30000001; //开始ID
        $s['num'] = [
            'small'     => 2000,   //小数据个数
            'middle'    => 10000,   //中数据个数
            'big'       => 40000,  //大数据个数
        ];
        $s['end_id'] = [
            'small'     => $s['start_id'] + $s['num']['small'] - 1,    //小数据结束ID
            'middle'    => $s['start_id'] + $s['num']['middle'] - 1,   //中数据结束ID
            'big'       => $s['start_id'] + $s['num']['big'] - 1,      //大数据结束ID
        ];

        //创意
        $b = [];
        $b['start_id'] = 40000001; //开始ID
        $b['num'] = [
            'small'     => 10000,   //小数据个数
            'middle'    => 100000,   //中数据个数
            'big'       => 1000000,  //大数据个数
        ];
        $b['end_id'] = [
            'small'     => $b['start_id'] + $b['num']['small'] - 1,    //小数据结束ID
            'middle'    => $b['start_id'] + $b['num']['middle'] - 1,   //中数据结束ID
            'big'       => $b['start_id'] + $b['num']['big'] - 1,      //大数据结束ID
        ];

        return ['C'=>$c,'O'=>$o,'S'=>$s,'B'=>$b];
    }

    public function index_get() {
        //父资源类型
        $pType = $this->get('p_type');
        //当前资源类型， 做降级处理
        $typeLowerArr = ['root'=>'C','C'=>'O','O'=>'S','S'=>'B' ];
        $type = isset($typeLowerArr[$pType]) ? $typeLowerArr[$pType] : 'root';
        //数据量选项 small/middle/big
        $data_type = $this->get('data_type');
        if(!in_array($data_type,['small','middle','big'])){
            $data_type = 'small';
        }

        if($type=='root'){
            $result = [
                [
                    'id'    => 'root',
                    'name' => '广告资源',
                    'disabled' => true,
                ]
            ];
//            $total = 1;
//            $page = 1;
        }else{
            //父ID
            $p_id = (int) $this->get('p_id');
            //当前页数
            $page = (int) $this->get('page');
            if(!$page){
                $page = $this->default_page;
            }
            //每页显示数
            $page_size = (int) $this->get('page_size');
            if(!$page_size){
                $page_size = $this->default_page_size;
            }

            //获取总数
//            $this->handle_condition($type, $p_id, $data_type);
//            $total = $this->db->count_all_results();

            //获取分页数据
            $this->handle_condition($type, $p_id, $data_type);
            $this->db->limit($page_size, $page_size * ($page - 1));
//        if($type!='B'){
//            $tbl = $this->tablename_arr[$type];
//            $tbl2 = $this->tablename_arr[$typeLowerArr[$type]];
//            $this->db->join($tbl2, $tbl2.'.p_id = '.$tbl.'.id');
//        }
            $result = $this->db->get()->result_array();

//        var_dump($this->db->last_query());
        }


        //组装数据
        $list = [];
        foreach($result as $k=>$v){
            $list[$k]['id'] = $v['id'];
            $list[$k]['label'] = $v['name'];
            $list[$k]['type'] = $type;
            if(isset($v['disabled'])){
                $list[$k]['disabled'] = $v['disabled'];
            }
            //创意类型增加叶节点标志
            if($type == 'B'){
                $list[$k]['isLeaf'] = true;
            }else{
                //非创意类型获取子节点总数和分页相关信息
                $tbl2 = $this->tablename_arr[$typeLowerArr[$type]];
                if($typeLowerArr[$type]!=='C'){
                    $this->db->where([$tbl2.'.p_id'=> (int) $v['id']]);
                }
                $this->db->where([$tbl2.'.id <= '=>$this->num[$typeLowerArr[$type]]['end_id'][$data_type]]);
                $this->db->from($tbl2);
                $childrenCount = $this->db->count_all_results();
                $list[$k]['total'] = $childrenCount;
//                $list[$k]['isLeaf'] = $childrenCount == 0;
//                $list[$k]['page_size'] = $page_size;
                $list[$k]['currentPage'] = 1;
//                $list[$k]['pageTotal'] = 1;

            }
        }

//        $this->_data = [/*'total'=>$total,*/ 'children'=>$list, /*'page_size'=>$page_size,*/ /*'currentPage'=>$page,*/ /*'pageTotal'=>ceil($total/$page_size)*/];
        
        $this->_data = $list;
        $this->send_response();
    }

    private function handle_condition($type, $p_id, $data_type){
        $tbl = $this->tablename_arr[$type];
        $this->db->select([$tbl.'.id', $tbl.'.name']);
        //根据数据量选项 选择数据范围
        $this->db->where([$tbl.'.id <= '=>$this->num[$type]['end_id'][$data_type]]);
        //不是活动类型需要加父ID
        if($type !== 'C'){
            $this->db->where([$tbl.'.p_id'=>$p_id]);
        }
        $this->db->from($tbl);
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
        $this->dbforge->add_key('p_id');
        $this->dbforge->create_table($this->tablename_arr['O'], FALSE, ['ENGINE'=>'MyISAM']);

        $this->dbforge->add_field($fields2);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('p_id');
        $this->dbforge->create_table($this->tablename_arr['S'], FALSE, ['ENGINE'=>'MyISAM']);

        $this->dbforge->add_field($fields2);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('p_id');
        $this->dbforge->create_table($this->tablename_arr['B'], FALSE, ['ENGINE'=>'MyISAM']);

        //插入数据

        //活动
        $cName = '活动2019';

        //活动 - 小数据
        $cData = [];
        for($i = 0; $i < $this->num['C']['num']['small'] ;$i++){
            $cData[] = [
                'id' => $this->num['C']['start_id'] + $i,
                'name' => $cName . ($i>0?'-'.($i+1):''),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['C'], $cData);

        //活动 - 中数据
        $cData = [];
        for($i = $this->num['C']['num']['small']; $i < $this->num['C']['num']['middle'] ;$i++){
            $cData[] = [
                'id' => $this->num['C']['start_id'] + $i,
                'name' => $cName . ($i>0?'-'.($i+1):''),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['C'], $cData);

        //活动 - 大数据
        $cData = [];
        for($i = $this->num['C']['num']['middle']; $i < $this->num['C']['num']['big'] ;$i++){
            $cData[] = [
                'id' => $this->num['C']['start_id'] + $i,
                'name' => $cName . ($i>0?'-'.($i+1):''),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['C'], $cData);

        //订单
        $name = '订单2019';

        //订单 - 小数据
        $data = [];
        for($i = 0; $i < $this->num['O']['num']['small'] ;$i++){
            $data[] = [
                'id' => $this->num['O']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['C']['start_id'], $this->num['C']['end_id']['small']),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['O'], $data);

        //订单 - 中数据
        $data = [];
        for($i = $this->num['O']['num']['small']; $i < $this->num['O']['num']['middle'] ;$i++){
            $data[] = [
                'id' => $this->num['O']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['C']['start_id'], $this->num['C']['end_id']['middle']),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['O'], $data);

        //订单 - 大数据
        $data = [];
        for($i = $this->num['O']['num']['middle']; $i < $this->num['O']['num']['big'] ;$i++){
            $data[] = [
                'id' => $this->num['O']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['C']['start_id'], $this->num['C']['end_id']['big']),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['O'], $data);


        //投放
        $name = '投放2019';

        //投放 - 小数据
        $data = [];
        for($i = 0; $i < $this->num['S']['num']['small'] ;$i++){
            $data[] = [
                'id' => $this->num['S']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['O']['start_id'], $this->num['O']['end_id']['small']),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['S'], $data);

        //投放 - 中数据
        $data = [];
        for($i = $this->num['S']['num']['small']; $i < $this->num['S']['num']['middle'] ;$i++){
            $data[] = [
                'id' => $this->num['S']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['O']['start_id'], $this->num['O']['end_id']['middle']),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['S'], $data);

        //投放 - 大数据
        $data = [];
        for($i = $this->num['S']['num']['middle']; $i < $this->num['S']['num']['big'] ;$i++){
            $data[] = [
                'id' => $this->num['S']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['O']['start_id'], $this->num['O']['end_id']['big']),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['S'], $data);


        //创意
        $name = '创意2019';

        //创意 - 小数据
        $data = [];
        for($i = 0; $i < $this->num['B']['num']['small'] ;$i++){
            $data[] = [
                'id' => $this->num['B']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['S']['start_id'], $this->num['S']['end_id']['small']),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['B'], $data);

        //创意 - 中数据
        $data = [];
        for($i = $this->num['B']['num']['small']; $i < $this->num['B']['num']['middle'] ;$i++){
            $data[] = [
                'id' => $this->num['B']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['S']['start_id'], $this->num['S']['end_id']['middle']),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
        }
        $this->db->insert_batch($this->tablename_arr['B'], $data);

        //创意 - 大数据
        $data = [];
        for($i = $this->num['B']['num']['middle']; $i < $this->num['B']['num']['big'] ;$i++){
            $data[] = [
                'id' => $this->num['B']['start_id'] + $i,
                'name' => $name . ($i>0?'-'.($i+1):''),
                'p_id' => mt_rand($this->num['S']['start_id'], $this->num['S']['end_id']['big']),
                'enable' => mt_rand(0,1)? 1 : (mt_rand(0,1)  ? 1 : mt_rand(0,2))
            ];
            if(count($data)>=10000){
                $this->db->insert_batch($this->tablename_arr['B'], $data);
                $data = [];
            }
        }
    }

}
