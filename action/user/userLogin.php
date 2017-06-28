<?php

/**
 * ユーザー　ログイン
 * @author Akiko Goto
 * @since 2014/09/26
 */

if($_SESSION['user_id']){

	$message= "すでにログインしています";
	header('Location:index.php?action=user/user_menu');

}elseif($u_id){ 
	
	$message= "グループとしてログインしています。<br>ユーザーとしてログインする場合は、いったんログアウトをしてから、ユーザーとしてログインしてください。";	
	
}else{
	// id がPOSTでわたって来ていたら、ログインの試み
	try {
		
		$is_garapagos=is_garapagos($carrier);
	
	        if($_POST['login_id']) {
	        	
		    	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	    		$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	        	
	    		$idArray=Users::login($login_id,$passwd);

			    // 存在すれば、ログイン成功
		    	if(isSet($idArray[0]['last_name'])) {		
				
		    		//ログインを記憶する場合、クッキーを設定
			     	if($_POST['autologin']){
						//自動ログイン用クレデンシャル生成
						$credential = Users::createCredential($idArray[0]['id']);
				
					}
		    			    		
		        	$_SESSION['user_id'] = $idArray[0]['id'];
			        $_SESSION['user_last_name'] = $idArray[0]['last_name'];
			        $_SESSION['user_first_name'] = $idArray[0]['first_name'];
			        $_SESSION['company_id'] = $idArray[0]['company_id'];
			        
					header('Location:index.php?action=user/user_menu');
		    	 
		    	} else {
		
		 	  		header('Location:index.php?action=message_mobile&situation=fail_login');
		
			    }
		}
	// 例外の捕捉
	} catch (PDOException $e) {
		die($e->getMessage() . 'データベースのエラーです。管理者に連絡してください');
	}
}

$smarty->assign('message', $message);
$smarty->assign('is_garapagos', $is_garapagos);

$smarty->assign("filename","user/userLogin.html");
$smarty->display("template.html");

?>