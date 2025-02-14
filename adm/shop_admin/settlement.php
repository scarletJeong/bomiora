<?php
$sub_menu = '400900';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = '정산관리';
include_once(G5_ADMIN_PATH . '/admin.head.php');
include_once(G5_PLUGIN_PATH . '/jquery-ui/datepicker.php');

$total_search = true;

$join_as = '';
$join_on = '';
$where_od = [];
$where_ct = [];
$where_it = [];

$doc = isset($_GET['doc']) ? clean_xss_tags($_GET['doc'], 1, 1) : '';
$sort1 = (isset($_GET['sort1']) && in_array($_GET['sort1'], array('od_id', 'od_cart_price', 'od_receipt_price', 'od_cancel_price', 'od_misu', 'od_cash'))) ? $_GET['sort1'] : '';
$sort2 = (isset($_GET['sort2']) && in_array($_GET['sort2'], array('desc', 'asc'))) ? $_GET['sort2'] : 'desc';
$sel_field = (isset($_GET['sel_field']) && in_array($_GET['sel_field'], array('od_id', 'mb_id', 'od_name', 'od_tel', 'od_hp', 'od_b_name', 'od_b_tel', 'od_b_hp', 'od_deposit_name', 'od_invoice', 'it_name', 'it_id', 'it_mb_inf'))) ? $_GET['sel_field'] : '';
$od_status = isset($_GET['od_status']) ? get_search_string($_GET['od_status']) : '';
$search = isset($_GET['search']) ? get_search_string($_GET['search']) : '';

$fr_date = (isset($_GET['fr_date']) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['fr_date'])) ? $_GET['fr_date'] : '';
$to_date = (isset($_GET['to_date']) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['to_date'])) ? $_GET['to_date'] : '';

$od_misu = isset($_GET['od_misu']) ? preg_replace('/[^0-9a-z]/i', '', $_GET['od_misu']) : '';
$od_cancel_price = isset($_GET['od_cancel_price']) ? preg_replace('/[^0-9a-z]/i', '', $_GET['od_cancel_price']) : '';
$od_refund_price = isset($_GET['od_refund_price']) ? preg_replace('/[^0-9a-z]/i', '', $_GET['od_refund_price']) : '';
$od_receipt_point = isset($_GET['od_receipt_point']) ? preg_replace('/[^0-9a-z]/i', '', $_GET['od_receipt_point']) : '';
$od_coupon = isset($_GET['od_coupon']) ? preg_replace('/[^0-9a-z]/i', '', $_GET['od_coupon']) : '';
$od_settle_case = isset($_GET['od_settle_case']) ? clean_xss_tags($_GET['od_settle_case'], 1, 1) : '';
$od_escrow = isset($_GET['od_escrow']) ? clean_xss_tags($_GET['od_escrow'], 1, 1) : '';

$od_once = [];
$tot_itemcount = $tot_sellprice = $tot_ct_receipt_price = $tot_cp_price = $tot_ct_point = $tot_anormal_cnt = 0;
$tot_receipt_price = $tot_ordercancel = $tot_misu = $tot_send_cost = 0;

if (!$fr_date) {
  $fr_date = date('Y-m-01');
}

if (!$to_date) {
  $to_date = date('Y-m-t');
}

$sql_search = "";
if ($search != "") {
  if ($sel_field != "") {
    if (in_array($sel_field, ['it_name','it_id'])) {
      if ($sel_field == 'it_id') {
        $where_ct[] = " ${sel_field} = '$search' ";
      } else {
        $where_ct[] = " ${sel_field} like '%$search%' ";
      }
    } else if ($sel_field == 'it_mb_inf') {
      $where_it[] = " ${sel_field} = '$search' ";
    } else {
      $where_od[] = " $sel_field like '%$search%' ";
    }
  }

  if ($save_search != $search) {
    $page = 1;
  }
}

if ($od_status) {
  switch ($od_status) {
    case '전체취소':
      $where_od[] = " od_status = '취소' ";
      break;
    case '부분취소':
      $where_od[] = " od_status IN('주문', '입금', '준비', '배송', '완료') and od_cancel_price > 0 ";
      break;
    case '취소제외정산':
      $where_od[] = " od_status IN('배송', '완료') ";
      break;
    default:
      $where_od[] = " od_status = '$od_status' ";
      break;
  }

  switch ($od_status) {
    case '주문':
      $sort1 = "od_id";
      $sort2 = "desc";
      break;
    case '입금':   // 결제완료
      $sort1 = "od_receipt_time";
      $sort2 = "desc";
      break;
    case '배송':   // 배송중
      $sort1 = "od_invoice_time";
      $sort2 = "desc";
      break;
  }
}

