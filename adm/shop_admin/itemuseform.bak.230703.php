<?php
$sub_menu = '400650';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check_menu($auth, $sub_menu, "w");

/* 제품리스트 */
$item_list  = '<option value="">선택</option>'.PHP_EOL;
$sql = " select * from {$g5['g5_shop_item_table']} ";
//$sql .= " where it_id = '{$it_id}' ";
$sql .= " order by it_id desc ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
	$item_list .= '<option value="'.$row['it_id'].'">'.$row['it_name'].'</option>'.PHP_EOL;
}

if ($w == '') {   
	$name = $member['mb_nick'] ? $member['mb_nick'] : $member['mb_name'];
	$is_outage_num = 0;

    $html_title = '추가';
} elseif ($w == 'u') {    
    
	$is_id = isset($_GET['is_id']) ? preg_replace('/[^0-9]/', '', $_GET['is_id']) : 0;
	$sql = " select *
           from {$g5['g5_shop_item_use_table']} a
           left join {$g5['member_table']} b on (a.mb_id = b.mb_id)
           left join {$g5['g5_shop_item_table']} c on (a.it_id = c.it_id)
          where is_id = '$is_id' ";
$is = sql_fetch($sql);

if (!$is['is_id'])
    alert('등록된 자료가 없습니다.');

	$name = get_text($is['is_name']);
	$is_outage_num = get_text($is['is_outage_num']);
	$is_score1 = $is['is_score1'];
	$is_score2 = $is['is_score2'];
	$is_score3 = $is['is_score3'];
	$is_score4 = $is['is_score4'];
	
	// 확인
	$is_confirm_yes  =  $is['is_confirm'] ? 'checked="checked"' : '';
	$is_confirm_no   = !$is['is_confirm'] ? 'checked="checked"' : '';

	$html_title = '수정';
} else {
    alert('제대로 된 값이 넘어오지 않았습니다.');
}

$g5['title'] = '리뷰';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$qstr .= ($qstr ? '&amp;' : '').'sca='.$sca;
?>

