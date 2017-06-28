<?php
/**
 * 配達開始ステータスのストップ時のデータ登録
 * テストデータ
 */
/*
$_POST['login_id']="kozaru";
$_POST['passwd']="123456";
$_POST['driver_id']=2;
$_POST['scenes_for_concrete_id']=132;
$_POST['concrete_attendance_id']=55;
$_POST['status']=1;
$_POST['remaining_water']=0;
$_POST['end_time']="2014/07/16 18:00";
*/

if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);	
		
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
		
		$driver_id = $idArray[0]['id'];
		
		$datas['end_time'] = $_POST['end'];
		
		foreach($end_concrete_scene as $key){
			if($key=='end_time'){
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

				$errors = $form_validate->validate_form_end_concrete_scene($datas);
				
				if($errors){
					
					//データが不足していたらエラー出力
					echo 'INVALID_ACCESS';
					//$form_validate->show_form($errors);
				
					foreach ($_POST as $key => $value) {
						$post_datas = "\"".$key."\" : \"".$value."\"";
					}
					Concrete::log_error( "end concrete scene [invalid access]", $post_datas);
									
				}else{
					
					concrete::end_concrete_driver_status($datas);
					
					$is_success = concrete::end_concrete_scene($datas);
				
//					if(! strlen(trim($datas['scenes_for_concrete_id']))){
//						echo "現場IDがありません";
//					}
					
					Concrete::log_error( "end concrete scene [post]", $post_datas);
				
					if ($is_success) {
						echo 'SUCCESS';
					} else {
						echo 'INVALID_ACCESS';
					}
										
				}
			
			}catch(Exception $e){
			
				$message=$e->getMessage();
				Concrete::log_error( "end concrete scene [ error ]", $e->getMessage());
				
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