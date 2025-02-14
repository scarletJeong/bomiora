<?php
$sub_menu = '600300';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check_menu($auth, $sub_menu, "w");

function get_editor_content($target, $tr = false) {
  if ($tr && $tr[$target]) {    
    //return base64_decode($tr[$target]);
    return html_entity_decode(htmlspecialchars_decode($tr[$target]));
  }
  
  $content = '';
  switch ($target) {
    case 'keyword':
    $content = '
<div style="line-height:2; margin-bottom:1em;"> <small style="color:#FF996A; font-weight:bold;">제목 키워드</small>
  <div style="display: grid; grid-template-columns: 1fr auto;">
    <p>예시) 다이어트, 디톡스, 한약, 비대면진료, 보미오라</p>    
  </div>
</div>
<div style="line-height:2; margin-bottom:1em;"> <small style="color:#FF996A; font-weight:bold;">본문 키워드</small>
  <div style="display: grid; grid-template-columns: 1fr auto;">
    <p>예시) 다이어트, 디톡스, 한약, 비대면진료, 보미오라</p>    
  </div>
</div>
<div>
  <div>
    <ul>
      <li>안내드린 제목 키워드 중 2개 이상을 선택하여 제목을 포함한 리뷰와 #태그에 꼭 넣어주세요.</li>
      <li>본문 키워드 중 3개 이상을 선택하여 리뷰 내 최소 5회 이상 언급해 주세요.</li>
      <li>단어 및 띄어쓰기를 정확히 작성하지 않을 시 수정 요청이 있을 수 있습니다.</li>
    </ul>
  </div>
</div>';    
    break;
    case 'mission':
    $content = '
<div style="line-height:2; margin-bottom:1em;"> <small style="color:#FF996A; font-weight:bold;">키워드</small>
  <div> 상단 [검색키워드] 내 내용 참고 바랍니다. </div>
</div>
<div style="line-height:2; margin-bottom:1em;"> <small style="color:#FF996A; font-weight:bold;">글자수</small>
  <div> 콘텐츠 작성 시 글자 수 최소 1,200자 이상<br>(공백 미포함) </div>
</div>
<div style="line-height:2; margin-bottom:1em;"> <small style="color:#FF996A; font-weight:bold;">이미지</small>
  <div>
    <ul>
      <li>제품 사용 연출 촬영컷</li>
      <li>제품 디테일 촬영컷</li>
      <li>사진은 반드시 최소 15장 이상 넣어주세요. </li>
    </ul>
  </div>
</div>
<div style="line-height:2; margin-bottom:1em;"> <small style="color:#FF996A; font-weight:bold;">영상</small>
  <div>
    <ul>
      <li>제품 사용 연출 (GIF)컷</li>
    </ul>
  </div>
</div>
<div style="line-height:2; margin-bottom:1em;"> <small style="color:#FF996A; font-weight:bold;">필수 언급 내용</small>
  <div>
    <ul>
      <li>예시) 부작용 걱정없는 한방 제품임을 강조</li>
      <li>예시) 요요 없이 체중을 유지하는데 도움이 점을 강조</li>
      <li>예시) 식욕 억제를 잘해주고 휴대성이 매우 좋은 점을 강조</li>
    </ul>
  </div>
</div>';    
    break;
    case 'guide':
    $content = '
<div style="line-height:2; margin-bottom:1em;"> <small style="color:#FF996A; font-weight:bold;">제공형 체험단 구매 안내</small>
  <div>
    <ul>
      <li>별도의 구매 과정 없이 제품이 제공되는 [제공형] 체험단입니다.</li>
    </ul>
  </div>
</div>
<div style="line-height:2; margin-bottom:1em;"> <small style="color:#FF996A; font-weight:bold;">제공형 체험단 유의사항</small>
  <div>
    <ul>
      <li>반드시 기간 내 작성한 리뷰의 링크를 제출해 주셔야 하며, 기간을 초과할 경우 비용이 청구됩니다.</li>
      <li>단순변심/실수로 제품이 손상되어 제품 회수가 불가능한 경우, 제품 비용이 청구될 수 있습니다.</li>
      <li>당첨 후 취소하는 경우 패널티가 적용됩니다.</li>
    </ul>
  </div>
</div>
<div style="line-height:2; margin-bottom:1em;"> <small style="color:#FF996A; font-weight:bold;">공통 유의사항</small>
  <div>
    <ul>
      <li>체험단에 선정될 시 체험단별 상세 내용을 꼼꼼히 확인 후 리뷰 작성 부탁드립니다.</li>
      <li>체험단별 기재된 기한을 지켜주시길 바라며, 지키지 않을 시 패널티가 부여될 수 있습니다.</li>
      <li>당첨된 상품 1개당 피드 및 포스팅 1개 기준으로 작성되어야 하며, 타제품과 함께 업로드시 재작성 및 수정요청 드릴 수 있습니다.</li>
      <li>체험단 미션이 지켜지지 않을 시 수정 요청이 있을 수 있습니다.</li>
      <li>부득이하게 체험단 참여가 어려운 경우, 미리 채널톡으로 문의 바랍니다. </li>
    </ul>
  </div>
</div>';
    break;
  }
  
  return trim($content);  
}

function get_tester_status($row) {
  //var_dump(strtotime('now'));
  //var_dump(strtotime($row['to_date']));
  //exit;

  if ($row['is_confirm'] == 'y') {
    if (strtotime('now') > strtotime($row['to_date'])) {
      $status = '기간만료';      
    } else {
      if ($row['selected'] >= $row['quota']) {
        $status = '선정완료';
      } else if ($row['applied'] >= $row['quota']) {
        $status = '지원마감';
      } else {
        $status = '<span style="color: #ff4081;">모집중</span>';
      }
    }
  } else {
    $status = '중단';
  }

  return $status;  
}
?>