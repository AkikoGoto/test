<?php
/** ドライバーのステータスアップデート空車　データベースへ状況を入力　スマートフォンアプリからの入力
 * ver2.2から追加
 * 画面なし
 */

/**
 * 共通設定読み込み
 */

$keys=array();
$keys=array_merge($driver_status_array);

//テストデータ
//スタート
//$_POST['login_id']="oc";
//$_POST['passwd']="akagi999";
//$_POST['latitude']=35.4706001;
//$_POST['longitude']=139.6221684;
//$_POST['driver_id']=1130;
//$_POST['status']=7;
//$_POST['speed']=0.0;
//$_POST['start']="2014-7-29 14:21:57";
//$_POST['end'] = NULL;
//$_POST['address']="神奈川県横浜市高島台７";

// normal
/*$_POST['login_id']="oc";
$_POST['passwd']="akagi999";
$_POST['latitude']=35.4707238;
$_POST['longitude']=139.6272598;
$_POST['driver_id']=1130;
$_POST['status']=7;
$_POST['speed']=0.0;
$_POST['start']=NULL;
$_POST['end'] = NULL;
$_POST['address']="神奈川県横浜市青木町２";
*/
/*
$_POST['login_id']="hanako";
$_POST['passwd']="hanako";
$_POST['latitude']=35.4707238;
$_POST['longitude']=139.6272598;
$_POST['status']=1;
$_POST['speed']=10;
$_POST['start']=NULL;
$_POST['end'] = NULL;
$_POST['address']="神奈川県横浜市青木町２";
*/
// end
//$_POST['login_id']="oc";
//$_POST['passwd']="akagi999";
//$_POST['latitude']=35.4707238;
//$_POST['longitude']=139.6272598;
//$_POST['driver_id']=1130;
//$_POST['status']=7;
//$_POST['speed']=0.0;
//$_POST['start']=NULL;
//$_POST['end'] = "2014-7-29 14:21:58";
//$_POST['address']="神奈川県横浜市青木町２";


$post_string = "";
foreach ($_POST as $key => $value) {
	$post_string .= ", ".$key." = ".$value;
}
//Concrete::log_error( "works", $post_string);


//$file = 'komatsu_kensyo_file.txt';
// ファイルをオープンして既存のコンテンツを取得します
//$current = "";
//// 新しい人物をファイルに追加します
//foreach ($_POST as $key => $value) {
//	$current .= $key.'=>'.$value.'\n';
//}
//
//$fh = fopen("/var/www/vhosts/doutaikanri.com/public_html/smart_location_test/uploaded/komattan/kommatan.txt","w");
//if(file_exists("file2.txt")){
//	echo 'OK';
//}
//
//fwrite($fh, $current);
//fclose($fh);
//print "ファイルの書込完了------------------------------------------";


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
				$is_success = Driver::statusUpdate($datas);

				//成功の場合の画面出力
				if($is_success == 1){
					
					print "SUCCESS";
					
				}elseif($is_success == 2){
					
					//間隔が短い
					print "TOO_SHORT";
				
				}elseif($is_success == 3){
					
					//GPSエラーの可能性が高いため、データベースへ登録しなかった
					print "NOT_REGISTERED";
									
				}elseif($is_success == 4){

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