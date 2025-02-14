<?php
$sub_menu = '400650';
include_once('./_common.php');

check_demo();

check_admin_token();

$count_post_chk = (isset($_POST['chk']) && is_array($_POST['chk'])) ? count($_POST['chk']) : 0;

if (! $count_post_chk) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

if ($_POST['act_button'] === "선택수정") {
    auth_check_menu($auth, $sub_menu, 'w');
} else if ($_POST['act_button'] === "선택삭제") {
    auth_check_menu($auth, $sub_menu, 'd');
} else {
    alert("선택수정이나 선택삭제 작업이 아닙니다.");
}

for ($i=0; $i<$count_post_chk; $i++)
{
    $k = isset($_POST['chk'][$i]) ? (int) $_POST['chk'][$i] : 0; // 실제 번호를 넘김
    $iit_id = isset($_POST['it_id'][$k]) ? preg_replace('/[^a-z0-9_\-]/i', '', $_POST['it_id'][$k]) : '';
    $iis_id = isset($_POST['is_id'][$k]) ? (int) $_POST['is_id'][$k] : 0;
    $iis_score1 = isset($_POST['is_score1'][$k]) ? (int) $_POST['is_score1'][$k] : 0;
	$iis_score2 = isset($_POST['is_score2'][$k]) ? (int) $_POST['is_score2'][$k] : 0;
	$iis_score3 = isset($_POST['is_score3'][$k]) ? (int) $_POST['is_score3'][$k] : 0;
	$iis_score4 = isset($_POST['is_score4'][$k]) ? (int) $_POST['is_score4'][$k] : 0;
    $iis_confirm = isset($_POST['is_confirm'][$k]) ? (int) $_POST['is_confirm'][$k] : 0;

    if ($_POST['act_button'] == "선택수정")
    {
        $sql = "update {$g5['g5_shop_item_use_table']}
                   set is_score1   = '{$iis_score1}',
				   	   is_score2   = '{$iis_score2}',
					   is_score3   = '{$iis_score3}',
					   is_score4   = '{$iis_score4}',
                       is_confirm = '{$iis_confirm}'
                 where is_id      = '{$iis_id}' ";
        sql_query($sql);

		//update_use_point($mb_id, $iis_id, $iis_confirm, 'u');// 회원아이디, 후기일련번호, 노출여부, (등록,수정,삭제)

    }
    else if ($_POST['act_button'] == "선택삭제")
    {
        $sql = "delete from {$g5['g5_shop_item_use_table']} where is_id = '{$iis_id}' ";
        sql_query($sql);

		update_use_point($mb_id, $iis_id, $iis_confirm, 'd');// 회원아이디, 후기일련번호, 노출여부, (등록,수정,삭제)
    }
    
    if($iit_id){
        update_use_cnt($iit_id);
        update_use_avg($iit_id);
    }
}

goto_url("./itemuselist.php?sca=$sca&amp;sca2=$sca2&amp;sca3=$sca3&amp;sst=$sst&amp;sod=$sod&amp;sfl=$sfl&amp;stx=$stx&amp;page=$page");