<?php

/**
 * form_validation_lang参数验证合并修改到此
 * 10000+ 参数验证
 * 20000+ 安全相关
 * 30000+ 服务相关
 * 40000+ 业务逻辑
 */

$lang['success'] = array('code' => '0', 'info' => 'success');

$lang['error_param_required'] = array('code' => '10000', 'info' => 'The %s field is required.');
$lang['error_param_isset'] = array('code' => '10001', 'info' => 'The %s field must have a value.');
$lang['error_param_valid_email'] = array('code' => '10002', 'info' => 'The %s field must contain a valid email address.');
$lang['error_param_valid_emails'] = array('code' => '10003', 'info' => 'The %s field must contain all valid email addresses.');
$lang['error_param_valid_url'] = array('code' => '10004', 'info' => 'The %s field must contain a valid URL.');
$lang['error_param_valid_ip'] = array('code' => '10005', 'info' => 'The %s field must contain a valid IP.');
$lang['error_param_min_length'] = array('code' => '10006', 'info' => 'The %s field must be at least %s characters in length.');
$lang['error_param_max_length'] = array('code' => '10007', 'info' => 'The %s field can not exceed %s characters in length.');
$lang['error_param_exact_length'] = array('code' => '10008', 'info' => 'The %s field must be exactly %s characters in length.');
$lang['error_param_alpha'] = array('code' => '10009', 'info' => 'The %s field may only contain alphabetical characters.');
$lang['error_param_alpha_numeric'] = array('code' => '10010', 'info' => 'The %s field may only contain alpha-numeric characters.');
$lang['error_param_alpha_dash'] = array('code' => '10011', 'info' => 'The %s field may only contain alpha-numeric characters, underscores, and dashes.');
$lang['error_param_numeric'] = array('code' => '10012', 'info' => 'The %s field must contain only numbers.');
$lang['error_param_is_numeric'] = array('code' => '10013', 'info' => 'The %s field must contain only numeric characters.');
$lang['error_param_integer'] = array('code' => '10014', 'info' => 'The %s field must contain an integer.');
$lang['error_param_regex_match'] = array('code' => '10015', 'info' => 'The %s field is not in the correct format.');
$lang['error_param_matches'] = array('code' => '10016', 'info' => 'The %s field does not match the %s field.');
$lang['error_param_is_unique'] = array('code' => '10017', 'info' => 'The %s field must contain a unique value.');
$lang['error_param_is_natural'] = array('code' => '10018', 'info' => 'The %s field must contain only positive numbers.');
$lang['error_param_is_natural_no_zero'] = array('code' => '10019', 'info' => 'The %s field must contain a number greater than zero.');
$lang['error_param_decimal'] = array('code' => '10020', 'info' => 'The %s field must contain a decimal number.');
$lang['error_param_less_than'] = array('code' => '10021', 'info' => 'The %s field must contain a number less than %s.');
$lang['error_param_greater_than'] = array('code' => '10022', 'info' => 'The %s field must contain a number greater than %s.');

$lang['error_param_is_null'] = array('code' => '10023', 'info' => '%s parameter is null.');
$lang['error_param_must_exists'] = array('code' => '10024', 'info' => '%s parameter is must exists.');
$lang['error_param_too_lang'] = array('code' => '10025', 'info' => '%s parameter is too lang.');
$lang['error_param_too_short'] = array('code' => '10026', 'info' => '%s parameter is too short.');
$lang['error_param_type_error'] = array('code' => '10027', 'info' => '%s parameter type error.');


/*
 * 安全
 */
$lang['error_ip_request_too_much'] = array('code' => '20000', 'info' => 'your ip request too much.');
$lang['error_user_request_too_much'] = array('code' => '20001', 'info' => 'user request too much.');
$lang['error_verify_code'] = array('code' => '20002', 'info' => 'verify code error.');

/*
 * 服务
 */
$lang['error_request_method_get'] = array('code' => '30000', 'info' => 'request method must be get.');
$lang['error_request_method_post'] = array('code' => '30001', 'info' => 'request method must be post.');


/*
 * 业务
 * 用户新增登陆等:40100+
 * 好友关系:40200+
 * 待新增,建议每个业务模块分配100个
 */

$lang['error_user_or_password'] = array('code' => '40100', 'info' => 'username or password error');
$lang['error_user_not_allow'] = array('code' => '40101', 'info' => 'user status exception, not allow');

$lang['error_user_not_exists'] = array('code' => '40200', 'info' => 'username or password error');
$lang['error_user_friend_limit'] = array('code' => '40200', 'info' => 'username or password error');



/* End of file error_lang.php */
/* Location: ./system/language/english/error_lang.php */