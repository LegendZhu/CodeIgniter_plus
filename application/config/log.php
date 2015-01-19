<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['handlers'] = array('file');

$config['threshold'] = '6'; // 'ERROR' => '1', 'WARNING' => '2', 'NOTICE' => '3', 'INFO' => '4', 'DEBUG' => '5', 'ALL' => '6'

$config['introspection_processor'] = TRUE; // add some meta data such as controller and line number to log messages

$config['log_path'] = APPPATH . 'logs/';

$config['log_name'] = 'log';

// exclusion list for pesky messages which you may wish to temporarily suppress with strpos() match
$config['exclusion_list'] = array();
