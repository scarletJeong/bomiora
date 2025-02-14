<?php
include_once('./_common.php');
include_once(G5_LIB_PATH . '/mailer.lib.php');

$post_p_hash = isset($_POST['P_HASH']) ? $_POST['P_HASH'] : '';
$post_enc_data = isset($_POST['enc_data']) ? $_POST['enc_data'] : '';
$post_enc_info = isset($_POST['enc_info']) ? $_POST['enc_info'] : '';
$post_tran_cd = isset($_POST['tran_cd']) ? $_POST['tran_cd'] : '';
$post_lgd_paykey = isset($_POST['LGD_PAYKEY']) ? $_POST['LGD_PAYKEY'] : '';

//삼성페이 또는 lpay 또는 이니시스 카카오페이 요청으로 왔다면 현재 삼성페이 또는 lpay 또는 이니시스 카카오페이는 이니시스 밖에 없으므로 $default['de_pg_service'] 값을 이니시스로 변경한다.
if (is_inicis_order_pay($od_settle_case) && !empty($_POST['P_HASH'])) {
  $default['de_pg_service'] = 'inicis';
}

// 타 PG 사용시 NHN KCP 네이버페이로 결제 요청이 왔다면 $default['de_pg_service'] 값을 kcp 로 변경합니다.
if (function_exists('is_use_easypay') && is_use_easypay('global_nhnkcp') && $post_enc_data && isset($_POST['site_cd']) && isset($_POST['nhnkcp_pay_case']) && $_POST['nhnkcp_pay_case'] === "naverpay") {
  $default['de_pg_service'] = 'kcp';
}

if ($default['de_pg_service'] == 'inicis' && get_session('ss_order_id')) {
  if ($exist_order = get_shop_order_data(get_session('ss_order_id'))) {    //이미 상품이 주문되었다면 리다이렉트
    if (isset($exist_order['od_tno']) && $exist_order['od_tno']) {
      exists_inicis_shop_order(get_session('ss_order_id'), array(), $exist_order['od_time'], $exist_order['od_ip']);
      exit;
    }
  }
}

if (function_exists('add_order_post_log')) add_order_post_log('init', 'init');

$page_return_url = G5_SHOP_URL . '/orderform.php';
if (get_session('ss_direct'))
$page_return_url .= '?sw_direct=1';


// 결제등록 완료 체크
// jacknam
//if ($od_settle_case && $od_settle_case != '무통장' && $od_settle_case != 'KAKAOPAY' && $od_settle_case != "테스트") {
if (!in_array($od_settle_case, ['', 'NONE', '무통장', 'KAKAOPAY', 'TEST'])) {
  if ($default['de_pg_service'] == 'kcp' && ($post_tran_cd === '' || $post_enc_info === '' || $post_enc_data === ''))
  alert('결제등록 요청 후 주문해 주십시오.', $page_return_url);

  if ($default['de_pg_service'] == 'lg' && !$post_lgd_paykey)
  alert('결제등록 요청 후 주문해 주십시오.', $page_return_url);

  if ($default['de_pg_service'] == 'inicis' && !$post_p_hash)
  alert('결제등록 요청 후 주문해 주십시오.', $page_return_url);
}

// 장바구니가 비어있는가?
if (get_session('ss_direct'))
$tmp_cart_id = get_session('ss_cart_direct');
else
$tmp_cart_id = get_session('ss_cart_id');

if (get_cart_count($tmp_cart_id) == 0) {    // 장바구니에 담기
  if (function_exists('add_order_post_log')) add_order_post_log('장바구니가 비어 있습니다.');
  alert('장바구니가 비어 있습니다.\\n\\n이미 주문하셨거나 장바구니에 담긴 상품이 없는 경우입니다.', G5_SHOP_URL . '/cart.php');
}

/*
$sql = "select * from {$g5['g5_shop_order_table']} limit 1";
$check_tmp = sql_fetch($sql);

if (!isset($check_tmp['od_other_pay_type'])) {
  $sql = "ALTER TABLE `{$g5['g5_shop_order_table']}`
  ADD COLUMN `od_other_pay_type` VARCHAR(100) NOT NULL DEFAULT '' AFTER `od_settle_case`; ";
  sql_query($sql, false);
}
*/

// 변수 초기화
$od_other_pay_type = '';

$od_temp_point = isset($_POST['od_temp_point']) ? (int)$_POST['od_temp_point'] : 0;
$od_hope_date = isset($_POST['od_hope_date']) ? clean_xss_tags($_POST['od_hope_date'], 1, 1) : '';
$ad_default = isset($_POST['ad_default']) ? (int)$_POST['ad_default'] : 0;

$error = "";
// 장바구니 상품 재고 검사
/*
$sql = " select it_id, ct_qty, it_name, io_id, io_type, ct_option, ct_kind
from {$g5['g5_shop_cart_table']} where od_id = '$tmp_cart_id' and ct_select = '1' and ct_output = 'Y'	";
*/

