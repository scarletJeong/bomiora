<?php
$sub_menu = "300110";
require_once './_common.php';

//var_dump($_const['level']);
//exit;

/*
CREATE TABLE `bomiora_shop_vendor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vd_id` int(11) NOT NULL DEFAULT 0,
  `inf_id` int(11) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `vd_id` (`vd_id`),
  KEY `inf_id` (`inf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
*/

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from {$g5['member_table']} ";

//$sql_search = " where mb_level = '{$_const['level']['인플루언서']}' ";
$sql_search = " where mb_level = '{$_const['level']['인플루언서']}' and mb_5 ='y'";
if ($stx) {
  $sql_search .= " and ( ";
  switch ($sfl) {
    case 'mb_charge':
      $sql_search .= " ({$sfl} >= '{$stx}') ";
      break;
    case 'mb_level':
      $sql_search .= " ({$sfl} = '{$stx}') ";
      break;
    case 'mb_tel':
    case 'mb_hp':
      $sql_search .= " ({$sfl} like '%{$stx}') ";
      break;
    default:
      $sql_search .= " ({$sfl} like '{$stx}%') ";
      break;
  }
  $sql_search .= " ) ";
}

if (!$sst) {
  $sst = "mb_datetime";
  $sod = "desc";
}

$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);

$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) {
  $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
}
$from_record = ($page - 1) * $rows; // 시작 열을 구함

// 탈퇴회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$leave_count = $row['cnt'];

// 차단회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_intercept_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$intercept_count = $row['cnt'];

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';

$g5['title'] = '벤더관리';
require_once './admin.head.php';

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$colspan = 16;
?>
<div class="local_ov01 local_ov">
  <?php echo $listall ?>
  <span class="btn_ov01"><span class="ov_txt">총벤더수 </span><span class="ov_num"> <?php echo number_format($total_count) ?>명 </span></span>
  <a href="?sst=mb_intercept_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>" class="btn_ov01" data-tooltip-text="차단된 순으로 정렬합니다.&#xa;전체 데이터를 출력합니다."> <span class="ov_txt">차단 </span><span class="ov_num"><?php echo number_format($intercept_count) ?>명</span></a>
  <a href="?sst=mb_leave_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>" class="btn_ov01" data-tooltip-text="탈퇴된 순으로 정렬합니다.&#xa;전체 데이터를 출력합니다."> <span class="ov_txt">탈퇴 </span><span class="ov_num"><?php echo number_format($leave_count) ?>명</span></a>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
  <label for="sfl" class="sound_only">검색대상</label>
  <select name="sfl" id="sfl">
    <option value="mb_id" <?php echo get_selected($sfl, "mb_id"); ?>>회원아이디</option>
    <option value="mb_nick" <?php echo get_selected($sfl, "mb_nick"); ?>>닉네임</option>
    <option value="mb_name" <?php echo get_selected($sfl, "mb_name"); ?>>이름</option>
    <option value="mb_email" <?php echo get_selected($sfl, "mb_email"); ?>>E-MAIL</option>
    <option value="mb_hp" <?php echo get_selected($sfl, "mb_hp"); ?>>연락처</option>
    <option value="mb_charge" <?php echo get_selected($sfl, "mb_charge"); ?>>포인트</option>
    <option value="mb_datetime" <?php echo get_selected($sfl, "mb_datetime"); ?>>가입일시</option>
    <option value="mb_ip" <?php echo get_selected($sfl, "mb_ip"); ?>>IP</option>
  </select>
  <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
  <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
  <input type="submit" class="btn_submit" value="검색">

</form>

