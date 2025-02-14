<?php
$sub_menu = '400300';
include_once('./_common.php');

$ca_id = isset($_REQUEST['ca_id']) ? preg_replace('/[^0-9a-z]/i', '', $_REQUEST['ca_id']) : '';
$it_id = isset($_REQUEST['it_id']) ? safe_replace_regex($_REQUEST['it_id'], 'it_id') : '';

auth_check_menu($auth, $sub_menu, "r");


// 인플루언서 회원 리스트
$sql = " select * from {$g5['member_table']} where mb_level = '{$_const['level']['인플루언서']}' and (mb_intercept_date = '' or mb_intercept_date > ".date("Ymd", G5_SERVER_TIME).") and (mb_leave_date = '' or mb_leave_date > ".date("Ymd", G5_SERVER_TIME).") order by mb_datetime desc ";
//echo $sql;
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
	$influencer_list[] = $row;
}

$g5['title'] = '인플루언서 분배';
include_once(G5_PATH.'/head.sub.php');
?>

<div class="new_win">
    <h1>인플루언서 복사</h1>
	<form name="fiteminfcopy" id="fiteminfcopy" action="./iteminfcopyupdate.php" method="post" autocomplete="off" onsubmit="return fiteminfformcheck(this)">
	<input type="hidden" name="it_id" value="<?php echo $it_id ?>">
	<input type="hidden" name="ca_id" value="<?php echo $ca_id ?>">

    <div id="sit_copy" class="tbl_frm01 tbl_wrap ">
        <table>
        <colgroup>
            <col class="grid_2">
            <col>            
        </colgroup>
        <tbody>
		<tr>
            <th scope="row"><label for="it_price">판매가격</label></th>
            <td>
                <input type="text" name="it_price" value="" id="it_price" class="frm_input" size="8"> 원
            </td>
        </tr>
		<tr>
            <th scope="row"><label for="it_inf_price">차감금액</label></th>
            <td>
                <input type="text" name="it_inf_price" value="" id="it_inf_price" class="frm_input" size="8"> 원
            </td>
        </tr>
		<tr>
            <th scope="row"><label for="it_nocoupon">쿠폰적용안함</label></th>
            <td>
                <?php echo help("설정에 체크하시면 쿠폰 생성 때 상품 검색 결과에 노출되지 않습니다."); ?>
                <input type="checkbox" name="it_nocoupon" value="1" id="it_nocoupon" checked> 예
            </td>
        </tr>
		<tr>
            <th scope="row">인플루언서</th>
            <td>
				<input type="checkbox" name="chk_all_inf" value="1" id="chk_all_inf">
				<label for="chk_all_inf">전체</label><br/>
                <?php
				$i = 0;
				foreach((array) $influencer_list as $row){
				?>
				<input type="checkbox" name="it_mb_inf[]" value="<?php echo $row['mb_id'] ?>" id="it_mb_inf_<?php echo $i ?>">
				<label for="it_mb_inf_<?php echo $i ?>"><?php echo get_text($row['mb_name']) ?></label>
				<?php
					$i++;
				}   // end foreach
				if ($i == 0)
					echo '<li class="empty_li">자료가 없습니다.</li>';
				?>
            </td>
        </tr>        
        </tbody>
        </table>
    </div>





<!--
    <div id="sit_copy">
        <label for="new_it_id">상품코드</label>
        <input type="text" name="new_it_id" value="<?php echo time(); ?>" id="new_it_id" class="frm_input" maxlength="20">
    </div>
-->
    <div class="win_btn btn_confirm">
        <input type="submit" value="분배하기" class="btn_submit">
        <button type="button" onclick="self.close();">창닫기</button>
    </div>

    </form>
</div>

<script src="<?php echo G5_ADMIN_URL ?>/admin.js"></script>

<script>
// 모두선택
$(document).on("click","input[name=chk_all_inf]",function(){  
	if ($(this).prop('checked')) {
		$("input[id^=it_mb_inf]").prop('checked', true);
	} else {
		$("input[id^=it_mb_inf]").prop("checked", false);
	}
});
</script>

<script>
// <![CDATA[
var g5_admin_csrf_token_key = "<?php echo (function_exists('admin_csrf_token_key')) ? admin_csrf_token_key() : ''; ?>";

function fiteminfformcheck(f)
{
	
    var token = get_ajax_token();
    if(!token) {
        alert("토큰 정보가 올바르지 않습니다.");
        return false;
    }

	

	return true;

    //opener.parent.location.href = encodeURI(link+"&token="+token);
    //self.close();
}
// ]]>
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');