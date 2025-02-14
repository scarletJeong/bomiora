<?php
$sub_menu = "300200";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');


// 인플루언서회원리스트
$in_member_select = '';
$sql = " select * from {$g5['member_table']} ";
$sql .= " where mb_level = '{$_const['level']['인플루언서']}' ";
$sql .= " order by mb_datetime desc ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
	$in_member_select .= "<option value=\"{$row['mb_id']}\"".get_selected($_GET['sfl_mb_id'], $row['mb_id']).">{$row['mb_name']}</option>\n";
}

$sql_common = " from {$g5['in_charge_table']} ch";
//$sql_search = " where (1) ";
$sql_search = " where ch.ch_admin = 1 ";// 관리자에서 설정해준 값만 출력

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case 'mb_id':
            $sql_search .= " (ch.{$sfl} = '{$stx}') ";
            break;
        default:
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if ($sca != "")
	$sql_search .= " and (ch.mb_id = '{$sca}') ";// 회원검색

if ($sca2 != "")
	$sql_search .= " and (ch.ch_content = '{$sca2}') ";// 충전내역

if($stx_sday && $stx_eday)
	$sql_search .= " and (left(ch_datetime,10) between '$stx_sday' and '$stx_eday')";// 기간

if (!$sst) {
    $sst  = "ch_id";
    $sod = "desc";
}
$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search}
            {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) {
    $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
}
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select ch.*, mb.mb_name, mb.mb_nick, mb.mb_email, mb.mb_homepage, mb.mb_charge
            {$sql_common}
            LEFT JOIN {$g5['member_table']} mb ON ch.mb_id = mb.mb_id 
            {$sql_search}
            {$sql_order}
            limit {$from_record}, {$rows} ";

$result = sql_query($sql);

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';

$mb = array();
if ($sca) {
    $mb = get_member($sca);
}

$g5['title'] = '충전내역관리';
require_once './admin.head.php';
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');
$colspan = 10;
/*
$ch_expire_term = '';
if ($config['cf_point_term'] > 0) {
    $ch_expire_term = $config['cf_point_term'];
}

if (strstr($sfl, "mb_id")) {
    $mb_id = $stx;
} else {
    $mb_id = "";
}
*/
$qstr = "$qstr&amp;sca=$sca&amp;sca2=$sca2&amp;stx_sday=$stx_sday&amp;stx_eday=$stx_eday&amp;page=$page";
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">전체 </span><span class="ov_num"> <?php echo number_format($total_count) ?> 건 </span></span>
    <?php
    if (isset($mb['mb_id']) && $mb['mb_id']) {
        echo '&nbsp;<span class="btn_ov01"><span class="ov_txt">' . $mb['mb_id'] . ' 님 포인트 합계 </span><span class="ov_num"> ' . number_format($mb['mb_charge']) . '원</span></span>';
    } else {
        $row2 = sql_fetch(" select sum(ch_point) as sum_point from {$g5['in_charge_table']} where ch_admin = 1");
        echo '&nbsp;<span class="btn_ov01"><span class="ov_txt">전체 합계</span><span class="ov_num">' . number_format($row2['sum_point']) . '원 </span></span>';
    }
    ?>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
	<label for="sfl" class="sound_only">인플루언서회원</label>
	<select name="sca" id="sca">
		<option value="">회원</option>
		<?php echo conv_selected_option($in_member_select, $sca); ?>
	</select>
	<!--충전내용 :-->
	<label for="sca2" class="sound_only">충전내용</label>
	<select name="sca2" id="sca2">
		<option value="">충전내용</option>
		<?php foreach($_const['ch_content_arr'] as $key => $value) { ?>
		<option value="<?=$key?>"<?php echo get_selected($sca2, $key); ?>><?=$value?></option>
		<?php }	?>
	</select>

    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" id="sfl">
        <option value="mb_id" <?php echo get_selected($sfl, "mb_id"); ?>>회원아이디</option>
        <option value="ch_content" <?php echo get_selected($sfl, "ch_content"); ?>>내용</option>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
    <input type="submit" class="btn_submit" value="검색">
</form>

