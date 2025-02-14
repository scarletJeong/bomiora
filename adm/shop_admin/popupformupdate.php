<?php
$sub_menu = '500600';
include_once('./_common.php');

//check_demo();

$w = isset($_REQUEST['w']) ? $_REQUEST['w'] : '';

if ($w == 'd') {
  auth_check_menu($auth, $sub_menu, "d");
} else {
  auth_check_menu($auth, $sub_menu, "w");
}

check_admin_token();

@mkdir(G5_IMG_PATH . "/popup", G5_DIR_PERMISSION);
@chmod(G5_IMG_PATH . "/popup", G5_DIR_PERMISSION);

$pu_img    = isset($_FILES['pu_img']['tmp_name']) ? $_FILES['pu_img']['tmp_name'] : '';
$pu_img_name  = isset($_FILES['pu_img']['name']) ? $_FILES['pu_img']['name'] : '';
$pu_img_del  = (isset($_POST['pu_img_del']) && $_POST['pu_img_del']) ? preg_replace('/[^0-9]/', '', $_POST['pu_img_del']) : 0;
$pu_id      = isset($_REQUEST['pu_id']) ? preg_replace('/[^0-9]/', '', $_REQUEST['pu_id']) : 0;
$pu_url      = isset($_POST['pu_url']) ? strip_tags(clean_xss_attributes($_POST['pu_url'])) : '';
$pu_alt			= isset($_POST['pu_alt']) ? strip_tags(clean_xss_attributes($_POST['pu_alt'])) : '';
$pu_subject    = isset($_POST['pu_subject']) ? clean_xss_tags($_POST['pu_subject'], 1, 1) : '';
$pu_device    = isset($_POST['pu_device']) ? clean_xss_tags($_POST['pu_device'], 1, 1) : '';
$pu_type    = isset($_POST['pu_type']) ? clean_xss_tags($_POST['pu_type'], 1, 1) : '';
$pu_location  = isset($_POST['pu_location']) ? clean_xss_tags($_POST['pu_location'], 1, 1) : '';
$pu_type    = isset($_POST['pu_type']) ? clean_xss_tags($_POST['pu_type'], 1, 1) : '';
$pu_border    = isset($_POST['pu_border']) ? (int) $_POST['pu_border'] : 0;
$pu_new_win    = isset($_POST['pu_new_win']) ? (int) $_POST['pu_new_win'] : 0;
$pu_begin_time  = isset($_POST['pu_begin_time']) ? clean_xss_tags($_POST['pu_begin_time'], 1, 1) : '';
$pu_end_time  = isset($_POST['pu_end_time']) ? clean_xss_tags($_POST['pu_end_time'], 1, 1) : '';
$pu_order    = isset($_POST['pu_order']) ? (int) $_POST['pu_order'] : 0;

if ($pu_img_del)  @unlink(G5_IMG_PATH . "/popup/popup_{$pu_id}.{$pu_type }");

//파일이 이미지인지 체크합니다.
if ($pu_img || $pu_img_name) {
  if (!preg_match('/\.(gif|jpe?g|bmp|png)$/i', $pu_img_name)) {
    alert("이미지 파일만 업로드 할수 있습니다.");
  }

  $img_info = @getimagesize($pu_img);
  if ($img_info['2'] < 1 || $img_info['2'] > 16) {
    alert("이미지 파일만 업로드 할수 있습니다.");
  }
  $pu_type = $img_info['2'] == 3 ? 'png' : ($img_info['2'] == 1 ? 'gif' : 'jpg');
}

if ($w == "") {
  if (!$pu_img_name) alert('이미지를 업로드 하세요.');
  $sql = " insert into {$g5['g5_shop_popup_table']}
                set pu_alt        = '$pu_alt',
					          pu_subject    = '$pu_subject',
                    pu_url        = '$pu_url',
                    pu_device     = '$pu_device',
                    pu_location   = '$pu_location',
					          pu_type		  = '$pu_type',
                    pu_border     = '$pu_border',
                    pu_new_win    = '$pu_new_win',
                    pu_begin_time = '$pu_begin_time',
                    pu_end_time   = '$pu_end_time',
                    pu_time       = '" . G5_TIME_YMDHIS . "',
                    pu_hit        = '0',
                    pu_order      = '$pu_order' ";
  sql_query($sql);

  $pu_id = sql_insert_id();
} else if ($w == "u") {
  $sql = " update {$g5['g5_shop_popup_table']}
                set pu_alt        = '$pu_alt',
					          pu_subject    = '$pu_subject',
                    pu_url        = '$pu_url',
                    pu_device     = '$pu_device',
                    pu_location   = '$pu_location',
					          pu_type		  = '$pu_type',
                    pu_border     = '$pu_border',
                    pu_new_win    = '$pu_new_win',
                    pu_begin_time = '$pu_begin_time',
                    pu_end_time   = '$pu_end_time',
                    pu_time       = '" . G5_TIME_YMDHIS . "',
                    pu_order      = '$pu_order'
              where pu_id = '$pu_id' ";
  sql_query($sql);
} else if ($w == "d") {
  @unlink(G5_IMG_PATH . "/popup/popup_{$pu_id}.{$pu_type}");
  $sql = " delete from {$g5['g5_shop_popup_table']} where pu_id = $pu_id ";
  $result = sql_query($sql);
}

if ($w == "" || $w == "u") {
  
  //jjy
  //alert("jjy");
  alert($pu_img);
  //var_dump($pu_img); ;

  $upload_img = resize_image($pu_img, 500, 500);
  $img_name = G5_IMG_PATH . "/popup/popup_{$pu_id}.{$upload_img['type']}";
  if ($upload_img['type'] == 'png') {
    imagepng($upload_img['data'], $img_name);
  } else if ($upload_img['type'] == 'gif') {
    imagegif($upload_img['data'], $img_name);
  } else {
    imagejpeg($upload_img['data'], $img_name);
  }
  goto_url("./popupform.php?w=u&amp;pu_id=$pu_id");
} else {
  goto_url("./popuplist.php");
}