<?php

//電話番号による自動ログイン　ドライバーアプリ用にJSONで出力　
/*
$_POST['company_group_id']='23456';
$_POST['company_name']='123457';
$_POST['is_company']='false';
$_POST['postal']='123456';
$_POST['prefecture']='14';
$_POST[$city] = '横浜市';
//$_POST['ward']='';
$_POST['town']='台町';
$_POST['passwd']='test';
$_POST['address']='１２３５';
$_POST['tel']='09080009120';
$_POST['email']= $_POST['mobile_email']= 'qwertyuiop@gmail.com';
$_POST['contact_person_last_name']= $_POST['last_name']= 'ハム';
$_POST['contact_person_first_name'] = $_POST['first_name']= '一';
$_POST['contact_tel']= $_POST['mobile_tel']= '08053333336';
$_POST['is_ban_driver_editing']='false';
$_POST['registration_id']='APA91bFGO8Y07T_WFz-yvRuh3j4pIJf5JCbvq5QuPBMmXP-iMRozJTHthL-eYptrIxN7jpewWuthwHPE9jTm2LExLHLWnJTWIkTbrzx4jAM-v-NKxbNnfmvfiLbVdu7dY_4iNBgqPtme';
//$_POST['company_id']='100';
$_POST['latitude']='35.676966';
$_POST['longitude']='139.7588231';
*/

// 電話番号 がPOSTでわたって来ていたら、ログインの試み
try {
	
	//ちゃんと入力されてるかチェック
	if ( $_POST['company_name'] && $_POST['postal']
		 && $_POST['prefecture'] && $_POST['town'] && $_POST['tel'] && $_POST['email'] && $_POST['passwd']
		 && $_POST['contact_person_last_name'] && $_POST['contact_person_first_name'] && $_POST['contact_tel']
		 && ( $_POST['registration_id'] || $_POST['ios_device_token'])) {
		
		//初期登録時と、データ編集時で投入する値が違う
		$group_id = array ('company_group_id','contact_tel');
		$keys = $company_array;
		$keys = array_merge ($group_id, $keys, $geographic_array ,$drivers_array);

		//$datas[データ名]にサニタイズ(文字変換)されたデータを入れる
		foreach($keys as $key){
			
			//送信されてくるデータはすでにUTF-8になっている
			$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			
		}
		
		$today = date("mdGi");
		$datas['is_group_manager']='1';
		$passwd = $datas['passwd'];
		
		//送られてきたデータが正しい形式かバリデーション(確認する）
		try{
			
			//再度データチェック
			$form_validate=new Validate();
			
			$errors1=$form_validate->validate_form_company($datas);
			
			$errors2=$form_validate->validate_form_geo($datas);
			if($datas['id']){
				
				$errors3=$form_validate->validate_form_fromWeb_exited($datas);
				$errors4=Array();
							
			}else{
					
				$errors3=$form_validate->validate_form_fromWeb($datas);
				$errors4=$form_validate->validate_form_mail($datas);
				
			}
			
			$errors=array_merge($errors1,$errors2,$errors3);
			
			//メールアドレスが重複していないかチェック
			if($form_validate->validate_form_mail($datas)){
				
				print 'ALREADY_EXIST';
				
			} else if($errors){
			
				print 'INVALID_ACCESS';
			
			}else{
			
				//ログインＩＤが重複しているかチェック
				$datas['login_id'] = creatDriverLoginId($datas['company_group_id']);

				//送信されたグループIDを使っている会社を検索
				$companyWithGroupId = Data::getCompanyByGroupId($datas['company_group_id']);

				//ここで既にグループIDが使われているかチェック
				if (!$companyWithGroupId) {

					//使われてなかったら、
					//データベースへ投入
					$status = 'NEWDATA';
					$datas['driver_passwd'] = $passwd;
					$retrurn_array=Data::PutIn($datas,$status);
					
					// 新規登録時の案内メール送信
					mail_to_new_company_admin($datas);
					
					//保存されたデータを取得
					$idArray=Driver::login($datas['login_id'], $passwd);
					$company_id = $idArray[0]['company_id'];
		    		$app_status = Data::getStatusAndInterval($company_id);
			        $group_id_data = Data::getCompanyGroupId($company_id);
			        $group_id = $group_id_data[0]->company_group_id;
			        $is_group_manager = $idArray[0]['is_group_manager'];
				    // 存在すれば、ログイン成功
					if(isSet($idArray[0]['id'])) {
						
						$response = array (
						'SUCCESS', 
						$idArray[0]['id'],
						$idArray[0]['last_name'],
						$idArray[0]['first_name'],
						$idArray[0]['login_id'],
						$passwd,
						$app_status[0]->action_1,
						$app_status[0]->action_2,
						$app_status[0]->action_3,
						$app_status[0]->action_4,
						$app_status[0]->distance,
						$app_status[0]->time,
						$group_id,
						$datas['email'],
						$is_group_manager,
						$app_status[0]->track_always,
						$app_status[0]->accuracy );

						// Androidの場合
						if (empty($datas['ios_device_token'])) {
							$i = 0;
							foreach ($response as $value) {
								if ($i >0) {
									echo ',';
								}
								echo $value;
								$i++;
							}
						} else {
							//iPhoneの場合
							echo json_encode( $response );
							
						}
						
					}
					
				} else {
					//使われてたら・・・
					print 'EXISTING_GROUP';
				}
			
			}
			}catch(Exception $e){
				
				//die($e->getMessage());
				print "DB_ERROR";
				
			}

		
		
	}else{
	
		//POSTデータがない場合、不正なアクセス
		print 'INVALID_ACCESS';
	
	}
// 例外の捕捉
} catch (PDOException $e) {
	//die($e->getMessage() . 'データベースのエラーです。管理者に連絡してください');
	print "DB_ERROR";
}

?>