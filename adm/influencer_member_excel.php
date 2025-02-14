<?php
$to_day = date('Y-m-d');
$output_file_name = "인플루언서 회원관리";

include_once('./_common.php');


header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename={$output_file_name}-{$to_day}.xls" );
header( "Content-Description: PHP4 Generated Data");

$sql_order = " order by mb_datetime desc ";
$sql_common = " from {$g5['member_table']} ";
$sql_search = " where mb_level = 5 ";

$sql = " select * {$sql_common} {$sql_search} {$sql_order} ";
$result = sql_query($sql);

$EXCEL_STR = "
<table border='1'>
<tr>
   <td>아이디</td>
   <td>이름</td>
   <td>닉네임</td>
   <td>연락처</td>
   <td>이메일</td>
   <td>코드</td>
   <td>상품수</td>
   <td>충전금</td>
   <td>상태</td>
   <td>가입일</td>
	 <td>최종접속</td>
</tr>
";

while($row = sql_fetch_array($result)) {
	$stat_msg = "";
	if ($row['mb_leave_date']) $stat_msg = "탈퇴함";
	else if ($row['mb_intercept_date']) $stat_msg = "차단됨";
	else $stat_msg = "정상";

	$sql2 = " select count(*) as cnt from {$g5['g5_shop_item_table']} where LOCATE('{$row['mb_id']}', it_mb_inf) > 0 and it_use = '1' ";
  $row2 = sql_fetch($sql2);

	$EXCEL_STR .= "
		<tr>
			<td>".$row['mb_id']."</td>
			<td>".$row['mb_name']."</td>
     <td>".$row['mb_nick']."</td>
     <td>".$row['mb_hp']."</td>
     <td>".$row['mb_email']."</td>
			<td>".get_text($row['mb_inf_code'])."</td>
			<td>".$row2['cnt']."</td>
			<td>".$row['mb_charge']."</td>
			<td>".$stat_msg."</td>
			<td>".substr($row['mb_datetime'], 2, 8)."</td>
			<td>".substr($row['mb_today_login'], 2, 8)."</td>
		</tr>
	";
}
$EXCEL_STR .= "</table>";

echo '<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ';

echo $EXCEL_STR;
?>