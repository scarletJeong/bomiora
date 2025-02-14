<?php
$sub_menu = '800300';
include_once('./_common.php');

check_admin_token();

$count_post_chk = (isset($_POST['chk']) && is_array($_POST['chk'])) ? count($_POST['chk']) : 0;
$post_act_button = isset($_POST['act_button']) ? $_POST['act_button'] : '';

if (!$count_post_chk) {
  alert($post_act_button . " 하실 항목을 하나 이상 체크하세요.");
}

if ($post_act_button == "처방하기") {

  auth_check_menu($auth, $sub_menu, 'w');

  $od_ids = [];
  for ($i = 0; $i < $count_post_chk; $i++) {
    // 실제 번호를 넘김
    $k = isset($_POST['chk'][$i]) ? (int) $_POST['chk'][$i] : 0;
    $p_hp_no  = isset($_POST['hp_no'][$k])       ? clean_xss_tags($_POST['hp_no'][$k], 1, 1)        : '';
    if (!$p_hp_no) {
      continue;
    }
    $p_hp_8    = (isset($_POST['hp_8']) && is_array($_POST['hp_8'])) ? strip_tags($_POST['hp_8'][$k]) : '';
    $p_hp_9    = (isset($_POST['hp_9']) && is_array($_POST['hp_9'])) ? strip_tags($_POST['hp_9'][$k]) : '';
    $p_hp_10  = (isset($_POST['hp_10']) && is_array($_POST['hp_10'])) ? strip_tags($_POST['hp_10'][$k]) : '';
    $p_hp_memo  = (isset($_POST['hp_memo']) && is_array($_POST['hp_memo'])) ? strip_tags($_POST['hp_memo'][$k]) : '';
    $p_od_id  = (isset($_POST['od_id']) && is_array($_POST['od_id'])) ? strip_tags($_POST['od_id'][$k]) : '';
    $p_mb_id  = (isset($_POST['mb_id']) && is_array($_POST['mb_id'])) ? strip_tags($_POST['mb_id'][$k]) : '';
    $p_pr_date  = (isset($_POST['pr_date']) && is_array($_POST['pr_date'])) ? strip_tags($_POST['pr_date'][$k]) : '';

    if (!$p_hp_10) {
      $p_hp_10 = $p_hp_9 == 'prescription' ? 'completion' : 'ongoing';
    }

    $sql  = "update {$g5['g5_shop_health_profile_cart_table']}";
    $sql .= " set hp_8 = '" . sql_real_escape_string($p_hp_8) . "',";
    $sql .= " hp_9 = '" . sql_real_escape_string($p_hp_9) . "',";
    $sql .= " hp_10 = '" . sql_real_escape_string($p_hp_10) . "',";
    $sql .= " hp_memo = '" . sql_real_escape_string($p_hp_memo) . "',";
    $sql .= " hp_mdatetime = '" . G5_TIME_YMDHIS . "'";
    if ($p_od_id && $p_mb_id) {
      $sql .= " where od_id = '" . $p_od_id . "' and mb_id = '" . $p_mb_id  . "' ";
    } else {
      $sql .= " where hp_no = '" . $p_hp_no . "' ";
    }
    sql_query($sql);

    $prescribed = $p_pr_date && $p_pr_date != '-';
    if ($config['cf_sms_use'] && $p_od_id && $p_hp_9 == 'prescription' && !$prescribed) {
      $od_ids[] = $p_od_id;
    }
  }

  if ($od_ids) {
    foreach ($od_ids as $od_id) {
      order_alimtalk('prescription', $od_id);
    }
  }
} else if ($post_act_button == "선택삭제") {

  for ($i = 0; $i < $count_post_chk; $i++) {
    // 실제 번호를 넘김
    $k = isset($_POST['chk'][$i]) ? (int) $_POST['chk'][$i] : 0;
    $p_hp_no  = isset($_POST['hp_no'][$k]) ? clean_xss_tags($_POST['hp_no'][$k], 1, 1) : '';
    if (!$p_hp_no) {
      continue;
    }
    $p_od_id  = (isset($_POST['od_id']) && is_array($_POST['od_id'])) ? strip_tags($_POST['od_id'][$k]) : '';
    $p_mb_id  = (isset($_POST['mb_id']) && is_array($_POST['mb_id'])) ? strip_tags($_POST['mb_id'][$k]) : '';
    if ($p_od_id && $p_mb_id) {
      $cond = " od_id = '{$p_od_id}' and mb_id = '{$p_mb_id}' ";
    } else {
      $cond = " hp_no = '{$p_hp_no}' ";
    }
    $sql = "update {$g5['g5_shop_health_profile_cart_table']} set hp_output = 'N', hp_mdatetime = '" . G5_TIME_YMDHIS . "' where {$cond} ";
    sql_query($sql);
  }
}

goto_url("./diagnosis2.php?sel_name=$sca&sel_name;sel_sex=$sel_sex&amp;sel_prescription=$sel_prescription&amp;page=$page");
