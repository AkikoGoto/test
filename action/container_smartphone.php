<?php
/** ドライバーのステータスアップデート空車　データベースへ状況を入力　スマートフォンアプリからの入力
 * ver2.2から追加
 * 画面なし
 */

/**
 * 共通設定読み込み
 */

$keys=array();
$keys=array_merge($target_array);

//テストデータ
/*
$_POST['latitude']=35.455892;
$_POST['longitude']=139.592055;
$_POST['target_id']=1234;
$_POST['driver_id']=636;
$_POST['address']="〒221-0835 横浜市神奈川区鶴屋町2-21-1　ダイヤビル5F";

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
//ドライバーテストデータ
//$driver_id = 636;

		//$datas[データ名]でサニタイズされたデータが入っている

		foreach($keys as $key){
			$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		}


		try{
			

			if((($datas["latitude"] != "0.0")||($datas["longitude"] != "0.0"))&&				
				((!empty($datas["latitude"]))||(!empty($datas["latitude"])))){

				//ドライバー情報をアップデート
				$datas['driver_id']=$driver_id;
				$is_success = Target::targetsUpdate($datas);

				//成功の場合の画面出力
				if($is_success == 1){
					
					print "SUCCESS";
					
				}elseif($is_success == 2){

					//DBエラーでデータベースへ登録できなかった
					print "DB_ERROR";
					
				
				}
				
			}else{
					
				//緯度経度が取得できなかった場合の画面出力
				print "GPS_FAILED";
			}


		}catch(Exception $e){

			$message=$e->getMessage();
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