<?php
$sub_menu = "300100";
require_once "./_common.php";
require_once G5_LIB_PATH . "/register.lib.php";
require_once G5_LIB_PATH . '/thumbnail.lib.php';

auth_check_menu($auth, $sub_menu, 'w');

//check_admin_token();

$mb_id          = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
$mb_password    = isset($_POST['mb_password']) ? trim($_POST['mb_password']) : '';
/*
$mb_certify_case = isset($_POST['mb_certify_case']) ? preg_replace('/[^0-9a-z_]/i', '', $_POST['mb_certify_case']) : '';
$mb_certify     = isset($_POST['mb_certify']) ? preg_replace('/[^0-9a-z_]/i', '', $_POST['mb_certify']) : '';
$mb_zip         = isset($_POST['mb_zip']) ? preg_replace('/[^0-9a-z_]/i', '', $_POST['mb_zip']) : '';
*/

$profile_img	= isset($_POST['profile_img']) ? clean_xss_tags(trim($_POST['profile_img']), 1, 1) : '';// 프로필 아이콘
$mb_instar_link	= isset($_POST['mb_instar_link']) ? clean_xss_tags(trim($_POST['mb_instar_link']), 1, 1) : '';// 인스타그램
$mb_web_link	= isset($_POST['mb_web_link']) ? clean_xss_tags(trim($_POST['mb_web_link']), 1, 1) : '';// 웹링크
$mb_blog_link	= isset($_POST['mb_blog_link']) ? clean_xss_tags(trim($_POST['mb_blog_link']), 1, 1) : '';// 블로그

/* 관리자가 자동등록방지를 사용해야 할 경우 ( 회원의 비밀번호 변경시 캡챠를 체크한다 )
if ($mb_password && function_exists('get_admin_captcha_by') && get_admin_captcha_by()) {
    include_once(G5_CAPTCHA_PATH . '/captcha.lib.php');

    if (!chk_captcha()) {
        alert('자동등록방지 숫자가 틀렸습니다.');
    }
}
*/
// 휴대폰번호 체크
$mb_hp = hyphen_hp_number($_POST['mb_hp']);
if ($mb_hp) {
    $result = exist_mb_hp($mb_hp, $mb_id);
    if ($result) {
        alert($result);
    }
}

/* 인증정보처리
if ($mb_certify_case && $mb_certify) {
    $mb_certify = isset($_POST['mb_certify_case']) ? preg_replace('/[^0-9a-z_]/i', '', (string)$_POST['mb_certify_case']) : '';
    $mb_adult = isset($_POST['mb_adult']) ? preg_replace('/[^0-9a-z_]/i', '', (string)$_POST['mb_adult']) : '';
} else {
    $mb_certify = '';
    $mb_adult = 0;
}
*/
/*
$mb_zip1 = substr($mb_zip, 0, 3);
$mb_zip2 = substr($mb_zip, 3);
*/

$mb_email = isset($_POST['mb_email']) ? get_email_address(trim($_POST['mb_email'])) : '';
$mb_nick = isset($_POST['mb_nick']) ? trim(strip_tags($_POST['mb_nick'])) : '';
if ($mb_instar_link)	$mb_instar_link = set_http(get_text($mb_instar_link));
if ($mb_web_link)		$mb_web_link = set_http(get_text($mb_web_link));
if ($mb_blog_link)		$mb_blog_link = set_http(get_text($mb_blog_link));

if ($msg = valid_mb_nick($mb_nick)) {
    alert($msg, "", true, true);
}

$posts = array();
$check_keys = array(
    'mb_name',
    'mb_homepage',
    'mb_tel',
    'mb_addr1',
    'mb_addr2',
    'mb_addr3',
    'mb_addr_jibeon',
    'mb_signature',
    'mb_leave_date',
    'mb_intercept_date',
    'mb_mailling',
    'mb_sms',
    'mb_open',
    'mb_profile',
    'mb_level'
);

for ($i = 1; $i <= 10; $i++) {
    $check_keys[] = 'mb_' . $i;
}

