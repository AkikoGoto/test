<?php
//日報用データ確認画面
$id_array=array('id','driver_id','company_id');
$keys=array();
$keys=array_merge($id_array,$day_report_array);
//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		if(!empty($_POST[$key])){
			$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			
			if($key != "driver_id"){
				$_SESSION[$key] = NULL;
			}
		}
	}
	
	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $datas['company_id']);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $datas['driver_id']);
	
	}else{		
		//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
		driver_company_auth($datas['driver_id'],$session_driver_id,$datas['company_id'],$u_id);
		
		//ドライバー本人がログインしている場合、会社が編集許可を子なっていなければエラー表示
		driver_editing_banned_check($datas['driver_id'],$session_driver_id,$datas['company_id'],$u_id);
	}
	
	//編集画面か、新規データ投入か

	if($datas['id']){		
		$status = "EDIT";						
	}else{
		$status = "NEWDATA";	
	}

try{

		//データベースへ投入
		DayReport::putInDayReportData($datas, $status);
		
		$driver_id=$datas['driver_id'];

		$company_id = $datas['company_id'];
		header("Location:index.php?action=message_mobile&situation=after_day_report&driver_id=$driver_id&company_id=$company_id");

	}catch(Exception $e){
	
		die($e->getMessage());
	
	}
	

	
?>