<?php

//メッセージ送信履歴表示画面　ドライバーアプリ用にJSONでメッセージを出力　
/*
$_POST['login_id']='kiryuuin';
$_POST['passwd']='hiroto';
*/
if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
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
				//print_r(get_class_methods('Data'));
				$datas['join_requests'] = Data::getJoinRequestsByCompanyId( $company_id );
				
//				var_dump($datas['join_requests']);
//				exit;
//				for($i = 0; $i < count($datas['messages']); ++$i){
//					$datas['messages'][$i]['cutted_message'] = mb_substr($datas['messages'][$i]['gcm_message'],0,15);
//				}
				
				if($datas){
	
					echo json_encode($datas);
						
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