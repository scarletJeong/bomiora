<?php
$sub_menu = "300200";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

check_admin_token();

$mb_id = isset($_POST['mb_id']) ? strip_tags(clean_xss_attributes($_POST['mb_id'])) : '';
$ch_point = isset($_POST['ch_point']) ? (int)strip_tags(clean_xss_attributes($_POST['ch_point'])) : 0;
$ch_content = isset($_POST['ch_content']) ? strip_tags(clean_xss_attributes($_POST['ch_content'])) : '';
$ch_sub_content = isset($_POST['ch_sub_content']) ? strip_tags(clean_xss_attributes($_POST['ch_sub_content'])) : '';
//$expire = isset($_POST['po_expire_term']) ? preg_replace('/[^0-9]/', '', $_POST['po_expire_term']) : '';


//$mb = get_member($mb_id);
$mb = get_use_member($mb_id, $_const['level']['인플루언서']);

if (!$mb['mb_id']) {
    alert('존재하는 회원아이디가 아닙니다.', './influencer_charge_list.php?' . $qstr);
}
/*
if (($po_point < 0) && ($po_point * (-1) > $mb['mb_point'])) {
    alert('포인트를 깎는 경우 현재 포인트보다 작으면 안됩니다.', './point_list.php?' . $qstr);
}
*/

//insert_point($mb_id, $po_point, $po_content, '@passive', $mb_id, $member['mb_id'] . '-' . uniqid(''), $expire);
insert_charge($mb_id, $ch_point, $ch_content, $ch_sub_content, '@passive', $mb_id, $member['mb_id'].'-'.uniqid(''), '1');

goto_url('./influencer_charge_list.php?' . $qstr);
