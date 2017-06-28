<?php
/** 
 * エジソン版限定
 * 走行ルートを外れていないか、指定のエリアに入っていないかを地図で表示する
 * @author Akiko Goto
 * @since 2017/03/13
 */

require 'action/utils/map_utils.php';
/**
 * 共通設定、セッションチェック読み込み　会社の管理者か、ドライバー本人か、サブグループマネージャーのみ閲覧可能
 */

//会社IDが来ているか
if(!empty($_GET['company_id'])){
	$company_id=sanitizeGet($_GET['company_id']);
}else{
	//不正なアクセスです、というメッセージ					
	header("Location:index.php?action=message_mobile&situation=wrong_access");		
}


//空車のみフラグ
if(!empty($_GET['vacancy'])){
	$vacancy=sanitizeGet($_GET['vacancy']);
}else{
	$vacancy = null;
}

//営業所
if(!empty($_GET['branch_id'])){
	$branch_id = sanitizeGet($_GET['branch_id']);
}else{
	$branch_id = null;
}

if(empty($branch_manager_id)){
	$branch_manager_id = null;
 }

	//会社のユーザーIDと、編集するIDがあっているか,営業所長かあるいはドライバー本人か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

try{
	
	//営業所の取得
	$branchList=Branch::getByCompanyId($company_id);
	
	//記録の取得
	if(!empty($is_branch_manager)){
		
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


	//Google Mapの中心点座標取得
	if($branch_id){
		
		$company_latlng=Branch::getlatLngByBranch($branch_id);
		
	}else{
		
		$company_latlng=Branch::getLatLng($company_id);
		
	}

	//会社の住所の緯度経度がある場合に地図の中心点として設定する
	if ( $company_latlng ) {
		$company_lat=$company_latlng['latitude'];
		$company_long=$company_latlng['longitude'];
	} else {
		//ない場合は、東京にする
		$company_lat = 35.681382;
		$company_long = 139.766084;
		$hasNoCompanysLatLng = true;
	}

}catch(Exception $e){

	$message=$e->getMessage();
}

$all_number_control_system_url_start = "<a href='http://54.64.177.119/TransportDetail.aspx?carno=";
$all_number_control_system_url_middle = "' target='_blank'>http://54.64.177.119/TransportDetail.aspx?carno=";
$all_number_control_system_url_end = "</a>";

list($data, $links)=make_page_link($dataList);
// echo '<pre>';
// var_dump($dataList);
// echo '</pre>';

$smarty->assign("links",$links['all']);
if(!empty($driver_name)){
	$smarty->assign("driver_name",$driver_name[0]);
}
$smarty->assign("data",$data);
$smarty->assign("company_id",$company_id);

if(!empty($driver_locations)){
	$smarty->assign("driver_locations",$driver_locations);
}
$smarty->assign("working_status", $workingStatus[0]);

$smarty->assign("all_number_control_system_url_start",$all_number_control_system_url_start);
$smarty->assign("all_number_control_system_url_middle",$all_number_control_system_url_middle);
$smarty->assign("all_number_control_system_url_end", $all_number_control_system_url_end);

//会社の座標
$smarty->assign("company_long",$company_long);
$smarty->assign("company_lat",$company_lat);

if(!empty($hasNoCompanysLatLng)){
	$smarty->assign("hasNoCompanysLatLng",$hasNoCompanysLatLng);
}

//営業所 営業所が2か所以上の時に営業所を表示
if(count($branchList) > 1){
	$smarty->assign("branch_list",$branchList);
}

$smarty->assign("js",'
<script src="'.MAPBOX_GL_JS.'" type="text/javascript"></script>');

$css = "<link rel=\"stylesheet\" href=\"".MAPBOX_GL_CSS."\" type=\"text/css\" media=\"screen\">";
$smarty->assign("additional_css", $css);


$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");


$smarty->assign("filename","area/areaMap.html");
$smarty->display('template.html');

?>