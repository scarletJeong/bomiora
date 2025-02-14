<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

require_once(G5_MSHOP_PATH . '/settle_' . $default['de_pg_service'] . '.inc.php');
require_once(G5_SHOP_PATH . '/settle_kakaopay.inc.php');

if (is_inicis_simple_pay()) {   //이니시스 삼성페이 또는 Lpay 사용시
  require_once(G5_MSHOP_PATH . '/samsungpay/incSamsungpayCommon.php');
}

if (function_exists('is_use_easypay') && is_use_easypay('global_nhnkcp')) {  // 타 PG 사용시 NHN KCP 네이버페이 사용이 설정되어 있다면
  require_once(G5_MSHOP_PATH . '/kcp/global_m_nhn_kcp.php');
}

$tablet_size = "1.0"; // 화면 사이즈 조정 - 기기화면에 맞게 수정(갤럭시탭,아이패드 - 1.85, 스마트폰 - 1.0)

// 개인결제번호제거
set_session('ss_personalpay_id', '');
set_session('ss_personalpay_hash', '');

// jacknam

// jacknam
$payment_test = false;

$tot_point = 0;
$tot_sell_price = 0;

$goods = $goods_it_id = "";
$goods_count = -1;

// $s_cart_id 로 현재 장바구니 자료 쿼리
$cart_list = get_cart_list($member['mb_id'], $s_cart_id, 'payment', true);
$rsvt_info = get_rsvt_info($cart_list, 1800, true);
$point_disable = is_coupon_point_disable($cart_list, 'point');
$total_count = count($cart_list);

if ($total_count == 0) {
  alert('결제할 상품이 없습니다.', G5_SHOP_URL.'/cart.php');
}

$total_discount = 0;
$send_cost_discount = 0;
$cp_prices = [];
$cp_infos = [];
if ($s_cart_coupons) {
  if (isset($s_cart_coupons['cp_id_price'])) {
    $cp_infos = get_coupon_infos(array_keys($s_cart_coupons['cp_id_price']));
  }

  if ($cp_infos) {
    if (isset($s_cart_coupons['cp_info'])) {
      if (isset($s_cart_coupons['cp_info']['total_discount'])) {
        $total_discount = $s_cart_coupons['cp_info']['total_discount'];
      }

      if (isset($s_cart_coupons['cp_info']['send_cost_discount'])) {
        $send_cost_discount = $s_cart_coupons['cp_info']['send_cost_discount'];
      }
    }

    if (isset($s_cart_coupons['cp_price'])) {
      $cp_prices = $s_cart_coupons['cp_price'];
    }

    $cp_id_prices = $s_cart_coupons['cp_id_price'];
  }
}

$good_info = '';
$it_send_cost = 0;
$it_cp_count = 0;

$comm_tax_mny = 0; // 과세금액
$comm_vat_mny = 0; // 부가세
$comm_free_mny = 0; // 면세금액
$tot_tax_mny = 0;

ob_start();

foreach ($cart_list as $i => $row) {
  $price = ($row['ct_price'] + $row['io_price']) * $row['ct_qty'];
  $sum = array('price' => $price, 'point' => ($row['ct_point'] * $row['ct_qty']), 'qty' => $row['ct_qty']);

  if (!$goods) {
    $goods = preg_replace("/\'|\"|\||\,|\&|\;/", "", $row['it_name']);
    $goods_it_id = $row['it_id'];
  }

  $goods_count++;

  // 에스크로 상품정보
  if($default['de_escrow_use']) {
    if ($i > 0) {
      $good_info .= chr(30);
    }
    $good_info .= "seq=".($i+1).chr(31);
    $good_info .= "ordr_numb={$od_id}_".sprintf("%04d", $i).chr(31);
    $good_info .= "good_name=".addslashes($row['it_name']).chr(31);
    $good_info .= "good_cntx=".$row['ct_qty'].chr(31);
    $good_info .= "good_amtx=".$row['ct_price'].chr(31);
  }

  $image = get_it_image($row['it_id'], 200, 200);

  $it_name = '<b>' . stripslashes($row['it_name']) . '</b>';
  if ($row['ct_option']) {
    $it_name .= '<div class="sod_opt"><ul><li>'.$row['ct_option'].'</li><li>옵션수량: '.$row['ct_qty'].'개 (+'.number_format($row['io_price']).')</li></ul></div>';
  }

  // 복합과세금액
  if ($default['de_tax_flag_use']) {
    if ($row['it_notax']) {
      $comm_free_mny += $sum['price'];
    } else {
      $tot_tax_mny += $sum['price'];
    }
  }

  $point      = $sum['point'];
  $sell_price = $sum['price'];

  $cp_button = '';
  // 쿠폰
  if ($is_member) {
    $cp_count = 0;

    $sql = " select cp_id
					from {$g5['g5_shop_coupon_table']}
					where mb_id IN ( '{$member['mb_id']}', '전체회원' )
						and cp_start <= '" . G5_TIME_YMD . "'
						and cp_end >= '" . G5_TIME_YMD . "'
						and cp_minimum <= '$sell_price'
						and (
								( cp_method = '0' and cp_target = '{$row['it_id']}' )
								OR
								( cp_method = '1' and ( cp_target IN ( '{$row['ca_id']}', '{$row['ca_id2']}', '{$row['ca_id3']}' ) ) )
						) ";
    $res = sql_query($sql);

    for ($k = 0; $cp = sql_fetch_array($res); $k++) {
      if (is_used_coupon($member['mb_id'], $cp['cp_id']))
        continue;

      $cp_count++;
    }

    if ($cp_count) {
      $cp_button = '<div class="li_cp"><button type="button" class="cp_btn">쿠폰적용</button></div>';
      $it_cp_count++;
    }
  }

  // 배송비
  switch ($row['ct_send_cost']) {
    case 1:
      $ct_send_cost = '착불';
      break;
    case 2:
      $ct_send_cost = '무료';
      break;
    default:
      $ct_send_cost = '선불';
      break;
  }

  // 조건부무료
  if ($row['it_sc_type'] == 2) {
    $sendcost = get_item_sendcost($row['it_id'], $sum['price'], $sum['qty'], $s_cart_id);

    if ($sendcost == 0)
      $ct_send_cost = '무료';
  }

  //jacknam
  $cp_price = 0;
  if(isset($cp_prices[$row['it_id']])) {
    $cp_price = $cp_prices[$row['it_id']];
  }
?>
  <div class="selected_object">
    <div class="selected_object_img">
      <?php echo $image; ?>
    </div>
    <div>
      <ul class="selected_object_info">
        <input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
        <input type="hidden" name="it_name[<?php echo $i; ?>]" value="<?php echo get_text($row['it_name']); ?>">
        <input type="hidden" name="it_price[<?php echo $i; ?>]" value="<?php echo $sell_price; ?>">
        <input type="hidden" name="cp_price[<?php echo $i; ?>]" value="<?php echo $cp_price; ?>">
        <?php if ($default['de_tax_flag_use']) { ?>
          <input type="hidden" name="it_notax[<?php echo $i; ?>]" value="<?php echo $row['it_notax']; ?>">
        <?php } ?>
        <li><?php echo $it_name; ?></li>
        <li>수량 : <?php echo number_format($sum['qty']); ?></li>
        <?php
        if($row['it_kind'] == 'prescription') {//처방제품이라면
          if ($rsvt_info) {
        ?>
        <li>진화진료 예약시간 : <?php echo date("m.d", strtotime($rsvt_info['hp_rsvt_date'])) ?>(<?php echo get_yoil($rsvt_info['hp_rsvt_date']) ?>)<?php echo get_text($rsvt_info['hp_rsvt_stime']) ?> ~ <?php echo get_text($rsvt_info['hp_rsvt_etime']) ?></li>
        <?php } else { ?>
        <li class='norsvt'><p>진화진료 예약정보 없음</p></li>
        <?php }
        } ?>
        <li>
          <?php //echo "( " . number_format($row['ct_price']) . "원 + " . number_format($row['io_price']) . "원 ) X {$row['ct_qty']}개 ="; ?>
          <?php echo number_format($sell_price); ?>원
        </li>
        <?php if ($row['it_icon2']) { ?>
          <li>
            <span class="pink_meno">오늘배송</span>
          </li>
        <?php } ?>

        <?php if ($cp_price > 0) { ?>
          <li class="coupon_info">
            <span>쿠폰할인</span>
            <span class="postfix_won">-<?php echo number_format($cp_price); ?></span>
          </li>
        <?php } ?>
      </ul>
    </div>


  </div>
<?php
  $tot_point      += $point;
  $tot_sell_price += $sell_price;
} // for 끝

