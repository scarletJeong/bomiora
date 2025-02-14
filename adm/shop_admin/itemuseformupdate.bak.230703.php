<?php
$sub_menu = '400650';
include_once('./_common.php');
/***********************************************
* 리뷰 등록 / 수정
	- 리뷰 등록은 관리자만 등록할 수 있으므로 포인트 지급을 하지 않는다.
	- 리뷰 수정은 관리자가 아니면서 is_confirm 이 확인되면 첫 지급여부를 따진다.
***********************************************/

if ($w == 'd')
    auth_check_menu($auth, $sub_menu, "d");
else
    auth_check_menu($auth, $sub_menu, "w");

//check_admin_token();

@mkdir(G5_DATA_PATH."/itemuse", G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH."/itemuse", G5_DIR_PERMISSION);

$it_id							= isset($_POST['it_id']) ? preg_replace('/[^A-Za-z0-9\-_]/', '', $_POST['it_id']) : '';// 제품ID
$is_name						= isset($_POST['is_name']) ? clean_xss_tags(trim($_POST['is_name']), 1, 1) : '';// 이름
$is_pay_mthod					= (isset($_POST['is_pay_mthod']) && in_array($_POST['is_pay_mthod'], array('solo', 'group')))	? $_POST['is_pay_mthod'] : 'solo';// 구매방식(내돈내산, 평가단)
$is_outage_num					= $_POST['is_outage_num']	? preg_replace('/[^0-9]/', '', trim($_POST['is_outage_num'])) : 0; // 체중감량
$is_score1						= isset($_POST['is_score1'])	? preg_replace('/[^0-9]/', '', trim($_POST['is_score1'])) : 1; // 효과
$is_score2						= isset($_POST['is_score2'])	? preg_replace('/[^0-9]/', '', trim($_POST['is_score2'])) : 1; // 가성비
$is_score3						= isset($_POST['is_score3'])	? preg_replace('/[^0-9]/', '', trim($_POST['is_score3'])) : 1; // 향 / 맛
$is_score4						= isset($_POST['is_score4'])	? preg_replace('/[^0-9]/', '', trim($_POST['is_score4'])) : 1; // 편리함
$is_positive_review_text		= isset($_POST['is_positive_review_text']) ? clean_xss_tags(trim($_POST['is_positive_review_text']), 1, 1, 0, 0) : '';// 좋았던 점
$is_negative_review_text		= isset($_POST['is_negative_review_text']) ? clean_xss_tags(trim($_POST['is_negative_review_text']), 1, 1, 0, 0) : '';// 아쉬운 점
$is_more_review_text			= isset($_POST['is_more_review_text']) ? clean_xss_tags(trim($_POST['is_more_review_text']), 1, 1, 0, 0) : '';// 꿀팁
$is_recommend					= (isset($_POST['is_recommend']) && in_array($_POST['is_recommend'], array('y', 'n')))	? $_POST['is_recommend'] : 'n';// 만족도(만족, 불만족)
$is_rv_check					= isset($_POST['is_rv_check']) ? clean_xss_tags(trim($_POST['is_rv_check']), 1, 1) : '0';// 직접사용리뷰
$is_rvkind						= (isset($_POST['is_rvkind']) && in_array($_POST['is_rvkind'], array('general', 'supporter')))	? $_POST['is_rvkind'] : 'general';// 리뷰종류(일반, 서포터)
$is_confirm						= (isset($_POST['is_confirm']) && in_array($_POST['is_confirm'], array('1', '0')))	? $_POST['is_confirm'] : '0';// 확인(예, 아니오)

$it = get_shop_item($it_id);
if(!$it)
	alert('상품정보가 존재하지 않습니다.');

$is_img1 = $is_img2 = $is_img3 = $is_img4 = $is_img5 = $is_img6 = $is_img7 = $is_img8 = $is_img9 = $is_img10 = '';
// 파일정보
if($w == "u") {
    $sql = " select is_img1, is_img2, is_img3, is_img4, is_img5, is_img6, is_img7, is_img8, is_img9, is_img10
                from {$g5['g5_shop_item_use_table']}
                where is_id = '$is_id' ";
    $file = sql_fetch($sql);

    $is_img1    = $file['is_img1'];
    $is_img2    = $file['is_img2'];
    $is_img3    = $file['is_img3'];
    $is_img4    = $file['is_img4'];
    $is_img5    = $file['is_img5'];
    $is_img6    = $file['is_img6'];
    $is_img7    = $file['is_img7'];
    $is_img8    = $file['is_img8'];
    $is_img9    = $file['is_img9'];
    $is_img10   = $file['is_img10'];
}

$is_img_dir = G5_DATA_PATH.'/itemuse';

for($i=0;$i<=10;$i++){
    ${'is_img'.$i.'_del'} = ! empty($_POST['is_img'.$i.'_del']) ? 1 : 0;
}

