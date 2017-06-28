<?php
/** ドライバーのステータスアップデート登録画面　iphone,PC
 * ver2.0から追加
 */
	 
	 // 共通設定、セッションチェック読み込み	 
	require_once('driver_check_session.php');
	
	//キャリアごとのGPS読み込み
	$gps_link_driver_status=get_gps_link_driver_status($carrier);	

	
	//AUのみリンクのプログラムをステータスごとに分ける
	$gps_link_vacancy="device:location?url=http://".SERVER_URL."au_driver_status_vacancy.php";		
	$gps_link_board="device:location?url=http://".SERVER_URL."au_driver_status_board.php";		
	$gps_link_driver_off="device:location?url=http://".SERVER_URL."au_driver_status_driver_off.php";		
	$gps_link_driver_reserved="device:location?url=http://".SERVER_URL."au_driver_status_driver_reserved.php";		
	$gps_link_driver_other="device:location?url=http://".SERVER_URL."au_driver_status_driver_other.php";		
	
	
	//セッションのドライバーID取得
	$session_driver_id=$_SESSION["driver_id"];
	$session_driver_company_id=$_SESSION["driver_company_id"];
	
	$smarty->assign("driver_id",$session_driver_id);
	$smarty->assign("company_id",$session_driver_company_id);
	$smarty->assign("gps_link",$gps_link_driver_status);		

	//AU用のプログラムアサイン
	$smarty->assign("gps_link_vacancy",$gps_link_vacancy);		
	$smarty->assign("gps_link_board",$gps_link_board);		
	$smarty->assign("gps_link_driver_off",$gps_link_driver_off);		
	$smarty->assign("gps_link_driver_reserved",$gps_link_driver_reserved);		
	$smarty->assign("gps_link_driver_other",$gps_link_driver_other);		
		
	
	$smarty->assign("page_title",DRIVER_STATUS_UPDATE);		
	$smarty->assign("filename","driverStatus.html");
	$smarty->display('template.html');
	

?>