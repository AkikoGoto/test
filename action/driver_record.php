<?php
/** 個別ドライバーの業務日誌確認画面　iphone,PC
 * ver2.0から追加
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
		
	//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認		
	driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);	
		
	//作業ステータス
	$workingStatus = Data::getStatusByCompanyId($company_id);
	$smarty->assign("working_status", $workingStatus[0]);
	
	try{
		
		//記録の取得
		$dataList=Driver::getStatusById($driver_id);
				
		//ドライバー名の取得
		$driver_name=Driver::getNameById($driver_id);
	
		}catch(Exception $e){
		
			$message=$e->getMessage();
	
		}

	
list($data, $links)=make_page_link($dataList[0]);
	
$smarty->assign("links",$links['all']);
$smarty->assign("driver_name",$driver_name[0]);
$smarty->assign("data",$data);

$smarty->assign("filename","driver_record.html");
$smarty->display('template.html');

?>