<?php
$sub_menu = '300300';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$g5['title'] = '인플루언서 제품관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 분류
$ca_list  = '<option value="">선택</option>'.PHP_EOL;
$sql = " select * from {$g5['g5_shop_category_table']} ";
if ($is_admin != 'super')
    $sql .= " where ca_mb_id = '{$member['mb_id']}' ";
$sql .= " order by ca_order, ca_id ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $len = strlen($row['ca_id']) / 2 - 1;
    $nbsp = '';
    for ($i=0; $i<$len; $i++) {
        $nbsp .= '&nbsp;&nbsp;&nbsp;';
    }
    $ca_list .= '<option value="'.$row['ca_id'].'">'.$nbsp.$row['ca_name'].'</option>'.PHP_EOL;
}

$where = " and ";
$sql_search = "";
if ($stx != "") {
    if ($sfl != "") {
        $sql_search .= " $where $sfl like '%$stx%' ";
        $where = " and ";
    }
    if ($save_stx != $stx)
        $page = 1;
}

if ($sca != "") {
    $sql_search .= " $where (a.ca_id like '$sca%' or a.ca_id2 like '$sca%' or a.ca_id3 like '$sca%') ";
}

if ($sinf != "") {
    $sql_search .= " $where (a.it_mb_inf = '$sinf') ";
}

if ($sfl == "")  $sfl = "it_name";