// 장바구니 상품 재고 검사 및 인플루언서 링크 제품 여부 검사
$sql = " select a.it_id, a.ct_qty, a.it_name, a.io_id, a.io_type, a.ct_option, a.ct_kind, b.it_mb_inf
from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_item_table']} b on a.it_id = b.it_id
where a.od_id = '$tmp_cart_id' and a.ct_select = '1' and a.ct_output = 'Y' ";
$result = sql_query($sql);

$it_mb_inf = [];
for ($i = 0; $row = sql_fetch_array($result); $i++) {
  // 상품에 대한 현재고수량
  if ($row['io_id']) {
    $it_stock_qty = (int)get_option_stock_qty($row['it_id'], $row['io_id'], $row['io_type']);
  } else {
    $it_stock_qty = (int)get_it_stock_qty($row['it_id']);
  }
  // 장바구니 수량이 재고수량보다 많다면 오류
  if ($row['ct_qty'] > $it_stock_qty) {
    $error .= "{$row['ct_option']} 의 재고수량이 부족합니다. 현재고수량 : $it_stock_qty 개\\n\\n";
  }

  if ($row['it_mb_inf'] != '') {
    $it_mb_inf[] = $row['it_mb_inf'];
  }
}

if ($i == 0) {
  if (function_exists('add_order_post_log')) add_order_post_log('장바구니가 비어 있습니다.');
  alert('장바구니가 비어 있습니다.\\n\\n이미 주문하셨거나 장바구니에 담긴 상품이 없는 경우입니다.', G5_SHOP_URL . '/cart.php');
}

if ($error != "") {
  $error .= "다른 고객님께서 {$od_name}님 보다 먼저 주문하신 경우입니다. 불편을 끼쳐 죄송합니다.";
  if (function_exists('add_order_post_log')) add_order_post_log($error);
  alert($error, $page_return_url);
}


$od_price = isset($_POST['od_price']) ? (int)$_POST['od_price'] : 0;

// 주문금액이 상이함
$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as od_price, COUNT(distinct it_id) as cart_count
         from {$g5['g5_shop_cart_table']} where od_id = '$tmp_cart_id' and ct_select = '1' and ct_output = 'Y' ";
$row = sql_fetch($sql);
$tot_ct_price = $row['od_price'];
$cart_count = $row['cart_count'];
$tot_od_price = (int)$tot_ct_price;

//echo '<br>tot_od_price ';
//var_dump($od_price, $tot_ct_price, $cart_count, $tot_od_price);
//exit;

if ($od_price != $tot_od_price) {
  if(function_exists('add_order_post_log')) add_order_post_log('주문금액 최종 계산 Error.');
  die("Error.");
}

$coupon_point_disable = $tot_od_price == 0 || count($it_mb_inf) > 0;

// 쿠폰금액계산
//jacknam
if ($coupon_point_disable) {
  $cp_id = [];
  $cp_id_price = [];
  $cp_price = [];
  $tot_it_cp_price = 0;
  $tot_od_cp_price = 0;
} else {
  $cp_id = (isset($_POST['cp_id']) && is_array($_POST['cp_id'])) ? $_POST['cp_id'] : array();
  $cp_id_price = (isset($_POST['cp_id_price']) && is_array($_POST['cp_id_price'])) ? $_POST['cp_id_price'] : array();
  $cp_price = (isset($_POST['cp_price']) && is_array($_POST['cp_price'])) ? $_POST['cp_price'] : array();
  $tot_it_cp_price = array_sum($cp_price);
  $tot_od_cp_price = isset($_POST['od_cp_price']) ? (int)$_POST['od_cp_price'] : 0;
}

if ($tot_it_cp_price != $tot_od_cp_price) {
  if(function_exists('add_order_post_log')) add_order_post_log('쿠폰금액 최종 계산 Error.');
  die("Error.");
}
$od_cart_coupon = 0;

$od_send_cost = isset($_POST['od_send_cost']) ? (int)$_POST['od_send_cost'] : 0;
$od_send_cost2 = isset($_POST['od_send_cost2']) ? (int)$_POST['od_send_cost2'] : 0;
$od_send_cost3 = isset($_POST['od_send_cost3']) ? (int)$_POST['od_send_cost3'] : 0;

if ($coupon_point_disable) {
  $od_send_coupon = 0;
} else {
  $od_send_coupon = isset($_POST['od_send_coupon']) ? (int)$_POST['od_send_coupon'] : 0;
}

$tot_send_cost = $od_send_cost + $od_send_cost2 + $od_send_cost3 - $od_send_coupon;

// 배송비가 상이함
$i_send_cost = get_sendcost($tmp_cart_id);
if ($i_send_cost != $od_send_cost || $tot_send_cost < 0) {
  if(function_exists('add_order_post_log')) add_order_post_log('배송비 최종 계산 Error..');
  die("Error..");
}