// 파일삭제
if ($is_img1_del) {
    $file_img1 = $is_img_dir.'/'.clean_relative_paths($is_img1);
    @unlink($file_img1);
    delete_item_thumbnail(dirname($file_img1), basename($file_img1));
    $is_img1 = '';
}
if ($is_img2_del) {
    $file_img2 = $is_img_dir.'/'.clean_relative_paths($is_img2);
    @unlink($file_img2);
    delete_item_thumbnail(dirname($file_img2), basename($file_img2));
    $is_img2 = '';
}
if ($is_img3_del) {
    $file_img3 = $is_img_dir.'/'.clean_relative_paths($is_img3);
    @unlink($file_img3);
    delete_item_thumbnail(dirname($file_img3), basename($file_img3));
    $is_img3 = '';
}
if ($is_img4_del) {
    $file_img4 = $is_img_dir.'/'.clean_relative_paths($is_img4);
    @unlink($file_img4);
    delete_item_thumbnail(dirname($file_img4), basename($file_img4));
    $is_img4 = '';
}
if ($is_img5_del) {
    $file_img5 = $is_img_dir.'/'.clean_relative_paths($is_img5);
    @unlink($file_img5);
    delete_item_thumbnail(dirname($file_img5), basename($file_img5));
    $is_img5 = '';
}
if ($is_img6_del) {
    $file_img6 = $is_img_dir.'/'.clean_relative_paths($is_img6);
    @unlink($file_img6);
    delete_item_thumbnail(dirname($file_img6), basename($file_img6));
    $is_img6 = '';
}
if ($is_img7_del) {
    $file_img7 = $is_img_dir.'/'.clean_relative_paths($is_img7);
    @unlink($file_img7);
    delete_item_thumbnail(dirname($file_img7), basename($file_img7));
    $is_img7 = '';
}
if ($is_img8_del) {
    $file_img8 = $is_img_dir.'/'.clean_relative_paths($is_img8);
    @unlink($file_img8);
    delete_item_thumbnail(dirname($file_img8), basename($file_img8));
    $is_img8 = '';
}
if ($is_img9_del) {
    $file_img9 = $is_img_dir.'/'.clean_relative_paths($is_img9);
    @unlink($file_img9);
    delete_item_thumbnail(dirname($file_img9), basename($file_img9));
    $is_img9 = '';
}
if ($is_img10_del) {
    $file_img10 = $is_img_dir.'/'.clean_relative_paths($is_img10);
    @unlink($file_img10);
    delete_item_thumbnail(dirname($file_img10), basename($file_img10));
    $is_img10 = '';
}

// 이미지업로드
if ($_FILES['is_img1']['name']) {
    if($w == 'u' && $is_img1) {
        $file_img1 = $is_img_dir.'/'.clean_relative_paths($is_img1);
        @unlink($file_img1);
        delete_item_thumbnail(dirname($file_img1), basename($file_img1));
    }
    $is_img1 = is_img_upload($_FILES['is_img1']['tmp_name'], $_FILES['is_img1']['name'], $is_img_dir.'/'.$it_id);
}

if ($_FILES['is_img2']['name']) {
    if($w == 'u' && $is_img2) {
        $file_img2 = $is_img_dir.'/'.clean_relative_paths($is_img2);
        @unlink($file_img2);
        delete_item_thumbnail(dirname($file_img2), basename($file_img2));
    }
    $is_img2 = is_img_upload($_FILES['is_img2']['tmp_name'], $_FILES['is_img2']['name'], $is_img_dir.'/'.$it_id);
}

