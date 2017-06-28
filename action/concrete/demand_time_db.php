<?php
//請求時間の編集、DB挿入画面
$demand_array=array('demand_time_id');
$demand_time_array=array_merge($demand_time_array,$demand_array);

//$datas[データ名]でサニタイズされたデータが入っている
	foreach($demand_time_array as $key){
			$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		}
	
	//update予定の開始時間と終了時間
	$start_time = $datas['start_year']."-".$datas['start_month']."-". $datas['start_day']." ". $datas['start_hour'].":". $datas['start_minit'];
	$end_time = $datas['end_year']."-".$datas['end_month']."-". $datas['end_day']." ". $datas['end_hour'].":". $datas['end_minit'];
	try{
		
		$ip=getenv("REMOTE_ADDR");
		
		$ban_ip=Data::banip();
		
		//禁止リストにあるIPとの照合
		if($ip==$ban_ip){
		
			//メッセージ画面を表示する
			header('Location:index.php?action=message&situation=ban_ip');
		
		}else{
			
			//開始と終了が、既存のデータにかぶっていないか
			$form_validate=new Concrete_Validate();
			$data = $form_validate->validate_form_demand_time($datas,$start_time,$end_time);
			
			if($errors){
				
				$form_validate->show_form($errors);
				$form_validate->lasturl='index.php?action=/destination/putDestinationDb';
				
			}else{
				
				if(strtotime($start_time) < strtotime($end_time)){
					
					concrete::modified_demand_time($datas,$start_time,$end_time);
					foreach($demand_time_array as $key){
						unset($_SESSION['$key']);
					}
					
					header("Location:index.php?action=message_mobile&situation=update_demad_time_db");
				}
			}
		}
		
	
	}catch(Exception $e){
	
		$message=$e->getMessage();
	}
	
?>