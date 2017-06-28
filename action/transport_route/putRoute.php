<?php
/**
 * 輸送ルートを新規に作成する画面　エジソン版限定
 * @author Akiko Goto
 * @since 2017/04/06
 * @version 2.0
 */

require 'action/utils/map_utils.php';

if(!empty($_GET['company_id'])){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if(!empty($_GET['transport_route_id'])){
	$transport_route_id=sanitizeGet($_GET['transport_route_id'],ENT_QUOTES);
}

if(!empty($_GET['driver_id'])){
	$driver_id=sanitizeGet($_GET['driver_id'],ENT_QUOTES);
}

if(!empty($_GET['branch_id'])){
	$branch_id=sanitizeGet($_GET['branch_id'],ENT_QUOTES);
}else{
	$branch_id = null;
}

if(!empty($_GET['date'])){
	$date=sanitizeGet($_GET['date'],ENT_QUOTES);
}else{
	$date = null;
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

	if(!empty($_POST['transport'])){
		$time_get = get_time_from_and_to_ymdhm();
		$time_from = $_POST['time_from_year'].'-'.$_POST['time_from_month'].'-'.$_POST['time_from_day'].' '.$_POST['time_from_hour'].':'.$_POST['time_from_minit'].':00';
		$time_to = $_POST['time_to_year'].'-'.$_POST['time_to_month'].'-'.$_POST['time_to_day'].' '.$_POST['time_to_hour'].':'.$_POST['time_to_minit'].':00';

		//記録の取得
		$dataList=Driver::getStatusById($_POST['driver_id'], null, $time_from, $time_to);

		if($dataList != NULL){

			$coordinate_array = array();

			foreach($dataList[0] as $each_data){

				//JSONに変換する際に、文字列のままだと緯度経度がGeoJSONとしてうまく読み込めないので、doubleにキャストする
				$coordinate = array((double)$each_data['longitude'], (double)$each_data[ 'latitude'] );
				$coordinate_array[] = $coordinate;

			}

			$feature_content['type'] = "Feature";
			$feature_content['properties']['name'] = "route";
			$feature_content['id'] = "1234";

			$feature_content['geometry']['coordinates'] = $coordinate_array;
			$feature_content['geometry']['type'] = "LineString";

			$feature = array($feature_content);


			$geo_json_content['features'] = $feature;

			$geo_json_content['type'] = "FeatureCollection";

			$geo_json = json_encode($geo_json_content);
			$name = '新規ルート';

			$smarty->assign("name",$name);
			$smarty->assign("geo_json",$geo_json);
		}
	}

	//ルートIDが指定されていれば、編集のため、元のデータを表示
	if(!empty($transport_route_id)){
		$status='EDIT';

		//編集の場合、ここに書く
		//ルートデータの取得
		$transportRoute = TransportRoute::selectRoute($transport_route_id);

		//エリア一覧のデータ取得
		$naviAreas = NaviArea::selectbyTransportRouteId($transport_route_id);

		//ドライバーIDとルートデータが紐づいたデータの場合のデータ取得
		$transport_route_driver_data = TransportRoute::selectDriverRouteAreas($company_id, $driver_id, $transport_route_id, $date);
		if(!empty($transport_route_driver_data)){

			$status='EDIT_DRIVER';
			$all_roots = TransportRoute::selectCompanyRoutesFetchUnique($company_id);
			$all_root_areas = TransportRoute::selectAllRoutesAllAreas($company_id);

			$transportRoute = $all_roots;
			$naviAreas = $all_root_areas;

			$driver_name = Driver::getNameById($driver_id);
			$title_date = explode('-', $date);
			$title_date = $title_date[0].'年'.$title_date[1].'月'.$title_date[2].'日';

			$smarty->assign("all_roots",$all_roots);
			$smarty->assign("all_root_areas",$all_root_areas);
			$smarty->assign("driver_name",$driver_name[0]);
			$smarty->assign("transport_route_drivers", $transport_route_driver_data[0]);
			$smarty->assign("title_date",$title_date);

		}

		$smarty->assign("status",$status);
		$smarty->assign("transport_data",$transport_data);

	}

	list($company_lat, $company_long, $hasNoCompanysLatLng) = getCompanyLatLng($company_id, $branch_id);

	//フォームなどで戻った時のために、セッションにデータを格納
	if(!empty($_SESSION['pre_data'])){
		$smarty->assign("pre_data",$_SESSION['pre_data']);
	}

}catch(Exception $e){

	$message=$e->getMessage();

}
$temporaryStorageDestinations = Destination::getByCategoryName($company_id, CATEGORY_TEMPORARY_STORAGE);

//会社の座標
$smarty->assign("company_long",$company_long);
$smarty->assign("company_lat",$company_lat);
$smarty->assign("hasNoCompanysLatLng",$hasNoCompanysLatLng);

$smarty->assign("js",'
<script src="'.MAPBOX_GL_JS.'" type="text/javascript"></script>');

$css = "<link rel=\"stylesheet\" href=\"".MAPBOX_GL_CSS."\" type=\"text/css\" media=\"screen\">";
$smarty->assign("additional_css", $css);

$smarty->assign("transportRoute",$transportRoute);
$smarty->assign("naviAreas",$naviAreas);
$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");
$smarty->assign("company_id",$company_id);
$smarty->assign("driver_id",$driver_id);
$smarty->assign("transport_route_id",$transport_route_id);
$smarty->assign("date",$date);
$smarty->assign("temporaryStorageDestinations",$temporaryStorageDestinations);
$smarty->assign("page_title",TRANSPOERT_ROUTE.' '.NEW_DATA);
$smarty->assign("filename","transport_route/put_route.html");
$smarty->display("template.html");
?>