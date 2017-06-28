<?php
require 'action/utils/map_utils.php';

if(!empty($_POST['company_id'])){
	$company_id=sanitizeGet($_POST['company_id'],ENT_QUOTES);
}

if(!empty($_POST['transport_route_id'])){
	$transport_route_id=sanitizeGet($_POST['transport_route_id'],ENT_QUOTES);
}

if(!empty($_POST['navi_area_id'])){
	$navi_area_id=sanitizeGet($_POST['navi_area_id'],ENT_QUOTES);
}

if(!empty($_POST['branch_id'])){
	$branch_id=sanitizeGet($_POST['branch_id'],ENT_QUOTES);
}else{
	$branch_id = null;
}

foreach($area_array as $key){

	$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());

	//フォームに戻った場合のため、過去のデータを保持
	$_SESSION['pre_data'][$key]=$_POST[$key];

}

$form_validate=new Validate();

$errors=$form_validate->validate_form_area($datas);

if($errors){

	$form_validate->show_form($errors);
	$form_validate->lasturl='index.php?action=/navi_area/putArea';

}else{
	foreach($datas as $key => $value){
	
		$smarty->assign("$key",$value);
	
	}
	list($company_lat, $company_long, $hasNoCompanysLatLng) = getCompanyLatLng($_POST['company_id'], $_POST['branch_id']);
	
	$naviAreas = NaviArea::selectbyTransportRouteId($transport_route_id);
	
	//真ん中の座標
	$latitude = $datas['latitude'];
	$longitude = $datas['longitude'];
	
	$smarty->assign("hasNoCompanysLatLng",$hasNoCompanysLatLng);
	
	$smarty->assign("js",'<script src="'.MAPBOX_GL_JS.'" type="text/javascript"></script>');
	
	$css = "<link rel=\"stylesheet\" href=\"".MAPBOX_GL_CSS."\" type=\"text/css\" media=\"screen\">";
	$smarty->assign("additional_css", $css);
	
	$smarty->assign("onload_js","onload=\"initialize($latitude, $longitude)\"");
	
	$smarty->assign("geo_json", $_POST['geo_json']);
	$smarty->assign("naviAreas", $naviAreas);
	$smarty->assign("navi_area_id", $navi_area_id);
	
	$smarty->assign("filename","navi_area/confirm_area.html");
	$smarty->assign("target",'navi_area/putindbArea');
	$smarty->display("template.html");
	
}
