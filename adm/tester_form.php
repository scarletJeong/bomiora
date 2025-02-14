<?php
$sub_menu = '600300';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check_menu($auth, $sub_menu, "w");

/* 제품리스트 */
$item_list  = '<option value="">선택</option>';
$sql = " select * from {$g5['g5_shop_item_table']} ";
//$sql .= " where it_id = '{$it_id}' ";
//jacknam
//$sql .= " WHERE it_org_id = '' group by it_id ";
$sql .= " WHERE it_org_id = '' OR (it_org_id <> '' AND ca_id <> '40') GROUP BY it_id ";
$sql .= " order by ca_id asc, it_id asc ";
$result = sql_query($sql);
for ($i = 0; $row = sql_fetch_array($result); $i++) {
  $item_list .= '<option value="' . $row['it_id'] . '">' . $row['it_name'] . '</option>';
}

if ($w == '') {
  $html_title = '추가';
  $confirm_y_yes  =  'checked="checked"';
  $confirm_no   = '';

} elseif ($w == 'u') {
  $tr_no = isset($_GET['tr_no']) ? preg_replace('/[^0-9]/', '', $_GET['tr_no']) : 0;
  $sql = "select * from {$g5['g5_tester_list_table']} where tr_no = '{$tr_no}' limit 1; ";
  $tr = sql_fetch($sql);
  if (!$tr['tr_no']) {
    alert('등록된 자료가 없습니다.');
  }

  // 게시
  $confirm_yes  =  $tr['tr_confirm'] ? 'checked="checked"' : '';
  $confirm_no   = !$tr['tr_confirm'] ? 'checked="checked"' : '';

  $html_title = '수정';
} else {
  alert('제대로 된 값이 넘어오지 않았습니다.');
}

$g5['title'] = '체험단 수정';
include_once(G5_ADMIN_PATH . '/admin.head.php');
include_once(G5_PLUGIN_PATH . '/jquery-ui/datepicker.php');
include_once(G5_ADMIN_PATH . '/tester_lib.php');

$tester_target = $tr['tester_target'] ?? '1';
$is_confirm = $tr['is_confirm'] ?? 'y';

$fr_date = $tr['fr_date'] ? date('Y-m-d', strtotime($tr['fr_date'])) : date('Y-m-d');
$to_date = $tr['to_date'] ? date('Y-m-d', strtotime($tr['to_date'])) : date('Y-m-d', strtotime('+7 days'));

$count = get_tester_count($tr_no);

$disable_change = '';
if ($count['applied'] > 0 || $count['selected'] > 0) {
  $disable_change = 'style="pointer-events: none;"';
}

?>

