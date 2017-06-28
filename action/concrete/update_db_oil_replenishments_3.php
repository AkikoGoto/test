<?php
if(!empty($_POST['concrete_attendance_id'])){ 

	$concrete_attendance_id = htmlentities($_POST['concrete_attendance_id'],ENT_QUOTES, mb_internal_encoding());
	
	foreach($start_oil_replenishments as $key){
		$_POST[$key]=trim( mb_convert_kana($_POST[$key], "s"));
		$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}
		
	$start_time=$datas['start_time'];
	$end_time=$datas['end_time'];
	
	$ip=getenv("REMOTE_ADDR");
	
	$ban_ip=Data::banip();
	
	//禁止リストにあるIPとの照合
	if($ip==$ban_ip){
	
		//メッセージ画面を表示する
		header('Location:index.php?action=message&situation=ban_ip');
	
	}else{
		//データチェック
		$form_validate = new Concrete_Validate();
		$errors = $form_validate->validate_form_update_oil_replenishment($datas,$start_time,$end_time);
		
		if($errors){
			$form_validate->show_form($errors);
		
		}else{
			
			try{
				
				if($datas['status']){
					concrete::update_oil_replishment($datas);
				}else{
					concrete::admin_insert_oil_replishment($datas);
				}
				
				unset($_SESSION['oil_concrete_attendance_id']);
				header("Location:index.php?action=message_mobile&situation=oil_replenishment");
								
			}catch(Exception $e){
					
				$message=$e->getMessage();
			}
		}
	}
}
	?>