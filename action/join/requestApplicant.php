<?php

//メッセージ送信履歴表示画面　ドライバーアプリ用にJSONでメッセージを出力　

if(	$_POST['login_id'] &&
	$_POST['passwd'] &&
	$_POST['join_request_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	$join_request_id = htmlentities($_POST['join_request_id'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);
	
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
		
		$is_group_manager = $idArray[0]['is_group_manager'];
		
		if ($is_group_manager != 0) {
			
			$driver_id = $idArray[0]['id'];
			$company_id = $idArray[0]['company_id'];
	       	$_SESSION['driver_id'] = $driver_id;
	        $_SESSION['driver_last_name'] = $idArray[0]['last_name'];
	        $_SESSION['driver_first_name'] = $idArray[0]['first_name'];
	        $_SESSION['driver_company_id'] = $company_id;
				
			try{
				
				$datas = Data::getJoinRequestByRequestId( $join_request_id );
				$applicant['applicant'] = $datas[0];
				
				if($applicant){
	
					echo json_encode($applicant);
						
				}else{
	
					print "NO_DATA";
						
				}
				
			 }catch(Exception $e){
					
				$message=$e->getMessage();
				//DBエラーの場合の画面出力
				print "DB_ERROR";
	
			 }
			 
		} else {
			//管理者でない場合
			echo 'NOT_GROUP_MANAGER';
		}
		
	}else{

		//ログインに失敗した場合の画面出力
		print "LOGIN_FAILED";
	}
}else{

	//IDがPOSTされていない場合
	print "INVALID_ACCESS";
}
	
?>