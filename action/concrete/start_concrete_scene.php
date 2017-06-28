<?php

/**
 * テストデータ
 */
/*
$_POST['login_id']="kozaru";
$_POST['passwd']="123456";
$_POST['driver_id']= 1130;
$_POST['start']="8";
$_POST['concrete_attendance_id'] = 45;
$_POST['destination_company'] = '夢のような国';
$_POST['scene_name']='夢のような現場';
$_POST['number']="ぶ　1368";
$_POST['status']=1;
$_POST['quantity']="100";
$_POST['loading_factory']="夢のような工場";
$_POST['loading_state']=1;
$_POST['mix']=1;
*/

if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);	
		
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
		
		$driver_id = $idArray[0]['id'];
		
		$datas['start_time']=$_POST['start'];
		
		foreach($start_concrete_scene as $key){
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

				$errors = $form_validate->validate_form_start_concrete_scene($datas);
			
				if($errors){
					
					//データが不足していたらエラー出力
					echo 'INVALID_ACCESS';
					//$form_validate->show_form($errors);
				
				}else{
					
						//出退勤のデータベースへ投入
						$scenes_for_concrete_id = concrete::start_concrete_for_scenes($datas);
						
						concrete::start_concrete_driver_status($datas,$scenes_for_concrete_id);
						
						concrete::set_mix_in_concrete_attendances($datas);

						$array=array('status' => 'SUCCESS',
								'scenes_for_concrete_id' => $scenes_for_concrete_id);
						
						echo json_encode($array);
										
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