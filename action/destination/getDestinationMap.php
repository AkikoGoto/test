<?php
/** 会社ごとのドライバーの位置と配送先をGoogleMapに表示する　iPhone ,PC
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

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);


try{


	//配送先の取得
	$destinationList=Destination::searchDestinations($company_id, null);	
	
	//配送先カテゴリーの取得
	$categoryList = DestinationCategory::searchDestinationCategories($company_id);

	//Mapの中心点座標取得
	$company_latlng=Branch::getLatLng($company_id);
	$company_lat=$company_latlng['latitude'];
	$company_long=$company_latlng['longitude'];

}catch(Exception $e){

	$message=$e->getMessage();
}

$smarty->assign("company_id",$company_id);
$smarty->assign("destinationList", $destinationList);
$smarty->assign("categoryList", $categoryList);

//会社の座標
$smarty->assign("company_long",$company_long);
$smarty->assign("company_lat",$company_lat);


//OpenStreetMap OpenLayers
$smarty->assign("js",'<script src="'.OPEN_LAYERS.'" type="text/javascript"></script>
<script src="'.MAPBOX_JS.'" type="text/javascript"></script>');

$smarty->assign("onload_js","onload=\"initialize($company_lat,$company_long)\"");


$smarty->assign("filename","destination/getDestinationMap.html");
$smarty->display('template.html');

?>