<?php
$sub_menu = '400650';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = '리뷰';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$where = " where ";
$sql_search = "";
$save_stx = isset($_REQUEST['save_stx']) ? clean_xss_tags($_REQUEST['save_stx'], 1, 1) : '';

if ($stx != "") {
    if ($sfl != "") {
        $sql_search .= " $where $sfl like '%$stx%' ";
        $where = " and ";
    }
    if ($save_stx != $stx)
        $page = 1;
}

if ($sca != "") {
    $sql_search .= " and ca_id like '$sca%' ";
}

if ($sca2 != "") {
    $sql_search .= " and is_pay_mthod = '$sca2' ";
}

if ($sca3 != "") {
    $sql_search .= " and is_rvkind = '$sca3' ";
}

if ($sfl == "")  $sfl = "a.it_name";
if (!$sst) {
    $sst = "is_id";
    $sod = "desc";
}

$sql_common = "  from {$g5['g5_shop_item_use_table']} a
                 left join {$g5['g5_shop_item_table']} b on (a.it_id = b.it_id)
                 left join {$g5['member_table']} c on (a.mb_id = c.mb_id) ";
$sql_common .= $sql_search;

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql  = " select *
          $sql_common
          order by $sst $sod, is_id desc
          limit $from_record, $rows ";
$result = sql_query($sql);

//$qstr = 'page='.$page.'&amp;sst='.$sst.'&amp;sod='.$sod.'&amp;stx='.$stx;
$qstr .= ($qstr ? '&amp;' : '').'sca='.$sca.'sca2='.$sca2.'sca3='.$sca3.'&amp;save_stx='.$stx;

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    <span class="btn_ov01"><span class="ov_txt"> 전체 리뷰내역</span><span class="ov_num">  <?php echo $total_count; ?>건</span></span>
</div>

<form name="flist" class="local_sch01 local_sch">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="save_stx" value="<?php echo $stx; ?>">

<label for="sca" class="sound_only">분류선택</label>
<select name="sca" id="sca">
    <option value=''>전체분류</option>
    <?php
    $sql1 = " select ca_id, ca_name from {$g5['g5_shop_category_table']} order by ca_order, ca_id ";
    $result1 = sql_query($sql1);
    for ($i=0; $row1=sql_fetch_array($result1); $i++) {
        $len = strlen($row1['ca_id']) / 2 - 1;
        $nbsp = "";
        for ($i=0; $i<$len; $i++) $nbsp .= "&nbsp;&nbsp;&nbsp;";
        $selected = ($row1['ca_id'] == $sca) ? ' selected="selected"' : '';
        echo '<option value="'.$row1['ca_id'].'"'.$selected.'>'.$nbsp.$row1['ca_name'].'</option>'.PHP_EOL;
    }
    ?>
</select>

<select name="sca2" id="sca2">
    <option value=''>구매방식</option>
    <option value="solo" <?php echo get_selected($sca2, 'solo'); ?>>내돈내산</option>
    <option value="group" <?php echo get_selected($sca2, 'group'); ?>>평가단</option>
</select>

<select name="sca" id="sca">
    <option value=''>리뷰종류</option>
    <option value="general" <?php echo get_selected($sca2, 'general'); ?>>일반</option>
    <option value="supporter" <?php echo get_selected($sca2, 'supporter'); ?>>서포터</option>
</select>

<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="it_name" <?php echo get_selected($sfl, 'it_name'); ?>>제품명</option>
    <option value="a.it_id" <?php echo get_selected($sfl, 'a.it_id'); ?>>제품코드</option>
    <option value="is_name" <?php echo get_selected($sfl, 'is_name'); ?>>이름</option>
</select>

<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" id="stx" value="<?php echo $stx; ?>" class="frm_input">
<input type="submit" value="검색" class="btn_submit">

</form>

