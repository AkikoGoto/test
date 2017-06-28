<?php
/**
 * 輸送ルート、及びルートに紐づくエリアをコピー
 * @author Koichi Saito
 * @since 2017/04/27
 * @version 1.0
 */

require 'action/utils/map_utils.php';

if(!empty($_GET['company_id'])){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if(!empty($_GET['driver_id'])){
	$driver_id=sanitizeGet($_GET['driver_id'],ENT_QUOTES);
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


	$transportRoute = TransportRoute::selectCompanyRoutesFetchUnique($company_id);
	$naviAreas = TransportRoute::selectAllRoutesAllAreas($company_id);

	list($company_lat, $company_long, $hasNoCompanysLatLng) = getCompanyLatLng($company_id, $branch_id);

	//フォームなどで戻った時のために、セッションにデータを格納
	if(!empty($_SESSION['pre_data'])){
		$smarty->assign("pre_data",$_SESSION['pre_data']);
	}

}catch(Exception $e){

	$message=$e->getMessage();

}

//会社の座標
$smarty->assign("company_long",$company_long);
$smarty->assign("company_lat",$company_lat);
$smarty->assign("hasNoCompanysLatLng",$hasNoCompanysLatLng);

$smarty->assign("js",'
<script src="'.MAPBOX_GL_JS.'" type="text/javascript"></script>');

$css = "<link rel=\"stylesheet\" href=\"".MAPBOX_GL_CSS."\" type=\"text/css\" media=\"screen\">";
$smarty->assign("additional_css", $css);

$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");

$smarty->assign("company_id",$company_id);
$smarty->assign("transport_route_id",$transport_route_id);
$smarty->assign("transportRoute",$transportRoute);
$smarty->assign("naviAreas",$naviAreas);
$smarty->assign("page_title",TRANSPOERT_ROUTE.' '.NEW_DATA);
$smarty->assign("filename","transport_route/route_copy.html");
$smarty->display("template.html");

?>