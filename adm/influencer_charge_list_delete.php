<?php
$sub_menu = '300200';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'd');

check_admin_token();

$count = (isset($_POST['chk']) && is_array($_POST['chk'])) ? count($_POST['chk']) : 0;
if (!$count) {
    alert($_POST['act_button'] . ' 하실 항목을 하나 이상 체크하세요.');
}

for ($i = 0; $i < $count; $i++) {
    // 실제 번호를 넘김
    $k = $_POST['chk'][$i];
    $ch_id = (int) $_POST['ch_id'][$k];
    $str_mb_id = sql_real_escape_string($_POST['mb_id'][$k]);

    // 포인트 내역정보
    $sql = " select * from {$g5['in_charge_table']} where ch_id = '{$ch_id}' ";
    $row = sql_fetch($sql);

    if (!$row['ch_id']) {
        continue;
    }

    if ($row['ch_point'] < 0) {
        $mb_id = $row['mb_id'];
        $ch_point = abs($row['ch_point']);

        if ($row['ch_rel_table'] == '@expire') {
            delete_expire_charge($mb_id, $ch_point);
        } else {
            delete_use_charge($mb_id, $ch_point);
        }
    } else {
        if ($row['ch_use_point'] > 0) {
            insert_use_charge($row['mb_id'], $row['ch_use_point'], $row['ch_id']);
        }
    }

    // 포인트 내역삭제
    $sql = " delete from {$g5['in_charge_table']} where ch_id = '{$ch_id}' ";
    sql_query($sql);

    // ch_mb_point에 반영 : 충전에 반영시키지는 않는다.???
	/*
    $sql = " update {$g5['in_charge_table']}
                set ch_mb_point = ch_mb_point - '{$row['ch_point']}'
                where mb_id = '{$str_mb_id}'
                  and ch_id > '{$ch_id}' ";
    sql_query($sql);
	*/

    // 포인트 UPDATE : 충전에 반영시키지는 않는다.???
	/*
    $sum_point = get_charge_sum($_POST['mb_id'][$k]);
    $sql = " update {$g5['member_table']} set mb_charge = '$sum_point' where mb_id = '{$str_mb_id}' ";
    sql_query($sql);
	*/
}

goto_url('./influencer_charge_list.php?' . $qstr);