<form name="ftesterform" id="ftesterform" action="./tester_form_update.php" method="post" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return ftesterform_submit(this)">
  <input type="hidden" name="w" value="<?php echo $w; ?>">
  <input type="hidden" name="tr_no" value="<?php echo $tr_no; ?>">
  <input type="hidden" name="page" value="<?php echo $page; ?>">
  <input type="hidden" name="mb_id" value="<?php echo ($tr['mb_id']) ? $tr['mb_id'] : $member['mb_id'] ?>">
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
            <select name="it_id" id="it_id" <?php echo $disable_change; ?>>
              <?php echo conv_selected_option($item_list, $tr['it_id']); ?>
            </select>
          </td>
          <th scope="row">체험단가격</th>
          <td>
            <input type="text" id="tr_price" name="tr_price" value="<?php echo (int)$tr['tr_price']; ?>" class="frm_input" size="30" readonly> 원
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="fr_date">시작일</label></th>
          <td>
            <input type="text" id="fr_date" name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input required" size="30" maxlength="10" readonly>
          </td>
          <th scope="row"><label for="to_date">종료일</label></th>
          <td>
            <input type="text" id="to_date" name="to_date" value="<?php echo $to_date; ?>" class="frm_input required" size="30" maxlength="10" readonly>
          </td>
        </tr>
        <tr>
          <th scope="row">모집정원</th>
          <td>
            <select name="quota" id="quota" style="width: 30%;">
              <option value="">선택</option>
              <?php
              for ($i = 1; $i < 25; $i++) {
                if ($i > 5) {
                  $ii = $i * 5 - 20;
                } else {
                  $ii = $i;
                }
                $selected = $ii == $tr['quota'] ? ' selected' : '';
                echo "<option value='{$ii}'{$selected}>{$ii}</option>";
              }
              ?>
            </select> 명
          </td>
          <th scope="row">모집현황</th>
          <td>
            신청 : <?php echo $count['applied']; ?>명 / 선정 : <?php echo $count['selected']; ?>명
          </td>
        </tr>
        <tr>
          <th scope="row">등록채널</th>
          <td>
            <?php foreach ($_const['tester_target'] as $k => $v) { ?>
            <input type="radio" name="tester_target" value="<?php echo $k; ?>" id="tester_target_<?php echo $k; ?>" <?php echo get_checked($tester_target, $k); ?> <?php echo $disable_change; ?>>
            <label for="tester_target_<?php echo $k; ?>"><?php echo $v[0]; ?></label>
            <?php } ?>
          </td>
          <th scope="row">게시</th>
          <td>
            <input type="radio" name="is_confirm" value="y" id="confirm_yes" <?php echo get_checked($is_confirm, 'y'); ?>>
            <label for="confirm_yes">예</label>
            <input type="radio" name="is_confirm" value="n" id="confirm_no" <?php echo get_checked($is_confirm, 'n'); ?>>
            <label for="confirm_no">아니오</label>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="tr_img">대표이미지</label></th>
          <td colspan="3">
            <input type="file" name="tr_img" id="tr_img">
            <?php
            $tr_img = G5_DATA_PATH . '/tester/' . $tr['tr_img'];
            $tr_img_exists = run_replace('shop_tester_image_exists', (is_file($tr_img) && file_exists($tr_img)), $tr, 1);
            if ($tr_img_exists) {
            $thumb = get_tr_thumbnail($tr['tr_img'], 50, 50);
            $img_tag = run_replace('shop_tester_image_tag', '<img src="' . G5_DATA_URL . '/tester/' . $tr['tr_img'] . '" class="shop_itemuse_preview_image" >', $tr, 1);
            ?>
            <label for="tr_img_del"><span class="sound_only">대표이미지 </span>파일삭제</label>
            <input type="checkbox" name="tr_img_del" id="tr_img_del" value="1">
            <span class="sit_wimg_limg"><?php echo $thumb; ?></span>
            <div id="limg" class="banner_or_img">
              <?php echo $img_tag; ?>
              <button type="button" class="sit_wimg_close">닫기</button>
            </div>
            <script>
              $('<button type="button" id="it_limg_view" class="btn_frmline sit_wimg_view">사진 확인</button>').appendTo('.sit_wimg_limg');
            </script>
            <?php } ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="title">제목</label></th>
          <td colspan="3"><input type="text" name="title" id="title" value="<?php echo $tr['title']; ?>" placeholder="입력하지 않으면 제품의 슬로건이 표시됩니다." class="frm_input w100p"></td>
        </tr>
        <tr>
          <th scope="row">검색키워드</th>
          <td colspan="3"> <?php echo editor_html('keyword', get_editor_content('keyword', $tr), 1); ?></td>
        </tr>
        <tr>
          <th scope="row">체험단미션</th>
          <td colspan="3"> <?php echo editor_html('mission', get_editor_content('mission', $tr), 1); ?></td>
        </tr>
        <tr>
          <th scope="row">추가안내사항</th>
          <td colspan="3"> <?php echo editor_html('guide', get_editor_content('guide', $tr), 1); ?></td>
        </tr>
        <tr>
          <th scope="row">제품 설명</th>
          <td colspan="3"> <?php echo editor_html('it_content', get_editor_content('it_content', $tr), 1); ?></td>
        </tr>
        <tr>
          <th scope="row"><label for="ref_link">참조링크</label></th>
          <td colspan="3"><input type="text" name="ref_link" id="ref_link" value="<?php echo $tr['ref_link']; ?>" class="frm_input w100p"></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="btn_fixed_top">
    <a href="./tester_list.php?<?php echo $qstr; ?>" class="btn_02 btn">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
  </div>
</form>
<style>
  .w100p { width: 100%; }
</style>

<script>
    $("#fr_date, #to_date").datepicker({
      changeMonth: true,
      changeYear: true,
      //dateFormat: "yy년 mm월 dd일 (DD)",
      dateFormat: "yy-mm-dd",
      showButtonPanel: true,
      yearRange: "c-99:c+99",
      maxDate: "+1y"
    });

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

  function ftesterform_submit(f) {
    var it_id = $("#it_id > option:selected").val();
    if (it_id == "") {
      alert("제품명을 선택해 주세요");
      return false;
    }

    var quota = $("#quota > option:selected").val();
    if (quota == "") {
      alert("모집정원을 선택해 주세요");
      return false;
    }

    var fr_date = new Date(f.fr_date.value);
    var to_date = new Date(f.to_date.value);
    //console.log(fr_date, to_date, fr_date > to_date);
    if (fr_date > to_date) {
      alert("종료일이 시작일보다 빠를 수 없습니다.");
      return false;
    }
    //return false;

    <?php echo get_editor_js('keyword'); ?>
    <?php echo get_editor_js('mission'); ?>
    <?php echo get_editor_js('guide'); ?>
    <?php echo get_editor_js('it_content'); ?>

    //console.log(keyword);

    //return false;

    return true;
  }
</script>

<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');
