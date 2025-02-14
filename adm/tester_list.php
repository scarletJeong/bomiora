<?php
$sub_menu = '600300';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$where = " where ";
$sql_search = "";

$sql_common = "  from {$g5['g5_tester_list_table']} a
                 left join {$g5['g5_shop_item_table']} b on (a.it_id = b.it_id)
                 left join {$g5['member_table']} c on (a.mb_id = c.mb_id) ";
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
          order by to_date desc, tr_no desc
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

$g5['title'] = '체험단';
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
          <th scope="col">체험단가격</th>
          <th scope="col">제목</th>
          <th scope="col">미션플랫폼</th>
          <th scope="col">모집</th>
          <th scope="col">지원</th>
          <th scope="col">선정</th>
          <th scope="col">시작일</th>
          <th scope="col">종료일</th>
          <th scope="col">상태</th>
          <th scope="col"><?php echo subject_sort_link("is_confirm"); ?>게시</a></th>
          <th scope="col">관리</th>
          <th scope="col">수정</th>
        </tr>
      </thead>
      <tbody>
        <?php
        for ($i = 0; $row = sql_fetch_array($result); $i++) {
          $href = shop_item_url($row['it_id']);
          $bg = 'bg' . ($i % 2);
          $row = array_merge($row, get_tester_count($row['tr_no']));
        ?>

          <tr class="<?php echo $bg; ?>">
            <td class="td_chk">
              <label for="chk_<?php echo $i; ?>" class="sound_only">체험단</label>
              <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
              <input type="hidden" name="tr_no[<?php echo $i; ?>]" value="<?php echo $row['tr_no']; ?>">
              <input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
            </td>
            <td class="">
              <a href="<?php echo $href; ?>">
                <?php echo get_it_image($row['it_id'], 50, 50); ?><br>
                <?php echo cut_str($row['it_name'], 30); ?><br>
                <?php echo $row['it_id']; ?>
              </a>
            </td>
            <td class="td_mng">
              <span><?php echo number_format($row['it_cust_price']); ?>원</span><br>>
              <span><?php echo number_format($row['tr_price']); ?>원</span>
            </td>            
            <td class="td_left max-elipsis min100"><?php echo $row['title'] ? $row['title'] : $row['it_basic']; ?></td>
            <td class="td_mng"><?php echo $_const['tester_target'][(int)$row['tester_target']][0]; ?></td>
            <td class="td_mng_s"><?php echo $row['quota']; ?>명</td>
            <td class="td_mng_s"><?php echo $row['applied']; ?>명</td>
            <td class="td_mng_s"><?php echo $row['selected']; ?>명</td>
            <td class="td_mng"><?php echo date('Y-m-d', strtotime($row['fr_date'])); ?></td>
            <td class="td_mng"><?php echo date('Y-m-d', strtotime($row['to_date'])); ?></td>
            <td class="td_mng"><?php echo get_tester_status($row); ?></td>
            <td class="td_chk2">
              <label for="confirm_<?php echo $i; ?>" class="sound_only">게시</label>
              <input type="checkbox" name="is_confirm[<?php echo $i; ?>]" <?php echo ($row['is_confirm'] == 'y' ? 'checked' : ''); ?> value="y" id="confirm_<?php echo $i; ?>">
            </td>
            <td class="td_mng td_mng_s">
              <a href="./tester_manage.php?w=u&amp;tr_no=<?php echo $row['tr_no']; ?>&amp;<?php echo $qstr; ?>" class="btn btn_03">관리</a>
            </td>            
            <td class="td_mng td_mng_s">
              <a href="./tester_form.php?w=u&amp;tr_no=<?php echo $row['tr_no']; ?>&amp;<?php echo $qstr; ?>" class="btn btn_03">수정</a>
            </td>
          </tr>
        <?php
        }

        if ($i == 0) {
          echo '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <?php if ($is_admin == 'super') { ?>
      <a href="./tester_form.php" id="tester_add" class="btn btn_01">체험단 추가</a>
    <?php } ?>
  </div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<style>
  .img_max_150 img {
    max-width: 150px;
  }

  .max-elipsis {
    max-width: 200px !important;
  }

  .min150 {
    min-width: 150px;
  }
</style>
<script>
  function fitemuselist_submit(f) {
    if (!is_checked("chk[]")) {
      alert(document.pressed + " 하실 항목을 하나 이상 선택하세요.");
      return false;
    }

    if (document.pressed == "선택삭제") {
      if (!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
        return false;
      }
    }

    return true;
  }
</script>

<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');