if ($od_settle_case) {
  if ($od_settle_case === '간편결제') {
    $where_od[] = " od_settle_case in ('간편결제', '삼성페이', 'lpay', 'inicis_kakaopay') ";
  } else {
    $where_od[] = " od_settle_case = '$od_settle_case' ";
  }
}

if ($od_misu) {
  $where_od[] = " od_misu != 0 ";
}

if ($od_cancel_price) {
  $where_od[] = " od_cancel_price != 0 ";
}

if ($od_refund_price) {
  $where_od[] = " od_refund_price != 0 ";
}

if ($od_receipt_point) {
  $where_od[] = " od_receipt_point != 0 ";
}

if ($od_coupon) {
  $where_od[] = " ( od_cart_coupon > 0 or od_coupon > 0 or od_send_coupon > 0 ) ";
}

if ($od_escrow) {
  $where_od[] = " od_escrow = 1 ";
}

if ($fr_date && $to_date) {
  $where_od[] = " od_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}

$cond = '';
if ($where_ct) {
  $cond = ' and a.' . implode(' and a.', array_map('trim', $where_ct));
}

if ($where_od) {
  $cond .= ' and b.' . implode(' and b.', array_map('trim', $where_od));
}

if ($where_it) {
  $cond .= ' and c.' . implode(' and c.', array_map('trim', $where_it));
}

$sql = "select
 a.ct_id,a.od_id,a.mb_id,a.it_id,a.it_name,a.ct_price,a.ct_point,a.cp_price,a.ct_option,a.ct_qty,a.io_price,((ct_price+io_price)*ct_qty) as sell_price,a.ct_mb_inf,a.ct_inf_price,
 b.od_name,b.od_b_name,b.od_email,b.od_tel,b.od_cart_count,b.od_cart_price,b.od_cart_coupon,b.od_send_cost,b.od_send_cost2,b.od_send_coupon,b.od_receipt_price,b.od_cancel_price,b.od_receipt_point,b.od_mobile,
 b.od_refund_price,b.od_receipt_time,b.od_coupon,b.od_misu,b.od_status,b.od_settle_case,b.od_pg,b.od_tax_mny,b.od_vat_mny,b.od_delivery_company,b.od_invoice,b.od_invoice_time,b.od_time,b.od_ip,
 c.ca_id,c.ca_id2,c.it_kind,c.it_cust_price,c.it_price,c.it_mb_inf,c.it_inf_price,c.it_org_id
 from {$g5['g5_shop_cart_table']} a
 left join {$g5['g5_shop_order_table']} b on a.od_id = b.od_id
 left join {$g5['g5_shop_item_table']} c on a.it_id = c.it_id
 where a.ct_output = 'Y' and b.od_status is not null {$cond} ";

//var_dump($sql);
$sql_cnt = " select count(cc.ct_id) AS cnt from ({$sql}) AS cc";
$row = sql_fetch($sql_cnt);
$total_count = $row['cnt'];

//var_dump($total_count);
//exit;

//$rows = $config['cf_page_rows'];
$rows = 25;
if ($fr_date && $to_date) {
  $rows = 10000;
}
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) {
  $page = 1;
} // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sort = '';
if ($sort1 && $sort2) {
  $sort = ", b.{$sort1} {$sort2}";
}

$sql = " {$sql} order by a.ct_id desc {$sort} limit {$from_record}, {$rows} ";

//var_dump($sql);
//exit;
$result = sql_query($sql);

$qstr1 = "od_status=" . urlencode($od_status) . "&amp;od_settle_case=" . urlencode($od_settle_case) . "&amp;od_misu=$od_misu&amp;od_cancel_price=$od_cancel_price&amp;od_refund_price=$od_refund_price&amp;od_receipt_point=$od_receipt_point&amp;od_coupon=$od_coupon&amp;fr_date=$fr_date&amp;to_date=$to_date&amp;sel_field=$sel_field&amp;search=$search&amp;save_search=$search";
if ($default['de_escrow_use']) {
  $qstr1 .= "&amp;od_escrow=$od_escrow";
}
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';

if (function_exists('pg_setting_check')) {
  pg_setting_check(true);
}
?>