// 배송비 계산
$send_cost = get_sendcost($s_cart_id);

// 복합과세처리
if ($default['de_tax_flag_use']) {
  $comm_tax_mny = round(($tot_tax_mny + $send_cost) / 1.1);
  $comm_vat_mny = ($tot_tax_mny + $send_cost) - $comm_tax_mny;
}
?>
<?php if ($goods_count) $goods .= ' 외 ' . $goods_count . '건'; ?>

<?php
$content = ob_get_contents();
ob_end_clean();

// 결제대행사별 코드 include (결제등록 필드)
require_once(G5_MSHOP_PATH . '/' . $default['de_pg_service'] . '/orderform.1.php');

if (is_inicis_simple_pay()) {   //이니시스 삼성페이 또는 lpay 사용시
  require_once(G5_MSHOP_PATH . '/samsungpay/orderform.1.php');
}

if (function_exists('is_use_easypay') && is_use_easypay('global_nhnkcp')) {  // 타 PG 사용시 NHN KCP 네이버페이 사용이 설정되어 있다면
  require_once(G5_MSHOP_PATH . '/kcp/easypay_form.1.php');
}
?>
<!--</div>-->

<?php
if ($is_kakaopay_use) {
  require_once(G5_SHOP_PATH . '/kakaopay/orderform.1.php');
}
?>
<div>
  <div class="order">
    <h2 class="order_title">
      주문 / 결제
    </h2>
    <form name="forderform" method="post" action="<?php echo $order_action_url; ?>" autocomplete="off">
      <input type="hidden" name="od_price" value="<?php echo $tot_sell_price; ?>">
      <input type="hidden" name="org_od_price" value="<?php echo $tot_sell_price; ?>">
      <input type="hidden" name="od_send_cost" value="<?php echo $send_cost; ?>">
      <input type="hidden" name="od_send_cost2" value="0">
      <input type="hidden" name="item_coupon" value="0">
      <input type="hidden" name="od_coupon" value="0">
      <!-- // <input type="hidden" name="od_send_coupon" value="0"> -->

  <!-- // jacknam pcorderform.sub.payment.php start -->
  <input type="hidden" name="od_cp_price" value="<?php echo $total_discount; ?>">
  <input type="hidden" name="od_send_coupon" value="<?php echo $send_cost_discount; ?>">
  <?php
  //$tot_price = $tot_sell_price + $send_cost; // 총계 = 주문상품금액합계 + 배송비
  $tot_price = $tot_sell_price + $send_cost - $total_discount - $send_cost_discount; // 총계 = 주문상품금액합계 + 배송비
  ?>
  <input type="hidden" name="tot_price" value="<?php echo $tot_price; ?>">
  <!--// jacknam payment.sub.inc end -->

  <input type="hidden" name="hp_rsvt_date" value="<?php echo $rsvt_info['hp_rsvt_date']; ?>">
  <input type="hidden" name="hp_rsvt_stime" value="<?php echo $rsvt_info['hp_rsvt_stime']; ?>">
  <input type="hidden" name="hp_rsvt_etime" value="<?php echo $rsvt_info['hp_rsvt_etime']; ?>">
  <input type="hidden" name="hp_doc_name" value="<?php echo $rsvt_info['hp_doc_name']; ?>">

      <!-- 추가 -->
      <input type="hidden" name="od_name" value="">
      <input type="hidden" name="od_tel" value="">
      <input type="hidden" name="od_hp" value="">
      <input type="hidden" name="od_zip" value="">
      <input type="hidden" name="od_addr1" value="">
      <input type="hidden" name="od_addr2" value="">
      <input type="hidden" name="od_addr3" value="">
      <input type="hidden" name="od_addr_jibeon" value="">
      <input type="hidden" name="od_email" value="<?php echo $member['mb_email']; ?>">
      <input type="hidden" name="od_b_hp" value="">
      <div class="order_write">
        <h3>배송지</h3>
        <ul class="order_write_list">
          <?php
          $addr_list = '';
          if ($is_member) {
            // 배송지 이력
            $sep = chr(30);

            // 주문자와 동일
            //$addr_list .= '<input type="radio" name="ad_sel_addr" value="same" id="ad_sel_addr_same">'.PHP_EOL;
            //$addr_list .= '<label for="ad_sel_addr_same">주문자와 동일</label>'.PHP_EOL;

            // 기본배송지
            $sql = " select *
									from {$g5['g5_shop_order_address_table']}
									where mb_id = '{$member['mb_id']}'
										and ad_default = '1' ";
            $row = sql_fetch($sql);
            if (isset($row['ad_id']) && $row['ad_id']) {
              $val1 = $row['ad_name'] . $sep . $row['ad_tel'] . $sep . $row['ad_hp'] . $sep . $row['ad_zip1'] . $sep . $row['ad_zip2'] . $sep . $row['ad_addr1'] . $sep . $row['ad_addr2'] . $sep . $row['ad_addr3'] . $sep . $row['ad_jibeon'] . $sep . $row['ad_subject'];
              $addr_list .= '<br><input type="radio" name="ad_sel_addr" value="' . get_text($val1) . '" id="ad_sel_addr_def">' . PHP_EOL;
              $addr_list .= '<label for="ad_sel_addr_def">기본배송지</label>' . PHP_EOL;
            }

            // 최근배송지
            $sql = " select *
									from {$g5['g5_shop_order_address_table']}
									where mb_id = '{$member['mb_id']}'
										and ad_default = '0'
									order by ad_id desc
									limit 1 ";
            $result = sql_query($sql);
            for ($i = 0; $row = sql_fetch_array($result); $i++) {
              $val1 = $row['ad_name'] . $sep . $row['ad_tel'] . $sep . $row['ad_hp'] . $sep . $row['ad_zip1'] . $sep . $row['ad_zip2'] . $sep . $row['ad_addr1'] . $sep . $row['ad_addr2'] . $sep . $row['ad_addr3'] . $sep . $row['ad_jibeon'] . $sep . $row['ad_subject'];
              $val2 = '<label for="ad_sel_addr_' . ($i + 1) . '">최근배송지(' . ($row['ad_subject'] ? get_text($row['ad_subject']) : get_text($row['ad_name'])) . ')</label>';
              $addr_list .= '<br><input type="radio" name="ad_sel_addr" value="' . get_text($val1) . '" id="ad_sel_addr_' . ($i + 1) . '"> ' . PHP_EOL . $val2 . PHP_EOL;
            }

            $addr_list .= '<br><input type="radio" name="ad_sel_addr" value="new" id="od_sel_addr_new">' . PHP_EOL;
            $addr_list .= '<label for="od_sel_addr_new">신규배송지</label>' . PHP_EOL;

            //$addr_list .='<a href="'.G5_SHOP_URL.'/orderaddress.php" id="order_address">배송지목록</a>';
          } else {
            // 주문자와 동일
            //$addr_list .= '<input type="checkbox" name="ad_sel_addr" value="same" id="ad_sel_addr_same">'.PHP_EOL;
            //$addr_list .= '<label for="ad_sel_addr_same">주문자와 동일</label>'.PHP_EOL;
          }
          ?>
          <li>
            <h2>배송지선택</h2>
            <div class="write_text">
              <?php echo $addr_list; ?>
            </div>
          </li>
          <?php if ($is_member) { ?>
            <li>
              <h2>배송지명</h2>
              <div class="write_text">
                <input type="text" name="ad_subject" value="" id="ad_subject" maxlength="20">
              </div>
            </li>
          <?php } ?>
          <li>
            <h2>수령인 <span class="required_star">*</span></h2>
            <div class="write_text">
              <input type="text" name="od_b_name" value="" id="od_b_name" maxlength="20">
            </div>
          </li>
          <li>
            <h2>핸드폰 번호 <span class="required_star">*</span></h2>
            <div class="write_text">
              <input type="text" name="od_b_tel" value="" id="od_b_tel" maxlength="20">
            </div>
          </li>
          <li>
            <h2>우편번호 <span class="required_star">*</span></h2>
            <div class="write_text mail_re">
              <input type="text" name="od_b_zip" id="od_b_zip" maxlength="6" readonly>
              <button type="button" class="mail_code" onclick="win_zip('forderform', 'od_b_zip', 'od_b_addr1', 'od_b_addr2', 'od_b_addr3', 'od_b_addr_jibeon');">주소 검색</button><br>
            </div>
          </li>
          <li>
            <h2>주소 검색 <span class="required_star">*</span></h2>
            <div class="write_text">
              <input type="text" name="od_b_addr1" id="od_b_addr1">
            </div>
          </li>
          <li>
            <h2>상세주소<span class="required_star">*</span></h2>
            <div class="write_text">
              <input type="text" name="od_b_addr2" id="od_b_addr2">
              <input type="hidden" name="od_b_addr3" id="od_b_addr3">
              <input type="hidden" name="od_b_addr_jibeon" value="">
              <?php if ($is_member) { ?>
                <div class="write_box">
                  <input type="checkbox" name="ad_default" id="ad_default" value="1">
                  <label for="ad_default"><span> 기본 배송지로 저장</span></label>
                </div>
              <?php } ?>
            </div>
          </li>
          <li>
            <h2>배송요청사항</h2>
            <div class="write_text">
              <input type="text" name="od_memo" id="od_memo">
              <div class="write_box">
                <input type="checkbox" id="today_delivery">
                <!--
								<label for="today_delivery">
									<span class="today_delivery_text"> 오늘 배송</span>
								</label>
								-->
                <!-- <p>* 한의약품은 오후 12시 전 "처방완료건", 건강식품은 "결제완료건"에 한하여 서울 지역만 저녁 8시~새벽 2시 사이 배송됩니다.</p>
								<p>* 대학교/회사/상가/백화점/대형시장은 오늘배송이 불가합니다.</p> -->
                <p>* 영업일 기준 오후 2시 이전 처방완료 시 당일 발송</p>
              </div>
            </div>
          </li>
        </ul>
      </div><!-- //order_write -->



      <div class="order_write">
        <!--// jacknam -->
        <h3>
          결제 예정 목록 <?php echo $total_count ?>건
        </h3>
        <?php echo $content; ?>
      </div>

      <!-- // jacknam moorderform.sub.payment.php start -->
      <?php
      if ($tot_price != 0) {
        require_once(G5_SHOP_PATH . '/moorderform.sub.payment.php');
      }
      ?>
      <!--// jacknam payment.sub.inc end -->
      <?php

