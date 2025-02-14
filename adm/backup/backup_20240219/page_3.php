<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/rsvt.lib.php');
/*
날짜는 현재날부터 7일정도 나오게 한다.
- 일요일은 휴진으로 한다.
- 토요일 진료시간은??
- 이전 날짜 및 이전 시간은 클릭이 안되게 한다.
- 저장시 이전날짜 이전 시간 확인해야 한다.
- 공휴일도 확인해야 한다.
https://sir.kr/g5_skin/26396?sfl=wr_subject%7C%7Cwr_content&stx=%EC%98%88%EC%95%BD
https://bomiora.kr/skin/board/booking/office/office_edit.php?bo_table=booking
*/
include_once(G5_PATH.'/head.php');

$od_id = isset($_POST['od_id']) ? safe_replace_regex($_POST['od_id'], 'od_id') : '';
$it_id = isset($_POST['it_id']) ? get_search_string(trim($_POST['it_id'])) : '';

if (!$od_id) alert('주문번호가 누락되었습니다.');
if (!$it_id) alert('제품번호가 누락되었습니다.');

// 제품정보 체크
$it = get_shop_item_with_category($it_id, $it_seo_title);
//$it = get_shop_item($it_id, true);
if(!(isset($it['it_id']) && $it['it_id'])) alert('제품정보가 존재하지 않습니다.', G5_URL);

if (!($it['ca_use'] && $it['it_use'])) {
    if (!$is_admin)
        alert('현재 판매가능한 제품이 아닙니다.', G5_URL);
}

// 주문정보체크 :: ct_output, ct_kind
$sql = " select * from {$g5['g5_shop_cart_table']} where od_id = '$od_id' and it_id = '$it_id' and mb_id = '{$member['mb_id']}'and ct_status = '쇼핑' ";
$ct = sql_fetch($sql);

if(!(isset($ct['od_id']) && $ct['od_id'])) alert('주문정보가 존재하지 않습니다.', G5_URL);

// 프로필 체크
$sql = " select * from {$g5['g5_shop_health_profile_cart_table']} where od_id = '$od_id' and it_id = '$it_id' and mb_id = '{$member['mb_id']}'and hp_status = '쇼핑' ";
$hp = sql_fetch($sql);

if(!(isset($hp['od_id']) && $hp['od_id'])) alert('프로필정보가 존재하지 않습니다.', G5_URL);

/*셋팅값 설정 */
$start_date = 0;// 예약가능시작일
$end_date = 7;// 예약가능종료일
$limit['min'] = date('Y-m-d', strtotime("+{$start_date} days", G5_SERVER_TIME)); // 달력 날짜(년-월-일로 변환).
$limit['max'] = date('Y-m-d', strtotime("+{$end_date} days", G5_SERVER_TIME)); // 달력 날짜(년-월-일로 변환).

?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.0/locale/ko.js"></script>
<div>
    <div class="page">
		<section>
			<div>
				<h2 class="page_title">건강 프로필</h2>
				<ul class="page_title_list">
					<li>
						<div>
							<span>1</span>
							<p>프로필</p>
							<p>작성하기</p>
						</div>
					</li>
					<li>
						<div>
							<span>2</span>
							<p>옵션 선택</p>
						</div>
					</li>
					<li class="on">
						<div>
							<span>3</span>
							<p>시간선택</p>
						</div>
					</li>
					<li>
						<div>
							<span>4</span>
							<p>개인정보</p>
						</div>
					</li>
					<hr class="page_title_line">
				</ul>
			</div>
		</section>
        <div class="page_list_w">
            <form name="fpage3form" id="fpage3form" method="post" action="<?php echo G5_SHOP_URL ?>/page_4.php">
			<input type="hidden" name="od_id" value="<?php echo $od_id ?>">
			<input type="hidden" name="it_id" value="<?php echo $it_id ?>">
			<input type="hidden" name="hp_doc_name" value="정대진">
                <h2>
                    전화 진료시간 예약
                </h2>
                <div id="day_value_fields" class="list_flex_one mt_30">
                    <h2>
                        날짜 선택
                    </h2>
                    <div>
						<?php
						// 휴진 부분은 아예 빼버리고 다른 날짜를 가져오는건??
						for($i = $start_date; $i < $end_date; $i++) {
							$var = date('Y-m-d', strtotime("+{$i} days", G5_SERVER_TIME)); // 달력 날짜(년-월-일로 변환).
							$yoil = date('w', strtotime($var)); // 요일 번호 추출
							list($y, $bg) = yoil_class($yoil); // 요일별 글자색상과 배경색에 관련된 class 명
							list($holiday_info, $booking_info) = day_info($var, $i, $limit['min'], $limit['max']);
							echo $holiday_info;// 예약정보
							//echo $booking_info;// 예약자정보


							/*
							$var = "{$year}-{$month}-" . sprintf('%02d', $day); // 달력 날짜(년-월-일로 변환).
					$yoil = date('w', strtotime($var)); // 요일 번호 추출
					list($y, $bg) = yoil_class($yoil); // 요일별 글자색상과 배경색에 관련된 class 명
					if(G5_TIME_YMD == $var) { $bg = 'today_td'; } // 오늘이면 배경색 변경
					echo '<td class="'.$bg.'">';
					echo '<ul class="day_title">';
						list($holiday_info, $booking_info) = day_info($var, $day, $limit['min'], $limit['max']);
							*/

						}
						?>
                    </div>
                </div>
				<div id="time_value_fields" class="list_flex_one mt_30">
					<?php include_once(G5_SHOP_PATH.'/time_value.php'); ?>
                </div>
                <div class="reservation_check">
                    <p></p>
                </div>
                <ul class="page_btn_layout">
                    <li>
                        <a class="page_list_cancel"  href="javascript:history.go(-1);">이전</a>
                    </li>
                    <li>
                      <!-- // jacknam
                        <a href="javascript:void(0);" id="page3_submit" class="page_list_btn">다음</a>
                       -->
                       <button id="page3_submit" class="page_list_btn">다음</button>
                    </li>
                </ul>
            </form>
        </div>
    </div>
