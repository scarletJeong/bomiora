<?php
$sub_menu = '400400';
include_once('./_common.php');
include_once('./admin.shop.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

auth_check_menu($auth, $sub_menu, "w");

define("_ORDERMAIL_", true);

$sms_count = 0;
$sms_messages = array();

if(isset($_FILES['excelfile']['tmp_name']) && $_FILES['excelfile']['tmp_name']) {
    $file = $_FILES['excelfile']['tmp_name'];

    include_once(G5_LIB_PATH.'/PHPExcel/IOFactory.php');

    $objPHPExcel = PHPExcel_IOFactory::load($file);
    $sheet = $objPHPExcel->getSheet(0);

    $num_rows = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    $fail_od_id = array();
    $total_count = 0;
    $fail_count = 0;
    $succ_count = 0;

    // $i 사용시 ordermail.inc.php의 $i 때문에 무한루프에 빠짐
    for ($k = 2; $k <= $num_rows; $k++) {
        $total_count++;

        $rowData = $sheet->rangeToArray('A' . $k . ':' . $highestColumn . $k,
                                            NULL,
                                            TRUE,
                                            FALSE);

        $od_id               = isset($rowData[0][0]) ? addslashes(trim($rowData[0][0])) : '';
        $od_delivery_company = isset($rowData[0][8]) ? addslashes($rowData[0][8]) : '';
        $od_invoice          = isset($rowData[0][9]) ? addslashes($rowData[0][9]) : '';

        if(!$od_id || !$od_delivery_company || !$od_invoice) {
            $fail_count++;
            $fail_od_id[] = $od_id;
            continue;
        }

        // 주문정보
        $od = sql_fetch(" select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' ");
        if (!$od) {
            $fail_count++;
            $fail_od_id[] = $od_id;
            continue;
        }

        if($od['od_status'] != '준비') {
            $fail_count++;
            $fail_od_id[] = $od_id;
            continue;
        }

        $delivery['invoice'] = $od_invoice;
        $delivery['invoice_time'] = G5_TIME_YMDHIS;
        $delivery['delivery_company'] = $od_delivery_company;

        // 주문정보 업데이트
        order_update_delivery($od_id, $od['mb_id'], '배송', $delivery);
        change_status($od_id, '준비', '배송');

        $succ_count++;
        
        $send_sms = isset($_POST['send_sms']) ? clean_xss_tags($_POST['send_sms'], 1, 1) : '';
        $od_send_mail = isset($_POST['od_send_mail']) ? clean_xss_tags($_POST['od_send_mail'], 1, 1) : '';
        $send_escrow = isset($_POST['send_escrow']) ? clean_xss_tags($_POST['send_escrow'], 1, 1) : '';

        // SMS
        //jacknam
        if($config['cf_sms_use']) {
          order_alimtalk('delivery', $od_id);
          //order_alimtalk('delivery', $od_id, [$default['de_sms_hp'],'010-9844-1114']);
        }
        // 포인트 적립 (배송 시)
        order_point($od_id);     
        //jacknam

        // 메일
        if($config['cf_email_use'] && $od_send_mail)
            include './ordermail.inc.php';

        // 에스크로 배송
        if($send_escrow && $od['od_tno'] && $od['od_escrow']) {
            $escrow_tno  = $od['od_tno'];
            $escrow_numb = $od_invoice;
            $escrow_corp = $od_delivery_company;

            include(G5_SHOP_PATH.'/'.$od['od_pg'].'/escrow.register.php');
        }
    }
}

$g5['title'] = '엑셀 배송일괄처리 결과';
include_once(G5_PATH.'/head.sub.php');
?>

<div class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <div class="local_desc01 local_desc">
        <p>배송일괄처리를 완료했습니다.</p>
    </div>

    <dl id="excelfile_result">
        <dt>총배송건수</dt>
        <dd><?php echo number_format($total_count); ?></dd>
        <dt class="result_done">완료건수</dt>
        <dd class="result_done"><?php echo number_format($succ_count); ?></dd>
        <dt class="result_fail">실패건수</dt>
        <dd class="result_fail"><?php echo number_format($fail_count); ?></dd>
        <?php if($fail_count > 0) { ?>
        <dt>실패주문코드</dt>
        <dd><?php echo implode(', ', $fail_od_id); ?></dd>
        <?php } ?>
    </dl>

    <div class="btn_confirm01 btn_confirm">
        <button type="button" onclick="window.close();">창닫기</button>
    </div>

</div>

<?php
include_once(G5_PATH.'/tail.sub.php');