foreach ($check_keys as $key) {
    if( in_array($key, array('mb_signature', 'mb_profile')) ){
        $posts[$key] = isset($_POST[$key]) ? clean_xss_tags($_POST[$key], 1, 1, 0, 0) : '';
    } else {
        $posts[$key] = isset($_POST[$key]) ? clean_xss_tags($_POST[$key], 1, 1) : '';
    }
}

$mb_memo = isset($_POST['mb_memo']) ? $_POST['mb_memo'] : '';

$sql_common = "  mb_name = '{$posts['mb_name']}',
                 mb_nick = '{$mb_nick}',
                 mb_email = '{$mb_email}',
                 mb_homepage = '{$posts['mb_homepage']}',
                 mb_tel = '{$posts['mb_tel']}',
                 mb_hp = '{$mb_hp}',
                 mb_certify = '{$mb_certify}',
                 mb_adult = '{$mb_adult}',
                 mb_zip1 = '$mb_zip1',
                 mb_zip2 = '$mb_zip2',
                 mb_addr1 = '{$posts['mb_addr1']}',
                 mb_addr2 = '{$posts['mb_addr2']}',
                 mb_addr3 = '{$posts['mb_addr3']}',
                 mb_addr_jibeon = '{$posts['mb_addr_jibeon']}',
                 mb_signature = '{$posts['mb_signature']}',
                 mb_leave_date = '{$posts['mb_leave_date']}',
                 mb_intercept_date='{$posts['mb_intercept_date']}',
                 mb_memo = '{$mb_memo}',
                 mb_mailling = '{$posts['mb_mailling']}',
                 mb_sms = '{$posts['mb_sms']}',
                 mb_open = '{$posts['mb_open']}',
				 profile_img = '{$profile_img}',
				 mb_instar_link = '{$mb_instar_link}',
				 mb_web_link = '{$mb_web_link}',
				 mb_blog_link = '{$mb_blog_link}',
                 mb_profile = '{$posts['mb_profile']}',
                 mb_level = '{$posts['mb_level']}',
                 mb_1 = '{$posts['mb_1']}',
                 mb_2 = '{$posts['mb_2']}',
                 mb_3 = '{$posts['mb_3']}',
                 mb_4 = '{$posts['mb_4']}',
                 mb_5 = '{$posts['mb_5']}',
                 mb_6 = '{$posts['mb_6']}',
                 mb_7 = '{$posts['mb_7']}',
                 mb_8 = '{$posts['mb_8']}',
                 mb_9 = '{$posts['mb_9']}',
                 mb_10 = '{$posts['mb_10']}' ";