// 결제포인트가 상이함
// 회원이면서 포인트사용이면
$i_temp_point = 0;
if (!$coupon_point_disable) {
  if ($is_member && $config['cf_use_point']) {
    if($member['mb_point'] >= $default['de_settle_min_point']) {
      $i_temp_point = (int)$default['de_settle_max_point'];

      if($i_temp_point > (int)$tot_od_price) {
        $i_temp_point = (int)$tot_od_price;
      }

      if($i_temp_point > (int)$member['mb_point']) {
        $i_temp_point = (int)$member['mb_point'];
      }

      $point_unit = (int)$default['de_settle_point_unit'];
      $i_temp_point = (int)((int)($i_temp_point / $point_unit) * $point_unit);
    }
  }
}

$od_temp_point = 0;
if ($i_temp_point > 0) {
  $od_temp_point = isset($_POST['od_temp_point']) ? (int)$_POST['od_temp_point'] : 0;
}

if ($od_temp_point > $i_temp_point || $od_temp_point < 0) {
  if(function_exists('add_order_post_log')) add_order_post_log('포인트 최종 계산 Error....');
  die("Error....");
}

if ($od_temp_point) {
  if ($member['mb_point'] < $od_temp_point) {
    if(function_exists('add_order_post_log')) add_order_post_log('회원님의 포인트가 부족하여 포인트로 결제 할 수 없습니다.');
    alert('회원님의 포인트가 부족하여 포인트로 결제 할 수 없습니다.');
  }
}

$od_receipt_point = (int)$od_temp_point;
//$tot_payment = $tot_od_price + $tot_send_cost - $tot_od_cp_price - $od_temp_point;
$tot_payment = $tot_od_price + $tot_send_cost - $tot_od_cp_price - $od_receipt_point;

