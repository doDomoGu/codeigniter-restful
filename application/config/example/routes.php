<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'error';

$route['404_override'] = 'error/page_404';

$route['translate_uri_dashes'] = TRUE;  //url中的下划线 '_' 会转变为 横折线 '-'