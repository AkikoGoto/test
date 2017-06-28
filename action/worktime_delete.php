<?php
/**
 * 日報のデータを消去
 */ 

	$id=sanitizeGet($_GET['id']);
	$company_id=sanitizeGet($_GET['company_id']);
	$driver_id=sanitizeGet($_GET['driver_id']);

	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $company_id);
	
		branch_manager_driver_auth($branch_manager_id,$driver_id);
		
	}else{		
		//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
		driver_company_auth($driver_id,$session_driver_id, $company_id,$u_id);
		
		//ドライバー本人がログインしている場合、会社が編集許可になっていなければエラー表示
		driver_editing_banned_check($driver_id,$session_driver_id, $company_id,$u_id);
	}
	
	Work::deleteWorkTimeById($id);	
    header('Location:index.php?action=message_mobile&situation=deleteData');
		
	
?>