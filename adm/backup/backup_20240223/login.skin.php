<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 로그인 시작 { -->
<div class="hidden_scroll">
    <div class="login_bg" style="position: relative;">
        <div class="login_w">
            <h2 class="login_title">
                 LOGIN
            </h2>
            <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post">
			<input type="hidden" name="url" value="<?php echo $login_url ?>">
			<input type="hidden" name="email_login" value="y">
                <ul class="login_list">
                    <li>
						<input type="text" name="mb_id" id="login_id" required class="email" maxLength="255" placeholder="이메일">
                    </li>
                    <li>
						<input type="password" name="mb_password" id="login_pw" required class="" maxLength="20" placeholder="비밀번호">
                    </li>
                </ul>
                <!-- 23_10_11 대표님 지시사항 자동로그인,회원가입 버튼 추가 -->
                <ul class="login_btn_list">
                    <!--<li><a href="<?php echo G5_BBS_URL ?>/register.php" class="">회원가입</a></li>-->
                    <li>
                        <input type="checkbox" id="login_auto">
                        <label for="login_auto">자동로그인</label>
                    </li>
                    <li><a href="<?php echo G5_BBS_URL ?>/register_form.php">회원가입</a></li>
                    <li><a href="<?php echo G5_BBS_URL ?>/password_lost.php" id="ol_password_lost">아이디/비밀번호 찾기</a></li>
                </ul>
                <!-- //23_10_11 -->
                <button type="submit" class="login_btn">로그인</button>

				<?php @include_once(get_social_skin_path().'/social_login.skin.php'); // 소셜로그인 사용시 소셜로그인 버튼 ?>

            </form>
            <p class="copyright">COPYRIGHT BY BOMIORA. ALL RIGHT RESERVED.</p>
        </div>
    </div>
</div>
<script>
function flogin_submit(f)
{
    if( $( document.body ).triggerHandler( 'login_sumit', [f, 'flogin'] ) !== false ){
        return true;
    }
    return false;
}
</script>
<!-- } 로그인 끝 -->