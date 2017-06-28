<?php

/**
 * GCMのregistration_idを更新する
 * @since 2013/09/20
 * @author Akiko Goto
 */

/*
$_POST['login_id']='ichiro';
$_POST['passwd']='ichiro';
$_POST['registration_id']='げほげほ';
*/
if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	$registration_id = htmlentities($_POST['registration_id'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);
	
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
			
		$driver_id = $idArray[0]['id'];
		$company_id = $idArray[0]['company_id'];
			
		try{
			
			$status = Driver::updateRegId($driver_id, $registration_id);
			
			print $status;
			
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