<?php
/** ドライバーのステータスアップデート空車　データベースへ状況を入力　スマートフォンアプリからの入力
 * ver5.2から追加
 * Jsonで複数データを扱うようになった
 * 画面なし
 */

//テストデータ

//$_POST['location_json']='{"location":[{"address":"〒220-0003 神奈川県横浜市西区楠町","start":"2017-5-17 17:59:40","saved_time":"2017-5-17 17:56:40","speed":"13.89","status":"1","longitude":"139.61567864666915","latitude":"35.46882244361106","end":"NULL"}],"login_info":[{"passwd":"gotoua","login_id":"gotoua"}]}';
//$_POST['location_json']='{"location":[{"address":"〒221-0835 神奈川県横浜市神奈川区鶴屋町３丁目","start":"NULL","saved_time":"2017-5-18 12:22:10","speed":"13.89","status":"1","longitude":"139.6177472512899","latitude":"35.46895931088053","end":"2017-5-18 12:22:10"}],"login_info":[{"passwd":"gotoua","login_id":"gotoua"}]}';

//$_POST['location_json']='{"location":[{"address":"〒221-0855 神奈川県横浜市神奈川区三ツ沢西町","start":"NULL","saved_time":"2017-5-18 10:41:24","speed":"13.89","status":"1","longitude":"139.60287","latitude":"35.47287","end":"NULL"},{"address":"〒221-0835 神奈川県横浜市神奈川区鶴屋町３丁目","start":"NULL","saved_time":"2017-5-18 10:41:40","speed":"13.89","status":"1","longitude":"139.62011928913995","latitude":"35.46915773828563","end":"NULL"},{"address":"〒221-0835 神奈川県横浜市神奈川区鶴屋町３丁目","start":"NULL","saved_time":"2017-5-18 10:41:55","speed":"13.89","status":"1","longitude":"139.6180039962005","latitude":"35.46897435271896","end":"NULL"},{"address":"〒220-0003 神奈川県横浜市西区楠町","start":"NULL","saved_time":"2017-5-18 10:42:11","speed":"13.89","status":"1","longitude":"139.61567727120757","latitude":"35.468822386300616","end":"NULL"},{"address":"〒220-0003 神奈川県横浜市西区楠町","start":"NULL","saved_time":"2017-5-18 10:42:11","speed":"13.89","status":"1","longitude":"139.6156773","latitude":"35.4688224","end":"2017-5-18 10:42:11"},{"address":"〒221-0835 神奈川県横浜市神奈川区鶴屋町２丁目２６?４","start":"2017-5-18 14:27:40","saved_time":"2017-5-18 14:27:40","speed":"13.89","status":"1","longitude":"139.6201548","latitude":"35.4693478","end":"NULL"},{"address":"住所が見つかりません","start":"NULL","saved_time":"2017-5-18 14:27:51","speed":"13.89","status":"1","longitude":"139.6186696","latitude":"35.4690282","end":"2017-5-18 14:27:51"}],"login_info":[{"passwd":"gotoua","login_id":"gotoua"}]}';

$keys=array();
$keys=array_merge($driver_status_array);

$posted_data = json_decode($_POST['location_json']);

$post_status_response = Array();
$each_location_response = Array();


//ドライバーのログインチェック
//ログインIDとパスワードでチェック
if($posted_data->login_info[0]->login_id) {

	$login_id = htmlentities($posted_data->login_info[0]->login_id, ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($posted_data->login_info[0]->passwd, ENT_QUOTES, mb_internal_encoding());

	$idArray=Driver::login($login_id,$passwd);

	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
			
		$driver_id = $idArray[0]['id'];

		foreach($posted_data->location as $each_location){

			foreach($keys as $key){
				$datas[$key]=htmlentities($each_location->$key, ENT_QUOTES, mb_internal_encoding());
			}

			try{


				if((($datas["latitude"] != "0.0")||($datas["longitude"] != "0.0"))&&
				((!empty($datas["latitude"]))||(!empty($datas["latitude"])))){

					//ドライバー情報をアップデート
					$datas['driver_id']=$driver_id;
					$is_success = Driver::statusUpdate($datas);

						
					if($is_success == 1){

						$status = "SUCCESS";

					}elseif($is_success == 2){

						$status = "TOO_SHORT";
							
					}elseif($is_success == 3){

						$status = "NOT_REGISTERED";

					}elseif($is_success == 4){
							
						$status = "DB_ERROR";
							
					}
						
				}else{

					//緯度経度が取得できなかった場合
					$status = "GPS_FAILED";
						
				}
					
					
			}catch(Exception $e){
				
				$status = "ERROR";

				echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
				error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
				
			}
				

			//スマホアプリに返すためのレスポンスを出力
			array_push($each_location_response, array("status"=>$status,
															"saved_time"=>$each_location->saved_time)
			);

				
		}
		
		//ログインに失敗した場合の画面出力
		$post_status_response = array("post_status"=>"SUCCESS");
		
	}else{

		//ログインに失敗した場合の画面出力
		$post_status_response = array("post_status"=>"LOGIN_FAILED");
	}

}else{

	//IDがPOSTされていない場合
	$post_status_response = array("post_status"=>"INVALID_ACCESS");

}

$each_response['each_location'] = $each_location_response;

$update_response = array_merge($post_status_response, $each_response);

$json = json_encode($update_response);
print $json;

?>