<?php
ini_set( 'display_errors', 1 );
if($_GET['set_alarmId']){
	$set_alarmId=sanitizeGet($_GET['set_alarmId'],ENT_QUOTES);
}

if($_GET['set_driverId']){
	$set_driverId=sanitizeGet($_GET['set_driverId'],ENT_QUOTES);
}
	
	//アラート情報をデータベースへ投入
	$id_array=array('company_id','status','driver_name','alarm_time');
	$keys=array();
	$keys=array_merge($id_array,$alerms_array);
	
	//$datas[データ名]にサニタイズされたデータを入れる
	foreach($keys as $key){
		
		$data[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		
	}
	
	$ip=getenv("REMOTE_ADDR");
	//会社のユーザーIDと、編集するIDがあっているか確認
	
	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $data['company_id'], $branch_manager_id);
	
	if(!empty($branch_manager_id)){
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $data['driver_id']);
	
	}
	
		try{
			
			//再度データチェック
			$form_validate=new Validate();
		
			$errors=$form_validate->validate_form_driverCheck($data);
			
			if($errors){
		
				$form_validate->show_form($errors);
				$form_validate->lasturl='index.php?action=/destination/putDestinationDb';
		
			}else{
				
				if($data['status'] == 'NEW'){
					
					//データベースへ投入
					$id = Alarms::setAlarmDB($data);
					unset($_SESSION['setAlarm']);
					
					//「next_alarms」に投入
					Alarms::setNextAlarmDB($data,$id);
					//戻るリンクのために、会社IDをGETで送信
					$company_id = $data['company_id'];
	
					header("Location:index.php?action=message_mobile&situation=after_driver_customized&company_id=$company_id");
				
				}elseif($data['status'] == 'EDIT'){
					
					//データベースへupdate
					Alarms::UpdateAlarmDB($data,$set_alarmId,$set_driverId);
					$company_id = $data['company_id'];
					unset($_SESSION['setAlarm']);
					
					//「next_alarm」テーブルのアップデート
 					Alarms::updateNextAlarmDB($data,$set_alarmId);
 
					header("Location:index.php?action=message_mobile&situation=after_driverUpdate_customized&company_id=$company_id");
				}

				exit(0);
			}
		
		}catch(Exception $e){
			die($e->getMessage());
		
		}		
		
?>