<?php
$sub_menu = "100200";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '상품등록';
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
</style>
<div>
    <ul class="guide_w">
        <li>
            <img src="<?php echo G5_IMG_URL?>/guide/guide2-1.png" alt="">
        </li>
        <li>
            <img src="<?php echo G5_IMG_URL?>/guide/guide2-2.png" alt="">
        </li>
        <li>
            <img src="<?php echo G5_IMG_URL?>/guide/guide2-3.png" alt="">
        </li>
        <li>
            <img src="<?php echo G5_IMG_URL?>/guide/guide2-4.png" alt="">
        </li>
        <li>
            <img src="<?php echo G5_IMG_URL?>/guide/guide2-5.png" alt="">
        </li>
        <li>
            <img src="<?php echo G5_IMG_URL?>/guide/guide2-6.png" alt="">
        </li>
        <li>
            <img src="<?php echo G5_IMG_URL?>/guide/guide2-7.png" alt="">
        </li>
        <li>
            <img src="<?php echo G5_IMG_URL?>/guide/guide2-8.png" alt="">
        </li>
        
    </ul>
</div>
<?php
$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
echo $pagelist;

include_once('./admin.tail.php');
?>






