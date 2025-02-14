<?php
$sub_menu = '600200';
include_once('./_common.php');

check_demo();

check_admin_token();

$count_post_chk = (isset($_POST['chk']) && is_array($_POST['chk'])) ? count($_POST['chk']) : 0;

if (!$count_post_chk) {
  alert($_POST['act_button'] . " 하실 항목을 하나 이상 체크하세요.");
}

if ($_POST['act_button'] === "선택수정") {
  auth_check_menu($auth, $sub_menu, 'w');
} else if ($_POST['act_button'] === "선택삭제") {
  auth_check_menu($auth, $sub_menu, 'd');
} else {
  alert("선택수정이나 선택삭제 작업이 아닙니다.");
}

for ($i = 0; $i < $count_post_chk; $i++) {
  $k = isset($_POST['chk'][$i]) ? (int) $_POST['chk'][$i] : 0; // 실제 번호를 넘김
  $mr_no = isset($_POST['mr_no'][$k]) ? (int) $_POST['mr_no'][$k] : 0;
  $mr_confirm = isset($_POST['mr_confirm'][$k]) ? (int) $_POST['mr_confirm'][$k] : 0;

  //var_dump([$k, $mr_no, $mr_confirm]);
  //exit;

  if ($_POST['act_button'] == "선택수정") {
    $sql = "update {$g5['g5_main_review_table']} set mr_confirm = '{$mr_confirm}' where mr_no = '{$mr_no}' ";
    sql_query($sql);
  } else if ($_POST['act_button'] == "선택삭제") {
    $sql = "delete from {$g5['g5_main_review_table']} where mr_no = '{$mr_no}' ";
    sql_query($sql);
  }
}

goto_url('./main_review_list.php?' . $qstr, false);
