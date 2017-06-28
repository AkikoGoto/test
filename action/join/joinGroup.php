<?php
/** 作業記録時のコメント投稿
 * ver2.7から追加
 * 画面なし
 */
//ドライバーのログインチェック
//ログインIDとパスワードでチェック

	$required_keys = array('company_group_id', 'last_name', 'first_name');
	$optional_keys = array('registration_id', 'ios_device_token', 'mobile_tel', 'mobile_email');
	$keys = array_merge( $required_keys, $optional_keys);
	
	$errors = array();
	// 必須項目ループ
	$isRequiredValuesPosted = true;
	foreach ($keys as $key) {
		$data = $_POST[$key];
		if (!empty($data)) {
			$datas[$key] = htmlentities($data,ENT_QUOTES, mb_internal_encoding());
		} else {
			// 値がないキーが必須項目のキーの場合エラーとする
			if (array_key_exists($key, $required_keys)) {
				$isRequiredValuesPosted = false;
			}
		}
	}
	// registration_id と ios_device_tokenがどちらもない場合、エラーとする　
	if (empty($datas['registration_id']) &&
		empty($datas['ios_device_token']) ) {
		$isRequiredValuesPosted = false;
	}

// 必須項目がポストされてるときのみ以下の処理を行う
if ($isRequiredValuesPosted) {
	
	// グループを検索
	// 管理者を検索
	// join_requestsに挿入
	//　管理者へ通知
	
	try{
		
		// グループを検索
		$company = Data::getCompanyByGroupId($datas['company_group_id']);
		$datas['company_id'] = $company[0]['company_id'];
		
		if(!$datas['company_id']) {
			//申請されたグループIDは存在しない
			print "NO_GROUP_ID";
			
		} else {
			
			$group_manager_id = $company[0]['driver_id'];
			
			/* =======================================
			 * 管理者の通知先を検索
			  =======================================*/
			// GCMのid
			$managerRegId = Driver::getGCM($group_manager_id);
			$group_manager_registration_id = $managerRegId[0]->registration_id;
			// APNSトークン
			$managerAPNSToken = Driver::getIOSDeviceToken($group_manager_id);
			$group_manager_apns_token = $managerAPNSToken[0]->ios_device_token;
			
			/* =======================================
			 * 営業所idを検索
			  =======================================*/
			$geographic = Branch::getBranches($datas['company_id']);
			$datas['geographic_id'] = $geographic[0]->id;
			
			/* =======================================
			 * ドライバーのアカウントを作成
			  =======================================*/
			$login_id = creatDriverLoginId($datas['company_group_id']);
			$passwd = RandomString();
			$datas['login_id'] = $login_id;
			$datas['passwd'] = $passwd;
			
			/* =======================================
			 * 参加申請者テーブルに挿入
			  =======================================*/
			Data::putJoinRequest($datas);
			
			//データ挿入
//			$isExistingDriver = Driver::getDriverByEmail($datas['mobile_email']);
//			if (!$isExistingDriver) {
			
				//通知するデータ
				$join_request = Data::getJoinRequestId($datas['registration_id'], $datas['ios_device_token'], $datas['company_id']);
				$join_request_id = $join_request[0]['id'];
				
				$sendDatas['join_request_id'] = $join_request_id;
				$sendDatas['company_group_id'] = $datas['company_group_id'];
				$sendDatas['registration_id'] = $group_manager_registration_id;
				$sendDatas['ios_device_token'] = $group_manager_apns_token;
				$sendDatas['first_name'] = $datas['first_name'];
				$sendDatas['last_name'] = $datas['last_name'];
				
				if ($join_request_id) {
					// push 通知
					$result = Message::SendJoinGroup($sendDatas);
					$status = $result[0];
					$error_info = $result[1];
					
					if($status == "SUCCESS"){
						echo 'SUCCESS';
					}else{
						echo 'FAILED_NOTIFICATION';
					}
					
				} else {
					echo 'DB_ERROR';
				}
				
//			} else {
//				//すでに同じドライバーが登録されている
//				echo 'EXISTING_DRIVER';
//			}
			
		}
		
	}catch(Exception $e){
		$message=$e->getMessage();
		//DBエラーの場合の画面出力
		print "DB_ERROR";
	}
	
}else{
	
	//IDがPOSTされていない場合
	print "INVALID_ACCESS";
}
?>