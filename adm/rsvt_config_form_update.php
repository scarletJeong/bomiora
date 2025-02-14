<?php
$sub_menu = '400100';
include_once('./_common.php');

check_demo();

auth_check_menu($auth, $sub_menu, "w");

//check_admin_token();

$check_keys = array(
    'de_rsvt_mon_stime' => 'char',
	'de_rsvt_mon_etime' => 'char',
	'de_rsvt_tue_stime' => 'char',
	'de_rsvt_tue_etime' => 'char',
	'de_rsvt_wed_stime' => 'char',
	'de_rsvt_wed_etime' => 'char',
	'de_rsvt_thu_stime' => 'char',
	'de_rsvt_thu_etime' => 'char',
	'de_rsvt_fri_stime' => 'char',
	'de_rsvt_fri_etime' => 'char',
	'de_rsvt_sat_stime' => 'char',
	'de_rsvt_sat_etime' => 'char',
	'de_rsvt_sun_stime' => 'char',
	'de_rsvt_sun_etime' => 'char',
	'de_rsvt_lunch_stime' => 'char',
	'de_rsvt_lunch_etime' => 'char',
	'de_rsvt_holiday_stime' => 'char',
	'de_rsvt_holiday_etime' => 'char',
	'de_rsvt_grelay_time' => 'int',
	'de_rsvt_limit_person' => 'int',
	'de_rsvt_mon_act' => 'int',
	'de_rsvt_tue_act' => 'int',
	'de_rsvt_wed_act' => 'int',
	'de_rsvt_thu_act' => 'int',
	'de_rsvt_fri_act' => 'int',
	'de_rsvt_sat_act' => 'int',
	'de_rsvt_sun_act' => 'int',
	'de_rsvt_holiday_act' => 'int'
);

foreach ($check_keys as $k => $v) {
    if ($v === 'int') {
        $posts[$key] = $_POST[$k] = isset($_POST[$k]) ? (int) $_POST[$k] : 0;
    } else {
		$posts[$key] = $_POST[$k] = isset($_POST[$k]) ? strip_tags(clean_xss_attributes($_POST[$k])) : '';
    }
}

$sql = " update {$g5['g5_shop_default_table']}
            set de_rsvt_mon_stime		= '{$de_rsvt_mon_stime}',
                de_rsvt_mon_etime		= '{$de_rsvt_mon_etime}',
                de_rsvt_tue_stime		= '{$de_rsvt_tue_stime}',
                de_rsvt_tue_etime		= '{$de_rsvt_tue_etime}',
                de_rsvt_wed_stime		= '{$de_rsvt_wed_stime}',
                de_rsvt_wed_etime		= '{$de_rsvt_wed_etime}',
                de_rsvt_thu_stime		= '{$de_rsvt_thu_stime}',
                de_rsvt_thu_etime		= '{$de_rsvt_thu_etime}',
                de_rsvt_fri_stime		= '{$de_rsvt_fri_stime}',
                de_rsvt_fri_etime		= '{$de_rsvt_fri_etime}',
                de_rsvt_sat_stime		= '{$de_rsvt_sat_stime}',
                de_rsvt_sat_etime		= '{$de_rsvt_sat_etime}',
				de_rsvt_sun_etime		= '{$de_rsvt_sun_etime}',
				de_rsvt_lunch_stime		= '{$de_rsvt_lunch_stime}',
				de_rsvt_lunch_etime		= '{$de_rsvt_lunch_etime}',
				de_rsvt_holiday_stime	= '{$de_rsvt_holiday_stime}',
				de_rsvt_holiday_etime	= '{$de_rsvt_holiday_etime}',
				de_rsvt_grelay_time		= '30',
				de_rsvt_limit_person	= '{$de_rsvt_limit_person}',
				de_rsvt_mon_act			= '{$de_rsvt_mon_act}',
				de_rsvt_tue_act			= '{$de_rsvt_tue_act}',
				de_rsvt_wed_act			= '{$de_rsvt_wed_act}',
				de_rsvt_thu_act			= '{$de_rsvt_thu_act}',
				de_rsvt_fri_act			= '{$de_rsvt_fri_act}',
				de_rsvt_sat_act			= '{$de_rsvt_sat_act}',
				de_rsvt_sun_act			= '{$de_rsvt_sun_act}',
				de_rsvt_holiday_act		= '{$de_rsvt_holiday_act}' ";
sql_query($sql);

// 정기 휴무. 일단 전체 삭제 후 체크된것만 다시 인서트
sql_query("delete from cm_holiday ", true);

if(!$de_rsvt_mon_act) {// 월요일 활성화
	$sql = "insert into cm_holiday
				set mb_id = '".$member['mb_id']."',
				section	= '0',
				yoil = '1',
				initial	= '".G5_TIME_YMD."' ";
	sql_query($sql, true);
}

if(!$de_rsvt_tue_act) {// 화요일 활성화
	$sql = "insert into cm_holiday
				set mb_id = '".$member['mb_id']."',
				section	= '0',
				yoil = '2',
				initial	= '".G5_TIME_YMD."' ";
	sql_query($sql, true);
}

if(!$de_rsvt_wed_act) {// 수요일 활성화
	$sql = "insert into cm_holiday
				set mb_id = '".$member['mb_id']."',
				section	= '0',
				yoil = '3',
				initial	= '".G5_TIME_YMD."' ";
	sql_query($sql, true);
}

if(!$de_rsvt_thu_act) {// 목요일 활성화
	$sql = "insert into cm_holiday
				set mb_id = '".$member['mb_id']."',
				section	= '0',
				yoil = '4',
				initial	= '".G5_TIME_YMD."' ";
	sql_query($sql, true);
}

if(!$de_rsvt_fri_act) {// 금요일 활성화
	$sql = "insert into cm_holiday
				set mb_id = '".$member['mb_id']."',
				section	= '0',
				yoil = '5',
				initial	= '".G5_TIME_YMD."' ";
	sql_query($sql, true);
}

if(!$de_rsvt_sat_act) {// 토요일 활성화
	$sql = "insert into cm_holiday
				set mb_id = '".$member['mb_id']."',
				section	= '0',
				yoil = '6',
				initial	= '".G5_TIME_YMD."' ";
	sql_query($sql, true);
}

if(!$de_rsvt_sun_act) {// 일요일 활성화
	$sql = "insert into cm_holiday
				set mb_id = '".$member['mb_id']."',
				section	= '0',
				yoil = '0',
				initial	= '".G5_TIME_YMD."' ";
	sql_query($sql, true);
}


goto_url("./rsvt_config_form.php");
?>