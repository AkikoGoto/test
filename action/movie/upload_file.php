<?php

/**
 * スマートフォンから動画をアップロードして、DBとファイルを格納する
 * @author Akiko Goto
 * @since 2015/04/22
 * 
 */

$uploadfile = SERVER_MOVIE_PATH . basename($_FILES['fl']['name']);

/*
$_POST['login_id']="ichiro";
$_POST['passwd']="ichiro";
$_POST['speed']="15";
$_POST['recorded_time']="2015-4-14 12:47";
$_POST['latitude']="35";
$_POST['longitude']="135";
$_FILES['fl']['name']="2015-04-10_19_01_22_crop.mp4";
$_FILES['fl']['size'] = 1000000;
$_FILES['fl']['error']=0;
*/


$keys=array('login_id', 'passwd', 'speed', 'recorded_time', 'address', 'latitude', 'longitude');

$err = null;

if(!empty($_POST)){


	//拡張子チェック
	$tmp_ary = explode('.', $_FILES['fl']['name']);
	$extension = $tmp_ary[count($tmp_ary)-1];

	if($extension != "mp4"){
		
		$status='FAIL';
		$error_info = "FILE_TYPE_ERROR";
	}

	//ファイルサイズチェック　1G以上の場合はアップロードできない
	if($_FILES['fl']['size'] > 1000000000){
		
		$status='FAIL';
		$error_info = "FILE_SIZE_ERROR";
		
	}
	
	//ディレクトリトラバーサルチェック
	if(preg_match('/(\.\.\/|\/|\.\.\\\\)/', $_FILES['fl']['name'])){
		$status='FAIL';
		$error_info = "INVALID_FILE_NAME";
	}
	

	if($status != "FAIL"){

		foreach($keys as $key){

			$datas[$key] = htmlentities( $_POST[$key], ENT_QUOTES, mb_internal_encoding());

		}

		// 存在すれば、ログイン成功
		$idArray = Driver::login( $datas['login_id'], $datas['passwd']);

		if(isSet($idArray[0]['last_name'])) {

			// エラーLOGIN_FAILEDではなかったら処理を進める
			try{

				$datas['driver_id'] = $idArray[0]['id'];

				$datas['file_name'] = $_FILES['fl']['name'];
				$datas['size'] = $_FILES['fl']['size'];
				
				if (move_uploaded_file($_FILES['fl']['tmp_name'], $uploadfile)) {

					$status = "SUCCESS";
					chmod($uploadfile, 0644);
						
					//DBに登録 帰り値は動画のID
					$movie_id = Movie::putInMovie($datas);
					
					// node.jsアプリに動画アップロードを通知
					$alert = Movie::alertMovie($movie_id);
					$alertData = $alert[0];
					$alertData['url'] = "uploaded/movies/".$alertData['name']; 
						
					$postURL = SOCKETIO_ALERT_API_HOST."broadcast/movie";
					$alertJson = json_encode($alertData);
					$options = array(
							'http' => array(
									'method'  => 'POST',
									'content' => $alertJson,
									'header'=>  "Content-Type: application/json\r\n" .
									"Accept: application/json\r\n"
							)
					);
						
					$context  = stream_context_create( $options );
					$result = file_get_contents( $postURL, false, $context );
					//$result = json_decode($result);
							

				} else {

					$status = "FAIL";
					$error_info = "FILE_ERROR";

				}
			
			}catch (Exception $e ){
				$status = "FAIL";
				$error_info = "DB_ERROR";
			}
				
		}else{

			$status = "FAIL";
			$error_info = "LOGIN_ERROR";

		}
	}

}else{

	$status='FAIL';
	$error_info = "INVALID_ACCESS";

}



$response = array("status" => $status, "error_info" => $error_info, "file_name" => $datas['file_name'] );
//error_log(json_encode( $response ), 3, "/tmp/my-errors.log");

echo json_encode( $response );


?>