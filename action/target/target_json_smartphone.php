<?php
/** ドライバーがスマホから近くのコンテナを検索する
 * JSONで作業時間を出力
 * ver2.5から追加
 * 画面なし
 */

//ドライバーのログインチェック
//ログインIDとパスワードでチェック

//テストデータ
/*$_POST['login_id']='ichiro';
$_POST['passwd']='ichiro';

$_POST['latitude']=35.455892;
$_POST['longitude']=139.592055;

$_POST['address']="〒221-0835 横浜市神奈川区鶴屋町2-21-1　ダイヤビル5F";
*/
if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);
	
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
			
		$driver_id = $idArray[0]['id'];

		$keys = $target_array;
	
		foreach($keys as $key){
			$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		}
		
		
		try{
			
			if((($datas["latitude"] != "0.0")||($datas["longitude"] != "0.0"))&&				
				((!empty($datas["latitude"]))||(!empty($datas["latitude"])))){

					$datas = Target::getNearTarget($datas, $driver_id);
					
					$near_targets['near_targets'] = $datas;
					
					if($datas){

						echo json_encode($near_targets);
					
					}else{

						print "NO_DATA";
					
					}
			
				}else{
				
					print "GPS_FAILED";
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