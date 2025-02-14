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

} else {
    alert('제대로 된 값이 넘어오지 않았습니다.');
}

$mb_cert_history = '';
if (isset($mb_id) && $mb_id) {
    $sql = "select * from {$g5['member_cert_history_table']} where mb_id = '{$mb_id}' order by ch_id asc";
    $mb_cert_history = sql_query($sql);
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
                        <input type="text" name="mb_email" value="<?php echo $mb['mb_email'] ?>" id="mb_email" <?php echo $required_mb_id ?> class="frm_input <?php echo $required_mb_id_class ?>" size="80" maxlength="255">
                    </td>
                    <th scope="row"><label for="mb_password">비밀번호<?php echo $sound_only ?></label></th>
                    <td><input type="password" name="mb_password" id="mb_password" <?php echo $required_mb_password ?> class="frm_input <?php echo $required_mb_password ?>" size="15" maxlength="20"></td>
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

</script>
<?php
run_event('admin_influencer_member_form_after', $mb, $w);

require_once './admin.tail.php';