if ($w == '') {
	// 인플루언서 코드 생성	
	//$inf_code = get_encrypt_string(rand(100000, 999999));
	//$inf_code = md5(pack('V*', rand(), rand(), rand(), rand()));
	$inf_code = get_uniqid();

	/*
    $mb = get_member($mb_id);
    if (isset($mb['mb_id']) && $mb['mb_id']) {
        alert('이미 존재하는 회원아이디입니다.\\nＩＤ : ' . $mb['mb_id'] . '\\n이름 : ' . $mb['mb_name'] . '\\n닉네임 : ' . $mb['mb_nick'] . '\\n메일 : ' . $mb['mb_email']);
    }
	*/

    // 닉네임중복체크
    $sql = " select mb_id, mb_name, mb_nick, mb_email from {$g5['member_table']} where mb_nick = '{$mb_nick}' ";
    $row = sql_fetch($sql);
    if (isset($row['mb_email']) && $row['mb_email']) {
        alert('이미 존재하는 닉네임입니다.\\n이름 : ' . $row['mb_name'] . '\\n닉네임 : ' . $row['mb_nick'] . '\\n메일 : ' . $row['mb_email']);
    }

    // 이메일중복체크
    $sql = " select mb_id, mb_name, mb_nick, mb_email from {$g5['member_table']} where mb_email = '{$mb_email}' ";
    $row = sql_fetch($sql);
    if (isset($row['mb_email']) && $row['mb_email']) {
        alert('이미 존재하는 이메일입니다.\\n이름 : ' . $row['mb_name'] . '\\n닉네임 : ' . $row['mb_nick'] . '\\n메일 : ' . $row['mb_email']);
    }

    sql_query(" insert into {$g5['member_table']} set mb_id = '{$inf_code}', mb_inf_code = '{$inf_code}', mb_password = '" . get_encrypt_string($mb_password) . "', mb_datetime = '" . G5_TIME_YMDHIS . "', mb_ip = '{$_SERVER['REMOTE_ADDR']}', mb_email_certify = '" . G5_TIME_YMDHIS . "', {$sql_common} ");

	$mb_no = sql_insert_id();
	
	// 아이디 생성 후 업데이트
	$mb_id = 'influencer_'.$mb_no;
	sql_query(" update {$g5['member_table']} set mb_id = '{$mb_id}' where mb_no = '{$mb_no}' ");

} elseif ($w == 'u') {
    $mb = get_member($mb_id);
    if (!(isset($mb['mb_id']) && $mb['mb_id'])) {
        alert('존재하지 않는 회원자료입니다.');
    }

    if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level']) {
        alert('자신보다 권한이 높거나 같은 회원은 수정할 수 없습니다.');
    }

    if ($is_admin !== 'super' && is_admin($mb['mb_id']) === 'super') {
        alert('최고관리자의 비밀번호를 수정할수 없습니다.');
    }

    if ($mb_id === $member['mb_id'] && $_POST['mb_level'] != $mb['mb_level']) {
        alert($mb['mb_id'] . ' : 로그인 중인 관리자 레벨은 수정할 수 없습니다.');
    }

    if ($posts['mb_leave_date'] || $posts['mb_intercept_date']){
        if ($member['mb_id'] === $mb['mb_id'] || is_admin($mb['mb_id']) === 'super'){
            alert('해당 관리자의 탈퇴 일자 또는 접근 차단 일자를 수정할 수 없습니다.');
        }
    }

    // 닉네임중복체크
    $sql = " select mb_id, mb_name, mb_nick, mb_email from {$g5['member_table']} where mb_nick = '{$mb_nick}' and mb_id <> '$mb_id' ";
    $row = sql_fetch($sql);
    if (isset($row['mb_id']) && $row['mb_id']) {
        alert('이미 존재하는 닉네임입니다.\\nＩＤ : ' . $row['mb_id'] . '\\n이름 : ' . $row['mb_name'] . '\\n닉네임 : ' . $row['mb_nick'] . '\\n메일 : ' . $row['mb_email']);
    }

    // 이메일중복체크
    $sql = " select mb_id, mb_name, mb_nick, mb_email from {$g5['member_table']} where mb_email = '{$mb_email}' and mb_id <> '$mb_id' ";
    $row = sql_fetch($sql);
    if (isset($row['mb_id']) && $row['mb_id']) {
        alert('이미 존재하는 이메일입니다.\\nＩＤ : ' . $row['mb_id'] . '\\n이름 : ' . $row['mb_name'] . '\\n닉네임 : ' . $row['mb_nick'] . '\\n메일 : ' . $row['mb_email']);
    }

    if ($mb_password) {
        $sql_password = " , mb_password = '" . get_encrypt_string($mb_password) . "' ";
    } else {
        $sql_password = "";
    }

    if (isset($passive_certify) && $passive_certify) {
        $sql_certify = " , mb_email_certify = '" . G5_TIME_YMDHIS . "' ";
    } else {
        $sql_certify = "";
    }

    $sql = " update {$g5['member_table']}
                set {$sql_common}
                     {$sql_password}
                     {$sql_certify}
                where mb_id = '{$mb_id}' ";
    sql_query($sql);
} else {
    alert('제대로 된 값이 넘어오지 않았습니다.');
}

