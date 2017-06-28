<?php
//出勤時データ(一番最初)の登録

/**
 * テストデータ
 */
/*
$_POST['login_id']="kozaru";
$_POST['passwd']="123456";
$_POST['loading_state']="5";
$_POST['driver_id']= 2;
$_POST['door_number']=2;
$_POST['attendance_time']=3;
$_POST['branch_name']="ほげ営業所";
$_POST['start_meter']=1050;
$_POST['date']="2014/07/16";
$_POST['loading_factory']="ほげ工場";
*/

if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);	
	
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
		
		$driver_id = $idArray[0]['id'];

		foreach($start_concrete_attendance as $key){
			if($key=='attendance_time'){	
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

				$errors = $form_validate->validate_form_concrete_attendances($datas);
				
				if($errors){
					
					//データが不足していたらエラー出力
					echo 'INVALID_ACCESS';
					//$form_validate->show_form($errors);
				
				}else{
					
					//同じドライバーで同じ日付のデータがあるか確認
					// デモ用として、なんども出勤できるようにする
					/*
					$check_date_data = concrete::check_date($datas);
					
					if(empty($check_date_data)){
					*/
				
						//出退勤のデータベースへ投入
						$concrete_attendance_id = concrete::insert_attendances($datas);
						
						//データベースへ投入
						concrete::start_meters($datas,$concrete_attendance_id);					
						
						$array=array('status' => 'SUCCESS',
									 'concrete_attendance_id' => $concrete_attendance_id);
						
						echo json_encode($array);
					/*
					}else{
						
						concrete::update_start_meters($datas,$check_date_data);
						
						//同じドライバーで同じ日付のデータがある場合のエラー出力
						echo 'IS_ALREADY_REGISTERED';
					}
					*/
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