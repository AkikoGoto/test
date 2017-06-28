<?php
//請求時間の編集、確認画面

$demand_array=array('demand_time_id');
$demand_time_array=array_merge($demand_time_array,$demand_array);

foreach($demand_time_array as $key){
	$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	$_SESSION[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
}
//修正前の開始時刻、終了時刻
//$before_repair_start_time = $datas['start_year']."-".$datas['start_month']."-". $datas['start_day']." ". $datas['before_repair_start_hour'].":". $datas['before_repair_start_minit'];
//$before_repair_end_time = $datas['end_year']."-".$datas['end_month']."-". $datas['end_day']." ". $datas['after_repair_end_hour'].":". $datas['after_repair_end_minit'];

//update予定の開始時間と終了時間
$start_time = $datas['start_year']."-".$datas['start_month']."-". $datas['start_day']." ". $datas['start_hour'].":". $datas['start_minit'];
$end_time = $datas['end_year']."-".$datas['end_month']."-". $datas['end_day']." ". $datas['end_hour'].":". $datas['end_minit'];

	try{
			
		$_SESSION['demand_from_year'] = $datas['start_year'];
		$_SESSION['demand_from_month'] = $datas['start_month'];
		$_SESSION['demand_from_day'] = $datas['start_day'];
		$_SESSION['demand_from_hour'] = $datas['start_hour'];
		$_SESSION['demand_from_minit'] = $datas['start_minit'];
		
		$_SESSION['demand_to_year'] = $datas['end_year'];
		$_SESSION['demand_to_month'] = $datas['end_month'];
		$_SESSION['demand_to_day'] = $datas['end_day'];
		$_SESSION['demand_to_hour'] = $datas['end_hour'];
		$_SESSION['demand_to_minit'] = $datas['end_minit'];
		
		$ip=getenv("REMOTE_ADDR");
		
		$ban_ip=Data::banip();
		
		//禁止リストにあるIPとの照合
		if($ip==$ban_ip){
		
			//メッセージ画面を表示する
			header('Location:index.php?action=message&situation=ban_ip');
		
		}else{
			
			//データチェック
			$form_validate = new Concrete_Validate();
						
			$errors = $form_validate->validate_form_demand_time($datas,$start_time,$end_time);
			
				if($errors){
					
					$form_validate->show_form($errors);
										
				}else{
			
						foreach($datas as $key => $value){
							$smarty->assign("$key",$value);
						}
						$smarty->assign("before_repair_start_hour",$datas['before_repair_start_hour']);
						$smarty->assign("before_repair_start_minit",$datas['before_repair_start_minit']);
						$smarty->assign("after_repair_end_hour",$datas['after_repair_end_hour']);
						$smarty->assign("after_repair_end_minit",$datas['after_repair_end_minit']);
						
						$smarty->assign("date",$datas['date']);
						$smarty->assign("concrete_attendance_id",$datas['concrete_attendance_id']);
						$smarty->assign("demand_time_id",$datas['demand_time_id']);
						$smarty->assign("data",$data);
						$smarty->assign("datas",$datas);
						$smarty->assign('session_address',$session_address);
						$smarty->assign("links",$links['all']);
						$smarty->assign("start_time",$start_time);
						$smarty->assign("end_time",$end_time);
						$smarty->assign("filename","concrete/demand_time_confirm.html");
						$smarty->display("template.html");
				
			
			}
		
		}
	}catch(Exception $e){
	
		$message=$e->getMessage();
	}
	
?>