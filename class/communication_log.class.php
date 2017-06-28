<?php

/**
 * エジソンさんの中継サーバーへとのやり取りのログへの保存を扱うクラス
 * @author Yuji Hamada
 * @since 2017/2/27
 * @version 2.0
 */
class CommunicationLog extends Data{
	
	public $id;
	public $transport_status_id;
	public $send_log;
	public $receive_log;
	public $status_code;
	public $created;
	public $updated;

	/**
	 * ログの保存
	 * @param array $datas
	 */
	public static function insertLog($datas){
		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			$sql="INSERT INTO communication_logs
					(transport_status_id, send_log, receive_log, status_code, http_status, url, created, updated)
					VALUES
					(:transport_status_id, :send_log, :receive_log, :status_code, :http_status, :url, NOW(), NOW());";
			
			$stmt=$dbh->prepare($sql);

			$param = array(
					'transport_status_id' => $datas['transport_status_id'],
					'send_log' => $datas['send_log'],
					'receive_log' => $datas['receive_log'],
					'status_code' => $datas['status_code'],	
					'http_status' => $datas['http_status'],
					'url' => $datas['url']
			);
			$stmt->execute($param);
			$id = $dbh->lastInsertId();
			$dbh->commit();
			
			return $id;
			
		}catch(PDOException $e){
			$dbh->rollback();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			mail_to_edison_oc_about_db_error($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	/**
	 * ログとドライバーステータスの中間テーブルにインサート
	 * @param string $logId
	 * @param array $driverStatusIds
	 */
	public function insertLogDriverStatus($logId, $driverStatusIds){
		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			$sql = "INSERT INTO log_driver_status
					(log_id, driver_status_id, created, updated)
					VALUES ";
			
			$insertQuery = array();
			$insertData = array();
			foreach($driverStatusIds as $key => $driverStatusId){
				$insertQuery[$key] = '(:log_id' . $key . ', :driver_status_id' . $key . ', NOW(), NOW())';
				
				$insertData['log_id'. $key] = $logId;
				$insertData['driver_status_id'. $key] = $driverStatusId;		
			}
			
			$sql .= implode(',', $insertQuery);
			
			$stmt = $dbh->prepare($sql);
			$stmt->execute($insertData);
			$dbh->commit();
		}catch(PDOException $e){
			$dbh->rollback();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			mail_to_edison_oc_about_db_error($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	public function __set($prop,$value){
		print("存在しないプロパティ'{$prop}'に値'{$value}'を設定しました\n");
		debug_print_backtrace();
		exit();
	}
	
	//存在しないプロパティを参照した場合にエラーを表示させる
	public function __get($prop){
		print("存在しないプロパティ'{$prop}'を参照しました\n");
		exit();
	}
	
}
?>