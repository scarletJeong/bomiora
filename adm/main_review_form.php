<?php
$sub_menu = '600200';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check_menu($auth, $sub_menu, "w");


//var_dump(mkdir('/ellucy/wwwroot/bomiora_dev/data/mainreview/1682401713', 0755));
//exit;

// 인플루언서 회원 리스트
$inf_list  = '<option value="">선택</option>';
$sql = " select mb_id, mb_name, mb_nick, mb_inf_code from {$g5['member_table']} where mb_level = '{$_const['level']['인플루언서']}' and (mb_intercept_date = '' or mb_intercept_date > " . date("Ymd", G5_SERVER_TIME) . ") and (mb_leave_date = '' or mb_leave_date > " . date("Ymd", G5_SERVER_TIME) . ") order by mb_datetime desc ";
//echo $sql;
$result = sql_query($sql);
for ($i = 0; $row = sql_fetch_array($result); $i++) {
  $inf_list .= '<option value="' . $row['mb_id'] . '">' . $row['mb_name'] . '</option>';
}

/* 제품리스트 */
$item_list  = '<option value="">선택</option>';
$sql = " select * from {$g5['g5_shop_item_table']} ";
//$sql .= " where it_id = '{$it_id}' ";
//jacknam
//$sql .= " WHERE it_org_id = '' group by it_id ";
$sql .= " WHERE it_org_id = '' OR (it_org_id <> '' AND ca_id <> '40') GROUP BY it_id ";
$sql .= " order by it_id desc ";
$result = sql_query($sql);
for ($i = 0; $row = sql_fetch_array($result); $i++) {
  $item_list .= '<option value="' . $row['it_id'] . '">' . $row['it_name'] . '</option>';
}


if ($w == '') {
  $html_title = '추가';
  $mr_confirm_yes  =  'checked="checked"';
  $mr_confirm_no   = '';
    
} elseif ($w == 'u') {

  $mr_no = isset($_GET['mr_no']) ? preg_replace('/[^0-9]/', '', $_GET['mr_no']) : 0;
  $sql = "select * from {$g5['g5_main_review_table']} where mr_no = '{$mr_no}' limit 1; ";

  $mr = sql_fetch($sql);

  if (!$mr['mr_no']) {
    alert('등록된 자료가 없습니다.');
  }

  $mr_score1 = $mr['mr_score1'];
  $mr_score2 = $mr['mr_score2'];
  $mr_score3 = $mr['mr_score3'];
  $mr_score4 = $mr['mr_score4'];

  // 게시
  $mr_confirm_yes  =  $mr['mr_confirm'] ? 'checked="checked"' : '';
  $mr_confirm_no   = !$mr['mr_confirm'] ? 'checked="checked"' : '';

  $html_title = '수정';
} else {
  alert('제대로 된 값이 넘어오지 않았습니다.');
}

$g5['title'] = '메인 리뷰';
include_once(G5_ADMIN_PATH . '/admin.head.php');
?>

