<?php
$sub_menu = '500600';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "r");

$pu_location = (isset($_GET['pu_location']) && in_array($_GET['pu_location'], $_const['popup_location'])) ? $_GET['pu_location'] : '';
$pu_device = (isset($_GET['pu_device']) && in_array($_GET['pu_device'], array('pc', 'mobile'))) ? $_GET['pu_device'] : '';
$pu_time = (isset($_GET['pu_time']) && in_array($_GET['pu_time'], array('ing', 'end'))) ? $_GET['pu_time'] : '';

$where = ' where ';
$sql_search = '';

if ($pu_location) {
  $sql_search .= " $where pu_location = '$pu_location' ";
  $where = ' and ';
  $qstr .= "&amp;pu_location=$pu_location";
}

if ( $pu_device && $pu_device !== '' ){
    $sql_search .= " $where pu_device = '$pu_device' ";
    $where = ' and ';
    $qstr .= "&amp;pu_device=$pu_device";
}

if ($pu_time) {
  $sql_search .= ($pu_time === 'ing') ? " $where '" . G5_TIME_YMDHIS . "' between pu_begin_time and pu_end_time " : " $where pu_end_time < '" . G5_TIME_YMDHIS . "' ";
  $where = ' and ';
  $qstr .= "&amp;pu_time=$pu_time";
}

$g5['title'] = '팝업관리';
include_once(G5_ADMIN_PATH . '/admin.head.php');

$sql_common = " from {$g5['g5_shop_popup_table']} ";
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
?>

<div class="local_ov01 local_ov">
  <span class="btn_ov01"><span class="ov_txt"> <?php echo ($sql_search) ? '검색' : '등록'; ?>된 팝업 </span><span class="ov_num"> <?php echo $total_count; ?>개</span></span>

  <form name="flist" class="local_sch01 local_sch">
    <input type="hidden" name="page" value="<?php echo $page; ?>">

    <label for="pu_location" class="sound_only">검색</label>
    <select name="pu_location" id="pu_location">
      <option value="" <?php echo get_selected($pu_location, '', true); ?>>위치 전체</option>
      <?php foreach ($_const['popup_location'] as $key => $value) { ?>
        <option value="<?php echo $key ?>" <?php echo get_selected($key, $pu_location, true) ?>><?php echo $value ?></option>
      <?php } ?>
    </select>
    <select name="pu_device" id="pu_device">
        <option value=""<?php echo get_selected($pu_device, '', true); ?>>기기 전체</option>
        <option value="pc"<?php echo get_selected($pu_device, 'pc'); ?>>PC</option>
        <option value="mobile"<?php echo get_selected($pu_device, 'mobile'); ?>>모바일</option>
    </select>
    <select name="pu_time" id="pu_time">
      <option value="" <?php echo get_selected($pu_time, '', true); ?>>팝업 시간 전체</option>
      <option value="ing" <?php echo get_selected($pu_time, 'ing'); ?>>진행중인 팝업</option>
      <option value="end" <?php echo get_selected($pu_time, 'end'); ?>>종료된 팝업</option>
    </select>

    <input type="submit" value="검색" class="btn_submit">

  </form>

</div>

<div class="btn_fixed_top">
  <a href="./popupform.php" class="btn_01 btn">팝업추가</a>
</div>

<div class="tbl_head01 tbl_wrap">
  <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
      <tr>
        <th scope="col" id="th_id">ID</th>
        <th scope="col">팝업대상</th>
        <th scope="col">이미지</th>
        <th scope="col">위치</th>
        <th scope="col">형식</th>
        <th scope="col">시작일시</th>
        <th scope="col">종료일시</th>
        <th scope="col">출력순서</th>
        <th scope="col">조회</th>
        <th scope="col">관리</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = " select * from {$g5['g5_shop_popup_table']} $sql_search
          order by pu_order, pu_id desc
          limit $from_record, $rows  ";
      $result = sql_query($sql);
      
      //jjyghh
      var_dump($result);
      echo("@@@");

      for ($i = 0; $row = sql_fetch_array($result); $i++) {
        // 테두리 있는지
        $pu_border  = $row['pu_border'];
        // 새창 띄우기인지
        $pu_new_win = ($row['pu_new_win']) ? 'target="_blank"' : '';

        //pc 이미지
        $pu_img = G5_IMG_PATH . "/popup/popup_{$row['pu_id']}.{$row['pu_type']}";

        echo("$pu_img :" );
        echo($pu_img);

        if (file_exists($pu_img)) {
          $size = @getimagesize($pu_img);
          if ($size[0] && $size[0] > 250)
            $width = 250;
          else
            $width = $size[0];
          $pu_img_str = '<img src="' . G5_IMG_URL . "/popup/popup_{$row['pu_id']}.{$row['pu_type']}" . '?' . preg_replace('/[^0-9]/i', '', $row['pu_time']) . '" width="' . $width . '" alt="' . strip_tags(clean_xss_attributes($row['pu_alt'])) . '">';
        }
        switch($row['pu_device']) {
            case 'pc':
                $pu_device = 'PC';
                break;
            case 'mobile':
                $pu_device = '모바일';
                break;
            default:
                $pu_device = '기기전체';
                break;
        }
        $pu_begin_time = substr($row['pu_begin_time'], 2, 14);
        $pu_end_time   = substr($row['pu_end_time'], 2, 14);

        $bg = 'bg' . ($i % 2);
      ?>

        <tr class="<?php echo $bg; ?>">
          <td class="td_num"><?php echo $row['pu_id'] ?></td>
          <td headers="th_loc"><?php echo $pu_device ?></td>
          <td class="td_img_view pu_img"><?php echo $pu_img_str ?></td>
          <td headers="th_loc"><?php echo $_const['popup_location'][$row['pu_location']] ?></td>
          <td headers="th_loc"><?php echo $row['pu_type'] ?></td>
          <td headers="th_st" class="td_datetime"><?php echo $pu_begin_time ?></td>
          <td headers="th_end" class="td_datetime"><?php echo $pu_end_time ?></td>
          <td headers="th_odr" class="td_num"><?php echo $row['pu_order'] ?></td>
          <td headers="th_hit" class="td_num"><?php echo $row['pu_hit'] ?></td>
          <td headers="th_mng" class="td_mng td_mns_m">
            <a href="./popupform.php?w=u&amp;pu_id=<?php echo $row['pu_id'] ?>" class="btn btn_03">수정</a>
            <a href="./popupformupdate.php?w=d&amp;pu_id=<?php echo $row['pu_id'] ?>" onclick="return delete_confirm(this);" class="btn btn_02">삭제</a>
          </td>
        </tr>
      <?php
      }
      if ($i == 0) {
        echo '<tr><td colspan="10" class="empty_table">자료가 없습니다.</td></tr>';
      }
      ?>
    </tbody>
  </table>

</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');
?>