$payment_request = false;
$od_status = '주문';
$od_tno    = '';
if ($od_settle_case == "무통장") {
  $od_receipt_price   = 0;
  //$od_misu            = $od_price - $od_receipt_price;
  $od_misu            = $tot_payment - $od_receipt_price;
  if ($od_misu == 0) {
    $od_status      = '입금';
    $od_receipt_time = G5_TIME_YMDHIS;
  } else {
    $payment_request = true;
  }
  $tno = $od_receipt_time = $od_app_no = '';
} else if ($od_settle_case == "계좌이체") {
  switch ($default['de_pg_service']) {
    case 'lg':
    include G5_SHOP_PATH . '/lg/xpay_result.php';
    break;
    case 'inicis':
    include G5_MSHOP_PATH . '/inicis/pay_result.php';
    break;
    default:
    include G5_MSHOP_PATH . '/kcp/pp_ax_hub.php';
    $bank_name  = iconv("cp949", "utf-8", $bank_name);
    break;
  }

  $od_tno             = $tno;
  $od_receipt_price   = $amount;
  $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
  $od_deposit_name    = $od_name;
  $od_bank_account    = $bank_name;
  $pg_price           = $amount;
  //$od_misu            = $od_price - $od_receipt_price;
  $od_misu            = $tot_payment - $od_receipt_price;
  if ($od_misu == 0) {
    $od_status      = '입금';
  }
} else if ($od_settle_case == "가상계좌") {
  switch ($default['de_pg_service']) {
    case 'lg':
    include G5_SHOP_PATH . '/lg/xpay_result.php';
    break;
    case 'inicis':
    include G5_MSHOP_PATH . '/inicis/pay_result.php';
    break;
    default:
    include G5_MSHOP_PATH . '/kcp/pp_ax_hub.php';
    $bankname   = iconv("cp949", "utf-8", $bankname);
    $depositor  = iconv("cp949", "utf-8", $depositor);
    break;
  }

  //$od_receipt_point   = $od_temp_point;
  $od_tno             = $tno;
  $od_app_no          = $app_no;
  $od_receipt_price   = 0;
  //$od_bank_account    = $bankname . ' ' . $account;
  $od_bank_account    = $bankname . '/' . $account . '/' . $va_date; //입금은행/가상계좌번호/입금시한
  $od_deposit_name    = $depositor;
  $pg_price           = $amount;
  //$od_misu            = $od_price - $od_receipt_price;
  $od_misu            = $tot_payment - $od_receipt_price;
  $od_receipt_time    = '';
  $payment_request = true;
} else if ($od_settle_case == "휴대폰") {
  switch ($default['de_pg_service']) {
    case 'lg':
    include G5_SHOP_PATH . '/lg/xpay_result.php';
    break;
    case 'inicis':
    include G5_MSHOP_PATH . '/inicis/pay_result.php';
    break;
    default:
    include G5_MSHOP_PATH . '/kcp/pp_ax_hub.php';
    break;
  }

  $od_tno             = $tno;
  $od_receipt_price   = $amount;
  //$od_receipt_point   = $od_temp_point;
  $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
  $od_bank_account    = $commid . ' ' . $mobile_no;
  $pg_price           = $amount;
  //$od_misu            = $od_price - $od_receipt_price;
  $od_misu            = $tot_payment - $od_receipt_price;
  if ($od_misu == 0)
  $od_status      = '입금';
} else if ($od_settle_case == "신용카드") {
  switch ($default['de_pg_service']) {
    case 'lg':
    include G5_SHOP_PATH . '/lg/xpay_result.php';
    break;
    case 'inicis':
    include G5_MSHOP_PATH . '/inicis/pay_result.php';
    break;
    default:
    include G5_MSHOP_PATH . '/kcp/pp_ax_hub.php';
    $card_name  = iconv("cp949", "utf-8", $card_name);
    break;
  }

  $od_tno             = $tno;
  $od_app_no          = $app_no;
  $od_receipt_price   = $amount;
  //$od_receipt_point   = $od_temp_point;
  $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
  $od_bank_account    = $card_name;
  $pg_price           = $amount;
  //$od_misu            = $od_price - $od_receipt_price;
  $od_misu            = $tot_payment - $od_receipt_price;
  if ($od_misu == 0)
  $od_status      = '입금';
} else if ($od_settle_case == "간편결제") {
  switch ($default['de_pg_service']) {
    case 'lg':
    include G5_SHOP_PATH . '/lg/xpay_result.php';
    break;
    case 'inicis':
    include G5_MSHOP_PATH . '/inicis/pay_result.php';
    break;
    default:
    include G5_MSHOP_PATH . '/kcp/pp_ax_hub.php';
    $card_name  = iconv("cp949", "utf-8", $card_name);
    break;
  }

  $od_tno             = $tno;
  $od_app_no          = $app_no;
  $od_receipt_price   = $amount;
  //$od_receipt_point   = $od_temp_point;
  $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
  $od_bank_account    = $card_name;
  $pg_price           = $amount;
  //$od_misu            = $od_price - $od_receipt_price;
  $od_misu            = $tot_payment - $od_receipt_price;
  if ($od_misu == 0) {
    $od_status      = '입금';
  }
} else if (is_inicis_order_pay($od_settle_case)) {  //이니시스의 삼성페이 또는 L.pay 또는 이니시스 카카오페이
  // 이니시스에서만 지원
  include G5_MSHOP_PATH . '/inicis/pay_result.php';

  $od_tno             = $tno;
  $od_app_no          = $app_no;
  $od_receipt_price   = $amount;
  //$od_receipt_point   = $od_temp_point;
  $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
  $od_bank_account    = $card_name;
  $pg_price           = $amount;
  //$od_misu            = $od_price - $od_receipt_price;
  $od_misu            = $tot_payment - $od_receipt_price;
  if ($od_misu == 0) {
    $od_status      = '입금';
  }
} else if ($od_settle_case == "KAKAOPAY") {
  include G5_SHOP_PATH . '/kakaopay/kakaopay_result.php';

  $od_tno             = $tno;
  $od_app_no          = $app_no;
  $od_receipt_price   = $amount;
  //$od_receipt_point   = $od_temp_point;
  $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
  $od_bank_account    = $card_name;
  $pg_price           = $amount;
  //$od_misu            = $od_price - $od_receipt_price;
  $od_misu            = $tot_payment - $od_receipt_price;
  if ($od_misu == 0) {
    $od_status      = '입금';
  }
} else if ($od_settle_case == "TEST") { //jacknam
  //$od_receipt_point   = $od_temp_point;
  $od_receipt_price   = $tot_payment;
  $od_misu            = $tot_payment - $od_receipt_price;
  if($od_misu == 0) {
    $od_status      = '입금';
    $od_receipt_time = G5_TIME_YMDHIS;
  }
} else {
  //jacknam
  if ($tot_payment == 0) {
    $od_settle_case = 'NONE';
  } else {
    die("od_settle_case Error!!!");
  }
}

if ($od_misu < 0 || ($od_misu > 0 && $od_misu <= $od_receipt_point)) {
  $od_misu = 0;
  $od_status = '입금';
  if (!$od_receipt_time) {
    $od_receipt_time = G5_TIME_YMDHIS;
  }
}

$od_pg = $default['de_pg_service'];
//jacknam
if ($od_settle_case == 'KAKAOPAY') {
  $od_pg = 'KAKAOPAY';
} else if ($od_settle_case == 'NONE') {
  $od_pg = 'NONE';
}

// 주문금액과 결제금액이 일치하는지 체크
if ($tno) {
  if ((int)$tot_payment !== (int)$pg_price) {
    $cancel_msg = '결제금액 불일치';
    switch ($od_pg) {
      case 'lg':
      include G5_SHOP_PATH . '/lg/xpay_cancel.php';
      break;
      case 'inicis':
      include G5_SHOP_PATH . '/inicis/inipay_cancel.php';
      break;
      case 'KAKAOPAY':
      $_REQUEST['TID']               = $tno;
      $_REQUEST['Amt']               = $amount;
      $_REQUEST['CancelMsg']         = $cancel_msg;
      $_REQUEST['PartialCancelCode'] = 0;
      include G5_SHOP_PATH . '/kakaopay/kakaopay_cancel.php';
      break;
      default:
      include G5_SHOP_PATH . '/kcp/pp_ax_hub_cancel.php';
      break;
    }

    if (function_exists('add_order_post_log')) add_order_post_log($cancel_msg);
    die("Receipt Amount Error");
  }
}



