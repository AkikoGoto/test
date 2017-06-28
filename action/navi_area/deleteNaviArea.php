<?php
/**
 * ナビエリアの削除
 * @author Yuji Hamada
 * @since 2017/4/20
 * @version 2.0
 */
 
$company_id=sanitizeGet($_GET['company_id']);
$navi_area_id = sanitizeGet($_GET['navi_area_id']);
$transport_route_id = sanitizeGet($_GET['transport_route_id']);
$driver_id = sanitizeGet($_GET['driver_id']);
$date = sanitizeGet($_GET['date']);

if($u_id==$company_id){
	NaviArea::delete($navi_area_id);
	header("Location:index.php?action=message_mobile&situation=after_delete_navi_area&company_id=$company_id&transport_route_id=$transport_route_id&driver_id=$driver_id&date=$date");
}else{
	header("Location:index.php?action=message_mobile&situation=wrong_access");
}
