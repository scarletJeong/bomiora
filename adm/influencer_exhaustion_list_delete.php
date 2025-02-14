<?php
$sub_menu = '300400';
include_once('./_common.php');

//check_demo();

auth_check($auth[$sub_menu], 'd');

check_admin_token();

// 회원
if ($sfl_mb_id != '') $qstr .= "&sfl_mb_id=".$sfl_mb_id;
// 분류
if ($sfl_ch_content) $qstr .= "&sfl_ch_content=".$sfl_ch_content;
// 이벤트명
if ($sfl_ch_rel_id != '') $qstr .= "&sfl_ch_rel_id=".$sfl_ch_rel_id;
// 기간
if($stx_sday && $stx_eday) {
	$qstr .= "&stx_sday=".$stx_sday;
	$qstr .= "&stx_eday=".$stx_eday;
}

$count = count($_POST['chk']);
if(!$count)
    alert($_POST['act_button'].' 하실 항목을 하나 이상 체크하세요.');

for ($i=0; $i<$count; $i++)
{
    // 실제 번호를 넘김
    $k = $_POST['chk'][$i];

    // 충전내역정보
    $sql = " select * from {$g5['in_charge_table']} where ch_id = '{$_POST['ch_id'][$k]}' ";
    $row = sql_fetch($sql);

    if(!$row['ch_id'])
        continue;

    if($row['ch_point'] < 0) {
        $mb_id = $row['mb_id'];
        $po_point = abs($row['ch_point']);

        if($row['ch_rel_table'] == '@expire')
            delete_expire_charge($mb_id, $po_point);//소멸 충전액
        else
            delete_use_charge($mb_id, $po_point);//사용충전액
    } else {
        if($row['ch_use_point'] > 0) {
            insert_use_charge($row['mb_id'], $row['ch_use_point'], $row['ch_id']);
        }
    }

    // 충전내역삭제
    $sql = " delete from {$g5['in_charge_table']} where ch_id = '{$_POST['ch_id'][$k]}' ";
    sql_query($sql);

    /* ch_mb_point에 반영 : 충전에 반영시키지는 않는다.
    $sql = " update {$g5['in_charge_table']}
                set ch_mb_point = ch_mb_point - '{$row['ch_point']}'
                where mb_id = '{$_POST['mb_id'][$k]}'
                  and ch_id > '{$_POST['ch_id'][$k]}' ";
    sql_query($sql);
	*/

    /* 충전액 UPDATE : 충전에 반영시키지는 않는다.
    $sum_point = get_charge_sum($_POST['mb_id'][$k]);
    $sql= " update {$g5['member_table']} set mb_point = '$sum_point' where mb_id = '{$_POST['mb_id'][$k]}' ";
    sql_query($sql);
	*/
}

goto_url('./exhaustion_list.php?'.$qstr);
?>