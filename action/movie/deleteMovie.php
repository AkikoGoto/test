<?php
/**
 * 動画消去　画面なし
 * @author Akiko Goto
 * @since 2015/4/22
 * @version For Edison
 */

	$id=sanitizeGet($_GET['id']);
	$company_id=sanitizeGet($_GET['company_id']);
	
	//会社のユーザーIDと、編集するIDがあっているか確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

	$is_success = Movie::deleteMovie($id);
	if($is_success){	

		header('Location:index.php?action=message_mobile&situation=deleteData');
	
	}else{
		
		header('Location:index.php?action=message_mobile&situation=cannotDeleteData');
	}
    
?>