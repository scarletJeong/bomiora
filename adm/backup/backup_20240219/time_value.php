<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/rsvt.lib.php');

$od_id = isset($_POST['it_id']) ? safe_replace_regex($_POST['od_id'], 'od_id') : '';
$it_id = isset($_POST['it_id']) ? safe_replace_regex($_POST['it_id'], 'it_id') : '';
$hp_doc_name = isset($_REQUEST['hp_doc_name']) ? clean_xss_tags(trim($_REQUEST['hp_doc_name']), 1, 1) : '';// 의사명
$d_day = isset($_REQUEST['d_day']) ? clean_xss_tags(trim($_REQUEST['d_day']), 1, 1) : date('Y-m-d');// 날짜
/*

// 지난시간은 빼고 가져와야 한다.


if(isset($it['it_id']) && $it['it_id']) {// 아직 안넘어온 상태
	$select = date('Y-m-d');
} else {// 날짜가 넘어온 상태
	$select = $d_day;
}
$select = date('Y-m-d');
echo $select;
*/
?>
					<h2>
                        시간 선택
                    </h2>
                    <div>
						<?php
						$am = booking_time($bo_table, $d_day); // 예약가능한시간대별리스트
						//print_r2($am);
						echo radio_time($am,$am,$wr_2,'wr_2');
						?>



                    </div>					