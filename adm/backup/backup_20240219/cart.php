<?php

/*************************************
 * 장바구니
	- ct_output 값이 Y인 값만 출력
		- 일반상품에서 장바구니로 저장시 ct_output 값을 'Y', ct_kind 값을 'general'로 설정
 *************************************/
include_once('./_common.php');
$naverpay_button_js = '';
include_once(G5_SHOP_PATH . '/settle_naverpay.inc.php');

// 보관기간이 지난 상품 삭제
cart_item_clean();

$sw_direct = isset($_REQUEST['sw_direct']) ? (int) $_REQUEST['sw_direct'] : 0;

// cart id 설정
set_cart_id($sw_direct);

$s_cart_id = get_session('ss_cart_id');
// 선택필드 초기화
$sql = " update {$g5['g5_shop_cart_table']} set ct_select = '0' where od_id = '$s_cart_id' ";
sql_query($sql);

$cart_action_url = G5_SHOP_URL . '/cartupdate.php';

if (function_exists('before_check_cart_price')) {
  before_check_cart_price($s_cart_id, true, true, true);
}

// 테마에 cart.php 있으면 include
if (defined('G5_THEME_SHOP_PATH')) {
  $theme_cart_file = G5_THEME_SHOP_PATH . '/cart.php';
  if (is_file($theme_cart_file)) {
    include_once($theme_cart_file);
    return;
    unset($theme_cart_file);
  }
}

// $s_cart_id 로 현재 장바구니 자료 쿼리
// edit jacknam
$cart_list = get_cart_list($member['mb_id'], $s_cart_id, 'cart', true);
$rsvt_info = get_rsvt_info($cart_list, 0, true);

function print_rsvt_info($rsvt, $target) {
  $css = $target == 'mobile' ? 'block_767' : 'none_767';
  $rsvt_html = "<ul class='cart_object_time {$css}'>";
  if ($rsvt) {
    $rsvt_html .= "<li><p>담당 한의사</p><p>{$rsvt['hp_doc_name']}</p></li>";
    $rsvt_html .= "<li><p>예약 일자</p><p>{$rsvt['hp_rsvt_date_yoil']}</p></li>";
    $rsvt_html .= "<li><p>예약 시간</p><p>{$rsvt['hp_rsvt_time']}</p></li>";
  } else {
    $rsvt_html .= "<li class='norsvt'><p>예약 정보 없음</p></li>";
  }
  $rsvt_html .= "</ul>";
  return $rsvt_html;
}

$coupon_disable = is_coupon_point_disable($cart_list, 'coupon');
// jacknam 쿠폰 정보 초기화
set_session('s_cart_coupons', null);

$g5['title'] = '장바구니';
include_once('./_head.php');
?>

