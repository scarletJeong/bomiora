<?php
if (!defined('_GNUBOARD_')) exit;

# 휴무일 종류
$HOLIDAY_SECTION = array(
		"0" => "매주 반복",
		"1" => "격주 반복",
		"2" => "3주 반복",
		"3" => "매월 반복",
		"99" => "매년 반복"
		);

# 휴무일 요일종류
$HOLIDAY_YOIL = array(
		"0" => "일요일",
		"1" => "월요일",
		"2" => "화요일",
		"3" => "수요일",
		"4" => "목요일",
		"5" => "금요일",
		"6" => "토요일"
		);
if(!function_exists(checkbox)) {
	function checkbox($data1,$data2,$db_data,$name,$rows=3) {
		$data1=explode("|",$data1);
		$data2=explode("|",$data2);
		for($i=0; $i < count($data1); $i++){$dataA[$i]=$data1[$i];}
		for($i=0; $i < count($data2); $i++){$dataB[$i]=$data2[$i];}
		$check=explode("|",$db_data);
		$result .="<ul>";
		$j=0;
		for($i=0; $i < count($data2); $i++)	{
			if($dataB[$i]==$check[$j]) {
				$checked="checked";
				$j++;
			} else {
				$checked="";
			}
			$result .= "<li><input type='checkbox' value='".$dataB[$i]."' name='{$name}[]' id='".$name."_".$i."' $checked class='".$name."'/>";
			$result .= "<label for='".$name."_".$i."'> {$dataA[$i]}</label></li>";
		}
		$result .="</ul>";
		return($result);
	}
}

if(!function_exists(option_int)) {
	function option_int($start,$end,$plus,$option_name) {
		for($i=$start; $i <= $end; $i+=$plus) {
			$selected=($option_name==$i)? "selected":"";
			$result .="<option value='$i' $selected>$i</option>";
		}
		return($result);
	}
}

if(!function_exists(option_str)) {
	function option_str($data1,$data2,$option_name='') {
		$data1=explode("|",$data1);
		$data2=explode("|",$data2);
		for($i=0; $i < count($data1); $i++) { $dataA[$i] = trim($data1[$i]); }
		for($i=0; $i < count($data2); $i++) { $dataB[$i] = trim($data2[$i]); }
		for($i=0; $i < count($data2); $i++) {
			$selected = ( $option_name == $dataB[$i] )? "selected":"";
			$result .="<option value='".$dataB[$i]."' ".$selected.">".$dataA[$i]."</option>";
		}
		return($result);
	}
}

if(!function_exists(radio)) {
	function radio($data1,$data2,$option_name,$name){
		$data1=explode("|",$data1);
		$data2=explode("|",$data2);
		$name2 = $name . "_tmp";
		for($i=0; $i < count($data1); $i++){$dataA[$i]=$data1[$i];}
		for($i=0; $i < count($data2); $i++){$dataB[$i]=$data2[$i];}
		//$result = "<input type=hidden name=$name2 value='$option_name'>";
		$result = "";
		for($i=0; $i < count($data2); $i++){
			$checked=($option_name==$dataB[$i])? "checked":"";
			$result .="<input type=radio name='$name' class='$name' id='${name}[$i]' value='$dataB[$i]' $checked style='border:0;cursor:pointer;'><label for='${name}[$i]' style='cursor:pointer;'>&nbsp;$dataA[$i]</label>&nbsp;&nbsp;";
		}
		return($result);
	}
}

// 정기휴무 배열
function booking_holiday_array($off_id) {
	//$sql = "select * from cm_holiday where mb_id = '".$off_id."' ";
	$sql = "select * from cm_holiday ";
	$result = sql_query($sql, true);
	$booking_holiday = array();
	while($row = sql_fetch_array($result)) {
		$booking_holiday[] = array(
			"section"	=> $row['section'],
			"yoil"		=> $row['yoil'],
			"initial"	=> $row['initial']
		);
	};
	return $booking_holiday;
}

// 토요일, 일요일 배경색 및 글자
function yoil_class($yoil) {

	switch($yoil) {
		case "0" :  //일요일
			$cls[] = "sunday";
			$cls[] = "sunday_td";
			break;
		case "6" : //토요일
			$cls[] = "sat";
			$cls[] = "sat_td";
			break;
		default:
			$cls[] = "";
			$cls[] = "";
			break;
	}
	return $cls;
}

