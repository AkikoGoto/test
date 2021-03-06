<?php
//営業所から帰ってくる(帰社)ステータス時の登録
if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);	
	
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
		
		$driver_id = $idArray[0]['id'];
		
		foreach($depart_leaving_time as $key){
			if($key=='depart_leaving_time'){	
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

				$errors = $form_validate->validate_form_depart_leaving_time($datas);
				
				if($errors){
					
					//データが不足していたらエラー出力
					echo 'INVALID_ACCESS';
									
				}else{
					
					//同じドライバーで同じ日付があるかどうか確認
					$check_depart_leaving_time = concrete::check_depart_leaving_time($datas);
					
					if(empty($check_depart_leaving_time)){
					
						//データベースへ投入
						concrete::depart_leaving_time($datas);
						echo'SUCCESS';
					
					}else{
						
						//同じドライバーで同じ日付のデータがあったら更新
						concrete::update_depart_leaving_time($datas);
						echo 'SUCCESS';
					}
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