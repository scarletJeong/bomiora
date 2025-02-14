<?php
$sub_menu = '800100';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = '진료하기';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$fr_date = (isset($_GET['fr_date']) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['fr_date'])) ? $_GET['fr_date'] : '';//진료예약일자
$to_date = (isset($_GET['to_date']) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['to_date'])) ? $_GET['to_date'] : '';//진료예약일자
$sel_name = isset($_GET['sel_name']) ? get_search_string($_GET['sel_name']) : '';// 이름
$sel_sex = (isset($_GET['sel_sex']) && in_array($_GET['sel_sex'], $_const['sex'])) ? $_GET['sel_sex'] : '';
$sel_prescription = (isset($_GET['sel_prescription']) && in_array($_GET['sel_prescription'], $_const['prescription'])) ? $_GET['sel_prescription'] : '';
//$od_escrow = isset($_GET['od_escrow']) ? clean_xss_tags($_GET['od_escrow'], 1, 1) : '';

$where = " and ";
$sql_search = "";

if ($fr_date && $to_date) {
   $sql_search .= " $where hp_rsvt_date between '$fr_date' and '$to_date' ";
}

if ($sel_name != "") {
	$sql_search .= " $where hp_rsvt_name like '%$sel_name%' ";
}

if ($sel_sex != "") {
	$sql_search .= " $where answer_2 = '$sel_sex' ";
}

if ($sel_prescription != "") {
	$sql_search .= " $where hp_9 = '$sel_prescription' ";
}


//$sql_common = " from {$g5['g5_shop_health_profile_cart_table']} where (hp_status <> '쇼핑' and hp_status <> '취소') and hp_output = 'Y' ";
$sql_common = " from {$g5['g5_shop_health_profile_cart_table']} where hp_status = '입금' and hp_output = 'Y' ";
$sql_common .= $sql_search;

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

//$rows = $config['cf_page_rows'];
$rows = 50;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if (!$sst) {
	//$sst = "hp_9";
	//$sod = "asc";
	//$sql_order = "order by hp_rsvt_date desc, hp_rsvt_stime asc, hp_9 asc";
	$sql_order = "order by hp_9 asc, hp_rsvt_date desc, hp_rsvt_stime asc";
} else {
	$sql_order = " order by {$sst} {$sod} ";

}

$sql  = " select * $sql_common $sql_order limit $from_record, $rows ";
$result = sql_query($sql);
//echo $sql;
//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr  = $qstr.'&amp;sel_name='.$sel_name.'&amp;sel_sex='.$sel_sex.'&amp;sel_prescription='.$sel_prescription.'&amp;page='.$page;

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    <span class="btn_ov01"><span class="ov_txt">전체 진료내역</span><span class="ov_num"> <?php echo number_format($total_count); ?>건</span></span>
</div>

<form class="local_sch03 local_sch">
<div class="sch_last">
    <strong>예약일자</strong>
    <input type="text" id="fr_date"  name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input" size="10" maxlength="10"> ~
    <input type="text" id="to_date"  name="to_date" value="<?php echo $to_date; ?>" class="frm_input" size="10" maxlength="10">
    <button type="button" onclick="javascript:set_date('오늘');">오늘</button>
    <button type="button" onclick="javascript:set_date('어제');">어제</button>
    <button type="button" onclick="javascript:set_date('이번주');">이번주</button>
    <button type="button" onclick="javascript:set_date('이번달');">이번달</button>
    <button type="button" onclick="javascript:set_date('지난주');">지난주</button>
    <button type="button" onclick="javascript:set_date('지난달');">지난달</button>
    <button type="button" onclick="javascript:set_date('전체');">전체</button>
    <input type="submit" value="검색" class="btn_submit">
</div>
<div>

    <strong>이름</strong>
	<label for="sel_name" class="sound_only">이름</label>
	<input type="text" name="sel_name" value="<?php echo $sel_name; ?>" id="sel_name" class="frm_input">
</div>
<div>
    <strong>성별</strong>
	<label for="sel_sex" class="sound_only">성별</label>
	<select name="sel_sex" id="sel_sex">
		<?php foreach ($_const['sex'] as $key => $val) { ?>
		<option value="<?php echo $key ?>" <?php echo get_selected($key, $sel_sex); ?>><?php echo $val ?></option>
		<?php } ?>
	</select>
