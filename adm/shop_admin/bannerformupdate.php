<?php
$sub_menu = '500500';
include_once('./_common.php');

check_demo();

$w = isset($_REQUEST['w']) ? $_REQUEST['w'] : '';

if ($w == 'd')
    auth_check_menu($auth, $sub_menu, "d");
else
    auth_check_menu($auth, $sub_menu, "w");

check_admin_token();

@mkdir(G5_DATA_PATH."/banner", G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH."/banner", G5_DIR_PERMISSION);

$bn_pcimg		= isset($_FILES['bn_pcimg']['tmp_name']) ? $_FILES['bn_pcimg']['tmp_name'] : '';
$bn_pcimg_name	= isset($_FILES['bn_pcimg']['name']) ? $_FILES['bn_pcimg']['name'] : '';
$bn_moimg		= isset($_FILES['bn_moimg']['tmp_name']) ? $_FILES['bn_moimg']['tmp_name'] : '';
$bn_moimg_name	= isset($_FILES['bn_moimg']['name']) ? $_FILES['bn_moimg']['name'] : '';
$bn_id			= isset($_REQUEST['bn_id']) ? preg_replace('/[^0-9]/', '', $_REQUEST['bn_id']) : 0;
$bn_pcimg_del	= (isset($_POST['bn_pcimg_del']) && $_POST['bn_pcimg_del']) ? preg_replace('/[^0-9]/', '', $_POST['bn_pcimg_del']) : 0;
$bn_moimg_del	= (isset($_POST['bn_moimg_del']) && $_POST['bn_moimg_del']) ? preg_replace('/[^0-9]/', '', $_POST['bn_moimg_del']) : 0;
$bn_url			= isset($_POST['bn_url']) ? strip_tags(clean_xss_attributes($bn_url)) : '';
//$bn_alt			= isset($_POST['bn_alt']) ? strip_tags(clean_xss_attributes($bn_alt)) : '';
//$bn_alt		= isset($_POST['bn_alt']) ? clean_xss_tags($_POST['bn_alt'], 1, 1) : '';
$bn_subject		= isset($_POST['bn_subject']) ? clean_xss_tags($_POST['bn_subject'], 1, 1) : '';
$bn_device		= isset($_POST['bn_device']) ? clean_xss_tags($_POST['bn_device'], 1, 1) : '';
$bn_position	= isset($_POST['bn_position']) ? clean_xss_tags($_POST['bn_position'], 1, 1) : '';
$bn_skin		= isset($_POST['bn_skin']) ? clean_xss_tags($_POST['bn_skin'], 1, 1) : '';
$bn_border		= isset($_POST['bn_border']) ? (int) $_POST['bn_border'] : 0;
$bn_new_win		= isset($_POST['bn_new_win']) ? (int) $_POST['bn_new_win'] : 0;
$bn_begin_time	= isset($_POST['bn_begin_time']) ? clean_xss_tags($_POST['bn_begin_time'], 1, 1) : '';
$bn_end_time	= isset($_POST['bn_end_time']) ? clean_xss_tags($_POST['bn_end_time'], 1, 1) : '';
$bn_order		= isset($_POST['bn_order']) ? (int) $_POST['bn_order'] : 0;

if ($bn_pcimg_del)  @unlink(G5_DATA_PATH."/banner/pc_".$bn_id);
if ($bn_moimg_del)  @unlink(G5_DATA_PATH."/banner/mo_".$bn_id);

//파일이 이미지인지 체크합니다.
if( $bn_pcimg || $bn_pcimg_name ){
	if( !preg_match('/\.(gif|jpe?g|bmp|png)$/i', $bn_pcimg_name) ){
		alert("이미지 파일만 업로드 할수 있습니다.");
	}
	
	$timg = @getimagesize($bn_pcimg);
	if ($timg['2'] < 1 || $timg['2'] > 16){
		alert("이미지 파일만 업로드 할수 있습니다.");
	}
}

if( $bn_moimg || $bn_moimg_name ){
	if( !preg_match('/\.(gif|jpe?g|bmp|png)$/i', $bn_moimg_name) ){
		alert("이미지 파일만 업로드 할수 있습니다.");
	}
	
	$timg = @getimagesize($bn_moimg);
	if ($timg['2'] < 1 || $timg['2'] > 16){
		alert("이미지 파일만 업로드 할수 있습니다.");
	}
}

if ($w=="")
{
    if (!$bn_pcimg_name) alert('PC 이미지를 업로드 하세요.');

	if (!$bn_moimg_name) alert('MOBILE 이미지를 업로드 하세요.');

    sql_query(" alter table {$g5['g5_shop_banner_table']} auto_increment=1 ");

    $sql = " insert into {$g5['g5_shop_banner_table']}
                set bn_alt        = '$bn_alt',
					bn_subject    = '$bn_subject',
                    bn_url        = '$bn_url',
                    bn_device     = '$bn_device',
                    bn_position   = '$bn_position',
					bn_skin		  = '$bn_skin',
                    bn_border     = '$bn_border',
                    bn_new_win    = '$bn_new_win',
                    bn_begin_time = '$bn_begin_time',
                    bn_end_time   = '$bn_end_time',
                    bn_time       = '".G5_TIME_YMDHIS."',
                    bn_hit        = '0',
                    bn_order      = '$bn_order' ";
    sql_query($sql);

    $bn_id = sql_insert_id();
}
else if ($w=="u")
{
    $sql = " update {$g5['g5_shop_banner_table']}
                set bn_alt        = '$bn_alt',
					bn_subject    = '$bn_subject',
                    bn_url        = '$bn_url',
                    bn_device     = '$bn_device',
                    bn_position   = '$bn_position',
					bn_skin		  = '$bn_skin',
                    bn_border     = '$bn_border',
                    bn_new_win    = '$bn_new_win',
                    bn_begin_time = '$bn_begin_time',
                    bn_end_time   = '$bn_end_time',
                    bn_time       = '".G5_TIME_YMDHIS."',
                    bn_order      = '$bn_order'
              where bn_id = '$bn_id' ";
    sql_query($sql);
}
else if ($w=="d")
{
    @unlink(G5_DATA_PATH."/banner/pc_".$bn_id);
	@unlink(G5_DATA_PATH."/banner/mo_".$bn_id);

    $sql = " delete from {$g5['g5_shop_banner_table']} where bn_id = $bn_id ";
    $result = sql_query($sql);
}


if ($w == "" || $w == "u")
{
    if ($_FILES['bn_pcimg']['name']) upload_file($_FILES['bn_pcimg']['tmp_name'], 'pc_'.$bn_id, G5_DATA_PATH."/banner");
	if ($_FILES['bn_moimg']['name']) upload_file($_FILES['bn_moimg']['tmp_name'], 'mo_'.$bn_id, G5_DATA_PATH."/banner");

    goto_url("./bannerform.php?w=u&amp;bn_id=$bn_id");
} else {
    goto_url("./bannerlist.php");
}