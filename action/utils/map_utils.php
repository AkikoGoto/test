<?php

/**
 * MAPのためにいろいろ使う関数
 */

function makeMarkerInformation($dataList, $company_id, $workingStatus=null){

	$searched_array = array();

	foreach($dataList as $key=>$each_dataList){
		
		$workingStatus = Data::getStatusByCompanyId($company_id);
		$driver_locations[$key]['driver_id']=$each_dataList['driver_id'];
		$driver_locations[$key]['latitude']=$each_dataList['latitude'];
		$driver_locations[$key]['longitude']=$each_dataList['longitude'];
		$driver_locations[$key]['driver_name']=$each_dataList['last_name'].$each_dataList['first_name'];
		$driver_locations[$key]['status']=statusToJapanese($each_dataList['status'], $workingStatus[0]);
		$driver_locations[$key]['direction']=$each_dataList['direction'];
		$driver_locations[$key]['geographic_id']=$each_dataList['geographic_id'];
		$server = RelayServer::getByGeographicId($each_dataList['geographic_id']);
		if($each_dataList['status'] == '2'){
			$driver_locations[$key]['url']=$server['zensu_url'];
		}
		$driver_locations[$key]['car_type']=$each_dataList['car_type'];
		
		//エジソンのマーカー画像はステータスにより変更される
		$driver_locations[$key]['image_name'] = makeDriverStatusImage($each_dataList['status']);
		
		$driver_locations[$key]['created']=$each_dataList['created'];
			
		//最後の記録が古かったら、進行方向を出さない
			
		if(secondDiffFromNow($each_dataList['created'])>NOT_NEW_SECOND){

			$driver_locations[$key]['direction']= null;
			$driver_locations[$key]['is_old'] = true;

		}else{

			$driver_locations[$key]['direction']=$each_dataList['direction'];
			$driver_locations[$key]['is_old'] = false;

		}
		
		//$driver_locations[$key]['marker'] = makeDriverStatusMarker($each_dataList['status'], $driver_locations[$key]['is_old']);		
		$driver_locations[$key]['marker'] = null;
		

		//アイコンが重なった地点にある場合に、注意を促す
		//重複を避けるために、すでに他の値と同じであることが分かっている値（$searched_array)にkeyがある場合は、検索しない
		$original_key = $key;
		if(!in_array($key, $searched_array)){
			list($same_position_array, $searched_keys) = in_array_field($each_dataList['latitude'],
			$each_dataList['longitude'],
			$dataList,
			$original_key);
			$searched_array = array_merge($searched_array, $searched_keys);
			if($same_position_array !=null){
				$driver_locations[$key]['same_position'] = implode('、', $same_position_array);
			}
		}
	}

	return $driver_locations;

}

/**
 * 会社の住所の座標を取得
*/

function getCompanyLatLng($company_id, $branch_id){
	
	//Google Mapの中心点座標取得
	if(!empty($branch_id)){
		
		$company_latlng=Branch::getlatLngByBranch($branch_id);
		
	}else{
		
		$company_latlng=Branch::getLatLng($company_id);
		
	}
	
	//会社の住所の緯度経度がある場合に地図の中心点として設定する
	if ( $company_latlng ) {
		$company_lat=$company_latlng['latitude'];
		$company_long=$company_latlng['longitude'];
		$hasNoCompanysLatLng = false;
	} else {
		//ない場合は、東京にする
		$company_lat = 35.681382;
		$company_long = 139.766084;
		$hasNoCompanysLatLng = true;
	}
	
	return array($company_lat, $company_long, $hasNoCompanysLatLng);
}
