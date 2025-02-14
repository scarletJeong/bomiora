<?php
$sub_menu = '800100';
include_once('./_common.php');

//check_admin_token();

$count_post_chk = (isset($_POST['chk']) && is_array($_POST['chk'])) ? count($_POST['chk']) : 0;
$post_act_button = isset($_POST['act_button']) ? $_POST['act_button'] : '';

if (! $count_post_chk) {
    alert($post_act_button." 하실 항목을 하나 이상 체크하세요.");
}

if ($post_act_button == "처방하기") {

    auth_check_menu($auth, $sub_menu, 'w');

    $od_ids = [];
    for ($i=0; $i< $count_post_chk; $i++) {
        // 실제 번호를 넘김
      $k = isset($_POST['chk'][$i]) ? (int) $_POST['chk'][$i] : 0;
  		$p_hp_no	= isset($_POST['hp_no'][$k])       ? clean_xss_tags($_POST['hp_no'][$k], 1, 1)        : '';
  		$p_hp_8		= (isset($_POST['hp_8']) && is_array($_POST['hp_8'])) ? strip_tags($_POST['hp_8'][$k]) : '';
  		$p_hp_9		= (isset($_POST['hp_9']) && is_array($_POST['hp_9'])) ? strip_tags($_POST['hp_9'][$k]) : '';
  		$p_hp_10	= (isset($_POST['hp_10']) && is_array($_POST['hp_10'])) ? strip_tags($_POST['hp_10'][$k]) : '';
  		$p_hp_memo	= (isset($_POST['hp_memo']) && is_array($_POST['hp_memo'])) ? strip_tags($_POST['hp_memo'][$k]) : '';

  		// 이미 처방을 했는지를 따져야 되서 여기서 쿼리작성
  		//jacknam
      $sql = " select a.it_id, a.it_name, a.od_id, b.hp_rsvt_tel, b.hp_9 from {$g5['g5_shop_cart_table']} a, {$g5['g5_shop_health_profile_cart_table']} b
      where a.od_id = b.od_id and a.it_id = b.it_id and b.hp_no = '{$p_hp_no}' ";
      $row = sql_fetch($sql);

      $sql = "update {$g5['g5_shop_health_profile_cart_table']}
      set hp_8 = '".sql_real_escape_string($p_hp_8)."',
      hp_9 = '".sql_real_escape_string($p_hp_9)."',
      hp_10 = '".sql_real_escape_string($p_hp_10)."',
      hp_memo = '".sql_real_escape_string($p_hp_memo)."',
      hp_mdatetime = '".G5_TIME_YMDHIS."'
      where hp_no = '".$p_hp_no."' ";
      sql_query($sql);

      if($config['cf_sms_use'] && $row && $p_hp_9 == 'prescription' && $row['hp_9'] != 'prescription') {
        $od_ids[] = $row['od_id'];
        //order_alimtalk('prescription', $row['od_id']);
        //order_alimtalk('prescription', $row['od_id'], [$default['de_sms_hp']]);
      }
  		//jacknam
    }
    
    if ($od_ids) {
      foreach ($od_ids as $od_id) {
        order_alimtalk('prescription', $od_id);
        //order_alimtalk('prescription', $od_id, [$default['de_sms_hp']]);        
      }
    }
    
    
} else if ($post_act_button == "선택삭제") {

	for ($i=0; $i< $count_post_chk; $i++) {
        // 실제 번호를 넘김
        $k = isset($_POST['chk'][$i]) ? (int) $_POST['chk'][$i] : 0;

        $p_hp_no	= isset($_POST['hp_no'][$k])       ? clean_xss_tags($_POST['hp_no'][$k], 1, 1)        : '';

		$sql = "update {$g5['g5_shop_health_profile_cart_table']} set hp_output = 'N', hp_mdatetime = '".G5_TIME_YMDHIS."' where hp_no   = '".$p_hp_no."' ";
		sql_query($sql);
    }
}

goto_url("./diagnosis.php?sel_name=$sca&sel_name;sel_sex=$sel_sex&amp;sel_prescription=$sel_prescription&amp;page=$page");

