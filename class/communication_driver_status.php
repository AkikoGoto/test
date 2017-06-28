<?php
/**
 * 中継サーバーへ通信が必要なドライバーステータス
 * 他サーバーへのPOSTやレスポンスを解析するのに使う
 * @author Yuji Hamada
 * @since 2017/2/27
 * @version 2.0
 */

Class CommunicationDriverStatus{
	public $id;
	public $driver_id;
	public $driver_status_id;
	public $send_id;
	public $latitude;
	public $longitude;
	public $speed;
	public $created;
	public $updated;
	
	/**
	 * 中継サーバーへ通信が必要なドライバーステータスをインサートする
	 * @param string $driver_status_id
	 * @param array $data
	 * @param string $created
	 */
	public static function insert($driver_status_id, $data, $created){
		$driver = Driver::getById($data['driver_id'], NULL);
		$isTransporting = Transport::isTransporting($data['driver_id']);
		
		$relay_server = RelayServer::getByGeographicId($driver[0]->geographic_id);
		//ドライバーの最新の輸送ステータスが０１（輸送中）かつサブグループに所属しており、そのサブグループがパトロールでない場合は通信テーブルに保存する
		if($isTransporting == '1' && !empty($relay_server) && !empty($relay_server['relay_server_url'])){
			try{
				$dbh=SingletonPDO::connect();
				$dbh->beginTransaction();
					
				$sql = "INSERT INTO
					communication_driver_status(driver_id, driver_status_id, geographic_id, latitude, longitude, speed, created)
					VALUES(:driver_id, :driver_status_id, :geographic_id, :latitude, :longitude, :speed, :created)";
					
				$statement=$dbh->prepare($sql);
					
				$param = array(
						'driver_id' => $data['driver_id'],
						'driver_status_id' => $driver_status_id,
						'geographic_id' => $driver[0]->geographic_id,
						'latitude' => $data['latitude'],
						'longitude' => $data['longitude'],
						'speed' => $data['speed'],
						'created' => $created
				);
					
				$statement->execute($param);
					
				$dbh->commit();
		
			}catch(PDOException $e){
				$dbh->rollback();
				echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
				error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			}
		}
	}
	
	/**
	 * 中継サーバーへ送信した送信ドライバーステータスを削除する
	 * @param arrau $ids
	 */
	public function delete($ids) {
		
		try {
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			$sql = "DELETE FROM communication_driver_status 
					WHERE id IN(
					";
			
			$deleteQuery = array();
			$deleteIds = array();
			foreach($ids as $key => $id){
				$deleteQuery[$key] = ':id' . $key;
				$deleteIds['id'. $key] = $id;
			}
			
			$sql .= implode(',', $deleteQuery);
			$sql .= ')';
			$stmt = $dbh->prepare($sql);
			$stmt->execute($deleteIds);
			
			$dbh->commit();
		}catch(PDOException $e){
			$dbh->rollback();
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	/**
	 * 指定された中継サーバーIDの送信ドライバーステータスを取得する
	 */
	public function getByRelayServerId($relay_server_id){
		try{
				
			$dbh=SingletonPDO::connect();
				
			$sql = "
					SELECT
					    communication_driver_status.id,
					    send_ids.send_id,
					    driver_id,
					    latitude,
					    longitude,
					    speed,
					    DATE_FORMAT(communication_driver_status.created, '%Y/%m/%d %H:%i:%S') as created
					FROM
					    communication_driver_status,
						send_ids
					WHERE
						send_ids.driver_status_id = communication_driver_status.driver_status_id
					AND
					    geographic_id IN(
					        SELECT
					            id
					        FROM
					            `geographic`
					        WHERE
					            `relay_server_id` = :relay_server_id
					    )
					ORDER BY 
						created DESC
					LIMIT " . SEND_DRIVER_STATUS_LIMIT;
		
			$stmt=$dbh->prepare($sql);
		
			$param = array(
					'relay_server_id' => $relay_server_id
			);
			$stmt->execute($param);
		
			$relay_server = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
			return $relay_server;
		
		}catch(PDOException $e){
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
}