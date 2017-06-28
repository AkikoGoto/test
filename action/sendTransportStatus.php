<?php
/**
 * エジソンさんの中継サーバーへの輸送ステータス送信
 * IFT0120
 * @author Yuji Hamada
 * @since 2017/2/27
 * @version 2.0
 */

$loginId = htmlentities($_POST['login_id'], ENT_QUOTES, mb_internal_encoding());
$password = htmlentities($_POST['passwd'], ENT_QUOTES, mb_internal_encoding());

if($loginId) {
	//ログインしているか確認のためドライバー情報を取得
	$driver = Driver::login($loginId, $password);
	if(isSet($driver[0]['last_name'])) {
		//ドライバーが存在し、サブグループが登録があるかつパトロール出ない場合のみ送信する
		$vclId = $driver[0]["id"];
		$trpSttKnd =  htmlentities($_POST['trpSttKnd'], ENT_QUOTES, mb_internal_encoding());
		$trpSttChgDt = date("Y/m/d H:i:s");
		
		$dtl[0] = array("chgKnd" => "I","sndId" => "", "vclId" => $vclId, "trpSttKnd" => $trpSttKnd, "trpSttChgDt" => $trpSttChgDt);
		
		$relay_server = RelayServer::getByGeographicId($driver[0]['geographic_id']);
		
		//輸送ステータスをテーブルに保存してトランスポートステータスID、送信IDを取得
		list($transportStatusId, $sendId) = SendId::createSendIdFromTransportStatus($dtl[0] ,$relay_server['com_id']);
		
		if($transportStatusId && $sendId){
			
			if(!empty($relay_server) && !empty($relay_server['relay_server_url'])){
				//トランスポートステータスIDをと送信IDが取得できたときのみ中継サーバーに送信する
				$data = array();
				$data['comId'] = $relay_server['com_id'];
				$dtl[0]['sndId'] = $sendId;
				$data['dtl'] = $dtl;
				
				//ここから送信
				$headers = array(
						'Content-Type: application/json; charset=utf-8',
				);
				
				$context = stream_context_create(array(
						'http' => array(
								'method' => 'POST',
								'header' => implode("\n", $headers),
								'content' => json_encode($data),
								'timeout' => COMMUNICATE_TIMEOUT
						),
				));
				
				$url = $relay_server['relay_server_url'] . IFT0120;
				
				list($response, $httpStatus)= Communication::communicate($url, $context);
				if(!empty($response) && $httpStatus == "200"){
					//レスポンスが存在かつHTTPステータスが２００の場合は通信成功
					list($post_status_response, $sttCd) = Communication::sentTransportSuccess($transportStatusId, $response);
				}else{
					//タイムアウトまたはHTTPステータス200以外の場合の場合200ミリ秒スリープ後リトライ
					usleep(RETRY_INTERVAL);
					list($response, $httpStatus)= Communication::communicate($url, $context);
					if(!empty($response) && $httpStatus == "200"){
						//レスポンスが存在しHTTPステータスが200、通信に成功した場合
						list($post_status_response, $sttCd) = Communication::sentTransportSuccess($transportStatusId, $response);
					}else{
						if(empty($response) && $httpStatus == "200"){
							//HTTPステータスは200だがレスポンスが空の場合
							$response = "response is nothing";
						}elseif(empty($httpStatus)){
							//HTTPステータスが空の場合
							$httpStatus = "timeout";
						}
						$errorMessage = Communication::transportErrorMessage($trpSttKnd);
						$post_status_response = array("status"=>"FAILED", "errors" => $errorMessage);
						mail_to_edison_oc_about_error(json_encode($data), $response, $httpStatus);
					}
				}
				
				//送信・受信データをログに残す
				$logData = array(
						"driver_status_id" => "",
						"transport_status_id" => $transportStatusId,
						"send_log" => json_encode($data), 
						"receive_log" => $response, 
						"status_code" => $sttCd,
						"http_status" => $httpStatus,
						"url" => $url
				);
				CommunicationLog::insertLog($logData);
			}else{
				$post_status_response = array("status"=>"SUCCESS");
			}
		}else{
			$post_status_response = array("status"=>"FAILED", "errors" => "データベースエラーがありました");
		}
	}else{
		//ログインに失敗した場合の画面出力
		$post_status_response = array("status"=>"FAILED", "errors" => "ログインに失敗しました");
	}
	
}else{
	//IDがPOSTされていない場合
	$post_status_response = array("status"=>"FAILED", "errors" => "ログインIDが送信されていません");
}

print json_encode($post_status_response);