$temp_point = 0;
// 회원이면서 포인트사용이면
if ($is_member && !$point_disable) {
  // 포인트 결제 사용 포인트보다 회원의 포인트가 크다면
  if ($member['mb_point'] >= $default['de_settle_min_point'])
  {
    $temp_point = (int)$default['de_settle_max_point'];
    if($temp_point > (int)$tot_price)
    $temp_point = (int)$tot_price;

    if($temp_point > (int)$member['mb_point'])
    $temp_point = (int)$member['mb_point'];

    $point_unit = (int)$default['de_settle_point_unit'];
    $temp_point = (int)((int)($temp_point / $point_unit) * $point_unit);

    $point_list = [];
    $v = 0;
    for ($i=0; $i < $temp_point / $point_unit; $i++) {
      if ($i < 5) {
        $v = ($i + 1) * $point_unit;
      } else if ($i < 10) {
        $v = ($i - 3) * 500;
      } else {
        $v = (($i - 6) * 1000);
      }
      if ($v > $temp_point) {
        break;
      }
      $point_list[] = $v;
    }
    if ($point_list) {
    ?>

<div class="order_write coupon_list">
  <input type="hidden" name="max_temp_point" value="<?php echo $temp_point; ?>">
  <h3 class="amount">
    포인트
  </h3>
  <ul class="amount_list select_point" data-mb_point="<?php echo $member['mb_point']; ?>" data-max_point="<?php echo $temp_point; ?>">
    <li>
      <p>
        보유포인트&nbsp;<span class="postfix_point"><?php echo number_format($member['mb_point']); ?></span>
        <br />
        최대 사용가능 포인트&nbsp;<span class="postfix_point"><?php echo number_format($temp_point); ?></span>
      </p>
      <p>
        <span>사용 포인트&nbsp;(<?php echo $point_unit; ?>점 단위)</span>
        <br />
        <select name="od_temp_point" id="od_temp_point" onchange="calculate_point()">
          <option value="0">사용안함</option>
          <?php foreach ($point_list as $val) { ?>
          <option value="<?php echo $val; ?>"><?php echo number_format($val); ?>점 사용</option>
          <?php } ?>
          <option value="<?php echo $temp_point; ?>" selected>모두사용</option>
        </select>
      </p>
    </li>
  </ul>
</div>
    <?php
    }
  }
}
?>

