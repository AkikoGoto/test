<?php
//日報用データ確認画面
$id_array=array('id','company_id','target_set_date');
$keys=array();
$keys=array_merge($id_array,$target_array);
//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
//		if(!empty($_POST[$key])){
			$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			$_SESSION[$key] = NULL;
//		}
	}
	
		
	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id,$datas['company_id']);		
		
	//編集画面か、新規データ投入か

	if($datas['id']){		
		$status = "EDIT";						
	}else{
		$status = "NEWDATA";	
	}

try{

		//データベースへ投入
		Target::putInTargetData($datas, $status);
		
		$driver_id=$datas['driver_id'];

		$company_id = $datas['company_id'];
		header("Location:index.php?action=message_mobile&situation=after_target_edit&driver_id=$driver_id&company_id=$company_id");

	}catch(Exception $e){
	
		die($e->getMessage());
	
	}
	

	
?>