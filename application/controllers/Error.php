<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Error extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function page_404() {
        $this->output->set_status_header(404);
        $this->output->set_content_type('application/json', strtolower($this->config->item('charset')));
        $this->output->set_output(json_encode(array('msg'=>'404 NOT FOUND')));
    }

}
