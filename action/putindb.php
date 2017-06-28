<?php
//Webからの登録申し込み　会社情報をデータベースへ投入
	
$id_array=array('id');
$keys=array();

	//初期登録時と、データ編集時で投入する値が違う
	if($_POST['id']){

		$keys=array_merge($id_array,$company_array,$geographic_array);
		
	}else{

		$keys=array_merge($id_array,$company_array,$registration_array,$geographic_array);
			
	}

	//$datas[データ名]にサニタイズされたデータを入れる

	foreach($keys as $key){
		
		//携帯の場合、文字コード変換
		if(is_garapagos()){
			$_POST[$key]=mb_convert_encoding($_POST[$key], "UTF-8", "SJIS");				
		}

		//送信されてくるデータはすでにUTF-8になっている
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		
	}
	//serviceがある場合
	
	
	if($_POST['service']){
		//serviceのみ配列
		foreach($_POST['service'] as $services){
		  $datas['services'][]=htmlentities($services,ENT_QUOTES, mb_internal_encoding());
		}
	}

	$ip=getenv("REMOTE_ADDR");
	

	if ($datas['id']) {
		//会社のユーザーIDと、編集するIDがあっているか確認
		user_auth($u_id,$datas['id']);
	} else {
		//初会社登録
		$datas['is_group_manager'] = 1;
		$datas['login_id'] = creatDriverLoginId($datas['company_group_id']);
	}
		
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
	
	

		
	$errors=array_merge($errors1,$errors2,$errors3,$errors4);

	if($errors){

		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=putin';
		
		}else{
		
			//IDがあるかどうか、編集かどうか分ける 新規の登録はなし
			$url = null;
			if($datas['id']){
				$status = 'EDIT';
				$url = "index.php?action=message_mobile&situation=after_edit";
			} else {
				$status = 'NEWDATA';
				$url = "index.php?action=message_mobile&situation=after_put_new_company";
			}
			
			//管理者用アカウント発行
			$datas['driver_passwd'] = $datas['passwd'];
			
			//データベースへ投入
			$retrurn_array=Data::PutIn($datas,$status);
			
			// 新規登録時の案内メール送信
			if(!$datas['id']){
				mail_to_new_company_admin($datas);
			}
			
			/*******************
			 * ログイン及びログイン情報の保存
			 ****************/
			
			//新規会社情報追加の場合
			if (!$datas['id']) {
				
				$id = $datas['email'];
				$pass = $datas['passwd'];
				$idArray=Data::login($id, $pass);
		
			    // 存在すれば、ログイン成功
		    	if(isSet($idArray[0]['id'])) {
					//ログインを記憶する場合、クッキーを設定
			     	if($_POST['autologin']){
		
						//1か月後にクッキーの有効期限が切れる
						setcookie('u_id',$idArray[0]['id'],time()+60*60*24*30);
				
					}
		    		
		        	$_SESSION['u_id'] = $idArray[0]['id'];
			        $_SESSION['u_company_name'] = $idArray[0]['company_name'];
					$u_id=$_SESSION['u_id'];
			        $u_company_name = $_SESSION['u_company_name'];
		    	  	$message= LOGIN_SUCCESS;
		    	  	
	   				//LITE版かどうか　ログインした瞬間に、通常版かLITE版か判定するため
					$is_lite = Lite::isLite($u_id);
					if($is_lite){
						$smarty->assign('is_lite', $is_lite);
					}
		    	  	
					
					
			 	// 存在しなければ、ログイン不成功
			    } else {
			    	
			    	$faillogin = DATE_ERROR_FAILLOGIN;
			   		header("$faillogin");
				    exit;
			    }
			}
						
			header("Location:".$url);
			exit;
		}	
	
	}catch(Exception $e){
	die($e->getMessage());
	
	}

?>