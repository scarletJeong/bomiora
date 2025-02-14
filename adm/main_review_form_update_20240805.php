<?php
$sub_menu = '600200';
include_once('./_common.php');
/***********************************************
* 리뷰 등록 / 수정
	- 리뷰 등록은 관리자만 등록할 수 있으므로 포인트 지급을 하지 않는다.
	- 리뷰 수정은 관리자가 아니면서 mr_confirm 이 확인되면 첫 지급여부를 따진다.
***********************************************/

if ($w == 'd')
    auth_check_menu($auth, $sub_menu, "d");
else
    auth_check_menu($auth, $sub_menu, "w");

//check_admin_token();

@mkdir(G5_DATA_PATH."/mainreview", G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH."/mainreview", G5_DIR_PERMISSION);

$mr_no	= isset($_POST['mr_no']) ? preg_replace('/[^A-Za-z0-9\-_]/', '', $_POST['mr_no']) : '';
$it_id							= isset($_POST['it_id']) ? preg_replace('/[^A-Za-z0-9\-_]/', '', $_POST['it_id']) : '';// 제품ID
$mr_name						= isset($_POST['mr_name']) ? clean_xss_tags(trim($_POST['mr_name']), 1, 1) : '';// 이름
$mr_score1						= isset($_POST['mr_score1'])	? preg_replace('/[^0-9]/', '', trim($_POST['mr_score1'])) : 1; // 효과
$mr_score2						= isset($_POST['mr_score2'])	? preg_replace('/[^0-9]/', '', trim($_POST['mr_score2'])) : 1; // 가성비
$mr_score3						= isset($_POST['mr_score3'])	? preg_replace('/[^0-9]/', '', trim($_POST['mr_score3'])) : 1; // 향 / 맛
$mr_score4						= isset($_POST['mr_score4'])	? preg_replace('/[^0-9]/', '', trim($_POST['mr_score4'])) : 1; // 편리함
$mr_title		= isset($_POST['mr_title']) ? clean_xss_tags(trim($_POST['mr_title']), 1, 1, 0, 0) : '';
$mr_summary		= isset($_POST['mr_summary']) ? clean_xss_tags(trim($_POST['mr_summary']), 1, 1, 0, 0) : '';
$mr_link		= isset($_POST['mr_link']) ? clean_xss_tags(trim($_POST['mr_link']), 1, 1, 0, 0) : '';
$mr_content			= isset($_POST['mr_more_review_text']) ? clean_xss_tags(trim($_POST['mr_more_review_text']), 1, 1, 0, 0) : '';
$mr_confirm						= (isset($_POST['mr_confirm']) && in_array($_POST['mr_confirm'], array('1', '0')))	? $_POST['mr_confirm'] : '0';// 확인(예, 아니오)
$mb_id							= isset($_POST['mb_id']) ? clean_xss_tags(trim($_POST['mb_id']), 1, 1) : '';
$inf_id							= isset($_POST['inf_id']) ? clean_xss_tags(trim($_POST['inf_id']), 1, 1) :'';

$it = get_shop_item($it_id);
if(!$it)
	alert('상품정보가 존재하지 않습니다.');

$mr_img1 = $mr_img2 = $mr_img3 = $mr_img4 = $mr_img5 = $mr_img6 = $mr_img7 = $mr_img8 = $mr_img9 = $mr_img10 = '';
// 파일정보
if($w == "u") {
    $sql = " select mr_img1, mr_img2, mr_img3, mr_img4, mr_img5, mr_img6, mr_img7, mr_img8, mr_img9, mr_img10
                from {$g5['g5_main_review_table']} where mr_no = '$mr_no' ";
    $file = sql_fetch($sql);

    $mr_img1    = $file['mr_img1'];
    $mr_img2    = $file['mr_img2'];
    $mr_img3    = $file['mr_img3'];
    $mr_img4    = $file['mr_img4'];
    $mr_img5    = $file['mr_img5'];
    $mr_img6    = $file['mr_img6'];
    $mr_img7    = $file['mr_img7'];
    $mr_img8    = $file['mr_img8'];
    $mr_img9    = $file['mr_img9'];
    $mr_img10   = $file['mr_img10'];
}

$mr_img_dir = G5_DATA_PATH.'/mainreview';

for($i=0;$i<=10;$i++){
    ${'mr_img'.$i.'_del'} = ! empty($_POST['mr_img'.$i.'_del']) ? 1 : 0;
}

