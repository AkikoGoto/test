<?php
/**  全員のドライバーの業務日誌確認画面　iphone,PC
 * 　MAPでの表示
 * ver2.1から追加
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

/*
 * 最初の表示かどうか
 * （検索前のフォームだけの画面か検索結果の表示か）
 */
$is_before_search = true;

/*
 * セレクトメニューで表示させる、検索に使用する日付を作成
 */
//現在時刻
$year = date("Y");
$month = date("m");
$day = date("d");
$hour = date("H");//G
$minit = date("i");
// test code
/*
$month = 3;
$day = 1;
$hour = 0;
$minit = 29;
*/

//　左側のセレクトメニュー用の日付
$origin_year = $year;
$origin_month = $month;
$origin_day = $day;
$minus_minit = $minit - 30;
$origin_hour = $hour;
$origin_minit = $minus_minit;
if ($origin_minit <= 0) {
	$origin_minit = 60 + $origin_minit;
	if ($minus_minit != 0) {
		$origin_hour = $hour - 1;
	}
}
// 30分前の日付が前日の場合
if ($origin_hour == -1) {
	$origin_day = $origin_day - 1;
	$origin_hour = 23;
}
//前日が前月末日の場合
if ($origin_day == 0) {
	$origin_month = $origin_month - 1;
	// 前月が前年の12月の場合
	if ($origin_month == 0) {
		$origin_year = $origin_year - 1;
		$origin_month = 12;
	}
	$origin_day = date('t', mktime(0, 0, 0, $origin_month, 1, $origin_year));
}

$select_time_array = array(
							'time_from_year', 'time_from_month', 'time_from_day', 'time_from_hour', 'time_from_minit',
							'time_to_year', 'time_to_month', 'time_to_day', 'time_to_hour', 'time_to_minit');
$current_time_array = array (
							$origin_year,$origin_month,$origin_day,$origin_hour,$origin_minit,//開始時刻
							$year,$month,$day,$hour,$minit//終了時刻
							);
foreach ($select_time_array as $time_index => $time_key) {
	if ($_GET[$time_key] == null) {
		$_GET[$time_key] = $current_time_array[$time_index];
	} else {
		$_GET[$time_key] = sprintf('%02d', $_GET[$time_key]);
		$is_before_search = false;
	}
}

/*
 * 軌跡を表示させたいドライバーを取得
 */
if ($_GET['driver_ids']) {
	$displayDriverIds = array();
	foreach ($_GET['driver_ids'] as $value) {
		array_push( $displayDriverIds, sanitizeGet($value));
	}
	$_SESSION['driver_ids'] = array();
	$_SESSION['driver_ids'] = $displayDriverIds;
	
	// userはドライバーを一人でも選択している場合にのみ、チェックマークをつける。これがないと、誰も選択してないとき、画面を更新するとすべてのドライバーにチェックがついてしまう。
	$driver_selected = TRUE;
} else {
	$driver_selected = FALSE;
}

//会社のユーザーIDと、編集するIDがあっているか,営業所長かあるいはドライバー本人か確認
company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

/*	if(!empty($u_id)){

		user_auth($u_id, $company_id);
	
	}elseif(!empty($branch_manager_id)){
			
		branch_manager_auth($branch_manager_id, $company_id);
	
	}*/
//user_auth($u_id,$company_id);

//時間の指定データが来ているか
$time_get = get_time_from_and_to_ymdhm();
$time_from = $time_get["0"];
$time_to = $time_get["1"];

