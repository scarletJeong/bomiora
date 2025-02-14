<?php
$sub_menu = "600100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

if ($is_admin != 'super') {
  alert('최고관리자만 접근 가능합니다.');
}

$check_keys = array (
'cf_no' => 'int',
'pres_date' => 'date',
'pres_count_1' => 'int',
'pres_count_2' => 'int',
'main_review' => 'y',
'main_review_count' => 'int',
'main_tester' => 'y',
'main_tester_count' => 'int',
'main_board' => 'y',
'main_board_count' => 'int',
'intl_enable' => 'y',
'intl_lang' => 'str',
'intl_google_api_key' => 'str',
'intl_google_project_id' => 'str',
);

$posts = array();
foreach ($check_keys as $k => $t) {
  $v = isset($_POST[$k]) ? preg_replace('/[^a-z0-9_\-\:\.\|\ ]/i', '', $_POST[$k]) : '';
  if ($t === 'int') {
    $v = (int)$v;
  } else if ($t == 'date') {
    if (strlen($v) !== 19 || substr_count($v, '-') !== 2 || substr_count($v, ':') !== 2) {
      $v = '0000-00-00 00:00:00';
    }
  } else if ($t == 'y') {
    if (!($v == 'y' || $v == '')) {
      $v = '';
    }
  }
  $posts[$k] = $v;
}

$cf_no = $posts['cf_no'];
if (!$cf_no) {
  alert('잘못된 접근입니다.');
}
unset($posts['cf_no']);

$update_set = [];
foreach ($posts as $k => $v) {
  $update_set[] = "{$k} = '{$v}'";
}
$update_set[] = "datetime = '".G5_TIME_YMDHIS."'";
$update_set = implode(',', $update_set);

$sql = " update {$g5['g5_mkt_config_table']} set {$update_set} where cf_no = '{$cf_no}'; ";

if (sql_query($sql)) {
  get_mkt_config(false);
}

goto_url('./mkt_config.php', false);
