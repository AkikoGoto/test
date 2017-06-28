<?php
/**
 * 輸送ルートの削除
 * @author Yuji Hamada
 * @since 2017/4/20
 * @version 2.0
 */
 
$company_id=sanitizeGet($_GET['company_id']);
$transport_route_id = sanitizeGet($_GET['transport_route_id']);

if($u_id==$company_id){
	TransportRoute::delete($transport_route_id);
	header('Location:index.php?action=message_mobile&situation=deleteData');
}else{
	header("Location:index.php?action=message_mobile&situation=wrong_access");
}
