<?php

//電話番号による自動ログイン　ドライバーアプリ用にJSONで出力　

$_POST['driver_tel']='090-1234-5678';

// 電話番号 がPOSTでわたって来ていたら、ログインの試み
try {
	if ($_POST['driver_tel']) {
	
		$driver_tel = htmlentities($_POST['driver_tel'],ENT_QUOTES, mb_internal_encoding());
	
		$idArray=Driver::loginWithTel($driver_tel);
	
		// 存在すれば、ログイン成功
		if(isSet($idArray[0]['id'])) {
	
			//ログインを記憶する場合、クッキーを設定
			if($_POST['autologin']){
			
				//1か月後にクッキーの有効期限が切れる
				setcookie('driver_id',$idArray[0]['id'],time()+60*60*24*30);
			
			}
		
			$company_id = $idArray[0]['company_id'];
			$_SESSION['driver_id'] = $idArray[0]['id'];
			$_SESSION['driver_last_name'] = $idArray[0]['last_name'];
			$_SESSION['driver_first_name'] = $idArray[0]['first_name'];
			$_SESSION['driver_company_id'] = $company_id;
			$app_status = Data::getStatusAndInterval($company_id);
			
			print $idArray[0]['id'];
			print ",";
			print $idArray[0]['last_name'];
			print ",";
			print $idArray[0]['first_name'];
			print ",";
			print $idArray[0]['login_id'];
			print ",";
			print $idArray[0]['passwd'];
			print ",";
			print $app_status[0]->action_1;
			print ",";
			print $app_status[0]->action_2;
			print ",";
			print $app_status[0]->action_3;
			print ",";
			print $app_status[0]->action_4;
			print ",";
			print $app_status[0]->distance;
			print ",";
			print $app_status[0]->time;
		
			//Android GCMで利用するRegIDを登録
			if($_POST['registration_id']){
				$registration_id = htmlentities($_POST['registration_id'],ENT_QUOTES, mb_internal_encoding());
				Driver::PutinGCM($idArray[0]['id'], $registration_id);
			}else{
			//print "GCM_BAD";
			}
	
		} else {
		
			// 存在しなければ、ログイン不成功
			print 'FAILED';
		
		}
		
	}else{
	
		//POSTデータがない場合、不正なアクセス
		print 'INVALID_ACCESS';
	
	}
// 例外の捕捉
} catch (PDOException $e) {
	die($e->getMessage() . 'データベースのエラーです。管理者に連絡してください');
}

?>