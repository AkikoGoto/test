<?php

/**
 *　ドライバー情報一覧
 */

/**
 * 共通設定、セッションチェック読み込み
 */

try{

	
	$id=sanitizeGet($_GET['id']);
	$dataList=Driver::getById($id, null);

	$company_id = $dataList[0]->company_id;
	
	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id, $company_id);
	
	
	 }catch(Exception $e){
	
		$message=$e->getMessage();
	}


list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data[0]);
$smarty->assign("filename","viewDriver.html");
$smarty->display("template.html");

?>