// 정기휴일 체크
function closed_Day($Holiday, $var) {

	if(count($Holiday)==0) {
		// 휴무일을 지정하지 않아 배열이 없는 경우.
		$msg = "X";
	}

	$yoil = date('w', strtotime($var)); // 달력상의 요일 : 0(일요일) ~ 6(토요일)
	for($i=0; $i < count($Holiday); $i++) {

		$yo = $Holiday[$i]['yoil']; // 업체에서 지정한 휴무요일 : 0(일요일) ~ 6(토요일)
		switch($Holiday[$i]['section']) {

			case "0" : // 매주 특정요일 휴무
				if($yo == $yoil) {
					$msg = $yo;
				} else {
					$msg = "X";
				}
				break;

			case "1" : // 격주 특정요일 휴무
				$st = new DateTime($Holiday[$i]['initial']);
				$ed = new DateTime($var);
				$get_day  = date_diff($st, $ed);
				if($get_day->days%14==0) {
					$msg = $yo;
				} else {
					$msg = "X";
				}
				break;

			case "2" : // 3주마다 특정요일 휴무
				$st = new DateTime($Holiday[$i]['initial']);
				$ed = new DateTime($var);
				$get_day  = date_diff($st, $ed);

				// 21일마다 휴무
				if($get_day->days%21==0) {
					$msg = $yo;
				} else {
					$msg = "X";
				}
				break;

			case "3" : // 매월 특정일 휴무
				// 업체에서 지정한 매월 일짜와 오늘의 일짜를 비교하여 같으면 요일을 넘긴다.
				$ca_day = explode("-",$var);
				$co_day = explode("-",$Holiday[$i]['initial']);
				if($ca_day[2]==$co_day[2]) {
					$msg = $yoil;
				} else {
					$msg = "X";
				}
				break;
			case "99" : // 매년 특정일 휴무
				// 업체에서 지정한 매월 일짜와 오늘의 일짜를 비교하여 같으면 요일을 넘긴다.
				$ca_day = substr($var, 5);
				$co_day = substr($Holiday[$i]['initial'], 5);
				if($ca_day==$co_day) {
					$msg = $yoil;
				} else {
					$msg = "X";
				}
				break;
			default :
				$msg = "X";
				break;
		}
		if($msg!="X") {
			break;
		}
	}
	return $msg;
}


// 시간대별 예약시간을 라디오버튼으로 변경
function radio_time($data1,$data2,$option_name,$name) {

	global $g5, $write, $board, $office_no, $select, $time_person;

	// 시간대별 예약자 추출
	/*
	$sql = "select
				wr_2,
				count(wr_2) as cnt
			from
				".$g5['write_prefix'].$board['bo_table']."
			where
				wr_1 = '$select' and ca_name = '$office_no'
			group by
				wr_2 ";
	$result = sql_query($sql, true);
	*/

	$person=array();
	while($res=sql_fetch_array($result)) {
		$tr = $res['wr_2']."|".$res['cnt'];
		$person[$tr] = $res['wr_2'];  // 예약시간대 배열 생성.
	}

	$data1=explode("|",$data1);
	$data2=explode("|",$data2);
	$name2 = $name . "_tmp";

	for($i=0; $i < count($data1); $i++)	{
		$dataA[$i]=$data1[$i];
	}

	for($i=0; $i < count($data2); $i++)	{
		$dataB[$i]=$data2[$i];
	}

	//$result = "<input type=hidden name=$name2 value='$option_name'>";
	$result = "";

	for($i=0; $i < count($data2); $i++)	{

		// 10개씩 출력하고 줄바꿈.
		if($i%10==0) {
			//$result .= "<br/>";
		}

		$checked=($option_name==$dataB[$i])? "checked":"";

		// 시간대별 예약가능한 최대인원수에 해당하는 시간대별배열.
		$closed = array_search($dataB[$i],$person);
		$cnt = explode("|",$closed);

		// 시간대별 예약 최대 인원수에 도달한 시간일경우 선택 못하게 disabled 시킨다.
		if($cnt[0]==$dataB[$i] && $cnt[1] >=$time_person ) {
			$disabled = "disabled";
			$color  = "color:red";
		}

		$result .= "<label for=\"d_time_02".$i."\" class=\"oneline_five\">";
		$result .= "<input type=\"radio\" name=\"d_time\" value=\"".$dataA[$i]."\" id=\"d_time_02".$i."\">";
		$result .= "<span>".$dataA[$i]."</span>";
		$result .= "</label>";
		$result .= "";







		//$result .="<input type='radio' name='$name' id='${name}[$i]' class='{$name} hand' value='$dataB[$i]' $checked {$disabled}><label for='${name}[$i]' style='cursor:pointer;{$color}'>&nbsp;$dataA[$i] (".number_format($cnt[1]).")</label>&nbsp;&nbsp;&nbsp;";
		unset($disabled);
		unset($color);
	}
	return($result);
}


