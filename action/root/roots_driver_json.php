<?php

//ルート履歴表示画面　ドライバーアプリ用にJSONでメッセージを出力　

/*
$_POST['login_id']='ichiro';
$_POST['passwd']='ichiro';
*/
if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);
	
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
			
		$driver_id = $idArray[0]['id'];
		$company_id = $idArray[0]['company_id'];
       	$_SESSION['driver_id'] = $driver_id;
       	
        $_SESSION['driver_last_name'] = $idArray[0]['last_name'];
        $_SESSION['driver_first_name'] = $idArray[0]['first_name'];
        $_SESSION['driver_company_id'] = $idArray[0]['company_id'];
			
		try{
			
			
			$datas['roots'] = Root::viewRoots($company_id, $driver_id);
			
			if($datas){

				echo json_encode($datas);
					
			}else{

				print "NO_DATA";
					
			}
			
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