<?php if ($cp_infos) {
$cp_method_title = ['상품쿠폰','카테고리쿠폰','주문할인쿠폰','배송비쿠폰'];
?>
<div class="order_write coupon_list">
  <h3 class="amount">
    적용 쿠폰
  </h3>
  <div>
    <ul class="amount_list">
      <?php foreach ($cp_infos as $i => $info) { ?>
      <input type="hidden" name="cp_id[<?php echo $i; ?>]" value="<?php echo $info['cp_id']; ?>">
      <input type="hidden" name="cp_id_price[<?php echo $i; ?>]" value="<?php echo (isset($cp_id_prices[$info['cp_id']]) ? $cp_id_prices[$info['cp_id']] : 0); ?>">
      <li data-cp_method="<?php echo $cp_method_title[(int)$info['cp_method']]; ?>"
        data-cp_price="<?php echo number_format($info['cp_price']) . ($info['cp_type'] == '1' ? '%' : '원'); ?>"
        data-cp_maximum="<?php echo ($info['cp_maximum'] == '0') ? '최대 제한없음' : number_format($info['cp_maximum']) . ' 원'; ?>">
        <p class="subject"><?php echo $info['cp_subject']; ?></p>
      </li>
      <?php } ?>
    </ul>
    <h3 class="amount">
      <div>쿠폰 할인금액</div>
      <div class="postfix_won"><strong class="print_price"><?php echo number_format($total_discount + $send_cost_discount); ?></strong></div>
    </h3>
  </div>
</div>
<?php } ?>

<div class="order_write">
  <h3 class="amount">
    결제 금액
  </h3>
  <div>
    <ul class="amount_list">
      <li>
        <p>구매금액</p>
        <p class="postfix_won"><?php echo number_format($tot_sell_price); ?></p>
      </li>

      <?php if($total_discount > 0) { ?>
      <li>
      <p>쿠폰할인</p>
      <p class="postfix_won"><strong id="ct_tot_coupon">-<?php echo number_format($total_discount); ?></strong></p>
      </li>
      <?php } ?>
      <li class="send_cost_display">
        <p>배송비</p>
        <p class="postfix_won"><?php echo number_format($send_cost); ?></p>
      </li>
      <?php if($oc_cnt > 0) { ?>
      <input type="hidden" name="od_cp_id" value="">
      <?php } ?>
      <?php if($sc_cnt > 0) { ?>
      <input type="hidden" name="sc_cp_id" value="">
      <?php } ?>
      <?php if($send_cost_discount > 0) { ?>
      <li>
      <p>배송비쿠폰할인</p>
      <p class="postfix_won"><strong id="od_send_coupon">-<?php echo number_format($send_cost_discount); ?></strong></p>
      </li>
      <?php } ?>
      <li>
        <p>추가배송비</p>
        <p class="postfix_won"><strong id="od_send_cost2">0</strong></p>
      </li>
      <li>
        <span>* 추가 배송비: 제주도 및 산간지역 3,000원, 오늘배송 2,500원</span>
      </li>
    </ul>
    <h3 id="od_tot_price" class="amount">
      <div>총 결제비용</div>
      <div class="postfix_won"><strong class="print_price"><?php echo number_format($tot_price); ?></strong></div>
    </h3>
  </div>