// 예약시간대별 배열생성 ( 1시간단위 및 30분단위 )
/*
function booking_time($bo_table, $select) {
	global $g5, $off, $start_time, $end_time, $time_gubun;

	$bt = array();

	for($i=$start_time; $i < $end_time; $i++) {
		$hour = sprintf("%02d",$i);
		$bt[] = $hour.":00";

		// 30분단위 예약추가
		if($time_gubun==1) {
			$bt[] .= $hour.":30";
		}
	}

	//예약시간 제외

	$sql = "select
				*
			from
				".$g5['write_prefix'].$bo_table."_sub
			where
				ex_kind = '2'
				and ex_date = '".$select."' ";
	$row = sql_fetch($sql, true);


	$except = explode("|", $row['ex_except']);

	for($i=0; $i < count($except); $i++) {
		$idx = array_search($except[$i], $bt); // 제외시간대상 위치(key) 찾기
		if($idx!==false){
			array_splice($bt, $idx, 1);
		}
	}

	$am = implode("|", $bt);
	return $am;
}
*/

function booking_time($bo_table, $select) {
	global $g5, $off, $start_time, $end_time, $time_gubun;
	global $default;
	//echo $default['de_rsvt_grelay_time'];

	$office_yoil = closed_Day(booking_holiday_array('skcompany'), $select); // 정기휴무일계산
	$temp_h = $holiday[$select]; // 임시휴무 (값이 1인 경우에만 임시휴무)

	$yoil = date('w', strtotime($select)); // 요일 번호 추출
	//$yoil = get_yoil($select);

	if($temp_h=="1") {
		return '';
	}

	if($office_yoil == $yoil) {
		return '';
	}

	switch($yoil) {
		case '1' :// 월
			list($starthour, $startminute) = explode(':', $default['de_rsvt_mon_stime']);
			list($endhour, $endminute) = explode(':', $default['de_rsvt_mon_etime']);
		break;
		case '2' :// 화
			list($starthour, $startminute) = explode(':', $default['de_rsvt_tue_stime']);
			list($endhour, $endminute) = explode(':', $default['de_rsvt_tue_etime']);
		break;
		case '3' :// 수
			list($starthour, $startminute) = explode(':', $default['de_rsvt_wed_stime']);
			list($endhour, $endminute) = explode(':', $default['de_rsvt_wed_etime']);
		break;
		case '4' :// 목
			list($starthour, $startminute) = explode(':', $default['de_rsvt_thu_stime']);
			list($endhour, $endminute) = explode(':', $default['de_rsvt_thu_etime']);
		break;
		case '5' :// 금
			list($starthour, $startminute) = explode(':', $default['de_rsvt_fri_stime']);
			list($endhour, $endminute) = explode(':', $default['de_rsvt_fri_etime']);
		break;
		case '6' :// 토
			list($starthour, $startminute) = explode(':', $default['de_rsvt_sat_stime']);
			list($endhour, $endminute) = explode(':', $default['de_rsvt_sat_etime']);
		break;
		case '0' :// 일
			list($starthour, $startminute) = explode(':', $default['de_rsvt_sun_stime']);
			list($endhour, $endminute) = explode(':', $default['de_rsvt_sun_etime']);
		break;
        default:
		break;
    }

	$bt = array();

	list($yy, $mm, $dd) = explode('-', $select); // - 로 잘라서 연,월,일을 구합니다

	$todate = new DateTime(date('Y-m-d'));
	$seldate = new DateTime($select);

	/*
	if ($todate == $seldate) {
		echo "날짜가 같습니다.";
	} else if ($todate > $seldate){
		echo "todate 날짜가 큽니다.";
	} else {
		echo "seldate 날짜가 큽니다.";
	}
	*/

	// 점심시간
	list($lunchstarthour, $lunchstartminute) = explode(':', $default['de_rsvt_lunch_stime']);
	list($lunchendhour, $lunchendminute) = explode(':', $default['de_rsvt_lunch_etime']);

	$grelayTm = $default['de_rsvt_grelay_time'];// 대기시간(분)




	//$iTodayFirstReserve = mktime($starthour, $startminute, 00, $mm, $dd, $yy);// 예약시작시간을 mktime 변경
	$iTodayEndReserve = mktime($endhour, $endminute, 00, $mm, $dd, $yy);// 예약종료시간을 mktime 변경
	$iTodayLunchStartReserve = mktime($lunchstarthour, $lunchstartminute, 00, $mm, $dd, $yy);// 점심시작시간을 mktime 변경
	$iTodayLunchEndReserve = mktime($lunchendhour, $lunchendminute, 00, $mm, $dd, $yy);// 점심종료시간을 mktime 변경
	$giTime = $grelayTm * 60;// 대기시간을 초단위로 변경
	//echo $giTime;


	if ($todate == $seldate) {// 날짜가 같을 경우는 시간 비교
		$iTodayFirstReserve = mktime($starthour, $startminute, 00, $mm, $dd, $yy);// 예약시작시간을 mktime 변경

		$pre_time = explode(":", date("H:i", strtotime("+30 minutes")));
		$pre_hour = $pre_time[0];
		$pre_min = $pre_time[1];

		//$iTodayFirstReserve2 = mktime(date('H'), date('i'), 00, $mm, $dd, $yy);// 예약시작시간을 mktime 변경
		$iTodayFirstReserve2 = mktime($pre_hour, $pre_min, 00, $mm, $dd, $yy);// 예약시작시간을 mktime 변경

		//echo $iTodayFirstReserve."<br>";
		//echo $iTodayFirstReserve2."<br>";
		if($iTodayFirstReserve2 > $iTodayFirstReserve) {
			if($pre_min > 30) {
				$iTodayFirstReserve = mktime($pre_hour, 30, 00, $mm, $dd, $yy);// 예약시작시간을 mktime 변경
			} else  {
				$iTodayFirstReserve = mktime($pre_hour, 00, 00, $mm, $dd, $yy);// 예약시작시간을 mktime 변경
			}
		}
	} else {
		$iTodayFirstReserve = mktime($starthour, $startminute, 00, $mm, $dd, $yy);// 예약시작시간을 mktime 변경
	}

	for ($i = $iTodayFirstReserve; $i < $iTodayEndReserve; $i += $giTime) {
		if($i >= $iTodayLunchStartReserve && $i < $iTodayLunchEndReserve) continue;
		$bt[] = date("H", $i).":".date("i", $i);
	}

	//예약시간 제외
	/*
	$sql = "select
				*
			from
				".$g5['write_prefix'].$bo_table."_sub
			where
				ex_kind = '2'
				and ex_date = '".$select."' ";
	$row = sql_fetch($sql, true);

	$except = explode("|", $row['ex_except']);
	*/

	//$except = explode("|", "11:00|11:30");

	for($i=0; $i < count($except); $i++) {
		$idx = array_search($except[$i], $bt); // 제외시간대상 위치(key) 찾기
		if($idx!==false){
			array_splice($bt, $idx, 1);
		}
	}

	$am = implode("|", $bt);
	return $am;
}





