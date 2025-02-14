<?php
$sub_menu = '600200';
include_once('./_common.php');

if ($w == 'd')
auth_check_menu($auth, $sub_menu, "d");
else
auth_check_menu($auth, $sub_menu, "w");

@mkdir(G5_DATA_PATH."/tester", G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH."/tester", G5_DIR_PERMISSION);

$tr_no  = isset($_POST['tr_no']) ? preg_replace('/[^A-Za-z0-9\-_]/', '', $_POST['tr_no']) : '';
$mb_id = isset($_POST['mb_id']) ? clean_xss_tags(trim($_POST['mb_id']), 1, 1) : '';
$it_id= isset($_POST['it_id']) ? preg_replace('/[^A-Za-z0-9\-_]/', '', $_POST['it_id']) : '';
$tr_price = isset($_POST['tr_price'])	? preg_replace('/[^0-9\-]/', '', trim($_POST['tr_price'])) : 0;
$fr_date = isset($_POST['fr_date'])	? preg_replace('/[^0-9\-]/', '', trim($_POST['fr_date'])) . ' 00:00:01' : '0000-00-00 00:00:00';
$to_date = isset($_POST['to_date'])	? preg_replace('/[^0-9\-]/', '', trim($_POST['to_date'])) . ' 23:59:59' : '0000-00-00 00:00:00';
$quota = isset($_POST['quota'])	? preg_replace('/[^0-9]/', '', trim($_POST['quota'])) : 0;
$is_confirm = (isset($_POST['is_confirm']) && in_array($_POST['is_confirm'], array('y', 'n'))) ? $_POST['is_confirm'] : 'n';
$tester_target = isset($_POST['tester_target'])	? preg_replace('/[^0-9]/', '', trim($_POST['tester_target'])) : 1;

$title = isset($_POST['title']) ? clean_xss_tags(trim($_POST['title']), 1, 1, 0, 0) : '';
$keyword = $_POST['keyword'] ? htmlentities(htmlspecialchars($_POST['keyword'])) : '';
$mission = $_POST['mission'] ? htmlentities(htmlspecialchars($_POST['mission'])) : '';
$guide = $_POST['guide'] ? htmlentities(htmlspecialchars($_POST['guide'])) : '';
$it_content = $_POST['it_content'] ? htmlentities(htmlspecialchars($_POST['it_content'])) : '';

$ref_link = isset($_POST['ref_link']) ? clean_xss_tags(trim($_POST['ref_link']), 1, 1, 0, 0) : '';

$it = get_shop_item($it_id);
if(!$it) {
  alert('선택된 제품이 없습니다.');
}

$tr_img = '';
if($tr_no) {
  $sql = " select tr_img from {$g5['g5_tester_list_table']} where tr_no = '$tr_no' ";
  $file = sql_fetch($sql);
  $tr_img    = $file['tr_img'];
}

$tr_img_dir = G5_DATA_PATH.'/tester';

$tr_img_del = ! empty($_POST['tr_img_del']) ? 1 : 0;

if ($tr_img && $tr_img_del) {
  $file_img = $tr_img_dir.'/'.clean_relative_paths($tr_img);
  @unlink($file_img);
  delete_item_thumbnail(dirname($file_img), basename($file_img));
  $tr_img = '';
}

if ($_FILES['tr_img']['name']) {
  if($w == 'u' && $tr_img) {
    $file_img = $tr_img_dir.'/'.clean_relative_paths($tr_img);
    @unlink($file_img);
    delete_item_thumbnail(dirname($file_img), basename($file_img));
  }
  $tr_img = tr_img_upload($_FILES['tr_img']['tmp_name'], $_FILES['tr_img']['name'], $tr_img_dir.'/'.$it_id);
}

$sql_common = "  mb_id = '{$mb_id}',
tr_price = '{$tr_price}',
fr_date = '{$fr_date}',
to_date = '{$to_date}',
quota = '{$quota}',
is_confirm = '{$is_confirm}',
tester_target = '{$tester_target}',
title = '{$title}',
keyword = '{$keyword}',
mission = '{$mission}',
guide = '{$guide}',
it_content = '{$it_content}',
ref_link = '{$ref_link}',
tr_img = '{$tr_img}' ";

if ($tr_no) {
  $sql = " update {$g5['g5_tester_list_table']} set datetime = '".G5_TIME_YMDHIS."', {$sql_common} where tr_no = '$tr_no' ";
} else {
  $sql = " insert into {$g5['g5_tester_list_table']} set it_id = '{$it_id}', datetime = '" . G5_TIME_YMDHIS . "', {$sql_common} ";
}
sql_query($sql);

goto_url('./tester_list.php?'.$qstr, false);