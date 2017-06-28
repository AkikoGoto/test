<?php
//Webからの登録申し込み　会社情報をデータベースへ投入
$id_array=array('company_id');
$keys=array();
$keys=array_merge($id_array,$app_setting_array);
	//$datas[データ名]にサニタイズされたデータを入れる
	foreach($keys as $key){
		//携帯の場合、文字コード変換
		if(is_garapagos()){
			$_POST[$key]=mb_convert_encoding($_POST[$key], "UTF-8", "SJIS");
		}
		//送信されてくるデータはすでにUTF-8になっている
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}
	
	$ip=getenv("REMOTE_ADDR");
	
	if ($datas['company_id']) {
		//会社のユーザーIDと、編集するIDがあっているか確認
		user_auth($u_id,$datas['company_id']);
	}
	
try{
	
	//再度データチェック
	$form_validate=new KomatsuValidate();
	$errors=$form_validate->komatsu_validate_form_app_status($datas);
	
	if($errors){
		
		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=putin';
		
		}else{
			
			//IDがあるかどうか、編集かどうか分ける 新規の登録はなし
			$url = "index.php?action=message_mobile&situation=after_edit";
			
			//移動時間をミリセカンドにする
			$time = $datas['time'] * 60 * 1000;
			$datas['time'] = $time;
			
			// 非公開で決め打ち
			$datas['is_public'] = false;
			
			//データベースへ投入
			$retrurn_array=Data::putAppStatusIn($datas,$status);
			KomatsuData::komatsu_saveCompanyOptions($datas);
			// push 通知
			$result = Message::SendStatusChanging($datas, $datas['company_id']);
			$status = $result[0];
			$error_info = $result[1];
			if($status == "SUCCESS"){
				$_SESSION['status_1'] = $datas['action_1'];
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