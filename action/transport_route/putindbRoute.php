<?php

require 'action/utils/map_utils.php';

if(!empty($_POST['company_id'])){
	$company_id=sanitizeGet($_POST['company_id'],ENT_QUOTES);
}

if(!empty($_POST['branch_id'])){
	$branch_id=sanitizeGet($_POST['branch_id'],ENT_QUOTES);
}else{
	$branch_id = null;
}


$array = array('id', 'status', 'company_id', 'driver_id', 'date', 'transport_route_drivers_id');
$array = array_merge($transport_route_array, $array);

foreach($array as $key){

	if($key != "geo_json"){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}else{
		$datas[$key]=$_POST[$key];
	}

	//フォームに戻った場合のため、過去のデータを保持
	$_SESSION['pre_data'][$key]=$_POST[$key];

}

$datas['geo_json'] = $datas['geo_json'];
$datas['company_id'] = $company_id;

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

	unset($_SESSION['pre_data']);

	//新規ルート作成、insert
	if(empty($datas['status'])){
		$transport_route_id = TransportRoute::insert($datas);

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
		$smarty->assign("transport_route_id", $transport_route_id);

		$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");

		$smarty->assign("filename","transport_route/completion_put.html");
		$smarty->display("template.html");

	//輸送ルートマスタ編集、UPDATE
	}elseif($datas['status'] == 'EDIT'){
		TransportRoute::update($datas);
		$transport_route_id = $datas['id'];

		header("Location:index.php?action=transport_route/viewRoute&company_id=$company_id&transport_route_id=$transport_route_id&branch_id=$branch_id");
	//ドライバーに紐づくルート編集、UPDATE
	}else{
		TransportRoute::updateDriverRoute($datas);
		$transport_route_id = $datas['select_root_id'];
		$driver_id = $datas['driver_id'];
		$date = $datas['date'];
		
		header("Location:index.php?action=transport_route/viewDriverRouteArea&company_id=$company_id&driver_id=$driver_id&transport_route_id=$transport_route_id&date=$date");
	}



}