if ($is_member) {
  $od_pwd = $member['mb_password'];
} else {
  $od_pwd = isset($_POST['od_pwd']) ? get_encrypt_string($_POST['od_pwd']) : get_encrypt_string(mt_rand());
}

// 주문번호를 얻는다.
$od_id = get_session('ss_order_id');

$od_escrow = 0;
if(isset($escw_yn) && $escw_yn === 'Y') {
  $od_escrow = 1;
}

// 복합과세 금액
$od_tax_mny = round($tot_payment / 1.1);
$od_vat_mny = $tot_payment - $od_tax_mny;
$od_free_mny = 0;
if($default['de_tax_flag_use']) {
  $od_tax_mny = isset($_POST['comm_tax_mny']) ? (int)$_POST['comm_tax_mny'] : 0;
  $od_vat_mny = isset($_POST['comm_vat_mny']) ? (int)$_POST['comm_vat_mny'] : 0;
  $od_free_mny = isset($_POST['comm_free_mny']) ? (int)$_POST['comm_free_mny'] : 0;
}

$od_email         = get_email_address($od_email);
$od_name          = clean_xss_tags($od_name);
$od_tel           = clean_xss_tags($od_tel);
$od_hp            = clean_xss_tags($od_hp);
//$od_hp            = clean_xss_tags($od_tel);
$od_zip           = preg_replace('/[^0-9]/', '', $od_zip);
$od_zip1          = substr($od_zip, 0, 3);
$od_zip2          = substr($od_zip, 3);
$od_addr1         = clean_xss_tags($od_addr1);
$od_addr2         = clean_xss_tags($od_addr2);
$od_addr3         = clean_xss_tags($od_addr3);
$od_addr_jibeon   = preg_match("/^(N|R)$/", $od_addr_jibeon) ? $od_addr_jibeon : '';
$od_b_name        = clean_xss_tags($od_b_name);
$od_b_tel         = clean_xss_tags($od_b_tel);
$od_b_hp          = clean_xss_tags($od_b_hp);
$od_b_addr1       = clean_xss_tags($od_b_addr1);
$od_b_addr2       = clean_xss_tags($od_b_addr2);
$od_b_addr3       = clean_xss_tags($od_b_addr3);
$od_b_addr_jibeon = preg_match("/^(N|R)$/", $od_b_addr_jibeon) ? $od_b_addr_jibeon : '';
$od_memo          = clean_xss_tags($od_memo, 1, 1, 0, 0);
$od_deposit_name  = clean_xss_tags($od_deposit_name);
$od_tax_flag      = $default['de_tax_flag_use'];

$od_name = $od_name ?: $od_b_name;
$od_tel = $od_tel ?: $od_b_tel;
$od_hp = $od_hp ?: $od_b_hp;
$od_addr1 = $od_addr1 ?: $od_b_addr1;
$od_addr2 = $od_addr2 ?: $od_b_addr2;
$od_addr3 = $od_addr3 ?: $od_b_addr3;
$od_addr_jibeon = $od_addr_jibeon ?: $od_b_addr_jibeon;

// 주문서에 입력
$sql = " insert {$g5['g5_shop_order_table']}
            set od_id             = '$od_id',
                mb_id             = '{$member['mb_id']}',
                od_pwd            = '$od_pwd',
                od_name           = '$od_name',
                od_email          = '$od_email',
                od_tel            = '$od_tel',
                od_hp             = '$od_hp',
                od_zip1           = '$od_zip1',
                od_zip2           = '$od_zip2',
                od_addr1          = '$od_addr1',
                od_addr2          = '$od_addr2',
                od_addr3          = '$od_addr3',
                od_addr_jibeon    = '$od_addr_jibeon',
                od_b_name         = '$od_b_name',
                od_b_tel          = '$od_b_tel',
                od_b_hp           = '$od_b_hp',
                od_b_zip1         = '$od_b_zip1',
                od_b_zip2         = '$od_b_zip2',
                od_b_addr1        = '$od_b_addr1',
                od_b_addr2        = '$od_b_addr2',
                od_b_addr3        = '$od_b_addr3',
                od_b_addr_jibeon  = '$od_b_addr_jibeon',
                od_deposit_name   = '$od_deposit_name',
                od_memo           = '$od_memo',
                od_cart_count     = '$cart_count',
                od_cart_price     = '$tot_ct_price',
                od_send_cost      = '$od_send_cost',
                od_send_coupon    = '$od_send_coupon',
                od_send_cost2     = '$od_send_cost2',
				        od_send_cost3     = '$od_send_cost3',
                od_cart_coupon    = '$od_cart_coupon',
                od_coupon         = '$tot_od_cp_price',
                od_receipt_price  = '$od_receipt_price',
                od_receipt_point  = '$od_receipt_point',
                od_bank_account   = '$od_bank_account',
                od_receipt_time   = '$od_receipt_time',
                od_misu           = '$od_misu',
                od_pg             = '$od_pg',
                od_tno            = '$od_tno',
                od_app_no         = '$od_app_no',
                od_escrow         = '$od_escrow',
                od_tax_flag       = '$od_tax_flag',
                od_tax_mny        = '$od_tax_mny',
                od_vat_mny        = '$od_vat_mny',
                od_free_mny       = '$od_free_mny',
                od_status         = '$od_status',
                od_shop_memo      = '',
                od_hope_date      = '$od_hope_date',
                od_time           = '".G5_TIME_YMDHIS."',
                od_ip             = '$REMOTE_ADDR',
                od_settle_case    = '$od_settle_case',
                od_other_pay_type = '$od_other_pay_type',
                od_test           = '{$default['de_card_test']}'
                ";
