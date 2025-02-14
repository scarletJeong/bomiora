<?php
$sub_menu = "100700";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '추가 개발사항';
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
    .w-800{
        max-width:800px;
        width:100%;
        margin: 0 auto;
    }
    .w-800 img{
        width:100%;
        display:block;
        mar
    }
    .guide_text{
        
    text-align: center;
    line-height: 40px;
    font-size: 20px;
    font-weight: 600;

    }
    .guide_text_bg{
        background: #ccc;
        padding: 20px;
    }
</style>
<div>
    <ul class="guide_w">
        <li>
            <p class="guide_text">1.진료하기에 메모 추가</p>
            <div class="w-800">
                <img src="<?php echo G5_IMG_URL?>/guide/guide7-1.png" alt="">
                <img src="<?php echo G5_IMG_URL?>/guide/guide7-2.png" alt="">
            </div>
        </li>
        <li>
            <p class="guide_text">2.인플루언서 정산</p>
            <div class="guide_text_bg">
                <p class="guide_text">
                    - 주문내역에서 상품코드 검색할수 있는 기능<br>
                    - 검색 후 총합 금액과 내역을 엑셀로 다운 가능하도록?<br>
                    정확한 내용 정리 필요
                </p>
            </div>
        </li>
        <li>
            <p class="guide_text">3.카톡 로그인 관리자 변경</p>
           <div class="guide_text_bg">
                <p class="guide_text">
                    키값 변경시 기존 회원들 로그인시 이미 사용중인 이메일이라고 나오면서 로그인이 안됨.<br>
                    이게 해결된다면 카카오로그인시 개인정보 작성페이지 작업 추가 할 필요 없음.
                </p>
           </div>  
        </li>
        <li>
            <p class="guide_text">4.리뷰 통합</p>
           <div class="guide_text_bg">
                <p class="guide_text">다이어트환과 다이어트체험하기 리뷰 통합<br>디톡스환과 디톡스 체험하기 리뷰 통합</p>                
           </div>  
        </li>
        <li>
            <p class="guide_text">5.리뷰 view페이지</p>
           <div class="guide_text_bg">
                <p class="guide_text">리뷰 view 페이지 하단에 전체리뷰 리스트 추가</p>
                <div class="w-800">
                    <img src="<?php echo G5_IMG_URL?>/guide/guide7-3.png" alt="">
                </div>
           </div>  
        </li>
        <li>
            <p class="guide_text">6.배송시 고객에게 문자 발송 운송추적 url 보내기</p>
           <div class="guide_text_bg">
                <p class="guide_text">
                - 배송조회 준비에서 배송으로 변경시 운송번호 입력 후 배송으로 변경 시 문자 발송<br>

                - 문자 내용에 운송장 번호와 추적할수 있는 url 넣어주기 or 자동으로 배송조회 url 생성해서 주기<br>

                - (https://trace.cjlogistics.com/web/info.jsp?slipno=657704099524)운송장 번호<br>

                - https://www.cjlogistics.com/ko/tool/parcel/tracking#none (배송 추적할수 있는 url)<br>

                - 가이드에서 발송상품설정 메뉴얼 참고<br>
                정확한 내용 정리 필요
                    
                </p>
           </div>  
        </li>
        <li>
            <p class="guide_text">7.미완료주문내역</p>
           <div class="guide_text_bg">
                <p class="guide_text">
                미완료 주문내역에 있는 사람들에게 알림톡이나 문자 보내기
                    
                </p>
           </div>  
        </li>
        <li>
            <p class="guide_text">8.알림톡</p>
           <div class="guide_text_bg">
                <p class="guide_text">
                문자에서 알림톡으로 변경하기
                    
                </p>
           </div>  
        </li>
        <li>
            <p class="guide_text">9.주문내역 주문상태</p>
           <div class="guide_text_bg">
                <p class="guide_text">
                배송으로 변경 후 5일 후 자동으로 완료로 되게?
                    
                </p>
           </div>  
        </li>
    </ul>
</div>
<?php
$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
echo $pagelist;

include_once('./admin.tail.php');
?>






