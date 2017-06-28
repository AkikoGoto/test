<?php
//記録編集情報をデータベースへ投入 新規・編集はフラグで判断
$id_array=array('id','driver_id','recreated', 'company_id');
$keys=array();
$keys=array_merge($id_array,$driver_status_array,$chk_start_end_array);

	//$datas[データ名]でサニタイズされたデータが入っている
	foreach($keys as $key){
		if(!empty($_POST[$key])){
			$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		}else{
			$datas[$key]=null;
		}
	}

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

	//編集画面か、新規データ投入か

	if($datas['id']){
			
		$status=EDIT;
			
	}else{

		$status=NEWDATA;	
	
	}

	if(!empty($datas['start'])){

		$datas['start'] = $datas['recreated'];

	}elseif(!empty($datas['end'])){
		
		$datas['end'] = $datas['recreated'];
	
	}else{

		$datas=array_diff($datas, $chk_start_end_array);

	}


try{

		//データベースへ投入
		$topic=Driver::putInRecord($datas,$status);
		$driver_id=$datas['driver_id'];

		//セッション解除
		$_SESSION['driver_record_map_id'] = null;
		$_SESSION['driver_record_map_status'] = null;
		$_SESSION['driver_record_map_adderss'] = null;
		$_SESSION['driver_record_map_speed'] = null;
		$_SESSION['driver_record_map_sales'] = null;
		$_SESSION['driver_record_map_detail'] = null;
		$_SESSION['driver_record_map_from_year'] = null;
		$_SESSION['driver_record_map_from_month'] = null;
		$_SESSION['driver_record_map_from_day'] = null;
		$_SESSION['driver_record_map_from_hour'] = null;
		$_SESSION['driver_record_map_from_minit'] = null;
/*		$_SESSION['driver_record_map_from_seconds'] = null;*/

		$company_id = $datas['company_id'];

		header("Location:index.php?action=message_mobile&situation=after_edit_driver_record&driver_id=$driver_id&company_id=$company_id");

	}catch(Exception $e){
	
		die($e->getMessage());
	
	}


?>