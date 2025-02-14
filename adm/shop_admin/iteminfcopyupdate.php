<?php
$sub_menu = '400300';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "w");

//check_admin_token();

//$ca_id		= isset($_POST['ca_id']) ? preg_replace('/[^0-9a-z]/i', '', $_POST['ca_id']) : '';
$ca_id			= 40;// 이걸로 하던 it_org_id 값을 가지고 판단하자.
$it_id			= isset($_POST['it_id']) ? safe_replace_regex($_POST['it_id'], 'it_id') : '';
$it_price		= isset($_POST['it_price']) ? (int) $_POST['it_price'] : '';
$it_inf_price	= isset($_POST['it_inf_price']) ? (int) $_POST['it_inf_price'] : 0;
$it_nocoupon	= isset($_POST['it_nocoupon']) ? (int) $_POST['it_nocoupon'] : '';

if ($is_admin != "super")
    alert("최고관리자만 접근 가능합니다.");

if (!trim($it_id))
	alert("복사할 상품코드가 없습니다.");

if (!$it_price)
	alert("판매금액을 입력해주세요.");

$it_mb_inf_cnt = (isset($_POST['it_mb_inf']) && is_array($_POST['it_mb_inf'])) ? count($_POST['it_mb_inf']) : 0;// 인플루언서
if($it_mb_inf_cnt == 0) {
	alert('인플루언서를 선택해 주세요.');
}

$sql = " select * from {$g5['g5_shop_item_table']} where it_id = '$it_id' limit 1 ";
$cp = sql_fetch($sql);

// 상품테이블의 필드가 추가되어도 수정하지 않도록 필드명을 추출하여 insert 퀴리를 생성한다. (상품코드만 새로운것으로 대체)
$sql_common = "";
$fields = sql_field_names($g5['g5_shop_item_table']);
foreach($fields as $fld) {
	if ($fld == 'it_id' || $fld == 'ca_id' || $fld == 'it_sum_qty' || $fld == 'it_use_cnt' || $fld == 'it_use_avg' || $fld == 'it_price' || $fld == 'it_inf_price' || $fld == 'it_mb_inf' || $fld == 'it_nocoupon' || $fld == 'it_org_id')
		continue;
	$sql_common .= " , $fld = '".addslashes($cp[$fld])."' ";
}

$org_it_id = time() + 1000;