<form name="fitemuseform" id="fitemuseform" action="./main_review_form_update.php" method="post" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return fitemuseform_submit(this)">
  <input type="hidden" name="w" value="<?php echo $w; ?>">
  <input type="hidden" name="mr_no" value="<?php echo $mr_no; ?>">
  <input type="hidden" name="it_id" value="<?php echo $mr['it_id']; ?>">
  <input type="hidden" name="page" value="<?php echo $page; ?>">
  <input type="hidden" name="mb_id" value="<?php echo ($mr['mb_id']) ? $mr['mb_id'] : $member['mb_id'] ?>">
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
              <?php echo conv_selected_option($item_list, $mr['it_id']); ?>
            </select>
          </td>
        </tr>

        <tr>
          <th scope="row">효과</th>
          <td>
            <select name="mr_score1" id="mr_score1">
              <option value="1" <?php echo get_selected($mr_score1, "1"); ?>>1</option>
              <option value="2" <?php echo get_selected($mr_score1, "2"); ?>>2</option>
              <option value="3" <?php echo get_selected($mr_score1, "3"); ?>>3</option>
              <option value="4" <?php echo get_selected($mr_score1, "4"); ?>>4</option>
              <option value="5" <?php echo get_selected($mr_score1, "5"); ?>>5</option>
            </select> 점
          </td>
        </tr>
        <tr>
          <th scope="row">가성비</th>
          <td>
            <select name="mr_score2" id="mr_score2">
              <option value="1" <?php echo get_selected($mr_score2, "1"); ?>>1</option>
              <option value="2" <?php echo get_selected($mr_score2, "2"); ?>>2</option>
              <option value="3" <?php echo get_selected($mr_score2, "3"); ?>>3</option>
              <option value="4" <?php echo get_selected($mr_score2, "4"); ?>>4</option>
              <option value="5" <?php echo get_selected($mr_score2, "5"); ?>>5</option>
            </select> 점
          </td>
        </tr>
        <tr>
          <th scope="row">향 / 맛</th>
          <td>
            <select name="mr_score3" id="mr_score3">
              <option value="1" <?php echo get_selected($mr_score3, "1"); ?>>1</option>
              <option value="2" <?php echo get_selected($mr_score3, "2"); ?>>2</option>
              <option value="3" <?php echo get_selected($mr_score3, "3"); ?>>3</option>
              <option value="4" <?php echo get_selected($mr_score3, "4"); ?>>4</option>
              <option value="5" <?php echo get_selected($mr_score3, "5"); ?>>5</option>
            </select> 점
          </td>
        </tr>
        <tr>
          <th scope="row">편리함</th>
          <td>
            <select name="mr_score4" id="mr_score4">
              <option value="1" <?php echo get_selected($mr_score4, "1"); ?>>1</option>
              <option value="2" <?php echo get_selected($mr_score4, "2"); ?>>2</option>
              <option value="3" <?php echo get_selected($mr_score4, "3"); ?>>3</option>
              <option value="4" <?php echo get_selected($mr_score4, "4"); ?>>4</option>
              <option value="5" <?php echo get_selected($mr_score4, "5"); ?>>5</option>
            </select> 점
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="mr_title">제목</label></th>
          <td colspan="3"><input type="text" name="mr_title" id="mr_title" value="<?php echo $mr['mr_title']; ?>" required="" class="frm_input required w100p"></td>
        </tr>
        <tr>
          <th scope="row"><label for="mr_summary">요약</label></th>
          <td colspan="3"><input type="text" name="mr_summary" id="mr_summary" value="<?php echo $mr['mr_summary']; ?>" required="" class="frm_input required w100p"></td>
        </tr>
        <tr>
          <th scope="row"><label for="mr_content">내용</label></th>
          <td colspan="3"><textarea name="mr_content" id="mr_content"><?php echo html_purifier($mr['mr_content']); ?></textarea></td>
        </tr>
        <tr>
          <th scope="row"><label for="mr_link">링크</label></th>
          <td colspan="3"><input type="text" name="mr_link" id="mr_link" value="<?php echo $mr['mr_summary']; ?>" class="frm_input w100p"></td>
        </tr>
        <tr>
          <th scope="row">인플루언서</th>
          <td>
            <?php echo help("리뷰종류를 서포터로 할 경우 인플루언서 지정은 필수 입니다."); ?>
            <select name="inf_id" id="inf_id">
              <?php echo conv_selected_option($inf_list, $mr['inf_id']); ?>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">게시</th>
          <td>
            <input type="radio" name="mr_confirm" value="1" id="mr_confirm_yes" <?php echo $mr_confirm_yes; ?>>
            <label for="mr_confirm_yes">예</label>
            <input type="radio" name="mr_confirm" value="0" id="mr_confirm_no" <?php echo $mr_confirm_no; ?>>
            <label for="mr_confirm_no">아니오</label>
          </td>
        </tr>
        <?php for ($i = 1; $i <= 10; $i++) { ?>
          <tr>
            <th scope="row"><label for="mr_img<?php echo $i; ?>">사진 <?php echo $i; ?></label></th>
            <td>
              <input type="file" name="mr_img<?php echo $i; ?>" id="mr_img<?php echo $i; ?>">
              <?php
              $mr_img = G5_DATA_PATH . '/mainreview/' . $mr['mr_img' . $i];
              $mr_img_exists = run_replace('shop_itemuse_image_exists', (is_file($mr_img) && file_exists($mr_img)), $mr, $i);
              if ($mr_img_exists) {
                $thumb = get_mr_thumbnail($mr['mr_img' . $i], 50, 50);
                $img_tag = run_replace('shop_itemuse_image_tag', '<img src="' . G5_DATA_URL . '/mainreview/' . $mr['mr_img' . $i] . '" class="shop_itemuse_preview_image" >', $mr, $i);
              ?>
                <label for="mr_img<?php echo $i; ?>_del"><span class="sound_only">사진 <?php echo $i; ?> </span>파일삭제</label>
                <input type="checkbox" name="mr_img<?php echo $i; ?>_del" id="mr_img<?php echo $i; ?>_del" value="1">
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

      </tbody>
    </table>
  </div>

  <div class="btn_fixed_top">
    <a href="./main_review_list.php?<?php echo $qstr; ?>" class="btn_02 btn">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
  </div>
</form>
<style>
  .w100p { width: 100%; }
</style>

<script>
  <?php if ($w == 'u') { ?>
    $(".banner_or_img").addClass("sit_wimg");
    $(function() {
      $(".sit_wimg_view").bind("click", function() {
        var sit_wimg_id = $(this).attr("id").split("_");
        var $img_display = $("#" + sit_wimg_id[1]);

        $img_display.toggle();

        if ($img_display.is(":visible")) {
          $(this).text($(this).text().replace("확인", "닫기"));
        } else {
          $(this).text($(this).text().replace("닫기", "확인"));
        }

        var $img = $("#" + sit_wimg_id[1]).children("img");
        var width = $img.width();
        var height = $img.height();
        if (width > 700) {
          var img_width = 700;
          var img_height = Math.round((img_width * height) / width);

          $img.width(img_width).height(img_height);
        }
      });
      $(".sit_wimg_close").bind("click", function() {
        var $img_display = $(this).parents(".banner_or_img");
        var id = $img_display.attr("id");
        $img_display.toggle();
        var $button = $("#it_" + id + "_view");
        $button.text($button.text().replace("닫기", "확인"));
      });
    });
  <?php } ?>

  function fitemuseform_submit(f) {
    /*
    <?php echo get_editor_js('mr_content'); ?>
    <?php echo get_editor_js('mr_reply_content'); ?>
	*/
    return true;
  }
</script>

<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');