<div>
  <div class="cart">
    <h2 class="cart_title">
      장바구니
    </h2>
    <div class="cart_layout">
      <form name="frmcartlist" id="sod_bsk_list" class="2017_renewal_itemform" method="post" action="<?php echo $cart_action_url; ?>">
        <ul class="cart_list">
          <!-- 피시버전 -->
          <?php
          $tot_point = 0;
          $tot_sell_price = 0;
          $send_cost = 0;
          $it_send_cost = 0;

          $cp_it_ids = array();
          $cp_ca_ids = array();

          $continue_ca_id = '';
          foreach ($cart_list as $i => $row) {
            $price = ($row['ct_price'] + $row['io_price']) * $row['ct_qty'];
            $sum = array('price' => $price, 'point' => ($row['ct_point'] * $row['ct_qty']), 'qty' => $row['ct_qty']);

            if ($i == 0) { // 계속쇼핑
              $continue_ca_id = $row['ca_id'];
            }

            if ($row['inf_code']) {
              $a1 = '<a href="' . shop_item_url($row['it_id'], '&infcode=' . $row['inf_code']) . '" class="prd_name"><b>';
            } else {
              $a1 = '<a href="' . shop_item_url($row['it_id']) . '" class="prd_name"><b>';
            }

            $a2 = '</b></a>';
            $image = get_it_image($row['it_id'], 420, 420);

            $it_name = $a1 . stripslashes($row['it_name']) . $a2;
            $it_options = print_item_options($row['it_id'], $s_cart_id);
            if ($it_options) {
              $mod_options = '<div class="sod_option_btn"><button type="button" class="mod_options">선택사항수정</button></div>';
              $it_name .= '<div class="sod_opt">' . $it_options . '</div>';
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

            $point      = $sum['point'];
            $sell_price = $sum['price'];
            //var_dump($sell_price);

            $cp_it_id = $row['it_org_id'] ? $row['it_org_id'] : $row['it_id'];
            if (!in_array($cp_it_id, $cp_it_ids)) {
              $cp_it_ids[] = $cp_it_id;
            }
            $cp_ca_id = $row['it_org_ca_id'] ? $row['it_org_ca_id'] : $row['ca_id'];
            if (!in_array($cp_ca_id, $cp_ca_ids)) {
              $cp_ca_ids[] = $cp_ca_id;
            }
          ?>
            <!-- // jacknam -->
            <li class="cart_box" data-cp_it_id="<?php echo $cp_it_id; ?>" data-cp_ca_id="<?php echo $cp_ca_id; ?>" data-sell_price="<?php echo $sell_price; ?>">
              <input type="hidden" name="cp_price[<?php echo $i; ?>]" value="0" id="cp_price_<?php echo $i; ?>">
              <div class="cart_object_close">
                <!-- // <a href="javascript:void(0);" id="cart_del" style="font-size:3em;" data-id=<?php echo $row['it_id'] ?>>삭제<img src="<?php echo G5_IMG_URL ?>/cart/cart_close.png" alt="삭제"></a>
                <input type="button" id="cart_del" class="page_btn" style="font-size:3em;" data-id="<?php echo $row['it_id']; ?>" value="삭제"> -->
                <button id="cart_del" class="cart_del page_list_btn" data-id="<?php echo $row['it_id']; ?>">삭제</button>
              </div>
                <a href="<?php echo ($row['inf_code'] ? shop_item_url($row['it_id'], '&infcode=' . $row['inf_code']) : shop_item_url($row['it_id'])); ?>">
                <div class="cart_object">
                  <div class="cart_object_img">
                    <?php echo $image; ?>
                  </div>
                  <div class="cart_object_info">
                    <div class="object_info_category">
                      <?php if ($row['it_icon1']) { ?><span class="bg_color_33">한의약품</span><?php } ?><?php if ($row['it_icon2']) { ?><span class="bg_color_EB6890">오늘의 배송</span><?php } ?>
                    </div>
                    <h2 class="object_info_name">
                      <input type="hidden" name="ct_chk[<?php echo $i; ?>]" value="1" id="ct_chk_<?php echo $i; ?>">
                      <input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
                      <input type="hidden" name="it_name[<?php echo $i; ?>]" value="<?php echo get_text($row['it_name']); ?>">
                      <?php echo stripslashes($row['it_name']) ?>
                    </h2>

                    <!-- // jacknam -->
                    <p class="cart_object_info_amout">
                      <?php echo stripslashes($row['ct_option']); ?>
                    </p>
                    <p class="cart_object_info_price">
                      <span>수량 : <?php echo number_format($sum['qty']); ?> 개</span>
                      <span>&nbsp;|&nbsp금액 : <?php echo number_format($sell_price); ?>원</span>
                    </p>
                    <?php
                    if ($row['it_kind'] == 'prescription') { //처방제품이라면
                      echo print_rsvt_info($rsvt_info, 'pc');
                    } ?>
                  </div>
                </div>
                <?php if ($row['it_kind'] == 'prescription') { //처방제품이라면
                  echo print_rsvt_info($rsvt_info, 'mobile');
                } ?>
              </a>
            </li>
          <?php
            $tot_point      += $point;
            $tot_sell_price += $sell_price;
          } // for 끝

          if (!$continue_ca_id) {
            echo '<li><div class="cart_object_empty"><p>장바구니에 담긴 상품이 없습니다.</p></div></li>';
          } else {
            // 배송비 계산
            $send_cost = get_sendcost($s_cart_id, 0);
          }
          ?>
        </ul>
        <p class="check_time">※ 진료 일자와 시간을 다시 한 번 확인해주세요.</p>

        <!-- // jacknam -->
        <?php if ($tot_sell_price > 0) {
          if ($coupon_disable) {
            $available_coupons = [];
          } else {
            $available_coupons = get_available_coupons($member['mb_id'], $cp_it_ids, $cp_ca_ids);
          }
          if ($available_coupons) {
            $cp_method_title = ['상품쿠폰', '카테고리쿠폰', '주문할인쿠폰', '배송비쿠폰'];
            $cp_methods = [[], [], [], []]; // 0: 개별상품할인, 1: 카테고리할인, 2: 주문금액할인, 3: 배송비할인
            $cp_count = 0;
            foreach ($available_coupons as $coupon) {
              $idx = (int)$coupon['cp_method'];
              if ($idx == 3 && !$send_cost) {
                continue;
              }
              if ($tot_sell_price < (int)$coupon['cp_minimum']) {
                continue;
              }
              $cp_methods[$idx][] = $coupon;
              $cp_count++;
            }

            if ($cp_count) {
              $coupon_box = '<div class="coupon_box page_list_w">';
              $coupon_box .= "<div class='pay_text'>";
              $coupon_box .= "<h2>적용가능쿠폰</h2><div class='coupon_box_list'>";

              foreach ($cp_methods as $idx => $method) {
                if ($method) {
                  $coupon_box .= "<ul><li><h2>{$cp_method_title[$idx]}</h2></li>";
                  $coupon_box .= "<li><input type='hidden' name='cp_id_price[{$idx}]' value='0'>";
                  $coupon_box .= "<select name='cp_id[{$idx}]' class='cp_id' onchange='coupon_calculate(this)'><option value='' selected>적용안함</option>";
                  foreach ($method as $row) {
                    $coupon_box .= "<option value='{$row['cp_id']}' data-cp_method='{$row['cp_method']}' data-cp_target='{$row['cp_target']}'";
                    $coupon_box .= " data-cp_type='{$row['cp_type']}' data-cp_price='{$row['cp_price']}' data-cp_trunc='{$row['cp_trunc']}' data-cp_maximum='{$row['cp_maximum']}'>";
                    $coupon_box .= "{$row['cp_subject']}</option>";
                  }
                  $coupon_box .= "</select></li></ul>";
                }
              }
              $coupon_box .= "</div></div></div>";
              echo $coupon_box;
            }
          }
          $total_payment = $tot_sell_price + $send_cost;
        ?>
          <div class="pay_info_box page_list_w" style="padding: 0 40px">
            <div class="pay_text">
              <h2>총 구매금액</h2>
              <p id="tot_sell_price" data-tot_sell_price="<?php echo $tot_sell_price; ?>"><?php echo number_format($tot_sell_price); ?></p>
            </div>
            <div class="pay_text">
              <h2>배송비</h2>
              <p id="send_cost" data-send_cost="<?php echo $send_cost; ?>"><?php echo number_format($send_cost); ?></p>
            </div>
            <hr class="horizontal_line" />
            <div class="pay_text">
              <input type="hidden" name="cp_info[total_payment]" value="<?php echo $total_payment; ?>" />
              <h2>결제금액</h2>
              <p id="total_payment" data-total_payment="<?php echo $total_payment; ?>"><?php echo number_format($total_payment); ?></p>
            </div>

          </div>
        <?php } ?>
        <!-- // jacknam -->

        <div class="order_btn">
          <?php if (!$continue_ca_id) { ?>
            <a href="<?php echo G5_SHOP_URL; ?>/" class="page_list_btn">쇼핑 계속하기</a>
          <?php } else { ?>
            <input type="hidden" name="url" value="./orderform.php">
            <input type="hidden" name="records" value="<?php echo $i; ?>">
            <input type="hidden" name="act" value="">
            <input type="hidden" name="del_it_id" value="">
            <!--<a href="<?php echo shop_category_url($continue_ca_id); ?>" class="page_list_btn">쇼핑 계속하기</a>-->
            <!-- // jacknam
          <a href="javascript:void(0);" onclick="return form_check('buy');" class="page_list_btn">결제하기</a>
          -->
            <button id="btn_buy" class="page_list_btn">결제하기</button>
            <!-- // jacknam -->
            <?php if ($naverpay_button_js) { ?>
              <div class="cart-naverpay"><?php echo $naverpay_request_js . $naverpay_button_js; ?></div>
            <?php } ?>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  var process_next = false;

  $(function() {
    var close_btn_idx;

    // 선택사항수정
    $(".mod_options").click(function() {
      var it_id = $(this).closest("tr").find("input[name^=it_id]").val();
      var $this = $(this);
      close_btn_idx = $(".mod_options").index($(this));

      $.post(
        "./cartoption.php", {
          it_id: it_id
        },
        function(data) {
          $("#mod_option_frm").remove();
          $this.after("<div id=\"mod_option_frm\"></div><div class=\"mod_option_bg\"></div>");
          $("#mod_option_frm").html(data);
          price_calculate();
        }
      );
    });

    // 모두선택
    $("input[name=ct_all]").click(function() {
      if ($(this).is(":checked")) {
        $("input[name^=ct_chk]").attr("checked", true);
      } else {
        $("input[name^=ct_chk]").attr("checked", false);
      }
    });

    // 옵션수정 닫기
    $(document).on("click", "#mod_option_close", function() {
      $("#mod_option_frm, .mod_option_bg").remove();
      $(".mod_options").eq(close_btn_idx).focus();
    });

    $("#win_mask").click(function() {
      $("#mod_option_frm").remove();
      $(".mod_options").eq(close_btn_idx).focus();
    });

    $(document).on("click", "#btn_buy", function(e) {
      e.preventDefault();
      if (process_next) {
        return false;
      }

      if ($(".norsvt").length > 0) {
        alert("예약 정보가 없거나 예약시간이 지났습니다.");
        return false;
      }

      process_next = true;
      if (!form_check('buy')) {
        process_next = false;
      } else {
        setTimeout(() => {
          process_next = false;
        }, 3000);
      }
    });
  });

  function coupon_calculate(s) {
    var _select = $(s),
      _cp_id_price = _select.siblings("input[type=hidden][name^=cp_id_price]"),
      select_val = _select.val(),
      _selects = _select.closest(".coupon_box_list").find("select.cp_id"),
      _items = $("form[name=frmcartlist]:first .cart_list:first .cart_box[data-sell_price]"),
      _tot_sell_price = $("#tot_sell_price[data-tot_sell_price]"),
      tot_sell_price = parseInt(_tot_sell_price.data("tot_sell_price")),
      _send_cost = $("#send_cost[data-send_cost]"),
      send_cost = parseInt(_send_cost.data("send_cost"));

    _items.each(function(idx, item) {
      var _input = $(item).find("input[type=hidden][name^=cp_price]");
      _input.val("0");
      //console.log(_input);
    });

    var get_discount = function(_selected, cp_method) {
      var cp_target = _selected.data("cp_target"),
        cp_price = parseInt(_selected.data("cp_price")),
        cp_type = _selected.data("cp_type"),
        cp_trunc = parseInt(_selected.data("cp_trunc")),
        cp_maximum = parseInt(_selected.data("cp_maximum")),
        discount = 0;

      //console.log(cp_method, cp_target, cp_price,cp_type, cp_trunc, cp_maximum);

      var cacluate = function(sell_price) {
        var i_discount = 0;
        if (cp_type == "0") {
          i_discount = cp_price;
        } else if (cp_type == "1") {
          i_discount = Math.floor((cp_price / 100) * sell_price / cp_trunc) * cp_trunc;
        }
        if (cp_maximum > 0) {
          return Math.min(cp_maximum, i_discount);
        }
        return i_discount;
      };

      var _filtered_items;
      if (cp_method == "3") {
        return cacluate(send_cost);
      } else {
        if (cp_method == "2" || cp_target == "") {
          _filtered_items = _items;
        } else {
          _filtered_items = _items.filter("[data-cp_" + (cp_method == "1" ? "ca" : "it") + "_id='" + cp_target + "']");
        }
      }

      _filtered_items.each(function(idx, item) {
        var sell_price = parseInt($(item).data("sell_price"));
        var t_discount = cacluate(sell_price);
        discount += t_discount;
        var _input = $(item).find("input[type=hidden][name^=cp_price]");
        _input.val(parseInt(_input.val()) + t_discount);
      });

      return discount;
    };

    var total_discount = 0;
    var send_cost_discount = 0;
    _selects.each(function(idx, item) {
      var _selected = $(item).find("option:selected"),
        selected_val = _selected.val(),
        cp_method = _selected.data("cp_method");
      if (selected_val != "") {
        if (cp_method == "3") {
          send_cost_discount += get_discount(_selected, cp_method);
        } else {
          total_discount += get_discount(_selected, cp_method);
        }
      }
    });

    if (tot_sell_price < total_discount) {
      total_discount = tot_sell_price;
    }

    if (send_cost < send_cost_discount) {
      send_cost_discount = send_cost;
    }

    var total_payment = tot_sell_price - total_discount + send_cost - send_cost_discount;

    $("div.discount_cloned").remove().end();

    var _target_obj;
    var _target_obj_clone;
    if (total_discount > 0) {
      _target_obj = _tot_sell_price.closest(".pay_text");
      _target_obj_clone = $("<div class='pay_text discount_cloned'><input type='hidden' name='cp_info[total_discount]' value='" + total_discount + "' />" +
        "<h2>총 쿠폰할인</h2><p id='total_discount' data-total_discount='" + total_discount + "'>-" + number_format(total_discount) + "</p></div>");
      _target_obj.after(_target_obj_clone);
    }

    if (send_cost_discount > 0) {
      _target_obj = _send_cost.closest(".pay_text");
      _target_obj_clone = $("<div class='pay_text discount_cloned'><input type='hidden' name='cp_info[send_cost_discount]' value='" + send_cost_discount + "' />" +
        "<h2>배송비 할인</h2><p id='send_cost_discount' data-send_cost_discount='" + send_cost_discount + "'>-" + number_format(send_cost_discount) + "</p></div>");
      _target_obj.after(_target_obj_clone);
    }

    _target_obj = $("#total_payment[data-total_payment]");
    if (select_val != "") {
      var cur_total_payment = _target_obj.data("total_payment") ?? 0;
      _cp_id_price.val(Math.abs(cur_total_payment - total_payment));
    } else {
      _cp_id_price.val(0);
    }
    _target_obj.data("total_payment", total_payment);
    _target_obj.text(number_format(total_payment));
    _target_obj.siblings("input[name^=cp_info]").val(total_payment);
    //_target_obj.siblings("input[name=total_payment]").val(total_payment);
  };

  function fsubmit_check(f) {
    if ($("input[name^=ct_chk]:checked").length < 1) {
      alert("구매하실 상품을 하나이상 선택해 주십시오.");
      return false;
    }

    return true;
  }

  function form_check(act) {
    var f = document.frmcartlist;
    var cnt = f.records.value;

    if (act == "buy") {
      /*
        if($("input[name^=ct_chk]:checked").length < 1) {
            alert("주문하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }
		*/

      f.act.value = act;
      f.submit();
    } else if (act == "alldelete") {
      f.act.value = act;
      f.submit();
    } else if (act == "seldelete") {
      if ($("input[name^=ct_chk]:checked").length < 1) {
        alert("삭제하실 상품을 하나이상 선택해 주십시오.");
        return false;
      }

      f.act.value = act;
      f.submit();
    }

    return true;
  }

  // 리뷰작성 이동
  $(document).on("click", "#cart_del", function() {

    var del_it_id = $(this).attr("data-id");

    if (!del_it_id) {
      alert('삭제하실 제품이 존재하지 않습니다.');
      return false
    }
    $("input:hidden[name='del_it_id']").val(del_it_id);
    $("input:hidden[name='act']").val('seldelete');
    $("#sod_bsk_list").submit();
  });
</script>
<style>
  .coupon_box {
    padding: 0 40px;
  }

  .coupon_box .coupon_box_list {
    width: 100%;
  }

  .coupon_box .coupon_box_list>ul {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .coupon_box .coupon_box_list>ul>li:first-child {
    text-align: right;
    padding: 0 20px;
  }

  .coupon_box .coupon_box_list>ul>li:last-child {
    width: 80%;
  }

  .coupon_box .coupon_box_list>ul>li h2 {
    width: auto;
    white-space: nowrap;
  }

  .coupon_box .coupon_box_list>ul>li select {
    width: 100%;
    padding: 5px;
  }

  .pay_info_box hr.horizontal_line {
    display: block;
    margin: 10px 0;
  }

  .pay_info_box .pay_text p::after {
    content: " 원";
  }

  .page_list_btn.cart_del {
    line-height: normal;
    margin-top: 0;
    padding: 6px;
    font-size: 2.6em;
  }

  @media screen and (max-width: 1024px) {
    .object_info_name {
      font-size: 3em;
    }
    .page_list_btn.cart_del {
      font-size: 2em;
    }
  }
</style>

<?php
include_once('./_tail.php');
