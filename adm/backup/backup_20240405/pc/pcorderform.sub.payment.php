<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 결제대행사별 코드 include (결제대행사 정보 필드)
require_once(G5_SHOP_PATH.'/'.$default['de_pg_service'].'/orderform.2.php');

if($is_kakaopay_use) {
  require_once(G5_SHOP_PATH.'/kakaopay/orderform.2.php');
}
?>
<div class="order_write">
<h3>결제수단</h3>


<div class="payment_method">

<?php
/*
if (!$default['de_card_point'])
echo '<li id="sod_frm_pt_alert"><strong>무통장입금</strong> 이외의 결제 수단으로 결제하시는 경우 포인트를 적립해드리지 않습니다.</li>';
*/

$multi_settle = 0;
$checked = '';

$escrow_title = "";
if ($default['de_escrow_use']) {
  $escrow_title = "에스크로<br>";
}

if ($is_kakaopay_use || $default['de_bank_use'] || $default['de_vbank_use'] || $default['de_iche_use'] || $default['de_card_use'] || $default['de_hp_use'] || $default['de_easy_pay_use'] || $default['de_inicis_lpay_use'] || $default['de_inicis_kakaopay_use']) {
  echo '<div class="payment_method_list">';
}

// 카카오페이
if($is_kakaopay_use) {
  $multi_settle++;
  echo '<input type="radio" id="od_settle_kakaopay" name="od_settle_case" value="KAKAOPAY" '.$checked.'> <label for="od_settle_kakaopay" class="kakaopay_icon lb_icon">KAKAOPAY</label>'.PHP_EOL;
  $checked = '';
}

// 무통장입금 사용
if ($default['de_bank_use']) {
  $multi_settle++;
  echo '<input type="radio" id="od_settle_bank" name="od_settle_case" value="무통장" '.$checked.'> <label for="od_settle_bank" class="lb_icon bank_icon">무통장입금</label>'.PHP_EOL;
  $checked = '';
}

// 가상계좌 사용
if ($default['de_vbank_use']) {
  $multi_settle++;
  echo '<input type="radio" id="od_settle_vbank" name="od_settle_case" value="가상계좌" '.$checked.'> <label for="od_settle_vbank" class="lb_icon vbank_icon">'.$escrow_title.'가상계좌</label>'.PHP_EOL;
  $checked = '';
}

// 신용카드 사용
if ($default['de_card_use']) {
  $multi_settle++;
  echo '<div>'.PHP_EOL;
  echo '<label for="od_settle_card" class="check_order">'.PHP_EOL;
  echo '<img src="'.G5_IMG_URL.'/order/mo_payment_card.png" alt="카드아이콘">'.PHP_EOL;
  echo '</label>'.PHP_EOL;
  echo '<input type="radio" id="od_settle_card" name="od_settle_case" value="신용카드" '.$checked.'>'.PHP_EOL;
  echo '</div>'.PHP_EOL;
  $checked = '';
}

// 계좌이체 사용
if ($default['de_iche_use']) {
  $multi_settle++;
  echo '<div>'.PHP_EOL;
  echo '<label for="od_settle_iche" class="check_order">'.PHP_EOL;
  echo '<img src="'.G5_IMG_URL.'/order/mo_payment_cash.png" alt="계좌 아이콘">'.PHP_EOL;
  echo '</label>'.PHP_EOL;
  echo '<input type="radio" id="od_settle_iche" name="od_settle_case" value="계좌이체" '.$checked.'>'.PHP_EOL;
  echo '</div>'.PHP_EOL;
  $checked = '';
}

// 휴대폰 사용
if ($default['de_hp_use']) {
  $multi_settle++;
  echo '<input type="radio" id="od_settle_hp" name="od_settle_case" value="휴대폰" '.$checked.'> <label for="od_settle_hp" class="lb_icon hp_icon">휴대폰</label>'.PHP_EOL;
  $checked = '';
}

$easypay_prints = array();

