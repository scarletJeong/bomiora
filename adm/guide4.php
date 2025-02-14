<?php
$sub_menu = "100400";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '발송상품설정';
include_once('./admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

?>
<style>
    .guide_w li{
        width:100%;
        border-bottom: solid 3px #333;
        padding-bottom:20px
    }
    .guide_w li > img{
        display:block;
        width:100%;
    }
    .guide_link{
        font-size:20px;
        font-weight:900;
    }
    .guide_link:hover{
        color:red;
    }
</style>
<div>
    <a class="guide_link"  href="https://sir.kr/manual/yc5/115"> 그누보드 주문내역 상태 변경 메뉴얼</a>
    <ul class="guide_w">
        <li>
            <img src="<?php echo G5_IMG_URL?>/guide/guide4-1.png" alt="">
        </li>
    </ul>
</div>
<?php
$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
echo $pagelist;

include_once('./admin.tail.php');
?>