</div>

<script>
// 날짜선택
$(document).on("click","#fpage3form input:radio[name='d_day']",function(){
	var od_id = $("#fpage3form input:hidden[name='od_id']").val();
	var it_id = $("#fpage3form input:hidden[name='it_id']").val();
	var hp_doc_name = $("#fpage3form input:hidden[name='hp_doc_name']").val();
	//var d_day = $("#fpage3form input:radio[name='d_day']:checked").val();
	var d_day = $(this).val();

	if(!od_id) {
		alert("주문번호가 존재하지 않습니다.");
		return false;
	}

	if(!it_id) {
		alert("제품번호가 존재하지 않습니다.");
		return false;
	}

	if(!it_id) {
		alert("제품번호가 존재하지 않습니다.");
		return false;
	}

	$.ajax({
		url : g5_shop_url + "/time_value.php",
		type: "POST",
		data: {od_id: od_id, it_id: it_id, hp_doc_name: hp_doc_name, d_day: d_day},
			success: function(data){
			$("#time_value_fields").empty().html(data);
			$("input:radio[name='d_time']:first").click();
		}
	});
});

// 시간선택
$(document).on("click","#fpage3form input:radio[name='d_time']",function(){
	var od_id = $("#fpage3form input:hidden[name='od_id']").val();
	var it_id = $("#fpage3form input:hidden[name='it_id']").val();
	var hp_doc_name = $("#fpage3form input:hidden[name='hp_doc_name']").val();
	var d_day = $("#fpage3form input:radio[name='d_day']:checked").val();
	//var d_time = $("#fpage3form input:radio[name='d_time']:checked").val();
	if(!d_day) {
		alert("날짜를 먼저 선택해 주세요.");
		return false;
	}

	var d_time = $(this).val();
	var theDate = d_day.split("-");
	var month = theDate[1];
	var day = theDate[2];
	var yoil = getDayOfWeek(d_day);
	var hours = d_time.split(":");
	var dt = moment(d_day + " " + d_time + ":00");
	dt.add(30,'minutes');
	var hour = dt.format("HH");
	var minute = dt.format("mm");
	var after30min = hour + ":" + minute;
	$('.reservation_check p').fadeOut().text('※ ' + month + '월 ' + day + '일 (' + yoil + ') ' + d_time + ' ~ ' + after30min + ' 사이에 상담, 예약 전화가 갑니다.').fadeIn();
});

function getDayOfWeek(str){ //ex) getDayOfWeek('2022-06-13')
	const week = ['일', '월', '화', '수', '목', '금', '토'];
	const dayOfWeek = week[new Date(str).getDay()];
	return dayOfWeek;
}

//jacknam
var process_next = false;
$(document).on("click","#page3_submit",function(e){
  e.preventDefault();

  if (process_next) {
    return false;
  }

  if(!$('input:radio[name="d_day"]').is(':checked')) {
    alert("날짜를 선택해 주세요.");
    return false;
  }

  if(!$('input:radio[name="d_time"]').is(':checked')) {
    alert("시간을 선택해 주세요.");
    return false;
  }

  process_next = true;
  setTimeout(() => {
    process_next = false;
  }, 3000);

  $("#fpage3form").submit();
});
</script>

<?php
include_once(G5_PATH.'/tail.php');
?>