<?php
/**
 * ナビエリアを新規に作成する画面　エジソン版限定
 * @author Akiko Goto
 * @since 2017/04/12
 * @version 2.0
 */

require 'action/utils/map_utils.php';

//会社ID、ルートIDが来ているか

if(!empty($_GET['company_id'])){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if(!empty($_GET['transport_route_id'])){
	$transport_route_id=sanitizeGet($_GET['transport_route_id'],ENT_QUOTES);
}

if(!empty($_GET['navi_area_id'])){
	$navi_area_id=sanitizeGet($_GET['navi_area_id'],ENT_QUOTES);
}

if(!empty($_GET['driver_id'])){
	$driver_id=sanitizeGet($_GET['driver_id'],ENT_QUOTES);
}

if(!empty($_GET['date'])){
	$date=sanitizeGet($_GET['date'],ENT_QUOTES);
}

if(!empty($_GET['branch_id'])){
	$branch_id=sanitizeGet($_GET['branch_id'],ENT_QUOTES);
}else{
	$branch_id = null;
}

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	if(!empty($branch_manager_id)){
			
		//ドライバー指定ありの場合は、このドライバーが許可された営業所のドライバー情報か
		if($driver_id){
			branch_manager_driver_auth($branch_manager_id, $driver_id);
		}
	
	}

try{		

	if(!empty($transport_route_id)){
		$transportRoute = TransportRoute::selectRoute($transport_route_id);
		$naviAreas = NaviArea::selectbyTransportRouteId($transport_route_id);
	}
	if(!empty($navi_area_id)){
		$naviArea = NaviArea::getById($navi_area_id);
	}

	list($company_lat, $company_long, $hasNoCompanysLatLng) = getCompanyLatLng($company_id, $branch_id);
	
	
	//フォームなどで戻った時のために、セッションにデータを格納
	if(!empty($_SESSION['root_data'])){
		$smarty->assign("pre_data",$_SESSION['root_data']);
	}

	

}catch(Exception $e){

	$message=$e->getMessage();

}

//会社の座標
$smarty->assign("company_long",$company_long);
$smarty->assign("company_lat",$company_lat);
$smarty->assign("hasNoCompanysLatLng",$hasNoCompanysLatLng);
$smarty->assign("geo_json", $transportRoute['geo_json']);
$smarty->assign("transport_route_id", $transport_route_id);
$smarty->assign("naviArea", $naviArea);
$smarty->assign("naviAreas", $naviAreas);
$smarty->assign("navi_area_id", $navi_area_id);
$smarty->assign("driver_id", $driver_id);
$smarty->assign("date", $date);

$smarty->assign("js",'<script src="'.MAPBOX_GL_JS.'" type="text/javascript"></script>');

$css = "<link rel=\"stylesheet\" href=\"".MAPBOX_GL_CSS."\" type=\"text/css\" media=\"screen\">";
$smarty->assign("additional_css", $css);

$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");

$smarty->assign("transportRoute",$transportRoute);
$smarty->assign("company_id",$company_id);
$smarty->assign("filename","navi_area/put_area.html");
$smarty->display("template.html");

?>