<?php
$sub_menu = "400100";
require_once './_common.php';
include_once(G5_LIB_PATH.'/rsvt.lib.php');

auth_check_menu($auth, $sub_menu, 'r');

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.');
}

$g5['title'] = '환경설정';
require_once './admin.head.php';
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$sql = "select * from cm_office where mb_id = '".$member['mb_id']."' ";

$rs = sql_fetch($sql, true);
$booking_time = explode("|",$rs['office_booking_time']); // 온라인예약관련설정값

if(!is_array($rs)) {
	alert("정상적인 방법으로 이용바랍니다.", G5_URL);
}


?>

<script>

$(document).ready(function(){

	
	$(".close_section_a").change(function() {
		var sec = $(".close_section_a").val();
		if(sec==1) {
			$(".booking_closed_daya").css({"display":"block"});
			$(".close_day_a").attr({"required":"required"});
		} else {
			$(".booking_closed_daya").css({"display":"none"});
			$(".close_day_a").removeAttr("required");
			$(".close_day_a").removeAttr("value");
		}
	});
	var close_a = "<?php echo $booking_close[0];?>";
	if(close_a==1) {
		$(".booking_closed_daya").css({"display":"block"});
	}
	
	$(".close_section_b").change(function() {
		var sec = $(".close_section_b").val();
		if(sec==1) {
			$(".booking_closed_dayb").css({"display":"block"});
			$(".close_day_b").attr("required","required");
		} else {
			$(".booking_closed_dayb").css({"display":"none"});
			$(".close_day_b").removeAttr("required");
			$(".close_day_b").removeAttr("value");
		}
	});
	var close_b = "<?php echo $booking_close[3];?>";
	if(close_b==1) {
		$(".booking_closed_dayb").css({"display":"block"});
	}

	// 정기휴무일 등록
	$("#office_table").on("click", "button#btn_holiday", function(){
		$("#ifm_m").attr("src","./_sub_holiday.php?bo_table="+g5_bo_table);
		$( '#dialog_m' ).dialog({
		    title : "정기휴무일 설정",
			width : 700,
		    height : 450,
	        modal : true,
		    resizable : false,
			buttons: {
				"등록":function(){
					$("#ifm_m").contents().find("#btn_submit").trigger("click");
				},
				"닫기":function(){
					$(this).dialog("close");
				}
			},
			open:function() { // 팝업시 실행

				$(".ui-dialog-buttonpane button:contains('닫기')").css({"background":"#555555", "color":"#eeeeee"});

				// 부모창 스크롤 막기
				$('html, body').css({'overflow': 'hidden'});
				$('#element').on('scroll touchmove mousewheel', function(event) {
					event.preventDefault();
					event.stopPropagation();
					return false;
				});
				// 부모창 스크롤 막기 끝.
			},
			close: function () {

				// 부모창 스크롤 막기 해제
				$('html').css({'overflow': 'scroll'});
				holiday_load();

			}
	    });
	});
	// 정기휴무일등록끝.
	holiday_load(); // 정기휴무 로드

	$('button.btn_cal').click(function(){
		var url = g5_bbs_url+'/board.php?bo_table='+g5_bo_table;
		location.href=url;
	});

});

function holiday_load() {
	$("#holiday").load("./_sub_holiday_list.php?bo_table="+g5_bo_table);
}


</script>

<div id="dialog_m" style="display:none;z-index:1000;">
	<iframe id="ifm_m" width="100%" height="99%"  marginwidth="0" marginheight="0" frameborder="0" scrolling="auto"></iframe>
</div><!-- dialog 팝업용 -->

