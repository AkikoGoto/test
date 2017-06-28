<?php

//メッセージ送信履歴表示画面　ドライバーアプリ用にJSONでメッセージを出力　

/*$_POST['login_id']='ichiro';
$_POST['passwd']='ichiro';
$_POST['message_id']=241;
*/
if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	$message_id = htmlentities($_POST['message_id'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray = Driver::login($login_id,$passwd);
	
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
			
		$driver_id = $idArray[0]['id'];
			
		try{
			
			Message::hasReadMessage($driver_id, $message_id);	

			
		 }catch(Exception $e){
				
			$message=$e->getMessage();
			//DBエラーの場合の画面出力
			print "DB_ERROR";

		 }
	}else{

		//ログインに失敗した場合の画面出力
		print "LOGIN_FAILED";
	}
}else{

	//IDがPOSTされていない場合
	print "INVALID_ACCESS";
}
	
?>