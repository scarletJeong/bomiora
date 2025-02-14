<?php
$sub_menu = "300100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$mb = array(
    'mb_certify' => null,
    'mb_adult' => null,
    'mb_sms' => null,
    'mb_intercept_date' => null,
    'mb_id' => null,
    'mb_name' => null,
    'mb_nick' => null,
    'mb_point' => null,
    'mb_email' => null,
    'mb_homepage' => null,
    'mb_hp' => null,
    'mb_tel' => null,
    'mb_zip1' => null,
    'mb_zip2' => null,
    'mb_addr1' => null,
    'mb_addr2' => null,
    'mb_addr3' => null,
    'mb_addr_jibeon' => null,
    'mb_signature' => null,
    'mb_profile' => null,
    'mb_memo' => null,
    'mb_leave_date' => null,
    'mb_1' => null,
    'mb_2' => null,
    'mb_3' => null,
    'mb_4' => null,
    'mb_5' => null,
    'mb_6' => null,
    'mb_7' => null,
    'mb_8' => null,
    'mb_9' => null,
    'mb_10' => null,
);

$sound_only = '';
$required_mb_id = '';
$required_mb_id_class = '';
$required_mb_password = '';
$html_title = '';

if ($w == '') {
    $required_mb_id = 'required';
    //$required_mb_id_class = 'required alnum_';
	$required_mb_id_class = 'required email';
    $required_mb_password = 'required';
    $sound_only = '<strong class="sound_only">필수</strong>';

    $mb['mb_mailling'] = 1;
    $mb['mb_open'] = 1;
    $mb['mb_level'] = $_const['level']['인플루언서'];
    $html_title = '추가';
} elseif ($w == 'u') {
    $mb = get_member($mb_id);
    if (!$mb['mb_id']) {
        alert('존재하지 않는 회원자료입니다.');
    }

    if ($is_admin != 'super' && $mb['mb_level'] != $_const['level']['인플루언서']) {
        alert('인플루언서 회원만 수정할 수 있습니다.');
    }

    if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level']) {
        alert('자신보다 권한이 높거나 같은 회원은 수정할 수 없습니다.');
    }

    //$required_mb_id = 'readonly';
	$required_mb_id = 'required';
	$required_mb_id_class = 'required email';
    $html_title = '수정';

    $mb['mb_name'] = get_text($mb['mb_name']);
    $mb['mb_nick'] = get_text($mb['mb_nick']);
    $mb['mb_email'] = get_text($mb['mb_email']);
    $mb['mb_homepage'] = get_text($mb['mb_homepage']);
    $mb['mb_birth'] = get_text($mb['mb_birth']);
    $mb['mb_tel'] = get_text($mb['mb_tel']);
    $mb['mb_hp'] = get_text($mb['mb_hp']);
    $mb['mb_addr1'] = get_text($mb['mb_addr1']);
    $mb['mb_addr2'] = get_text($mb['mb_addr2']);
    $mb['mb_addr3'] = get_text($mb['mb_addr3']);
    $mb['mb_signature'] = get_text($mb['mb_signature']);
    $mb['mb_recommend'] = get_text($mb['mb_recommend']);
	$mb['mb_instar_link'] = get_text($mb['mb_instar_link']);
	$mb['mb_web_link'] = get_text($mb['mb_web_link']);
	$mb['mb_blog_link'] = get_text($mb['mb_blog_link']);
    $mb['mb_profile'] = get_text($mb['mb_profile']);
    $mb['mb_1'] = get_text($mb['mb_1']);
    $mb['mb_2'] = get_text($mb['mb_2']);
    $mb['mb_3'] = get_text($mb['mb_3']);
    $mb['mb_4'] = get_text($mb['mb_4']);
    $mb['mb_5'] = get_text($mb['mb_5']);
    $mb['mb_6'] = get_text($mb['mb_6']);
    $mb['mb_7'] = get_text($mb['mb_7']);
    $mb['mb_8'] = get_text($mb['mb_8']);
    $mb['mb_9'] = get_text($mb['mb_9']);
    $mb['mb_10'] = get_text($mb['mb_10']);

	// 프로필
	$sql = " select * from {$g5['health_profile_table']} where mb_id = '{$mb_id}' ";
	$answer = sql_fetch($sql);

} else {
    alert('제대로 된 값이 넘어오지 않았습니다.');
}

