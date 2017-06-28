<?php
/**
 * ドライバールート表示
 * @author Yuji Hamada
 * @since 2017/05/08
 * @version 2.0
 *
 */

require 'action/utils/map_utils.php';

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id']);
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

$date = $_GET['date'];
$titleDates = explode('-', $date);
$titleDate = $titleDates[0].'年'.$titleDates[1].'月'.$titleDates[2].'日';

$driver_id = $_GET['driver_id'];

$driverNameArray = Driver::getNameById($driver_id);
$driverName = $driverNameArray[0]['last_name'] . $driverNameArray[0]['first_name'];

$transport_route_id = sanitizeGet($_GET['transport_route_id']);

$transportRoute = TransportRoute::selectRoute($transport_route_id);

list($company_lat, $company_long, $hasNoCompanysLatLng) = getCompanyLatLng($company_id, $branch_id);

$transport_route_driver = TransportRouteDriver::getRoutesByDriverIdAndDate($driver_id, $date);

$naviAreas = NaviArea::selectbyTransportRouteId($transport_route_id);

$smarty->assign("company_long",$company_long);
$smarty->assign("company_lat",$company_lat);
$smarty->assign("hasNoCompanysLatLng",$hasNoCompanysLatLng);

$smarty->assign("js",'<script src="'.MAPBOX_GL_JS.'" type="text/javascript"></script>');
$css = "<link rel=\"stylesheet\" href=\"".MAPBOX_GL_CSS."\" type=\"text/css\" media=\"screen\">";
$smarty->assign("additional_css", $css);

$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");
$smarty->assign("transportRoute",$transportRoute);
$smarty->assign("transport_route_driver",$transport_route_driver);
$smarty->assign("naviAreas",$naviAreas);
$smarty->assign("company_id",$company_id);
$smarty->assign("date",$date);
$smarty->assign("titleDate",$titleDate);
$smarty->assign("driverName",$driverName);
$smarty->assign("driver_id",$driver_id);
$smarty->assign("filename","transport_route/view_driver_route_area.html");
$smarty->display("template.html");