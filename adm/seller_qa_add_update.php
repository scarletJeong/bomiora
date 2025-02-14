<?php
$sub_menu = "700100";
include_once('./_common.php');


    auth_check($auth[$sub_menu], 'w');


    $phone = $_POST['phone'];
    $wr_name = $_POST['wr_name'];
    $wr_1_opt = $_POST['wr_1_opt'];//디비구분
    $wr_7 = $_POST['wr_7'];//r공구제품
    $wr_3 = $_POST['wr_3'];//디비구분
    $wr_4 = $_POST['wr_4'];//상담내용
    $wr_6 = $_POST['wr_6'];//주소
    $wr_8 = $_POST['wr_8'];//셀러
    $wr_9 = $_POST['wr_9'];//체험단
    $wr_link2 = $_POST['wr_link2'];//상담구분
    $vdevice = '외부';
    
    // 기록
    $sql = " insert into g5_write_qa
    set 
                wr_comment		= 0,
                wr_name			= '$wr_name',
                wr_homepage		= '$phone',
                wr_link1		= '$vdevice',
                wr_content	= '$wr_content',
                wr_link2		= '$wr_link2',
				wr_datetime		= '".G5_TIME_YMDHIS."',
				wr_last			= '".G5_TIME_YMDHIS."',
				wr_ip			= '{$_SERVER['REMOTE_ADDR']}',
				wr_3			= '$wr_3',
				wr_4			= '$wr_4',
				wr_6			= '$wr_6',
				wr_7			= '$wr_7',
				wr_8			= '$wr_8',
				wr_9			= '$wr_9',
				wr_1			= '$wr_1_opt' ";
sql_query($sql);


alert($_POST['act_button'].'완료','./seller_qa.php?'.$qstr);
?>
