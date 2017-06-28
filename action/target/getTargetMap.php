<?php
/** コンテナの位置をGoogleMapに表示する　iPhone ,PC
 * ver2.5から追加
 */

/**
 * 共通設定、セッションチェック読み込み　会社の管理者か、ドライバー本人のみ閲覧可能
 */

	//会社IDが来ているか
	if($_GET['company_id']){	
		$company_id=sanitizeGet($_GET['company_id']);
		}
		
	//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
	if($session_id=$driver_id){
		user_auth($u_id,$company_id);
	}
	
	if($_POST["time_from_year"]){
		$refine['time_from_year'] = htmlentities($_POST["time_from_year"], ENT_QUOTES, mb_internal_encoding());
	    $refine['time_from_month'] = htmlentities($_POST["time_from_month"], ENT_QUOTES, mb_internal_encoding());
	    $refine['time_from_day'] = htmlentities($_POST["time_from_day"], ENT_QUOTES, mb_internal_encoding());
	    
	    $refine['time_to_year'] = htmlentities($_POST["time_to_year"], ENT_QUOTES, mb_internal_encoding());
	    $refine['time_to_month'] = htmlentities($_POST["time_to_month"], ENT_QUOTES, mb_internal_encoding());
	    $refine['time_to_day'] = htmlentities($_POST["time_to_day"], ENT_QUOTES, mb_internal_encoding());
	    
	    $smarty->assign("time_from_year", $refine['time_from_year']);
		$smarty->assign("time_from_month", $refine['time_from_month']);
		$smarty->assign("time_from_day", $refine['time_from_day']);
		
		$smarty->assign("time_to_year", $refine['time_to_year']);
		$smarty->assign("time_to_month", $refine['time_to_month']);
		$smarty->assign("time_to_day", $refine['time_to_day']);
	    
	}	 
	 
	try{
		
		//記録の取得
		$dataList=Target::getTargetMap($refine, $company_id);
		
		if($dataList){
			foreach($dataList as $key=>$each_dataList){
			
				$target_locations[$key]['target_id']=$each_dataList['target_id'];
				$target_locations[$key]['latitude']=$each_dataList['latitude'];
				$target_locations[$key]['longitude']=$each_dataList['longitude'];
				$target_locations[$key]['address']=$each_dataList['address'];
				$target_locations[$key]['created']=$each_dataList['created'];
				$target_locations[$key]['driver_name']=$each_dataList['last_name'].$each_dataList['first_name'];
				
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
$smarty->assign("target_locations",$target_locations);


//会社の座標
$smarty->assign("company_long",$company_lat);
$smarty->assign("company_lat",$company_long);

//Google Map
//$smarty->assign("js",'<script src="'.GOOGLE_MAP.'"type="text/javascript"></script>');
//OpenStreetMap OpenLayers
$smarty->assign("js",'<script src="'.OPEN_LAYERS.'" type="text/javascript"></script>
<script src="'.MAPBOX_JS.'" type="text/javascript"></script>');

$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");						

$smarty->assign("filename","target/getTargetMap.html");
$smarty->display('template.html');

?>