<form name="fchargelist" id="fchargelist" method="post" action="./influencer_charge_list_delete.php" onsubmit="return fchargelist_submit(this);">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
	<input type="hidden" name="sca" value="<?php echo $sca ?>">
	<input type="hidden" name="sca2" value="<?php echo $sca2 ?>">
	<input type="hidden" name="stx_sday" value="<?php echo $stx_sday ?>">
	<input type="hidden" name="stx_eday" value="<?php echo $stx_eday ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="token" value="">

    <div class="tbl_head01 tbl_wrap">
        <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <thead>
                <tr>
                    <th scope="col">
                        <label for="chkall" class="sound_only">포인트 내역 전체</label>
                        <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
                    </th>
                    <th scope="col"><?php echo subject_sort_link('mb_id') ?>회원아이디</a></th>
                    <th scope="col">이름</th>
                    <th scope="col">닉네임</th>
					<th scope="col">이메일</th>
                    <th scope="col"><?php echo subject_sort_link('ch_content') ?>충전내용</a></th>
					<th scope="col"><?php echo subject_sort_link('ch_sub_content') ?>비고</a></th>
                    <th scope="col"><?php echo subject_sort_link('ch_point') ?>금액</a></th>
                    <th scope="col"><?php echo subject_sort_link('ch_datetime') ?>일시</a></th>
                    <th scope="col">금액합</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $row = sql_fetch_array($result); $i++) {
					/*
                    $mb_nick = get_sideview($row['mb_id'], $row['mb_nick'], $row['mb_email'], $row['mb_homepage']);

                    $link1 = $link2 = '';
                    if (!preg_match("/^\@/", $row['ch_rel_table']) && $row['ch_rel_table']) {
                        $link1 = '<a href="' . get_pretty_url($row['ch_rel_table'], $row['ch_rel_id']) . '" target="_blank">';
                        $link2 = '</a>';
                    }

                    $expr = '';
                    if ($row['ch_expired'] == 1) {
                        $expr = ' txt_expired';
                    }
					*/

                    $bg = 'bg' . ($i % 2);
                ?>

                    <tr class="<?php echo $bg; ?>">
                        <td class="td_chk">
                            <input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
                            <input type="hidden" name="ch_id[<?php echo $i ?>]" value="<?php echo $row['ch_id'] ?>" id="ch_id_<?php echo $i ?>">
                            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['ch_content'] ?> 내역</label>
                            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
                        </td>
                        <td class="td_left"><a href="?sca=<?php echo $row['mb_id'] ?>"><?php echo $row['mb_id'] ?></a></td>
                        <td class="td_left"><?php echo get_text($row['mb_name']); ?></td>
                        <td class="td_left sv_use"><?php echo get_text($row['mb_nick']); ?></td>
						<td class="td_left sv_use"><?php echo get_text($row['mb_email']); ?></td>
                        <td class="td_left"><?php echo $link1 ?><?php echo $row['ch_content'] ?><?php echo $link2 ?></td>
						<td class="td_left"><?php echo $row['ch_sub_content'] ?></td>
                        <td class="td_num td_pt"><?php echo number_format($row['ch_point']) ?></td>
                        <td class="td_datetime"><?php echo $row['ch_datetime'] ?></td>
						<!--
                        <td class="td_datetime2<?php echo $expr; ?>">
                            <?php if ($row['ch_expired'] == 1) { ?>
                                만료<?php echo substr(str_replace('-', '', $row['ch_expire_date']), 2); ?>
                            <?php } else {
                                echo $row['ch_expire_date'] == '9999-12-31' ? '&nbsp;' : $row['ch_expire_date'];
                            } ?>
                        </td>
						-->
                        <td class="td_num td_pt"><?php echo number_format($row['ch_mb_point']) ?></td>
                    </tr>

                <?php
                }

                if ($i == 0) {
                    echo '<tr><td colspan="' . $colspan . '" class="empty_table">자료가 없습니다.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="btn_fixed_top">
        <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    </div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<section id="point_mng">
    <h2 class="h2_frm">인플루언서 충전금 증감 설정</h2>

    <form name="fchargelist2" method="post" id="fchargelist2" action="./influencer_charge_update.php" autocomplete="off">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <input type="hidden" name="token" value="<?php echo isset($token) ? $token : ''; ?>">
		<input type="hidden" name="sfl_mb_id" value="<?php echo $sfl_mb_id ?>">
		<input type="hidden" name="sfl_ch_content" value="<?php echo $sfl_ch_content ?>">
		<input type="hidden" name="stx_sday" value="<?php echo $stx_sday ?>">
		<input type="hidden" name="stx_eday" value="<?php echo $stx_eday ?>">
        <div class="tbl_frm01 tbl_wrap">
            <table>
                <colgroup>
                    <col class="grid_4">
                    <col>
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row"><label for="mb_id">회원아이디<strong class="sound_only">필수</strong></label></th>
                        <td>
							<select name="mb_id" id="mb_id" required>
								<option value="">선택하세요</option>
								<?php echo conv_selected_option($in_member_select, $sca); ?>
							</select>
						</td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ch_content">충전금 내용<strong class="sound_only">필수</strong></label></th>
                        <td>
							<select name="ch_content" id="ch_content" required>
								<option value="">선택</option>
								<?php foreach($_const['ch_content_arr'] as $key => $value) { ?>
								<option value="<?php echo $key ?>"<?php echo get_selected($sfl_ch_content, $key); ?>><?php echo $value ?></option>
								<?php }	?>
							</select>
						</td>
                    </tr>
					<tr>
						<th scope="row"><label for="ch_point">충전금<strong class="sound_only">필수</strong></label></th>
						<td><input type="text" name="ch_point" id="ch_point" required class="required frm_input"></td>
					</tr>
					<tr>
						<th scope="row"><label for="ch_sub_content">비고</label></th>
						<td><input type="text" name="ch_sub_content" id="ch_sub_content" class="frm_input" size="80"></td>
					</tr>
                </tbody>
            </table>
        </div>

        <div class="btn_confirm01 btn_confirm">
            <input type="submit" value="확인" class="btn_submit btn">
        </div>

    </form>

</section>

<script>
    function fchargelist_submit(f) {
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
require_once './admin.tail.php';
