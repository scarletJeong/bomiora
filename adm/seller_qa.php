<?php
$sub_menu = "700100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '셀러/체험단 신청관리';
include_once('./admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$colspan = 12;
$max_wr_1 = 15;

if (empty($fr_date) || ! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fr_date) ) $fr_date = '';
if (empty($to_date) || ! preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $to_date) ) $to_date = '';

$to_day = date('Y-m-d');
$beforeDay = date("Y-m-d", strtotime($to_day." -1 day"));

$day_of_the_week = date('w'); 
$a_week_ago = date('Y-m-d', strtotime($date." -".$day_of_the_week."days"));
$to_mon = date('Y-m')."-01";

$w_start = date('Y-m-d',$t=time()-((date('w')+6)%7+7)*86400);
$w_end = date('Y-m-d',$t+86400*6);
 
$d = mktime(0,0,0, date("m"), 1, date("Y")); //이번달 1일
$prev_month = strtotime("-1 month", $d); //한달전
$m_start = date("Y-m-01", $prev_month ); //지난달 1일
$m_end = date("Y-m-t", $prev_month ); //지난달 말일

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

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            {$sql_order}
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);
?>
<style>
    .sch_reset_btn{
    width: 30px;
    height: 30px;
    border: 1px solid #dcdcdc;
    padding: 0;
    overflow: hidden;
    font-size:18px;
    }
</style>
<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
	<div class="sch_last">
		<strong>상담진행상태 : </strong>
		<select name="swr_1" id="swr_1">
			<option value="">전체</option>
			<option value="1" <?php echo get_selected($swr_1, "1"); ?>>확인전</option>
			<option value="2" <?php echo get_selected($swr_1, "2"); ?>>부재중</option>
			<option value="3" <?php echo get_selected($swr_1, "3"); ?>>통화완료</option>
			<!-- <option value="4" <?php echo get_selected($swr_1, "4"); ?>>상담예약</option>
			<option value="5" <?php echo get_selected($swr_1, "5"); ?>>1차부재</option>
			<option value="6" <?php echo get_selected($swr_1, "6"); ?>>2차부재</option>
			<option value="7" <?php echo get_selected($swr_1, "7"); ?>>3차부재</option>
			<option value="8" <?php echo get_selected($swr_1, "8"); ?>>4차부재</option>
			<option value="9" <?php echo get_selected($swr_1, "9"); ?>>5차부재</option>
			<option value="10" <?php echo get_selected($swr_1, "10"); ?>>리콜대상</option>
			<option value="11" <?php echo get_selected($swr_1, "11"); ?>>에러</option>
			<option value="12" <?php echo get_selected($swr_1, "12"); ?>>내원안함</option>
			<option value="13" <?php echo get_selected($swr_1, "13"); ?>>내원</option>
			<option value="14" <?php echo get_selected($swr_1, "14"); ?>>수술완료</option>
			<option value="15" <?php echo get_selected($swr_1, "15"); ?>>예약취소</option> -->
		</select> 
		<strong>장치구분 : </strong>
		<select name="wr_link1" id="wr_link1">
			<option value="">전체</option>
			<option value="PC" <?php echo get_selected($wr_link1, "PC"); ?>>PC</option>
			<option value="MOBILE" <?php echo get_selected($wr_link1, "MOBILE"); ?>>MOBILE</option>
		</select> 
	</div>

	<div class="sch_last">
		<strong>기간별검색 : </strong>
		<input type="text" name="fr_date" value="<?php echo $fr_date ?>" id="fr_date" class="frm_input" size="11" maxlength="10">
		~
		<input type="text" name="to_date" value="<?php echo $to_date ?>" id="to_date" class="frm_input" size="11" maxlength="10">
		<span class="btn btn_02 bt" id="bt1" data-value="오늘">오늘</span>
		<span class="btn btn_02 bt" id="bt2" data-value="어제">어제</span>
		<span class="btn btn_02 bt" id="bt3" data-value="이번주">이번주</span>
		<span class="btn btn_02 bt" id="bt4" data-value="이번달">이번달</span>
		<span class="btn btn_02 bt" id="bt5" data-value="지난주">지난주</span>
		<span class="btn btn_02 bt" id="bt6" data-value="지난달">지난달</span>
		<span class="btn btn_02 bt" id="bt7" data-value="전체">전체</span>
	</div>

	<div class="sch_last">
		<strong>검색대상 : </strong> 
		<select name="sfl" id="sfl">
			<option value="wr_name"		<?php echo get_selected($sfl, "wr_name"); ?>>이름</option>
			<option value="wr_homepage"	<?php echo get_selected($sfl, "wr_homepage"); ?>>전화번호</option>
			<option value="wr_content"	<?php echo get_selected($sfl, "wr_content"); ?>>내용내용</option>
			<option value="wr_8"		<?php echo get_selected($sfl, "wr_8"); ?>>셀러신청</option>
			<option value="wr_9"		<?php echo get_selected($sfl, "wr_9"); ?>>체험단신청</option>
		</select>
		<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
		<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
		<input type="submit" class="btn_submit" value="검색" id="total_search">
        <button class="sch_reset_btn" onclick="sch_reset()"><i class="xi-sync"></i></button>
	</div> 