if ($w == '' || $w == 'u') {
    $mb_dir = substr($mb_id, 0, 2);
    $mb_icon_img = get_mb_icon_name($mb_id) . '.gif';

    // 회원 아이콘 삭제
    if (isset($del_mb_icon) && $del_mb_icon) {
        @unlink(G5_DATA_PATH . '/member/' . $mb_dir . '/' . $mb_icon_img);
    }

    $image_regex = "/(\.(gif|jpe?g|png))$/i";

    // 아이콘 업로드
    if (isset($_FILES['mb_icon']) && is_uploaded_file($_FILES['mb_icon']['tmp_name'])) {
        if (!preg_match($image_regex, $_FILES['mb_icon']['name'])) {
            alert($_FILES['mb_icon']['name'] . '은(는) 이미지 파일이 아닙니다.');
        }

        if (preg_match($image_regex, $_FILES['mb_icon']['name'])) {
            $mb_icon_dir = G5_DATA_PATH . '/member/' . $mb_dir;
            @mkdir($mb_icon_dir, G5_DIR_PERMISSION);
            @chmod($mb_icon_dir, G5_DIR_PERMISSION);

            $dest_path = $mb_icon_dir . '/' . $mb_icon_img;

            move_uploaded_file($_FILES['mb_icon']['tmp_name'], $dest_path);
            chmod($dest_path, G5_FILE_PERMISSION);

            if (file_exists($dest_path)) {
                $size = @getimagesize($dest_path);
                if ($size) {
                    if ($size[0] > $config['cf_member_icon_width'] || $size[1] > $config['cf_member_icon_height']) {
                        $thumb = null;
                        if ($size[2] === 2 || $size[2] === 3) {
                            //jpg 또는 png 파일 적용
                            $thumb = thumbnail($mb_icon_img, $mb_icon_dir, $mb_icon_dir, $config['cf_member_icon_width'], $config['cf_member_icon_height'], true, true);
                            if ($thumb) {
                                @unlink($dest_path);
                                rename($mb_icon_dir . '/' . $thumb, $dest_path);
                            }
                        }
                        if (!$thumb) {
                            // 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
                            @unlink($dest_path);
                        }
                    }
                }
            }
        }
    }

    $mb_img_dir = G5_DATA_PATH . '/member_image/';
    if (!is_dir($mb_img_dir)) {
        @mkdir($mb_img_dir, G5_DIR_PERMISSION);
        @chmod($mb_img_dir, G5_DIR_PERMISSION);
    }
    $mb_img_dir .= substr($mb_id, 0, 2);

    // 회원 이미지 삭제
    if (isset($del_mb_img) && $del_mb_img) {
        @unlink($mb_img_dir . '/' . $mb_icon_img);
    }
	
	if($profile_img) {
		// 아이콘 삭제
		@unlink($mb_img_dir.'/'.$mb_icon_img);
	} else {
		// 아이콘 업로드
		if (isset($_FILES['mb_img']) && is_uploaded_file($_FILES['mb_img']['tmp_name'])) {
			if (!preg_match($image_regex, $_FILES['mb_img']['name'])) {
				alert($_FILES['mb_img']['name'] . '은(는) 이미지 파일이 아닙니다.');
			}
			
			if (preg_match($image_regex, $_FILES['mb_img']['name'])) {
				@mkdir($mb_img_dir, G5_DIR_PERMISSION);
				@chmod($mb_img_dir, G5_DIR_PERMISSION);
				
				$dest_path = $mb_img_dir . '/' . $mb_icon_img;
				
				move_uploaded_file($_FILES['mb_img']['tmp_name'], $dest_path);
				chmod($dest_path, G5_FILE_PERMISSION);
				
				if (file_exists($dest_path)) {
					$size = @getimagesize($dest_path);
					if ($size) {
						if ($size[0] > $config['cf_member_img_width'] || $size[1] > $config['cf_member_img_height']) {
							$thumb = null;
							if ($size[2] === 2 || $size[2] === 3) {
								//jpg 또는 png 파일 적용
								$thumb = thumbnail($mb_icon_img, $mb_img_dir, $mb_img_dir, $config['cf_member_img_width'], $config['cf_member_img_height'], true, true);
								if ($thumb) {
									@unlink($dest_path);
									rename($mb_img_dir . '/' . $thumb, $dest_path);
								}
							}
							if (!$thumb) {
								// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
								@unlink($dest_path);
							}
						}
					}
				}
			}
		}
    }
}
/*
if (function_exists('get_admin_captcha_by')) {
    get_admin_captcha_by('remove');
}
*/

