<?php
require 'action/utils/map_utils.php';

$array = array('id', 'status', 'company_id', 'driver_id', 'first_name', 'last_name', 'title_date', 'date', 'transport_route_drivers_id');
$array = array_merge($transport_route_array, $array);

foreach($array as $key){

	if($key != "geo_json"){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}else{
		$datas[$key]=preg_replace('/\r\n/', '',  $_POST[$key]);
		$datas[$key] = preg_replace('/\s(?=\s)/', '', $datas[$key]);
	}

	//フォームに戻った場合のため、過去のデータを保持
	$_SESSION['pre_data'][$key]=$_POST[$key];

}

$form_validate=new Validate();
//新規ルート作成
if(empty($datas['status'])){
	$errors=$form_validate->validate_form_transport_route($datas);

//ルートのマスタ編集
}elseif($datas['status'] == 'EDIT'){
	$errors=$form_validate->validate_form_transport_route_edit($datas);

//ドライバーに紐づくルート編集の場合
}else{
	$errors=$form_validate->validate_form_transport_route_for_driver($datas);
}

if($errors){

	$form_validate->show_form($errors);
	$form_validate->lasturl='index.php?action=/transport_route/putRoute';

}else{

	//新規ルート作成
	if(empty($datas['status'])){
		$destination = Destination::getById($datas['destination_id']);

	//ルートのマスタ編集
	}elseif($datas['status'] == 'EDIT'){
		$destination = Destination::getById($datas['destination_id']);
		if(empty($datas['geo_json'])){

			$transportRoute = TransportRoute::selectRoute($datas['id']);
			$naviAreas = NaviArea::selectbyTransportRouteId($datas['id']);

			$datas['geo_json'] = $transportRoute['geo_json'];
			$smarty->assign("name", $transportRoute['name']);
			$smarty->assign("information", $transportRoute['information']);
			$smarty->assign("transportRoute",$transportRoute);
			$smarty->assign("naviAreas",$naviAreas);

		}

	//ドライバーに紐づくルート編集
	}else{

		$transportRoute = TransportRoute::selectRoute($datas['select_root_id']);
		$naviAreas = NaviArea::selectbyTransportRouteId($datas['select_root_id']);

	}

	foreach($datas as $key => $value){
		$smarty->assign("$key",$value);
	}

	list($company_lat, $company_long, $hasNoCompanysLatLng) = getCompanyLatLng($datas['company_id'], $branch_id);

	$smarty->assign("company_long",$company_long);
	$smarty->assign("company_lat",$company_lat);
	$smarty->assign("hasNoCompanysLatLng",$hasNoCompanysLatLng);

	$smarty->assign("js",'<script src="'.MAPBOX_GL_JS.'" type="text/javascript"></script>');
	$css = "<link rel=\"stylesheet\" href=\"".MAPBOX_GL_CSS."\" type=\"text/css\" media=\"screen\">";
	$smarty->assign("additional_css", $css);

	$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");

	$smarty->assign("transportRoute",$transportRoute);
	$smarty->assign("naviAreas",$naviAreas);
	$smarty->assign("destinationName", $destination['destination_name']);
	$smarty->assign("filename","transport_route/confirm_route.html");
	$smarty->assign("target",'transport_route/putindbRoute');
	$smarty->display("template.html");
}