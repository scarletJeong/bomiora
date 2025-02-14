<?php
$sub_menu = '400400';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check_menu($auth, $sub_menu, "r");

$sql = " select a.it_id, a.it_name, b.* from {$g5['g5_shop_cart_table']} a, {$g5['g5_shop_health_profile_cart_table']} b
			where a.od_id = b.od_id
				and a.it_id = b.it_id
				and b.hp_no = '{$hp_no}' ";
$row = sql_fetch($sql);
if(!$row)
	alert('처방정보가 존재하지 않습니다.');

$answer_8	= explode('|', $row['answer_8']);// 식습관
$answer_9	= explode('|', $row['answer_9']);// 자주 먹는 음식
$answer_11	= explode('|', $row['answer_11']);// 질병
$answer_12	= explode('|', $row['answer_12']);// 복용중인 약

// 제품이미지
$image = get_it_image($row['it_id'], 50, 50);



$qstr1 = "od_id=$od_id&amp;od_status=".urlencode($od_status)."&amp;od_settle_case=".urlencode($od_settle_case)."&amp;od_misu=$od_misu&amp;od_cancel_price=$od_cancel_price&amp;od_refund_price=$od_refund_price&amp;od_receipt_point=$od_receipt_point&amp;od_coupon=$od_coupon&amp;fr_date=$fr_date&amp;to_date=$to_date&amp;sel_field=$sel_field&amp;search=$search&amp;save_search=$search";
if($default['de_escrow_use'])
    $qstr1 .= "&amp;od_escrow=$od_escrow";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

$g5['title'] = '처방정보';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');
?>

<form name="forderhealth" action="./orderform_health_update.php" onsubmit="return forderhealth_check(this)" method="post">
<input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
<input type="hidden" name="od_status" value="<?php echo urlencode($od_status); ?>">
<input type="hidden" name="od_settle_case" value="<?php echo urlencode($od_settle_case); ?>">
<input type="hidden" name="od_misu" value="<?php echo $od_misu; ?>">
<input type="hidden" name="od_cancel_price" value="<?php echo $od_cancel_price; ?>">
<input type="hidden" name="od_refund_price" value="<?php echo $od_refund_price; ?>">
<input type="hidden" name="od_receipt_point" value="<?php echo $od_receipt_point; ?>">
<input type="hidden" name="od_coupon" value="<?php echo $od_coupon; ?>">
<input type="hidden" name="fr_date" value="<?php echo $fr_date; ?>">
<input type="hidden" name="to_date" value="<?php echo $to_date; ?>">
<input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
<input type="hidden" name="search" value="<?php echo $search; ?>">
<input type="hidden" name="save_search" value="<?php echo $search; ?>">
<input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
<input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="hp_no" value="<?php echo $hp_no; ?>">
<input type="hidden" name="token" value="">
<section id="anc_scf_info">
    <h2 class="h2_frm">예약 정보</h2>
    <?php echo $pg_anchor; ?>
	<!--
    <div class="local_desc02 local_desc">
        <p>
        </p>
    </div>
	-->
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>예약정보 수정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">제품명</th>
            <td colspan="3">
                <?php echo stripslashes($row['it_name']); ?>
            </td>
        </tr>
		<tr>
            <th scope="row"><label for="hp_rsvt_name">이름</label></th>
            <td>
                <input type="text" name="hp_rsvt_name" value="<?php echo get_text($row['hp_rsvt_name']) ?>" id="hp_rsvt_name" class="frm_input" size="30">
            </td>
            <th scope="row"><label for="hp_rsvt_tel">연락처</label></th>
            <td>
                <input type="text" name="hp_rsvt_tel" value="<?php echo get_text($row['hp_rsvt_tel']) ?>" id="hp_rsvt_tel" class="frm_input" size="30">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="hp_rsvt_date">예약일자</label></th>
            <td>
                <input type="text" name="hp_rsvt_date" value="<?php echo get_text($row['hp_rsvt_date']) ?>" id="hp_rsvt_date" class="frm_input" size="10" maxlength="10">
            </td>
            <th scope="row"><label for="de_admin_company_fax">예약시간</label></th>
            <td>
				<select name="hp_rsvt_stime" id="hp_rsvt_stime">
					<?php foreach ($_const['start_time'] as $key => $val) { ?>
					<option value="<?php echo $key ?>" <?php echo get_selected($key, $row['hp_rsvt_stime']); ?>><?php echo $val ?></option>
					<?php } ?>
				</select> ~
				<select name="hp_rsvt_etime" id="hp_rsvt_etime">
					<?php foreach ($_const['end_time'] as $key => $val) { ?>
					<option value="<?php echo $key ?>" <?php echo get_selected($key, $row['hp_rsvt_etime']); ?>><?php echo $val ?></option>
					<?php } ?>
				</select>
            </td>
        </tr>        
        </tbody>
        </table>
    </div>
