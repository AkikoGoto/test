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

//営業所
if($_GET['branch_id']){
	$branch_id = sanitizeGet($_GET['branch_id']);
}

//配送先フラグ
if($_GET['show_destinations']){
	$show_destinations = sanitizeGet($_GET['show_destinations']);
}

//配送先フラグ
if(!empty($_GET['show_route'])){
	$show_route = sanitizeGet($_GET['show_route']);
}


//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
	user_auth($u_id,$company_id);

try{
	
	if($is_government){

		$jvList = Data::getJvs();
		if($company_id != $u_id){
			
			//営業所の取得
			$branchList=Branch::getByCompanyId($company_id);
					
		}
		
	}else{
			//営業所の取得
			$branchList=Branch::getByCompanyId($company_id);
		
	}	
	
	if(!empty($show_route)){
		
		$transport_routes = TransportRoute::selectCompanyRoutes($company_id);
	
	}
	
	if($show_destinations){
		//配送先の取得
		$destinationList=Destination::searchDestinations($company_id, null);
		
		//Javascript誤作動防止のために、改行、シングルクォテーション、ダブルクォテーションを除く
		foreach($destinationList as $k => $each_data){
			
			foreach($each_data as $key => $value){
				
				$destinationList[$k][$key] = removeNewLineAndQuotation($value);
				
			}
			
		}		
		
	}


	//Google Mapの中心点座標取得
	if($branch_id){
		
		$company_latlng=Branch::getlatLngByBranch($branch_id);
		
	}else{
		
		$company_latlng=Branch::getLatLng($company_id);
		
	}
		
	$company_lat=$company_latlng['latitude'];
	$company_long=$company_latlng['longitude'];
		

}catch(Exception $e){

	$message=$e->getMessage();
}


list($data, $links)=make_page_link($dataList);

$smarty->assign('jvList',$jvList);

$smarty->assign("company_id",$company_id);
$smarty->assign("key", md5(DB_NAME));

//会社の座標
$smarty->assign("company_long",$company_long);
$smarty->assign("company_lat",$company_lat);

//営業所 営業所が2か所以上の時に営業所を表示

if(count($branchList) > 1){
	$smarty->assign("branch_list",$branchList);
}

$smarty->assign("branch_id", $branch_id);
$smarty->assign("show_destinations", $show_destinations);
$smarty->assign("transport_routes",$transport_routes);
$smarty->assign("destinationList", $destinationList);


$smarty->assign("js",'
<script src="'.MAPBOX_GL_JS.'" type="text/javascript"></script>');

$css = "<link rel=\"stylesheet\" href=\"".MAPBOX_GL_CSS."\" type=\"text/css\" media=\"screen\">";
$smarty->assign("additional_css", $css);


$workingStatus = Data::getStatusByCompanyId($company_id);
$smarty->assign("working_status", $workingStatus[0]);

$smarty->assign("filename","realtimeDriverMap.html");
$smarty->display('template.html');

?>