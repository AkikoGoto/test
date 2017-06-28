<?php

/**
 * 管理画面　ログイン
 */


	// id がPOSTでわたって来ていたら、ログインの試み
	try {
	     if($_POST['id']) {
	
	     	$id = htmlentities($_POST['id'],ENT_QUOTES, mb_internal_encoding());
	    	$pass = htmlentities($_POST['pass'],ENT_QUOTES, mb_internal_encoding());

	    	$idArray=Data::login($id, $pass);
	
		    // 存在すれば、ログイン成功
	    	if(isSet($idArray[0]['id'])==ADMIN_ID) {
			
				//ログインを記憶する場合、クッキーを設定
		     	if($_POST['autologin']){
	
		     		//自動ログイン用クレデンシャル生成
					$credential = Data::createCredential($idArray[0]['id']);
				}
		
	    		
	        	$_SESSION['u_id'] = $idArray[0]['id'];
		        $_SESSION['u_company_name'] = $idArray[0]['company_name'];
				$u_id=$_SESSION['u_id'];
		        $u_company_name = $_SESSION['u_company_name'];
		        $status_1_datas = Data::getStatusALL($u_id);
				$_SESSION['status_1'] = $status_1_datas[0]->action_1;
				$status_1 = $_SESSION['status_1'];
	    	  	$message= LOGIN_SUCCESS;
	    	  	
   				//LITE版かどうか　ログインした瞬間に、通常版かLITE版か判定するため
				$is_lite = Lite::isLite($u_id);
				if($is_lite){
					$smarty->assign('is_lite', $is_lite);
				}

				header("Location:index.php?action=top");
	    	  	
		 	// 存在しなければ、ログイン不成功
		    } else {
		
		    	$faillogin = DATE_ERROR_FAILLOGIN;
		   		header("$faillogin");
			    
		    }
	   }
	// 例外の捕捉
	} catch (PDOException $e) {
		die($e->getMessage() . 'データベースのエラーです。管理者に連絡してください');
	}



$smarty->assign('message', $message);

$smarty->assign("filename","login.html");
$smarty->display("template.html");

?>