$result = sql_query($sql, false);

// 정말로 insert 가 되었는지 한번더 체크한다.
$exists_sql = "select od_id, od_tno, od_ip from {$g5['g5_shop_order_table']} where od_id = '$od_id'";
$exists_order = sql_fetch($exists_sql);

if (!$result && (isset($exists_order['od_id']) && $od_id && $exists_order['od_id'] === $od_id)) {
  if (isset($exists_order['od_tno']) && $exists_order['od_tno']) {
    //이미 상품이 주문되었다면 리다이렉트
    exists_inicis_shop_order($od_id, array(), $exists_order['od_time'], $REMOTE_ADDR);
    goto_url(G5_SHOP_URL);
  }
}

// 주문정보 입력 오류시 결제 취소
if (!$result || !(isset($exists_order['od_id']) && $od_id && $exists_order['od_id'] === $od_id)) {
  if ($tno) {
    $cancel_msg = '주문정보 입력 오류 : ' . $sql;
    switch ($od_pg) {
      case 'lg':
      include G5_SHOP_PATH . '/lg/xpay_cancel.php';
      break;
      case 'inicis':
      include G5_SHOP_PATH . '/inicis/inipay_cancel.php';
      break;
      case 'KAKAOPAY':
      $_REQUEST['TID']               = $tno;
      $_REQUEST['Amt']               = $amount;
      $_REQUEST['CancelMsg']         = $cancel_msg;
      $_REQUEST['PartialCancelCode'] = 0;
      include G5_SHOP_PATH . '/kakaopay/kakaopay_cancel.php';
      break;
      default:
      include G5_SHOP_PATH . '/kcp/pp_ax_hub_cancel.php';
      break;
    }
  }

  // 관리자에게 오류 알림 메일발송
  $error = 'order';
  include G5_SHOP_PATH . '/ordererrormail.php';

  if (function_exists('add_order_post_log')) add_order_post_log($cancel_msg);
  // 주문삭제
  sql_query(" delete from {$g5['g5_shop_order_table']} where od_id = '$od_id' ", false);

  die('<p>고객님의 주문 정보를 처리하는 중 오류가 발생해서 주문이 완료되지 않았습니다.</p><p>' . strtoupper($od_pg) . '를 이용한 전자결제(신용카드, 계좌이체, 가상계좌 등)은 자동 취소되었습니다.');
}

// 장바구니 상태변경
// 신용카드로 주문하면서 신용카드 포인트 사용하지 않는다면 포인트 부여하지 않음
$cart_status = $od_status;
$sql_card_point = "";
if ($od_receipt_price > 0 && !$default['de_card_point']) {
  $sql_card_point = " , ct_point = '0' ";
}
$sql = "update {$g5['g5_shop_cart_table']} set od_id = '$od_id', ct_status = '$cart_status' $sql_card_point
        where od_id = '$tmp_cart_id' and ct_select = '1' and ct_output = 'Y' ";
$result = sql_query($sql, false);

// 주문정보 입력 오류시 결제 취소
if (!$result) {
  if ($tno) {
    $cancel_msg = '주문상태 변경 오류';
    switch ($od_pg) {
      case 'lg':
        include G5_SHOP_PATH . '/lg/xpay_cancel.php';
        break;
      case 'inicis':
        include G5_SHOP_PATH . '/inicis/inipay_cancel.php';
        break;
      case 'KAKAOPAY':
        $_REQUEST['TID']               = $tno;
        $_REQUEST['Amt']               = $amount;
        $_REQUEST['CancelMsg']         = $cancel_msg;
        $_REQUEST['PartialCancelCode'] = 0;
        include G5_SHOP_PATH . '/kakaopay/kakaopay_cancel.php';
        break;
      default:
        include G5_SHOP_PATH . '/kcp/pp_ax_hub_cancel.php';
        break;
    }
  }

  // 관리자에게 오류 알림 메일발송
  $error = 'status';
  include G5_SHOP_PATH . '/ordererrormail.php';

  if (function_exists('add_order_post_log')) add_order_post_log($cancel_msg);
  // 주문삭제
  sql_query(" delete from {$g5['g5_shop_order_table']} where od_id = '$od_id' ");

  die('<p>고객님의 주문 정보를 처리하는 중 오류가 발생해서 주문이 완료되지 않았습니다.</p><p>' . strtoupper($od_pg) . '를 이용한 전자결제(신용카드, 계좌이체, 가상계좌 등)은 자동 취소되었습니다.');
}

