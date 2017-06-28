<?php
/** 引き取られたコンテナのデータを表示する
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
		$dataList=Target::getPickedTarget($refine, $company_id);

	}catch(Exception $e){
		
			$message=$e->getMessage();
	}

	
list($data, $links)=make_page_link($dataList);
	
$smarty->assign("links",$links['all']);
$smarty->assign("driver_name",$driver_name[0]);
$smarty->assign("data",$data);
$smarty->assign("company_id",$company_id);


$smarty->assign("filename","target/getPickedTarget.html");
$smarty->display('template.html');

?>