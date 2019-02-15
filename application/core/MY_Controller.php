<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class MY_Controller extends REST_Controller {

    public $_code = 0;

    public $_data = array();

    public $_msg = '';

    function __construct()
    {
        parent::__construct();

    }
}