// 파일삭제
if ($mr_img1_del) {
    $file_img1 = $mr_img_dir.'/'.clean_relative_paths($mr_img1);
    @unlink($file_img1);
    delete_item_thumbnail(dirname($file_img1), basename($file_img1));
    $mr_img1 = '';
}
if ($mr_img2_del) {
    $file_img2 = $mr_img_dir.'/'.clean_relative_paths($mr_img2);
    @unlink($file_img2);
    delete_item_thumbnail(dirname($file_img2), basename($file_img2));
    $mr_img2 = '';
}
if ($mr_img3_del) {
    $file_img3 = $mr_img_dir.'/'.clean_relative_paths($mr_img3);
    @unlink($file_img3);
    delete_item_thumbnail(dirname($file_img3), basename($file_img3));
    $mr_img3 = '';
}
if ($mr_img4_del) {
    $file_img4 = $mr_img_dir.'/'.clean_relative_paths($mr_img4);
    @unlink($file_img4);
    delete_item_thumbnail(dirname($file_img4), basename($file_img4));
    $mr_img4 = '';
}
if ($mr_img5_del) {
    $file_img5 = $mr_img_dir.'/'.clean_relative_paths($mr_img5);
    @unlink($file_img5);
    delete_item_thumbnail(dirname($file_img5), basename($file_img5));
    $mr_img5 = '';
}
if ($mr_img6_del) {
    $file_img6 = $mr_img_dir.'/'.clean_relative_paths($mr_img6);
    @unlink($file_img6);
    delete_item_thumbnail(dirname($file_img6), basename($file_img6));
    $mr_img6 = '';
}
if ($mr_img7_del) {
    $file_img7 = $mr_img_dir.'/'.clean_relative_paths($mr_img7);
    @unlink($file_img7);
    delete_item_thumbnail(dirname($file_img7), basename($file_img7));
    $mr_img7 = '';
}
if ($mr_img8_del) {
    $file_img8 = $mr_img_dir.'/'.clean_relative_paths($mr_img8);
    @unlink($file_img8);
    delete_item_thumbnail(dirname($file_img8), basename($file_img8));
    $mr_img8 = '';
}
if ($mr_img9_del) {
    $file_img9 = $mr_img_dir.'/'.clean_relative_paths($mr_img9);
    @unlink($file_img9);
    delete_item_thumbnail(dirname($file_img9), basename($file_img9));
    $mr_img9 = '';
}
if ($mr_img10_del) {
    $file_img10 = $mr_img_dir.'/'.clean_relative_paths($mr_img10);
    @unlink($file_img10);
    delete_item_thumbnail(dirname($file_img10), basename($file_img10));
    $mr_img10 = '';
}

// 이미지업로드
if ($_FILES['mr_img1']['name']) {
    if($w == 'u' && $mr_img1) {
        $file_img1 = $mr_img_dir.'/'.clean_relative_paths($mr_img1);
        @unlink($file_img1);
        delete_item_thumbnail(dirname($file_img1), basename($file_img1));
    }
    $mr_img1 = mr_img_upload($_FILES['mr_img1']['tmp_name'], $_FILES['mr_img1']['name'], $mr_img_dir.'/'.$it_id);    
}

if ($_FILES['mr_img2']['name']) {
    if($w == 'u' && $mr_img2) {
        $file_img2 = $mr_img_dir.'/'.clean_relative_paths($mr_img2);
        @unlink($file_img2);
        delete_item_thumbnail(dirname($file_img2), basename($file_img2));
    }
    $mr_img2 = mr_img_upload($_FILES['mr_img2']['tmp_name'], $_FILES['mr_img2']['name'], $mr_img_dir.'/'.$it_id);
}