/* 프로필 */
$it_id			= isset($_POST['direct_it_id']) ? get_search_string(trim($_POST['direct_it_id'])) : '';
$answer_1		= isset($_POST['answer_1'])		? preg_replace('/[^0-9]/', '', trim($_POST['answer_1'])) : ''; // 생년월일(YYYYMMDD)
$answer_2		= isset($_POST['answer_2']) ? clean_xss_tags(trim($_POST['answer_2']), 1, 1) : '';// 성별(M/F)
$answer_3		= isset($_POST['answer_3'])		? preg_replace('/[^0-9]/', '', trim($_POST['answer_3'])) : ''; // 목표감량체중(KG)
$answer_4		= isset($_POST['answer_4'])		? preg_replace('/[^0-9]/', '', trim($_POST['answer_4'])) : ''; // 키(CM)
$answer_5		= isset($_POST['answer_5'])		? preg_replace('/[^0-9]/', '', trim($_POST['answer_5'])) : ''; // 몸무게(KG)
$answer_6		= (isset($_POST['answer_6']) && in_array($_POST['answer_6'], array_keys($_const['diet_period'])))	? $_POST['answer_6'] : '';// 다이어트 예상기간
$answer_7		= (isset($_POST['answer_7']) && in_array($_POST['answer_7'], array_keys($_const['day_meal'])))	? $_POST['answer_7'] : '';// 하루끼니
//$answer_8		= (isset($_POST['answer_8']) && in_array($_POST['answer_8'], array_keys($_const['eating_habits'])))	? $_POST['answer_8'] : '';// 식습관(중복가능)
$answer_8_cnt	= (isset($_POST['answer_8']) && is_array($_POST['answer_8'])) ? count($_POST['answer_8']) : 0;// 식습관(카운트)
//$answer_9		= (isset($_POST['answer_9']) && in_array($_POST['answer_9'], array_keys($_const['often_food'])))	? $_POST['answer_9'] : '';// 자주 먹는 음식(중복가능)
$answer_9_cnt	= (isset($_POST['answer_9']) && is_array($_POST['answer_9'])) ? count($_POST['answer_9']) : 0;// 자주 먹는 음식(카운트)
$answer_10		= (isset($_POST['answer_10']) && in_array($_POST['answer_10'], array_keys($_const['exercise_habit'])))	? $_POST['answer_10'] : '';// 운동습관
//$answer_11	= (isset($_POST['answer_11']) && in_array($_POST['answer_11'], array_keys($_const['often_food'])))	? $_POST['answer_11'] : '';// 질병(중복가능)
$answer_11_cnt	= (isset($_POST['answer_11']) && is_array($_POST['answer_11'])) ? count($_POST['answer_11']) : 0;// 질병(카운트)
//$answer_12	= (isset($_POST['answer_12']) && in_array($_POST['answer_12'], array_keys($_const['often_food'])))	? $_POST['answer_12'] : '';// 복용중인 약(중복가능)
$answer_12_cnt	= (isset($_POST['answer_12']) && is_array($_POST['answer_12'])) ? count($_POST['answer_12']) : 0;// 복용중인 약(카운트)

// 식습관
if($answer_8_cnt) {
    $arr_answer_8 = array();
    for($i=0; $i<$answer_8_cnt; $i++) {
        $post_answer_8_id = isset($_POST['answer_8'][$i]) ? preg_replace(G5_OPTION_ID_FILTER, '', strip_tags($_POST['answer_8'][$i])) : '';

        $answer_8_val = explode(chr(30), $post_answer_8_id);
        if(!in_array($answer_8_val[0], $arr_answer_8))
            $arr_answer_8[] = $answer_8_val[0];
    }

    $answer_8 = implode('|', $arr_answer_8);
}


// 자주 먹는 음식
if($answer_9_cnt) {
    $arr_answer_9 = array();
    for($i=0; $i<$answer_9_cnt; $i++) {
        $post_answer_9_id = isset($_POST['answer_9'][$i]) ? preg_replace(G5_OPTION_ID_FILTER, '', strip_tags($_POST['answer_9'][$i])) : '';

        $answer_9_val = explode(chr(30), $post_answer_9_id);
        if(!in_array($answer_9_val[0], $arr_answer_9))
            $arr_answer_9[] = $answer_9_val[0];
    }

    $answer_9 = implode('|', $arr_answer_9);
}