// PG 간편결제
if($default['de_easy_pay_use']) {
  switch($default['de_pg_service']) {
    case 'lg':
    $pg_easy_pay_name = 'PAYNOW';
    break;
    case 'inicis':
    $pg_easy_pay_name = 'KPAY';
    break;
    default:
    $pg_easy_pay_name = 'PAYCO';
    break;
  }

  $multi_settle++;

  if($default['de_pg_service'] === 'kcp' && isset($default['de_easy_pay_services']) && $default['de_easy_pay_services']){
    $de_easy_pay_service_array = explode(',', $default['de_easy_pay_services']);
    if( in_array('nhnkcp_payco', $de_easy_pay_service_array) ){
      $easypay_prints['nhnkcp_payco'] = '<input type="radio" id="od_settle_nhnkcp_payco" name="od_settle_case" data-pay="payco" value="간편결제"> <label for="od_settle_nhnkcp_payco" class="PAYCO nhnkcp_payco lb_icon" title="NHN_KCP - PAYCO">PAYCO</label>';
    }
    if( in_array('nhnkcp_naverpay', $de_easy_pay_service_array) ){
      $easypay_prints['nhnkcp_naverpay'] = '<input type="radio" id="od_settle_nhnkcp_naverpay" name="od_settle_case" data-pay="naverpay" value="간편결제" > <label for="od_settle_nhnkcp_naverpay" class="naverpay_icon nhnkcp_naverpay lb_icon" title="NHN_KCP - 네이버페이">네이버페이</label>';
    }
    if( in_array('nhnkcp_kakaopay', $de_easy_pay_service_array) ){
      $easypay_prints['nhnkcp_kakaopay'] = '<input type="radio" id="od_settle_nhnkcp_kakaopay" name="od_settle_case" data-pay="kakaopay" value="간편결제" > <label for="od_settle_nhnkcp_kakaopay" class="kakaopay_icon nhnkcp_kakaopay lb_icon" title="NHN_KCP - 카카오페이">카카오페이</label>';
    }
  } else {
    $easypay_prints[strtolower($pg_easy_pay_name)] = '<input type="radio" id="od_settle_easy_pay" name="od_settle_case" value="간편결제"> <label for="od_settle_easy_pay" class="'.$pg_easy_pay_name.' lb_icon">'.$pg_easy_pay_name.'</label>';
  }
}

if( ! isset($easypay_prints['nhnkcp_naverpay']) && function_exists('is_use_easypay') && is_use_easypay('global_nhnkcp') ){
  $easypay_prints['nhnkcp_naverpay'] = '<input type="radio" id="od_settle_nhnkcp_naverpay" name="od_settle_case" data-pay="naverpay" value="간편결제" > <label for="od_settle_nhnkcp_naverpay" class="naverpay_icon nhnkcp_naverpay lb_icon" title="NHN_KCP - 네이버페이">네이버페이</label>';
}

if($easypay_prints) {
  $multi_settle++;
  echo run_replace('shop_orderform_easypay_buttons', implode(PHP_EOL, $easypay_prints), $easypay_prints, $multi_settle);
}

//이니시스 Lpay
if($default['de_inicis_lpay_use']) {
  echo '<input type="radio" id="od_settle_inicislpay" data-case="lpay" name="od_settle_case" value="lpay" '.$checked.'> <label for="od_settle_inicislpay" class="inicis_lpay lb_icon">L.pay</label>'.PHP_EOL;
  $checked = '';
}

//이니시스 카카오페이
if(isset($default['de_inicis_kakaopay_use']) && $default['de_inicis_kakaopay_use']) {
  echo '<input type="radio" id="od_settle_inicis_kakaopay" data-case="inicis_kakaopay" name="od_settle_case" value="inicis_kakaopay" '.$checked.' title="KG 이니시스 카카오페이"> <label for="od_settle_inicis_kakaopay" class="inicis_kakaopay lb_icon">KG 이니시스 카카오페이<em></em></label>'.PHP_EOL;
  $checked = '';
}


if ($is_kakaopay_use || $default['de_bank_use'] || $default['de_vbank_use'] || $default['de_iche_use'] || $default['de_card_use'] || $default['de_hp_use'] || $default['de_easy_pay_use'] || $default['de_inicis_lpay_use'] || $default['de_inicis_kakaopay_use'] ) {
  echo '</div>';
}
?>
<div class="card_add">
<!--<div onclick="card_add()" id="card_add_btn">+ 카드등록</div>-->
<div id="card_add_layout">
</div>
<!-- <p>* <span class="bold point_color">현대카드사</span>는 결제가 불가합니다.</p> -->
<p>* 할부 결제는 일반카드 결제만 가능합니다.</p>
<p>* 최소 결제금액은 1,000원입니다.</p>
</div>
</div>

<?php
if ($default['de_bank_use']) {
  // 은행계좌를 배열로 만든후
  $str = explode("\n", trim($default['de_bank_account']));
  if (count($str) <= 1)
  {
    $bank_account = '<input type="hidden" name="od_bank_account" value="'.$str[0].'">'.$str[0].PHP_EOL;
  }
  else
  {
    $bank_account = '<select name="od_bank_account" id="od_bank_account">'.PHP_EOL;
    $bank_account .= '<option value="">선택하십시오.</option>';
    for ($i=0; $i<count($str); $i++)
    {
      //$str[$i] = str_replace("\r", "", $str[$i]);
      $str[$i] = trim($str[$i]);
      $bank_account .= '<option value="'.$str[$i].'">'.$str[$i].'</option>'.PHP_EOL;
    }
    $bank_account .= '</select>'.PHP_EOL;
  }
  echo '<div id="settle_bank" style="display:none">';
  echo '<label for="od_bank_account" class="sound_only">입금할 계좌</label>';
  echo $bank_account;
  echo '<br><label for="od_deposit_name">입금자명</label> ';
  echo '<input type="text" name="od_deposit_name" id="od_deposit_name" size="10" maxlength="20">';
  echo '</div>';
}

if ($multi_settle == 0)
echo '<p>결제할 방법이 없습니다.<br>운영자에게 알려주시면 감사하겠습니다.</p>';
?>
</div>