<?php
if(!empty($_POST['concrete_attendance_id'])){ 

	$concrete_attendance_id = htmlentities($_POST['concrete_attendance_id'],ENT_QUOTES, mb_internal_encoding());
	
	foreach($update_oil_replinishment as $key){
		$_POST[$key]=trim( mb_convert_kana($_POST[$key], "s"));
		$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}
		
	//update予定の開始時間と終了時間
	$start_time = $datas['check_year']."-".$datas['check_month']."-". $datas['check_day']." ". $datas['start_time_hour'].":". $datas['start_time_minit'];
	$end_time = $datas['check_year']."-".$datas['check_month']."-". $datas['check_day']." ". $datas['end_time_hour'].":". $datas['end_time_minit'];
	$datas['start_time']=$start_time;
	$datas['end_time']=$end_time;	
	
	$_SESSION['oil_from_hour'] = $datas['start_time_hour'];
	$_SESSION['oil_from_minit'] = $datas['start_time_minit'];
	$_SESSION['oil_to_hour'] = $datas['end_time_hour'];
	$_SESSION['oil_to_minit'] = $datas['end_time_minit'];
	$_SESSION['oil_replenishment'] = $datas['oil_replenishment'];
	$_SESSION['oil_concrete_attendance_id'] = $datas['concrete_attendance_id'];
	
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
				
					foreach($datas as $key => $value){
						$smarty->assign("$key",$value);
					}
												
					$smarty->assign("filename","concrete/update_confirm_oil_replenishments_3.html");
					$smarty->display('template.html');
				
				
			}catch(Exception $e){
					
				$message=$e->getMessage();
			}
		}
	}
}
	?>