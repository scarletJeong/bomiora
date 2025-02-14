<?php
/*******************************************************
https://www.mein-n.com/adm
meinn / hjsk1101







실리콘 한개.


select * from bomiora_member where mb_id = 'kakao_968f095d'
select * from bomiora_member_health_profiles where mb_id = 'kakao_968f095d'
select * from bomiora_member_social_profiles where mb_id = 'kakao_968f095d'


마스킹테이프, 화장실 2구 콘센트(투명), 1구 스위치, 실리콘(방수, 백색), 싼타페



https://bomiora.kr/shop/review_view.php?is_id=2036



마스킹테이프, 실리콘, 1구 스위치, 2구 콘센트, 문손잡이, 싼타페, 메지



제품이 삭제되면 소진내역을 알 수 없음
일단 주문내역을 확인해보자.

select *, (od_cart_coupon + od_coupon + od_send_coupon) as couponprice from bomiora_shop_order where od_id like '%34234%' order by od_id desc limit 0, 25
order 테이블에 들어왔다면 주문은 완료된걸로 됨

SELECT * FROM bomiora_shop_order a, bomiora_shop_cart b where a.od_id = b.od_id and a.od_time between '2023-09-27 00:00:00' and '2023-09-27 23:59:59' and b.ct_status = '입금' order by od_time asc, b.it_id, b.io_type, b.ct_id



SELECT * FROM bomiora_shop_order a, bomiora_shop_cart b where a.od_id = b.od_id and a.od_time between '2023-09-27 00:00:00' and '2023-09-27 23:59:59' and b.ct_status = '입금'

SELECT * FROM bomiora_shop_order a, bomiora_shop_cart b, bomiora_charge c where a.od_id = b.od_id and a.od_id = c.ch_it_id


select ch.*, mb.mb_name, mb.mb_nick, mb.mb_email, mb.mb_homepage, mb.mb_charge from bomiora_charge ch LEFT JOIN bomiora_member mb ON ch.mb_id = mb.mb_id where ch.ch_admin <> 1 and (ch.mb_id = 'influencer_71') order by ch_id desc limit 0, 15

select count(*) as cnt from bomiora_charge ch where ch.ch_admin <> 1 order by ch_id desc


mb_id : 인플루언서 아이디
ch_point : 차감금액
ch_content : 제품이름
ch_rel_id : 제품아이디
ch_rel_action : 구매자아이디-유니크
ch_admin : 0 은 차감 1은 충전
ch_it_name : 구매자이름
ch_it_id : 주문번호

1번
select * from bomiora_charge as ch, bomiora_shop_cart as ct where ch.ch_rel_id = ct.it_id and ch.ch_admin = 0


select ch.*, ct.it_id from bomiora_charge ch RIGHT JOIN bomiora_shop_cart ct ON ch.ch_rel_id = ct.it_id where ch.ch_admin <> 1 and (ch.mb_id = 'influencer_71') order by ch_id desc limit 0, 15


select ch.* from bomiora_charge ch where ch.ch_admin = 1 order by ch.ch_id desc


select * from bomiora_shop_item




select * from bomiora_charge as ch, bomiora_shop_cart as ct where ch.ch_rel_id = ct.it_id and ch.ch_it_id = ct.od_id and ch.ch_admin = 0







SELECT *, (select c.mb_id from bomiora_charge c where a.od_id = c.ch_it_id ) as ititid FROM bomiora_shop_order a, bomiora_shop_cart b where a.od_id = b.od_id and

select ch.* from bomiora_charge ch where ch.ch_admin <> 1 order by ch.ch_id desc


카트에도 주문번호가 여러개일수도 있다.


아이디 / 이름 / 닉네임 / 대표전화, 등록상품수, 잔액
*******************************************************/
?>