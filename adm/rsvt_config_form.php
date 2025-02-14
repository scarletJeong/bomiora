<?php
$sub_menu = "800200";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.');
}

$copy_config = get_config(true);

$g5['title'] = '예약시간설정';
require_once './admin.head.php';
?>

<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
    <input type="hidden" name="token" value="" id="token">
<input type="submit" value="확인" class="btn_submit btn" accesskey="s">
    <section id="anc_cf_basic">
        <h2 class="h2_frm">진료예약 설정</h2>
        <?php echo $pg_anchor ?>

        <div class="tbl_frm01 tbl_wrap">
            <table>
                <caption>진료예약 설정</caption>
                <colgroup>
                    <col class="grid_4">
                    <col>
                </colgroup>
                <tbody>
					<tr>
                        <th scope="row"><label for="de_rsvt_limit_person">허용가능숫자<strong class="sound_only">필수</strong></label></th>
                        <td><input type="text" name="de_rsvt_limit_person" value="<?php echo $default['de_rsvt_limit_person'] ?>" id="de_rsvt_limit_person" required maxlength="2" class="required frm_input" size="2" onkeydown="this.value=this.value.replace(/[^0-9]/g,'')" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onblur="this.value=this.value.replace(/[^0-9]/g,'')"></td>
                    </tr>
					<tr>
                        <th scope="row">점심시간</th>
                        <td colspan="3">
							<select name="de_rsvt_lunch_stime" id="de_rsvt_lunch_stime">
								<?php foreach ($_const['start_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_lunch_stime'], $key); ?>><?php echo $val ?></option>
							<?php } ?>
							</select> ~ 
							<select name="de_rsvt_lunch_etime" id="de_rsvt_lunch_etime">
								<?php foreach ($_const['end_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_lunch_etime'], $key); ?>><?php echo $val ?></option>
								<?php } ?>
							</select>
                        </td>
                    </tr>
					<tr>
                        <th scope="row">변경 할 요일 및 시간</th>
                        <td style="line-height:3em">
							<input type="checkbox" name="de_rsvt_mon_act" value="1" id="de_rsvt_mon_act" <?php echo $default['de_rsvt_mon_act'] ? 'checked' : ''; ?>>월요일
							<select name="de_rsvt_mon_stime" id="de_rsvt_mon_stime">
								<?php foreach ($_const['start_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_mon_stime'], $key); ?>><?php echo $val ?></option>
							<?php } ?>
							</select> ~ 
							<select name="de_rsvt_mon_etime" id="de_rsvt_mon_etime">
								<?php foreach ($_const['end_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_mon_etime'], $key); ?>><?php echo $val ?></option>
								<?php } ?>
							</select><br>
							<input type="checkbox" name="de_rsvt_tue_act" value="1" id="de_rsvt_tue_act" <?php echo $default['de_rsvt_tue_act'] ? 'checked' : ''; ?>>화요일
							<select name="de_rsvt_tue_stime" id="de_rsvt_tue_stime">
								<?php foreach ($_const['start_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_tue_stime'], $key); ?>><?php echo $val ?></option>
							<?php } ?>
							</select> ~ 
							<select name="de_rsvt_tue_etime" id="de_rsvt_tue_etime">
								<?php foreach ($_const['end_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_tue_etime'], $key); ?>><?php echo $val ?></option>
								<?php } ?>
							</select><br>
							<input type="checkbox" name="de_rsvt_wed_act" value="1" id="de_rsvt_wed_act" <?php echo $default['de_rsvt_wed_act'] ? 'checked' : ''; ?>>수요일
							<select name="de_rsvt_wed_stime" id="de_rsvt_wed_stime">
								<?php foreach ($_const['start_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_wed_stime'], $key); ?>><?php echo $val ?></option>
							<?php } ?>
							</select> ~ 
							<select name="de_rsvt_wed_etime" id="de_rsvt_wed_etime">
								<?php foreach ($_const['end_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_wed_etime'], $key); ?>><?php echo $val ?></option>
								<?php } ?>
							</select><br>
							<input type="checkbox" name="de_rsvt_thu_act" value="1" id="de_rsvt_thu_act" <?php echo $default['de_rsvt_thu_act'] ? 'checked' : ''; ?>>목요일
							<select name="de_rsvt_thu_stime" id="de_rsvt_thu_stime">
								<?php foreach ($_const['start_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_thu_stime'], $key); ?>><?php echo $val ?></option>
							<?php } ?>
							</select> ~ 
							<select name="de_rsvt_thu_etime" id="de_rsvt_thu_etime">
								<?php foreach ($_const['end_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_thu_etime'], $key); ?>><?php echo $val ?></option>
								<?php } ?>
							</select><br>
							<input type="checkbox" name="de_rsvt_fri_act" value="1" id="de_rsvt_fri_act" <?php echo $default['de_rsvt_fri_act'] ? 'checked' : ''; ?>>금요일
							<select name="de_rsvt_fri_stime" id="de_rsvt_fri_stime">
								<?php foreach ($_const['start_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_fri_stime'], $key); ?>><?php echo $val ?></option>
							<?php } ?>
							</select> ~ 
							<select name="de_rsvt_fri_etime" id="de_rsvt_fri_etime">
								<?php foreach ($_const['end_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_fri_etime'], $key); ?>><?php echo $val ?></option>
								<?php } ?>
							</select><br>
							<input type="checkbox" name="de_rsvt_sat_act" value="1" id="de_rsvt_sat_act" <?php echo $default['de_rsvt_sat_act'] ? 'checked' : ''; ?>>토요일
							<select name="de_rsvt_sat_stime" id="de_rsvt_sat_stime">
								<?php foreach ($_const['start_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_sat_stime'], $key); ?>><?php echo $val ?></option>
							<?php } ?>
							</select> ~ 
							<select name="de_rsvt_sat_etime" id="de_rsvt_sat_etime">
								<?php foreach ($_const['end_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_sat_etime'], $key); ?>><?php echo $val ?></option>
								<?php } ?>
							</select><br>
							<input type="checkbox" name="de_rsvt_sun_act" value="1" id="de_rsvt_sun_act" <?php echo $default['de_rsvt_sun_act'] ? 'checked' : ''; ?>>일요일
							<select name="de_rsvt_sun_stime" id="de_rsvt_sun_stime">
								<?php foreach ($_const['start_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_sun_stime'], $key); ?>><?php echo $val ?></option>
							<?php } ?>
							</select> ~ 
							<select name="de_rsvt_sun_etime" id="de_rsvt_sun_etime">
								<?php foreach ($_const['end_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_sun_etime'], $key); ?>><?php echo $val ?></option>
								<?php } ?>
							</select><br>
							<input type="checkbox" name="de_rsvt_holiday_act" value="1" id="de_rsvt_holiday_act" <?php echo $default['de_rsvt_holiday_act'] ? 'checked' : ''; ?>> 공휴일근무
							<select name="de_rsvt_holiday_stime" id="de_rsvt_holiday_stime">
								<?php foreach ($_const['start_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_holiday_stime'], $key); ?>><?php echo $val ?></option>
							<?php } ?>
							</select> ~ 
							<select name="de_rsvt_holiday_etime" id="de_rsvt_holiday_etime">
								<?php foreach ($_const['end_time'] as $key => $val) { ?>
								<option value="<?php echo $key ?>" <?php echo get_selected($default['de_rsvt_holiday_etime'], $key); ?>><?php echo $val ?></option>
								<?php } ?>
							</select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <div class="btn_fixed_top btn_confirm">
        <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
    </div>

</form>

<script>



    function fconfigform_submit(f) {
        f.action = "./rsvt_config_form_update.php";
        return true;
    }
</script>

<?
require_once './admin.tail.php';
?>