$sql_common = " from {$g5['g5_shop_item_table']} a ,
                     {$g5['g5_shop_category_table']} b
               where it_mb_inf <> '' and (a.ca_id = b.ca_id";
if ($is_admin != 'super')
    $sql_common .= " and b.ca_mb_id = '{$member['mb_id']}'";
$sql_common .= ") ";
$sql_common .= $sql_search;

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

//$rows = $config['cf_page_rows'];
$rows = 75;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if (!$sst) {
    $sst  = "it_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql  = " select *
           $sql_common
           $sql_order
           limit $from_record, $rows ";
$result = sql_query($sql);

//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr  = $qstr.'&amp;sca='.$sca.'&amp;sinf='.$sinf.'&amp;page='.$page.'&amp;save_stx='.$stx;

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    <span class="btn_ov01"><span class="ov_txt">등록된 제품</span><span class="ov_num"> <?php echo $total_count; ?>건</span></span>
</div>

<form name="flist" class="local_sch01 local_sch">
<input type="hidden" name="save_stx" value="<?php echo $stx; ?>">
<!--
<label for="sca" class="sound_only">분류선택</label>
<select name="sca" id="sca">
    <option value="">전체분류</option>
    <?php
	$sql1 = " select ca_id, ca_name from {$g5['g5_shop_category_table']} order by ca_order, ca_id ";
    $result1 = sql_query($sql1);
    for ($i=0; $row1=sql_fetch_array($result1); $i++) {
        $len = strlen($row1['ca_id']) / 2 - 1;
        $nbsp = '';
        for ($i=0; $i<$len; $i++) $nbsp .= '&nbsp;&nbsp;&nbsp;';
        echo '<option value="'.$row1['ca_id'].'" '.get_selected($sca, $row1['ca_id']).'>'.$nbsp.$row1['ca_name'].'</option>'.PHP_EOL;
    }
    ?>
</select>
-->
<label for="sinf" class="sound_only">인플루언서선택</label>
<select name="sinf" id="sinf">
    <option value="">전체인플루언서</option>
    <?php
    $sql1 = " select mb_id, mb_name, mb_nick, mb_inf_code from {$g5['member_table']} where mb_level = '{$_const['level']['인플루언서']}' order by mb_datetime desc ";
    $result1 = sql_query($sql1);
    for ($i=0; $row1=sql_fetch_array($result1); $i++) {
		echo '<option value="'.$row1['mb_id'].'" '.get_selected($sinf, $row1['mb_id']).'>'.$row1['mb_name'].'</option>'.PHP_EOL;
    }
    ?>
</select>
<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="it_name" <?php echo get_selected($sfl, 'it_name'); ?>>제품명</option>
    <option value="it_id" <?php echo get_selected($sfl, 'it_id'); ?>>제품코드</option>
</select>

<label for="stx" class="sound_only">검색어</label>
<input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" class="frm_input">
<input type="submit" value="검색" class="btn_submit">

</form>

<form name="fitemlistupdate" method="post" action="./influencer_itemlistupdate.php" onsubmit="return fitemlist_submit(this);" autocomplete="off" id="fitemlistupdate">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" rowspan="3">
            <label for="chkall" class="sound_only">제품 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('it_id', 'sca='.$sca.'&sinf='.$sinf); ?>제품코드</a></th>
        <!--<th scope="col">분류</th>-->
		<th scope="col"><?php echo subject_sort_link('it_mb_inf', 'sca='.$sca.'&sinf='.$sinf); ?>인플루언서</a></th>
		<th scope="col">이미지</th>
		<th scope="col"><?php echo subject_sort_link('it_name', 'sca='.$sca.'&sinf='.$sinf); ?>제품명</a></th>
		<th scope="col"><?php echo subject_sort_link('it_cust_price', 'sca='.$sca.'&sinf='.$sinf); ?>시중가격</a></th>
		<th scope="col"><?php echo subject_sort_link('it_price', 'sca='.$sca.'&sinf='.$sinf); ?>판매가격</a></th>
		<th scope="col"><?php echo subject_sort_link('it_inf_price', 'sca='.$sca.'&sinf='.$sinf); ?>차감금액</a></th>
        <th scope="col"><?php echo subject_sort_link('it_order', 'sca='.$sca.'&sinf='.$sinf); ?>순서</a></th>
        <th scope="col"><?php echo subject_sort_link('it_use', 'sca='.$sca.'&sinf='.$sinf, 1); ?>판매</a></th>
        <th scope="col"><?php echo subject_sort_link('it_soldout', 'sca='.$sca.'&sinf='.$sinf, 1); ?>품절</a></th>
		<th scope="col"><?php echo subject_sort_link('it_point', 'sca='.$sca.'&sinf='.$sinf); ?>포인트</a></th>
		<th scope="col">도움쿠폰</th>
		<th scope="col">리뷰수</th>
		<!--
        <th scope="col"><?php echo subject_sort_link('it_stock_qty', 'sca='.$sca.'&sinf='.$sinf); ?>재고</a></th>
		-->
        <th scope="col"><?php echo subject_sort_link('it_hit', 'sca='.$sca.'&sinf='.$sinf, 1); ?>조회</a></th>
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
		// 도움쿠폰 수
		$sql2 = " select count(*) as cnt from {$g5['g5_shop_coupon_table']} where cp_method = '0' and cp_target = '{$row['it_id']}' ";		
		$row2 = sql_fetch($sql2);
		$coupon_cnt = $row2['cnt'];

		// 리뷰 수 :: 제품별 전체 리뷰 수
		//echo $row['it_org_id']."<br>";
		$sql3 = " select count(*) as cnt from {$g5['g5_shop_item_use_table']} where is_confirm = '1' and it_id = '{$row['it_id']}' ";
		$row3 = sql_fetch($sql3);
		$review_cnt = $row3['cnt'];

		$inf_mb = get_member($row['it_mb_inf']);
        $href = shop_item_url($row['it_id']);
        $bg = 'bg'.($i%2);

        $it_point = $row['it_point'];
        if($row['it_point_type'])
            $it_point .= '%';
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['it_name']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
        </td>
        <td class="td_num">
            <input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
            <?php echo $row['it_id']; ?>
        </td>
		<!--
        <td class="td_sort">
            <label for="ca_id_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['it_name']); ?> 기본분류</label>
            <select name="ca_id[<?php echo $i; ?>]" id="ca_id_<?php echo $i; ?>">
                <?php echo conv_selected_option($ca_list, $row['ca_id']); ?>
            </select>
        </td>
		-->
		<td class="td_left">
            이름 : <?php echo $inf_mb['mb_name'] ?><br>
			아이디 : <?php echo get_text($row['it_mb_inf']) ?>
        </td>
		<td class="td_img"><a href="<?php echo $href; ?>"><?php echo get_it_image($row['it_id'], 50, 50); ?></a></td>
        <td class="td_input">
            <label for="name_<?php echo $i; ?>" class="sound_only">제품명</label>
            <input type="text" name="it_name[<?php echo $i; ?>]" value="<?php echo htmlspecialchars2(cut_str($row['it_name'],250, "")); ?>" id="name_<?php echo $i; ?>" required class="tbl_input required" size="30">
        </td>
		<td class="td_numbig td_input">
			<label for="cust_price_<?php echo $i; ?>" class="sound_only">시중가격</label>
			<input type="text" name="it_cust_price[<?php echo $i; ?>]" value="<?php echo $row['it_cust_price']; ?>" id="cust_price_<?php echo $i; ?>" class="tbl_input sit_camt" size="7">
		</td>
		<td class="td_numbig td_input">
			<label for="price_<?php echo $i; ?>" class="sound_only">판매가격</label>
			<input type="text" name="it_price[<?php echo $i; ?>]" value="<?php echo $row['it_price']; ?>" id="price_<?php echo $i; ?>" class="tbl_input sit_amt" size="7">
		</td>
		<td class="td_numbig td_input">
			<label for="inf_price_<?php echo $i; ?>" class="sound_only">차감금액</label>
			<input type="text" name="it_inf_price[<?php echo $i; ?>]" value="<?php echo $row['it_inf_price']; ?>" id="inf_price_<?php echo $i; ?>" class="tbl_input sit_amt" size="7">
		</td>
        <td class="td_num">
            <label for="order_<?php echo $i; ?>" class="sound_only">순서</label>
            <input type="text" name="it_order[<?php echo $i; ?>]" value="<?php echo $row['it_order']; ?>" id="order_<?php echo $i; ?>" class="tbl_input" size="3">
        </td>
        <td>
            <label for="use_<?php echo $i; ?>" class="sound_only">판매여부</label>
            <input type="checkbox" name="it_use[<?php echo $i; ?>]" <?php echo ($row['it_use'] ? 'checked' : ''); ?> value="1" id="use_<?php echo $i; ?>">
        </td>
        <td>
            <label for="soldout_<?php echo $i; ?>" class="sound_only">품절</label>
            <input type="checkbox" name="it_soldout[<?php echo $i; ?>]" <?php echo ($row['it_soldout'] ? 'checked' : ''); ?> value="1" id="soldout_<?php echo $i; ?>">
        </td>
		<td class="td_numbig td_input"><?php echo $it_point; ?></td>
		<!--
        <td class="td_numbig td_input">
            <label for="stock_qty_<?php echo $i; ?>" class="sound_only">재고</label>
            <input type="text" name="it_stock_qty[<?php echo $i; ?>]" value="<?php echo $row['it_stock_qty']; ?>" id="stock_qty_<?php echo $i; ?>" class="tbl_input sit_qty" size="7">
        </td>
		-->
		<td class="td_num"><?php echo $coupon_cnt; ?></td>
		<td class="td_num"><?php echo $review_cnt; ?></td>
        <td class="td_num"><?php echo $row['it_hit']; ?></td>
        <td class="td_mng td_mng_s">
            <a href="./influencer_itemform.php?w=u&amp;it_id=<?php echo $row['it_id']; ?>&amp;ca_id=<?php echo $row['ca_id']; ?>&amp;<?php echo $qstr; ?>" class="btn btn_03"><span class="sound_only"><?php echo htmlspecialchars2(cut_str($row['it_name'],250, "")); ?> </span>수정</a>
            <a href="<?php echo $href; ?>" class="btn btn_02"><span class="sound_only"><?php echo htmlspecialchars2(cut_str($row['it_name'],250, "")); ?> </span>보기</a>
        </td>       
    </tr>	
    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="12" class="empty_table">자료가 한건도 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <?php if ($is_admin == 'super') { ?>
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <?php } ?>
</div>
<!-- <div class="btn_confirm01 btn_confirm">
    <input type="submit" value="일괄수정" class="btn_submit" accesskey="s">
</div> -->
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
function fitemlist_submit(f)
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