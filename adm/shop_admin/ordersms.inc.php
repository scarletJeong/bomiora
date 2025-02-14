<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
if (!defined("_ORDERSMS_")) exit;

// jacknam
if($config['cf_sms_use']) {
  order_alimtalk('delivery', $od_id);
  //order_alimtalk('delivery', $od_id, [$default['de_sms_hp'],'010-9844-1114']);
}
// jacknam
