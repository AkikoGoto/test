<?php
/** 会社ごとのドライバーの位置をGoogleMapに表示する　iPhone ,PC
 * ver2.0から追加
 */

require 'action/utils/map_utils.php';

/**
 * 共通設定、セッションチェック読み込み　会社の管理者か、ドライバー本人か、サブグループマネージャーのみ閲覧可能
 */

//会社IDが来ているか
if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id']);
}else{
	//不正なアクセスです、というメッセージ					
	header("Location:index.php?action=message_mobile&situation=wrong_access");		
}

//空車のみフラグ
if(!empty($_GET['vacancy'])){
	$vacancy =sanitizeGet($_GET['vacancy']);
}else{
	$vacancy = null;
}

//営業所
if(!empty($_GET['branch_id'])){
	$branch_id = sanitizeGet($_GET['branch_id']);
}else{
	$branch_id = null;
}


	//会社のユーザーIDと、編集するIDがあっているか,営業所長かあるいはドライバー本人か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

try{
	
	//営業所の取得
	$branchList=Branch::getByCompanyId($company_id);
	
	//記録の取得
	if($is_branch_manager){
		
		$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);	
		$dataList = Driver::getDriverMapByBranch($company_id, $vacancy, $branch_id);
		
	}elseif(!empty($branch_id)){
		$dataList = Driver::getDriverMapByBranch($company_id, $vacancy, $branch_id);
	}else{
		$dataList = Driver::getDriverMap($company_id, $vacancy);
	}
	
	$workingStatus = Data::getStatusByCompanyId($company_id);
	
	if($dataList){
		
		$driver_locations = makeMarkerInformation($dataList, $company_id, $workingStatus);
		
	}


	//輸送ルート 
	$transport_routes = TransportRoute::selectCompanyRoutes($company_id);
	

	list($company_lat, $company_long, $hasNoCompanysLatLng) = getCompanyLatLng($company_id, $branch_id);
	
	
}catch(Exception $e){

	$message=$e->getMessage();
}

$transport_all_number_control_system_url_start = "<a href='http://54.65.195.110:8081/TransportDetail.aspx?carno=";
$transport_all_number_control_system_url_middle = "' target='_blank'>http://54.65.195.110:8081/TransportDetail.aspx?carno=";
$transport_all_number_control_system_url_end = "</a>";

$test_all_number_control_system_url_start = "<a href='http://54.65.195.110:8082/TransportDetail.aspx?carno=";
$test_all_number_control_system_url_middle = "' target='_blank'>http://54.65.195.110:8082/TransportDetail.aspx?carno=";
$test_all_number_control_system_url_end = "</a>";

list($data, $links)=make_page_link($dataList);
// echo '<pre>';
// var_dump($dataList);
// echo '</pre>';

$smarty->assign("links",$links['all']);
$smarty->assign("data",$data);
$smarty->assign("company_id",$company_id);
$smarty->assign("driver_locations",$driver_locations);
$smarty->assign("working_status", $workingStatus[0]);
$smarty->assign("transport_routes",$transport_routes);

$smarty->assign("transport_all_number_control_system_url_start",$transport_all_number_control_system_url_start);
$smarty->assign("transport_all_number_control_system_url_middle",$transport_all_number_control_system_url_middle);
$smarty->assign("transport_all_number_control_system_url_end", $transport_all_number_control_system_url_end);

$smarty->assign("test_all_number_control_system_url_start",$test_all_number_control_system_url_start);
$smarty->assign("test_all_number_control_system_url_middle",$test_all_number_control_system_url_middle);
$smarty->assign("test_all_number_control_system_url_end", $test_all_number_control_system_url_end);

//会社の座標
$smarty->assign("company_long",$company_long);
$smarty->assign("company_lat",$company_lat);
$smarty->assign("hasNoCompanysLatLng",$hasNoCompanysLatLng);

//営業所 営業所が2か所以上の時に営業所を表示
if(count($branchList) > 1){
	$smarty->assign("branch_list",$branchList);
}


$smarty->assign("js",'
<script src="'.MAPBOX_GL_JS.'" type="text/javascript"></script>');

$css = "<link rel=\"stylesheet\" href=\"".MAPBOX_GL_CSS."\" type=\"text/css\" media=\"screen\">";
$smarty->assign("additional_css", $css);



$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");


$smarty->assign("filename","getDriverMap.html");
$smarty->display('template.html');

?>