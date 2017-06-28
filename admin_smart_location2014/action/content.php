<?php

/**
 *管理画面　投稿詳細画面 
 */

/**
 * 共通設定、セッションチェック読み込み
 */
require_once('admin_check_session.php');

try{

//ランキングなど表示のため、セッションでスレッドIDが来ているか、来ていなければGETの値

	if($_GET['id']){	
		$id=$_GET['id'];
		}
	
	if($_GET['from_web']){	
		$from_web=$_GET['from_web'];
		}	
	
	$dataList=Data::getById($id,$from_web);
	
	//請求書情報の取得
	$invoices_data = Data::searchInvoices($id);
	
	//サービス名の取得

// 	$service_ids=Data::getServiceForCompany($id);
// 	var_dump($service_ids);
// 	//サービスがあれば、次の処理
// 	if($service_ids){
// 		for($i=0, $num_service_ids=count($service_ids);$i<$num_service_ids;$i++){ 
			
// 			$service_names[]=Data::getServiceNameEach($service_ids[$i]['service_id']);
		
// 		}
		
// 		foreach($service_names as $service_name){
// 			$service_names_each[]=$service_name[0]['service_name'];			
// 		}
// 	}

	//登録ドライバー数をカウント
	$driverNumber_data=Driver::countDrivers($id);
	$driverNumberActual=$driverNumber_data['COUNT(*)'];
		
	 }catch(Exception $e){
	
		$message=$e->getMessage();
	}


list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data[0]);
$smarty->assign("driver_number_actual",$driverNumberActual);
$smarty->assign("invoices_data",$invoices_data[0]);
//$smarty->assign("service_names_each",$service_names_each);

//画像
// $image_info = make_image_array($dataList);		
// $smarty->assign('image_info',$image_info);

//ページのタイトルに、スレッドのタイトルをアサイン
$smarty->assign('page_title',$dataList[0]->company_name);


$smarty->assign("filename","content.html");
$smarty->display("template.html");

?>