<?php
require 'action/utils/map_utils.php';

if(!empty($_POST['company_id'])){
	$company_id=sanitizeGet($_POST['company_id'],ENT_QUOTES);
}

if(!empty($_GET['branch_id'])){
	$branch_id=sanitizeGet($_GET['branch_id'],ENT_QUOTES);
}else{
	$branch_id = null;
}

if(!empty($_GET['select_root_id'])){
	$select_root_id=sanitizeGet($_GET['select_root_id'],ENT_QUOTES);
}else{
	$select_root_id = null;
}

foreach($transport_route_array as $key){

	if($key != "geo_json"){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}else{
		$datas[$key]=$_POST[$key];
	}

	//フォームに戻った場合のため、過去のデータを保持
	$_SESSION['pre_data'][$key]=$_POST[$key];
}


$form_validate=new Validate();

$errors=$form_validate->validate_form_transport_copy_route($datas);

if($errors){

	$form_validate->show_form($errors);
	$form_validate->lasturl='index.php?action=/transport_route/routeCopy';

}else{

	$transportRoute = TransportRoute::selectRoute($datas['select_root_id']);
	$naviAreas = NaviArea::selectbyTransportRouteId($datas['select_root_id']);

	foreach($datas as $key => $value){

		$smarty->assign("$key",$value);

	}

	list($company_lat, $company_long, $hasNoCompanysLatLng) = getCompanyLatLng($company_id, $branch_id);

	$smarty->assign("company_long",$company_long);
	$smarty->assign("company_lat",$company_lat);
	$smarty->assign("hasNoCompanysLatLng",$hasNoCompanysLatLng);

	$smarty->assign("js",'<script src="'.MAPBOX_GL_JS.'" type="text/javascript"></script>');
	$css = "<link rel=\"stylesheet\" href=\"".MAPBOX_GL_CSS."\" type=\"text/css\" media=\"screen\">";
	$smarty->assign("additional_css", $css);

	$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");

	$smarty->assign("destinationName", $destination['destination_name']);
	$smarty->assign("transport_route_id",$transport_route_id);
	$smarty->assign("transportRoute",$transportRoute);
	$smarty->assign("naviAreas",$naviAreas);
	$smarty->assign("filename","transport_route/confirm_copy_route.html");
	$smarty->assign("target",'transport_route/putindbCopyRoute');
	$smarty->display("template.html");
}