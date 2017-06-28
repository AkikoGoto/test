<?php
/*
 * 配送先消去　画面なし
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */

	$id=sanitizeGet($_GET['id']);
	$company_id=sanitizeGet($_GET['company_id']);
	
	//会社のユーザーIDと、編集するIDがあっているか確認
	if($u_id==$company_id){	
		
		Alarms::deleteAlarm($id);	
	    
		header('Location:index.php?action=message_mobile&situation=deleteData');
	}else{
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
		
	}
    
?>