</div>
<div>
    <strong>처방여부</strong>
    <label for="sel_prescription" class="sound_only">처방여부</label>
	<select name="sel_prescription" id="sel_prescription">
		<?php foreach ($_const['prescription'] as $key => $val) { ?>
		<option value="<?php echo $key ?>" <?php echo get_selected($key, $sel_prescription); ?>><?php echo $val ?></option>
		<?php } ?>
	</select>
</div>
</form>

<form name="fdiagnosis" id="fdiagnosis" onsubmit="return fdiagnosis_submit(this);" method="post" autocomplete="off">
<input type="hidden" name="sel_name" value="<?php echo $sel_name ?>">
<input type="hidden" name="sel_sex" value="<?php echo $sel_sex ?>">
<input type="hidden" name="sel_prescription" value="<?php echo $sel_prescription ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<div class="tbl_head01 tbl_wrap adm_mo_layout">
    <table id="sodr_list">
    <caption>예약 내역 목록</caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only">예약 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
		<th scope="col">제품명</th>
		<th scope="col" class="adm_mo_w">이름</th>
		<th scope="col">성별</th>
		<th scope="col">연락처</th>
		<!--
		<th scope="col">키</th>
		<th scope="col">몸무게</th>
		<th scope="col">목표감량체중</th>
		<th scope="col" class="adm_mo_none">다이어트<br>예상기간</th>
		-->
		<th scope="col"><?php echo subject_sort_link('hp_rsvt_date', 'sel_name='.$sel_name.'&sel_sex='.$sel_sex.'&sel_prescription='.$sel_prescription); ?>예약일자</a></th>
		<th scope="col">예약시간</th>
		<th scope="col"><?php echo subject_sort_link('hp_8', 'sel_name='.$sel_name.'&sel_sex='.$sel_sex.'&sel_prescription='.$sel_prescription); ?>초진/재진</a></th>
		<th scope="col" class="adm_mo_w"><?php echo subject_sort_link('hp_9', 'sel_name='.$sel_name.'&sel_sex='.$sel_sex.'&sel_prescription='.$sel_prescription); ?>처방여부</a></th>
		<th scope="col">메모</th>
		<!-- <th scope="col"><?php echo subject_sort_link('hp_10', 'sel_name='.$sel_name.'&sel_sex='.$sel_sex.'&sel_prescription='.$sel_prescription); ?>진료완료여부</a></th> -->
		<!-- <th scope="col"><?php echo subject_sort_link('hp_status', 'sel_name='.$sel_name.'&sel_sex='.$sel_sex.'&sel_prescription='.$sel_prescription); ?>결제여부</a></th> -->
		<th scope="col">상세내용</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
		// 장바구니 제품
		$sql2 = " select it_name, od_id from {$g5['g5_shop_cart_table']} where od_id = '{$row['od_id']}' and it_id = '{$row['it_id']}' ";
		//echo $sql2."<br>";
		$row2 = sql_fetch($sql2);

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <input type="hidden" name="hp_no[<?php echo $i ?>]" value="<?php echo $row['hp_no'] ?>" id="hp_no_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only">일련번호 <?php echo $row['hp_no']; ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
		<td class="adm_mo_w"><?php echo stripslashes($row2['it_name']); ?><br>
			<?php
			$sql = " select * from {$g5['g5_shop_order_table']} where od_id = '{$row['od_id']}' ";
			$od = sql_fetch($sql);

			// 제품의 옵션정보
			$sql = " select ct_id, it_id, ct_price, ct_point, ct_qty, ct_option, ct_status, cp_price, ct_stock_use, ct_point_use, ct_send_cost, io_type, io_price
						from {$g5['g5_shop_cart_table']}
						where od_id = '{$od['od_id']}'
							and it_id = '{$row['it_id']}'
						order by io_type asc, ct_id asc ";
			//echo $sql."<br>";
			$res = sql_query($sql);
			$rowspan = sql_num_rows($res);
			for($k=0; $opt=sql_fetch_array($res); $k++) {
				if($opt['io_type'])
					$opt_price = $opt['io_price'];
				else
					$opt_price = $opt['ct_price'] + $opt['io_price'];
				// 소계
				$ct_price['stotal'] = $opt_price * $opt['ct_qty'];
				$ct_point['stotal'] = $opt['ct_point'] * $opt['ct_qty'];
			?>
			<?php echo get_text($opt['ct_option']); ?><br>
			<?php } ?>
			<?php echo $od['od_receipt_time'] ?><br>
			<?php echo $row2['od_id'] ?>

		</td>
		<td><?php echo get_text($row['hp_rsvt_name'] ? $row['hp_rsvt_name'] : $od['od_name']) ?><br>(<?php echo get_text($od['od_b_name']) ?>)</td>
		<td><?php echo $_const['sex'][$row['answer_2']] ?></td>
		<td><?php echo get_text($row['hp_rsvt_tel'] ? $row['hp_rsvt_tel'] : $od['od_b_hp']); ?></td>
		<!--
		<td><?php echo number_format($row['answer_4']) ?>CM</td>
		<td><?php echo number_format($row['answer_5']) ?>KG</td>
		<td><?php echo number_format($row['answer_3']) ?>KG</td>
		<td class="adm_mo_none"><?php echo $_const['diet_period'][$row['answer_6']] ?></td>
		-->
		<td><?php echo date("Y.m.d", strtotime($row['hp_rsvt_date'])) ?> <?php echo get_yoil($row['hp_rsvt_date']) ?></td>
		<td><?php echo get_text($row['hp_rsvt_stime']) ?> ~ <?php echo get_text($row['hp_rsvt_etime']) ?></td>
		<td class="adm_mo_w">
			<label for="hp_8_<?php echo $i; ?>" class="sound_only">초진/재진</label>
			<select name="hp_8[<?php echo $i; ?>]" id="hp_8_<?php echo $i; ?>">
				<?php foreach ($_const['treatment'] as $key => $val) { ?>
				<option value="<?php echo $key ?>" <?php echo get_selected($key, $row['hp_8']); ?>><?php echo $val ?></option>
			<?php } ?>
			</select>
		</td>
		<td class="adm_mo_w">
			<label for="hp_9_<?php echo $i; ?>" class="sound_only">처방여부</label>
			<select name="hp_9[<?php echo $i; ?>]" id="hp_9_<?php echo $i; ?>">
				<?php foreach ($_const['prescription'] as $key => $val) { ?>
				<option value="<?php echo $key ?>" <?php echo get_selected($key, $row['hp_9']); ?>><?php echo $val ?></option>
			<?php } ?>
			</select>
		</td>
		<td><textarea name="hp_memo[<?php echo $i ?>]" id="hp_memo_<?php echo $i ?>" style="height:70px;"><?php echo $row['hp_memo'] ?></textarea></td>
		<!-- <td class="adm_mo_w">
			<label for="hp_10_<?php echo $i; ?>" class="sound_only">진행여부</label>
			<select name="hp_10[<?php echo $i; ?>]" id="hp_10_<?php echo $i; ?>">
				<?php foreach ($_const['progress'] as $key => $val) { ?>
				<option value="<?php echo $key ?>" <?php echo get_selected($key, $row['hp_10']); ?>><?php echo $val ?></option>
			<?php } ?>
			</select>
		</td> -->
		<!-- <td><?php echo ($row['hp_status'] == '입금') ? '완료' : get_text($row['hp_status']); ?></td> -->
		<td class="td_mng td_mns_m">
			<!--<a href="javascript:void(0)" data-no="<?php echo $row['hp_no'] ?>" class="orderitem btn btn_03">보기</a>-->
			<a href="javascript:void(0);" class="sbn_profile_view btn btn_03">보기</a>
		</td>
	</tr>

	<tr class="td_profile_view" style="display:none;">
		<td colspan="16">
			<?php
			$answer_8_str = str_replace('|', ', ', $row['answer_8']);// 식습관
			$answer_9_str = str_replace('|', ', ', $row['answer_9']);// 자주 먹는 음식
			$answer_11_str = str_replace('|', ', ', $row['answer_11']);// 질병:
			$answer_12_str = str_replace('|', ', ', $row['answer_12']);// 복용중인 약
			/*
			$answer_8_str = '';
			$answer_8 = explode(',', $row['answer_8']);// 식습관
			foreach ($answer_8 as $key => $val) {
				$answer_8_arr[] = $_const['eating_habits'][$val];
			}
			$answer_8_str = implode(', ', $answer_8_arr);
			*/
			?>
			<div class="diagnosis_main">
				<div>
					<h3>요약</h3>
					<div class="diagnosis_main_list">
						<span class="requisite">전화번호: <?php echo get_text($row['hp_rsvt_tel']) ?></span>
						<span class="requisite">생년월일: <?php echo get_text($row['answer_1']) ?></span>
						<span >성별: <?php echo $_const['sex'][$row['answer_2']] ?></span>
						<span >키: <?php echo number_format($row['answer_4']) ?>cm</span>
						<span >몸무게: <?php echo number_format($row['answer_5']) ?>kg</span>
						<span >목표감량체중: <?php echo number_format($row['answer_3']) ?>kg</span>
						<span >다이어트예상기간: <?php echo $_const['diet_period'][$row['answer_6']] ?></span>
						<span >하루끼니: <?php echo $_const['day_meal'][$row['answer_7']] ?></span>
						<span >식습관: <?php echo $answer_8_str ?></span>
						<span >자주 먹는 음식: <?php echo $answer_9_str ?></span>
						<span >운동습관: <?php echo $_const['exercise_habit'][$row['answer_10']] ?></span>
						<span >질병: <?php echo $answer_11_str ?></span>
						<span >복용중인 약: <?php echo $answer_12_str ?></span>
					</div>
				</div>
				<div class="diagnosis_address">
					<h3>주소</h3>
					<p>우편번호: <?php echo get_text($od['od_b_zip1']).get_text($od['od_b_zip2']); ?></p>
					<p>
					기본주소: <?php echo get_text($od['od_b_addr1']); ?>
					</p>
					<p>
					상세주소: <?php echo get_text($od['od_b_addr2']); ?>
					</p>
					<p class="bold">
					배송메모: <?php if ($od['od_memo']) echo get_text($od['od_memo'], 1);else echo "없음";?>
					</p>
				</div>
			</div>
		</td>
    </tr>
    <?php
    }
    sql_free_result($result);
    if ($i == 0)
        echo '<tr><td colspan="16" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>


