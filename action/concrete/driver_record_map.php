<?php
/** 個別ドライバーの業務日誌確認画面　iphone,PC
 * 　MAPでの表示
 * ver2.1から追加
 */

/**
 * 共通設定、セッションチェック読み込み　会社の管理者か、ドライバー本人のみ閲覧可能
 */

//ドライバーIDが来ているか

if($_GET['driver_id']){
	$driver_id=sanitizeGet($_GET['driver_id']);
}
//ドライバーIDが来ているか
if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id']);
}

//取得したい記録の数が来ているか
if($_GET['record_no']){
	
	$record_no=sanitizeGet($_GET['record_no']);
	
}elseif($_GET["time_from_year"]){
	
	$record_no = NULL;
	
}else{
	
	//件数と、年月日が投げられてなければ100件がデフォルトの件数
	$record_no = MAX_DRIVER_RECORDS;
}



//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);

//時間の指定データが来ているか

$time_get = get_time_from_and_to_ymdhm();
$time_from = $time_get["0"];
$time_to = $time_get["1"];


try{

	//記録の取得
	$dataList=Driver::getStatusById($driver_id, $record_no, $time_from, $time_to);

	$workingStatuses = Data::getStatusByCompanyId($company_id);
	$workingStatus = $workingStatuses[0];
	$driver_locations = $dataList[0];
	
	/*
	if($dataList){
		foreach($dataList[0] as $key=>$each_dataList){
			$workingStatuses = Data::getStatusByCompanyId($company_id);
			$workingStatus = $workingStatuses[0];
			$driver_locations[$key]['latitude']=$each_dataList['latitude'];
			$driver_locations[$key]['longitude']=$each_dataList['longitude'];
			$driver_locations[$key]['status']=$each_dataList['status'];
			$driver_locations[$key]['address']=$each_dataList['address'];
			$driver_locations[$key]['speed']=$each_dataList['speed'];
			
			$driver_locations[$key]['start']=$each_dataList['start'];
			$driver_locations[$key]['end']=$each_dataList['end'];
			
			$driver_locations[$key]['work_id']=$each_dataList['work_id'];
			$driver_locations[$key]['created']=$each_dataList['created'];			
			
		}
	}
	*/
	//ドライバー名の取得
	$driver_name=Driver::getNameById($driver_id);
	//ドライバー自身の編集許可ステータス
	$is_ban_editing = Data::isBannedDriverEditing($company_id, $u_id);
	
}catch(Exception $e){

	$message=$e->getMessage();

}
			
list($data, $links)=make_page_link($dataList[0],30);

$smarty->assign("links",$links['all']);
$smarty->assign("driver_name",$driver_name[0]);
$smarty->assign("is_ban_editing",$is_ban_editing);
$smarty->assign("data",$data);
$smarty->assign("driver_locations",$driver_locations);
$smarty->assign("working_status", $workingStatus);

//絞り込み検索で、何件が表示されているか、など
$smarty->assign("record_no",$record_no);
$smarty->assign("time_from", $time_from);
$smarty->assign("time_to", $time_to);


$smarty->assign("driver_id",$driver_id);
$smarty->assign("company_id",$company_id);
$smarty->assign("record_no",$record_no);

//最後の場所をGoogle Mapの中心にする
$last_latitude = $driver_locations[0]['latitude'];
$last_longitude = $driver_locations[0]['longitude'];

//OpenStreetMap OpenLayers
$smarty->assign("js",'<script src="'.OPEN_LAYERS.'" type="text/javascript"></script>
<script src="'.MAPBOX_JS.'" type="text/javascript"></script>');
$smarty->assign("onload_js","onload=\"initialize($last_latitude, $last_longitude)\"");

//Google Map
$smarty->assign("center_latitude", $last_latitude);
$smarty->assign("center_longitude", $last_longitude);

//$smarty->assign("js",'<script src="'.GOOGLE_MAP.'" type="text/javascript"></script>');
//$smarty->assign("onload_js","onload=\"initialize($last_latitude,$last_longitude)\"");


$smarty->assign("filename", "concrete/driver_record_map.html");
$smarty->display('template.html');

?>