// 임시공휴일체크
function holiday_check($select) {
	global $g5, $bo_table, $office_no;
	$sql = "select
				count(*) as cnt
			from
				".$g5['write_prefix'].$bo_table."_sub
			where
				ex_kind = '1'
				and ex_date = '".$select."'
				and office_no = '".$office_no."' ";
	$rs = sql_fetch($sql, true);
	$msg = ($rs['cnt']>0)? "true":"false";
	return $msg;
}

// 임시공휴일 및 예약시간제외 배열
function holiday_array($fr_day, $to_day, $off_no) {
	/*
	global $write_table;
	$sql = "select
				*
			from
				".$write_table."_sub
			where
				office_no = '".$off_no."'
				and ex_date between '".$fr_day."'
				and '".$to_day."'
			order by
				ex_date asc";
	$result = sql_query($sql, true);
	while($row=sql_fetch_array($result)) {
		// 예약 일자 추출
		//$var = explode('-', $row['ex_date']);  // 임시휴무일자.
		//$open = (int)$var[2];   // "일"
		$open = $row['ex_date']; // 임시휴무일자
		$holiday[$open] = $row['ex_kind'];
	}
	*/
	return $holiday;
}

// 예약현황 배열
function booking_array($fr_day, $to_day, $off_no) {
	/*
	global $write_table;
	// 일자별 및 예약자 배열생성 (예 : $booking['일자']['예약자정보'])
	$sql = "SELECT
				*
			FROM
				".$write_table."
			WHERE
				wr_is_comment = 0
				and ca_name = '".$off_no."'
				and wr_1 between '".$fr_day."'
				and '".$to_day."'
			order by
				wr_2 asc";
	$result = sql_query($sql, true);
	$booking = array();
	while($row = sql_fetch_array($result)) {

		//$var = explode('-', $row['wr_1']);  // 고객 예약일
		//$open = (int)$var[2];   // "일"
		$open = $row['wr_1'];

		// 각 날자별로 배열로 담는다.(예약자이름|예약글고유번호|예약시간|예약상태)
		$booking[$open][] = $row['wr_name']."|".$row['wr_id']."|".$row['wr_2']."|".$row['wr_3'];
	}
	*/
	return $booking;
}

