<?php
if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);	
		
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
		
		$driver_id = $idArray[0]['id'];
				
		foreach($concrete_driver_status_start_time as $key){
			if($key=='start_time'){	
				$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			}else{
				$_POST[$key]=trim( mb_convert_kana($_POST[$key], "s"));
				$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			}
		}
			
		$ip=getenv("REMOTE_ADDR");
		
		$ban_ip=Data::banip();
		
		//禁止リストにあるIPとの照合
		if($ip==$ban_ip){
				
			//メッセージ画面を表示する
			header('Location:index.php?action=message&situation=ban_ip');
		
		}else{
		
			try{
			
				//データチェック
				$form_validate = new Concrete_Validate();

				$errors = $form_validate->validate_form_concrete_driver_status_start_time($datas);
				
				if($errors){
					
					//データが不足していたらエラー出力
					echo 'INVALID_ACCESS';
					//$form_validate->show_form($errors);
				
				}else{
					
					//データベースへ投入
					$concrete_driver_status_id = concrete::concrete_driver_status_start_time($datas);
					
					$response = array ("concrete_driver_status_id" => $concrete_driver_status_id,
										"status" => "SUCCESS");
					
					echo json_encode( $response );
										
				}
			
			}catch(Exception $e){
			
				$message=$e->getMessage();
			}
		}
	}else{
	
		//ログインに失敗した場合の画面出力
		print "LOGIN_FAILED";
	}
	
}else{
	
	//IDがPOSTされていない場合
	print "NOT_POST_ID";
}
	?>