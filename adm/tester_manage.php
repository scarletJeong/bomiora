<?php
$sub_menu = '600300';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "w");

$tr_no  = isset($_REQUEST['tr_no']) ? preg_replace('/[^0-9]/', '', $_REQUEST['tr_no']) : '';
if (!$tr_no) {
  alert('등록된 자료가 없습니다.');
}

$sql = "select * from {$g5['g5_tester_list_table']} where tr_no = '{$tr_no}' limit 1; ";
$tr = sql_fetch($sql);
if (!$tr['tr_no']) {
  alert('유효하지 않은 정보입니다.');
}

$it_id = $tr['it_id'];
$it = get_shop_item_with_category($it_id);
if (!(isset($it['it_id']) && $it['it_id'])) {
  alert('유효하지 않은 제품입니다.');
}

$is_valid_dt = (strtotime('now') >= strtotime($tr['from_date'])) && (strtotime('now') <= strtotime($tr['to_date']));
$applicable = $is_valid_dt && ((int)$tr['quota'] > (int)$applied);

$g5['title'] = '체험단 관리';
include_once(G5_ADMIN_PATH . '/admin.head.php');
include_once(G5_ADMIN_PATH . '/tester_lib.php');

$is_confirm = $tr['is_confirm'] == 'y' ? '예' : '아니오';
$tester_target = $_const['tester_target'][$tr['tester_target']];

$week = ['일','월','화','수','목','금','토'];
$fr_date = date('Y년 m월 d일', strtotime($tr['fr_date'])) . ' ' . $week[date('w', strtotime($tr['fr_date']))] . '요일';
$to_date = date('Y년 m월 d일', strtotime($tr['to_date'])) . ' ' . $week[date('w', strtotime($tr['to_date']))] . '요일';

$count = get_tester_count($tr_no);
$tr = array_merge($tr, $count);

$disable_change = '';
if ($count['applied'] > 0 || $count['selected'] > 0) {
  $disable_change = 'style="pointer-events: none;"';
}

if (!$tr['title']) $tr['title'] = $it['it_basic'];
if ($tr['tr_img']) {
  $img = get_tr_thumbnail($tr['tr_img'], 236, 236);
  if ($img) {
    $tr['img'] = $img;
  }
}
if (!$tr['img']) {
  for ($i = 1; $i <= 10; $i++) {
    $img_name = $it['it_img' . $i];
    if ($img_name) {
      $img = get_it_thumbnail($img_name, 236, 236);
      if ($img) {
        $tr['img'] = $img;
        break;
      }
    }
  }
}

$ta_list = ['applied'=>[],'selected'=>[],'canceled'=>[]];
$sql =  "SELECT * FROM {$g5['g5_tester_apply_table']} WHERE tr_no = '{$tr_no}' order by ta_no desc;";
$result = sql_query($sql);
if ($result) {
  while ($row = sql_fetch_array($result)) {
    $row['mb_link'] = '<a href="' . G5_ADMIN_URL . '/member_form.php?w=u&amp;mb_id=' . $row['mb_id'] . '" target="_blank" rel="nofollow">' . $row['mb_id'] .'</a>';
    $row['tt_link'] = '<a href="https://' . $tester_target[1] . '/' . $row['tt_id'] . '" target="_blank" rel="nofollow" class="btn btn_03">미션확인</a>';
    $row['addr_link'] = '<a href="#" class="addr_show btn btn_02" data-addr_info="' . $row['ta_zip1'] . '|' . $row['ta_addr1'] . '|' . $row['ta_addr2'] . '|' . $row['ta_addr3'] . '|' . $row['ad_oversea']  .'">' . $row['ta_name'] . '</a>';
    if ($row['apply_cancel'] != 'n') {
      $ta_list['cancelled'][] = $row;
    } else if ($row['selected']) {
      $ta_list['selected'][] = $row;
    } else {
      $ta_list['applied'][] = $row;
    }
  }
}

$status = get_tester_status($tr);

