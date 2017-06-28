<?php
/**
 * エジソンさんの中継サーバーへJSONを送信する際に使用する送信IDを扱うクラス
 * @author Yuji Hamada
 * @since 2017/2/27
 * @version 2.0
 */
class SendId{
	
	public $id;
	public $send_id;
	public $driver_status_id;
	public $transport_status_id;
	public $created;
	public $updated;
	
	public function createSendIdFromTransportStatus($dtl, $com_id){
		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			//トランスポートステータスをインサートしてトランスポートステータスIDを取得
			$transportStatusId = Transport::insert($com_id,$dtl, $dbh);
			//send_idsテーブルにトランスポートステータスIDをインサート
			SendId::insertTransportStatusId($transportStatusId, $dbh);
			//インサートしたトランスポートステータスIDから送信IDを作成
			SendId::updateTransportSendId($transportStatusId, $dbh);
			//送信IDを取得
			$sendId = SendId::getTransportSendId($transportStatusId, $dbh);
			
			$dbh->commit();
			return array($transportStatusId, $sendId);
		}catch(PDOException $e){
			$dbh->rollback();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			mail_to_edison_oc_about_db_error($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
		}
	}
	
	/**
	 * ドライバーステータスで未送信かつ送信未成功のデータを送信IDテーブルにインサートする
	 */
	public function insertDriverStatusId(){
		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
		
			$sql = "INSERT IGNORE INTO send_ids(driver_status_id, created, updated) 
					SELECT driver_status_id, NOW(), NOW() 
					FROM communication_driver_status";
			
			$statement = $dbh->query($sql);
			$dbh->commit();
		
		}catch(PDOException $e){
			$dbh->rollback();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			mail_to_edison_oc_about_db_error($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	/**
	 * 引数のトランスポートステータスIDを送信IDテーブルにインサートする
	 * @param string $transportStatusId
	 * @param PDO $dbh
	 */
	public function insertTransportStatusId($transportStatusId, $dbh){
		try{
		
			$sql = "INSERT INTO send_ids (transport_status_id, created, updated) VALUES (:transport_status_id, NOW(), NOW());";
			
			$stmt=$dbh->prepare($sql);
			
			$param = array(
					'transport_status_id' => $transportStatusId
			);
			$stmt->execute($param);
		
		}catch(PDOException $e){
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			throw $e;
		}
	}
	/**
	 * 送信IDテーブルの引数のトランスポートステータスIDのレコードの送信IDを作成する
	 * @param string $transportStatusId
	 * @param PDO $dbh
	 */
	public function updateTransportSendId($transportStatusId, $dbh){
		try{
			
			$sql = "UPDATE send_ids SET send_id = CONCAT('2', id) WHERE transport_status_id = :transport_status_id";
		
			$stmt=$dbh->prepare($sql);
			
			$param = array(
					'transport_status_id' => $transportStatusId
			);
			$stmt->execute($param);
		
		}catch(PDOException $e){
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			throw $e;
		}
	}
	/**
	 * 送信IDテーブルから引数のトランスポートステータスIDのレコードの送信IDを取得する
	 * @param string $transportStatusId
	 * @param PDO $dbh
	 */
	public function getTransportSendId($transportStatusId, $dbh){
		try{
			
			$sql = "SELECT send_id FROM send_ids WHERE transport_status_id = :transport_status_id";
			
			$stmt=$dbh->prepare($sql);
			
			$param = array(
					'transport_status_id' => $transportStatusId
			);
			$stmt->execute($param);
		
			$sendId = $stmt->fetch(PDO::FETCH_ASSOC);
			
			return $sendId['send_id'];
			
		}catch(PDOException $e){
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	/**
	 * ドライバーステータスの送信IDを生成する
	 */
	public function createDriverStatusSendId(){
		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
		
			$sql = "UPDATE send_ids SET send_id = CONCAT('2', id) WHERE send_id IS NULL and driver_status_id IS NOT NULL;";
		
			$statement = $dbh->query($sql);
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