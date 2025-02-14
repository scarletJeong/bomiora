<?php
require_once './_common.php';
//include_once(G5_LIB_PATH.'/json.lib.php');

$target = $_POST['target'];
$task = $_POST['task'];

$error = admin_referer_check(true);
if ($target == '' || $error) {
    die(json_encode(array('error' => 1, 'url' => G5_URL)));
}

function member_search($task) {
  global $g5;

  $field = $_POST['field'];
  $value = $_POST['value'];

  if ($task == 'influencer_set') {
    $items = 'mb_no, mb_id, mb_inf_code, mb_name, mb_nick, mb_email, mb_hp, mb_instar_link, mb_datetime';
    $level = 2;
  } else {
    $items = '*';
    $level = 0;
  }

  if ($_POST['items']) {
    //$items = $_POST['items'];
  }

  if ($_POST['level']) {
    $level = $_POST['level'];
  }

  //$items = $_POST['items'] ?? "*";
  //$level = $_POST['level'] ?? 0;

  $sql = "select {$items} from {$g5['member_table']} where ";
  if ($level > 0) {
    $sql .= " mb_level = '${level}' and ";
  }

  switch ($field) {
    case 'mb_charge':
    $sql .= " mb_charge >= '${value}' ";
    break;
    case 'mb_level':
    $sql .= " mb_level = '${value}' ";
    break;
    case 'mb_tel':
    case 'mb_hp':
    $sql .= " ${field} like '%{$value}' ";
    break;
    default:
    $sql .= " ${field} like '%${value}%' ";
    break;
  }

  $sql .= " order by mb_datetime desc limit 0, 100 ";
  //$rows = sql_query($sql);
  //$rows = sql_fetch_array(sql_query($sql));

  $rows = [];
  $result = sql_query($sql);
  for ($i = 0; $row = sql_fetch_array($result); $i++) {
    $rows[] = $row;
  }

  return array('error' => 0, 'rows' => $rows, 'length' => count($rows), 'task' => $task ); //'sql' => $sql,
}

function vendor_inf_list($task) {
  global $g5;
  global $_const;

  if ($task == 'add_inf') {
    $mb_id = $_POST['mb_id'];
    if (!$mb_id) {
      return array('error' => 1, 'msg' => '필수 정보가 누락되었습니다.' );
    }

    $sql = "SELECT * FROM {$g5['member_table']} a left join {$g5['g5_shop_vendor_table']} b on a.mb_id = b.i_id
            WHERE a.mb_level = '{$_const['level']['인플루언서']}' AND a.mb_inf_code <> '' AND a.mb_id <> '{$mb_id}' and (isnull(b.v_id) or b.v_id <> '{$mb_id}') ORDER BY a.mb_id DESC;";
  } else if ($task == 'list_inf') {
    $mb_id = $_POST['mb_id'];
    if (!$mb_id) {
      return array('error' => 1, 'msg' => '필수 정보가 누락되었습니다.' );
    }
    $sql = "SELECT * FROM {$g5['g5_shop_vendor_table']} a LEFT JOIN {$g5['member_table']} b ON a.i_id = b.mb_id WHERE a.v_id = '{$mb_id}' ORDER BY mb_no DESC;";
  } else {
    //add_vendor
    $sql = "select * from {$g5['member_table']} where mb_level = '{$_const['level']['인플루언서']}' and mb_inf_code <> '' and mb_5 <> 'y' order by mb_id desc;";
  }

  $rows = [];
  $result = sql_query($sql);
  for ($i = 0; $row = sql_fetch_array($result); $i++) {
    $rows[] = $row;
  }

  return array('error' => 0, 'rows' => $rows, 'length' => count($rows), 'task' => $task);//,'sql' => $sql);
}

function vendor_inf_set($task) {
  $vendor_id = $_POST['vendor_id'];
  $mb_id = $_POST['mb_id'];

  if (!$vendor_id || !$mb_id) {
    return array('error' => 1, 'msg' => '필수 정보가 누락되었습니다.' );
  }

  global $g5;

  if ($task == 'inf_add') {
    $sql = " insert into {$g5['g5_shop_vendor_table']} (v_id, i_id, datetime) values ('{$vendor_id}', '{$mb_id}', '".G5_TIME_YMDHIS."');";
  } else {
    $sql = " delete from {$g5['g5_shop_vendor_table']} where v_id = '{$vendor_id}' and i_id = '{$mb_id}' ";
  }

  $result = sql_query($sql);

  return array('error' => 0, 'result' => $result);//, 'sql' => $sql);
}

function vendor_set($task) {


  global $g5;
  //global $_const;

  if ($task == 'vendor_list_remove') {
    $vendor_ids = $_POST['vendor_ids'];

    $vendor_ids = explode('|', $vendor_ids);
    $vendor_ids = "'" . implode("','", $vendor_ids) . "'";

    $sql = " update {$g5['member_table']} set mb_5 = '' where mb_id in ({$vendor_ids}); ";
  } else {
    $mb_no = $_POST['mb_no'];
    $mb_id = $_POST['mb_id'];
    $mb_inf_code = $_POST['mb_inf_code'];

    if (!$mb_no || !$mb_id) { // || !$mb_inf_code) {
      return array('error' => 1, 'msg' => '필수 정보가 누락되었습니다.' );
    }

    if ($task == 'vendor_add') {
      $mb_5 = 'y';
    } else {
      $mb_5 = '';
    }
    $sql = " update {$g5['member_table']} set mb_5 = '{$mb_5}' where mb_no = '{$mb_no}' and mb_id = '{$mb_id}' ";
  }

  $result = sql_query($sql);

  return array('error' => 0, 'result' => $result); //, 'sql' => $sql
}

function influencer_set($task) {
  $mb_no = $_POST['mb_no'];
  $mb_inf_code = $_POST['mb_inf_code'];
  $mb_instar_link = $_POST['mb_instar_link'];

  if (!$mb_no) {
    return array('error' => 1, 'msg' => '필수 정보가 누락되었습니다.' );
  }

  global $g5;
  global $_const;

  if ($task == 'influencer_add') {
    $mb_level = $_const['level']['인플루언서'];
  } else {
    $mb_level = $_const['level']['일반'];
  }

  if (!$mb_inf_code) {
    $mb_inf_code = get_uniqid();
  }

  if ($mb_instar_link) {
    $mb_instar_link = set_http(get_text($mb_instar_link), "https://");
  }

  $sql = " update {$g5['member_table']} set mb_level = '{$mb_level}', mb_inf_code = '{$mb_inf_code}', mb_instar_link = '{$mb_instar_link}' where mb_no = '{$mb_no}' ";
  $result = sql_query($sql);

  //return array('error' => 1, 'msg' => $g5['member_table'] . ':' . $_const['level']['인플루언서'] );

  return array('error' => 0, 'result' => $result);//, 'sql' => $sql

}

switch ($target) {
  case 'member_search':
  $json_array = member_search($task);
  break;
  case 'vendor_inf_list':
  $json_array = vendor_inf_list($task);
  break;
  case 'influencer_set':
  $json_array = influencer_set($task);
  break;
  case 'vendor_inf_set':
  $json_array = vendor_inf_set($task);
  break;
  case 'vendor_set':
  $json_array = vendor_set($task);
  break;
  default:
  $json_array = array('error' => 1, 'url' => G5_URL);
  break;
}

//die(json_encode(array('target' => $target, 'field' => $field, 'value' => $value)));
die(json_encode($json_array, true));
