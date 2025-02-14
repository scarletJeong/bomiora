<?php
$sub_menu = "600100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

if ($is_admin != 'super') {
  alert('최고관리자만 접근 가능합니다.');
}

$copy_config = get_mkt_config(false);
//var_dump($copy_config);
//exit;
//var_dump($mkt_config['pres_date']);
//var_dump(date('Y년 m월', strtotime($mkt_config['pres_date'])));
//exit;


$g5['title'] = '마케팅 설정';
require_once './admin.head.php';

$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_cf_pres">처방 카운트</a></li>
    <li><a href="#anc_cf_main_display">메인 화면</a></li>
    <li><a href="#anc_cf_intl_lang">다국어</a></li>
</ul>';


$pres_date_type = $copy_config['pres_date'] == '0000-00-00 00:00:00' ? 'auto' : 'manual';
$pres_date = $copy_config['pres_date'] == '0000-00-00 00:00:00' ? G5_TIME_YMDHIS : $copy_config['pres_date'];

$intl_lang = isset($copy_config['intl_lang']) ? array_filter(explode('|', $copy_config['intl_lang'])) : [];
//array_filter( explode(",", $copy_config['intl_lang']), 'strlen' );

$langs = array(
'ko'=>'한국어',
'en'=>'영어',
'ja'=>'일본어',
'zh-CN'=>'중국어(간체)',
'zh-TW'=>'중국어(번체)',
'th'=>'태국어',
);

$user_langs = get_user_langs();

?>

<form name="fconfigform" id="fconfigform" method="post" action="./mkt_config_update.php">
  <input type="hidden" name="cf_no" value="<?php echo $copy_config['cf_no']; ?>">

  <section id="anc_cf_pres">
    <h2 class="h2_frm">카운트 설정</h2>
    <?php echo $pg_anchor ?>
    <div class="tbl_frm01 tbl_wrap">
      <table>
        <caption>홈페이지 기본환경 설정</caption>
        <colgroup>
          <col class="grid_4">
          <col>
          <col class="grid_4">
          <col>
        </colgroup>
        <tbody>
          <tr>
            <th scope="row">처방 카운트 날짜 기준</th>
            <input type="hidden" name="pres_date" id="pres_date" value="<?php echo $copy_config['pres_date']; ?>" />
            <td>
              <input type="radio" name="pres_date_type" value="auto" <?php echo get_checked($pres_date_type, 'auto'); ?> /> 자동
              <input type="radio" name="pres_date_type" value="manual"  <?php echo get_checked($pres_date_type, 'manual'); ?> /> 수동
            </td>
            <th scope="row">수동 년월</th>
            <td>
              <select id="pres_date_y" name="pres_date_y" <?php echo get_disabled($pres_date_type, 'auto'); ?>>
                <?php
                $pres_date_y = (int)substr($pres_date, 0, 4);
                for ($i = -10; $i < 0; $i++) {
                  $y = (int)date('Y') + $i + 1;
                  echo "<option value='{$y}' " . get_selected($y, $pres_date_y) . ">{$y} 년</option>";
                }
                ?>
              </select>
              <select id="pres_date_m" name="pres_date_m" <?php echo get_disabled($pres_date_type, 'auto'); ?>>
                <?php
                $pres_date_m = substr($pres_date, 5, 2);
                for ($i = 1; $i < 13; $i++) {
                  $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                  echo "<option value='{$m}'" . get_selected($m, $pres_date_m) . ">{$m} 월</option>";
                }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <th scope="row">누적 처방 건수 (단위: 1건)</th>
            <td>
              <input type="text" name="pres_count_1" value="<?php echo $copy_config['pres_count_1'] ?>" id="pres_count_1" class="frm_input" size="3">
              건
            </td>
            <th scope="row">누적 처방 포수 (단위: 1만포)</th>
            <td>
              <input type="text" name="pres_count_2" value="<?php echo $copy_config['pres_count_2'] ?>" id="pres_count_2" class="frm_input" size="3">
              만포
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
  <section id="anc_cf_main_display">
    <h2 class="h2_frm">메인 화면 설정</h2>
    <?php echo $pg_anchor ?>
    <div class="tbl_frm01 tbl_wrap">
      <table>
        <caption>메인 화면 설정</caption>
        <colgroup>
          <col class="grid_4">
          <col>
          <col class="grid_4">
          <col>
        </colgroup>
        <tbody>
          <tr>
            <th scope="row">마케팅 리뷰</th>
            <td>
              <input type="radio" name="main_review" value="y" <?php echo get_checked($copy_config['main_review'], 'y'); ?> /> 표시
              <input type="radio" name="main_review" value="" <?php echo get_checked($copy_config['main_review'], ''); ?> /> 미표시
            </td>
            <th scope="row">마케팅 리뷰 표시 갯수</th>
            <td>
              <input type="text" name="main_review_count" value="<?php echo $copy_config['main_review_count']; ?>" class="frm_input" size="3">
              개
            </td>
          </tr>
          <tr>
            <th scope="row">체험단</th>
            <td>
              <input type="radio" name="main_tester" value="y" <?php echo get_checked($copy_config['main_tester'], 'y'); ?> /> 표시
              <input type="radio" name="main_tester" value="" <?php echo get_checked($copy_config['main_tester'], ''); ?> /> 미표시
            </td>
            <th scope="row">체험단 표시 갯수</th>
            <td>
              <input type="text" name="main_tester_count" value="<?php echo $copy_config['main_tester_count']; ?>" class="frm_input" size="3">
              개
            </td>
          </tr>
          <tr>
            <th scope="row">실시간 게시판</th>
            <td>
              <input type="radio" name="main_board" value="y" <?php echo get_checked($copy_config['main_board'], 'y'); ?> /> 표시
              <input type="radio" name="main_board" value="" <?php echo get_checked($copy_config['main_board'], ''); ?> /> 미표시
            </td>
            <th scope="row">실시간 게시판 표시 갯수</th>
            <td>
              <input type="text" name="main_board_count" value="<?php echo $copy_config['main_board_count']; ?>" class="frm_input" size="3">
              개
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
  <section id="anc_cf_intl_lang">
    <h2 class="h2_frm">다국어 설정</h2>
    <?php echo $pg_anchor ?>
    <div class="tbl_frm01 tbl_wrap">
      <table>
        <caption>다국어 설정</caption>
        <colgroup>
          <col class="grid_4">
          <col>
          <col class="grid_4">
          <col>
        </colgroup>
        <tbody>
          <tr>
            <th scope="row">다국어 서비스</th>
            <td colspan="3">
              <input type="radio" name="intl_enable" value="y" <?php echo get_checked($copy_config['intl_enable'], 'y'); ?> /> 제공
              <input type="radio" name="intl_enable" value="" <?php echo get_checked($copy_config['intl_enable'], ''); ?> /> 미제공
            </td>
          </tr>
          <tr>
            <th scope="row">사용자 선택 가능 다국어</th>
            <td colspan="3">
              <input type="hidden" name="intl_lang" value="<?php echo $copy_config['intl_lang']; ?>" />
              <?php
              foreach ($user_langs as $k => $v) {
                $checked = in_array($k, $intl_lang) ? 'checked' : '';
                echo "<input type='checkbox' name='langs[]' value='{$k}' {$checked} /> {$v[0]} ";
              }
              ?>
            </td>
          </tr>
          <tr>
            <th scope="row">구글 클라우드 API 키</th>
            <td colspan="3">
              <input type="text" name="intl_google_api_key" value="<?php echo get_sanitize_input($copy_config['intl_google_api_key']); ?>" size="100" class="frm_input">
            </td>
          </tr>
          <tr>
            <th scope="row">구글 클라우드 프로젝트 ID</th>
            <td colspan="3">
              <input type="text" name="intl_google_project_id" value="<?php echo get_sanitize_input($copy_config['intl_google_project_id']); ?>" size="100" class="frm_input">
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <a href="https://cloud.google.com/translate?hl=ko" class="btn btn_02" target="blank">구글 클라우드</a>
  </section>

  <div class="btn_fixed_top btn_confirm">
    <!-- // <input type="submit" value="확인" class="btn_submit btn" accesskey="s"> -->
    <input type="button" value="확인" class="btn_submit btn">
  </div>