<form name="fitemuseform" id="fitemuseform" action="./itemuseformupdate.php" method="post" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return fitemuseform_submit(this)">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="is_id" value="<?php echo $is_id; ?>">
<input type="hidden" name="it_id" value="<?php echo $is['it_id']; ?>">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sca2" value="<?php echo $sca2; ?>">
<input type="hidden" name="sca3" value="<?php echo $sca3; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="mb_id" value="<?php echo ($is['mb_id']) ? $is['mb_id'] : $member['mb_id'] ?>">
<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row">제품명</th>
        <td>
			<select name="it_id" id="it_id">
				<?php echo conv_selected_option($item_list, $is['it_id']); ?>
			</select>
		</td>
    </tr>
    <tr>
        <th scope="row"><label for="is_name">이름</label></th>
        <td><input type="text" name="is_name" value="<?php echo $name; ?>" id="is_name" class="frm_input"></td>
    </tr>
	<tr>
        <th scope="row">구매방식</th>
        <td>
			<input type="radio" name="is_pay_mthod" value="solo" id="solo" <?php if ($is['is_pay_mthod'] == 'solo') { echo 'checked="checked"'; } ?>>
			<label for="solo">내돈내산</label>
			<input type="radio" name="is_pay_mthod" value="group" id="group" <?php if ($is['is_pay_mthod'] == 'group') { echo 'checked="checked"'; } ?>>
			<label for="group">평가단</label>
        </td>
    </tr>
	<tr>
        <th scope="row"><label for="is_outage_num">체중감량</label></th>
        <td><input type="text" name="is_outage_num" value="<?php echo $is_outage_num; ?>" id="is_outage_num" maxlength="3" class="frm_input"> KG</td>
    </tr>
	
	<tr>
        <th scope="row">효과</th>
        <td>
			<select name="is_score1" id="is_score1">
				<option value="1" <?php echo get_selected($is_score1, "1"); ?>>1</option>
				<option value="2" <?php echo get_selected($is_score1, "2"); ?>>2</option>
				<option value="3" <?php echo get_selected($is_score1, "3"); ?>>3</option>
				<option value="4" <?php echo get_selected($is_score1, "4"); ?>>4</option>
				<option value="5" <?php echo get_selected($is_score1, "5"); ?>>5</option>
			</select> 점
        </td>
    </tr>
	<tr>
        <th scope="row">가성비</th>
        <td>
			<select name="is_score2" id="is_score2">
				<option value="1" <?php echo get_selected($is_score2, "1"); ?>>1</option>
				<option value="2" <?php echo get_selected($is_score2, "2"); ?>>2</option>
				<option value="3" <?php echo get_selected($is_score2, "3"); ?>>3</option>
				<option value="4" <?php echo get_selected($is_score2, "4"); ?>>4</option>
				<option value="5" <?php echo get_selected($is_score2, "5"); ?>>5</option>
			</select> 점			
        </td>
    </tr>
	<tr>
        <th scope="row">향 / 맛</th>
        <td>
			<select name="is_score3" id="is_score3">
				<option value="1" <?php echo get_selected($is_score3, "1"); ?>>1</option>
				<option value="2" <?php echo get_selected($is_score3, "2"); ?>>2</option>
				<option value="3" <?php echo get_selected($is_score3, "3"); ?>>3</option>
				<option value="4" <?php echo get_selected($is_score3, "4"); ?>>4</option>
				<option value="5" <?php echo get_selected($is_score3, "5"); ?>>5</option>
			</select> 점			
        </td>
    </tr>
	<tr>
        <th scope="row">편리함</th>
        <td>
			<select name="is_score4" id="is_score4">
				<option value="1" <?php echo get_selected($is_score4, "1"); ?>>1</option>
				<option value="2" <?php echo get_selected($is_score4, "2"); ?>>2</option>
				<option value="3" <?php echo get_selected($is_score4, "3"); ?>>3</option>
				<option value="4" <?php echo get_selected($is_score4, "4"); ?>>4</option>
				<option value="5" <?php echo get_selected($is_score4, "5"); ?>>5</option>
			</select> 점			
        </td>
    </tr>
	<tr>
		<th scope="row"><label for="is_positive_review_text">좋았던 점</label></th>
		<td colspan="3"><textarea name="is_positive_review_text" id="is_positive_review_text"><?php echo html_purifier($is['is_positive_review_text']); ?></textarea></td>
	</tr>
	<tr>
		<th scope="row"><label for="is_negative_review_text">아쉬운 점</label></th>
		<td colspan="3"><textarea name="is_negative_review_text" id="is_negative_review_text"><?php echo html_purifier($is['is_negative_review_text']); ?></textarea></td>
	</tr>
	<tr>
		<th scope="row"><label for="is_more_review_text">꿀팁</label></th>
		<td colspan="3"><textarea name="is_more_review_text" id="is_more_review_text"><?php echo html_purifier($is['is_more_review_text']); ?></textarea></td>
	</tr>
	<?php for($i=1; $i<=10; $i++) { ?>
	<tr>
		<th scope="row"><label for="is_img<?php echo $i; ?>">사진 <?php echo $i; ?></label></th>
		<td>
			<input type="file" name="is_img<?php echo $i; ?>" id="is_img<?php echo $i; ?>">
			<?php
			$is_img = G5_DATA_PATH.'/itemuse/'.$is['is_img'.$i];
			$is_img_exists = run_replace('shop_itemuse_image_exists', (is_file($is_img) && file_exists($is_img)), $is, $i);
			if($is_img_exists) {
				$thumb = get_is_thumbnail($is['is_img'.$i], 50, 50);
				$img_tag = run_replace('shop_itemuse_image_tag', '<img src="'.G5_DATA_URL.'/itemuse/'.$is['is_img'.$i].'" class="shop_itemuse_preview_image" >', $is, $i);				
			?>
			<label for="is_img<?php echo $i; ?>_del"><span class="sound_only">사진 <?php echo $i; ?> </span>파일삭제</label>
			<input type="checkbox" name="is_img<?php echo $i; ?>_del" id="is_img<?php echo $i; ?>_del" value="1">
			<span class="sit_wimg_limg<?php echo $i; ?>"><?php echo $thumb; ?></span>
			<div id="limg<?php echo $i; ?>" class="banner_or_img">
				<?php echo $img_tag; ?>
				<button type="button" class="sit_wimg_close">닫기</button>
			</div>
			<script>
			$('<button type="button" id="it_limg<?php echo $i; ?>_view" class="btn_frmline sit_wimg_view">사진<?php echo $i; ?> 확인</button>').appendTo('.sit_wimg_limg<?php echo $i; ?>');
			</script>
			<?php } ?>
		</td>
	</tr>
	<?php } ?>
	<tr>
        <th scope="row">이 의약품/제품을 만족하시나요?</th>
        <td>
			<input type="radio" name="is_recommend" value="y" id="is_recommend_y" <?php if ($is['is_recommend'] == 'y') { echo 'checked="checked"'; } ?>>
			<label for="is_recommend_y">만족해요</label>
			<input type="radio" name="is_recommend" value="n" id="is_recommend_n" <?php if ($is['is_recommend'] == 'n') { echo 'checked="checked"'; } ?>>
			<label for="is_recommend_n">만족 안해요</label>
        </td>
    </tr>

	<tr>
        <th scope="row">직접 상품을 사용하고 작성하는 리뷰인가요?</th>
        <td>
			<input type="checkbox" name="is_rv_check" value="1" <?php echo ($is['is_rv_check'] ? "checked" : ""); ?> id="is_rv_check">
			<label for="is_rv_check">네 </label>
        </td>
    </tr>
    <tr>
        <th scope="row">리뷰종류</th>
        <td>
            <input type="radio" name="is_rvkind" value="general" id="is_rvkind_g" <?php if ($is['is_rvkind'] == 'general') { echo 'checked="checked"'; } ?>>
            <label for="is_rvkind_g">일반</label>
            <input type="radio" name="is_rvkind" value="supporter" id="is_rvkind_s" <?php if ($is['is_rvkind'] == 'supporter') { echo 'checked="checked"'; } ?>>
            <label for="is_rvkind_s">서포터</label>
        </td>
    </tr>

