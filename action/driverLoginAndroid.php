<?php

/**
 * ドライバー　ログイン Smart動態管理アプリからのログイン
 */
	// id がPOSTでわたって来ていたら、ログインの試み
//$_POST['login_id'] = "heikou";
//$_POST['passwd'] = "heikou";
//
//var_dump($_POST);
//echo json_encode($_POST);

	try {
		
		$is_garapagos=is_garapagos($carrier);
	
	        if($_POST['login_id']) {
	        	
		    	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	    		$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	        	
	    		$idArray=Driver::login($login_id,$passwd);
	
		    // 存在すれば、ログイン成功
	    	if(isSet($idArray[0]['last_name'])) {		
			
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
		        $company_login_id = Data::getCompanyLoginId($company_id);
		        $app_status = Data::getStatusAndInterval($company_id);
		        $group_id_data = Data::getCompanyGroupId($company_id);
		        $group_id = $group_id_data[0]->company_group_id;
		        $is_group_manager = $idArray[0]['is_group_manager'];
		        
		        print $idArray[0]['id'];	//0
				print ",";
				print $idArray[0]['last_name'];	//1
				print ",";
				print $idArray[0]['first_name'];	//2
				print ",";
				print $idArray[0]['login_id'];	//3
				print ",";
				print $passwd;	//4
				print ",";
				print $app_status[0]->action_1;	//5
				print ",";
				print $app_status[0]->action_2;	//6
				print ",";
				print $app_status[0]->action_3;	//7
				print ",";
				print $app_status[0]->action_4;	//8
				print ",";
				print $app_status[0]->distance;	//9
				print ",";
				print $app_status[0]->time;	//10
				print ",";
				print $group_id;	//11
				print ",";
				print $company_login_id[0]->email;	//12
				print ",";
				print $is_group_manager;	//13
				print ",";
				print $app_status[0]->track_always;	//14
				print ",";
				print $app_status[0]->accuracy;	//15
				
				
		        //Android GCMで利用するRegIDを登録
		        if($_POST['registration_id']){
		        	$registration_id = htmlentities($_POST['registration_id'],ENT_QUOTES, mb_internal_encoding());
		        	Driver::PutinGCM($idArray[0]['id'], $registration_id);
		        }
		        
		        if ($_POST['ios_device_token']){
		        	$ios_device_token = htmlentities($_POST['ios_device_token'],ENT_QUOTES, mb_internal_encoding());
		        	Driver::PutinIOSDeviceToken($idArray[0]['id'], $ios_device_token);
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