<?php
/**
 * 動画一覧を表示
 * @author Akiko Goto
 * @since 2015/4/13
 */

if($_GET['time_from_year']){
	//日付の各値を取得
	$time_from_year = sanitizeGet($_GET['time_from_year'],ENT_QUOTES);
	$time_from_month = sanitizeGet($_GET['time_from_month'],ENT_QUOTES);
	$time_from_day = sanitizeGet($_GET['time_from_day'],ENT_QUOTES);
	
	$time_to_year = sanitizeGet($_GET['time_to_year'],ENT_QUOTES);
	$time_to_month = sanitizeGet($_GET['time_to_month'],ENT_QUOTES);
	$time_to_day = sanitizeGet($_GET['time_to_day'],ENT_QUOTES);
	//between指定のためtime_to_dayを一日プラスする。time_to_dayが月末日より大きい場合は翌月１日にする
	if($time_to_day >= date('t',strtotime($time_to_year . sprintf("%02d",$time_to_month)))){
		$time_to_day = 1;
		$time_to_month = $time_to_month+1;
	}else{
		$time_to_day = $time_to_day+1;
	}
	
	$selected_start = $time_from_year.sprintf("%02d", $time_from_month) . sprintf("%02d", $time_from_day);
	$selected_end = $time_to_year.sprintf("%02d", $time_to_month) . sprintf("%02d", $time_to_day);
	
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
	$branchId = sanitizeGet($_GET['suddenBrakingBranchId'],ENT_QUOTES);
	//CSVに出力する情報の取得
	$informations = Information::suddenBrakingInformation($selected_start, $selected_end, $company_id, $branchId);
}
//CSV出力のデータが存在するか
if($informations){
	$stream = fopen('php://output', 'w');
	
	foreach($informations as $row){
		
		fputcsv($stream, $row);
	}
	$fileName = 'IFT0160_' . date(Ymd) . '.csv';
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . $fileName);
	
}else{
	if($_GET['company_id']){
		$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
	}
	
	if($_GET['branch_id']){
		$post_branch_id=sanitizeGet($_GET['branch_id'],ENT_QUOTES);
		$smarty->assign("branch_id",$post_branch_id);
	}
	
	if($_GET['time_from_year']){
		$smarty->assign("message","該当するCSVデータがありません。");
	}
	
	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	//営業所の取得
	$branchList=Branch::getByCompanyId($company_id);
	
	if(!empty($branch_manager_id)){
			
		$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);	
		
	}else{
		
		$branch_id = null;
	}


	try{
	
		$dataList = Movie::viewMovies($company_id,$post_branch_id);

	}catch(Exception $e){
	
		$message=$e->getMessage();
	
	}

	list($data, $links)=alarms_page_link($dataList);	
	
	$smarty->assign('dataList',$dataList);
	$smarty->assign("data",$data);
	if(count($branchList) > 1){
		$smarty->assign("branch_list",$branchList);
	}
	$smarty->assign("links",$links['all']);
	$smarty->assign("company_id",$company_id);
	$smarty->assign("filename","movie/view_movies.html");
	$smarty->display("template.html");
}
?>