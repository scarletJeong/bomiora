<?php
$sub_menu = '600200';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = '메인 리뷰';
include_once(G5_ADMIN_PATH . '/admin.head.php');

$where = " where ";
$sql_search = "";

$abc = "mr_order_num";
$def = "asc";
$sst = "mr_no";
$sod = "desc";

$sql_common = "  from {$g5['g5_main_review_table']} a
                 left join {$g5['g5_shop_item_table']} b on (a.it_id = b.it_id)
                 left join {$g5['member_table']} c on (a.inf_id = c.mb_id) ";
$sql_common .= $sql_search;

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// $rows = $config['cf_page_rows'];
$rows = 50;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) {
  $page = 1;
} // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql  = " select *
          $sql_common
          order by $abc $def, $sst $sod
          limit $from_record, $rows ";
$result = sql_query($sql);

$qstr .= ($qstr ? '&amp;' : '') . 'sca=' . $sca . 'sca2=' . $sca2 . 'sca3=' . $sca3 . '&amp;save_stx=' . $stx;

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';
?>

<div class="local_ov01 local_ov">
  <?php echo $listall; ?>
  <span class="btn_ov01"><span class="ov_txt"> 전체 리뷰내역</span><span class="ov_num"> <?php echo $total_count; ?>건</span></span>
</div>

<form name="fitemuselist" method="post" action="./main_review_list_update.php" onsubmit="return fitemuselist_submit(this);" autocomplete="off">
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
          <th scope="col">제목</th>
          <th scope="col">요약</th>            
          <th scope="col">이미지</th>
          <th scope="col">평가점수</th>
          <th scope="col">작성일</th>
          <th scope="col"><?php echo subject_sort_link("is_confirm"); ?>게시</th>
		  <th scope="col">순위</th>
          <th scope="col">관리</th>
        </tr>
      </thead>
      <tbody>
        <?php
        for ($i = 0; $row = sql_fetch_array($result); $i++) {
          $href = shop_item_url($row['it_id']);
          $name = get_text($row['mr_name']);
          $mr_content = get_view_thumbnail(conv_content($row['mr_content'], 1), 300);

          $bg = 'bg' . ($i % 2);
        ?>

          <tr class="<?php echo $bg; ?>">
            <td class="td_chk">
              <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['mr_subject']) ?> 리뷰</label>
              <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
              <input type="hidden" name="mr_no[<?php echo $i; ?>]" value="<?php echo $row['mr_no']; ?>">
              <input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
			  <input type="hidden" name="rank_number" value="">
            </td>
            <td class="">
              <a href="<?php echo $href; ?>">
              <?php echo get_it_image($row['it_id'], 50, 50); ?><br>
              <?php echo cut_str($row['it_name'], 30); ?><br>
              <?php echo $row['it_id']; ?>
              </a>
            </td>
            <td class="min150"><?php echo $row['mr_title']; ?></td>
            <td class="td_left max-elipsis"><?php echo $row['mr_summary']; ?></td>            
            <?php if ($row['mr_img1']) {
              echo '<td class="img_max_150 td_mng_s">' . get_mr_thumbnail($row['mr_img1'], 80, 80) . '</td>';
            } ?>
            
            <td class="td_mng_s"><?php echo ($row['mr_score1'] + $row['mr_score2'] + $row['mr_score3'] + $row['mr_score4']) / 4; ?></td>
            <td class=""><?php echo date('Y-m-d', strtotime($row['mr_datetime'])); ?></td>

            <td class="td_chk2">
              <label for="confirm_<?php echo $i; ?>" class="sound_only">게시</label>
              <input type="checkbox" name="mr_confirm[<?php echo $i; ?>]" <?php echo ($row['mr_confirm'] ? 'checked' : ''); ?> value="1" id="confirm_<?php echo $i; ?>">
            </td>
			<td class="td_mng_s"></td>
			<td class="td_mng td_mng_s">
              <a href="./main_review_form.php?w=u&amp;mr_no=<?php echo $row['mr_no']; ?>&amp;<?php echo $qstr; ?>" class="btn btn_03">수정</a>
            </td>
          </tr>
        <?php
        }

        if ($i == 0) {
          echo '<tr><td colspan="9" class="empty_table">자료가 없습니다.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <?php if ($is_admin == 'super') { ?>
      <a href="./main_review_form.php" id="main_review_add" class="btn btn_01">리뷰추가</a>
    <?php } ?>
	<a href="javascript:void(0);" onclick="main_review_arrange_order();" id="arrange_order" class="btn btn_02">랭크확인</a>
	<input type="submit" name="act_button" value="랭크수정" onclick="document.pressed=this.value" class="btn btn_02">
  </div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<style>
  .img_max_150 img { max-width: 150px; }
  .max-elipsis { max-width: 200px !important;  }
  .min150 { min-width: 150px; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
  function fitemuselist_submit(f) {	

	if (document.pressed == "랭크수정") {
		main_review_arrange_order();

		var chk = document.getElementsByName("chk[]");
		var chk_total = chk.length;		
		var chked_total = 0;
		for (var i=0; i<chk.length; i++) {
			if (chk[i].checked) {
				chked_total += 1;
			}
		}
		
		if (chk_total != chked_total) {
			alert(document.pressed + "은 전체 선택하세요.");
			return false;
		}

	}
	  
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
 
  jQuery(document).ready(function() {
	jQuery("table > tbody").sortable({
		start : function(event, ui) {
			ui.item.addClass("selected");
		}, 
		stop : function(event, ui) {
			ui.item.removeClass("selected");
		}
	});
  });
  
  function main_review_arrange_order() {
	var order_number = 0;
	
	$('table>tbody>tr').each(function(i) {
		switch (<?php echo $page ?>) {
		  case 1:
			order_number = (i + 1);
			break;
		  case 2:
			order_number = (i + 16);
			break;
		  case 3:
			order_number = (i + 31);
			break;
		}
		$(this).find('td').eq(8).html(order_number);
		document.getElementsByName("rank_number")[i].value=order_number;
	});
  }
  
</script>

<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');
