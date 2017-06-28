<?php
/**
 * アラーム消去　画面なし
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */

	$id=sanitizeGet($_GET['id']);
	$company_id=sanitizeGet($_GET['company_id']);
	
	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	if(!empty($branch_manager_id)){

		
		$alarm_info = Alarms::getAlarm($id);

		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $alarm_info[0]['driver_id']);
	
	}
	
	//アラーム情報削除
	Alarms::deleteAlarm($id);
    
	//「next_alarm」テーブルの削除
	Alarms::deleteNextAlarm($id);
	
	header('Location:index.php?action=message_mobile&situation=deleteData');
		
    
?>