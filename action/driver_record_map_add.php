<?php
//ドライバー情報入力

//会社ID、ドライバーIDが来ているか
if($_GET['driver_id']){	
	$driver_id=sanitizeGet($_GET['driver_id'],ENT_QUOTES, 'Shift_JIS');
	}
	
if($_GET['company_id']){	
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES, 'Shift_JIS');
	}

if ($_GET['id']){
	$id=sanitizeGet($_GET['id'],ENT_QUOTES, 'Shift_JIS');
}

	//会社のユーザーIDと、編集するIDがあっているか確認
	driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);	

	//ドライバー本人がログインしている場合、会社が編集許可を子なっていなければエラー表示
	driver_editing_banned_check($driver_id,$session_driver_id,$company_id,$u_id);
	
	//作業ステータス
	$workingStatus = Data::getStatusByCompanyId($company_id);
	
//セッションのid判定
try{
//	if(!empty($_SESSION['driver_record_map_id'])){
			$smarty->assign("working_status", $workingStatus[0]);
			//セッション
			$smarty->assign('driver_record_add_status_session',$_SESSION['driver_record_add_status_session']);
			$smarty->assign('driver_record_add_address_session',$_SESSION['driver_record_add_address_session']);
			$smarty->assign('driver_record_add_speed_session',$_SESSION['driver_record_add_speed_session']);
			$smarty->assign('driver_record_add_sales_session',$_SESSION['driver_record_add_sales_session']);
			$smarty->assign('driver_record_add_detail_session',$_SESSION['driver_record_add_detail_session']);
			//セッション（日付、開始）
			$smarty->assign('driver_record_map_from_year_session',$_SESSION['driver_record_map_from_year']);
			$smarty->assign('driver_record_map_from_month_session',$_SESSION['driver_record_map_from_month']);
			$smarty->assign('driver_record_map_from_day_session',$_SESSION['driver_record_map_from_day']);
			$smarty->assign('driver_record_map_from_hour_session',$_SESSION['driver_record_map_from_hour']);
			$smarty->assign('driver_record_map_from_minit_session',$_SESSION['driver_record_map_from_minit']);
/*			$smarty->assign('driver_record_map_from_seconds_session',$_SESSION['driver_record_map_from_seconds']);*/
//		}
	}
catch(Exception $e){
		
			$message=$e->getMessage();

	}

$smarty->assign("filename","driver_record_map_add.html");
$smarty->assign("driver_id",$driver_id);
$smarty->assign("company_id",$company_id);
$smarty->display("template.html");

?>