<form name="fimemberlist" id="fimemberlist" action="" onsubmit="return fimemberlist_submit(this);" method="post">
  <input type="hidden" name="sst" value="<?php echo $sst ?>">
  <input type="hidden" name="sod" value="<?php echo $sod ?>">
  <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
  <input type="hidden" name="stx" value="<?php echo $stx ?>">
  <input type="hidden" name="page" value="<?php echo $page ?>">
  <input type="hidden" name="token" value="">

  <div class="tbl_head01 tbl_wrap">
    <table>
      <caption><?php echo $g5['title']; ?> 목록</caption>
      <thead>
        <tr>
          <th scope="col">
            <label for="chkall" class="sound_only">인플루언서회원 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
          </th>
          <th scope="col"><?php echo subject_sort_link('mb_id') ?>아이디</a></th>
          <th scope="col"><?php echo subject_sort_link('mb_name') ?>이름</a></th>
          <th scope="col"><?php echo subject_sort_link('mb_nick') ?>닉네임</a></th>
          <th scope="col">연락처</a></th>
          <th scope="col">이메일</th>
          <th scope="col">코드</th>
          <th scope="col">상태</th>
          <th scope="col"><?php echo subject_sort_link('mb_datetime', '', 'desc') ?>가입일</a></th>
          <th scope="col"><?php echo subject_sort_link('mb_today_login', '', 'desc') ?>최종접속</a></th>
          <th scope="col">총 리뷰수</th>
          <th scope="col">인플루언서</th>
          <th scope="col">관리</th>
          <th scope="col">지정</th>
        </tr>

      </thead>
      <tbody>
        <?php
        for ($i = 0; $row = sql_fetch_array($result); $i++) {
          // 리뷰
          //$sql2 = " SELECT COUNT(id) AS cnt FROM {$g5['g5_shop_vendor_table']} a LEFT JOIN {$g5['g5_shop_item_use_table']} b ON a.inf_id = b.mb_id AND b.is_confirm = '1' WHERE a.vd_id = '{$row['mb_id']}';";
          $sql2 = "SET @sum_a = (SELECT COUNT(*) AS cnt FROM {$g5['g5_shop_vendor_table']} a LEFT JOIN {$g5['g5_shop_item_use_table']} b ON a.i_id = b.mb_id AND b.is_confirm = '1' WHERE a.v_id = '{$row['mb_id']}');
                  SET @sum_b = (SELECT COUNT(*) AS cnt FROM {$g5['g5_shop_item_use_table']} WHERE mb_id = '{$row['mb_id']}' and is_confirm = '1');
                  select @sum_a + @sum_b as cnt;";

          $row2 = sql_fetch($sql2);
          $review_count = $row2['cnt'];

         //인플루언서 수
          $sql3 = " select count(*) as cnt from {$g5['g5_shop_vendor_table']} where v_id = '{$row['mb_id']}'; ";
          $row3 = sql_fetch($sql3);
          $inf_count = $row3['cnt'];

          $leave_date = $row['mb_leave_date'] ? $row['mb_leave_date'] : date('Ymd', G5_SERVER_TIME);
          $intercept_date = $row['mb_intercept_date'] ? $row['mb_intercept_date'] : date('Ymd', G5_SERVER_TIME);

          $mb_nick = get_text($row['mb_nick']);

          $mb_id = $row['mb_id'];
          $leave_msg = '';
          $intercept_msg = '';
          $intercept_title = '';
          if ($row['mb_leave_date']) {
            $mb_id = $mb_id;
            $leave_msg = '<span class="mb_leave_msg">탈퇴함</span>';
          } elseif ($row['mb_intercept_date']) {
            $mb_id = $mb_id;
            $intercept_msg = '<span class="mb_intercept_msg">차단됨</span>';
            $intercept_title = '차단해제';
          }
          if ($intercept_title == '') {
            $intercept_title = '차단하기';
          }

          $bg = 'bg' . ($i % 2);
        ?>

          <tr class="<?php echo $bg; ?>">
            <td class="td_chk">
              <input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
              <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['mb_name']); ?> <?php echo $mb_nick ?>님</label>
              <input type="checkbox" name="chk[]" value="<?php echo $mb_id; ?>" id="chk_<?php echo $i ?>">
            </td>

            <td class="td_name sv_use mb_id">
              <?php echo $mb_id ?>
              <?php
              //소셜계정이 있다면
              if (function_exists('social_login_link_account')) {
                if ($my_social_accounts = social_login_link_account($row['mb_id'], false, 'get_data')) {
                  echo '<div class="member_social_provider sns-wrap-over sns-wrap-32">';
                  foreach ((array) $my_social_accounts as $account) {     //반복문
                    if (empty($account) || empty($account['provider'])) {
                      continue;
                    }

                    $provider = strtolower($account['provider']);
                    $provider_name = social_get_provider_service_name($provider);

                    echo '<span class="sns-icon sns-' . $provider . '" title="' . $provider_name . '">';
                    echo '<span class="ico"></span>';
                    echo '<span class="txt">' . $provider_name . '</span>';
                    echo '</span>';
                  }
                  echo '</div>';
                }
              }

              ?>
            </td>

            <td class="td_mbname mb_name"><?php echo get_text($row['mb_name']); ?></td>
            <td class="td_name sv_use mb_nick">
              <div><?php echo $mb_nick ?></div>
            </td>
            <td class="td_tel mb_hp"><?php echo get_text($row['mb_hp']); ?></td>
            <td class="td_mng_s mb_email"><?php echo get_text($row['mb_email']); ?></td>
            <td class="td_mng_s mb_inf_code"><?php echo get_text($row['mb_inf_code']); ?></td>
            <td class="td_mbstat td_mng_s mb_stat">
              <?php
              if ($leave_msg || $intercept_msg) {
                echo $leave_msg . ' ' . $intercept_msg;
              } else {
                echo "정상";
              }
              ?>
            </td>
            <td class="td_date td_mng_s mb_datetime"><?php echo substr($row['mb_datetime'], 2, 8); ?></td>
            <td class="td_date td_mng_s mb_today_login"><?php echo substr($row['mb_today_login'], 2, 8); ?></td>
            <td class="td_mng_s mb_review_count"><?php echo number_format($review_count) ?></td>
            <td class="td_mng_s mb_inf_count"><?php echo number_format($inf_count) ?></td>
            <td class="td_mng td_mng_s"><a href="#" class="btn_manage btn btn_03" data-mb_no="<?php echo $row['mb_no']; ?>" data-mb_id="<?php echo $row['mb_id']; ?>">관리</a></td>
            <td class="td_mng td_mng_s"><a href="#" class="btn_delete btn btn_03" data-mb_no="<?php echo $row['mb_no']; ?>" data-mb_id="<?php echo $row['mb_id']; ?>">해제</a></td>
          </tr>
        <?php
        }
        if ($i == 0) {
          echo "<tr><td colspan=\"" . $colspan . "\" class=\"empty_table\">자료가 없습니다.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="btn_fixed_top">
    <?php if ($is_admin == 'super') { ?>
    <a href="#" id="vendor_list_remove" class="btn btn_02">선택 지정해제</a>
    <a href="#" id="vendor_add" class="btn btn_01">벤더추가</a>
    <?php } ?>
  </div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?' . $qstr . '&amp;page='); ?>

<script>

  // add ajax influencer member jacknam
  $(function() {

    $(document).on("click", "#vendor_list_remove", function(e) {
      e.preventDefault();

      var _form = $("#fimemberlist"),
      _chks = _form.find("input[type=checkbox][name='chk[]']:checked");

      if (!_chks.length) {
        alert("지정 해제 하실 항목을 하나이상 선택하세요.");
        return false;
      }

      var result = confirm("선택된 벤더들을 지정 해제 하시겠습니까?");
      if (result) {
        var vendor_ids = [];
        _chks.each(function(e) {
          var _that = $(this);
          vendor_ids.push(_that.val().trim());
        });

        if (vendor_ids.length) {

          $.ajax({
            url: '/adm/ajax.member.php',
            type: 'post',
            data: {
              target: 'vendor_set',
              task: 'vendor_list_remove',
              vendor_ids: vendor_ids.join("|"),
            },
            dataType: 'json',
            success: function(result) {
              if (typeof(result.error) == undefined || result.error > 0) {
                if (typeof(result.msg) != undefined) {
                  alert(result.msg);
                }
                return false;
              }

              if (result.result) {
                location.reload();
              }

              //console.log(result);
            },
            error: function() {},
            complete: function() {
              $("#popup_section .modal_bg").trigger("click");
            }
          });

        }

        //console.log(vendor_ids);

      }
    });

    $(document).on("click", "#vendor_add", function(e) {
      e.preventDefault();

      var _modal_box = $("#popup_section .modal_bg .modal_box");
      _modal_box.empty().end();

      $('<div><button id="add_new_vendor" class="btn btn_02">신규 입력 추가</button>&nbsp;&nbsp;' +
        '<button class="select_vendor_influencer btn btn_01" data-task_target="add_vendor">기존 선택 추가</button></div>').appendTo(_modal_box);
      $("#popup_section .modal_bg").addClass("on");

      return false;
    });

    $(document).on("click", "#add_new_vendor", function(e) {
      e.preventDefault();
      location.href = "./influencer_member_form.php?back_to=influencer_vendor_list";
      return false;
    });

    $(document).on("click", "#popup_section button.set_vendor", function(e) {
      e.preventDefault();
      var _this = $(this);
      var _td = _this.closest("tr").find("td");
      var _length = _td.length;
      if (_length == 0) {
        return false;
      }
      var _th = _this.closest("table").find("th");
      var mb_no = _this.attr("data-mb_no");
      var mb_id = _this.attr("data-mb_id");
      //var mb_inf_code = _this.attr("data-mb_inf_code");
      //var mb_instar_link = _this.attr("data-mb_instar_link");

      _length -= 1;
      var _table = "<table><thead><th>항목</th><th>내용</th></thead><tbody>";
      _td.each(function(idx, item) {
        if (idx == _length) {
          return false;
        }
        var f = _th.eq(idx).text();
        var v = $(item).text();
        _table += "<tr><td>" + f + "</td><td>" + v + "</td></tr>";
      });
      _table += "</tbody></table>";

      var _confirm_box = $("<div class='confirm_btns'></div>");
      _confirm_box.append("<div class='msg'>이 인플루언서를 벤더로 지정하시겠습니까?</div>");

      $("<button class='btn btn_02'>취소</button>").on("click", function(e) {
        e.preventDefault();
        $(this).closest("div.modal_bg.modal_msg").trigger("click");
        return false;
      }).appendTo(_confirm_box);

      $("<button class='btn btn_01'>확인</button>").on("click", function(e) {
        e.preventDefault();

        var _that = $(this);
        var instar_link = _that.closest(".confirm_box").find("input.frm_input").val();
        if (instar_link == "" && mb_instar_link != "") {
          instar_link = mb_instar_link;
        }

        $.ajax({
          url: '/adm/ajax.member.php',
          type: 'post',
          data: {
            target: 'vendor_set',
            task: 'vendor_add',
            mb_no: mb_no,
            mb_id: mb_id,
            //mb_inf_code: mb_inf_code,
          },
          dataType: 'json',
          success: function(result) {
            if (typeof(result.error) == undefined || result.error > 0) {
              if (typeof(result.msg) != undefined) {
                alert(result.msg);
              }
              return false;
            }

            if (result.result) {
              location.reload();
            }

            //console.log(result);
          },
          error: function() {},
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
    });

    $(document).on("click", "#popup_section button.add_inf", function(e) {
      e.preventDefault();
      var _this = $(this);
      var _td = _this.closest("tr").find("td");
      var _length = _td.length;
      if (_length == 0) {
        return false;
      }
      var _th = _this.closest("table").find("th");
      //var mb_no = _this.attr("data-mb_no");
      var mb_id = _this.attr("data-mb_id");
      var vendor_id = _this.attr("data-vendor_id");
      if (!mb_id || !vendor_id) {
        return false;
      }

      _length -= 1;
      var _table = "<table><thead><th>항목</th><th>내용</th></thead><tbody>";
      _td.each(function(idx, item) {
        if (idx == _length) {
          return false;
        }
        var f = _th.eq(idx).text();
        var v = $(item).text();
        _table += "<tr><td>" + f + "</td><td>" + v + "</td></tr>";
      });
      _table += "</tbody></table>";

      var _confirm_box = $("<div class='confirm_btns'></div>");
      _confirm_box.append("<div class='msg'>이 인플루언서를 추가하시겠습니까?</div>");

      $("<button class='btn btn_02'>취소</button>").on("click", function(e) {
        e.preventDefault();
        $(this).closest("div.modal_bg.modal_msg").trigger("click");
        return false;
      }).appendTo(_confirm_box);

      $("<button class='btn btn_01'>확인</button>").on("click", function(e) {
        e.preventDefault();

        var _that = $(this);

        $.ajax({
          url: '/adm/ajax.member.php',
          type: 'post',
          data: {
            target: 'vendor_inf_set',
            task: 'inf_add',
            vendor_id: vendor_id,
            mb_id: mb_id,
          },
          dataType: 'json',
          success: function(result) {
            if (typeof(result.error) == undefined || result.error > 0) {
              if (typeof(result.msg) != undefined) {
                alert(result.msg);
              }
              return false;
            }

            if (result.result) {
              location.reload();
            }

            //console.log(result);
          },
          error: function() {},
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
    });

    $(document).on("click", ".select_vendor_influencer", function(e) {
      e.preventDefault();

      var _this = $(this),
      task_target = _this.attr("data-task_target"),
      mb_id = _this.attr("data-mb_id") ?? "";

      var _modal_box = $("#popup_section .modal_bg .modal_box");
      _modal_box.empty().end();

      var _div = $("<div />").append("<div class='result_box' />");

      var disp_rows = function(rows) {
        //console.log(rows);
        var _div2 = $("<div class='result_box tbl_head01 tbl_wrap' />");
        var _table = "<table><thead>";
        _table += "<tr><th>아이디</th><th>이름</th><th>닉네임</th><th>연락처</th><th>이메일</th><th>가입일</th><th>기존코드</th><th>관리</th></tr></thead><tbody>";
        for (var i = 0; i < rows.length; i++) {
          var v = rows[i];
          _table += "<tr><td>" + v.mb_id + "</td><td>" + v.mb_name + "</td><td>" + v.mb_nick + "</td><td>" + phone_format(v.mb_hp) + "</td><td>" + v.mb_email +
            "</td><td>" + v.mb_datetime + "</td><td>" + v.mb_inf_code + "</td><td>" +
            "<button class='" + (task_target == "add_inf" ? "add_inf" : "set_vendor") + " btn btn_03' data-mb_no='" + v.mb_no + "' data-mb_id='" + v.mb_id + "' data-mb_inf_code='" + v.mb_inf_code +
            "' data-mb_instar_link='" + v.mb_instar_link + "' data-vendor_id='" + mb_id + "'>" + (task_target == "add_inf" ? "추가" : "지정") + "</button></td></tr>";
        }
        _table += "</tbody></table>";
        _div2.append(_table).appendTo(_div);
      }

      $.ajax({
        url: '/adm/ajax.member.php',
        type: 'post',
        data: {
          target: 'vendor_inf_list',
          task: task_target,
          mb_id: mb_id ? mb_id : "",
        },
        dataType: 'json',
        success: function(result) {
          //console.log(result);
          if (typeof(result.rows) == undefined || typeof(result.error) == undefined || result.error > 0) {
            return false;
          }
          _div.find(".result_box").remove().end();
          if (result.length > 0) {
            disp_rows(result.rows);
          } else {
            $("<div class='result_box'><div class='guide'>검색 결과가 없습니다.</div></div>").appendTo(_div);
          }
        },
        error: function() {

        }
      });


      _div.appendTo(_modal_box);
      return false;
    });

    function get_info_table(_this, tb_type) {
      var _td = _this.closest("tr").find("td");
      var _length = _td.length;
      if (_length == 0) {
        return false;
      }

      var is_manage = tb_type == "manage";
      var _th = _this.closest("table").find("th");
      var mb_no = _this.attr("data-mb_no");
      var mb_id = _this.attr("data-mb_id");

      if (is_manage) {
        _length -= 9;//10;
      } else {
        _length -= 2;
      }

      var _table = "<table><thead><th>항목</th><th>내용</th></thead><tbody>";
      _td.each(function(idx, item) {
        //if (idx == 0) return true;
        //if (idx == _length) return false;
        var _that = $(item);
        if (_that.find("input[type=checkbox]").length) return true;

        var f = _th.eq(idx).text();
        var v = _that.text();

        if (is_manage && _that.hasClass("mb_inf_count")) {
          _table += "<tr><td>" + f + "</td><td class='inf_count'>" + v + "</td></tr>";
        } else {
          if (idx <= _length) {
            _table += "<tr><td>" + f + "</td><td>" + v + "</td></tr>";
          }
        }
      });

      if (is_manage) {
        var temp ="<div class='inf_sub_list' style='min-width: 800px; max-height: 300px; overflow-y: scroll;'><table class='tbl_head01 tbl_wrap' style='margin-bottom:0;'>";
        temp += "<thead><tr><th>아이디</th><th>이름</th><th>닉네임</th><th>연락처</th><th class='td_mng_s'>삭제</th></tr></thead>";
        temp += "<tbody class='manage_list'>";
        temp += "</tbody></table></div>";
        _table += "<tr><td>소속 인플루언서</td><td class='inf_list'>" + temp + "</td></tr>";
      }

      _table += "</tbody></table>";

      return _table;
    }


    $(document).on("click", "#fimemberlist .btn_manage", function(e) {
      e.preventDefault();

      var _this = $(this);

      var _table = get_info_table(_this, "manage");
      if (!_table) return false;

      var mb_no = _this.attr("data-mb_no");
      var mb_id = _this.attr("data-mb_id");
      var mb_inf_code = _this.attr("data-mb_inf_code");

      var _confirm_box = $("<div class='confirm_btns'></div>");

      $("<button class='btn btn_02'>닫기</button>").on("click", function(e) {
        e.preventDefault();
        $(this).closest("div.modal_bg.modal_msg").trigger("click");
        return false;
      }).appendTo(_confirm_box);

      _confirm_box.append("<button class='select_vendor_influencer btn btn_01' data-task_target='add_inf' data-mb_id='" + mb_id + "'>인플루언서 추가</button>");

      var _modal_box = $("<div class='modal_box confirm_box' />");
      _modal_box.append("<div class='tbl_head01 tbl_wrap'>" + _table + "</div>");
      _modal_box.append(_confirm_box);

      var _modal_bg = $("<div class='modal_bg modal_msg on' />");
      _modal_bg.append(_modal_box).appendTo($("#popup_section"));

      var disp_rows = function(rows) {
        var _list_box = _modal_box.find(".manage_list:first");
        if (!_list_box.length) return false;
        var _trs = "";

        for (var i = 0; i < rows.length; i++) {
          var v = rows[i];
          _trs += "<tr><td>" + v.mb_id + "</td><td>" + v.mb_name + "</td><td>" + v.mb_nick + "</td><td>" + (v.mb_hp ? phone_format(v.mb_hp) : "-")
          + "</td><td><button class='remove_inf btn btn_01' data-vendor_id='" + mb_id + "' data-mb_id='" + v.mb_id + "'>삭제</button></td></tr>";
        }
        _list_box.append(_trs);
      }

      $.ajax({
        url: '/adm/ajax.member.php',
        type: 'post',
        data: {
          target: 'vendor_inf_list',
          task: 'list_inf',
          mb_id: mb_id,
        },
        dataType: 'json',
        success: function(result) {
          if (typeof(result.error) == undefined || result.error > 0) {
            if (typeof(result.msg) != undefined) {
              alert(result.msg);
            }
            return false;
          }

          if (result.rows) {
            disp_rows(result.rows);
          }
        },
        error: function() {},
        complete: function() {
        }
      });
    });

    $(document).on("click", "#fimemberlist .btn_delete", function(e) {
      e.preventDefault();

      var _this = $(this);
      var _table = get_info_table(_this, "delete");
      if (!_table) return false;

      var _confirm_box = $("<div class='confirm_btns'></div>");
      _confirm_box.append("<div class='msg'>이 벤더를 인플루언서 벤더에서 지정해제 하시겠습니까?</div>");

      $("<button class='btn btn_02'>취소</button>").on("click", function(e) {
        e.preventDefault();
        $(this).closest("div.modal_bg.modal_msg").trigger("click");
        return false;
      }).appendTo(_confirm_box);

      $("<button class='btn btn_01'>확인</button>").on("click", function(e) {
        e.preventDefault();

      var mb_no = _this.attr("data-mb_no");
      var mb_id = _this.attr("data-mb_id");

        $.ajax({
          url: '/adm/ajax.member.php',
          type: 'post',
          data: {
            target: 'vendor_set',
            task: 'vendor_delete',
            mb_no: mb_no,
            mb_id: mb_id,
          },
          dataType: 'json',
          success: function(result) {
            if (typeof(result.error) == undefined || result.error > 0) {
              if (typeof(result.msg) != undefined) {
                alert(result.msg);
              }
              return false;
            }

            if (result.result) {
              location.reload();
            }

            //console.log(result);
          },
          error: function() {},
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
    });

    $(document).on("click", ".manage_list .remove_inf", function(e) {
      e.preventDefault();

      var _this = $(this);
      var _table = get_info_table(_this, "remove");
      if (!_table) return false;

      var _confirm_box = $("<div class='confirm_btns'></div>");
      _confirm_box.append("<div class='msg'>이 인플루언서를 벤더 관리 목록에서 삭제하시겠습니까?</div>");

      $("<button class='btn btn_02'>취소</button>").on("click", function(e) {
        e.preventDefault();
        $(this).closest("div.modal_bg.modal_msg").trigger("click");
        return false;
      }).appendTo(_confirm_box);

      $("<button class='btn btn_01'>확인</button>").on("click", function(e) {
        e.preventDefault();

        var vendor_id = _this.attr("data-vendor_id");
        var mb_id = _this.attr("data-mb_id");
        $.ajax({
          url: '/adm/ajax.member.php',
          type: 'post',
          data: {
            target: 'vendor_inf_set',
            task: 'inf_remove',
            vendor_id: vendor_id,
            mb_id: mb_id,
          },
          dataType: 'json',
          success: function(result) {
            if (typeof(result.error) == undefined || result.error > 0) {
              if (typeof(result.msg) != undefined) {
                alert(result.msg);
              }
              return false;
            }

            if (result.result) {
              location.reload();
            }

            //console.log(result);
          },
          error: function() {},
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
    });
  });
  // add ajax influencer member jacknam
</script>

<?php
require_once './admin.tail.php';
