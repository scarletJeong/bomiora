<?php
$menu['menu200'] = array(
    array('200000', '일반회원관리', G5_ADMIN_URL . '/general_member_list.php', 'member'),
    array('200100', '일반회원관리', G5_ADMIN_URL . '/general_member_list.php', 'general_member_list'),
    array('200300', '회원메일발송', G5_ADMIN_URL . '/mail_list.php', 'mb_mail'),
	array('200400', '팝업레이어관리', G5_ADMIN_URL . '/newwinlist.php', 'scf_poplayer'),
    array('200800', '접속자집계', G5_ADMIN_URL . '/visit_list.php', 'mb_visit', 1),
    array('200810', '접속자검색', G5_ADMIN_URL . '/visit_search.php', 'mb_search', 1),
    array('200820', '접속자로그삭제', G5_ADMIN_URL . '/visit_delete.php', 'mb_delete', 1),
    array('200200', '포인트관리', G5_ADMIN_URL . '/point_list.php', 'mb_point'),
    array('200840', '관리권한설정', G5_ADMIN_URL.'/auth_list.php',     'cf_auth'),
	array('200830', '환경설정', G5_ADMIN_URL.'/config_form.php',     'config_form'),
    //array('200900', '투표관리', G5_ADMIN_URL . '/poll_list.php', 'mb_poll')
);
