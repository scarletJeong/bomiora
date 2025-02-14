<?php
$sub_menu = '400900';
include_once('./_common.php');

$data_sql = isset($_REQUEST['data_sql']) ? str_replace('\\', '', clean_xss_tags($_REQUEST['data_sql'], 1, 1)) : '';
$total_count = isset($_REQUEST['total_count']) ? preg_replace('/[^0-9]/i', '', $_REQUEST['total_count']) : '';

if (!$data_sql || !$total_count) {
  alert("잘못된 요청입니다.");
}

if ((int)$total_count == 0) {
  alert("출력할 내역이 없습니다.");
}

auth_check_menu($auth, $sub_menu, "r");

function column_char($i) {
  return chr(65 + $i);
}

//var_dump($data_sql);
//exit;

// MS엑셀 XLS 데이터로 다운로드 받음

$result = sql_query($data_sql);
$cnt = @sql_num_rows($result);
if (!$cnt) {
  alert("출력할 내역이 없습니다.");
}

include_once(G5_LIB_PATH . '/PHPExcel.php');

$headers = array('번호', '주문번호', '주문상품', '주문상태', '결제수단', '에스크로', '주문기기', '주문자', '주문자전화', '회원ID', '누적주문수', '운송장번호',
 '배송회사', '받는분', '배송일시', '주문상품수', '상품금액', '쿠폰', '포인트', '지불금액', '지불상태');
$widths  = array(5, 20, 20, 10, 10, 10, 10, 10, 15, 15, 10, 20, 15, 10, 15, 10, 12, 12, 12, 15, 10);
$header_bgcolor = 'B4C6E7';
$last_char = column_char(count($headers) - 1);

$od_once = [];
$tot_itemcount = $tot_sellprice = $tot_ct_receipt_price = $tot_cp_price = $tot_ct_point = $tot_anormal_cnt = 0;
$tot_receipt_price = $tot_ordercancel = $tot_misu = $tot_send_cost = 0;

$rows = [];
for ($i = 1; $row = sql_fetch_array($result); $i++) {
  // 주문 상품
  $it_name = stripslashes($row['it_name']) . " | " . $row['ct_option'];

  // 결제 수단
  if ($row['od_settle_case']) {
    $s_receipt_way = check_pay_name_replace($row['od_settle_case'], $row);
  } else {
    $s_receipt_way = '결제수단없음';
  }

  if ($row['od_receipt_point'] > 0) {
    $s_receipt_way .= ', 포인트';
  }

  $mb_nick = get_sideview($row['mb_id'], get_text($row['od_name']), $row['od_email'], '');

  $od_cnt = 0;
  if ($row['mb_id']) {
    $sql2 = " select count(*) as cnt from {$g5['g5_shop_order_table']} where mb_id = '{$row['mb_id']}' ";
    $row2 = sql_fetch($sql2);
    $od_cnt = $row2['cnt'];
  }

  // device 표시
  $od_device = $row['od_mobile'] ? '휴대폰' : 'PC';

  // 에스크로 표시
  $od_escrow = $default['de_escrow_use'] && $row['od_escrow'] ? '예' : '아니오';

  // 지불상태
  if ($row['od_cancel_price'] > 0) {
    $bg .= 'cancel';
    $row['ct_receipt_price'] = '0';
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

  $rows[] = array(
    $i,
    ' ' . $row['od_id'],
    stripslashes($row['it_name']) . " | " . $row['ct_option'],
    $row['od_status'],
    $s_receipt_way,
    $row['od_escrow'] ? '예' : '아니오',
    $row['od_mobile'] ? '휴대폰' : 'PC',
    $row['od_name'],
    hyphen_hp_number($row['od_tel']),
    $row['mb_id'],
    $od_cnt,
    $row['od_invoice'] ? $row['od_invoice'] : '-',
    $row['od_delivery_company'] ? $row['od_delivery_company'] : '-',
    $row['od_invoice'] ? $row['od_b_name'] : '-',
    is_null_time($row['od_invoice_time']) ? '-' : substr($row['od_invoice_time'], 2, 14),
    $row['ct_qty'],
    $row['sell_price'],
    $row['cp_price'],
    $row['ct_point'],
    $row['ct_receipt_price'],
    $row['ct_pay_status']
  );

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

$rows[] = array(
  '합계',
  '',
  '',
  '',
  '',
  '',
  '',
  '',
  '',
  '',
  '',
  '',
  '',
  '',
  '',
  $tot_itemcount,
  $tot_sellprice,
  $tot_cp_price,
  $tot_ct_point,
  $tot_ct_receipt_price,
  $tot_anormal_cnt > 0 ? '주의 ' . number_format($tot_anormal_cnt) . '건' : '정상'
);

$last_row = count($rows) + 1;

$data = array_merge(array($headers), $rows);

$borderStyle = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);

$excel = new PHPExcel();
$excel->setActiveSheetIndex(0)->getStyle("B")->getNumberFormat()->setFormatCode('@');
$excel->setActiveSheetIndex(0)->getStyle("P:{$last_char}")->getNumberFormat()->setFormatCode('#,##0');
$excel->setActiveSheetIndex(0)->getStyle("A1:{$last_char}1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($header_bgcolor);
$excel->setActiveSheetIndex(0)->getStyle("A:$last_char")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
$excel->setActiveSheetIndex(0)->mergeCells("A{$last_row}:O{$last_row}");
$excel->setActiveSheetIndex(0)->getStyle("A{$last_row}:{$last_char}{$last_row}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($header_bgcolor);
$excel->setActiveSheetIndex(0)->getStyle("A1:{$last_char}{$last_row}")->applyFromArray($borderStyle);

foreach ($widths as $i => $w) $excel->setActiveSheetIndex(0)->getColumnDimension(column_char($i))->setWidth($w);
$excel->getActiveSheet()->fromArray($data, NULL, 'A1');

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"정산관리-" . date("Ymd", time()) . ".xls\"");
header("Cache-Control: max-age=0");

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$writer->save('php://output');

?>