<?php
$sub_menu = "800100";
include_once('./_common.php');
auth_check($auth[$sub_menu], 'r');

$g5['title'] = '진료하기';
include_once('./admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

?>

<div>
    <form action="">
        <div class="diagnosis_top_bg">
            <div class="diagnosis_top">
                <div class="diagnosis_top_info">
                    <p>진료 예약 날짜</p>
                    <div class="diagnosis_top_info_date">
                        <input type="date">
                        <span>-</span>
                        <input type="date">
                    </div>
                </div>
                <div class="diagnosis_top_info">
                    <p>이름</p>
                    <div class="diagnosis_top_info_name">
                        <input type="text">
                    </div>
                </div>
                <div class="diagnosis_top_info">
                    <div class="diagnosis_top_info_2dep">
                        <p>성별</p>
                        <div>
                            <select name="" id="">
                                <option value="여자">여자</option>
                                <option value="남자">남자</option>
                            </select>
                        </div>
                    </div>
                    <div class="diagnosis_top_info_2dep">
                        <p>처방여부</p>
                        <div>
                            <select name="" id="">
                                <option value="대기">대기</option>
                                <option value="처방완료">처방완료</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="diagnosis_top_info_btn">
                    <button>오늘 날짜로 검색</button>
                    <button>검색</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div style="margin-top:50px;">
    <form action="">
        <div>
            <button class="check_btn">
                처방하기
            </button>
        </div>
        <div class="diagnosis_bottom">
            <ul class="diagnosis_bottom_title">
                <li></li>
                <li><p>이름/성별</p></li>
                <li><p>진료과목</p></li>
                <li><p>처방여부</p></li>
                <li><p>결제여부</p></li>
                <li><p>예약날짜</p></li>
                <li><p>진료완료여부</p></li>
                <li></li>
            </ul>
            <ul class="diagnosis_list">
                <!-- 상품하나 -->
                <li class="">
                    <div class="item">
                        <ul class="diagnosis_bottom_info">
                            <li>
                                <input type="checkbox">
                            </li>
                            <li class="diagnosis_bottom_info_2dep">
                                <div>
                                    <p>
                                        이름<span class="sex">(남자)</span>
                                    </p>
                                    <p>
                                        구매날짜
                                    </p>
                                    <p>
                                        주문번호
                                    </p>
                                </div>
                                <div>
                                    <p class="first_buy">초진</p>
                                </div>
                            </li>
                            <li><p>상품명</p></li>
                            <li>
                                <p class="wait_text">대기</p>
                            </li>
                            <li><p class="complete_text">완료</p></li>
                            <li>
                                <p>진료예약시간</p>
                            </li>
                            <li>
                                <p class="wait_text">진행중</p>
                            </li>
                            <li>
                                <button class="main_btn">상세보기</button>
                            </li>
                        </ul>
                        <div class="diagnosis_main">
                            <div >
                                <h3>요약</h3>
                                <div class="diagnosis_main_list">
                                    <span class="requisite">전화번호:01012345678</span>
                                    <span class="requisite">생년월일:031231</span>
                                    <span >성별:여자</span>
                                    <span >키:165cm</span>
                                    <span >몸무게:62cm</span>
                                    <span >목표감량체중:5kg</span>
                                    <span >다이어트예상기간:3일이내</span>
                                    <span >하루끼니:하루 1식</span>
                                    <span >식습관:야식 주3회이상, 카페인음료 1일3잔 이상</span>
                                    <span >자주 먹는 음식:한식, 양식, 중식</span>
                                    <span >운동습관:일주일 1회 이하</span>
                                    <span >질병:간질환,심혈관, 없음</span>
                                    <span >복용중인 약:갑상선약, 당뇨약, 정신과약, 없음</span>
                                </div>
                            </div>
                            <div class="diagnosis_address">
                                <h3>주소</h3>
                                <p>우편번호: 41929</p>
                                <p>
                                    기본주소: 대구 중구 달구벌대로 1995 ,1125동 2301호
                                </p>
                                <p>
                                    상세주소: 무슨동 대신센트러자이
                                </p>
                                <p class="bold">
                                    배송메모:112동 택배함에 넣고 비밀번호 설정해서 연락주시면 감사하겠습니다 :)
                                </p>
                            </div>
                        </div>
                    </div> 
                </li>
                 <!-- 상품하나 -->
                 <li class="">
                    <div class="item">
                        <ul class="diagnosis_bottom_info">
                            <li>
                                <input type="checkbox">
                            </li>
                            <li class="diagnosis_bottom_info_2dep">
                                <div>
                                    <p>
                                        홍길동<span class="sex">(남자)</span>
                                    </p>
                                    <p>
                                        2023.07.18
                                    </p>
                                    <p>
                                        20230718-10155678
                                    </p>
                                </div>
                                <div>
                                    <p class="second_buy">재진</p>
                                </div>
                            </li>
                            <li><p>보미 디톡스환 3일 체험분</p></li>
                            <li>
                                <p class="complete_text">완료</p>
                            </li>
                            <li><p class="complete_text">완료</p></li>
                            <li>
                                <p>2023.07.20</p>
                                <p>12:00-12:30</p>
                            </li>
                            <li>
                                <p class="complete_text">완료</p>
                            </li>
                            <li>
                                <button class="main_btn">상세보기</button>
                            </li>
                        </ul>
                        <div class="diagnosis_main">
                            <div >
                                <h3>요약</h3>
                                <div class="diagnosis_main_list">
                                    <span class="requisite">전화번호:01012345678</span>
                                    <span class="requisite">생년월일:031231</span>
                                    <span >성별:여자</span>
                                    <span >키:165cm</span>
                                    <span >몸무게:62cm</span>
                                    <span >목표감량체중:5kg</span>
                                    <span >다이어트예상기간:3일이내</span>
                                    <span >하루끼니:하루 1식</span>
                                    <span >식습관:야식 주3회이상, 카페인음료 1일3잔 이상</span>
                                    <span >자주 먹는 음식:한식, 양식, 중식</span>
                                    <span >운동습관:일주일 1회 이하</span>
                                    <span >질병:간질환,심혈관, 없음</span>
                                    <span >복용중인 약:갑상선약, 당뇨약, 정신과약, 없음</span>
                                </div>
                            </div>
                            <div class="diagnosis_address">
                                <h3>주소</h3>
                                <p>우편번호: 41929</p>
                                <p>
                                    기본주소: 대구 중구 달구벌대로 1995 ,1125동 2301호
                                </p>
                                <p>
                                    상세주소: 무슨동 대신센트러자이
                                </p>
                                <p class="bold">
                                    배송메모:112동 택배함에 넣고 비밀번호 설정해서 연락주시면 감사하겠습니다 :)
                                </p>
                            </div>
                        </div>
                    </div> 
                </li>
            </ul>   
        </div>
    </form>
</div>
<script>
        // $('.fade_btn').click(function(e){
        //     e.preventDefault();
        //     $('.diagnosis_main').slideToggle();
        // })
        $('.main_btn').click(function(e){
            e.preventDefault();
            console.log("눌림1");
            if($('.diagnosis_main').hasClass('diagnosis_on')){
                $('.diagnosis_main').removeClass('diagnosis_on');
                $('.diagnosis_main').addClass('diagnosis_hidden');
                console.log("눌림2");
            }else{
                $('.diagnosis_main').addClass('diagnosis_on');
                $('.diagnosis_main').removeClass('diagnosis_hidden');
                console.log("눌림3");
            }
            
        })

</script>