//$od_memo = nl2br(htmlspecialchars2(stripslashes($od_memo))) . "&nbsp;";

set_session('s_cart_coupons', null);

// 쿠폰사용내역기록
if($is_member) {
  $update_cart = false;

  if ($tot_od_cp_price > 0) {
    foreach ($cp_id as $i => $id) {
      $price = isset($cp_id_price[$i]) ? $cp_id_price[$i] : 0;
      $sql = " insert into {$g5['g5_shop_coupon_log_table']}
      set cp_id   = '{$id}',
      mb_id       = '{$member['mb_id']}',
      od_id       = '{$od_id}',
      cp_price    = '{$price}',
      cl_datetime = '".G5_TIME_YMDHIS."' ";
      sql_query($sql);
    }
    // 쿠폰사용금액 cart에 기록
    $update_cart = true;
  }

  // 회원이면서 포인트를 사용했다면 테이블에 사용을 추가
  if ($od_receipt_point) {
    insert_point($member['mb_id'], (-1) * $od_receipt_point, "주문번호 $od_id 결제");
    $update_cart = true;
  }

  if ($update_cart) {
    $it_id = (isset($_POST['it_id']) && is_array($_POST['it_id'])) ? $_POST['it_id'] : array();
    $it_price = (isset($_POST['it_price']) && is_array($_POST['it_price'])) ? $_POST['it_price'] : array();

    $share_prices = [];
    $sqls = [];
    foreach($it_id as $i => $id) {
      $coupon_price = isset($cp_price[$i]) ? $cp_price[$i] : 0;
      $share_prices[] = isset($it_price[$i]) ? ($it_price[$i] - $coupon_price) : 0;

      $sqls[] = " update {$g5['g5_shop_cart_table']} set cp_price = '{$coupon_price}', ct_point = '0', ct_point_use = '0'
      where od_id = '{$od_id}' and it_id = '{$id}' and ct_select = '1' and ct_output = 'Y' order by ct_id asc limit 1 ";
    }

    $ct_point = [];
    if ($od_receipt_point > 0) {
      $ct_point = get_point_share($share_prices, $od_receipt_point);
    }

    foreach($sqls as $i => $sql) {
      if (isset($ct_point[$i]) && $ct_point[$i] > 0) {
        $sql = str_replace("ct_point_use = '0'", "ct_point_use = '1'", $sql);
        $sql = str_replace("ct_point = '0'", "ct_point = '{$ct_point[$i]}'", $sql);
      }
      sql_query($sql);
    }
  }
}

// 건강프로필 업데이트 jacknam
$hp_rsvt_date = $_POST['hp_rsvt_date'];
$hp_rsvt_stime = $_POST['hp_rsvt_stime'];
$hp_rsvt_etime = $_POST['hp_rsvt_etime'];
$hp_doc_name = $_POST['hp_doc_name'];

if ($hp_rsvt_date && $hp_rsvt_stime && $hp_rsvt_etime && $hp_doc_name) {
  $set_val = ", hp_rsvt_date = '{$hp_rsvt_date}', hp_rsvt_stime = '{$hp_rsvt_stime}', hp_rsvt_etime = '{$hp_rsvt_etime}', hp_doc_name = '{$hp_doc_name}'";
} else {
  $set_val = '';
}

$sql = " update {$g5['g5_shop_health_profile_cart_table']} set od_id = '$od_id', hp_status = '{$cart_status}' {$set_val} where od_id = '$tmp_cart_id' ";
$result = sql_query($sql, false);
if(!$result) {
  $sql = " update {$g5['g5_shop_health_profile_cart_table']} set od_id = '$od_id', hp_status = '{$cart_status}' {$set_val} where od_id = '$od_id' ";
  sql_query($sql);
}