if ($mb['mb_intercept_date']) {
    $g5['title'] = "차단된 ";
} else {
    $g5['title'] .= "";
}
$g5['title'] .= '인플루언서회원 ' . $html_title;
require_once './admin.head.php';

// add_javascript('js 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js
?>

<form name="fmember" id="fmember" action="./influencer_member_form_update.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="mb_level" value="<?php echo $mb['mb_level'] ?>">
<input type="hidden" name="mb_mailling" value="1">
<input type="hidden" name="mb_sms" value="1">
<?php if ($w == 'u') { ?><input type="hidden" name="mb_id" value="<?php echo $mb['mb_id'] ?>"><?php } ?>

<section id="anc_sitfrm_cate">
    <h2 class="h2_frm">회원정보</h2>
	<!--
    <div class="local_desc02 local_desc">
        <p></p>
    </div>
	-->
    <div class="tbl_frm01 tbl_wrap">
        <table>
            <caption><?php echo $g5['title']; ?></caption>
            <colgroup>
                <col class="grid_4">
                <col>
                <col class="grid_4">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th scope="row"><label for="mb_email">아이디<?php echo $sound_only ?></label> <?php if ($w == 'u') { ?>(<?php echo $mb['mb_id'] ?>)<?php } ?>
					</th>
                    <td>
						<?php echo help('이메일로 작성, 아이디와 키값은 자동생성 됩니다.') ?>
						<!-- // prevent autocomplete jacknam -->
                        <input type="text" autocomplete="off" name="mb_email" value="<?php echo $mb['mb_email'] ? $mb['mb_email'] : '.';  ?>" id="mb_email" <?php echo $required_mb_id ?> class="frm_input <?php echo $required_mb_id_class ?>" size="80" maxlength="255">
                    </td>
                    <th scope="row"><label for="mb_password">비밀번호<?php echo $sound_only ?></label></th>
                    <td><input type="password" autocomplete="off" name="mb_password" id="mb_password" <?php echo $required_mb_password ?> class="frm_input <?php echo $required_mb_password ?>" size="15" maxlength="20"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="mb_name">이름(실명)<strong class="sound_only">필수</strong></label></th>
                    <td><input type="text" name="mb_name" value="<?php echo $mb['mb_name'] ?>" id="mb_name" required class="required frm_input" size="15" maxlength="20"></td>
                    <th scope="row"><label for="mb_nick">닉네임<strong class="sound_only">필수</strong></label></th>
                    <td><input type="text" name="mb_nick" value="<?php echo $mb['mb_nick'] ?>" id="mb_nick" required class="required frm_input" size="15" maxlength="20"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="mb_hp">휴대폰번호</label></th>
                    <td><input type="text" name="mb_hp" value="<?php echo $mb['mb_hp'] ?>" id="mb_hp" class="frm_input" size="15" maxlength="20"></td>
                    <th scope="row"><label for="mb_tel">전화번호</label></th>
                    <td><input type="text" name="mb_tel" value="<?php echo $mb['mb_tel'] ?>" id="mb_tel" class="frm_input" size="15" maxlength="20"></td>
                </tr>
				<?php if($w == 'u') { ?>
				<tr>
                    <th scope="row">코드</th>
                    <td><?php echo $mb['mb_inf_code'] ?></td>
					<th scope="row">충전금</th>
                    <td><a href="./influencer_charge_list.php?sca=<?php echo $mb['mb_id'] ?>" target="_blank"><?php echo number_format($mb['mb_charge']) ?></a> 점</td>
                </tr>
				<?php } ?>
                <tr>
                    <th scope="row"><label for="mb_img">회원이미지</label></th>
                    <td colspan="3">
                        <?php echo help('이미지 크기는 <strong>넓이 ' . $config['cf_member_img_width'] . '픽셀 높이 ' . $config['cf_member_img_height'] . '픽셀</strong>로 해주세요.') ?>
                        <input type="file" name="mb_img" id="mb_img">
                        <?php
                        $mb_dir = substr($mb['mb_id'], 0, 2);
                        $icon_file = G5_DATA_PATH . '/member_image/' . $mb_dir . '/' . get_mb_icon_name($mb['mb_id']) . '.gif';
                        if (file_exists($icon_file)) {
                            echo get_member_profile_img($mb['mb_id']);
                            echo '<input type="checkbox" id="del_mb_img" name="del_mb_img" value="1">삭제';
                        }
                        ?>
                    </td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_instar_link">인스타그램</label></th>
                    <td colspan="3"><input type="text" name="mb_instar_link" value="<?php echo $mb['mb_instar_link'] ?>" id="mb_instar_link" class="frm_input" size="100" maxlength="255"></td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_web_link">웹링크</label></th>
                    <td colspan="3"><input type="text" name="mb_web_link" value="<?php echo $mb['mb_web_link'] ?>" id="mb_web_link" class="frm_input" size="100" maxlength="255"></td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_blog_link">블로그</label></th>
                    <td colspan="3"><input type="text" name="mb_blog_link" value="<?php echo $mb['mb_blog_link'] ?>" id="mb_blog_link" class="frm_input" size="100" maxlength="255"></td>
                </tr>
				<tr>
					<th scope="row"><label for="mb_profile">자기 소개</label></th>
					<td colspan="3"><textarea name="mb_profile" id="mb_profile"><?php echo html_purifier($mb['mb_profile']); ?></textarea></td>
                </tr>
				<!-- 프로필 추가 { -->
				<tr>
                    <th scope="row"><label for="answer_1">생년월일<strong class="sound_only">필수</strong></label></th>
                    <td><input type="text" name="answer_1" value="<?php echo $answer['answer_1'] ?>" id="answer_1" class="frm_input" minlength="8" maxlength="8" placeholder="예)20200101" onkeydown="this.value=this.value.replace(/[^0-9]/g,'')" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onblur="this.value=this.value.replace(/[^0-9]/g,'')" >
					<th scope="row"><label for="mb_weight_loss">목표감량체중</label></th>
                    <td><input type="text" name="answer_3" value="<?php echo $answer['answer_3'] ?>" id="answer_3" class="frm_input" maxlength="3" onkeydown="this.value=this.value.replace(/[^0-9]/g,'')" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onblur="this.value=this.value.replace(/[^0-9]/g,'')"> KG
                </tr>
				<tr>
                    <th scope="row"><label for="answer_2">성별<strong class="sound_only">필수</strong></label></th>
                    <td colspan="3">
						<?php foreach ($_const['sex'] as $key => $val) { ?>
						<label for="mb_sex_<?php echo $key ?>"><input type="radio" name="answer_2" id="mb_sex_<?php echo $key ?>" value="<?php echo $key ?>" <?php echo get_checked($key, $answer['answer_2']); ?>> <?php echo $val ?></label>
						<?php } ?>
                    </td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_height">키</label></th>
                    <td><input type="text" name="answer_4" value="<?php echo $answer['answer_4'] ?>" id="answer_4" class="frm_input" maxlength="3" onkeydown="this.value=this.value.replace(/[^0-9]/g,'')" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onblur="this.value=this.value.replace(/[^0-9]/g,'')" > CM</td>
                    <th scope="row"><label for="mb_weight">몸무게</label></th>
                    <td><input type="text" name="answer_5" value="<?php echo $answer['answer_5'] ?>" id="answer_5" class="frm_input" maxlength="3" onkeydown="this.value=this.value.replace(/[^0-9]/g,'')" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')" onblur="this.value=this.value.replace(/[^0-9]/g,'')" > KG</td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_diet_period">다이어트 예상시간</label></th>
                    <td colspan="3">
						<?php
						$number = 1;
						foreach ($_const['diet_period'] as $key => $val) { ?>
						<label for="answer_6_<?php echo $number ?>"><input type="radio" name="answer_6" id="answer_6_<?php echo $number ?>" value="<?php echo $key ?>" <?php echo get_checked($key, $answer['answer_6']); ?>> <?php echo $val ?></label>
						<?php
							$number++;
						}
						?>
                    </td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_day_meal">하루끼니</label></th>
                    <td colspan="3">
						<?php
						$number = 1;
						foreach ($_const['day_meal'] as $key => $val) { ?>
						<label for="answer_7_<?php echo $number ?>"><input type="radio" name="answer_7" id="answer_7_<?php echo $number ?>" value="<?php echo $key ?>" <?php echo get_checked($key, $answer['answer_7']); ?>> <?php echo $val ?></label>
						<?php
							$number++;
						}
						?>
                    </td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_eating_habits">식습관</label></th>
                    <td colspan="3">
						<?php
						//$answer_8	= explode('|', '');// 식습관
						$answer_8	= explode('|', $answer['answer_8']);// 식습관
						$number = 1;
						foreach ($_const['eating_habits'] as $key => $val) {
						?>
						<input type="checkbox" name="answer_8[]" value="<?php echo $key ?>" id="answer_8_<?php echo $number ?>"<?php echo (in_array($key, $answer_8) !== false) ? ' checked' : '' ?>><label for="answer_8_<?php echo $number ?>"><?php echo $val ?></label>
						<?php
							$number++;
						}
						?>
                    </td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_often_food">자주 먹는 음식</label></th>
                    <td colspan="3">
						<?php
						//$answer_9	= explode('|', '');// 자주 먹는 음식
						$answer_9	= explode('|', $answer['answer_9']);// 자주 먹는 음식
						$number = 1;
						foreach ($_const['often_food'] as $key => $val) {
						?>
						<input type="checkbox" name="answer_9[]" value="<?php echo $key ?>" id="answer_9_<?php echo $number ?>"<?php echo (in_array($key, $answer_9) !== false) ? ' checked' : '' ?>><label for="answer_9_<?php echo $number ?>"><?php echo $val ?></label>
						<?php
							$number++;
						}
						?>
                    </td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_exercise_habit">운동 습관</label></th>
                    <td colspan="3">
						<?php
						$number = 1;
						foreach ($_const['exercise_habit'] as $key => $val) { ?>
						<label for="answer_10_<?php echo $number ?>"><input type="radio" name="answer_10" id="answer_10_<?php echo $number ?>" value="<?php echo $key ?>" <?php echo get_checked($key, $answer['answer_10']); ?>> <?php echo $val ?></label>
						<?php
							$number++;
						}
						?>
                    </td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_eisease">질병</label></th>
                    <td colspan="3">
						<?php
						//$answer_11	= explode('|', '');// 질병
						$answer_11	= explode('|', $answer['answer_11']);// 질병
						$number = 1;
						foreach ($_const['eisease'] as $key => $val) {
						?>
						<input type="checkbox" name="answer_11[]" value="<?php echo $key ?>" id="answer_11_<?php echo $number ?>"<?php echo (in_array($key, $answer_11) !== false) ? ' checked' : '' ?>><label for="answer_11_<?php echo $number ?>"><?php echo $val ?></label>
						<?php
							$number++;
						}
						?>
                    </td>
                </tr>
				<tr>
                    <th scope="row"><label for="mb_medication_hand">복용중인 약</label></th>
                    <td colspan="3">
						<?php
						//$answer_12	= explode('|', '');// 복용중인 약
						$answer_12	= explode('|', $answer['answer_12']);// 복용중인 약
						$number = 1;
						foreach ($_const['medication_hand'] as $key => $val) {
						?>
						<input type="checkbox" name="answer_12[]" value="<?php echo $key ?>" id="answer_12_<?php echo $number ?>"<?php echo (in_array($key, $answer_12) !== false) ? ' checked' : '' ?>><label for="answer_12_<?php echo $number ?>"><?php echo $val ?></label>
						<?php
							$number++;
						}
						?>
                    </td>
                </tr>
				<!-- } 프로필 추가 -->
                <tr>
                    <th scope="row"><label for="mb_memo">메모</label></th>
                    <td colspan="3"><textarea name="mb_memo" id="mb_memo"><?php echo html_purifier($mb['mb_memo']); ?></textarea></td>
                </tr>

                <?php if ($w == 'u') { ?>
                    <tr>
                        <th scope="row">회원가입일</th>
                        <td><?php echo $mb['mb_datetime'] ?></td>
                        <th scope="row">최근접속일</th>
                        <td><?php echo $mb['mb_today_login'] ?></td>
                    </tr>
                    <tr>
                        <th scope="row">IP</th>
                        <td colspan="3"><?php echo $mb['mb_ip'] ?></td>
                    </tr>
                    <?php if ($config['cf_use_email_certify']) { ?>
                        <tr>
                            <th scope="row">인증일시</th>
                            <td colspan="3">
                                <?php if ($mb['mb_email_certify'] == '0000-00-00 00:00:00') { ?>
                                    <?php echo help('회원님이 메일을 수신할 수 없는 경우 등에 직접 인증처리를 하실 수 있습니다.') ?>
                                    <input type="checkbox" name="passive_certify" id="passive_certify">
                                    <label for="passive_certify">수동인증</label>
                                <?php } else { ?>
                                    <?php echo $mb['mb_email_certify'] ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                <tr>
                    <th scope="row"><label for="mb_leave_date">탈퇴일자</label></th>
                    <td>
                        <input type="text" name="mb_leave_date" value="<?php echo $mb['mb_leave_date'] ?>" id="mb_leave_date" class="frm_input" maxlength="8">
                        <input type="checkbox" value="<?php echo date("Ymd"); ?>" id="mb_leave_date_set_today" onclick="if (this.form.mb_leave_date.value==this.form.mb_leave_date.defaultValue) { this.form.mb_leave_date.value=this.value; } else { this.form.mb_leave_date.value=this.form.mb_leave_date.defaultValue; }">
                        <label for="mb_leave_date_set_today">탈퇴일을 오늘로 지정</label>
                    </td>
                    <th scope="row">접근차단일자</th>
                    <td>
                        <input type="text" name="mb_intercept_date" value="<?php echo $mb['mb_intercept_date'] ?>" id="mb_intercept_date" class="frm_input" maxlength="8">
                        <input type="checkbox" value="<?php echo date("Ymd"); ?>" id="mb_intercept_date_set_today" onclick="if (this.form.mb_intercept_date.value==this.form.mb_intercept_date.defaultValue) { this.form.mb_intercept_date.value=this.value; } else { this.form.mb_intercept_date.value=this.form.mb_intercept_date.defaultValue; }">
                        <label for="mb_intercept_date_set_today">접근차단일을 오늘로 지정</label>
                    </td>
                </tr>

                <?php
                //소셜계정이 있다면
                if (function_exists('social_login_link_account') && $mb['mb_id']) {
                    if ($my_social_accounts = social_login_link_account($mb['mb_id'], false, 'get_data')) { ?>
                        <tr>
                            <th>소셜계정목록</th>
                            <td colspan="3">
                                <ul class="social_link_box">
                                    <li class="social_login_container">
                                        <h4>연결된 소셜 계정 목록</h4>
                                        <?php foreach ($my_social_accounts as $account) {     //반복문
                                            if (empty($account)) {
                                                continue;
                                            }

                                            $provider = strtolower($account['provider']);
                                            $provider_name = social_get_provider_service_name($provider);
                                        ?>
                                            <div class="account_provider" data-mpno="social_<?php echo $account['mp_no']; ?>">
                                                <div class="sns-wrap-32 sns-wrap-over">
                                                    <span class="sns-icon sns-<?php echo $provider; ?>" title="<?php echo $provider_name; ?>">
                                                        <span class="ico"></span>
                                                        <span class="txt"><?php echo $provider_name; ?></span>
                                                    </span>

                                                    <span class="provider_name"><?php echo $provider_name;   //서비스이름 ?> ( <?php echo $account['displayname']; ?> )</span>
                                                    <span class="account_hidden" style="display:none"><?php echo $account['mb_id']; ?></span>
                                                </div>
                                                <div class="btn_info"><a href="<?php echo G5_SOCIAL_LOGIN_URL . '/unlink.php?mp_no=' . $account['mp_no'] ?>" class="social_unlink" data-provider="<?php echo $account['mp_no']; ?>">연동해제</a> <span class="sound_only"><?php echo substr($account['mp_register_day'], 2, 14); ?></span></div>
                                            </div>
                                        <?php } //end foreach ?>
                                    </li>
                                </ul>
                                <script>
                                    jQuery(function($) {
                                        $(".account_provider").on("click", ".social_unlink", function(e) {
                                            e.preventDefault();

                                            if (!confirm('정말 이 계정 연결을 삭제하시겠습니까?')) {
                                                return false;
                                            }

                                            var ajax_url = "<?php echo G5_SOCIAL_LOGIN_URL . '/unlink.php' ?>";
                                            var mb_id = '',
                                                mp_no = $(this).attr("data-provider"),
                                                $mp_el = $(this).parents(".account_provider");

                                            mb_id = $mp_el.find(".account_hidden").text();

                                            if (!mp_no) {
                                                alert('잘못된 요청! mp_no 값이 없습니다.');
                                                return;
                                            }

                                            $.ajax({
                                                url: ajax_url,
                                                type: 'POST',
                                                data: {
                                                    'mp_no': mp_no,
                                                    'mb_id': mb_id
                                                },
                                                dataType: 'json',
                                                async: false,
                                                success: function(data, textStatus) {
                                                    if (data.error) {
                                                        alert(data.error);
                                                        return false;
                                                    } else {
                                                        alert("연결이 해제 되었습니다.");
                                                        $mp_el.fadeOut("normal", function() {
                                                            $(this).remove();
                                                        });
                                                    }
                                                }
                                            });

                                            return;
                                        });
                                    });
                                </script>

                            </td>
                        </tr>

                <?php
                    }   //end if
                }   //end if

                run_event('admin_member_form_add', $mb, $w, 'table');
                ?>
            </tbody>
        </table>
    </div>
</section>

<?php
// 제품수
$sql = " select count(*) as cnt from {$g5['g5_shop_item_table']} where it_mb_inf = '{$mb_id}' and it_use = '1' ";
$row = sql_fetch($sql);
$item_count = $row['cnt'];
$sql = " select * from {$g5['g5_shop_item_table']} where it_mb_inf = '{$mb_id}' and it_use = '1' order by it_id desc ";
$result = sql_query($sql);

if($w == 'u') {
?>
<section id="anc_sitfrm_cate">
    <h2 class="h2_frm">상품정보(<?php echo $item_count ?>)</h2>
	<!--
    <div class="local_desc02 local_desc">
        <p></p>
    </div>
	-->
    <div class="tbl_head01 tbl_wrap">
        <table>
            <caption>상품 목록</caption>
            <thead>
                <tr>
                    <th scope="col">상품코드</th>
					<th scope="col">상품분류</th>
					<th scope="col">이미지</th>
					<th scope="col">상품명</th>
					<th scope="col">판매가격</th>
					<th scope="col">품절</th>
					<th scope="col">조회</th>
					<th scope="col">판매수</th>
					<th scope="col">후기수</th>
					<th scope="col">도움쿠폰</th>
					<th scope="col">링크복사</th>
                </tr>

            </thead>
            <tbody>
                <?php
                for ($i = 0; $row = sql_fetch_array($result); $i++) {
					$href = shop_item_url($row['it_id']);
					$bg = 'bg'.($i%2);
					/*
					$navi_datas = get_shop_navigation_data(true, $row['ca_id']);
					$ca_ids = array(
						'ca_id' => substr($row['ca_id'],0,2),
						'ca_id2' => substr($row['ca_id'],0,4),
						'ca_id3' => substr($row['ca_id'],0,6)
					);
					if( isset($navi_datas[0]) && $navi_datas[0] ){
						foreach((array) $navi_datas[0] as $data ){
							echo $data['ca_name']."<br>";
						}
					}
					*/
					$ca = sql_fetch(get_shop_category_sql($row['ca_id'], strlen($row['ca_id'])));
                ?>

                    <tr class="<?php echo $bg; ?>">
						<td class="td_num"><?php echo $row['it_id'] ?></td>
						<td class="td_category"><?php echo get_text($ca['ca_name']) ?></td>
						<td class="td_img"><a href="<?php echo $href; ?>"><?php echo get_it_image($row['it_id'], 50, 50); ?></a></td>
						<td class=""><?php echo htmlspecialchars2(cut_str($row['it_name'],250, "")); ?></td>
						<td class="td_numbig"><?php echo display_price($row['it_price']) ?></td>
						<td class="td_mbstat"><?php echo ($row['it_soldout'] ? '품절' : ''); ?></td>
						<td class=""><?php echo $row['it_hit']; ?></td>
						<td class="td_num">판매수</td>
						<td class="td_num">후기수</td>
                        <td class="td_mng">
							<button type="button" id="" class="inf_itemcopy btn btn_02" data-id=<?php echo $i ?>>복사</button>
							<input type="hidden" name="itemurl" value="<?php echo $href ?>" id="itemurl_<?php echo $i ?>">
						</td>
                    </tr>
                <?php
                }
                if ($i == 0) {
                    echo "<tr><td colspan=\"10\" class=\"empty_table\">제품이 없습니다.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<?php } ?>
<div class="btn_fixed_top">
	<a href="./influencer_member_list.php?<?php echo $qstr ?>" class="btn btn_02">목록</a>
	<input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
    function fmember_submit(f) {
        if (!f.mb_icon.value.match(/\.(gif|jpe?g|png)$/i) && f.mb_icon.value) {
            alert('아이콘은 이미지 파일만 가능합니다.');
            return false;
        }

        if (!f.mb_img.value.match(/\.(gif|jpe?g|png)$/i) && f.mb_img.value) {
            alert('회원이미지는 이미지 파일만 가능합니다.');
            return false;
        }

        return true;
    }

// prevent autocomplete jacknam
$( document ).ready(function() {
  setTimeout(function() {
    $(":input[type=text][value='.']").prop("autocomplete", "off").val("");
  }, 1000);
});

</script>
<?php
run_event('admin_influencer_member_form_after', $mb, $w);

require_once './admin.tail.php';
