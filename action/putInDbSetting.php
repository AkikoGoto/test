<?php
//Webからの登録申し込み　会社情報をデータベースへ投入
	
$id_array=array('company_id');
$keys=array();
$keys=array_merge($id_array, $app_setting_array, $realtime_map_setting_array);

	//$datas[データ名]にサニタイズされたデータを入れる
	foreach($keys as $key){
		//携帯の場合、文字コード変換
		if(is_garapagos()){
			$_POST[$key]=mb_convert_encoding($_POST[$key], "UTF-8", "SJIS");
		}
		if ( $key == 'visible_driver_id' ) {
			//一般公開マップで表示させたいドライバーの一覧
			if (!empty($_POST[$key])) {
				foreach ($_POST[$key] as $value) {
					$datas[$key][]=htmlentities($value,ENT_QUOTES, mb_internal_encoding());
				}
			}
		} else if ( $key == 'viewed_driver_id' ) {
			//閲覧ユーザー毎に閲覧させたいドライバーの一覧
			if (!empty($_POST[$key])) {
				foreach ($_POST[$key] as $key_of_user_id => $value ) {
					foreach ( $value as $viewed_driver_id => $viewed_drivers) {
						$datas[$key][$key_of_user_id][$viewed_driver_id] = htmlentities( $viewed_drivers,ENT_QUOTES, mb_internal_encoding());
					}
				}
			}
		} else {
			$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		}
	}
	
	$ip=getenv("REMOTE_ADDR");
	
	$company_id = $datas['company_id'];
	if ( $company_id ) {
		//会社のユーザーIDと、編集するIDがあっているか確認
		user_auth( $u_id, $company_id);
	}
	
	try{

		//再度データチェック
		$form_validate=new Validate();
		$errors1=$form_validate->validate_form_app_status($datas);
		$errors2=$form_validate->validate_form_app_setting_status($datas);
		$errors = array_merge($errors1,$errors2);
		
		if($errors){

			$form_validate->show_form($errors);
			$form_validate->lasturl='index.php?action=putin';
		
		}else{
		
			//IDがあるかどうか、編集かどうか分ける 新規の登録はなし
			$url = "index.php?action=message_mobile&situation=after_edit_setting&company_id=$company_id";
			
			//移動時間をミリセカンドにする
			$time = $datas['time'] * 1000;
			$datas['time'] = $time;
			
			//データベースへ投入
			$retrurn_array = Data::putAppStatusIn( $datas, $status);
			// push 通知
			$result = Message::SendStatusChanging( $datas, $company_id);
			$status = $result[0];
			$error_info = $result[1];
			if($status == "SUCCESS"){
				
				$_SESSION['status_1'] = $datas['action_1'];
				$_SESSION['status_2'] = $datas['action_2'];
				$_SESSION['status_3'] = $datas['action_3'];
				$_SESSION['status_4'] = $datas['action_4'];
				$result_info = MESSAGE_SUCCESS;
				
			}else{
				
				$result_info = MESSAGE_FAILED;
				
			}
			
			header("Location:".$url);
			exit;
		}	
	
	}catch(Exception $e){
		
		die($e->getMessage());
	
	}

?>