<?php
/**
 * 輸送ルートドライバーの削除
 * @author Yuji Hamada
 * @since 2017/4/28
 * @version 2.0
 */
 
$company_id=sanitizeGet($_GET['company_id']);
$transport_route_drivers_id = sanitizeGet($_GET['transport_route_drivers_id']);
if($u_id==$company_id){
	TransportRouteDriver::delete($transport_route_drivers_id);
	header('Location:index.php?action=message_mobile&situation=deleteData');
}else{
	header("Location:index.php?action=message_mobile&situation=wrong_access");
}
