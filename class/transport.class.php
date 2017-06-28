<?php
/**
 * エジソンさんの中継サーバーに送信する輸送ステータスを扱うクラス
 * @author Yuji Hamada
 * @since 2017/2/27
 * @version 2.0
 */
class Transport extends Data{
	
	public $id;
	public $comId;
	public $change_kind;
	public $driver_id;
	public $transport_status_kind;
	public $is_sent_success;
	public $transport_time;
	public $created;
	public $updated;

	/**
	 * 輸送ステータステーブルへのインサート
	 * @param string $comId
	 * @param array $datas
	 * @param PDO $dbh
	 * @throws PDOException
	 */
	public static function insertTransportStatus($comId, $datas, $dbh){
		try{
			$sql="
			INSERT INTO transport_status
					(comId, change_kind, driver_id, transport_status_kind, is_sent_success, transport_time, created, updated)
					VALUES
					(:comId, :change_kind, :driver_id, :transport_status_kind, :is_sent_success, :transport_time,  NOW(), NOW());";
			
			$stmt=$dbh->prepare($sql);

			$param = array(
					'comId' => $comId,
					'change_kind' => $datas['chgKnd'],
					'driver_id' => $datas['vclId'],
					'transport_status_kind' => $datas['trpSttKnd'],
					'is_sent_success' => "0",
					'transport_time' => $datas['trpSttChgDt'],	
					
			);
			$stmt->execute($param);
			$id = $dbh->lastInsertId();
			return $id;
		}catch(PDOException $e){
			throw  $e;
		}
	}
	
	/**
	 * ラスト輸送ステータステーブルのリプレイス
	 * @param array $datas
	 * @param PDO $dbh
	 * @throws PDOException
	 */
	public function updateLastTransportStatus($datas, $dbh){
		try{
			$sql="
					INSERT INTO 
						last_transport_status(driver_id, is_transporting, created, updated)
					VALUES
						(:driver_id, CASE WHEN :is_transporting = 01 THEN 1 ELSE 0 END, NOW(), NOW())
					ON DUPLICATE KEY UPDATE
						is_transporting = CASE WHEN VALUES(is_transporting) = 01 THEN 1 ELSE 0 END
					;";
			
			
			$stmt=$dbh->prepare($sql);
		
			$param = array(
					'driver_id' => $datas['vclId'],
					'is_transporting' => $datas['trpSttKnd']
			);
			$stmt->execute($param);
				
		}catch(PDOException $e){
			throw  $e;
		}
	}
	
	/**
	 * 輸送ステータステーブルとラスト輸送ステータステーブルにインサート（アップデート）
	 * @param string $comId
	 * @param array $datas
	 * @param PDO $dbh
	 * @throws PDOException
	 */
	public function insert($comId, $datas, $dbh){
		try{
			$id = self::insertTransportStatus($comId, $datas, $dbh);
			self::updateLastTransportStatus($datas, $dbh);
			return $id;
		}catch(PDOException $e){
			throw  $e;
		}
	}
	
	/**
	 * //送信成功フラグの更新
	 * @param string $id
	 * @param string $isSentSuccess
	 */
	public function updateIsSentSuccess($id,$isSentSuccess){
		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			$sql = "UPDATE transport_status SET is_sent_success = :is_sent_success where id = :id";
			$stmt=$dbh->prepare($sql);
			$param = array('is_sent_success' => $isSentSuccess, 'id' => $id);
			$stmt->execute($param);
			$dbh->commit();
		}catch(PDOException $e){
			$dbh->rollback();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			mail_to_edison_oc_about_db_error($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
		
	}
	
	/**
	 * 最新のトランスポートステータスを取得
	 * @param string $driverId
	 */
	public function isTransporting($driverId){
		try{
			$dbh=SingletonPDO::connect();
			
			$sql = "
					SELECT 
						is_transporting 
					FROM 
						last_transport_status 
					WHERE 
						driver_id = :driver_id 
					";
			$stmt=$dbh->prepare($sql);
			
			$param = array('driver_id' => $driverId);
			$stmt->execute($param);
			
			$isTransporting=$stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if(isset($isTransporting[0]['is_transporting'])){
				$isTransporting = $isTransporting[0]['is_transporting'];
			}else{
				//存在しない場合はまだボタンが一回も押されていないので輸送開始していないと判断
				$isTransporting = '0';
			}
			
			return $isTransporting;
		}catch(PDOException $e){
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
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