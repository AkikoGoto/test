<?php
/** 会社ごとのドライバーの位置をGoogleMapに表示する　iPhone ,PC
 * ver2.0から追加
 */

/**
 * 共通設定、セッションチェック読み込み　会社の管理者か、ドライバー本人のみ閲覧可能
 */

//公開用ユニークIDがあるか
if($_GET['unique_id']) {
	
	$unique_id = sanitizeGet($_GET['unique_id']);
	
} else if ( $_SESSION['user_id'] ) {
	
	//ユーザーIDでログインされているか
	$user_id = htmlentities( $_SESSION['user_id'], ENT_QUOTES, mb_internal_encoding());
	$is_viewers_map = true;
	
} else {
	//不正なアクセスです、というメッセージ	
	header("Location:index.php?action=message_mobile&situation=wrong_access");
	exit;	
}

try{
	
	if ( !$is_viewers_map ) {
		
		//公開用マップ
		$publiCompanyDatas = CompanyOptions::getPublicCompanyByUniqueId( $unique_id );
		
		//公開している会社であったら、以下の処理を行う
		if (!empty($publiCompanyDatas)) {
			
			$company_id = $publiCompanyDatas['company_id'];
			$visible_drivers = DriverOptions::getPublicDriversByCompanyId( $company_id );
			
		} else {
			
			//不正なアクセスです、というメッセージ					
			header("Location:index.php?action=message_mobile&situation=private_company");
			exit;
			
		}
		
	} else if ( $user_id ) {
		
		// 閲覧ユーザー用リアルタイムマップ
		$company_id = $_SESSION['company_id'];
		
		if ( CompanyOptions::CanUsersViewRealtimeMap( $company_id )) {
			
			$visible_drivers = Users::getVisibleDriversByUserId( $user_id );
			
		} else {
			
			//不正なアクセスです、というメッセージ					
			header("Location:index.php?action=message_mobile&situation=private_company");
			exit;
			
		}
		
	}
	
	if ( count( $visible_drivers ) ) {
		
		$smarty->assign("company_id",$company_id);
		$smarty->assign("key", md5(DB_NAME));
//		$smarty->assign("key", "b383d848d880cffe582151f23254ee0f");	//ローカルでのテスト用
		
		//作業ステータス
		$workingStatus = Data::getStatusByCompanyId($company_id);
		$smarty->assign("working_status", $workingStatus[0]);
			
	} else {
		
		$is_no_drivers = true;
		$smarty->assign("is_no_drivers", $is_no_drivers);
		
	}

}catch(Exception $e){

	$message=$e->getMessage();
}

$smarty->assign("filename","publicMap.html");

if (!$is_viewers_map) 
	$smarty->display('template_publicmap.html');
else
	$smarty->display('template.html');
?>