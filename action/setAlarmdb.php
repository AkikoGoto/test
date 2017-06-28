<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php

if($_GET['set_alarmId']){
	$set_alarmId=sanitizeGet($_GET['set_alarmId'],ENT_QUOTES);
}

if($_GET['set_driverId']){
	$set_driverId=sanitizeGet($_GET['set_driverId'],ENT_QUOTES);
}

	//アラート情報をデータベースへ投入
	$id_array=array('company_id','status','driver_name');
	$keys=array();
	$keys=array_merge($id_array,$alerms_array);
	
	//$datas[データ名]にサニタイズされたデータを入れる
	foreach($keys as $key){
		
		$data[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		
	}

	//メール送信情報
	$to = "koichi.saito@onlineconsultant.jp" ;//送信先
	$driver_name = $data['driver_name'];
	$middle_language = '様は、指定された';
	$email = 'web@onlineconsultant.jp';
	$contact_number = date('YmdGis');//問い合わせ番号
	$from_address = $email; //どこから来たか（送信元）
	$add_header = "From:$from_address";
	
	//メールの送信先の人が指定した時間にいるかどうか
	if($data['alert_when_there']){
		
		//題名
		$subject = "管理者が設定した住所にいました。";
		$mystery = 'にいました。';
		 
	}elseif($data['alert_when_not_there']){
		
		//題名
		$subject = "管理者が設定した住所にいませんでした。";
		$mystery = 'にいませんでした。';
		 
	}
	
	//メールの本文
	$message = <<<EOM
	
---------------------------------
【アラーム番号】
$contact_number
---------------------------------
	
【ドライバー名】
$driver_name
	
【ご連絡事項】
$driver_name$middle_language$address$mystery
	
EOM;
	
	$from_address = $email; //どこから来たか（送信元）
	
	$ip=getenv("REMOTE_ADDR");
	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id,$data['company_id']);
	
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
				Alarms::setAlarmDB($data);
				//unset($_SESSION['destination_data']);
				
				//戻るリンクのために、会社IDをGETで送信
				$company_id = $data['company_id'];
				
				$subject  = mb_convert_encoding( $subject, "iso-2022-jp", "auto" );
				$message  = mb_convert_encoding( $message, "iso-2022-jp", "auto" );
				
				//メールを送る関数
				if(mb_send_mail($to,$subject,$message,$add_header)){
					echo 'ok';
				}else{
					echo 'NG';
				}
				
				//header("Location:index.php?action=message_mobile&situation=after_driver_customized&company_id=$company_id");
				
				}elseif($data['status'] == 'EDIT'){
					
					Alarms::UpdateAlarmDB($data,$set_alarmId,$set_driverId);
					$company_id = $data['company_id'];
					
					$subject  = mb_convert_encoding( $subject, "iso-2022-jp", "auto" );
					$message  = mb_convert_encoding( $message, "iso-2022-jp", "auto" );
					
					//メールを送る関数
					if(mb_send_mail($to,$subject,$message,$add_header)){
						echo 'ok';
					}else{
						echo 'NG';
					}
					
					
					//header("Location:index.php?action=message_mobile&situation=after_driverUpdate_customized&company_id=$company_id");
				}

				exit(0);
			}
		
		}catch(Exception $e){
			die($e->getMessage());
		
		}		
		
?>