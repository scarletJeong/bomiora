<?php
require_once './_common.php';

$target = $_POST['target'];
$task = $_POST['task'];

$error = admin_referer_check(true);
if ($target == '' || $error) {
    die(json_encode(array('error' => 1, 'url' => G5_URL)));
}

function post_selected($ta_no, $mb_id) {
  if (order_tester_item($ta_no, $mb_id)) {
    tester_alimtalk('selected', $ta_no);
    return true;
  }
  
  return false; 
}

function tester_set($task) {
  global $g5;

  $tr_no = $_POST['tr_no'];
  $ta_no = $_POST['ta_no'];
  $mb_id = $_POST['mb_id'];

  if (!$tr_no || !$ta_no || !$mb_id) {
    return array('error' => 1, 'url' => G5_URL);
  }

  $reload = true;
  $where = "where ta_no = '{$ta_no}' and tr_no = '{$tr_no}' and mb_id = '{$mb_id}'";

  switch ($task) {
    case 'select':
    $sql = "update {$g5['g5_tester_apply_table']} set selected = '1', s_datetime = '" . G5_TIME_YMDHIS . "', datetime = '" . G5_TIME_YMDHIS . "' {$where};";
    break;
    case 'cancel':
    $sql = "update {$g5['g5_tester_apply_table']} set apply_cancel = 'y', selected = '0', datetime = '" . G5_TIME_YMDHIS . "' {$where};";
    break;
    case 'reviewrequest':
    $sql = "update {$g5['g5_tester_apply_table']} set msg_sent = 'y', datetime = '" . G5_TIME_YMDHIS . "' {$where};";
    $reload = false;
    break;
    case 'cancelselect':
    $od_id = $_POST['od_id'];
    if (!$od_id) {
      return array('error' => 1, 'url' => G5_URL);
    }
    $sql = "update {$g5['g5_tester_apply_table']} set selected = '0', od_id = '', s_datetime = '" . G5_TIME_YMDHIS . "', datetime = '" . G5_TIME_YMDHIS . "' {$where};";
    break;
    case 'cancelback':
    $sql = "update {$g5['g5_tester_apply_table']} set apply_cancel = 'n', selected = '0', datetime = '" . G5_TIME_YMDHIS . "' {$where};";
    break;
    default:
    return array('error' => 1, 'url' => G5_URL);
    break;
  }

  $msg = '';
  $result = sql_query($sql);
  if ($result) {
    $error = 0;
    
    if ($task == 'select') {
      if (!post_selected($ta_no, $mb_id)) {
        $error = 1;
        $msg = '체험단 선정이 실패하였습니다.';        
      }
    } else if ($task == 'reviewrequest') {
      if (tester_alimtalk('reviewrequest', $ta_no)) {
        $error = 0;
        $msg = '성공적으로 전송되었습니다.';
      } else {
        $error = 1;
        $msg = '메시지 전송이 실패하였습니다.';
      }
    }  else if ($task == 'cancelselect') {
      cleanup_tester_selected($od_id);
    }
  } else {
    $error = 1;
    $msg = '실패';
  }

  return array('error' => $error, 'msg' => $msg, 'task' => $task, 'reload' => ($reload ? 'y' : 'n'), 'sql' => $sql);
}

switch ($target) {
  case 'tester_set':
  $json_array = tester_set($task);
  break;
  default:
  $json_array = array('error' => 1, 'url' => G5_URL);
  break;
}

//die(json_encode(array('target' => $target, 'field' => $field, 'value' => $value)));
die(json_encode($json_array, true));
