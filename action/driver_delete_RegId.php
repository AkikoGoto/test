<?php
/** スマートフォンからSmart動態管理がアンインストールされたらRegIdを消去する
 * ver3.0から追加
 * 
 */

//ドライバーのログインチェック
//ログインIDとパスワードでチェック

if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);

	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
			
		$driver_id = $idArray[0]['id'];

		try{
			
				Driver::deleteRegId($driver_id);

		}catch(Exception $e){

			$message=$e->getMessage();
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