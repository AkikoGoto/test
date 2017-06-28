<?php
/** 作業記録時のコメント投稿
 * ver2.7から追加
 * 画面なし
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
		
		$keys = $comment_array;
	
		foreach($keys as $key) {
			$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		}
		
		try{
			
			if(!$datas["comment"]){
				print "NO_COMMENT";
			}else{
				$work = Work::getWorkIdByDriverId($datas['status'], $driver_id);
				$return = Work::Comment($datas, $driver_id, $work[0]['id']);
				
				if($return){

					if($datas == 1){
						print "SUCCESS";
					}elseif($datas == 2){
						print "NO_DATA";
					}elseif($datas == 3){
						print "DB_ERROR";
					}
				}
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