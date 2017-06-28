<?php
/**
 * 日報をcsvで出力するアクション
 */


	//ドライバーIDが来ているか
	if($_GET['driver_id']){
		$driver_id=sanitizeGet($_GET['driver_id']);
	}
	//会社IDが来ているか
	if($_GET['company_id']){
		$company_id=sanitizeGet($_GET['company_id']);
	}
	//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
	driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);

	$driver_name = Driver::getNameById($driver_id);


	$time_get = get_time_from_and_to_ymdhm();
	$time_from = $time_get["0"];
	$time_to = $time_get["1"];


	//記録の取得
	$dataList=Driver::getStatusById($driver_id, null, $time_from, $time_to);

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

		//出力する
		$today = date('Y').'_'.date('m').'_'.date('d');
		$file = 'geojson'.'_'.$today.'.geojson';

		header('Content-Disposition: attachment; filename="'. $file . '"');
		header('Content-Type: application/json;');

		echo $geo_json;

	}else{

		$smarty->assign("message","該当するデータがありません。");

		$smarty->assign("driver_id",$driver_id);
		$smarty->assign("company_id",$company_id);

		$smarty->assign("filename","driver_record_map.html");

		$smarty->display('template.html');
	}