</div>

      <?php
      //jacknam
      if ($payment_test) {
        echo '<div class="order_btn"><input type="button" value="결제하기테스트" id="make_payment_test" class="page_list_btn"></div>';
      }

      if ($tot_price == 0) {
      ?>
        <div id="display_pay_button" class="order_btn">
        	<input type="button" value="결제하기" id="make_payment" class="page_list_btn">
        </div>
      <?php
      } else {
        // 결제대행사별 코드 include (결제대행사 정보 필드 및 주분버튼)
        require_once(G5_MSHOP_PATH . '/' . $default['de_pg_service'] . '/orderform.2.php');

        if (is_inicis_simple_pay()) {   //삼성페이 또는 L.pay 사용시
          require_once(G5_MSHOP_PATH . '/samsungpay/orderform.2.php');
        }

        if (function_exists('is_use_easypay') && is_use_easypay('global_nhnkcp')) {  // 타 PG 사용시 NHN KCP 네이버페이 사용이 설정되어 있다면
          require_once(G5_MSHOP_PATH . '/kcp/easypay_form.2.php');
        }

        if ($is_kakaopay_use) {
          require_once(G5_SHOP_PATH . '/kakaopay/orderform.2.php');
        }

      ?>

        <div id="show_progress" style="display:none;">
          <img src="<?php echo G5_MOBILE_URL; ?>/shop/img/loading.gif" alt="">
          <span>주문완료 중입니다. 잠시만 기다려 주십시오.</span>
        </div>

      <?php
        if ($is_kakaopay_use) {
          require_once(G5_SHOP_PATH . '/kakaopay/orderform.3.php');
        }
      }
      ?>

    </form>
    <?php
    //jacknam
    if ($tot_price != 0) {
      if ($default['de_escrow_use']) {
        // 결제대행사별 코드 include (에스크로 안내)
        require_once(G5_MSHOP_PATH . '/' . $default['de_pg_service'] . '/orderform.3.php');

        if (is_inicis_simple_pay()) {   //삼성페이 사용시
          require_once(G5_MSHOP_PATH . '/samsungpay/orderform.3.php');
        }
      }
    }
    ?>
  </div>
</div>

<?php
//jacknam
if ($tot_price != 0) {
  if (is_inicis_simple_pay()) {   //삼성페이 사용시
    require_once(G5_MSHOP_PATH . '/samsungpay/order.script.php');
  }

  if (function_exists('is_use_easypay') && is_use_easypay('global_nhnkcp')) {  // 타 PG 사용시 NHN KCP 네이버페이 사용이 설정되어 있다면
    require_once(G5_MSHOP_PATH . '/kcp/m_order.script.php');
  }
}
?>
<script>
//jacknam
var process_next = false;

<?php if ($payment_test) { ?>
$(document).on("click", "#make_payment_test", function(e) {
  e.preventDefault();
  var _form = $(this).closest("form[name=forderform]");
  var _input = _form.find("*[name=od_settle_case]");
  _input.val("TEST");
  _form.submit();
});
<?php } ?>

$(document).on("click", "#make_payment_approval", function(e) {
  e.preventDefault();

  if (process_next) {
    return false;
  }

  if (!pay_approval()) {
    return false;
  }
  process_next = true;
  setTimeout(() => {
    process_next = false;
  }, 3000);
});


$(document).on("click", "#make_payment", function(e) {
  e.preventDefault();

  if (process_next) {
    return false;
  }

  if (!forderform_check()) {
    return false;
  }
  process_next = true;
  setTimeout(() => {
    process_next = false;
  }, 3000);
});

  var zipcode = "";

  $(function() {
    $("#od_b_addr2").focus(function() {
      var zip = $("#od_b_zip").val().replace(/[^0-9]/g, "");
      if (zip == "")
        return false;

      var code = String(zip);

      if (zipcode == code)
        return false;

      zipcode = code;
      calculate_sendcost(code);
    });

    $("#od_settle_bank").on("click", function() {
      $("[name=od_deposit_name]").val($("[name=od_name]").val());
      $("#settle_bank").show();
      $("#show_req_btn").css("display", "none");
      $("#show_pay_btn").css("display", "inline");
    });

    $("#od_settle_iche,#od_settle_card,#od_settle_vbank,#od_settle_hp,#od_settle_easy_pay,#od_settle_kakaopay,#od_settle_samsungpay,#od_settle_nhnkcp_payco,#od_settle_nhnkcp_naverpay,#od_settle_nhnkcp_kakaopay,#od_settle_inicislpay,#od_settle_inicis_kakaopay").bind("click", function() {
      $("#settle_bank").hide();
      $("#show_req_btn").css("display", "inline");
      $("#show_pay_btn").css("display", "none");
    });

    // 배송지선택
    $("input[name=ad_sel_addr]").on("click", function() {
      var addr = $(this).val().split(String.fromCharCode(30));

      if (addr[0] == "same") {
        gumae2baesong();
      } else {
        if (addr[0] == "new") {
          for (i = 0; i < 10; i++) {
            addr[i] = "";
          }
        }

        var f = document.forderform;
        f.od_b_name.value = addr[0];
        f.od_b_tel.value = addr[1];
        f.od_b_hp.value = addr[2];
        f.od_b_zip.value = addr[3] + addr[4];
        f.od_b_addr1.value = addr[5];
        f.od_b_addr2.value = addr[6];
        f.od_b_addr3.value = addr[7];
        f.od_b_addr_jibeon.value = addr[8];
        f.ad_subject.value = addr[9];

        var zip1 = addr[3].replace(/[^0-9]/g, "");
        var zip2 = addr[4].replace(/[^0-9]/g, "");

        var code = String(zip1) + String(zip2);

        if (zipcode != code) {
          calculate_sendcost(code);
        }
      }
    });

    // 배송지목록
    $("#order_address").on("click", function() {
      var url = this.href;
      window.open(url, "win_address", "left=100,top=100,width=650,height=500,scrollbars=1");
      return false;
    });
  });


function calculate_order_price()
{
  var sell_price = parseInt($("input[name=od_price]").val() ?? 0);
  var send_cost = parseInt($("input[name=od_send_cost]").val() ?? 0);
  var send_cost2 = parseInt($("input[name=od_send_cost2]").val() ?? 0);
  var send_coupon = parseInt($("input[name=od_send_coupon]").val() ?? 0);
  //jacknam
  //var tot_price = sell_price + send_cost + send_cost2 - send_coupon;
  var point_discount = parseInt($("select[name=od_temp_point]").val() ?? 0);
  var total_discount = parseInt($("input[name=od_cp_price]").val() ?? 0);
  var tot_price = sell_price + send_cost + send_cost2 - send_coupon - total_discount - point_discount;

  //console.log(point_discount, send_coupon, tot_price);

  $("input[name=good_mny]").val(tot_price);
  $("#od_tot_price .print_price").text(number_format(String(tot_price)));
}

