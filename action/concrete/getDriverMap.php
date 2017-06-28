<?php
/** 会社ごとのドライバーの位置をGoogleMapに表示する　iPhone ,PC
 * ver2.0から追加
 */

/**
 * 共通設定、セッションチェック読み込み　会社の管理者か、ドライバー本人のみ閲覧可能
 */

//会社IDが来ているか
if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id']);
}else{
	//不正なアクセスです、というメッセージ					
	header("Location:index.php?action=message_mobile&situation=wrong_access");		
}

//空車のみフラグ
if($_GET['vacancy']){
	$vacancy=sanitizeGet($_GET['vacancy']);
}

//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
	user_auth($u_id,$company_id);



try{

	//記録の取得
	$dataList=Driver::getDriverMap($company_id,$vacancy);

	if($dataList){
		
		$searched_array = array();
		
		foreach($dataList as $key=>$each_dataList){
			$workingStatus = Data::getStatusByCompanyId($company_id);
			$driver_locations[$key]['latitude']=$each_dataList['latitude'];
			$driver_locations[$key]['longitude']=$each_dataList['longitude'];
			$driver_locations[$key]['driver_name']=$each_dataList['last_name'].$each_dataList['first_name'];
			$driver_locations[$key]['status']=$each_dataList['status'];
			$driver_locations[$key]['image_name']=$each_dataList['image_name'];

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
	}


	//Google Mapの中心点座標取得
	$company_latlng=Branch::getLatLng($company_id);
	$company_lat=$company_latlng['latitude'];
	$company_long=$company_latlng['longitude'];


}catch(Exception $e){

	$message=$e->getMessage();
}


list($data, $links)=make_page_link($dataList);

$smarty->assign("links",$links['all']);
$smarty->assign("driver_name",$driver_name[0]);
$smarty->assign("data",$data);
$smarty->assign("company_id",$company_id);
$smarty->assign("driver_locations",$driver_locations);
$smarty->assign("working_status", $workingStatus[0]);

//会社の座標
$smarty->assign("company_long",$company_long);
$smarty->assign("company_lat",$company_lat);

//Google Map
//$smarty->assign("js",'<script src="'.GOOGLE_MAP.'"type="text/javascript"></script>');

//OpenStreetMap OpenLayers
$smarty->assign("js",'<script src="'.OPEN_LAYERS.'" type="text/javascript"></script>
<script src="'.MAPBOX_JS.'" type="text/javascript"></script>');

$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");


$smarty->assign("filename","concrete/getDriverMap.html");
$smarty->display('template.html');

?>