// 인플루언서 분야 배열 삽입
for($j=0; $j<$it_mb_inf_cnt; $j++) {
	$post_inf_id = isset($_POST['it_mb_inf'][$j]) ? preg_replace(G5_OPTION_ID_FILTER, '', strip_tags($_POST['it_mb_inf'][$j])) : '';
	//echo get_uniqid()."<br>"; 
	//echo $post_inf_id."<br>";
	$new_it_id = $org_it_id + $j;
	echo $new_it_id."<br>";
	
	if(isset($new_it_id) && preg_match('/[^A-Za-z0-9\-_]+/', $new_it_id))
		alert("제품코드는 영문자, 숫자, -, _ 만 사용할 수 있습니다.");
	
	$row = sql_fetch(" select count(*) as cnt from {$g5['g5_shop_item_table']} where it_id = '$new_it_id' ");
	if (isset($row['cnt']) && $row['cnt'])
		alert('이미 존재하는 제품코드 입니다.');
	








	$sql = " insert {$g5['g5_shop_item_table']}
				set it_id = '$new_it_id',
					ca_id = '$ca_id',
					it_price = '$it_price',
					it_mb_inf = '$post_inf_id',
					it_inf_price = '$it_inf_price',
					it_nocoupon = '$it_nocoupon',
					it_org_id = '$it_id'
					$sql_common ";
	echo $sql."<br>";
	sql_query($sql);



	// 선택/추가 옵션 copy
	$opt_sql = " insert ignore into {$g5['g5_shop_item_option_table']} ( io_id, io_type, it_id, io_price, io_stock_qty, io_noti_qty, io_use )
					select io_id, io_type, '$new_it_id', io_price, io_stock_qty, io_noti_qty, io_use
					from {$g5['g5_shop_item_option_table']}
					where it_id = '$it_id'
					order by io_no asc ";
	sql_query($opt_sql);


	// html 에디터로 첨부된 이미지 파일 복사
	if($cp['it_explan']) {
		$matchs = get_editor_image($cp['it_explan'], false);
		$count_matchs = (isset($matchs[1]) && is_array($matchs[1])) ? count($matchs[1]) : 0;
		
		// 파일의 경로를 얻어 복사
		for($i=0;$i<$count_matchs;$i++) {
			$p = parse_url($matchs[1][$i]);
			if(strpos($p['path'], "/data/") != 0)
				$src_path = preg_replace("/^\/.*\/data/", "/data", $p['path']);
			else
				$src_path = $p['path'];
			
			$srcfile = G5_PATH.$src_path;
			$dstfile = preg_replace("/\.([^\.]+)$/", "_".$new_it_id.".\\1", $srcfile);
			
			if(is_file($srcfile)) {
				copy($srcfile, $dstfile);
				
				$newfile = preg_replace("/\.([^\.]+)$/", "_".$new_it_id.".\\1", $matchs[1][$i]);
				$cp['it_explan'] = str_replace($matchs[1][$i], $newfile, $cp['it_explan']);
			}
		}
		
		$sql = " update {$g5['g5_shop_item_table']} set it_type1=0, it_type3=0, it_type4=0, it_explan = '".addslashes($cp['it_explan'])."' where it_id = '$new_it_id' ";
		sql_query($sql);
	}

	/*
	if($cp['it_mobile_explan']) {
		$matchs = get_editor_image($cp['it_mobile_explan'], false);
		$count_matchs = (isset($matchs[1]) && is_array($matchs[1])) ? count($matchs[1]) : 0;
		
		// 파일의 경로를 얻어 복사
		for($i=0;$i<$count_matchs;$i++) {
			$p = parse_url($matchs[1][$i]);
			if(strpos($p['path'], "/data/") != 0)
				$src_path = preg_replace("/^\/.*\/data/", "/data", $p['path']);
			else
				$src_path = $p['path'];
			
			$srcfile = G5_PATH.$src_path;
			$dstfile = preg_replace("/\.([^\.]+)$/", "_".$new_it_id.".\\1", $srcfile);
			
			if(is_file($srcfile)) {
				copy($srcfile, $dstfile);
				
				$newfile = preg_replace("/\.([^\.]+)$/", "_".$new_it_id.".\\1", $matchs[1][$i]);
				$cp['it_mobile_explan'] = str_replace($matchs[1][$i], $newfile, $cp['it_mobile_explan']);
			}
		}
		
		$sql = " update {$g5['g5_shop_item_table']} set it_mobile_explan = '".addslashes($cp['it_mobile_explan'])."' where it_id = '$new_it_id' ";
		sql_query($sql);
	}
	*/

	// 파일복사
	$dest_path = G5_DATA_PATH.'/item/'.$new_it_id;
	@mkdir($dest_path, G5_DIR_PERMISSION);
	@chmod($dest_path, G5_DIR_PERMISSION);
	$comma = '';
	$sql_img = '';
	
	for($i=1; $i<=10; $i++) {
		$file = G5_DATA_PATH.'/item/'.$cp['it_img'.$i];
		$new_img = '';
		
		if(is_file($file)) {
			$dstfile = $dest_path.'/'.basename($file);
			copy($file, $dstfile);
			@chmod($dstfile, G5_FILE_PERMISSION);
			$new_img = $new_it_id.'/'.basename($file);
		}
		
		$sql_img .= $comma." it_img{$i} = '$new_img' ";
		$comma = ',';
	}
	
	$sql = " update {$g5['g5_shop_item_table']}
				set $sql_img
				where it_id = '$new_it_id' ";
	sql_query($sql);

	// 배너복사
	$dest_path_ban = G5_DATA_PATH.'/banner_item/'.$new_it_id;
	@mkdir($dest_path_ban, G5_DIR_PERMISSION);
	@chmod($dest_path_ban, G5_DIR_PERMISSION);
	
	$pc_file = G5_DATA_PATH.'/banner_item/'.'pc_'.$it_id;
	
	if(is_file($pc_file)) {
		$dstfile_ban = $dest_path_ban.'/'.basename($pc_file);
		copy($pc_file, $dstfile_ban);
		@chmod($dstfile_ban, G5_FILE_PERMISSION);
	}

	$mo_file = G5_DATA_PATH.'/banner_item/'.'mo_'.$it_id;
	
	if(is_file($mo_file)) {
		$dstfile_ban = $dest_path_ban.'/'.basename($mo_file);
		copy($mo_file, $dstfile_ban);
		@chmod($dstfile_ban, G5_FILE_PERMISSION);
	}

}

// 상품이미지 복사
function copy_directory($src_dir, $dest_dir)
{
    if($src_dir == $dest_dir)
        return false;

    if(!is_dir($src_dir))
        return false;

    if(!is_dir($dest_dir)) {
        @mkdir($dest_dir, G5_DIR_PERMISSION);
        @chmod($dest_dir, G5_DIR_PERMISSION);
    }

    $dir = opendir($src_dir);
    while (false !== ($filename = readdir($dir))) {
        if($filename == "." || $filename == "..")
            continue;

        $files[] = $filename;
    }

    for($i=0; $i<count($files); $i++) {
        $src_file = $src_dir.'/'.$files[$i];
        $dest_file = $dest_dir.'/'.$files[$i];
        if(is_file($src_file)) {
            copy($src_file, $dest_file);
            @chmod($dest_file, G5_FILE_PERMISSION);
        }
    }
}

//$qstr = "ca_id=$ca_id&amp;sfl=$sfl&amp;sca=$sca&amp;page=$page&amp;stx=".urlencode($stx);
//goto_url("itemlist.php?$qstr");

        echo '
        <!doctype html>
        <html lang="ko">
        <head>
        <meta charset="utf-8">
        <title>인플루언서 분배완료</title>
        <body>
        <script>
        alert("정상적으로 분배가 완료되었습니다.");
		opener.parent.location.href = "influencer_itemlist.php";
		self.close();
        </script>
        </body>
        </html>';

?>