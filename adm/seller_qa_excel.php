<?php
$to_day = date('Y-m-d');
$output_file_name = "셀러/체험단 리스트";

include_once('./_common.php');

header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename={$output_file_name}-{$to_day}.xls" );
header( "Content-Description: PHP4 Generated Data");



if (!$sst) {
    $sst = "wr_id";
    $sod = "desc";
}

$sql_order = " order by {$sst} {$sod} ";
$sql_common = " from g5_write_qa ";
$sql_search = " where 1 = 1 ";

if($fr_date != ''){
	$sql_search = $sql_search." and date(wr_datetime) between '{$fr_date}' and '{$to_date}' ";
}
if (!empty($swr_1)) {
	$sql_search = $sql_search." and wr_1 = '{$swr_1}' ";
}
if (!empty($swr_2)) {
	$sql_search = $sql_search." and wr_2 = '{$swr_2}' ";
}
if (!empty($wr_link1)) {
	$sql_search = $sql_search." and wr_link1 = '{$wr_link1}' ";
}
if (!empty($wr_link2)) {
	$sql_search = $sql_search." and wr_link2 = '{$wr_link2}' ";
}
if (!empty($stx)) {
	$sql_search = $sql_search." and {$sfl} like '%{$stx}%' ";
}

$sql = " select *
            {$sql_common}
            {$sql_search}
            {$sql_order} ";
$result = sql_query($sql);



/*================================================================================
php_writeexcel http://www.bettina-attack.de/jonny/view.php/projects/php_writeexcel/
=================================================================================*/

// include_once('../PHPExcel/lib/Excel/php_writeexcel/class.writeexcel_workbook.inc.php');
// include_once('../PHPExcel/lib/Excel/php_writeexcel/class.writeexcel_worksheet.inc.php');


// $fname = tempnam(G5_DATA_PATH,"tmp-itemqa.xls");
// $workbook = new writeexcel_workbook($fname);
// $worksheet = $workbook->addworksheet();




// // Put Excel data
// $data = array('등록일', '구분', '상담구분', '이름', '연락처', '상담내용', '매니저상담내용', '내원상담/리콜 일정', '상담진행사항');
// //$data = array_map('iconv_euckr', $data);

// $col = 0;
// foreach($data as $cell) {
// 	$worksheet->write(0, $col++,iconv('utf-8','euc-kr',$cell));
// }


// $save_it_id = '';
//     for($i=1; $row=sql_fetch_array($result); $i++) {
//         $item_sub_cate_name = gr_cate_name($row['ca_id'], $row['it_type1']);
// 		$it_mb = get_member($row['it_mb_id']);
		
// 		$worksheet->write($i, 0, iconv('utf-8','euc-kr',item_cate_name($row['ca_id'])));// 이벤트 구분
// 		$worksheet->write($i, 1, iconv('utf-8','euc-kr',$row['it_name']));// 이벤트명
// 		$worksheet->write($i, 2, iconv('utf-8','euc-kr',$item_sub_cate_name['ca_name']));// 카테고리
// 		$worksheet->write($i, 3, iconv('utf-8','euc-kr',$it_mb['mb_name']));// 병원명
// 		$worksheet->write($i, 4, $row['it_mb_id']);// 병원 아이디
// 		$worksheet->write($i, 5, iconv('utf-8','euc-kr',$row['iq_name']));// 이름
//         $worksheet->write($i, 6, $row['iq_hp']);// 전화번호
//         $worksheet->write($i, 7, iconv('utf-8','euc-kr',$_const['call_time_info'][$row['iq_calltime']]));// 통화가능시간
//         $worksheet->write($i, 8, iconv('utf-8','euc-kr',$row['iq_question']));// 문의 내용
// 		$worksheet->write($i, 9, iconv('utf-8','euc-kr',$row['iq_manager_content']));// 매니저상담내용
//         $worksheet->write($i, 10, iconv('utf-8','euc-kr',$row['iq_recall_content']));// 내원상담리콜일정
// 		$worksheet->write($i, 11, iconv('utf-8','euc-kr',$_const['counsel_status_arr'][$row['iq_status_fk']]));// 상담진행상황
// 		$worksheet->write($i, 12, $row['mb_id']);
// 		$worksheet->write($i, 13, $row['iq_1']);
// 		$worksheet->write($i, 14, $row['iq_time']);
// 		$worksheet->write($i, 15, $row['iq_ip']);

//     }

//     $workbook->close();

//     header("Content-Type: application/x-msexcel; name=\"".iconv('utf-8','euc-kr','이벤트상담DB')."-".date("ymd", time()).".xls\"");
//     header("Content-Disposition: inline; filename=\"".iconv('utf-8','euc-kr','이벤트상담DB')."-".date("ymd", time()).".xls\"");
//     $fh=fopen($fname, "rb");
//     fpassthru($fh);
//     unlink($fname);

//     exit;


//     //변경중

$EXCEL_STR = "
<table border='1'>
<tr>
   <td>등록일</td>
   <td>구분</td>
   <td>이름</td>
   <td>연락처</td>
   <td>셀러신청</td>
   <td>체험단신청</td>
   <td>주소</td>
   <td>공구제품</td>
   <td>상담내용</td>
   <td>상담일정</td>
</tr>
";
while($row = sql_fetch_array($result)) { 
	$tmp = "";
	if($row['wr_1'] == "1") $tmp = "확인전";
	else if($row['wr_1'] == "2") $tmp = "상담중";
	else if($row['wr_1'] == "3") $tmp = "상담완료";
	else if($row['wr_1'] == "4") $tmp = "상담예약";
	else if($row['wr_1'] == "5") $tmp = "1차부재";
	else if($row['wr_1'] == "6") $tmp = "2차부재";
	else if($row['wr_1'] == "7") $tmp = "3차부재";
	else if($row['wr_1'] == "8") $tmp = "4차부재";
	else if($row['wr_1'] == "9") $tmp = "5차부재";
	else if($row['wr_1'] == "10") $tmp = "리콜대상";
	else if($row['wr_1'] == "11") $tmp = "에러";
	else if($row['wr_1'] == "12") $tmp = "내원안함";
	else if($row['wr_1'] == "13") $tmp = "내원";
	else if($row['wr_1'] == "14") $tmp = "수술완료";
	else if($row['wr_1'] == "15") $tmp = "예약취소";

	
	$EXCEL_STR .= "
		<tr>
			
			<td>".substr($row['wr_datetime'],0,10)."</td>
			<td>".$row['wr_link1']."</td>
			<td>".$row['wr_link2']."</td>
			<td>".$row['wr_name']."</td>
			<td>".$row['wr_homepage']."</td>
            <td>".$row['wr_8']."</td>
            <td>".$row['wr_9']."</td>
            <td>".$row['wr_6']."</td>
			<td>".$row['wr_7']."</td>
			<td>".$row['wr_content']."</td>
			<td>".$row['wr_4']."</td>
			<td>".$tmp."</td>
		</tr>
	";
}
$EXCEL_STR .= "</table>";

echo '<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ';

// echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_STR;
?>