<div class="local_ov01 local_ov">
  <?php echo $listall; ?>
  <span class="btn_ov01"><span class="ov_txt">전체 주문내역</span><span class="ov_num"> <?php echo number_format($total_count); ?>건</span></span>
  <?php if ($od_status == '준비' && $total_count > 0) { ?>
    <a href="./orderdelivery.php" id="order_delivery" class="ov_a">엑셀배송처리</a>
  <?php } ?>
</div>

<div class="btn_fixed_top">
  <form name="fsettlementexcel" method="post" action="./settlementexcel.php" onsubmit="return fsettlementexcelcheck(this);" target = "_top">
    <input type="hidden" name="data_sql" value="<?php echo $sql; ?>">
    <input type="hidden" name="total_count" value="<?php echo $total_count; ?>">
    <input type="submit" value="엑셀저장" class="btn btn_01">
  </form>
</div>

<form name="frmorderlist" class="<?php echo ($total_search ? 'local_sch03' : 'local_sch01'); ?> local_sch">
  <input type="hidden" name="doc" value="<?php echo $doc; ?>">
  <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
  <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
  <input type="hidden" name="page" value="<?php echo $page; ?>">
  <input type="hidden" name="save_search" value="<?php echo $search; ?>">

  <label for="sel_field" class="sound_only">검색대상</label>
  <select name="sel_field" id="sel_field">
    <option value="it_id" <?php echo get_selected($sel_field, 'it_id'); ?>>상품코드</option>
    <option value="od_id" <?php echo get_selected($sel_field, 'od_id'); ?>>주문번호</option>
    <option value="it_name" <?php echo get_selected($sel_field, 'it_name'); ?>>주문상품</option>
    <option value="mb_id" <?php echo get_selected($sel_field, 'mb_id'); ?>>회원 ID</option>
    <option value="it_mb_inf" <?php echo get_selected($sel_field, 'it_mb_inf'); ?>>인플루언서 ID</option>
    <option value="od_name" <?php echo get_selected($sel_field, 'od_name'); ?>>주문자</option>
    <option value="od_tel" <?php echo get_selected($sel_field, 'od_tel'); ?>>주문자전화</option>
    <option value="od_hp" <?php echo get_selected($sel_field, 'od_hp'); ?>>주문자핸드폰</option>
    <option value="od_b_name" <?php echo get_selected($sel_field, 'od_b_name'); ?>>받는분</option>
    <option value="od_b_tel" <?php echo get_selected($sel_field, 'od_b_tel'); ?>>받는분전화</option>
    <option value="od_b_hp" <?php echo get_selected($sel_field, 'od_b_hp'); ?>>받는분핸드폰</option>
    <option value="od_deposit_name" <?php echo get_selected($sel_field, 'od_deposit_name'); ?>>입금자</option>
    <option value="od_invoice" <?php echo get_selected($sel_field, 'od_invoice'); ?>>운송장번호</option>
  </select>
