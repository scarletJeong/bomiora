<?php
include_once('./_common.php');

//var_dump($config['cf_use_point'], $member['mb_point'],$default['de_settle_min_point']);

// add_javascript('js 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js

// 주문상품 재고체크 js 파일
add_javascript('<script src="'.G5_JS_URL.'/shop.order.js"></script>', 0);

$sw_direct = isset($_REQUEST['sw_direct']) ? preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['sw_direct']) : '';

// 모바일 주문인지
$is_mobile_order = is_mobile();

set_session("ss_direct", $sw_direct);
// 장바구니가 비어있는가?
if ($sw_direct) {
  $tmp_cart_id = get_session('ss_cart_direct');
}
else {
  $tmp_cart_id = get_session('ss_cart_id');
}

if (get_cart_count($tmp_cart_id) == 0) {
  alert('장바구니가 비어 있습니다.', G5_SHOP_URL.'/cart.php');
}

if (function_exists('before_check_cart_price')) {
  if(! before_check_cart_price($tmp_cart_id) ) alert('장바구니 금액에 변동사항이 있습니다.\n장바구니를 다시 확인해 주세요.', G5_SHOP_URL.'/cart.php');
}

//jacknam
$s_cart_coupons = get_session('s_cart_coupons');
if ($s_cart_coupons) {
  //set_session('s_cart_coupons', null);
  if ($s_cart_coupons['tmp_cart_id'] != $tmp_cart_id) {
    $s_cart_coupons = null;
  } else {
    //쿠폰 검증 코드 필요
  }
}
//var_dump($s_cart_coupons);
//exit;

// 새로운 주문번호 생성
$od_id = get_uniqid();
set_session('ss_order_id', $od_id);
$s_cart_id = $tmp_cart_id;
if($default['de_pg_service'] == 'inicis' || $default['de_inicis_lpay_use'] || $default['de_inicis_kakaopay_use']) {
  set_session('ss_order_inicis_id', $od_id);
}

$tot_price = 0;

$g5['title'] = '주문서 작성';

include_once(G5_SHOP_PATH.'/_head.php');

// 희망배송일 지정
if ($default['de_hope_date_use']) {
  include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');
}

//$is_mobile_order = true;
// 기기별 주문폼 include
if($is_mobile_order) {
  $order_action_url = G5_HTTPS_SHOP_URL.'/moorderformupdate.php';
  require_once(G5_SHOP_PATH.'/moorderform.sub.php');
} else {
  $order_action_url = G5_HTTPS_SHOP_URL.'/pcorderformupdate.php';
  //echo G5_SHOP_PATH.'/pcoderform.sub.php';
  require_once(G5_SHOP_PATH.'/pcorderform.sub.php');
}

include_once(G5_SHOP_PATH.'/_tail.php');

?>
<style>
  .postfix_won::after {
    content: " 원";
  }
  
  .postfix_point::after {
    content: " 점";
  }
  
  .selected_object .discount_info {
    width: 20%;
    text-align: center;
    margin-left: auto;
  }
  
  .coupon_info {
    padding: 10px;
  }
  
  .coupon_info span:first-child {
    margin-right: 10px;
  }
</style>