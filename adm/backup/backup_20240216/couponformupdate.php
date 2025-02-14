<?php
$sub_menu = '400800';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "w");

check_admin_token();

$_POST = array_map('trim', $_POST);

$check_sanitize_keys = array(
  'cp_subject',       // 쿠폰이름
  'cp_method',        // 쿠폰종류
  'cp_target',        // 적용상품
  'mb_id',            // 회원아이디
  'cp_start',         // 사용시작일
  'cp_end',           // 사용종료일
  'cp_type',          // 쿠폰타입
  'cp_price',         // 할인금액
  'cp_type',          // 할인금액타입
  'cp_trunc',         // 절사금액
  'cp_minimum',       // 최소주문금액
  'cp_maximum',       // 최대할인금액
  'chk_all_mb'        // 전체회원 체크
);

foreach ($check_sanitize_keys as $key) {
  $$key = $_POST[$key] = isset($_POST[$key]) ? strip_tags(clean_xss_attributes($_POST[$key])) : '';
}

if (!$_POST['cp_subject'])
  alert('쿠폰이름을 입력해 주십시오.');

if ($_POST['cp_method'] == 0 && !$_POST['cp_target'])
  alert('적용상품을 입력해 주십시오.');

if ($_POST['cp_method'] == 1 && !$_POST['cp_target'])
  alert('적용분류를 입력해 주십시오.');

if (!$_POST['mb_id'] && !$_POST['chk_all_mb'])
  alert('회원아이디를 입력해 주십시오.');

if (!$_POST['cp_start'] || !$_POST['cp_end'])
  alert('사용 시작일과 종료일을 입력해 주십시오.');

if ($_POST['cp_start'] > $_POST['cp_end'])
  alert('사용 시작일은 종료일 이전으로 입력해 주십시오.');

if ($_POST['cp_end'] < G5_TIME_YMD)
  alert('종료일은 오늘(' . G5_TIME_YMD . ')이후로 입력해 주십시오.');

if (!$_POST['cp_price']) {
  if ($_POST['cp_type'])
    alert('할인비율을 입력해 주십시오.');
  else
    alert('할인금액을 입력해 주십시오.');
}

if ((int) $_POST['cp_price'] < 0) {
  alert('할인금액 또는 할인비율은 음수를 입력할수 없습니다.');
}

if ($_POST['cp_type'] && ($_POST['cp_price'] < 1 || $_POST['cp_price'] > 99))
  alert('할인비율을 1과 99사이 값으로 입력해 주십시오.');

if ($_POST['cp_method'] == 0) {
  $sql = " select count(*) as cnt from {$g5['g5_shop_item_table']} where it_id = '$cp_target' and it_nocoupon = '0' ";
  $row = sql_fetch($sql);
  if (!$row['cnt'])
    alert('입력하신 상품코드는 존재하지 않는 코드이거나 쿠폰적용안함으로 설정된 상품입니다.');
} else if ($_POST['cp_method'] == 1) {
  $sql = " select count(*) as cnt from {$g5['g5_shop_category_table']} where ca_id = '$cp_target' and ca_nocoupon = '0' ";
  $row = sql_fetch($sql);
  if (!$row['cnt'])
    alert('입력하신 분류코드는 존재하지 않는 분류코드이거나 쿠폰적용안함으로 설정된 분류입니다.');
}

if ($w == '') { // 새로입력
  if ($_POST['chk_all_mb']) {
    $mb_id = '전체회원';
  } else {
    $sql = " select mb_id from {$g5['member_table']} where mb_id = '{$_POST['mb_id']}' and mb_leave_date = '' and mb_intercept_date = '' ";
    $row = sql_fetch($sql);
    if (!$row['mb_id'])
      alert('입력하신 회원아이디는 존재하지 않거나 탈퇴 또는 차단된 회원아이디입니다.');

    $mb_id = $_POST['mb_id'];
  }

  $j = 0;
  do {
    $cp_id = get_coupon_id();

    $sql3 = " select count(*) as cnt from {$g5['g5_shop_coupon_table']} where cp_id = '$cp_id' ";
    $row3 = sql_fetch($sql3);

    if (!$row3['cnt'])
      break;
    else {
      if ($j > 20)
        die('Coupon ID Error');
    }

    $j++;
  } while (1);

  $sql = " INSERT INTO {$g5['g5_shop_coupon_table']}
                ( cp_id, cp_subject, cp_method, cp_target, mb_id, cp_start, cp_end, cp_type, cp_price, cp_trunc, cp_minimum, cp_maximum, cp_datetime )
            VALUES
                ( '$cp_id', '$cp_subject', '$cp_method', '$cp_target', '$mb_id', '$cp_start', '$cp_end', '$cp_type', '$cp_price', '$cp_trunc', '$cp_minimum', '$cp_maximum', '" . G5_TIME_YMDHIS . "' ) ";

  sql_query($sql);
} else if ($w == 'u') { // 쿠폰 수정
  $sql = " select * from {$g5['g5_shop_coupon_table']} where cp_id = '$cp_id' ";
  $cp = sql_fetch($sql);

  if (!$cp['cp_id'])
    alert('쿠폰정보가 존재하지 않습니다.', './couponlist.php');

  if ($_POST['chk_all_mb']) {
    $mb_id = '전체회원';
  }

  $sql = " update {$g5['g5_shop_coupon_table']}
                set cp_subject  = '$cp_subject',
                    cp_method   = '$cp_method',
                    cp_target   = '$cp_target',
                    mb_id       = '$mb_id',
                    cp_start    = '$cp_start',
                    cp_end      = '$cp_end',
                    cp_type     = '$cp_type',
                    cp_price    = '$cp_price',
                    cp_trunc    = '$cp_trunc',
                    cp_maximum  = '$cp_maximum',
                    cp_minimum  = '$cp_minimum'
                where cp_id = '$cp_id' ";
  sql_query($sql);
}

if ($w == '') { // 신규발행
  if (!$_POST['chk_all_mb'] && $mb_id) {
    $alimtalk = coupon_alimtalk('coupon1', $cp_id, ($config['cf_sms_use'] && $_POST['cp_alimtalk_send']));

    if ($config['cf_email_use'] && $_POST['cp_email_send']) {
      if ($alimtalk['mb_name'] && $alimtalk['mb_email']) {
        include_once(G5_LIB_PATH . '/mailer.lib.php');
        $mb_name = get_text($alimtalk['mb_name']);
        $title = '보미오라(BOMIORA) - ' . $alimtalk['title'];
        $contents = str_replace('\n', '<br>', $alimtalk['contents']);
        $email = $alimtalk['mb_email'];

        ob_start();
        include G5_SHOP_PATH . '/mail/couponmail.mail.php';
        $content = ob_get_contents();
        ob_end_clean();

        mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $email, $title, $content, 1);
      }
    }
  }
}

goto_url('./couponlist.php');
