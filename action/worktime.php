<?php
/** 個別ドライバーの業務日誌確認画面　iphone,PC
 * 　MAPでの表示
 * ver2.1から追加
 */

/**
 * 共通設定、セッションチェック読み込み　会社の管理者か、ドライバー本人のみ閲覧可能
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
	
	}
	
	//ドライバー名の取得
	$driver_name=Driver::getNameById($driver_id);	

	//作業ステータス
	$workingStatus = Data::getStatusByCompanyId($company_id);
	$smarty->assign("working_status", $workingStatus[0]);
		
	//ドライバー自身の編集許可ステータス
	$is_ban_editing = Data::isBannedDriverEditing($company_id, $u_id);
	
	//作業履歴の取得
	$dataList = Work::getWorkRecordsByNew($driver_id);
	
	//作業時間の合計　たくると違うので注意
	$this_month_work_time = Work::getWorktimeByDate($driver_id, "all_select", date('Y'), date('m'), 1, 
		 					date('Y'), date('m'), date('t'));

	//業務時間の合計を計算
	$selected_total_time = 0;
	foreach($this_month_work_time as $each_data){
		if ( $each_data['total_time'] != "00:00:00" ) {
			$each_data_seconds = h2s($each_data['total_time']);
			$selected_total_time = $selected_total_time + $each_data_seconds;
		}
	}
	
	if ($selected_total_time > 0) {
		//秒数を時：分：秒に変換
		$selected_total_time = s2h($selected_total_time);
	}
	 					
	if ($selected_total_time == '-838:59:59') {
		$selected_total_time = '00:00:00';
	}
	
	
	
	list($data, $links)=make_page_link($dataList);

	$smarty->assign("driver_name",$driver_name[0]);
	$smarty->assign('dataList',$dataList);
	$smarty->assign("data",$data);
	$smarty->assign("links",$links['all']);
	$smarty->assign('is_ban_editing',$is_ban_editing);
	$smarty->assign("selected_total_time",$selected_total_time);
	
	$smarty->assign("driver_id",$driver_id);
	$smarty->assign("company_id",$company_id);
	
	$smarty->assign("filename","worktime.html");
	$smarty->display('template.html');

?>