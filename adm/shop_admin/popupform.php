<?php
$sub_menu = '500600';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "w");

$pu_id = isset($_REQUEST['pu_id']) ? preg_replace('/[^0-9]/', '', $_REQUEST['pu_id']) : 0;
$pu = array(
  'pu_id' => 0,
  'pu_alt' => '',
  'pu_device' => '',
  'pu_location' => '',
  'pu_type' => '',
  'pu_border' => '',
  'pu_new_win' => '',
  'pu_order' => ''
);

$html_title = '팝업';
$g5['title'] = $html_title . '관리';

if ($w == "u") {
  $html_title .= ' 수정';
  $sql = " select * from {$g5['g5_shop_popup_table']} where pu_id = '$pu_id' ";
  $pu = sql_fetch($sql);
} else {
  $html_title .= ' 입력';
  $pu['pu_url']        = "";
  $pu['pu_begin_time'] = date("Y-m-d 00:00:00", time());
  $pu['pu_end_time']   = date("Y-m-d 00:00:00", time() + (60 * 60 * 24 * 31));
}

include_once(G5_ADMIN_PATH . '/admin.head.php');
?>

<form name="fpopup" action="./popupformupdate.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="w" value="<?php echo $w; ?>">
  <input type="hidden" name="pu_id" value="<?php echo $pu_id; ?>">
  <input type="hidden" name="pu_type" value="<?php echo $pu['type']; ?>">
  <div class="tbl_frm01 tbl_wrap">
    <table>
      <caption><?php echo $g5['title']; ?></caption>
      <colgroup>
        <col class="grid_4">
        <col>
      </colgroup>
      <tbody>
        <tr>
          <th scope="row">팝업 대상</th>
          <td>
            <input type="radio" name="pu_device" value="" id="pu_device_all" <?php echo get_checked($pu['pu_device'], ''); ?>>
            <label for="pu_device_all">전체</label>
            <input type="radio" name="pu_device" value="pc" id="pu_device_pc" <?php echo get_checked($pu['pu_device'], 'pc'); ?>>
            <label for="pu_device_pc">PC</label>
            <input type="radio" name="pu_device" value="mobile" id="pu_device_mobile" <?php echo get_checked($pu['pu_device'], 'mobile'); ?>>
            <label for="pu_device_mobile">MOBILE</label>
          </td>
        </tr>
        <tr>
          <th scope="row">이미지 파일</th>
          <td>
            <input type="file" name="pu_img" id="pu_img" accept="image/gif, image/jpeg, image/png">
            <?php
            $pu_img_str = '';
            $pu_img = G5_IMG_PATH . "/popup/popup_{$pu['pu_id']}.{$pu['pu_type']}";
            if (file_exists($pu_img) && $pu['pu_id']) {
              $size = @getimagesize($pu_img);
              if ($size[0] && $size[0] > 750) {
                $width = 750;
              } else {
                $width = $size[0];
              }
              echo '<input type="checkbox" name="pu_img_del" value="1" id="pu_img_del"> <label for="pu_img_del">삭제</label>';
              $pu_img_str = '<img src="' . G5_IMG_URL . "/popup/popup_{$pu['pu_id']}.{$pu['pu_type']}" . '" width="' . $width . '">';
            }
            if ($pu_img_str) {
              echo '<div class="popup_or_img">';
              echo $pu_img_str;
              echo '</div>';
            }
            ?>
          </td>
        </tr>
        <!--
	<tr>
        <th scope="row"><label for="pu_subject">팝업제목</label></th>
        <td>
            <?php echo help("탭방식 팝업일 경우 탭이름."); ?>
            <input type="text" name="pu_subject" size="80" value="<?php echo get_text($pu['pu_subject']); ?>" id="pu_subject" class="frm_input">
        </td>
    </tr>
	-->
        <tr>
          <th scope="row"><label for="pu_alt">이미지 설명</label></th>
          <td>
            <?php echo help("팝업에 마우스를 오버하면 이미지의 설명이 나옵니다. (생략 가능)"); ?>
            <!--<textarea name="pu_alt" id="pu_alt" class="textarea" style="width:100%;height:50px"><?php echo get_text($pu['pu_alt']); ?></textarea>-->
            <input type="text" name="pu_alt" value="<?php echo get_text($pu['pu_alt']); ?>" id="pu_alt" class="frm_input" size="80">
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="pu_url">링크</label></th>
          <td>
            <?php echo help("팝업 클릭시 이동하는 주소입니다. (생략 가능)"); ?>
            <input type="text" name="pu_url" size="80" value="<?php echo get_sanitize_input($pu['pu_url']); ?>" id="pu_url" class="frm_input">
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="pu_location">출력위치</label></th>
          <td>
            <select name="pu_location" id="pu_location">
              <?php foreach ($_const['popup_location'] as $key => $value) { ?>
                <option value="<?php echo $key ?>" <?php echo get_selected($key, $pu['pu_location']) ?>><?php echo $value ?></option>
              <?php } ?>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="pu_border">테두리</label></th>
          <td>
            <?php echo help("팝업이미지에 테두리를 넣을지를 설정합니다.", 50); ?>
            <select name="pu_border" id="pu_border">
              <option value="0" <?php echo get_selected($pu['pu_border'], 0); ?>>사용안함</option>
              <option value="1" <?php echo get_selected($pu['pu_border'], 1); ?>>사용</option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="pu_new_win">새창</label></th>
          <td>
            <?php echo help("팝업클릭시 새창을 띄울지를 설정합니다.", 50); ?>
            <select name="pu_new_win" id="pu_new_win">
              <option value="0" <?php echo get_selected($pu['pu_new_win'], 0); ?>>사용안함</option>
              <option value="1" <?php echo get_selected($pu['pu_new_win'], 1); ?>>사용</option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="pu_begin_time">시작일시</label></th>
          <td>
            <?php echo help("팝업 게시 시작일시를 설정합니다."); ?>
            <input type="text" name="pu_begin_time" value="<?php echo $pu['pu_begin_time']; ?>" id="pu_begin_time" class="frm_input" size="21" maxlength="19">
            <input type="checkbox" name="pu_begin_chk" value="<?php echo date("Y-m-d 00:00:00", time()); ?>" id="pu_begin_chk" onclick="if (this.checked == true) this.form.pu_begin_time.value=this.form.pu_begin_chk.value; else this.form.pu_begin_time.value = this.form.pu_begin_time.defaultValue;">
            <label for="pu_begin_chk">오늘</label>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="pu_end_time">종료일시</label></th>
          <td>
            <?php echo help("팝업 게시 종료일시를 설정합니다."); ?>
            <input type="text" name="pu_end_time" value="<?php echo $pu['pu_end_time']; ?>" id="pu_end_time" class="frm_input" size=21 maxlength=19>
            <input type="checkbox" name="pu_end_chk" value="<?php echo date("Y-m-d 23:59:59", time() + 60 * 60 * 24 * 31); ?>" id="pu_end_chk" onclick="if (this.checked == true) this.form.pu_end_time.value=this.form.pu_end_chk.value; else this.form.pu_end_time.value = this.form.pu_end_time.defaultValue;">
            <label for="pu_end_chk">오늘+31일</label>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="pu_order">출력 순서</label></th>
          <td>
            <?php echo help("팝업를 출력할 때 순서를 정합니다. 숫자가 작을수록 먼저 출력됩니다."); ?>
            <?php echo order_select("pu_order", $pu['pu_order']); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="btn_fixed_top">
    <a href="./popuplist.php" class="btn_02 btn">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
  </div>

</form>

<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');