if ($_FILES['is_img3']['name']) {
    if($w == 'u' && $is_img3) {
        $file_img3 = $is_img_dir.'/'.clean_relative_paths($is_img3);
        @unlink($file_img3);
        delete_item_thumbnail(dirname($file_img3), basename($file_img3));
    }
    $is_img3 = is_img_upload($_FILES['is_img3']['tmp_name'], $_FILES['is_img3']['name'], $is_img_dir.'/'.$it_id);
}
if ($_FILES['is_img4']['name']) {
    if($w == 'u' && $is_img4) {
        $file_img4 = $is_img_dir.'/'.clean_relative_paths($is_img4);
        @unlink($file_img4);
        delete_item_thumbnail(dirname($file_img4), basename($file_img4));
    }
    $is_img4 = is_img_upload($_FILES['is_img4']['tmp_name'], $_FILES['is_img4']['name'], $is_img_dir.'/'.$it_id);
}
if ($_FILES['is_img5']['name']) {
    if($w == 'u' && $is_img5) {
        $file_img5 = $is_img_dir.'/'.clean_relative_paths($is_img5);
        @unlink($file_img5);
        delete_item_thumbnail(dirname($file_img5), basename($file_img5));
    }
    $is_img5 = is_img_upload($_FILES['is_img5']['tmp_name'], $_FILES['is_img5']['name'], $is_img_dir.'/'.$it_id);
}
if ($_FILES['is_img6']['name']) {
    if($w == 'u' && $is_img6) {
        $file_img6 = $is_img_dir.'/'.clean_relative_paths($is_img6);
        @unlink($file_img6);
        delete_item_thumbnail(dirname($file_img6), basename($file_img6));
    }
    $is_img6 = is_img_upload($_FILES['is_img6']['tmp_name'], $_FILES['is_img6']['name'], $is_img_dir.'/'.$it_id);
}
if ($_FILES['is_img7']['name']) {
    if($w == 'u' && $is_img7) {
        $file_img7 = $is_img_dir.'/'.clean_relative_paths($is_img7);
        @unlink($file_img7);
        delete_item_thumbnail(dirname($file_img7), basename($file_img7));
    }
    $is_img7 = is_img_upload($_FILES['is_img7']['tmp_name'], $_FILES['is_img7']['name'], $is_img_dir.'/'.$it_id);
}
if ($_FILES['is_img8']['name']) {
    if($w == 'u' && $is_img8) {
        $file_img8 = $is_img_dir.'/'.clean_relative_paths($is_img8);
        @unlink($file_img8);
        delete_item_thumbnail(dirname($file_img8), basename($file_img8));
    }
    $is_img8 = is_img_upload($_FILES['is_img8']['tmp_name'], $_FILES['is_img8']['name'], $is_img_dir.'/'.$it_id);
}
if ($_FILES['is_img9']['name']) {
    if($w == 'u' && $is_img9) {
        $file_img9 = $is_img_dir.'/'.clean_relative_paths($is_img9);
        @unlink($file_img9);
        delete_item_thumbnail(dirname($file_img9), basename($file_img9));
    }
    $is_img9 = is_img_upload($_FILES['is_img9']['tmp_name'], $_FILES['is_img9']['name'], $is_img_dir.'/'.$it_id);
}
if ($_FILES['is_img10']['name']) {
    if($w == 'u' && $is_img10) {
        $file_img10 = $is_img_dir.'/'.clean_relative_paths($is_img10);
        @unlink($file_img10);
        delete_item_thumbnail(dirname($file_img10), basename($file_img10));
    }
    $is_img10 = is_img_upload($_FILES['is_img10']['tmp_name'], $_FILES['is_img10']['name'], $is_img_dir.'/'.$it_id);
}

$sql_common = "  is_name = '{$is_name}',
                 is_pay_mthod = '{$is_pay_mthod}',
				 is_outage_num = '{$is_outage_num}',
				 is_score1 = '{$is_score1}',
				 is_score2 = '{$is_score2}',
				 is_score3 = '{$is_score3}',
				 is_score4 = '{$is_score4}',
				 is_positive_review_text = '{$is_positive_review_text}',
				 is_negative_review_text = '{$is_negative_review_text}',
				 is_more_review_text = '{$is_more_review_text}',
				 is_recommend = '{$is_recommend}',
				 is_rv_check = '{$is_rv_check}',
				 is_rvkind = '{$is_rvkind}',
				 is_confirm = '{$is_confirm}',
				 is_subject = '{$is_subject}',
				 is_content = '{$is_content}',
				 is_reply_subject = '{$is_reply_subject}',
				 is_reply_content = '{$is_reply_content}',
				 is_reply_name = '{$is_reply_name}',
				 is_img1             = '$is_img1',
				 is_img2             = '$is_img2',
				 is_img3             = '$is_img3',
				 is_img4             = '$is_img4',
				 is_img5             = '$is_img5',
				 is_img6             = '$is_img6',
				 is_img7             = '$is_img7',
				 is_img8             = '$is_img8',
				 is_img9             = '$is_img9',
				 is_img10            = '$is_img10' ";

if ($w == "") {
	$sql = " insert into {$g5['g5_shop_item_use_table']} set it_id = '{$it_id}', mb_id = '{$member['mb_id']}', is_time = '" . G5_TIME_YMDHIS . "', is_ip = '{$_SERVER['REMOTE_ADDR']}', {$sql_common} ";
	sql_query($sql);
	
	$is_id = sql_insert_id();

	update_use_point($mb_id, $is_id, $is_confirm, '');// 회원아이디, 후기일련번호, 노출여부, (등록,수정,삭제)
}
else if ($w == "u")
{	
    $sql = " update {$g5['g5_shop_item_use_table']} set is_update_time = '".G5_TIME_YMDHIS."', {$sql_common} where is_id = '$is_id' ";
    sql_query($sql);

	update_use_point($mb_id, $is_id, $is_confirm, 'u');// 회원아이디, 후기일련번호, 노출여부, (등록,수정,삭제)	
}

goto_url('./itemuselist.php?'.$qstr, false);