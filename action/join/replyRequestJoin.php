<?php
/** 作業記録時のコメント投稿
 * ver2.7から追加
 * 画面なし
 */


//ドライバーのログインチェック
//ログインIDとパスワードでチェック
	$keys = array('join_request_id', 'join_accept', 'company_group_id');
	$errors = array();
	foreach ($keys as $key) {
		$data = $_POST[$key];
		if (!empty($data)) {
			$datas[$key] = htmlentities($data,ENT_QUOTES, mb_internal_encoding());
		} else {
			$errors[$key] = '入力されていない項目があります';
		}
	}
	
if (count($errors) === 0) {
	
	// 承認されたかチェック
	// 承認されてたら・・・
		//　申請者情報を取得
		// driverテーブルへ挿入
		// join_requestテーブルから削除
		//　申請者へログインアカウントを通知
	// 承認されてたら・・・
		// join_requestテーブルから削除
		
	try{
		
		// 承認されてたら・・・
		if ($datas['join_accept'] != "DECLINE") {
			// 承認されてたら・・・
			//　申請者情報を取得
			$joinRequestDatas = Data::getJoinRequestDataId($datas['join_request_id']);
			
			//申請者情報があるか
			if ($joinRequestDatas) {
				$joinRequestData = $joinRequestDatas[0];
				$status = 'NEWDATA';
				
				//すでに登録されているか
//				$isExistingDriver = Driver::getDriverByEmail($joinRequestData['mobile_email']);
//				if (!$isExistingDriver) {
					// driverテーブルへ挿入
					Driver::registerDriverFromApp($joinRequestData, $status);
					
					//action, intervalを取得
					$interval = Data::getStatusAndInterval($joinRequestData['company_id']);
					$intervalDatas = $interval[0];
					
					// 会社ID
					$company_id = $joinRequestData['company_id'];
					
					//drivers id　を取得
					//ログイン状態にする
					$idArray = Driver::login($joinRequestData['login_id'], $joinRequestData['passwd']);
					$driver_id = $idArray[0]['id'];
					
					// join_requestテーブルから削除
					Data::deleteJoinRequestById($datas['join_request_id']);
					
					//グループIDを取得
					$group_id = $datas['company_group_id'];
					
					//グループ管理者か
					$is_group_manager = $idArray[0]['is_group_manager'];
//			        $groupIdData = Data::getCompanyGroupId($joinRequestData['company_id']);
//			        $group_id = $groupIdData[0]->company_group_id;
					
					//会社ログインID
			        $company_login_id = Data::getCompanyLoginId($company_id);
					
			        //セッション
					$_SESSION['driver_id'] = $driver_id;
			        $_SESSION['driver_last_name'] = $idArray[0]['last_name'];
			        $_SESSION['driver_first_name'] = $idArray[0]['first_name'];
			        $_SESSION['driver_company_id'] =  $idArray[0]['company_id'];
					
			        //送信するデータ
					$sendDatas['company_group_id'] = $group_id;
					$sendDatas['driver_id'] = $driver_id;
					$sendDatas['registration_id'] = $joinRequestData['registration_id'];
					$sendDatas['ios_device_token'] = $joinRequestData['ios_device_token'];
					$sendDatas['first_name'] = $joinRequestData['first_name'];
					$sendDatas['last_name'] = $joinRequestData['last_name'];
					$sendDatas['login_id'] = $joinRequestData['login_id'];
					$sendDatas['passwd'] = $joinRequestData['passwd'];
					$sendDatas['action_1'] = $intervalDatas->action_1;
					$sendDatas['action_2'] = $intervalDatas->action_2;
					$sendDatas['action_3'] = $intervalDatas->action_3;
					$sendDatas['action_4'] = $intervalDatas->action_4;
					$sendDatas['distance'] = $intervalDatas->distance;
					$sendDatas['time'] = $intervalDatas->time;
					$sendDatas['is_group_manager'] = $is_group_manager;
					$sendDatas['company_login_id'] = $company_login_id[0]->email;
					
					//　申請者へログインアカウントを通知
					// push 通知
						$result = Message::SendAcceptJoinGroup($sendDatas);
						$status = $result[0];
						$error_info = $result[1];
						
						if($status == "SUCCESS"){
							echo 'SUCCESS_ACCEPT';
						}else{
							echo 'FAILED_NOTIFICATION';
						}
					
//				} else {
//					//すでに同じドライバーが登録されている
//					echo 'EXISTING_DRIVER';
//				}
				
			} else {
				//申請者が取得できない
				echo 'INVALID_JOIN_REQUEST';
			}
			
		} else {
			// 承認されてなかったら・・・
			// join_requestテーブルから削除
			Data::deleteJoinRequestById($datas['join_request_id']);
			echo 'SUCCESS_REJECT';
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