function calculate_point() {
  var _select = $("#od_temp_point"),
  val = _select.val() ?? 0;

  $("li.select_list_cloned").remove().end();
  if (val > 0) {
    var _send_cost = _select.closest("form").find(".send_cost_display:first");
    _send_cost.before($("<li class='select_list_cloned'><p>포인트할인</p><p class='postfix_won'><strong>-" + number_format(val) + "</strong></p></li>"));
  }
  calculate_order_price();
};

function calculate_sendcost(code)
{
  //jacknam
  var tot_price = $("input[type=hidden][name=tot_price]:first").val();
  if (tot_price > 0) {
    $.post(
    "./ordersendcost.php",
    { zipcode: code },
    function(data) {
      //console.log(data);
      //data = 2500;
      $("input[name=od_send_cost2]").val(data);
      $("#od_send_cost2").text(number_format(String(data)));
      zipcode = code;
      calculate_order_price();
    }
    );
  }
}

function calculate_tax()
{
  var $it_prc = $("input[name^=it_price]");
  var $cp_prc = $("input[name^=cp_price]");
  var it_price, cp_price, it_notax;
  var tot_mny = comm_free_mny = tax_mny = vat_mny = 0;
  var send_cost = parseInt($("input[name=od_send_cost]").val()) ?? 0;
  var send_cost2 = parseInt($("input[name=od_send_cost2]").val() ?? 0);
  //var od_coupon = parseInt($("input[name=od_coupon]").val());
  var send_coupon = parseInt($("input[name=od_send_coupon]").val() ?? 0);
  var temp_point = 0;

  //jacknam
  var total_discount = parseInt($("input[name=od_cp_price]").val() ?? 0);

  $it_prc.each(function(index) {
    it_price = parseInt($(this).val());
    cp_price = parseInt($cp_prc.eq(index).val() ?? 0);
    //sell_price += it_price;
    //tot_cp_price += cp_price;
    it_notax = $("input[name^=it_notax]").eq(index).val() ?? 0;
    if(it_notax == "1") {
      comm_free_mny += (it_price - cp_price);
    } else {
      tot_mny += (it_price - cp_price);
    }
  });

  if($("select[name=od_temp_point]").length) {
    temp_point = parseInt($("select[name=od_temp_point]").val() ?? 0);
  }

  //jacknam
  //tot_mny += (send_cost + send_cost2 - od_coupon - send_coupon - temp_point);
  tot_mny += (send_cost + send_cost2 - send_coupon - temp_point);
  if(tot_mny < 0) {
    comm_free_mny = comm_free_mny + tot_mny;
    tot_mny = 0;
  }

  tax_mny = Math.round(tot_mny / 1.1);
  vat_mny = tot_mny - tax_mny;
  $("input[name=comm_tax_mny]").val(tax_mny);
  $("input[name=comm_vat_mny]").val(vat_mny);
  $("input[name=comm_free_mny]").val(comm_free_mny);
}

  /* 결제방법에 따른 처리 후 결제등록요청 실행 */
  var settle_method = "";
  var temp_point = 0;

  function pay_approval() {
    // 무통장 아닌 가상계좌, 계좌이체, 휴대폰, 신용카드, 기타 등등 을 처리한다.
    // 재고체크
    var stock_msg = order_stock_check();
    if (stock_msg != "") {
      alert(stock_msg);
      return false;
    }

    var f = document.sm_form;
    var pf = document.forderform;

    // 필드체크
    if (!orderfield_check(pf))
      return false;

    // 금액체크
    if (!payment_check(pf))
      return false;

    // 카카오페이 지불
    if (settle_method == "KAKAOPAY") {
      <?php if ($default['de_tax_flag_use']) { ?>
        pf.SupplyAmt.value = parseInt(pf.comm_tax_mny.value) + parseInt(pf.comm_free_mny.value);
        pf.GoodsVat.value = parseInt(pf.comm_vat_mny.value);
      <?php } ?>
      pf.good_mny.value = f.good_mny.value;
      getTxnId(pf);
      return false;
    }

    var form_order_method = '';

    if (settle_method == "삼성페이" || settle_method == "lpay" || settle_method == "inicis_kakaopay") {
      form_order_method = 'samsungpay';
    } else if (settle_method == "간편결제") {
      if (jQuery("input[name='od_settle_case']:checked").attr("data-pay") === "naverpay") {
        form_order_method = 'nhnkcp_naverpay';
      }
    }

    if (jQuery(pf).triggerHandler("form_sumbit_order_" + form_order_method) !== false) {
      <?php if ($default['de_pg_service'] == 'kcp') { ?>
        f.buyr_name.value = pf.od_name.value;
        f.buyr_mail.value = pf.od_email.value;
        f.buyr_tel1.value = pf.od_tel.value;
        f.buyr_tel2.value = pf.od_hp.value;
        f.rcvr_name.value = pf.od_b_name.value;
        f.rcvr_tel1.value = pf.od_b_tel.value;
        f.rcvr_tel2.value = pf.od_b_hp.value;
        f.rcvr_mail.value = pf.od_email.value;
        f.rcvr_zipx.value = pf.od_b_zip.value;
        f.rcvr_add1.value = pf.od_b_addr1.value;
        f.rcvr_add2.value = pf.od_b_addr2.value;
        f.settle_method.value = settle_method;

        if (typeof f.payco_direct !== "undefined") f.payco_direct.value = "";
        if (typeof f.naverpay_direct !== "undefined") f.naverpay_direct.value = "A";
        if (typeof f.kakaopay_direct !== "undefined") f.kakaopay_direct.value = "A";
        if (typeof f.ActionResult !== "undefined") f.ActionResult.value = "";
        if (typeof f.pay_method !== "undefined") f.pay_method.value = "";

        if (settle_method == "간편결제") {
          var nhnkcp_easy_pay = jQuery("input[name='od_settle_case']:checked").attr("data-pay");

          if (nhnkcp_easy_pay === "naverpay") {
            if (typeof f.naverpay_direct !== "undefined") {
              f.naverpay_direct.value = "Y";
            }
          } else if (nhnkcp_easy_pay === "kakaopay") {
            if (typeof f.kakaopay_direct !== "undefined") f.kakaopay_direct.value = "Y";
          } else {
            if (typeof f.payco_direct !== "undefined") f.payco_direct.value = "Y";
          }

          if (typeof f.ActionResult !== "undefined") f.ActionResult.value = "CARD"; // 대소문자 구분
          if (typeof f.pay_method !== "undefined") f.pay_method.value = "card"; // 대소문자 구분

        }

      <?php } else if ($default['de_pg_service'] == 'lg') { ?>
        var pay_method = "";
        var easy_pay = "";
        switch (settle_method) {
          case "계좌이체":
            pay_method = "SC0030";
            break;
          case "가상계좌":
            pay_method = "SC0040";
            break;
          case "휴대폰":
            pay_method = "SC0060";
            break;
          case "신용카드":
            pay_method = "SC0010";
            break;
          case "간편결제":
            easy_pay = "PAYNOW";
            break;
        }
        f.LGD_CUSTOM_FIRSTPAY.value = pay_method;
        f.LGD_BUYER.value = pf.od_name.value;
        f.LGD_BUYEREMAIL.value = pf.od_email.value;
        f.LGD_BUYERPHONE.value = pf.od_hp.value;
        f.LGD_AMOUNT.value = f.good_mny.value;
        f.LGD_EASYPAY_ONLY.value = easy_pay;
        <?php if ($default['de_tax_flag_use']) { ?>
          f.LGD_TAXFREEAMOUNT.value = pf.comm_free_mny.value;
        <?php } ?>
      <?php } else if ($default['de_pg_service'] == 'inicis') { ?>
        var paymethod = "";
        var width = 330;
        var height = 480;
        var xpos = (screen.width - width) / 2;
        var ypos = (screen.width - height) / 2;
        var position = "top=" + ypos + ",left=" + xpos;
        var features = position + ", width=320, height=440";
        var p_reserved = f.DEF_RESERVED.value;
        f.P_RESERVED.value = p_reserved;
        switch (settle_method) {
          case "계좌이체":
            paymethod = "bank";
            break;
          case "가상계좌":
            paymethod = "vbank";
            break;
          case "휴대폰":
            paymethod = "mobile";
            break;
          case "신용카드":
            paymethod = "wcard";
            f.P_RESERVED.value = f.P_RESERVED.value.replace("&useescrow=Y", "");
            break;
          case "간편결제":
            paymethod = "wcard";
            f.P_RESERVED.value = p_reserved + "&d_kpay=Y&d_kpay_app=Y";
            break;
          case "삼성페이":
            paymethod = "wcard";
            f.P_RESERVED.value = f.P_RESERVED.value.replace("&useescrow=Y", "") + "&d_samsungpay=Y";
            //f.DEF_RESERVED.value = f.DEF_RESERVED.value.replace("&useescrow=Y", "");
            f.P_SKIP_TERMS.value = "Y"; //약관을 skip 해야 제대로 실행됨
            break;
          case "lpay":
            paymethod = "wcard";
            f.P_RESERVED.value = f.P_RESERVED.value.replace("&useescrow=Y", "") + "&d_lpay=Y";
            //f.DEF_RESERVED.value = f.DEF_RESERVED.value.replace("&useescrow=Y", "");
            f.P_SKIP_TERMS.value = "Y"; //약관을 skip 해야 제대로 실행됨
            break;
          case "inicis_kakaopay":
            paymethod = "wcard";
            f.P_RESERVED.value = f.P_RESERVED.value.replace("&useescrow=Y", "") + "&d_kakaopay=Y";
            //f.DEF_RESERVED.value = f.DEF_RESERVED.value.replace("&useescrow=Y", "");
            f.P_SKIP_TERMS.value = "Y"; //약관을 skip 해야 제대로 실행됨
            break;
        }
        f.P_AMT.value = f.good_mny.value;
        f.P_UNAME.value = pf.od_name.value;
        f.P_MOBILE.value = pf.od_hp.value;
        f.P_EMAIL.value = pf.od_email.value;
        <?php if ($default['de_tax_flag_use']) { ?>
          f.P_TAX.value = pf.comm_vat_mny.value;
          f.P_TAXFREE = pf.comm_free_mny.value;
        <?php } ?>
        f.P_RETURN_URL.value = "<?php echo $return_url . $od_id; ?>";
        f.action = "https://mobile.inicis.com/smart/" + paymethod + "/";
      <?php } ?>

      // 주문 정보 임시저장
      var order_data = $(pf).serialize();
      var save_result = "";
      $.ajax({
        type: "POST",
        data: order_data,
        url: g5_url + "/shop/ajax.orderdatasave.php",
        cache: false,
        async: false,
        success: function(data) {
          save_result = data;
        }
      });

      if (save_result) {
        alert(save_result);
        return false;
      }

      f.submit();
    }

    return false;
  }

  function forderform_check() {
    // 무통장만 여기에처 처리한다.
    // 재고체크
    var stock_msg = order_stock_check();
    if (stock_msg != "") {
      alert(stock_msg);
      return false;
    }

    var f = document.forderform;

    // 필드체크
    if (!orderfield_check(f))
      return false;

    // 금액체크
    if (!payment_check(f))
      return false;

    console.log(settle_method);
    console.log(f.res_cd);

    if (settle_method != "무통장" && f.res_cd.value != "0000") {
      //alert("결제등록요청 후 주문해 주십시오.");

      //return false;
    }

    document.getElementById("display_pay_button").style.display = "none";
    document.getElementById("show_progress").style.display = "block";

    setTimeout(function() {
      f.submit();
    }, 300);
  }

  // 주문폼 필드체크
  function orderfield_check(f) {
    errmsg = "";
    errfld = "";
    var deffld = "";

    $("input:hidden[name=od_name]").val($("input:text[name=od_b_name]").val());
    $("input:hidden[name=od_tel]").val($("input:text[name=od_b_tel]").val());
    $("input:hidden[name=od_b_hp]").val($("input:text[name=od_b_tel]").val());
    //$("input:hidden[name=od_hp]").val($("input:text[name=od_b_hp]").val());
    $("input:hidden[name=od_hp]").val($("input:text[name=od_b_tel]").val());
    $("input:hidden[name=od_zip]").val($("input:text[name=od_b_zip]").val());
    $("input:hidden[name=od_addr1]").val($("input:text[name=od_b_addr1]").val());
    $("input:hidden[name=od_addr2]").val($("input:text[name=od_b_addr2]").val());
    $("input:hidden[name=od_addr3]").val($("input:text[name=od_b_addr3]").val());
    $("input:hidden[name=od_addr_jibeon]").val($("input:text[name=od_b_addr_jibeon]").val());

    clear_field(f.od_email);
    if (f.od_email.value == '' || f.od_email.value.search(/(\S+)@(\S+)\.(\S+)/) == -1)
      error_field(f.od_email, "E-mail을 바르게 입력해 주십시오.");

    check_field(f.od_b_name, "수령하시는 분 이름을 입력하십시오.");
    check_field(f.od_b_tel, "수령하시는 분 핸드폰 번호를 입력하십시오.");
    check_field(f.od_b_addr1, "주소검색을 이용하여 주문하시는 분 주소를 입력하십시오.");
    check_field(f.od_b_zip, "");

    var od_settle_bank = document.getElementById("od_settle_bank");
    if (od_settle_bank) {
      if (od_settle_bank.checked) {
        check_field(f.od_bank_account, "계좌번호를 선택하세요.");
        check_field(f.od_deposit_name, "입금자명을 입력하세요.");
      }
    }

    // 배송비를 받지 않거나 더 받는 경우 아래식에 + 또는 - 로 대입
    f.od_send_cost.value = parseInt(f.od_send_cost.value);

    if (errmsg) {
      alert(errmsg);
      errfld.focus();
      return false;
    }

    //jacknam
    var od_price = parseInt(f.od_price.value);
    if (od_price == 0) {
      f.submit();
      return false;
    }
    //jacknam

    var settle_case = document.getElementsByName("od_settle_case");
    var settle_check = false;
    for (i = 0; i < settle_case.length; i++) {
      if (settle_case[i].checked) {
        settle_check = true;
        settle_method = settle_case[i].value;
        break;
      }
    }
    if (!settle_check) {
      alert("결제방식을 선택하십시오.");
      return false;
    }

    return true;
  }

  // 결제체크
  function payment_check(f) {
    var od_price = parseInt(f.od_price.value);
    var send_cost = parseInt(f.od_send_cost.value);
    var send_cost2 = parseInt(f.od_send_cost2.value);
    var send_coupon = parseInt(f.od_send_coupon.value ?? 0);
    var total_discount = parseInt(f.od_cp_price.value ?? 0);

    var max_point = 0;
    if (typeof(f.max_temp_point) != "undefined") {
      max_point  = parseInt(f.max_temp_point.value ?? 0);
    }

    var temp_point = 0;
    if (typeof(f.od_temp_point) != "undefined") {
      var point_unit = parseInt(<?php echo $default['de_settle_point_unit']; ?>);
      temp_point = parseInt(f.od_temp_point.value ?? 0) || 0;

      if (f.od_temp_point.value)
      {
        if (temp_point > od_price) {
          alert("상품 주문금액(배송비 제외) 보다 많이 포인트결제할 수 없습니다.");
          f.od_temp_point.select();
          return false;
        }

        if (temp_point > <?php echo (int)$member['mb_point']; ?>) {
          alert("회원님의 포인트보다 많이 결제할 수 없습니다.");
          f.od_temp_point.select();
          return false;
        }

        if (temp_point > max_point) {
          alert(max_point + "점 이상 결제할 수 없습니다.");
          f.od_temp_point.select();
          return false;
        }
      }
      // pg 결제 금액에서 포인트 금액 차감
      if(settle_method != "무통장") {
        //jacknam
        f.good_mny.value = od_price + send_cost + send_cost2 - send_coupon - total_discount - temp_point;
      }
    }

    //jacknam
    var tot_price = od_price + send_cost + send_cost2 - send_coupon - total_discount - temp_point;

    if (document.getElementById("od_settle_iche")) {
      if (document.getElementById("od_settle_iche").checked) {
        if (tot_price < 150) {
          alert("계좌이체는 150원 이상 결제가 가능합니다.");
          return false;
        }
      }
    }

    if (document.getElementById("od_settle_card")) {
      if (document.getElementById("od_settle_card").checked) {
        if (tot_price < 1000) {
          alert("신용카드는 1000원 이상 결제가 가능합니다.");
          return false;
        }
      }
    }

    if (document.getElementById("od_settle_hp")) {
      if (document.getElementById("od_settle_hp").checked) {
        if (tot_price < 350) {
          alert("휴대폰은 350원 이상 결제가 가능합니다.");
          return false;
        }
      }
    }

    <?php if ($default['de_tax_flag_use']) { ?>
      calculate_tax();
    <?php } ?>

    return true;
  }

  // 구매자 정보와 동일합니다.
  function gumae2baesong() {
    var f = document.forderform;

    f.od_b_name.value = f.od_name.value;
    f.od_b_tel.value = f.od_tel.value;
    f.od_b_hp.value = f.od_hp.value;
    f.od_b_zip.value = f.od_zip.value;
    f.od_b_addr1.value = f.od_addr1.value;
    f.od_b_addr2.value = f.od_addr2.value;
    f.od_b_addr3.value = f.od_addr3.value;
    f.od_b_addr_jibeon.value = f.od_addr_jibeon.value;

    calculate_sendcost(String(f.od_b_zip.value));
  }

    $(function() {
      calculate_point();
  <?php if ($default['de_hope_date_use']) { ?>
      $("#od_hope_date").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        yearRange: "c-99:c+99",
        minDate: "+<?php echo (int)$default['de_hope_date_after']; ?>d;",
        maxDate: "+<?php echo (int)$default['de_hope_date_after'] + 6; ?>d;"
      });
  <?php } ?>
    });
</script>
<style>
.order_write.coupon_list .amount_list > li > p.subject {
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  word-break: break-all;
}
.select_point.amount_list {
  border-bottom: none;
  margin-bottom:auto;
}
</style>