<form name="fitemuselist" method="post" action="./itemuselistupdate.php" onsubmit="return fitemuselist_submit(this);" autocomplete="off">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sca2" value="<?php echo $sca2; ?>">
<input type="hidden" name="sca3" value="<?php echo $sca3; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
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
		<th scope="col"><?php echo subject_sort_link("it_name"); ?>제품명</a></th>
        <th scope="col"><?php echo subject_sort_link("a.it_id"); ?>제품코드</a></th>
		<th scope="col"><?php echo subject_sort_link("is_rvkind"); ?>리뷰종류</a></th>
		<th scope="col"><?php echo subject_sort_link("is_pay_mthod"); ?>구매방식</a></th>
        <th scope="col"><?php echo subject_sort_link("is_name"); ?>이름</a></th>
        <th scope="col"><?php echo subject_sort_link("is_score1"); ?>효과</a></th>
		<th scope="col"><?php echo subject_sort_link("is_score2"); ?>가성비</a></th>
		<th scope="col"><?php echo subject_sort_link("is_score3"); ?>향/맛</a></th>
		<th scope="col"><?php echo subject_sort_link("is_score4"); ?>편리함</a></th>
        <th scope="col"><?php echo subject_sort_link("is_confirm"); ?>게시</a></th>
        <th scope="col">관리</th>
    </tr>
    </thead> 
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $href = shop_item_url($row['it_id']);
        $name = get_text($row['is_name']);
        $is_content = get_view_thumbnail(conv_content($row['is_content'], 1), 300);

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['is_subject']) ?> 리뷰</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
            <input type="hidden" name="is_id[<?php echo $i; ?>]" value="<?php echo $row['is_id']; ?>">
            <input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
        </td>
        <td class="td_left"><a href="<?php echo $href; ?>"><?php echo get_it_image($row['it_id'], 50, 50); ?><?php echo cut_str($row['it_name'],30); ?></a></td>
		<td class=""><?php echo $row['it_id']; ?></td>
		<td class=""><?php echo ($row['is_rvkind'] == 'general') ? '일반' : '서포터' ?></td>
		<td class=""><?php echo ($row['is_pay_mthod'] == 'solo') ? '내돈내산' : '평가단' ?></td>
        <td class="td_name"><?php echo $name; ?></td>        
        <td class="td_select">
			<label for="score1_<?php echo $i; ?>" class="sound_only">효과</label>
			<select name="is_score1[<?php echo $i; ?>]" id="score1_<?php echo $i; ?>">
				<option value="5" <?php echo get_selected($row['is_score1'], "5"); ?>>5</option>
				<option value="4" <?php echo get_selected($row['is_score1'], "4"); ?>>4</option>
				<option value="3" <?php echo get_selected($row['is_score1'], "3"); ?>>3</option>
				<option value="2" <?php echo get_selected($row['is_score1'], "2"); ?>>2</option>
				<option value="1" <?php echo get_selected($row['is_score1'], "1"); ?>>1</option>
			</select>
        </td>
		<td class="td_select">
			<label for="score2_<?php echo $i; ?>" class="sound_only">가성비</label>
			<select name="is_score2[<?php echo $i; ?>]" id="score2_<?php echo $i; ?>">
				<option value="5" <?php echo get_selected($row['is_score2'], "5"); ?>>5</option>
				<option value="4" <?php echo get_selected($row['is_score2'], "4"); ?>>4</option>
				<option value="3" <?php echo get_selected($row['is_score2'], "3"); ?>>3</option>
				<option value="2" <?php echo get_selected($row['is_score2'], "2"); ?>>2</option>
				<option value="1" <?php echo get_selected($row['is_score2'], "1"); ?>>1</option>
			</select>
        </td>
		<td class="td_select">
			<label for="score3_<?php echo $i; ?>" class="sound_only">향/맛</label>
			<select name="is_score3[<?php echo $i; ?>]" id="score3_<?php echo $i; ?>">
				<option value="5" <?php echo get_selected($row['is_score3'], "5"); ?>>5</option>
				<option value="4" <?php echo get_selected($row['is_score3'], "4"); ?>>4</option>
				<option value="3" <?php echo get_selected($row['is_score3'], "3"); ?>>3</option>
				<option value="2" <?php echo get_selected($row['is_score3'], "2"); ?>>2</option>
				<option value="1" <?php echo get_selected($row['is_score3'], "1"); ?>>1</option>
			</select>
        </td>
		<td class="td_select">
			<label for="score4_<?php echo $i; ?>" class="sound_only">편리함</label>
			<select name="is_score4[<?php echo $i; ?>]" id="score4_<?php echo $i; ?>">
				<option value="5" <?php echo get_selected($row['is_score4'], "5"); ?>>5</option>
				<option value="4" <?php echo get_selected($row['is_score4'], "4"); ?>>4</option>
				<option value="3" <?php echo get_selected($row['is_score4'], "3"); ?>>3</option>
				<option value="2" <?php echo get_selected($row['is_score4'], "2"); ?>>2</option>
				<option value="1" <?php echo get_selected($row['is_score4'], "1"); ?>>1</option>
			</select>
        </td>

        <td class="td_chk2">
            <label for="confirm_<?php echo $i; ?>" class="sound_only">게시</label>
            <input type="checkbox" name="is_confirm[<?php echo $i; ?>]" <?php echo ($row['is_confirm'] ? 'checked' : ''); ?> value="1" id="confirm_<?php echo $i; ?>">
        </td>
        <td class="td_mng td_mng_s">
            <a href="./itemuseform.php?w=u&amp;is_id=<?php echo $row['is_id']; ?>&amp;<?php echo $qstr; ?>" class="btn btn_03"><span class="sound_only"><?php echo get_text($row['it_name']); ?> </span>수정</a>
        </td>
    </tr>

    <?php
    }

    if ($i == 0) {
        echo '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
	<?php if ($is_admin == 'super') { ?>
            <a href="./itemuseform.php" id="itemuse_add" class="btn btn_01">리뷰추가</a>
        <?php } ?>
</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
function fitemuselist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');