</form>

<script>
  $(document).on("click", "#fconfigform .btn_submit", function(e) {
    e.preventDefault();

    var _form = $("#fconfigform"),
    _intl_lang = _form.find("input[type=hidden][name=intl_lang]:first"),
    arr_intl_lang = [],
    intl_lang_val = "";

    _form.find("input[type=checkbox][name='langs[]']:checked").each(function(e) {
      arr_intl_lang.push($(this).val().trim());
    });

    if (arr_intl_lang.length) {
      intl_lang_val = arr_intl_lang.join("|");
    }

    _intl_lang.val(intl_lang_val);

    _form.submit();
  });

  $(document).on("change", "#fconfigform input[type=radio][name=pres_date_type]", function(e) {
    e.preventDefault();

    var _this = $(this),
    val = _this.val(),
    _tr = _this.closest("tr"),
    _press_date = _tr.find("input[type=hidden][name=pres_date]:first"),
    _press_date_y = _tr.find("select[name=pres_date_y]:first"),
    _press_date_m = _tr.find("select[name=pres_date_m]:first");

    if (val == "auto") {
      _press_date.val("0000-00-00 00:00:00");
      _press_date_y.prop("disabled", true);
      _press_date_m.prop("disabled", true);
    } else {
      _press_date.val(_press_date_y.val() + "-" + _press_date_m.val() + "-01 12:00:00");
      _press_date_y.prop("disabled", false);
      _press_date_m.prop("disabled", false);
    }
  });

  $(document).on("change", "#fconfigform select[name^='pres_date_']", function(e) {
    e.preventDefault();

    var _this = $(this),
    _tr = _this.closest("tr"),
    _press_date = _tr.find("input[type=hidden][name=pres_date]:first"),
    _press_date_y = _tr.find("select[name=pres_date_y]:first"),
    _press_date_m = _tr.find("select[name=pres_date_m]:first");
    _press_date.val(_press_date_y.val() + "-" + _press_date_m.val() + "-01 12:00:00");
  });
</script>

<?php
require_once './admin.tail.php';
