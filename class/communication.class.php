<?php

/**
 * 通信クラス
 * 他サーバーへのPOSTやレスポンスを解析するのに使う
 * @author Yuji Hamada
 * @since 2017/2/27
 * @version 2.0
 */
 
Class Communication{

	/**
	 * 引数のURLに第二引数のパラメータをPOSTする
	 * 通信に成功した場合は戻り値のしてレスポンスとHTTPステータスを返却する
	 * @param unknown $url
	 * @param unknown $context
	 */
	public static function communicate($url, $context){
		$response = @file_get_contents($url, false, $context);
		if(count($http_response_header) > 0){
			preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
			return array($response, $matches[1]);
		}else{
			return false;
		}
	}
	
	/**
	 * エジソン中継サーバーへの輸送ステータス送信に成功したあとの処理
	 * @param unknown $transportStatusId
	 * @param unknown $response
	 */
	public static function sentTransportSuccess($transportStatusId, $response){
		$post_status_response = array("status"=>"SUCCESS");
		Transport::updateIsSentSuccess($transportStatusId, '1');
		$sttCd = json_decode($response)->sttCd;
		
		return array($post_status_response, $sttCd);
	}
	
	/**
	 * 送信するドライバーステータスのデータを作成する
	 * @param array $driverStatuses
	 * @param array $server
	 */
	public function creatSendDriverStatusDate($driverStatuses, $server){
		$dtl = array();
		$communicationDriverStatusIds = array();
		//送信用データの配列を作成する
		foreach($driverStatuses as $key => $driverStatus){
			$dtl[$key]['chgKnd'] = 'I';
			$dtl[$key]['sndId'] = $driverStatus['send_id'];
			$dtl[$key]['vclId'] = $driverStatus['driver_id'];
			$dtl[$key]['posDt'] = $driverStatus['created'];
			$dtl[$key]['vclLat'] = $driverStatus['latitude'];
			$dtl[$key]['vclLon'] = $driverStatus['longitude'];
			$dtl[$key]['vclSpd'] = "0";
		
			array_push($communicationDriverStatusIds, $driverStatus['id']);
		}
		
		$data['dtl'] = $dtl;
		$data['comId'] = $server['com_id'];
		
		return array($data, $communicationDriverStatusIds);
	}
	
	/**
	 * 引数のURLに引数のドライバーステータスを送信する
	 * @param string $url
	 * @param array $driverStatuses
	 */
	public function sendDriverStatus($server, $driverStatuses){
		if($driverStatuses){
			list($data, $communicationDriverStatusIds) = Communication::creatSendDriverStatusDate($driverStatuses, $server);
		
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
			
			$url = $server['relay_server_url'] . IFT0150;
			//URLとcontextからレスポンスとHTTPステータスの取得
			list($response, $httpStatus)= Communication::communicate($url, $context);
		
			//レスポンスとHTTPステータスからログに残すレスポンスと送信ステータスを得る
			if(!empty($response) && $httpStatus == "200"){
				//レスポンスが帰ってきている場合
				$post_status_response = array("status"=>"SUCCESS");
				$statusCode = json_decode($response)->sttCd;
				//通信に成功した場合は送信したドライバーステータスを削除する
				CommunicationDriverStatus::delete($communicationDriverStatusIds);
			}elseif(empty($response) && $httpStatus == "200"){
				$post_status_response = array("status"=>"FAILED", "errors" => "response is nothing");
				$response = "response is nothing";
				mail_to_edison_oc_about_error(json_encode($data), $response,$httpStatus);
			}elseif($httpStatus){
				$post_status_response = array("status"=>"FAILED", "errors" => "HTTP status is " . $httpStatus);
				mail_to_edison_oc_about_error(json_encode($data), $response,$httpStatus);
			}else{
				//HTTPステータスが存在しない（タイムアウトになった）場合
				$post_status_response = array("status"=>"FAILED", "errors" => "timeout");
				$httpStatus = "timeout";
				mail_to_edison_oc_about_error(json_encode($data), $response,$httpStatus);
			}
		
			//送信・受信データをログに残す
			$logData = array("transport_status_id" => NULL, "send_log" => json_encode($data), "receive_log" => $response, "status_code" => $statusCode, "http_status" => $httpStatus, "url" => $url);
			$logId = CommunicationLog::insertLog($logData);
			if($logId){
				CommunicationLog::insertLogDriverStatus($logId, $communicationDriverStatusIds);
			}
		
		}else{
			//送信するドライバーステータスがない場合は処理を終了させる
			$post_status_response = array("status"=>"FAILED", "errors" => "no driver status data");
		}
		print json_encode($post_status_response);
	}
	

	/**
	 * 輸送ステータスの中継サーバー送信を失敗した際のエラーメッセージの取得
	 * @param string $transportStatusKind
	 */
	public function transportErrorMessage($transportStatusKind){
		if($transportStatusKind == "01"){
			$errorMessage = TRANSPORT_START_ERROR_MESSAGE;
		}else{
			$errorMessage = TRANSPORT_END_ERROR_MESSAGE;
		}
		return $errorMessage;
	}
}