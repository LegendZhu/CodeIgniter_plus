<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['handlers'] = array('file');//暂未用到，作为多处写预留

$config['threshold'] = '6'; // 'ERROR' => '1', 'WARNING' => '2', 'NOTICE' => '3', 'INFO' => '4', 'DEBUG' => '5', 'ALL' => '6'

$config['threshold_extra'] = TRUE;//是否记录扩展信息

$config['introspection_processor'] = TRUE; // 记录类名方法

$config['record_memory_info'] = TRUE;// 是否记录内存实用信息

$config['log_path'] = APPPATH . 'logs/';

$config['log_name'] = 'log';//日志名前缀

$config['log_cut'] = 'h';//d:按日切割,h:按小时切割

$config['exclusion_list'] = array();//过滤日志
