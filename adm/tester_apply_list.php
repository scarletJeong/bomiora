<?php
$sub_menu = '600400';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$where = " where ";
$sql_search = "";

$sql_common = "  from {$g5['g5_tester_apply_table']} a
                 left join {$g5['g5_shop_item_table']} b on (a.it_id = b.it_id)";
$sql_common .= $sql_search;

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) {
  $page = 1;
} // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql  = " select *
          $sql_common
          order by ta_no desc, tr_no desc
          limit $from_record, $rows ";
$result = sql_query($sql);

//var_dump($sql);
//var_dump($result);
//exit;

//var_dump($_const['tester_target']);
//exit;

//$xxx = '1';

//var_dump($_const['tester_target'][(int)$xxx][0]);
//exit;

//$qstr .= ($qstr ? '&amp;' : '') . 'sca=' . $sca . 'sca2=' . $sca2 . 'sca3=' . $sca3 . '&amp;save_stx=' . $stx;
$qstr = '';

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';

$g5['title'] = '체험단 신청자';
include_once(G5_ADMIN_PATH . '/admin.head.php');
include_once(G5_ADMIN_PATH . '/tester_lib.php');
?>

<div class="local_ov01 local_ov">
  <?php echo $listall; ?>
  <span class="btn_ov01"><span class="ov_txt"> 전체 체험단</span><span class="ov_num"> <?php echo $total_count; ?>건</span></span>
</div>

<form name="fitemuselist" method="post" action="./tester_list_update.php" onsubmit="return fitemuselist_submit(this);" autocomplete="off">
  <input type="hidden" name="sst" value="<?php echo $sst; ?>">
  <input type="hidden" name="sod" value="<?php echo $sod; ?>">
  <input type="hidden" name="page" value="<?php echo $page; ?>">

  <div class="tbl_head01 tbl_wrap" id="itemuselist">
    <table>
      <caption><?php echo $g5['title']; ?> 목록</caption>
      <thead>
        <tr>
          <th scope="col">
            <label for="chkall" class="sound_only">리뷰 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
          </th>
          <th scope="col">제품</th>
          <th scope="col">아이디</th>
          <th scope="col">이름</th>
          <th scope="col">핸드폰</th>
          <th scope="col">미션채널</th>
          <th scope="col">미션아이디</th>
          <th scope="col">신청일</th>
          <th scope="col">선정일</th>
          <th scope="col">신청상태</th>
          <th scope="col">배송상태</th>
          <th scope="col">주소</th>
          <th scope="col">체험단</th>
        </tr>
      </thead>
      <tbody>
        <?php
        for ($i = 0; $row = sql_fetch_array($result); $i++) {
          $row['it_link'] = '<a href="' . G5_SHOP_URL . '/tester_item.php?tr_no=' . $row['tr_no'] . '" target="_blank" rel="nofollow">' . $row['it_name'] .'</a>';
          $row['mb_link'] = '<a href="' . G5_ADMIN_URL . '/member_form.php?w=u&amp;mb_id=' . $row['mb_id'] . '" target="_blank" rel="nofollow">' . $row['mb_id'] .'</a>';
          $row['tt_link'] = '<a href="https://' . $tester_target[1] . '/' . $row['tt_id'] . '" target="_blank" rel="nofollow">'.$row['tt_id'].'</a>';
          $row['addr_link'] = '<a href="#" class="addr_show btn btn_01" data-addr_info="' . $row['ta_name'] . '|' . $row['ta_zip1'] . '|' . $row['ta_addr1'] . '|' . $row['ta_addr2'] . '|' . $row['ta_addr3'] . '|' . $row['ad_oversea']  .'">보기</a>';
          $status = $row['apply_cancel'] == 'y' ? '신청취소' : ($row['selected'] ? '선정' : '신청');
          $deliver_status = get_tester_od_status($row['od_id']) == '배송' ? '배송' : '-';
          $bg = 'bg' . ($i % 2);
        ?>

          <tr class="<?php echo $bg; ?>">
            <td class="td_chk">
              <label for="chk_<?php echo $i; ?>" class="sound_only">체험단</label>
              <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
              <input type="hidden" name="ta_no[<?php echo $i; ?>]" value="<?php echo $row['ta_no']; ?>">
              <input type="hidden" name="mb_id[<?php echo $i; ?>]" value="<?php echo $row['mb_id']; ?>">
            </td>
            <td class=""><?php echo $row['it_link']; ?> </td>
            <td class=""><?php echo $row['mb_link']; ?></td>
            <td class=""><?php echo $row['ta_name']; ?></td>
            <td class=""><?php echo format_phone($row['ta_hp']); ?></td>
            <td class=""><?php echo $_const['tester_target'][(int)$row['tester_target']][0]; ?></td>
            <td class=""><?php echo $row['tt_link']; ?></td>
            <td class=""><?php echo date('Y-m-d', strtotime($row['a_datetime'])); ?></td>
            <td class=""><?php echo ($row['s_datetime'] == '0000-00-00 00:00:00' ? '-' : date('Y-m-d', strtotime($row['s_datetime']))); ?></td>
            <td class=""><?php echo $status; ?></td>
            <td class=""><?php echo $deliver_status; ?></td>
            <td class="td_mng td_mng_s"><?php echo $row['addr_link']; ?></td>
            <td class="td_mng td_mng_s">
              <a href="./tester_manage.php?w=u&amp;tr_no=<?php echo $row['tr_no']; ?>&amp;<?php echo $qstr; ?>" class="btn btn_03">관리</a>
            </td>
          </tr>
        <?php
        }

        if ($i == 0) {
          echo '<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="btn_fixed_top">

  </div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<style>
</style>
<script>
  $(document).on("click", ".addr_show[data-addr_info]", function(e) {
    e.preventDefault();

    var _this = $(this),
    arr_addr_info = _this.attr("data-addr_info").split("|"),
    arr_title = ["이름", "우편번호","주소1","주소2","주소3","해외주소"]

    var _table = "<table><thead><th>항목</th><th>내용</th></thead><tbody>";
    for (i = 0; i < arr_addr_info.length; i++) {
      var val = arr_addr_info[i];
      if (val) {
        if (i == 5) {
          val = val > 0 ? "예" : "아니오";
        }
      } else {
        val = "-";
      }
      _table += "<tr><td>" + arr_title[i] + "</td><td>" + val + "</td></tr>";
    }

    var _confirm_box = $("<div class='confirm_btns'></div>");

    $("<button class='btn btn_02'>닫기</button>").on("click", function(e) {
      e.preventDefault();
      $(this).closest("div.modal_bg.modal_msg").trigger("click");
      return false;
    }).appendTo(_confirm_box);

    var _modal_box = $("<div class='modal_box confirm_box' />");
    _modal_box.append("<div class='tbl_head01 tbl_wrap'>" + _table + "</div>");
    _modal_box.append(_confirm_box);

    var _modal_bg = $("<div class='modal_bg modal_msg on' />");
    _modal_bg.append(_modal_box).appendTo($("#popup_section"));

    return false;
  });
</script>

<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');