<?php if ($total_search) { ?>
  <input type="text" name="search" value="<?php echo $search; ?>" id="search" class="frm_input" autocomplete="off">
<?php } else { ?>
  <label for="search" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
  <input type="text" name="search" value="<?php echo $search; ?>" id="search" required class="required frm_input" autocomplete="off">
  <input type="submit" value="검색" class="btn_submit">
</form>
<form class="local_sch03 local_sch">
<?php } ?>
  <div>
    <strong>주문상태</strong>
    <input type="radio" name="od_status" value="" id="od_status_all" <?php echo get_checked($od_status, '');     ?>>
    <label for="od_status_all">전체</label>
    <input type="radio" name="od_status" value="주문" id="od_status_odr" <?php echo get_checked($od_status, '주문'); ?>>
    <label for="od_status_odr">주문</label>
    <input type="radio" name="od_status" value="입금" id="od_status_income" <?php echo get_checked($od_status, '입금'); ?>>
    <label for="od_status_income">입금</label>
    <input type="radio" name="od_status" value="준비" id="od_status_rdy" <?php echo get_checked($od_status, '준비'); ?>>
    <label for="od_status_rdy">준비</label>
    <input type="radio" name="od_status" value="배송" id="od_status_dvr" <?php echo get_checked($od_status, '배송'); ?>>
    <label for="od_status_dvr">배송</label>
    <input type="radio" name="od_status" value="완료" id="od_status_done" <?php echo get_checked($od_status, '완료'); ?>>
    <label for="od_status_done">완료</label>
    <input type="radio" name="od_status" value="전체취소" id="od_status_cancel" <?php echo get_checked($od_status, '전체취소'); ?>>
    <label for="od_status_cancel">전체취소</label>
    <input type="radio" name="od_status" value="부분취소" id="od_status_pcancel" <?php echo get_checked($od_status, '부분취소'); ?>>
    <label for="od_status_pcancel">부분취소</label>
    <input type="radio" name="od_status" value="취소제외정산" id="od_status_pcancel" <?php echo get_checked($od_status, '취소제외정산'); ?>>
    <label for="od_status_pcancel">취소제외정산</label>
  </div>

  <div>
    <strong>결제수단</strong>
    <input type="radio" name="od_settle_case" value="" id="od_settle_case01" <?php echo get_checked($od_settle_case, '');          ?>>
    <label for="od_settle_case01">전체</label>
    <input type="radio" name="od_settle_case" value="무통장" id="od_settle_case02" <?php echo get_checked($od_settle_case, '무통장');    ?>>
    <label for="od_settle_case02">무통장</label>
    <input type="radio" name="od_settle_case" value="가상계좌" id="od_settle_case03" <?php echo get_checked($od_settle_case, '가상계좌');  ?>>
    <label for="od_settle_case03">가상계좌</label>
    <input type="radio" name="od_settle_case" value="계좌이체" id="od_settle_case04" <?php echo get_checked($od_settle_case, '계좌이체');  ?>>
    <label for="od_settle_case04">계좌이체</label>
    <input type="radio" name="od_settle_case" value="휴대폰" id="od_settle_case05" <?php echo get_checked($od_settle_case, '휴대폰');    ?>>
    <label for="od_settle_case05">휴대폰</label>
    <input type="radio" name="od_settle_case" value="신용카드" id="od_settle_case06" <?php echo get_checked($od_settle_case, '신용카드');  ?>>
    <label for="od_settle_case06">신용카드</label>
    <input type="radio" name="od_settle_case" value="간편결제" id="od_settle_case07" <?php echo get_checked($od_settle_case, '간편결제');  ?>>
    <label for="od_settle_case07" data-tooltip-text="NHN_KCP 간편결제 : PAYCO, 네이버페이, 카카오페이(NHN_KCP) &#xa;LG유플러스 간편결제 : PAYNOW &#xa;KG 이니시스 간편결제 : KPAY, 삼성페이, LPAY, 카카오페이(KG이니시스)">PG간편결제</label>
    <input type="radio" name="od_settle_case" value="KAKAOPAY" id="od_settle_case08" <?php echo get_checked($od_settle_case, 'KAKAOPAY');  ?>>
    <label for="od_settle_case08">KAKAOPAY</label>
  </div>

  <div>
    <strong>기타선택</strong>
    <input type="checkbox" name="od_misu" value="Y" id="od_misu01" <?php echo get_checked($od_misu, 'Y'); ?>>
    <label for="od_misu01">미수금</label>
    <input type="checkbox" name="od_cancel_price" value="Y" id="od_misu02" <?php echo get_checked($od_cancel_price, 'Y'); ?>>
    <label for="od_misu02">반품,품절</label>
    <input type="checkbox" name="od_refund_price" value="Y" id="od_misu03" <?php echo get_checked($od_refund_price, 'Y'); ?>>
    <label for="od_misu03">환불</label>
    <input type="checkbox" name="od_receipt_point" value="Y" id="od_misu04" <?php echo get_checked($od_receipt_point, 'Y'); ?>>
    <label for="od_misu04">포인트주문</label>
    <input type="checkbox" name="od_coupon" value="Y" id="od_misu05" <?php echo get_checked($od_coupon, 'Y'); ?>>
    <label for="od_misu05">쿠폰</label>
    <?php if ($default['de_escrow_use']) { ?>
      <input type="checkbox" name="od_escrow" value="Y" id="od_misu06" <?php echo get_checked($od_escrow, 'Y'); ?>>
      <label for="od_misu06">에스크로</label>
    <?php } ?>
  </div>

  <div class="sch_last">
    <strong>주문일자</strong>
    <input type="text" id="fr_date" name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input" size="10" maxlength="10"> ~
    <input type="text" id="to_date" name="to_date" value="<?php echo $to_date; ?>" class="frm_input" size="10" maxlength="10">
    <button type="button" onclick="javascript:set_date('오늘');">오늘</button>
    <button type="button" onclick="javascript:set_date('어제');">어제</button>
    <button type="button" onclick="javascript:set_date('이번주');">이번주</button>
    <button type="button" onclick="javascript:set_date('이번달');">이번달</button>
    <button type="button" onclick="javascript:set_date('지난주');">지난주</button>
    <button type="button" onclick="javascript:set_date('지난달');">지난달</button>
    <button type="button" onclick="javascript:set_date('전체');">전체</button>
    <input type="submit" value="검색" class="btn_submit">
    <?php if (!$total_search && $search != "") { ?>
      <input type="button" value="결과 내 검색" class="btn_submit" onclick="search_result(this, '<?php echo $sel_field; ?>', '<?php echo $search; ?>')">
    <?php } ?>
  </div>