</form>
<div class="btn_online_add" style="display:flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom:15px;">
    <a onclick="javascript:ExcelDownLoad();" style="cursor: pointer;     display: block;
    font-size: 18px;
    padding: 8px 10px;
    color: #fff;
    font-weight: 600;
    background: #333;
    border-radius: 5px;">엑셀다운</a>
    <a href="./seller_qa_add.php" style="cursor: pointer;     display: block;
    font-size: 18px;
    padding: 8px 10px;
    color: #fff;
    font-weight: 600;
    background: #3f51b5;
    border-radius: 5px;">외부상담추가</a>
</div>
<form name="fonlineqalist" id="fonlineqalist" action="./seller_qa_update.php" onsubmit="return fonlineqalist_submit(this);" method="post">
	<input type="hidden" name="sst" value="<?php echo $sst ?>">
	<input type="hidden" name="sod" value="<?php echo $sod ?>">
	<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
	<input type="hidden" name="stx" value="<?php echo $stx ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>">
	<input type="hidden" name="token" value="<?php echo $token ?>">
	<div class="tbl_head01 tbl_wrap">
	<table>
		<caption><?php echo $g5['title']; ?> 목록</caption>
		<thead>
		<tr>
			<th scope="col">
				<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
			</th>
			<th scope="col"><?php echo subject_sort_link('wr_datetime') ?>등록일</a></th>
			<th scope="col"><?php echo subject_sort_link('wr_link1') ?>구분</a></th> 
			<!-- <th scope="col"><?php echo subject_sort_link('wr_link2') ?>상담구분</a></th>  -->
            <th scope="col"><?php echo subject_sort_link('wr_datetime') ?>이름</a></th>
			<th scope="col"><?php echo subject_sort_link('wr_datetime') ?>연락처</a></th>  
			<th scope="col"><?php echo subject_sort_link('wr_8') ?>셀러신청</a></th> 
			<th scope="col"><?php echo subject_sort_link('wr_9') ?>체험단 신청</a></th> 
			<th scope="col"><?php echo subject_sort_link('wr_6') ?>주소</a></th> 
			<th scope="col"><?php echo subject_sort_link('wr_7') ?>공구제품</a></th> 
			
			<!-- <th scope="col">내용/상담가능시간</th> -->
			<th scope="col">상담내용</th>
			
			<th scope="col"><?php echo subject_sort_link('wr_datetime') ?>진행사항</a></th>
		</tr>
		</thead>
		<tbody>
		<?php
		for ($i=0; $row=sql_fetch_array($result); $i++) {        
			$bg = 'bg'.($i%2);
		?>
		<tr class="<?php echo $bg; ?>">

			<td class="td_chk">
				<input type="hidden" name="wr_id[<?php echo $i ?>]" value="<?php echo $row['wr_id'] ?>">
				<input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
			</td>
			<td><?php echo substr($row['wr_datetime'],0,10); ?></td>
			<td style="text-align:left;">장치구분 : <?php echo $row['wr_link1'] ?><br/>디비구분 : <?php echo $row['wr_3'] ?></td> 
			<!-- <td><?php echo $row['wr_link2']; if ($row['wr_link2'] == '이벤트'){echo " 게시판";} ?></td>  -->
            <td><?php echo $row['wr_name'] ?></td>
			<td><?php echo $row['wr_homepage'] ?></td> 
			<td><?php echo $row['wr_8'] ?></td>
			<td><?php echo $row['wr_9'] ?></td>
			<td><textarea name="wr_6[<?php echo $i ?>]" id="wr_6_<?php echo $i ?>" style="height:70px;"><?php echo $row['wr_6'] ?></textarea></td>
			<td>
                <select name="wr_7[<?php echo $i ?>]" id="wr_7_<?php echo $i ?>" style="width: 80%;">
                    <option value="공구제품선텍" <?php echo get_selected($row['wr_7'], "공구제품선텍"); ?>>공구제품선텍</option>  
					<option value="보미 디톡스환" <?php echo get_selected($row['wr_7'], "보미 디톡스환"); ?>>보미 디톡스환</option>
                    <option value="보미 다이어트환" <?php echo get_selected($row['wr_7'], "보미 다이어트환"); ?>>보미 다이어트환</option>
                    <option value="어린이치약" <?php echo get_selected($row['wr_7'], "어린이치약"); ?>>어린이치약</option>
                    <option value="하루비움" <?php echo get_selected($row['wr_7'], "하루비움"); ?>>하루비움</option>
				</select>
            </td>
			
			<!-- <td><textarea name="wr_content[<?php echo $i ?>]" id="content_<?php echo $i ?>" style="height:70px;"><?php echo $row['wr_content'] ?></textarea></td> -->
			<td><textarea name="wr_4[<?php echo $i ?>]" id="wr_4_<?php echo $i ?>" style="height:70px;"><?php echo $row['wr_4'] ?></textarea></td>
			
			
			<td>			
				<select name="wr_1[<?php echo $i ?>]" id="wr_1_<?php echo $i ?>" style="width: 80%;">  
					<option value="1" <?php echo get_selected($row['wr_1'], "1"); ?>>확인전</option>
					<option value="2" <?php echo get_selected($row['wr_1'], "2"); ?>>부재중</option>
					<option value="3" <?php echo get_selected($row['wr_1'], "3"); ?>>통화완료</option>
					<!-- <option value="4" <?php echo get_selected($row['wr_1'], "4"); ?>>상담예약</option>
					<option value="5" <?php echo get_selected($row['wr_1'], "5"); ?>>1차부재</option>
					<option value="6" <?php echo get_selected($row['wr_1'], "6"); ?>>2차부재</option>
					<option value="7" <?php echo get_selected($row['wr_1'], "7"); ?>>3차부재</option>
					<option value="8" <?php echo get_selected($row['wr_1'], "8"); ?>>4차부재</option>
					<option value="9" <?php echo get_selected($row['wr_1'], "9"); ?>>5차부재</option>
					<option value="10" <?php echo get_selected($row['wr_1'], "10"); ?>>리콜대상</option>
					<option value="11" <?php echo get_selected($row['wr_1'], "11"); ?>>에러</option>
					<option value="12" <?php echo get_selected($row['wr_1'], "12"); ?>>내원안함</option>
					<option value="13" <?php echo get_selected($row['wr_1'], "13"); ?>>내원</option>
					<option value="14" <?php echo get_selected($row['wr_1'], "14"); ?>>수술완료</option>
					<option value="15" <?php echo get_selected($row['wr_1'], "15"); ?>>예약취소</option> -->
				</select>			
			</td>		
		</tr>

		<?php
		}
		if ($i == 0)
			echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없거나 관리자에 의해 삭제되었습니다.</td></tr>';
		?>
		</tbody>
		</table>
	</div>
	<div class="btn_fixed_top">
		<input type="submit" name="act_button" value="선택저장" onclick="document.pressed=this.value" class="btn btn_03">
		<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_submit">
	</div>