?>
<div>
  <input type="hidden" name="tr_no" value="<?php echo $tr_no; ?>">
  <div class="tbl_frm01 tbl_wrap">
    <table>
      <caption><?php echo $g5['title']; ?></caption>
      <colgroup>
        <col class="grid_4">
        <col>
      </colgroup>
      <tbody>
        <tr>
          <th scope="row">미션 제품</th>
          <td colspan="3">
            <div class="product-item">
              <div class="img-wrap">
                <?php echo $tr['img']; ?>
              </div>
              <div class="description">
                <p class="subject"><?php echo $it['it_subject']; ?></p>
                <p class="name"><?php echo $it['it_name']; ?></p>
                <p class="title"><?php echo $tr['title']; ?></p>
              </div>
              <div class="price">
                <p class="original-price"><?php echo number_format($it['it_cust_price']); ?>원</p>
                <p class="offer-price"><?php echo number_format($tr['tr_price']); ?>원</p>
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="fr_date">시작일</label></th>
          <td>
            <?php echo $fr_date; ?>
          </td>
          <th scope="row"><label for="to_date">종료일</label></th>
          <td>
            <?php echo $to_date; ?>
          </td>
        </tr>
        <tr>
          <th scope="row">모집정원</th>
          <td>
            <?php echo $tr['quota']; ?>명
          </td>
          <th scope="row">모집상태</th>
          <td>
            <?php echo $status; ?>
          </td>
        </tr>
        <tr>
          <th scope="row">신청인원</th>
          <td>
            <span class="applied"><?php echo $count['applied']; ?></span>명
          </td>
          <th scope="row">선정인원</th>
          <td>
            <span class="selected"><?php echo $count['selected']; ?></span>명
          </td>
        </tr>
        <tr>
          <th scope="row">등록채널</th>
          <td>
            <?php echo $tester_target[0]; ?>
          </td>
          <th scope="row">게시</th>
          <td>
            <?php echo $is_confirm; ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="ref_link">신청</label></th>
          <td colspan="3" class="tbl_head01 tbl_wrap">
            <table>
              <thead>
                <tr>
                  <th scope="col">회원아이디</th>
                  <th scope="col">이름</th>
                  <th scope="col">휴대폰</th>
                  <th scope="col">채널아이디</th>
                  <th scope="col">채널미션수행</th>
                  <th scope="col">신청일시</th>
                  <th scope="col">관리</th>
                </tr>
              </thead>
              <tbody>
            <?php if ($ta_list['applied']) { foreach ($ta_list['applied'] as $ta) { ?>
            <tr>
              <td><?php echo $ta['mb_link']; ?></td>
              <td><?php echo $ta['addr_link']; ?></td>
              <td><?php echo format_phone($ta['ta_hp']); ?></td>
              <td><?php echo $ta['tt_id']; ?></td>
              <td class="no_pop"><?php echo $ta['tt_link']; ?></td>
              <td><?php echo $ta['a_datetime']; ?></td>
              <td class="action_box no_pop" data-tr_no="<?php echo $ta['tr_no']; ?>" data-ta_no="<?php echo $ta['ta_no']; ?>" data-mb_id="<?php echo $ta['mb_id']; ?>">
                <?php if ($status == '중단') { ?>
                -
                <?php } else { ?>
                <?php if ($tr['selected'] < $tr['quota']) { ?>
                <a href="#" data-action="select" class="do_action btn btn_01">선정</a>
                <?php } ?>
                <a href="#" data-action="cancel" class="do_action btn btn_03">신청취소</a>
                <?php } ?>
              </td>
            </tr>
            <?php } } else { ?>
            <tr><td colspan="7">데이터가 없습니다.</td></tr>
            <?php } ?>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="ref_link">선정</label></th>
          <td colspan="3" class="tbl_head01 tbl_wrap">
            <table>
              <thead>
                <tr>
                  <th scope="col">회원아이디</th>
                  <th scope="col">이름</th>
                  <th scope="col">휴대폰</th>
                  <th scope="col">채널아이디</th>
                  <th scope="col">채널미션수행</th>
                  <th scope="col">선정일시</th>
                  <th scope="col">관리</th>
                </tr>
              </thead>
              <tbody>
            <?php if ($ta_list['selected']) { foreach ($ta_list['selected'] as $ta) { ?>
            <tr>
              <td><?php echo $ta['mb_link']; ?></td>
              <td><?php echo $ta['addr_link']; ?></td>
              <td><?php echo format_phone($ta['ta_hp']); ?></td>
              <td><?php echo $ta['tt_id']; ?></td>
              <td class="no_pop"><?php echo $ta['tt_link']; ?></td>
              <td><?php echo $ta['s_datetime']; ?></td>
              <td class="action_box no_pop" data-tr_no="<?php echo $ta['tr_no']; ?>" data-ta_no="<?php echo $ta['ta_no']; ?>" data-mb_id="<?php echo $ta['mb_id']; ?>" data-od_id="<?php echo $ta['od_id']; ?>">
                <?php if ($status == '중단') { ?>
                -
                <?php } else { $od_status = get_tester_od_status($ta['od_id']); ?>
                <?php if ($od_status == '배송' && $ta['msg_sent'] == 'n') { ?>
                <a href="#" data-action="reviewrequest" class="do_action btn btn_01">리뷰요청</a>
                <?php } ?>
                <?php if ($od_status == '입금') { ?>
                <a href="#" data-action="cancelselect" class="do_action btn btn_03">선정취소</a>
                <?php } ?>
                <?php } ?>
              </td>
            </tr>
            <?php } } else { ?>
            <tr><td colspan="7">데이터가 없습니다.</td></tr>
            <?php } ?>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="ref_link">신청취소</label></th>
          <td colspan="3" class="tbl_head01 tbl_wrap">
            <table>
              <thead>
                <tr>
                  <th scope="col">회원아이디</th>
                  <th scope="col">이름</th>
                  <th scope="col">휴대폰</th>
                  <th scope="col">채널아이디</th>
                  <th scope="col">채널미션수행</th>
                  <th scope="col">취소일시</th>
                  <th scope="col">관리</th>
                </tr>
              </thead>
              <tbody>
            <?php if ($ta_list['cancelled']) { foreach ($ta_list['cancelled'] as $ta) { ?>
            <tr>
              <td><?php echo $ta['mb_link']; ?></td>
              <td><?php echo $ta['addr_link']; ?></td>
              <td><?php echo format_phone($ta['ta_hp']); ?></td>
              <td><?php echo $ta['tt_id']; ?></td>
              <td class="no_pop"><?php echo $ta['tt_link']; ?></td>
              <td><?php echo $ta['datetime']; ?></td>
              <td class="action_box no_pop" data-tr_no="<?php echo $ta['tr_no']; ?>" data-ta_no="<?php echo $ta['ta_no']; ?>" data-mb_id="<?php echo $ta['mb_id']; ?>">
                <?php if ($status == '중단') { ?>
                -
                <?php } else { ?>
                <a href="#" data-action="cancelback" class="do_action btn btn_03">제외취소</a>
                <?php } ?>
              </td>
            </tr>
            <?php } } else { ?>
            <tr><td colspan="7">데이터가 없습니다.</td></tr>
            <?php } ?>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="btn_fixed_top">
    <a href="./tester_list.php?<?php echo $qstr; ?>" class="btn_02 btn">목록</a>
  </div>