</form>

<form name="forderlist" id="forderlist" onsubmit="return forderlist_submit(this);" method="post" autocomplete="off">
  <input type="hidden" name="search_od_status" value="<?php echo $od_status; ?>">

  <div class="tbl_head01 tbl_wrap">
    <table id="sodr_list">
      <caption>주문 내역 목록</caption>
      <thead>
        <tr>
          <th scope="col" id="th_ordnum" rowspan="2" colspan="2"><a href="<?php echo title_sort("od_id", 1) . "&amp;$qstr1"; ?>">주문번호</a></th>
          <th scope="col" id="th_odrer">주문자</th>
          <th scope="col" id="th_odrertel">주문자전화</th>
          <th scope="col" id="th_recvr">받는분</th>
          <th scope="col" rowspan="3">상품금액</th>
          <th scope="col" rowspan="3">쿠폰</th>
          <th scope="col" rowspan="3">포인트</th>
          <th scope="col" rowspan="3">지불금액<br>(배송비제외)</th>
          <th scope="col" rowspan="3">지불상태</th>
          <th scope="col" rowspan="3">보기</th>
        </tr>
        <tr>
          <th scope="col" id="th_odrid">회원ID</th>
          <th scope="col" id="th_odrcnt">주문상품수</th>
          <th scope="col" id="th_odrall">누적주문수</th>
        </tr>
        <tr>
          <th scope="col" id="odrstat">주문상태</th>
          <th scope="col" id="odrpay">결제수단</th>
          <th scope="col" id="delino">운송장번호</th>
          <th scope="col" id="delicom">배송회사</th>
          <th scope="col" id="delidate">배송일시</th>
        </tr>
      </thead>
      <tbody>
        <?php
        for ($i = 0; $row = sql_fetch_array($result); $i++) {
          // 결제 수단
          $s_receipt_way = $s_br = "";
          if ($row['od_settle_case']) {
            $s_receipt_way = check_pay_name_replace($row['od_settle_case'], $row);
            $s_br = '<br />';
          } else {
            $s_receipt_way = '결제수단없음';
            $s_br = '<br />';
          }

          if ($row['od_receipt_point'] > 0) {
            $s_receipt_way .= $s_br . "포인트";
          }

          $mb_nick = get_sideview($row['mb_id'], get_text($row['od_name']), $row['od_email'], '');

          $od_cnt = 0;
          if ($row['mb_id']) {
            $sql2 = " select count(*) as cnt from {$g5['g5_shop_order_table']} where mb_id = '{$row['mb_id']}' ";
            $row2 = sql_fetch($sql2);
            $od_cnt = $row2['cnt'];
          }

          // 주문 번호에 device 표시
          $od_mobile = '';
          if ($row['od_mobile']) {
            $od_mobile = '(M)';
          }

          // 주문 번호에 에스크로 표시
          $od_paytype = '';
          if ($default['de_escrow_use'] && $row['od_escrow']) {
            $od_paytype .= '<span class="list_escrow">에스크로</span>';
          }

          $invoice_time = is_null_time($row['od_invoice_time']) ? G5_TIME_YMDHIS : $row['od_invoice_time'];
          $delivery_company = $row['od_delivery_company'] ? $row['od_delivery_company'] : $default['de_delivery_company'];

          $bg = 'bg' . ($i % 2);
          if ($row['od_cancel_price'] > 0) {
            $bg .= 'cancel';
            $row['ct_receipt_price'] = 0;
            $row['ct_pay_status'] = '주문취소';
          } else {
            $row['ct_receipt_price'] = $row['sell_price'] - $row['cp_price'] - $row['ct_point'];
            if ($row['od_misu'] != 0) {
              $bg .= 'cancel';
              $row['ct_pay_status'] = '미수금';
            } else {
              $row['ct_pay_status'] = '정상';
            }
          }
        ?>
          <tr class="orderlist<?php echo ' ' . $bg; ?>">
            <td headers="th_ordnum" class="td_odrnum2" rowspan="2" colspan="2">
              <a href="#" class="orderitem"><?php echo $row['od_id']; ?></a>
              <?php echo $od_mobile; ?>
              <?php echo $od_paytype; ?>
              <?php
              echo "<br>" . stripslashes($row['it_name']) . " | ";
              echo $row['ct_option'];
              ?>
            </td>
            <td headers="th_odrer" class="td_name"><?php echo $mb_nick; ?></td>
            <td headers="th_odrertel" class="td_tel_x"><?php echo get_text($row['od_tel']); ?></td>
            <td headers="th_recvr" class="td_name"><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?sort1=<?php echo $sort1; ?>&amp;sort2=<?php echo $sort2; ?>&amp;sel_field=od_b_name&amp;search=<?php echo get_text($row['od_b_name']); ?>"><?php echo get_text($row['od_b_name']); ?></a></td>
            <td rowspan="3" class="td_num td_numsum"><?php echo number_format($row['sell_price']); ?></td>
            <td rowspan="3" class="td_num_right"><?php echo number_format($row['cp_price']); ?></td>
            <td rowspan="3" class="td_num_right"><?php echo number_format($row['ct_point']); ?></td>
            <td rowspan="3" class="td_num_right"><?php echo number_format($row['ct_receipt_price']); ?></td>
            <td rowspan="3" class="td_cancel"><?php echo $row['ct_pay_status']; ?></td>
            <td rowspan="3" class="td_mng td_mng_s">
              <a href="./orderform.php?od_id=<?php echo $row['od_id']; ?>&amp;<?php echo $qstr; ?>" class="mng_mod btn btn_02"><span class="sound_only"><?php echo $row['od_id']; ?> </span>보기</a>
            </td>
          </tr>
          <tr class="<?php echo $bg; ?>">
            <td headers="th_odrid">
              <?php if ($row['mb_id']) { ?>
                <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?sort1=<?php echo $sort1; ?>&amp;sort2=<?php echo $sort2; ?>&amp;sel_field=mb_id&amp;search=<?php echo $row['mb_id']; ?>"><?php echo $row['mb_id']; ?></a>
              <?php } else { ?>
                비회원
              <?php } ?>
            </td>
            <td headers="th_odrcnt"><?php echo $row['ct_qty']; ?>개</td>
            <td headers="th_odrall"><?php echo $od_cnt; ?>건</td>
          </tr>
          <tr class="<?php echo $bg; ?>">
            <td headers="odrstat" class="odrstat">
              <input type="hidden" name="current_status[<?php echo $i ?>]" value="<?php echo $row['od_status'] ?>">
              <?php echo $row['od_status']; ?>
            </td>
            <td headers="odrpay" class="odrpay">
              <input type="hidden" name="current_settle_case[<?php echo $i ?>]" value="<?php echo $row['od_settle_case'] ?>">
              <?php echo $s_receipt_way; ?>
            </td>
            <td headers="delino" class="delino">
              <?php if ($od_status == '준비') { ?>
                <input type="text" name="od_invoice[<?php echo $i; ?>]" value="<?php echo $row['od_invoice']; ?>" class="frm_input" size="10">
              <?php } else {
                echo ($row['od_invoice'] ? $row['od_invoice'] : '-');
              } ?>
            </td>
            <td headers="delicom">
              <?php if ($od_status == '준비') { ?>
                <select name="od_delivery_company[<?php echo $i; ?>]">
                  <?php echo get_delivery_company($delivery_company); ?>
                </select>
              <?php } else {
                echo ($row['od_delivery_company'] ? $row['od_delivery_company'] : '-');
              } ?>
            </td>
            <td headers="delidate">
              <?php if ($od_status == '준비') { ?>
                <input type="text" name="od_invoice_time[<?php echo $i; ?>]" value="<?php echo $invoice_time; ?>" class="frm_input" size="10" maxlength="19">
              <?php } else {
                echo (is_null_time($row['od_invoice_time']) ? '-' : substr($row['od_invoice_time'], 2, 14));
              } ?>
            </td>
          </tr>
        <?php
          $tot_itemcount     += $row['ct_qty'];
          $tot_sellprice    +=  $row['sell_price'];
          $tot_ct_receipt_price  += $row['ct_receipt_price'];
          $tot_cp_price       += $row['cp_price'];
          $tot_ct_point       += $row['ct_point'];
          if ($row['ct_pay_status'] != '정상') {
            $tot_anormal_cnt++;
          }

          if (!in_array($row['od_id'], $od_once)) {
            $od_once[] = $row['od_id'];
            $tot_receipt_price += $row['od_receipt_price'];
            $tot_ordercancel   += $row['od_cancel_price'];
            $tot_misu          += $row['od_misu'];
            $tot_send_cost     += ($row['od_send_cost'] + $row['od_send_cost2'] - $row['od_send_coupon']);
          }
        }
        sql_free_result($result);
        if ($i == 0) {
          echo '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
        }

        $tot_itemcount = number_format($tot_itemcount);
        $tot_sellprice = number_format($tot_sellprice);
        $tot_cp_price = number_format($tot_cp_price);
        $tot_ct_point = number_format($tot_ct_point);
        $tot_ct_receipt_price = number_format($tot_ct_receipt_price);
        $tot_receipt_price = number_format($tot_receipt_price);
        $tot_ordercancel = number_format($tot_ordercancel);
        $tot_misu = number_format($tot_misu * -1);
        $tot_send_cost = number_format($tot_send_cost);
        ?>
      </tbody>
      <tfoot>
        <tr class="orderlist">
          <th scope="row" colspan="3">&nbsp;</th>
          <td><?php echo $tot_itemcount; ?>개</td>
          <th scope="row">합 계</th>
          <td><?php echo $tot_sellprice; ?></td>
          <td><?php echo $tot_cp_price; ?></td>
          <td><?php echo $tot_ct_point; ?></td>
          <td><?php echo $tot_ct_receipt_price; ?></td>
          <td><?php echo ($tot_anormal_cnt > 0 ? '주의 ' . number_format($tot_anormal_cnt) . '건' : '정상'); ?></td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <div class="local_desc02 local_desc">
    <?php
    echo "상품개수 총계: {$tot_itemcount}개 | 상품금액 총계: {$tot_sellprice}원 | 쿠폰사용금액 총계: {$tot_cp_price}원 | 포인트사용금액 총계: {$tot_ct_point}원<br>";
    echo "입금액 총계: {$tot_receipt_price}원 | 배송비결제액 총계: {$tot_send_cost}원 | 취소금액 총계: {$tot_ordercancel}원 | 미수금액 총계: {$tot_misu}원";
    ?>
  </div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
  $(function() {
    $("#fr_date, #to_date").datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: "yy-mm-dd",
      showButtonPanel: true,
      yearRange: "c-99:c+99",
      maxDate: "+0d"
    });

    // 주문상품보기
    $(".orderitem").on("click", function() {
      var $this = $(this);
      var od_id = $this.text().replace(/[^0-9]/g, "");

      if ($this.next("#orderitemlist").length)
        return false;

      $("#orderitemlist").remove();

      $.post(
        "./ajax.orderitem.php", {
          od_id: od_id
        },
        function(data) {
          $this.after("<div id=\"orderitemlist\"><div class=\"itemlist\"></div></div>");
          $("#orderitemlist .itemlist")
            .html(data)
            .append("<div id=\"orderitemlist_close\"><button type=\"button\" id=\"orderitemlist-x\" class=\"btn_frmline\">닫기</button></div>");
        }
      );

      return false;
    });

    // 상품리스트 닫기
    $("#sodr_list").on("click", "#orderitemlist-x", function(e) {
      $("#orderitemlist").remove();
    });

    $("body").on("click", function(e) {
      if ($(e.target).closest("#orderitemlist").length === 0) {
        $("#orderitemlist").remove();
      }
    });

    // 엑셀배송처리창
    $("#order_delivery").on("click", function() {
      var opt = "width=600,height=450,left=10,top=10";
      window.open(this.href, "win_excel", opt);
      return false;
    });
  });

  function search_result(e, sel_field, search) {
    var form = $(e).closest("form");
    if (!form.length || sel_field == "" || search == "") {
      return false;
    }

    form.append($('<input>', {
      type: 'hidden',
      name: 'sel_field',
      val: sel_field
    }));
    form.append($('<input>', {
      type: 'hidden',
      name: 'search',
      val: search
    }));

    form.submit();
    return false;
  };

  function set_date(today) {
    <?php
    $date_term = date('w', G5_SERVER_TIME);
    $week_term = $date_term + 7;
    $last_term = strtotime(date('Y-m-01', G5_SERVER_TIME));
    ?>
    if (today == "오늘") {
      document.getElementById("fr_date").value = "<?php echo G5_TIME_YMD; ?>";
      document.getElementById("to_date").value = "<?php echo G5_TIME_YMD; ?>";
    } else if (today == "어제") {
      document.getElementById("fr_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
      document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
    } else if (today == "이번주") {
      document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-' . $date_term . ' days', G5_SERVER_TIME)); ?>";
      document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "이번달") {
      document.getElementById("fr_date").value = "<?php echo date('Y-m-01', G5_SERVER_TIME); ?>";
      document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "지난주") {
      document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-' . $week_term . ' days', G5_SERVER_TIME)); ?>";
      document.getElementById("to_date").value = "<?php echo date('Y-m-d', strtotime('-' . ($week_term - 6) . ' days', G5_SERVER_TIME)); ?>";
    } else if (today == "지난달") {
      document.getElementById("fr_date").value = "<?php echo date('Y-m-01', strtotime('-1 Month', $last_term)); ?>";
      document.getElementById("to_date").value = "<?php echo date('Y-m-t', strtotime('-1 Month', $last_term)); ?>";
    } else if (today == "전체") {
      document.getElementById("fr_date").value = "";
      document.getElementById("to_date").value = "";
    }
  }
</script>

<script>
  function forderlist_submit(f) {
    if (!is_checked("chk[]")) {
      alert(document.pressed + " 하실 항목을 하나 이상 선택하세요.");
      return false;
    }

    /*
    switch (f.od_status.value) {
        case "" :
            alert("변경하실 주문상태를 선택하세요.");
            return false;
        case '주문' :

        default :

    }
    */

    if (document.pressed == "선택삭제") {
      if (confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
        f.action = "./orderlistdelete.php";
        return true;
      }
      return false;
    }

    var change_status = f.od_status.value;

    if (f.od_status.checked == false) {
      alert("주문상태 변경에 체크하세요.");
      return false;
    }

    var chk = document.getElementsByName("chk[]");

    for (var i = 0; i < chk.length; i++) {
      if (chk[i].checked) {
        var k = chk[i].value;
        var current_settle_case = f.elements['current_settle_case[' + k + ']'].value;
        var current_status = f.elements['current_status[' + k + ']'].value;

        switch (change_status) {
          case "입금":
            if (!(current_status == "주문" && current_settle_case == "무통장")) {
              alert("'주문' 상태의 '무통장'(결제수단)인 경우에만 '입금' 처리 가능합니다.");
              return false;
            }
            break;

          case "준비":
            if (current_status != "입금") {
              alert("'입금' 상태의 주문만 '준비'로 변경이 가능합니다.");
              return false;
            }
            break;

          case "배송":
            if (current_status != "준비") {
              alert("'준비' 상태의 주문만 '배송'으로 변경이 가능합니다.");
              return false;
            }

            var invoice = f.elements['od_invoice[' + k + ']'];
            var invoice_time = f.elements['od_invoice_time[' + k + ']'];
            var delivery_company = f.elements['od_delivery_company[' + k + ']'];

            if ($.trim(invoice_time.value) == '') {
              alert("배송일시를 입력하시기 바랍니다.");
              invoice_time.focus();
              return false;
            }

            if ($.trim(delivery_company.value) == '') {
              alert("배송업체를 입력하시기 바랍니다.");
              delivery_company.focus();
              return false;
            }

            if ($.trim(invoice.value) == '') {
              alert("운송장번호를 입력하시기 바랍니다.");
              invoice.focus();
              return false;
            }

            break;
        }
      }
    }

    if (!confirm("선택하신 주문서의 주문상태를 '" + change_status + "'상태로 변경하시겠습니까?"))
      return false;

    f.action = "./orderlistupdate.php";
    return true;
  }

  function fsettlementexcelcheck(f) {
    var total_count = parseInt(f.total_count.value);
    if (total_count && total_count > 0) {
      return true;
    }
    alert("엑셀로 변환할 데이터가 없습니다.");
    return false;
  }
</script>

<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');