</form>





<script>
	$(function(){
		$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
		$(".bt").click(function(){
			var id = $(this).attr('id');	
			var today = $(this).data('value');	
			$('.bt').removeClass('btn_03');
			$(this).addClass('btn_03');	
			if (today == "오늘") {
				$('#fr_date').val("<?php echo $to_day?>");
				$('#to_date').val("<?php echo $to_day?>");
			} else if (today == "어제") {
				$('#fr_date').val("<?php echo $beforeDay?>");
				$('#to_date').val("<?php echo $beforeDay?>");				
			} else if (today == "이번주") {
				$('#fr_date').val("<?php echo $a_week_ago?>");
				$('#to_date').val("<?php echo $to_day?>");
			} else if (today == "이번달") {
				$('#fr_date').val("<?php echo $to_mon?>");
				$('#to_date').val("<?php echo $to_day?>");
			} else if (today == "지난주") {
				$('#fr_date').val("<?php echo $w_start?>");
				$('#to_date').val("<?php echo $w_end?>");
			} else if (today == "지난달") {
				$('#fr_date').val("<?php echo $m_start?>");
				$('#to_date').val("<?php echo $m_end?>");				
			} else if (today == "전체") {
				$('#fr_date').val("");
				$('#to_date').val("");
			}
		});
	});

	function fonlineqalist_submit(f)
	{
		if (!is_checked("chk[]")) {
			alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
			return false;
		}

		if(document.pressed == "선택삭제") {
			if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
				return false;
			}
		}

		return true;
	}

    function ExcelDownLoad(){ 
		$("#fsearch").attr("action", "./seller_qa_excel.php");
		$("#fsearch").submit();
		return false;
	}
    function sch_reset(){ 
		document.getElementById('stx').value='';
		document.getElementById('sfl').value='';
	}

</script>

<?php
$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
echo $pagelist;

include_once('./admin.tail.php');
?>






