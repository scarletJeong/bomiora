<?php
$sub_menu = "700100";
include_once('./_common.php');

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}
if ($_POST['act_button'] == "선택저장") {
    auth_check($auth[$sub_menu], 'w');

    for ($i=0; $i<count($_POST['chk']); $i++) { 
        $k = $_POST['chk'][$i];

        $sql = " update g5_write_qa
                    set wr_1				= '".sql_real_escape_string($_POST['wr_1'][$k])."',
                        wr_2				= '".sql_real_escape_string($_POST['wr_2'][$k])."',
                        wr_4				= '".sql_real_escape_string($_POST['wr_4'][$k])."',
                        wr_5				= '".sql_real_escape_string($_POST['wr_5'][$k])."',
                        wr_6				= '".sql_real_escape_string($_POST['wr_6'][$k])."',
                        
                        wr_7				= '".sql_real_escape_string($_POST['wr_7'][$k])."',
                        wr_content			= '".sql_real_escape_string($_POST['wr_content'][$k])."'

                  where wr_id				= '".sql_real_escape_string($_POST['wr_id'][$k])."' ";

        sql_query($sql);
    }
} else if ($_POST['act_button'] == "선택삭제") {
    auth_check($auth[$sub_menu], 'd');

    for ($i=0; $i<count($_POST['chk']); $i++) { 
        $k = $_POST['chk'][$i];
        $sql = " delete from g5_write_qa
                  where wr_id				= '".sql_real_escape_string($_POST['wr_id'][$k])."' ";

        sql_query($sql);
    }
}
alert($_POST['act_button'].'완료','./seller_qa.php?'.$qstr);
?>