</div>

<style>
  .product-item {
    display: flex;
    gap: 3rem;
    align-items: center;
  }

  .product-item .img-wrap img {
    width: 236px;
  }

  .product-item .description {
    max-width: 30%;
  }

  .product-item .description .subject {
    font-size: 0.8rem;
    color: #999999;
  }

  .product-item .description .name {
    font-size: 1.2rem;
    font-weight: 600;
  }

  .product-item .description .title {
    padding: 1.1875rem 0;
    border-top: 0.0625rem solid #dedede;
    border-bottom: 0.0625rem solid #dedede;
    margin: 1.0625rem 0;
    font-size: 0.75rem;
  }

  .product-item .price {
    text-align: center;
    font-size: 1.2rem;
    width: 100%;
    max-width: 30%;
  }

  .product-item .price > p {
    padding: 0.7rem 0;
  }

  .product-item .price .original-price {
    font-weight: 500;
    color: #9f9f9f;
    text-decoration: line-through;
  }

  .product-item .price .offer-price {
    font-weight: 600;
    color: #ff3787;
  }
</style>
<script>

  $(document).on("click", ".do_action[data-action]", function(e) {
    e.preventDefault();

    var _this = $(this),
    btn_title = _this.text(),
    action = _this.attr("data-action"),
    _action_box = _this.closest(".action_box"),
    tr_no = _action_box.attr("data-tr_no"),
    ta_no = _action_box.attr("data-ta_no"),
    mb_id = _action_box.attr("data-mb_id"),
    od_id = _action_box.attr("data-od_id");

    var _table = get_info_table(_this);
    if (!_table) return false;

    var _confirm_box = $("<div class='confirm_btns'></div>");
    _confirm_box.append("<div class='msg'>상기 신청자를 " + btn_title + " 하시겠습니까?</div>");

    $("<button class='btn btn_02'>닫기</button>").on("click", function(e) {
      e.preventDefault();
      $(this).closest("div.modal_bg.modal_msg").trigger("click");
      return false;
    }).appendTo(_confirm_box);

    $("<button class='btn btn_01'>체험단 " + btn_title  + "</button>").on("click", function(e) {
      e.preventDefault();

      $.ajax({
        url: '/adm/ajax.tester.php',
        type: 'post',
        data: {
          target: 'tester_set',
          task: action,
          tr_no: tr_no,
          ta_no: ta_no,
          mb_id: mb_id,
          od_id: od_id ? od_id : '',
        },
        dataType: 'json',
        success: function(result) {
          if (result.msg) {
            alert(result.msg);
          }

          if (result.error) {
            return false;
          }

          if (result.reload == 'y') {
            location.reload();
          }
        },
        error: function(error) { console.log(error); },
        complete: function() {
          $("#popup_section .modal_bg").trigger("click");
        }
      });
      return false;
    }).appendTo(_confirm_box);

    var _modal_box = $("<div class='modal_box confirm_box' />");
    _modal_box.append("<div class='tbl_head01 tbl_wrap'>" + _table + "</div>");
    _modal_box.append(_confirm_box);

    var _modal_bg = $("<div class='modal_bg modal_msg on' />");
    _modal_bg.append(_modal_box).appendTo($("#popup_section"));

    return false;
  });

  function get_info_table(_this) {
    var _td = _this.closest("tr").find("td").not(".no_pop");
    var _th = _this.closest("table").find("th");
    var _table = "<table><thead><th>항목</th><th>내용</th></thead><tbody>";

    _td.each(function(idx, item) {
      var _that = $(item);
      if (_that.find("input[type=checkbox]").length) return true;

      var f = _th.eq(idx).text();
      var v = _that.text();
      _table += "<tr><td>" + f + "</td><td>" + v + "</td></tr>";
    });
    _table += "</tbody></table>";

    return _table;
  }

  $(document).on("click", ".addr_show[data-addr_info]", function(e) {
    e.preventDefault();

    var _this = $(this),
    btn_title = _this.text(),
    arr_addr_info = _this.attr("data-addr_info").split("|"),
    arr_title = ["우편번호","주소1","주소2","주소3","해외주소"]

    var _table = "<table><thead><th>항목</th><th>내용</th></thead><tbody>";
    for (i = 0; i < arr_addr_info.length; i++) {
      var val = arr_addr_info[i];
      if (val) {
        if (i == 4) {
          val = val > 0 ? "예" : "아니오";
        }
      } else {
        val = "-";
      }
      _table += "<tr><td>" + arr_title[i] + "</td><td>" + val + "</td></tr>";
    }

    var _confirm_box = $("<div class='confirm_btns'></div>");
    _confirm_box.append("<div class='msg'>" + btn_title + "님의 배송지 주소</div>");

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