if ($_FILES['mr_img3']['name']) {
    if($w == 'u' && $mr_img3) {
        $file_img3 = $mr_img_dir.'/'.clean_relative_paths($mr_img3);
        @unlink($file_img3);
        delete_item_thumbnail(dirname($file_img3), basename($file_img3));
    }
    $mr_img3 = mr_img_upload($_FILES['mr_img3']['tmp_name'], $_FILES['mr_img3']['name'], $mr_img_dir.'/'.$it_id);
}
if ($_FILES['mr_img4']['name']) {
    if($w == 'u' && $mr_img4) {
        $file_img4 = $mr_img_dir.'/'.clean_relative_paths($mr_img4);
        @unlink($file_img4);
        delete_item_thumbnail(dirname($file_img4), basename($file_img4));
    }
    $mr_img4 = mr_img_upload($_FILES['mr_img4']['tmp_name'], $_FILES['mr_img4']['name'], $mr_img_dir.'/'.$it_id);
}
if ($_FILES['mr_img5']['name']) {
    if($w == 'u' && $mr_img5) {
        $file_img5 = $mr_img_dir.'/'.clean_relative_paths($mr_img5);
        @unlink($file_img5);
        delete_item_thumbnail(dirname($file_img5), basename($file_img5));
    }
    $mr_img5 = mr_img_upload($_FILES['mr_img5']['tmp_name'], $_FILES['mr_img5']['name'], $mr_img_dir.'/'.$it_id);
}
if ($_FILES['mr_img6']['name']) {
    if($w == 'u' && $mr_img6) {
        $file_img6 = $mr_img_dir.'/'.clean_relative_paths($mr_img6);
        @unlink($file_img6);
        delete_item_thumbnail(dirname($file_img6), basename($file_img6));
    }
    $mr_img6 = mr_img_upload($_FILES['mr_img6']['tmp_name'], $_FILES['mr_img6']['name'], $mr_img_dir.'/'.$it_id);
}
if ($_FILES['mr_img7']['name']) {
    if($w == 'u' && $mr_img7) {
        $file_img7 = $mr_img_dir.'/'.clean_relative_paths($mr_img7);
        @unlink($file_img7);
        delete_item_thumbnail(dirname($file_img7), basename($file_img7));
    }
    $mr_img7 = mr_img_upload($_FILES['mr_img7']['tmp_name'], $_FILES['mr_img7']['name'], $mr_img_dir.'/'.$it_id);
}
if ($_FILES['mr_img8']['name']) {
    if($w == 'u' && $mr_img8) {
        $file_img8 = $mr_img_dir.'/'.clean_relative_paths($mr_img8);
        @unlink($file_img8);
        delete_item_thumbnail(dirname($file_img8), basename($file_img8));
    }
    $mr_img8 = mr_img_upload($_FILES['mr_img8']['tmp_name'], $_FILES['mr_img8']['name'], $mr_img_dir.'/'.$it_id);
}
if ($_FILES['mr_img9']['name']) {
    if($w == 'u' && $mr_img9) {
        $file_img9 = $mr_img_dir.'/'.clean_relative_paths($mr_img9);
        @unlink($file_img9);
        delete_item_thumbnail(dirname($file_img9), basename($file_img9));
    }
    $mr_img9 = mr_img_upload($_FILES['mr_img9']['tmp_name'], $_FILES['mr_img9']['name'], $mr_img_dir.'/'.$it_id);
}
if ($_FILES['mr_img10']['name']) {
    if($w == 'u' && $mr_img10) {
        $file_img10 = $mr_img_dir.'/'.clean_relative_paths($mr_img10);
        @unlink($file_img10);
        delete_item_thumbnail(dirname($file_img10), basename($file_img10));
    }
    $mr_img10 = mr_img_upload($_FILES['mr_img10']['tmp_name'], $_FILES['mr_img10']['name'], $mr_img_dir.'/'.$it_id);
}

$sql_common = "  mb_id = '{$mb_id}',
				 inf_id = '{$inf_id}',
				 mr_score1 = '{$mr_score1}',
				 mr_score2 = '{$mr_score2}',
				 mr_score3 = '{$mr_score3}',
				 mr_score4 = '{$mr_score4}',
				 mr_title = '{$mr_title}',
				 mr_summary = '{$mr_summary}',
				 mr_content = '{$mr_content}',
				 mr_confirm = '{$mr_confirm}',
				 mr_link = '{$mr_link}',
				 mr_img1             = '$mr_img1',
				 mr_img2             = '$mr_img2',
				 mr_img3             = '$mr_img3',
				 mr_img4             = '$mr_img4',
				 mr_img5             = '$mr_img5',
				 mr_img6             = '$mr_img6',
				 mr_img7             = '$mr_img7',
				 mr_img8             = '$mr_img8',
				 mr_img9             = '$mr_img9',
				 mr_img10            = '$mr_img10' ";

if ($w == "") {
	$sql = " insert into {$g5['g5_main_review_table']} set it_id = '{$it_id}', mr_datetime = '" . G5_TIME_YMDHIS . "', {$sql_common} ";
	
	//var_dump($sql);
	//exit;
	sql_query($sql);
	
	//$mr_no = sql_insert_id();

	//update_use_point($mb_id, $mr_no, $mr_confirm, '');// 회원아이디, 후기일련번호, 노출여부, (등록,수정,삭제)
} else if ($w == "u") {	
    $sql = " update {$g5['g5_main_review_table']} set mr_datetime = '".G5_TIME_YMDHIS."', {$sql_common} where mr_no = '$mr_no' ";
    sql_query($sql);

	//update_use_point($mb_id, $mr_no, $mr_confirm, 'u');// 회원아이디, 후기일련번호, 노출여부, (등록,수정,삭제)	
}

goto_url('./main_review_list.php?'.$qstr, false);