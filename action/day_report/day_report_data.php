<?php
/**
 * 日報用のデータ表示　編集など
 */

//ドライバーIDが来ているか
if($_GET['driver_id']){
	$driver_id=sanitizeGet($_GET['driver_id']);
}
//ドライバーIDが来ているか
if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id']);
}

	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $company_id);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $driver_id);
	
	}else{

		//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
		driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);
		
		//ドライバー本人がログインしている場合、会社が編集許可を子なっていなければエラー表示
		driver_editing_banned_check($driver_id,$session_driver_id,$company_id,$u_id);
	}

//作業ステータス
$workingStatus = Data::getStatusByCompanyId($company_id);
$smarty->assign("working_status", $workingStatus[0]);

//ドライバー名の取得
$driver_name=Driver::getNameById($driver_id);

$dataList = DayReport::getDayReportDatasByNew($driver_id);

	 					
list($data, $links)=make_page_link($dataList,30);

$smarty->assign("driver_name",$driver_name[0]);
$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("selected_total_time",$selected_total_time);

$smarty->assign("driver_id",$driver_id);
$smarty->assign("company_id",$company_id);

$smarty->assign("filename","day_report/day_report_data.html");
$smarty->display('template.html');

