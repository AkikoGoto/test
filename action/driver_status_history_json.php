<?php
/**
 * Driver StatusをJSONで返す;
 */
	
	//会社IDが来ているか
	if($_GET['company_id']){
		$company_id=sanitizeGet($_GET['company_id']);
	}
			
	//ドライバーIDが来ているか
	if($_GET['driver_id']){
		$driver_id=sanitizeGet($_GET['driver_id']);
	}
	
	if($_GET['time_from']){
		$time_from =sanitizeGet($_GET['time_from']);
	}
	if($_GET['time_to']){
		$time_to =sanitizeGet($_GET['time_to']);
	} 
	
	
	
	//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
	if(!driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id)) {
		header_remove();
		header("HTTP/1.1 401 Unauthorized");
		exit;
	}
	
	$driver_status = Driver::getStatusById($driver_id, null, $time_from, $time_to);
	$driver_names = Driver::getNameById($driver_id);
	$driver_name = $driver_names[0];
	$datas['driver_name'] = $driver_name['last_name'].' '.$driver_name['first_name'];
	$datas['points'] = $driver_status[0];
	
	if(count($driver_status) > 0 ) {
		$json = json_encode( $datas );
//		echo $json;
		header( 'Content-Type: text/javascript; charset=utf-8' );
		echo $json;
	} else {
		header("HTTP/1.1 404");
	}
