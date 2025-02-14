<?php
$sub_menu = '400650';
include_once('./_common.php');
/***********************************************
* ���� ��� / ����
	- ���� ����� �����ڸ� ����� �� �����Ƿ� ����Ʈ ������ ���� �ʴ´�.
	- ���� ������ �����ڰ� �ƴϸ鼭 is_confirm �� Ȯ�εǸ� ù ���޿��θ� ������.
***********************************************/

if ($w == 'd')
    auth_check_menu($auth, $sub_menu, "d");
else
    auth_check_menu($auth, $sub_menu, "w");

//check_admin_token();

@mkdir(G5_DATA_PATH."/itemuse", G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH."/itemuse", G5_DIR_PERMISSION);

$it_id							= isset($_POST['it_id']) ? preg_replace('/[^A-Za-z0-9\-_]/', '', $_POST['it_id']) : '';// ��ǰID
$is_name						= isset($_POST['is_name']) ? clean_xss_tags(trim($_POST['is_name']), 1, 1) : '';// �̸�
$is_pay_mthod					= (isset($_POST['is_pay_mthod']) && in_array($_POST['is_pay_mthod'], array('solo', 'group')))	? $_POST['is_pay_mthod'] : 'solo';// ���Ź��(��������, �򰡴�)
$is_outage_num					= $_POST['is_outage_num']	? preg_replace('/[^0-9]/', '', trim($_POST['is_outage_num'])) : 0; // ü�߰���
$is_score1						= isset($_POST['is_score1'])	? preg_replace('/[^0-9]/', '', trim($_POST['is_score1'])) : 1; // ȿ��
$is_score2						= isset($_POST['is_score2'])	? preg_replace('/[^0-9]/', '', trim($_POST['is_score2'])) : 1; // ������
$is_score3						= isset($_POST['is_score3'])	? preg_replace('/[^0-9]/', '', trim($_POST['is_score3'])) : 1; // �� / ��
$is_score4						= isset($_POST['is_score4'])	? preg_replace('/[^0-9]/', '', trim($_POST['is_score4'])) : 1; // ����
$is_positive_review_text		= isset($_POST['is_positive_review_text']) ? clean_xss_tags(trim($_POST['is_positive_review_text']), 1, 1, 0, 0) : '';// ���Ҵ� ��
$is_negative_review_text		= isset($_POST['is_negative_review_text']) ? clean_xss_tags(trim($_POST['is_negative_review_text']), 1, 1, 0, 0) : '';// �ƽ��� ��
$is_more_review_text			= isset($_POST['is_more_review_text']) ? clean_xss_tags(trim($_POST['is_more_review_text']), 1, 1, 0, 0) : '';// ����
$is_recommend					= (isset($_POST['is_recommend']) && in_array($_POST['is_recommend'], array('y', 'n')))	? $_POST['is_recommend'] : 'n';// ������(����, �Ҹ���)
$is_rv_check					= isset($_POST['is_rv_check']) ? clean_xss_tags(trim($_POST['is_rv_check']), 1, 1) : '0';// ������븮��
$is_rvkind						= (isset($_POST['is_rvkind']) && in_array($_POST['is_rvkind'], array('general', 'supporter')))	? $_POST['is_rvkind'] : 'general';// ��������(�Ϲ�, ������)
$is_confirm						= (isset($_POST['is_confirm']) && in_array($_POST['is_confirm'], array('1', '0')))	? $_POST['is_confirm'] : '0';// Ȯ��(��, �ƴϿ�)

$it = get_shop_item($it_id);
if(!$it)
	alert('��ǰ������ �������� �ʽ��ϴ�.');

$is_img1 = $is_img2 = $is_img3 = $is_img4 = $is_img5 = $is_img6 = $is_img7 = $is_img8 = $is_img9 = $is_img10 = '';
// ��������
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

// ���ϻ���
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

// �̹������ε�
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

	update_use_point($mb_id, $is_id, $is_confirm, '');// ȸ�����̵�, �ı��Ϸù�ȣ, ���⿩��, (���,����,����)
}
else if ($w == "u")
{	
    $sql = " update {$g5['g5_shop_item_use_table']} set is_update_time = '".G5_TIME_YMDHIS."', {$sql_common} where is_id = '$is_id' ";
    sql_query($sql);

	update_use_point($mb_id, $is_id, $is_confirm, 'u');// ȸ�����̵�, �ı��Ϸù�ȣ, ���⿩��, (���,����,����)	
}

goto_url('./itemuselist.php?'.$qstr, false);