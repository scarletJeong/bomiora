<?php
$sub_menu = "700100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '셀러/체험단 신청관리';
include_once('./admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

?>
<style>
    .tbl_head01 tbody td{
        text-align: left !important;
    }
</style>
<div class="tbl_head01 tbl_wrap online_qa_add">
<form name="fonlineqalist" id="fonlineqalist" method="post">
	<input type="hidden" id="wr_1_opt" name="wr_1_opt">
	<table>
		<tr>
			<th>디비구분</th>
			<td><input type="text" id="wr_3" name="wr_3"> <span style="color:#999"> ex) 유입된 경로_미인엔, 굿닥, 강남언니, 바비톡, 카톡 등</span></td>
		</tr>
		<!-- <tr>
			<th>상담구분</th>
			<td><input type="text" id="wr_link2" name="wr_link2"> <span style="color:#999"> ex) 상담종류,부위 등 </span></td>
		</tr> -->
        <tr>
            <th>셀러신청</th>
            <td class="mutiple">
                <input type="checkbox" value="셀러" name="wr_8" id="wr_8">
                <label for="wr_8">셀러 신청</label>
            </td>
        </tr>
        <tr>
            <th>체험단신청</th>
            <td class="mutiple">
                <input type="checkbox" value="체험단" name="wr_9" id="wr_9">
                <label for="wr_9">체험단 신청</label>
            </td>
        </tr>
		<tr>
			<th>이름</th>
			<td><input type="text" id="wr_name" name="wr_name" class="input_name"></td>
		</tr>
		<tr>
			<th>연락처</th>
			<td><input type="text" id="phone" name="phone" class="input_ph"></td>
		</tr>
		<tr>
			<th>주소</th>
			<td><textarea name="wr_6" id="wr_6" style="height:70px;"><?php echo $row['wr_6'] ?></textarea></td>
		</tr>
        <tr>
            <th>공구제품</th>
            <td>
                <select name="wr_7" id="wr_7">
                    <option value="공구제품 선택" style="color:#ccc;">공구제품 선택</option>
                    <option value="보미 디톡스환">보미 디톡스환</option>
                    <option value="보미 다이어트환">보미 다이어트환</option>
                    <option value="어린이치약">어린이치약</option>
                    <option value="하루비움">하루비움</option>
                </select>
            </td>
        </tr>
		<tr>
			<th>상담내용</th>
			<td><textarea name="wr_4" id="wr_4" style="height:70px;"><?php echo $row['wr_4'] ?></textarea></td>
		</tr>
		<tr>
			<th>진행사항</th>
			<td>
			<select name="wr_1[<?php echo $i ?>]" id="wr_1">  
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

	</table>
	<div class="btn_fixed_top">
		<input type="submit" id="submit_btn" name="act_button" value="선택저장" onclick="document.pressed=this.value" class="btn btn_03">
	</div>

</form>
</div>

<script>
    var dup = 0;
    $(function() {
      $('#nurl').val(document.location.href);

      $('#submit_btn').on('click', function() {
        if(dup == 1){
          alert('처리중 입니다.');
        return false;
        }
              
        if($('#wr_name').val()==''){
          alert('이름을 적어 주세요');
          $('#wr_name').focus();
          return false;
        }
        if($('#phone').val()==''){
          alert('연락처를 적어주세요');
          $('#phone').focus();
          return false;
        }
        if(!$('.mutiple input:checkbox').is(':checked')){
            alert('셀러/체험단 1개이상 선택해주세요.');
            return false;
            }
		var csStatus = $('#wr_1').val();
		$('#wr_1_opt').attr("value",csStatus);



        dup = 1;
        $("#fonlineqalist").attr("action", "./seller_qa_add_update.php");
        $("#fonlineqalist").submit();
      });
    });
  </script>


<!-- 
<script>
	function fonlineqalist_submit(f)
	{
		if (!is_checked("chk[]")) {
			alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
			return false;
		}

		return true;
	}
</script> -->

<?php


include_once('./admin.tail.php');
?>