</section>
<section id="anc_scf_skin">
    <h2 class="h2_frm">프로필 정보</h2>
	<!--
    <div class="local_desc02 local_desc">
        <p></p>
    </div>
	-->
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>프로필 정보</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="hp_rsvt_name">생년월일</label></th>
            <td>
				<input type="text" name="answer_1" value="<?php echo get_text($row['answer_1']) ?>" id="answer_1" class="frm_input" size="10" minlength="8" maxlength="8" onkeydown="this.value=this.value.replace(/[^0-9]/g,'')" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onblur="this.value=this.value.replace(/[^0-9]/g,'')">
            </td>
            <th scope="row"><label for="hp_rsvt_tel">성별</label></th>
            <td>
				<?php foreach ($_const['sex'] as $key => $val) { ?>
				<input type="radio" name="answer_2" value="<?php echo $key ?>"  <?php echo get_checked($key, $row['answer_2'])?> id="answer_2_<?php echo $key ?>">
				<label for="answer_2_<?php echo $key ?>"><?php echo $val ?></label>
				<?php } ?>
            </td>
        </tr>
		<tr>
            <th scope="row"><label for="answer_3">목표강량체중</label></th>
            <td>
                <input type="text" name="answer_3" value="<?php echo get_text($row['answer_3']) ?>" id="answer_3" class="frm_input" maxlength="3" size="5" onkeydown="this.value=this.value.replace(/[^0-9]/g,'')" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onblur="this.value=this.value.replace(/[^0-9]/g,'')"> KG
            </td>
            <th scope="row"><label for="answer_4">키</label></th>
            <td>
                <input type="text" name="answer_4"  value="<?php echo get_text($row['answer_4']) ?>" id="answer_4" class="frm_input" maxlength="3" size="5" onkeydown="this.value=this.value.replace(/[^0-9]/g,'')" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onblur="this.value=this.value.replace(/[^0-9]/g,'')"> CM
            </td>
        </tr>
		<tr>
            <th scope="row"><label for="answer_5">몸무게</label></th>
            <td colspan="3">
                <input type="text" name="answer_5" value="<?php echo get_text($row['answer_5']) ?>" id="answer_5" class="frm_input" maxlength="3" size="5" onkeydown="this.value=this.value.replace(/[^0-9]/g,'')" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onblur="this.value=this.value.replace(/[^0-9]/g,'')"> KG
            </td>
        </tr>
		<tr>
            <th scope="row">다이어트 예상기간</th>
            <td colspan="3">
				<?php
				$number = 1;
				foreach ($_const['diet_period'] as $key => $val) {
				?>
				<input type="radio" name="answer_6" value="<?php echo $key ?>"  <?php echo get_checked($key, $row['answer_6'])?> id="answer_6_<?php echo $number ?>">
				<label for="answer_6_<?php echo $number ?>"><?php echo $val ?></label>
				<?php
					$number++;
				}
				?>
            </td>
        </tr>
		<tr>
            <th scope="row">하루끼니</th>
            <td colspan="3">
				<?php
				$number = 1;
				foreach ($_const['day_meal'] as $key => $val) {
				?>
				<input type="radio" name="answer_7" value="<?php echo $key ?>"  <?php echo get_checked($key, $row['answer_7'])?> id="answer_7_<?php echo $number ?>">
				<label for="answer_7_<?php echo $number ?>"><?php echo $val ?></label>
				<?php
					$number++;
				}
				?>
            </td>
        </tr>
		<tr>
            <th scope="row">식습관(중복선택가능)</th>
            <td colspan="3">
				<?php
				$number = 1;
				foreach ($_const['eating_habits'] as $key => $val) {
				?>
				<input type="checkbox" name="answer_8[]" value="<?php echo $key ?>" <?php echo (in_array($key, $answer_8) !== false) ? ' checked' : '' ?> id="answer_8_<?php echo $number ?>">
				<label for="answer_8_<?php echo $number ?>"><?php echo $val ?></label>
				<?php
					$number++;
				}
				?>
            </td>
        </tr>
		<tr>
            <th scope="row">자주 먹는 음식(중복선택가능)</th>
            <td colspan="3">
				<?php
				$number = 1;
				foreach ($_const['often_food'] as $key => $val) {
				?>
				<input type="checkbox" name="answer_9[]" value="<?php echo $key ?>" <?php echo (in_array($key, $answer_9) !== false) ? ' checked' : '' ?> id="answer_9_<?php echo $number ?>">
				<label for="answer_9_<?php echo $number ?>"><?php echo $val ?></label>
				<?php
					$number++;
				}
				?>
            </td>
        </tr>
		<tr>
            <th scope="row">운동습관</th>
            <td colspan="3">
				<?php
				$number = 1;
				foreach ($_const['exercise_habit'] as $key => $val) {
				?>
				<input type="radio" name="answer_10" value="<?php echo $key ?>"  <?php echo get_checked($key, $row['answer_10'])?> id="answer_10_<?php echo $number ?>">
				<label for="answer_10_<?php echo $number ?>"><?php echo $val ?></label>
				<?php
					$number++;
				}
				?>
            </td>
        </tr>
		<tr>
            <th scope="row">질병(중복선택가능)</th>
            <td colspan="3">
				<?php
				$number = 1;
				foreach ($_const['eisease'] as $key => $val) {
				?>
				<input type="checkbox" name="answer_11[]" value="<?php echo $key ?>" <?php echo (in_array($key, $answer_11) !== false) ? ' checked' : '' ?> id="answer_11_<?php echo $number ?>">
				<label for="answer_11_<?php echo $number ?>"><?php echo $val ?></label>
				<?php
					$number++;
				}
				?>
            </td>
        </tr>
		<tr>
            <th scope="row">복용중인 약(중복선택가능)</th>
            <td colspan="3">
				<?php
				$number = 1;
				foreach ($_const['medication_hand'] as $key => $val) {
				?>
				<input type="checkbox" name="answer_12[]" value="<?php echo $key ?>" <?php echo (in_array($key, $answer_12) !== false) ? ' checked' : '' ?> id="answer_12_<?php echo $number ?>">
				<label for="answer_12_<?php echo $number ?>"><?php echo $val ?></label>
				<?php
					$number++;
				}
				?>
            </td>
        </tr>
		<tr>
            <th scope="row">상담내용</th>
            <td colspan="3">
				<textarea name="hp_memo" id="hp_memo" rows="8"><?php echo html_purifier(stripslashes($row['hp_memo'])); ?></textarea>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>
<div class="btn_fixed_top">
    <a href="./orderform.php?<?php echo $qstr ?>" class="btn btn_02">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
</div>

</form>

<script>
$(function(){
	$("#hp_rsvt_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+7d", minDate: "0" });
});

function forderhealth_check(f)
{
	return true;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>