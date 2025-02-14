<?php
$sub_menu = '400400';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$hp_no = isset($_REQUEST['od_settle_case']) ? clean_xss_tags($_REQUEST['hp_no'], 1, 1) : '';
$od_id = isset($_REQUEST['od_id']) ? safe_replace_regex($_REQUEST['od_id'], 'od_id') : '';
$od_status = isset($_REQUEST['od_status']) ? clean_xss_tags($_REQUEST['od_status'], 1, 1) : '';
$od_settle_case = isset($_REQUEST['od_settle_case']) ? clean_xss_tags($_REQUEST['od_settle_case'], 1, 1) : '';
$od_misu = isset($_REQUEST['od_misu']) ? clean_xss_tags($_REQUEST['od_misu'], 1, 1) : '';
$od_cancel_price = isset($_REQUEST['od_cancel_price']) ? clean_xss_tags($_REQUEST['od_cancel_price'], 1, 1) : '';
$od_refund_price = isset($_REQUEST['od_refund_price']) ? clean_xss_tags($_REQUEST['od_refund_price'], 1, 1) : '';
$od_receipt_point = isset($_REQUEST['od_receipt_point']) ? clean_xss_tags($_REQUEST['od_receipt_point'], 1, 1) : '';
$od_coupon = isset($_REQUEST['od_coupon']) ? clean_xss_tags($_REQUEST['od_coupon'], 1, 1) : '';
$fr_date = isset($_REQUEST['fr_date']) ? preg_replace('/[^0-9 :\-]/i', '', $_REQUEST['fr_date']) : '';
$to_date = isset($_REQUEST['to_date']) ? preg_replace('/[^0-9 :\-]/i', '', $_REQUEST['to_date']) : '';
$sort1 = isset($_REQUEST['sort1']) ? clean_xss_tags($_REQUEST['sort1'], 1, 1) : '';
$sort2 = isset($_REQUEST['sort2']) ? clean_xss_tags($_REQUEST['sort2'], 1, 1) : '';
$sel_field = isset($_REQUEST['sel_field']) ? clean_xss_tags($_REQUEST['sel_field'], 1, 1) : '';
$search = isset($_REQUEST['search']) ? get_search_string($_REQUEST['search']) : '';

$hp_rsvt_name	= isset($_POST['hp_rsvt_name']) ? clean_xss_tags(trim($_POST['hp_rsvt_name']), 1, 1) : '';// 이름
$hp_rsvt_tel	= isset($_POST['hp_rsvt_tel']) ? clean_xss_tags(trim($_POST['hp_rsvt_tel']), 1, 1) : '';// 연락처
$hp_rsvt_date	= isset($_POST['hp_rsvt_date']) ? clean_xss_tags(trim($_POST['hp_rsvt_date']), 1, 1) : '';// 예약일자
$hp_rsvt_stime	= isset($_POST['hp_rsvt_stime']) ? clean_xss_tags(trim($_POST['hp_rsvt_stime']), 1, 1) : '';// 예약시간
$hp_rsvt_etime	= isset($_POST['hp_rsvt_etime']) ? clean_xss_tags(trim($_POST['hp_rsvt_etime']), 1, 1) : '';// 예약시간
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

$sql = " select * from {$g5['g5_shop_health_profile_cart_table']} where hp_no = '{$hp_no}' ";
$row = sql_fetch($sql);
if(!$row)
	alert('처방정보가 존재하지 않습니다.');

$sql = " update {$g5['g5_shop_health_profile_cart_table']}
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
				hp_rsvt_name = '{$hp_rsvt_name}',
				hp_rsvt_tel = '{$hp_rsvt_tel}',
				hp_rsvt_date = '{$hp_rsvt_date}',
				hp_rsvt_stime = '{$hp_rsvt_stime}',
				hp_rsvt_etime = '{$hp_rsvt_etime}',
				hp_memo = '{$hp_memo}',
				hp_mdatetime = '".G5_TIME_YMDHIS."',
                    hp_1 = '{$hp_1}',
                    hp_2 = '{$hp_2}',
                    hp_3 = '{$hp_3}',
                    hp_4 = '{$hp_4}',
                    hp_5 = '{$hp_5}',
                    hp_6 = '{$hp_6}',
                    hp_7 = '{$hp_7}',
                    hp_8 = '{$hp_8}',
                    hp_9 = '{$hp_9}',
                    hp_10 = '{$hp_10}'                    
              where hp_no = '$hp_no' ";
sql_query($sql);

$qstr1 = "od_id=$od_id&amp;od_status=".urlencode($od_status)."&amp;od_settle_case=".urlencode($od_settle_case)."&amp;od_misu=$od_misu&amp;od_cancel_price=$od_cancel_price&amp;od_refund_price=$od_refund_price&amp;od_receipt_point=$od_receipt_point&amp;od_coupon=$od_coupon&amp;fr_date=$fr_date&amp;to_date=$to_date&amp;sel_field=$sel_field&amp;search=$search&amp;save_search=$search";
if($default['de_escrow_use'])
    $qstr1 .= "&amp;od_escrow=$od_escrow";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

goto_url("./orderform.php?$qstr");
?>