<?php

/**
 * スマートフォンから積雪や渋滞をアップロードして、DBに格納する
 * @author Akiko Goto
 * @since 2015/05/07
 *
 */

/*

$_POST['login_id']="ichiro";
$_POST['passwd']="ichiro";
$_POST['speed']="15";
$_POST['type']="SNOW";
$_POST['address']="横浜市神奈川区鶴屋町2-21-1　ダイヤビル5F";
$_POST['latitude']="35";
$_POST['longitude']="135";
*/


$keys=array('login_id', 'passwd', 'speed', 'type', 'address', 'latitude', 'longitude');

$err = null;

if(!empty($_POST)){


	foreach($keys as $key){

		$datas[$key] = htmlentities( $_POST[$key], ENT_QUOTES, mb_internal_encoding());

	}

	// 存在すれば、ログイン成功
	$idArray = Driver::login( $datas['login_id'], $datas['passwd']);

	if(isSet($idArray[0]['last_name'])) {

		// エラーLOGIN_FAILEDではなかったら処理を進める
		try{

			$datas['driver_id'] = $idArray[0]['id'];

			$information_id = Information::putInInformation($datas);
			
			$alert = Information::alertInformation($information_id);
			$alertData = $alert[0];
			switch($alertData['type']) {
				case "SNOW" :
					$alertData['type'] = SNOW;
					break;
				case "TRAFFIC" :
					$alertData['type'] = TRAFFIC;
					break;
				case "OUT_OF_ROUTE" :
					$alertData['type'] = OUT_OF_ROUTE;
					break;
				case "ACCIDENT" :
					$alertData['type'] = "事故";
					break;
				case "SUDDEN_BRAKING" :
					$alertData['type'] = SUDDEN_BRAKING;
					break;
					
					
			}
			
		//	$postURL = "http://edison.doutaikanri.com:3000/broadcast/information";
			$postURL = SOCKETIO_ALERT_API_HOST."broadcast/information";
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
			
			$status = "SUCCESS";

		}catch (Exception $e ){
			$status = "FAIL";
			$error_info = "DB_ERROR";
		}

	}else{

		$status = "FAIL";
		$error_info = "LOGIN_ERROR";

	}
}else{

	$status = "FAIL";
	$error_info = "INVALID_ACCESS";
	
}



$response = array("status" => $status, "error_info" => $error_info);

echo json_encode( $response );


?>