<!--
    <tr>
        <th scope="row"><label for="is_subject">제목</label></th>
        <td><input type="text" name="is_subject" required class="required frm_input" id="is_subject" size="100"
        value="<?php echo get_text($is['is_subject']); ?>"></td>
    </tr>
    <tr>
        <th scope="row">내용</th>
        <td><?php echo editor_html('is_content', get_text(html_purifier($is['is_content']), 0)); ?></td>
    </tr>
    <tr>
        <th scope="row"><label for="is_reply_subject">답변 제목</label></th>
        <td><input type="text" name="is_reply_subject" class="frm_input" id="is_reply_subject" size="100"
        value="<?php echo get_text($is['is_reply_subject']); ?>"></td>
    </tr>
    <tr>
        <th scope="row">답변 내용</th>
        <td><?php echo editor_html('is_reply_content', get_text(html_purifier($is['is_reply_content']), 0)); ?></td>
    </tr>
-->
    <tr>
        <th scope="row">확인</th>
        <td>
            <input type="radio" name="is_confirm" value="1" id="is_confirm_yes" <?php echo $is_confirm_yes; ?>>
            <label for="is_confirm_yes">예</label>
            <input type="radio" name="is_confirm" value="0" id="is_confirm_no" <?php echo $is_confirm_no; ?>>
            <label for="is_confirm_no">아니오</label>
        </td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <a href="./itemuselist.php?<?php echo $qstr; ?>" class="btn_02 btn">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
</div>
</form>

<script>
<?php if ($w == 'u') { ?>
$(".banner_or_img").addClass("sit_wimg");
$(function() {
    $(".sit_wimg_view").bind("click", function() {
        var sit_wimg_id = $(this).attr("id").split("_");
        var $img_display = $("#"+sit_wimg_id[1]);

        $img_display.toggle();

        if($img_display.is(":visible")) {
            $(this).text($(this).text().replace("확인", "닫기"));
        } else {
            $(this).text($(this).text().replace("닫기", "확인"));
        }

        var $img = $("#"+sit_wimg_id[1]).children("img");
        var width = $img.width();
        var height = $img.height();
        if(width > 700) {
            var img_width = 700;
            var img_height = Math.round((img_width * height) / width);

            $img.width(img_width).height(img_height);
        }
    });
    $(".sit_wimg_close").bind("click", function() {
        var $img_display = $(this).parents(".banner_or_img");
        var id = $img_display.attr("id");
        $img_display.toggle();
        var $button = $("#it_"+id+"_view");
        $button.text($button.text().replace("닫기", "확인"));
    });
});
<?php } ?>

function fitemuseform_submit(f)
{
	/*
    <?php echo get_editor_js('is_content'); ?>
    <?php echo get_editor_js('is_reply_content'); ?>
	*/
    return true;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');