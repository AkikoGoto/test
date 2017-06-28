<?php
/**
 * LastDriverStatusクラス
 * last_driver_statusの更新や取得のためのクラス
 * @author Akiko Goto
 * @since 2017/05/17
 * @version 2.0
 *
 */

class LastDriverStatus{

	public $driver_id;
	public $status;
	public $latitude;
	public $longitude;
	public $sales;
	public $address;
	public $detail;
	public $speed;
	public $start;
	public $end;
	public $work_id;
	public $direction;
	public $is_deviated;
	public $created;
	public $updated;
	
	/**
	 * last_driver_statusをアップデート
	 * @param unknown $dbh
	 * @param unknown $datas
	 * @param unknown $recent_work_id
	 * @param unknown $direction
	 * @param unknown $is_deviated
	 * @param unknown $created
	 */
	public function updateLastDriverStatus($datas, $recent_work_id, $direction, $is_deviated, $created){
		
		try{
			
			$dbh=SingletonPDO::connect();
			
			$sql_last_driver_status ="
			REPLACE INTO
				last_driver_status
					(driver_id, status, latitude, longitude, detail, sales,
					address, speed, start, end, work_id, direction, is_deviated, created)
				VALUES
					(:driver_id, :status, :latitude, :longitude, :detail, :sales, :address, :speed, :start,
					:end, :work_id, :direction, :is_deviated, :created)";
			
			$stmt=$dbh->prepare($sql_last_driver_status);
			
			$param = array(
					'driver_id' => $datas['driver_id'],
					'status' => $datas['status'],
					'latitude' => $datas['latitude'],
					'longitude' => $datas['longitude'],
					'detail' => $datas['detail'],
					'sales' => $datas['sales'],
					'address' => $datas['address'],
					'speed' => $datas['speed'],
					'start' => $datas['start'],
					'end' => $datas['end'],
					'work_id' => $recent_work_id,
					'direction' => $direction,
					'is_deviated' => $is_deviated,
					'created' => $created
			);
			
			
			$stmt->execute($param);
			$stmt->closeCursor();
			
		}catch(PDOException $e){
			
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
		}
		
	}
	
	/**
	 * 最新のドライバーの状況を取得
	 * @param unknown $driver_id
	 * @return mixed
	 */
	public static function getLastDriverStatusByDriver($driver_id){
		
		try{
			
			$dbh=SingletonPDO::connect();
			
			$sql = "
			SELECT
			created, latitude, longitude, status, work_id,
			TIMEDIFF(NOW(), created) as time_diff
			FROM
			last_driver_status
			WHERE
			driver_id = $driver_id
			LIMIT	1
			";
			
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$last_created=$stmt->fetch();
			
			return $last_created;
		
		}catch(PDOException $e){
			
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
		}
	}


	//存在しないプロパティに値をセットした場合エラーを表示させる
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