<div id="office_table">
	<form name="fmData" method="post" style="margin:0px;" enctype="MULTIPART/FORM-DATA">
	<input type="hidden" name="mode" value="save">
	<input type="hidden" name="bo_table" value="<?php echo $bo_table; ?>">
	<input type="hidden" name="id_no" class="id_no" value="<?php echo $rs['id_no']; ?>">
	<input type="hidden" name="mb_id" class="mb_id" value="<?php echo $member['mb_id']; ?>">

	<!-- 업체 정보 -->
		<table>
			<colgroup>
				<col width="8%" />
				<col width="16%" />
				<col width="8%" />
				<col width="15%" />
				<col width="8%" />
				<col width="15%" />
			</colgroup>
			<tr>
				<th>업체회원아이디</th>
				<td><input type="text" name="mb_id" value="<?php echo $rs['mb_id']; ?>" class="mb_id"/></td>
				<th></th>
				<td></td>
				<th></th>
				<td></td>
			</tr>
			<tr>
				<th>사업자명칭</th>
				<td><input type="text" name="office_name" value="<?php echo $rs['office_name']; ?>" class="office_name"/></td>
				<th>대표자명</th>
				<td><input type="text" name="office_ceo" value="<?php echo $rs['office_ceo']; ?>" class="office_ceo"/></td>
				<th>이메일</th>
				<td><input type="text" name="office_email" value="<?php echo $rs['office_email']; ?>" class="office_email"/></td>
			</tr>
			<tr>
				<th>전화번호</th>
				<td><input type="text" name="office_tel" value="<?php echo $rs['office_tel']; ?>" class="office_tel"/></td>
				<th>팩스번호</th>
				<td><input type="text" name="office_fax" value="<?php echo $rs['office_fax']; ?>" class="office_fax"/></td>
				<th>휴대폰번호</th>
				<td><input type="text" name="office_hp" value="<?php echo $rs['office_hp']; ?>" class="office_hp"/></td>
			</tr>
			<tr>
				<th>사업자등록번호</th>
				<td><input type="text" name="office_license" value="<?php echo $rs['office_license']; ?>" class="office_license"/></td>
				<th>업태</th>
				<td><input type="text" name="office_uptae" value="<?php echo $rs['office_uptae']; ?>" class="office_uptae"/></td>
				<th>종목</th>
				<td><input type="text" name="office_jongmok" value="<?php echo $rs['office_jongmok']; ?>" class="office_jongmok"/></td>
			</tr>
			<tr>
				<th>사업장주소</th>
				<td colspan="5" style="line-height:250%">
		            <label for="reg_mb_zip" class="sound_only">우편번호<?php echo $config['cf_req_addr']?'<strong class="sound_only"> 필수</strong>':''; ?></label>
			        <input type="text" name="mb_zip" value="<?php echo $rs['office_zip']; ?>" id="reg_mb_zip" class="frm_input required" size="6" maxlength="6"/>
				    <button type="button" class="btn_frmline" onclick="win_zip('fmData', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">주소 검색</button><br>
					<input type="text" name="mb_addr1" value="<?php echo $rs['office_addr1']; ?>" id="reg_mb_addr1" class="frm_input frm_address required" size="50"/>
	                <label for="reg_mb_addr1">기본주소 <strong class="sound_only"> 필수</strong></label><br>
		            <input type="text" name="mb_addr2" value="<?php echo $rs['office_addr2']; ?>" id="reg_mb_addr2" class="frm_input frm_address" size="50"/>
			        <label for="reg_mb_addr2">상세주소</label>
				    <br>
	                <input type="text" name="mb_addr3" value="<?php echo $rs['office_addr3']; ?>" id="reg_mb_addr3" class="frm_input frm_address" size="50" readonly="readonly"/>
	                <label for="reg_mb_addr3">참고항목</label>
		            <input type="hidden" name="mb_addr_jibeon" value="<?php echo $rs['office_addr_jibeon']; ?>"/>
				</td>
			</tr>

			<tr>
				<th>예약가능일</th>
				<td colspan="5" style="line-height:250%;">
					<select name="booking_fr_day" id="booking_fr_day" class="booking_fr_day">
						<?php echo option_int(0,10,1,$booking_time[0])?>
					</select> 일 후부터 예약가능 [ 최소 몇일후부터 예약을 할 수 있는지를 설정합니다. ("0"이면 당일 예약가능) ]<br/>
					<select name="booking_to_day" id="booking_to_day" class="booking_to_day">
						<?php echo option_int(0,60,5,$booking_time[1])?>
					</select> 일 후까지 예약가능 [ 최대 몇일후까지 예약을 할 수 있는지를 설정합니다. ("0"이면 제한 없음) ]
				</td>
			</tr>
			<tr>
				<th>예약시간</th>
				<td colspan="5">
					<select name="booking_fr_time" id="booking_fr_time" class="booking_fr_time">
						<?php echo option_int(0,24,1,$booking_time[2])?>
					</select> 시부터&nbsp;&nbsp;
					<select name="booking_to_time" id="booking_to_time" class="booking_to_time">
						<?php echo option_int(0,24,1,$booking_time[3])?>
					</select> 시까지&nbsp;&nbsp;
					<select name="booking_se" id="booking_se" class="booking_se">
						<?php echo option_str("1시간 단위 예약|30분 단위 예약","0|1",$booking_time[4])?>
					</select>&nbsp;&nbsp;[ 예약 가능한 시간을 설정합니다 ]
				</td>
			</tr>
			<tr>
				<th>예약시간별인원</th>
				<td colspan="5">
					<select name="booking_person" id="booking_person" class="booking_person">
						<?php echo option_int(0,10,1,$booking_time[5])?>
					</select> 명까지 동시 예약 가능 [ 시간단위별로 동시 예약가능한 인원을 설정, "0"이면 제한 없음) ]
				</td>
			</tr>
			<tr>
				<th>관리자발송</th>
				<td><input type="checkbox" name="office_sms_admin" value="1" id="office_sms_admin" class="office_sms_admin" /><label for="office_sms_admin"> 예약시 관리자 SMS 발송</label></td>
				<th>예약자발송</th>
				<td><input type="checkbox" name="office_sms_user" value="1" id="office_sms_user" class="office_sms_user" /><label for="office_sms_user"> 예약시 예약자 SMS 발송</label></td>
				<th></th>
				<td></td>
			</tr>
			<tr>
				<th>정기휴무</th>
				<td colspan="5">
					<button type="button" id="btn_holiday"><i class='fa fa-pencil-square-o' aria-hidden='true'></i> 정기휴무일 추가</button>
					<button type="button" onclick="holiday_load();"><i class='fa fa-refresh' aria-hidden='true'></i> 목록 새로고침</button>
					※ 정기휴무일 설정한 날짜에 예약 불가.<br/>
					<div id="holiday"></div>
				</td>
			</tr>
		</table>

		<div class="button_zone">
			<button type="submit" class="btn_submit">저장</button>&nbsp;&nbsp;
			<button type="button" class="btn_cal" >달력보기</button>
		</div>
	</form>
</div>

<?php
require_once './admin.tail.php';
?>