// 상품목록
$sql = " select it_id, it_name from {$g5['g5_shop_cart_table']} where od_id = '$od_id' and ct_select = '1' and ct_output = 'Y' group by it_id order by ct_id ";
//echo $sql."<br>";
$result = sql_query($sql);
// 구매 제품을 돌리는데. 인플루언서 제품인지를 확인하는 방법은...
for($i=0; $row=sql_fetch_array($result); $i++) {
  // 상품정보
  $it = get_shop_item($row['it_id'], false);

  if(!$it['it_id']) continue;
  /*
  초진/재진여부
  - 장바구니 조건문으로는
  1. 주문번호가 달라야 한다.
  2. mb_id 가 같아야 한다.
  3. ct_status 값이 취소가 아니어야 하거나 준비,입금,배송,완료 등이어야 한다.
  4. ct_select 값이 1이어야 하고 ct_output 값이 Y 여야 한다
  5. 상품 it_id 값이
  * 주문시 건강프로필 조건문으로 대처
  */
  $hp_sql = " select hp_no from {$g5['g5_shop_health_profile_cart_table']} where mb_id = '{$member['mb_id']}' and od_id <> '$od_id' and (it_id = '{$it['it_id']}' or it_id = '{$it['it_org_id']}') and hp_status = '입금' limit 1  ";//select cf_phone from {$g5['sms5_config_table']} limit 1// and hp_output = 'Y'
  $hp_row = sql_fetch($hp_sql);
  //echo $hp_sql."<br>";

  if($hp_row['hp_no']) {// 있다면 업데이트
    $sql2 = " update {$g5['g5_shop_health_profile_cart_table']} set hp_8 = 'second' where od_id = '$od_id' and it_id = '{$row['it_id']}' and hp_status = '$cart_status' ";// and hp_output = 'Y'
    sql_query($sql2);
  }

  // 여기서 충전금 차감해보자.
  if(!$it['it_mb_inf']) continue;
  if($it['it_inf_price'] <= 0) continue;
  insert_charge($it['it_mb_inf'], '-'.(int)$it['it_inf_price'], $row['it_name'], $row['it_name'], '@item-'.$row['it_name'], $row['it_id'], $member['mb_id'].'-'.uniqid(''), '0', $od_id, $member['mb_name']);
}
//exit;
include_once(G5_SHOP_PATH.'/ordermail1.inc.php');
include_once(G5_SHOP_PATH.'/ordermail2.inc.php');


// AT(알림톡) BEGIN --------------------------------------------------------
// 주문고객과 쇼핑몰관리자에게 SMS 전송
// jacknam
if($config['cf_sms_use']) {
  if ($payment_request) {
    order_alimtalk('payment_request', $od_id);    
  } else {
    order_alimtalk('payment', $od_id);
    order_alimtalk('reservation', $od_id, [$default['de_sms_hp']]);    
  }
}
// jacknam
// AT(알림톡) END   --------------------------------------------------------

// orderview 에서 사용하기 위해 session에 넣고
$uid = md5($od_id.G5_TIME_YMDHIS.$REMOTE_ADDR);
set_session('ss_orderview_uid', $uid);

// 주문 정보 임시 데이터 삭제
//if($od_pg == 'inicis') {
  $sql = " delete from {$g5['g5_shop_order_data_table']} where od_id = '$od_id' and dt_pg = '$od_pg' ";
  sql_query($sql);
//}

if(function_exists('add_order_post_log')) add_order_post_log('', 'delete');

// 주문번호제거
set_session('ss_order_id', '');

// 기존자료 세션에서 제거
if (get_session('ss_direct')) {
  set_session('ss_cart_direct', '');
}

// 배송지처리
if($is_member) {
  $sql = " select * from {$g5['g5_shop_order_address_table']}
  where mb_id = '{$member['mb_id']}'
  and ad_name = '$od_b_name'
  and ad_tel = '$od_b_tel'
  and ad_hp = '$od_b_hp'
  and ad_zip1 = '$od_b_zip1'
  and ad_zip2 = '$od_b_zip2'
  and ad_addr1 = '$od_b_addr1'
  and ad_addr2 = '$od_b_addr2'
  and ad_addr3 = '$od_b_addr3' ";
  $row = sql_fetch($sql);

  // 기본배송지 체크
  if($ad_default) {
    $sql = " update {$g5['g5_shop_order_address_table']}
    set ad_default = '0'
    where mb_id = '{$member['mb_id']}' ";
    sql_query($sql);
  }

  $ad_subject = isset($_POST['ad_subject']) ? clean_xss_tags($_POST['ad_subject']) : '';

  if(isset($row['ad_id']) && $row['ad_id']){
    $sql = " update {$g5['g5_shop_order_address_table']}
    set ad_default = '$ad_default',
    ad_subject = '$ad_subject',
    ad_jibeon  = '$od_b_addr_jibeon'
    where mb_id = '{$member['mb_id']}'
    and ad_id = '{$row['ad_id']}' ";
  } else {
    $sql = " insert into {$g5['g5_shop_order_address_table']}
    set mb_id       = '{$member['mb_id']}',
    ad_subject  = '$ad_subject',
    ad_default  = '$ad_default',
    ad_name     = '$od_b_name',
    ad_tel      = '$od_b_tel',
    ad_hp       = '$od_b_hp',
    ad_zip1     = '$od_b_zip1',
    ad_zip2     = '$od_b_zip2',
    ad_addr1    = '$od_b_addr1',
    ad_addr2    = '$od_b_addr2',
    ad_addr3    = '$od_b_addr3',
    ad_jibeon   = '$od_b_addr_jibeon' ";
  }

  sql_query($sql);
}

$is_noti_pay = isset($is_noti_pay) ? $is_noti_pay : false;

if ($is_noti_pay) {
  $order_id = $od_id;
  return;
}

goto_url(G5_SHOP_URL . '/orderinquiryview.php?od_id=' . $od_id . '&amp;uid=' . $uid);