function checkbox_booking($data1,$data2,$db_data,$name,$id_v,$rows=3) {
	$data1=explode("|",$data1);
	$data2=explode("|",$data2);
	for($i=0; $i < count($data1); $i++){$dataA[$i]=$data1[$i];}
	for($i=0; $i < count($data2); $i++){$dataB[$i]=$data2[$i];}
	$check=explode("|",$db_data);

	$wr_five = array();
	for($i=0;$i<count($check);$i++) {
		$wr_five[] = $check[$i];
	}

	$result .="<ul>";
	for($i=0; $i < count($data2); $i++) {
		$db = array_search($dataB[$i],$wr_five);

		if($dataB[$i]==$wr_five[$db]) {
			$checked="checked";
			$span="color:#FF0080;";

		} else {
			$checked="";
			$span="";
		}
		$result .= "<li>";
		$result .= "<input type='checkbox' value='$dataB[$i]' name='${name}' $checked class='hand' id='${id_v}[$i]'/>";
		$result .= "<label for='${id_v}[$i]' style='{$span}'> {$dataA[$i]}</label>";
		$result .= "</li>";
	}
	$result .="</ul>";
	return($result);
}

function day_info($var, $day, $min, $max) {

	global $off, $member, $booking, $is_admin, $holiday, $bo_table, $board_skin_url;

	list($year, $month, $date) = explode('-', $var); // 날짜 년-월-일
	$s = '01'; // 월의 시작 날짜 (모든 월의 시작일은 1일)
	$e = date('t', strtotime($var . ' 00:00:00')); // 월의 마지막 날짜

	$fr_day = $year."-".$month."-".$s; // 선택한달의 시작일
	$to_day = $year."-".$month."-".$e; // 선택한달의 마지막일

	$yoil = date('w', strtotime($var)); // 요일 번호 추출

	//list($y, $bg) = yoil_class($yoil); // 요일별 글자색상과 배경색에 관련된 class 명
	//$office_yoil = closed_Day(booking_holiday_array($off['mb_id']), $var); // 정기휴무일계산
	$office_yoil = closed_Day(booking_holiday_array('skcompany'), $var); // 정기휴무일계산
	//$holiday = holiday_array($fr_day, $to_day, $off['id_no']); // 임시휴무일
	//$booking = booking_array($fr_day, $to_day, $off['id_no']); // 예약현황 배열
	//$holiday = holiday_array($fr_day, $to_day, 1); // 임시휴무일
	$booking = booking_array($fr_day, $to_day, 1); // 예약현황 배열
	$booking_check = close_check($bo_table, $var, count($booking[$var]));
	$temp_h = $holiday[$var]; // 임시휴무 (값이 1인 경우에만 임시휴무)

	//echo "예약인원:".$booking_check;


	$clock_ico =($temp_h=="2")? "<i class='fa fa-clock-o txt_red' aria-hidden='true'></i>":"";//예약시간제외
	$except_url = ($is_admin || $member['mb_id']==$off['mb_id'])? "onclick=\"except_pop('".$var."', '".$min."', '".$max."', '".$off['id_no']."', '".$bo_table."');\" style=\"cursor:pointer;\"":"";


	$rst = '';
	$day_str = date('m/d', strtotime("+{$day} days", G5_SERVER_TIME)).'('.get_yoil($var).')';

		if($var == G5_TIME_YMD) {
			$checked = ' checked';
		}

	if($var < $min || $var > $max || $office_yoil == $yoil) {
		//$rst =  ($var < $min)? "<li class='gray'>".(int)$day."일 ".$clock_ico."</li>":"<li class='li_day {$y}' ".$except_url.">".(int)$day."일 ".$clock_ico."</li>";// 예약 가능일 이전과 이후 글자색상
		if($temp_h=="1") {
			//$day_str .= ' 임시휴무';
			$day_str .= ' 휴진';
			$disabled = 'disabled';
		}

		if($office_yoil == $yoil) {
			//$day_str .= ' 정기휴무';
			$day_str .= ' 휴진';
			$disabled = 'disabled';
		}

		$rst .= "<label for=\"d_day_0".$day."\" class=\"oneline_five".$off_day_class."\">";
		$rst .= "<input type=\"radio\" name=\"d_day\" value=\"".$var."\" id=\"d_day_0".$day."\" ".$disabled." ".$checked.">";
		$rst .= "<span>".$day_str."</span>";
		$rst .= "</label>";


	} else {

		//$link = G5_BBS_URL."/write.php?bo_table={$bo_table}&amp;select={$var}&office_no=".$off['id_no'];
		//$rst .=  "<li class='li_day txt_green {$y}' ".$holiday_title." ".$except_url.">".(int)$day."일 ".$clock_ico."</li>";

		// 임시휴무 - 값이 1이면 임시휴무( g5_write_booking.ex_kind 의 값)
		if($temp_h=="1") {
			$day_str .= ' 임시휴무';
			$disabled = 'disabled';
		} else {
			if($booking_check==true) {
				//$rst .=  "<li class='li_btn'><a href='{$link}'> 예약가능</a></li>";
			} else {
				$day_str .= ' 예약마감';
				$disabled = 'disabled';
				//$rst .=  "<li class='li_closed2 txt_gray'> 예약마감</li>";
			}
		}

		$rst .= "<label for=\"d_day_0".$day."\" class=\"oneline_five".$off_day_class."\">";
		$rst .= "<input type=\"radio\" name=\"d_day\" value=\"".$var."\" id=\"d_day_0".$day."\" ".$disabled." ".$checked.">";
		$rst .= "<span>".$day_str."</span>";
		$rst .= "</label>";
	}

	if($temp_h=="1") { unset($booking); } // 임시휴무이면 예약자리스트를 초기화하여 화면에 노출을 하지 않는다.
	$boo = '';
	for($k = 0; $k < count($booking[$var]); $k++) {

		list($na,$wr,$ti,) = explode("|",$booking[$var][$k]); //(예약자이름|예약글고유번호|예약시간|예약상태)

		if($member['mb_level'] >= '5') {
			$popup_url = $board_skin_url."/booking_popup.php?bo_table=".$bo_table."&office_no=".$off['id_no']."&wr_id=".$wr;
			$link2 = "javascript:booking_view(\"{$popup_url}\")";
		} else {
			$link2 = G5_BBS_URL."/board.php?bo_table=".$bo_table."&office_no=".$off['id_no']."&wr_id=".$wr."&".$qstr;
		}

		$boo .= "<li class='member_list'><a href='".$link2."'><i class='fa fa-volume-up' aria-hidden='true'></i> ".$ti."&nbsp;".$na."</a></li>";
	}

	$arr[] = $rst; // 임시휴무 및 예약시간제외설정값
	$arr[] = $boo; // 예약자 리스트

	return $arr;
}

// 예약시간대별 예약이 모두 마감인지 체크.
function close_check($bo_table, $select, $booking_cnt) {
	global $office, $time_person, $default;
	$time_person = 15;
	//$time_person = $default['de_rsvt_limit_person'];

	$booking_time = explode("|", booking_time($bo_table, $select));
	$total_person =  count($booking_time)*$time_person;  // 1일 예약 가능한 총인원.

	if($booking_cnt >= $total_person) {
		$msg = false;
	} else {
		$msg = true;
	}

	return $msg;
}
?>
