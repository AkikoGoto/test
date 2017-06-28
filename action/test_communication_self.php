<?php
/**
 * エジソンさんの中継サーバーへ送信するテスト
 * @author Yuji Hamada
 * @since 2017/2/27
 * @version 2.0
 */
$json_string = file_get_contents('php://input');
$remoteAddress = $_SERVER["REMOTE_ADDR"];

//Smart動態管理エジソンサーバー
$ip = "160.16.70.168";

//ローカル
// $ip = "127.0.0.1";

if($remoteAddress == $ip){
	
	if($json_string){
		$post_status_response = array("status"=>"test communication success", "REMOTE_ADDR" => $remoteAddress);
		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			$sql = "INSERT INTO test_communication (post_data, response_data, created, updated) VALUES (:post_data, :response_data, NOW(), NOW());";
				
			$stmt=$dbh->prepare($sql);
				
			$param = array(
					'post_data' => $json_string,
					'response_data' => json_encode($post_status_response)
			);
			$stmt->execute($param);
			$dbh->commit();
		}catch(PDOException $e){
			$dbh->rollback();
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}else{
		$post_status_response = array("status"=>"post data is nothing", "REMOTE_ADDR" => $remoteAddress);
	}
}else{
	$post_status_response = array("status"=>"invalid access", "REMOTE_ADDR" => $remoteAddress);
}
print json_encode($post_status_response);