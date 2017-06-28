<?php
/*
 * 配送先カテゴリー消去　画面なし
 * @author Akiko Goto
 * @since 2014/09/22
 * @version 4.4
 */

	$id=sanitizeGet($_GET['id']);
	$company_id=sanitizeGet($_GET['company_id']);
	
	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

	DestinationCategory::deleteDestinationCategory($id);	
    header('Location:index.php?action=message_mobile&situation=deleteData');
    
?>