try{
	//記録の取得
	// 初期表示の場合、ドライバーのデータを表示しない
	
	if($is_branch_manager && $branch_manager_id){
		
		$allDrivers = Branch::getBranchDriverByBranchManagerId($branch_manager_id);
		
	}elseif(!empty($u_id)){
		
		$allDrivers = Driver::getAllDriverByCompanyId($company_id);
	
	}
		
	if (!$is_before_search) {
		//$driver_locations = Driver::getAllDriverStatus($displayDriverIds, $allDrivers, $time_from, $time_to);
		$recorded_drivers = Driver::getDriverIDsHaveStatusRecorded($displayDriverIds, $allDrivers, $time_from, $time_to);
		//die(var_dump($r));
		
		//会社情報を取得
		$company = Data::getById($company_id, 0);
		$last_latitude = $company[0]->latitude;
		$last_longitude = $company[0]->longitude;
		//print_r($company[0]);
	
		$workingStatuses = Data::getStatusByCompanyId($company_id);
		$workingStatus = $workingStatuses[0];
		/*
		//　選択したドライバーの中で、作業履歴があるドライバーにのみ色を割り振る
		$colorsOfDriversWithLocation = array();
		foreach ($driver_locations as $driver) {
			$colorsOfDriversWithLocation[$driver['id']] = get_attention_color();//$driver['color'];
		}
		*/
	}
	
	// 選択されたドライバーか check
	//　データのあるドライバーか color
	$displayDriverWithColor = array();	// 地図の下に表示させるドライバーの情報
	$displayDriverWithNoColor = array();	//地図の下に表示させる、データのないドライバーの情報
	foreach ($allDrivers as $key => $driver) {
		$driver_id = $driver['id'];
		if (count($displayDriverIds) > 0 &&
			in_array( $driver_id, $displayDriverIds)) {
			$allDrivers[$key]['checked'] = true;
		}
		
		if (count($recorded_drivers) > 0 &&
			in_array( $driver_id, $recorded_drivers)) {
			$allDrivers[$key]['color'] = get_attention_color();
			array_push( $displayDriverWithColor, $allDrivers[$key]);
		} else if ( $allDrivers[$key]['checked'] ) {
			array_push( $displayDriverWithNoColor, $allDrivers[$key]);
		}
	}
	
}catch(Exception $e){

	$message=$e->getMessage();

}

//$smarty->assign("driver_locations",$driver_locations);
//$smarty->assign("driver_locations",array());
$smarty->assign("working_status", $workingStatus);

//絞り込み検索で、何件が表示されているか、など
$smarty->assign("record_no",$record_no);
$smarty->assign("time_from", $time_from);
$smarty->assign("time_to", $time_to);

$smarty->assign("driver_id",$driver_id);
$smarty->assign("company_id",$company_id);

// すべてのドライバー情報
$smarty->assign("is_before_search",$is_before_search);
$smarty->assign("driver_selected",$driver_selected);
$smarty->assign("all_drivers",$allDrivers);
$smarty->assign("display_driver_ids",$displayDriverIds);
$smarty->assign("recorded_drivers",$recorded_drivers);

if ( !$is_before_search and count($displayDriverWithColor) > 0 ) {
//if (isset($recoreded_drivers)) {
	//OpenStreetMap OpenLayers
	$json = "'".json_encode($displayDriverWithColor)."'";
	$smarty->assign("js",'
	<script src="'.MAPBOX_JS.'" type="text/javascript"></script>
	<script src="templates/js/all_driver_record_map.js" type="text/javascript"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.compat.min.js"  type="text/javascript"></script>
	<script>
		$(document).ready(function(){
			initializeMap('.$last_latitude.','.$last_longitude.','
				.$company_id.','
				.'"'.$time_from.'","'.$time_to.'",'.$json.',"'.MAPBOX_PROJECT_ID.'",'.json_encode($workingStatus).',"'.MAPBOX_ACCESS_TOKEN.'");
		});
	</script>
	
	');
}
//	$smarty->assign("onload_js","onload=\"initialize($last_latitude, $last_longitude)\"");
	//マップの中心点
	$smarty->assign("center_latitude", $last_latitude);
	$smarty->assign("center_longitude", $last_longitude);
//}
// 地図の下に表示させる、表示中または選択中のドライバーデータ
$smarty->assign("driver_with_color", $displayDriverWithColor);
$smarty->assign("driver_with_no_color", $displayDriverWithNoColor);

if (MODE != "CONCRETE") {
	$smarty->assign("filename","allDriverRecordMap.html");
} else {
	$smarty->assign("filename","concrete/allDriverRecordMap.html");
}

$smarty->display('template.html');

?>