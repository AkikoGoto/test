<?php
//記録データ確認画面

$id_array=array('id','driver_id','company_id');
$keys=array();
$keys=array_merge($id_array,$worktime_array, $work_array);

//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		if($_POST[$key] != null){
			$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			
			if($key != "driver_id"){

				$_SESSION[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			
			}
			
		}
	}
	//編集画面か、新規データ投入か
	if($_POST['edit']){
		
		//編集画面の場合は、記録番号を投入
		$datas['id']=htmlentities($_POST['id'],ENT_QUOTES, mb_internal_encoding());
		
	}

//開始時刻、終了時刻
$start_worktime = $datas['time_from_year']."-".$datas['time_from_month']."-". $datas['time_from_day']." ". $datas['time_from_hour']
	.":". $datas['time_from_minit'].":". $datas['time_from_second'];
$end_worktime = $datas['time_to_year']."-".$datas['time_to_month']."-". $datas['time_to_day']." ". $datas['time_to_hour']
	.":". $datas['time_to_minit'].":". $datas['time_to_second'];

//作業ステータス
$workingStatus = Data::getStatusByCompanyId($datas['company_id']);
$smarty->assign("working_status", $workingStatus[0]);

try{
	$_SESSION['worktime_id'] = $datas['id'];
	$_SESSION['worktime_status'] = $datas['status'];
	$_SESSION['worktime_from_year'] = $datas['time_from_year'];
	$_SESSION['worktime_from_month'] = $datas['time_from_month'];
	$_SESSION['worktime_from_day'] = $datas['time_from_day'];
	$_SESSION['worktime_from_hour'] = $datas['time_from_hour'];
	$_SESSION['worktime_from_minit'] = $datas['time_from_minit'];
	$_SESSION['worktime_from_second'] = $datas['time_from_second'];
	$_SESSION['worktime_to_year'] = $datas['time_to_year'];
	$_SESSION['worktime_to_month'] = $datas['time_to_month'];
	$_SESSION['worktime_to_day'] = $datas['time_to_day'];
	$_SESSION['worktime_to_hour'] = $datas['time_to_hour'];
	$_SESSION['worktime_to_minit'] = $datas['time_to_minit'];
	$_SESSION['worktime_to_second'] = $datas['time_to_second'];
/*	$_SESSION['worktime_to_seconds'] = $datas['time_to_seconds'];*/

	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $datas['company_id']);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $datas['driver_id']);
	
	}else{

		//会社のユーザーIDと、編集するIDがあっているか確認
		driver_company_auth($datas['driver_id'],$session_driver_id,$datas['company_id'],$u_id);
		
		//ドライバー本人がログインしている場合、会社が編集許可を子なっていなければエラー表示
		driver_editing_banned_check($datas['driver_id'],$session_driver_id,$datas['company_id'],$u_id);
	}
				
	$ip=getenv("REMOTE_ADDR");

	$ban_ip=Data::banip();
	
	//禁止リストにあるIPとの照合
	if($ip==$ban_ip){

	//メッセージ画面を表示する
      header('Location:index.php?action=message&situation=ban_ip');

	}else{


		//入力データ検証
		//開始と終了が、既存のデータにかぶっていないか
		$form_validate=new Validate();
		
		$errors=$form_validate->validate_worktime($start_worktime, $end_worktime, $datas);
		
		if($errors){
	
				$form_validate->show_form($errors);
				$form_validate->lasturl='index.php?action=worktime';
				
		}else{		
			//編集データの取得
			
	
			if($datas['id']){
				$record_data=Work::getWorkRecords($datas);
			}
			
			foreach($datas as $key => $value){
				
					//	$value=mb_convert_encoding($value, "UTF-8", "SJIS");									
				$smarty->assign("$key",$value);
	
			}
			
			$smarty->assign("record_data",$record_data);
			$smarty->assign("created",$created);
			$smarty->assign("start",$start_worktime);
			$smarty->assign("end",$end_worktime);			
			$smarty->assign("filename","worktime_confirm.html");
			$smarty->assign("target","worktime_putindb");
			$smarty->display("template.html");	
			
		}
		

			
	}
				
			
	}catch(Exception $e){

		die($e->getMessage());
		
	}
	

	
?>