// 질병
if($answer_11_cnt) {
    $arr_answer_11 = array();
    for($i=0; $i<$answer_11_cnt; $i++) {
        $post_answer_11_id = isset($_POST['answer_11'][$i]) ? preg_replace(G5_OPTION_ID_FILTER, '', strip_tags($_POST['answer_11'][$i])) : '';

        $answer_11_val = explode(chr(30), $post_answer_11_id);
        if(!in_array($answer_11_val[0], $arr_answer_11))
            $arr_answer_11[] = $answer_11_val[0];
    }

    $answer_11 = implode('|', $arr_answer_11);
}

// 복용중인 약
if($answer_12_cnt) {
    $arr_answer_12 = array();
    for($i=0; $i<$answer_12_cnt; $i++) {
        $post_answer_12_id = isset($_POST['answer_12'][$i]) ? preg_replace(G5_OPTION_ID_FILTER, '', strip_tags($_POST['answer_12'][$i])) : '';

        $answer_12_val = explode(chr(30), $post_answer_12_id);
        if(!in_array($answer_12_val[0], $arr_answer_12))
            $arr_answer_12[] = $answer_12_val[0];
    }

    $answer_12 = implode('|', $arr_answer_12);
}

// 중복체크
$sql = " select mb_id from {$g5['health_profile_table']} where mb_id = '{$mb_id}' ";
$row = sql_fetch($sql);
if (!empty($row['mb_id'])) {// 업데이트
    $sql = " update {$g5['health_profile_table']}
                set answer_1 = '{$answer_1}',
					answer_2 = '{$answer_2}',
					answer_3 = '{$answer_3}',
					answer_4 = '{$answer_4}',
					answer_5 = '{$answer_5}',
					answer_6 = '{$answer_6}',
					answer_7 = '{$answer_7}',
					answer_8 = '{$answer_8}',
					answer_9 = '{$answer_9}',
					answer_10 = '{$answer_10}',
					answer_11 = '{$answer_11}',
					answer_12 = '{$answer_12}',
					pf_mdatetime = '".G5_TIME_YMDHIS."',
                    pf_1 = '{$pf_1}',
                    pf_2 = '{$pf_2}',
                    pf_3 = '{$pf_3}',
                    pf_4 = '{$pf_4}',
                    pf_5 = '{$pf_5}',
                    pf_6 = '{$pf_6}',
                    pf_7 = '{$pf_7}',
                    pf_8 = '{$pf_8}',
                    pf_9 = '{$pf_9}',
                    pf_10 = '{$pf_10}'                    
              where mb_id = '$mb_id' ";
    sql_query($sql);
} else {// 인서트
	$sql = " insert into {$g5['health_profile_table']}
				set mb_id = '{$mb_id}',
					answer_1 = '{$answer_1}',
					answer_2 = '{$answer_2}',
					answer_3 = '{$answer_3}',
					answer_4 = '{$answer_4}',
					answer_5 = '{$answer_5}',
					answer_6 = '{$answer_6}',
					answer_7 = '{$answer_7}',
					answer_8 = '{$answer_8}',
					answer_9 = '{$answer_9}',
					answer_10 = '{$answer_10}',
					answer_11 = '{$answer_11}',
					answer_12 = '{$answer_12}',
					pf_wdatetime = '".G5_TIME_YMDHIS."',
					pf_ip = '{$_SERVER['REMOTE_ADDR']}',
					pf_1 = '{$pf_1}',
					pf_2 = '{$pf_2}',
					pf_3 = '{$pf_3}',
					pf_4 = '{$pf_4}',
					pf_5 = '{$pf_5}',
					pf_6 = '{$pf_6}',
					pf_7 = '{$pf_7}',
					pf_8 = '{$pf_8}',
					pf_9 = '{$pf_9}',
					pf_10 = '{$pf_10}' ";
	sql_query($sql);
}

run_event('admininfluencer__member_form_update', $w, $mb_id);

goto_url('./influencer_member_form.php?' . $qstr . '&amp;w=u&amp;mb_id=' . $mb_id, false);