<div class="btn_fixed_top">
	<input type="submit" name="act_button" value="처방하기" onclick="document.pressed=this.value" class="btn btn_01">
	<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

	$(".sbn_profile_view").on("click", function() {
		//$(this).closest("tr").next(".td_profile_view").css('display','');
		$(this).closest("tr").next(".td_profile_view").slideToggle();
    });
});

function set_date(today)
{
    <?php
    $date_term = date('w', G5_SERVER_TIME);
    $week_term = $date_term + 7;
    $last_term = strtotime(date('Y-m-01', G5_SERVER_TIME));
    ?>
    if (today == "오늘") {
        document.getElementById("fr_date").value = "<?php echo G5_TIME_YMD; ?>";
        document.getElementById("to_date").value = "<?php echo G5_TIME_YMD; ?>";
    } else if (today == "어제") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
    } else if (today == "이번주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$date_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "이번달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', G5_SERVER_TIME); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "지난주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$week_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', strtotime('-'.($week_term - 6).' days', G5_SERVER_TIME)); ?>";
    } else if (today == "지난달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', strtotime('-1 Month', $last_term)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-t', strtotime('-1 Month', $last_term)); ?>";
    } else if (today == "전체") {
        document.getElementById("fr_date").value = "";
        document.getElementById("to_date").value = "";
    }
}
</script>

<script>
function fdiagnosis_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }
	/*
	if (!confirm("선택하신 진료에 대한 처방상태를 변경하시겠습니까?"))
        return false;
	*/
	if (document.pressed == "선택삭제") {
		if (!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
			return false;
		}
	}

    f.action = "./diagnosis_list_update.php";
    return true;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');