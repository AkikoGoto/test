<?php
/**
 * ルートをダウンロードするアクション
*/


//ドライバーIDが来ているか
if($_GET['driver_id']){
	$driver_id=sanitizeGet($_GET['driver_id']);
}
//会社IDが来ているか
if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id']);
}
//会社IDが来ているか
if($_GET['transport_route_id']){
	$transport_route_id=sanitizeGet($_GET['transport_route_id']);
}

//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);

//記録の取得
$transportRoute = TransportRoute::selectRoute($transport_route_id);

if($transportRoute != NULL){

	$geo_json = $transportRoute['geo_json'];

	//出力する
	$name = $transportRoute['name'];
	$today = date('Y').'_'.date('m').'_'.date('d');
	$file = 'geojson'.'_'.$name.'_'.$today.'.geojson';

	header('Content-Disposition: attachment; filename="'. $file . '"');
	header('Content-Type: application/json;');

	echo $geo_json;

}else{

	$smarty->assign("message","該当するデータがありません。");

	$smarty->assign("driver_id",$driver_id);
	$smarty->assign("company_id",$company_id);

	$smarty->assign("filename","driver_record_map.html");

	$smarty->display('template.html');
}

