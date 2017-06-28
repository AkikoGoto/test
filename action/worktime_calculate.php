<?php
//ドライバーIDが来ているか
if($_POST['driver_id']){
	$driver_id = htmlentities($_POST["driver_id"], ENT_QUOTES, mb_internal_encoding());
}
//ドライバーIDが来ているか
if($_POST['company_id']){
	$company_id = htmlentities($_POST["company_id"], ENT_QUOTES, mb_internal_encoding());
}
	

	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $company_id);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $driver_id);
	
	}else{
	
		//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認

		driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);
	}

	$status = htmlentities($_POST['status'], ENT_QUOTES, mb_internal_encoding());
    
    $time_from_year = htmlentities($_POST["time_from_year"], ENT_QUOTES, mb_internal_encoding());
    $time_from_month = htmlentities($_POST["time_from_month"], ENT_QUOTES, mb_internal_encoding());
    $time_from_day = htmlentities($_POST["time_from_day"], ENT_QUOTES, mb_internal_encoding());
    
    $time_to_year = htmlentities($_POST["time_to_year"], ENT_QUOTES, mb_internal_encoding());
    $time_to_month = htmlentities($_POST["time_to_month"], ENT_QUOTES, mb_internal_encoding());
    $time_to_day = htmlentities($_POST["time_to_day"], ENT_QUOTES, mb_internal_encoding());
    
    //記録の取得
	$dataList=Work::getWorktimeByDate($driver_id, $status, $time_from_year, $time_from_month, $time_from_day, 
	 					$time_to_year, $time_to_month, $time_to_day);
    
	//業務時間の合計を計算
	$selected_total_time = 0;					
	foreach($dataList as $each_data){
		
		$each_data_seconds = h2s($each_data['total_time']);
		$selected_total_time = $selected_total_time + $each_data_seconds;
	}

	//秒数を時：分：秒に変換
	$selected_total_time = s2h($selected_total_time);

list($data, $links)=make_page_link($dataList,30);

//作業ステータス
$workingStatus = Data::getStatusByCompanyId($company_id);
$smarty->assign("working_status", $workingStatus[0]);

$smarty->assign("status",$status);
$smarty->assign("links",$links['all']);
$smarty->assign("data",$data);
$smarty->assign("selected_total_time",$selected_total_time);

$smarty->assign("driver_id",$driver_id);
$smarty->assign("company_id",$company_id);

$smarty->assign("filename","worktime_calculate.html");
$smarty->display('template.html');

?>