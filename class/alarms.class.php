<?php
/*
 * Alarmsクラス
 * 配送先を設定
 * @author Koichi Saito
 * @since 2014/02/05
 * @version ?
 *
 */

class Alarms{

	public $id;
	public $driver_id;
	public $daily;
	public $weekly;
	public $date;
	public $address;
	public $latitude;
	public $longitude;
	public $alert_when_not_there;
	public $alert_when_there;
	public $mail_timing;
	public $mail_before_or_after;
	public $accuracy;
	public $email_other_admin;
	public $mail_time;
	public $updated;
	public $created;
	public $company_id;
	public $name;
	public $set_alarmId;
	public $increase_time;
	public $decrease_time;
	
	
	//新規アラーム情報のデータ投入
	public static function setAlarmDB($data){
		
		try{
			
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			$sql1="
						INSERT INTO
							alarms
							(driver_id, daily, weekly, date, address, latitude, longitude, alert_when_not_there, alert_when_there,
							 accuracy, email_other_admin, mail_time, created)
						VALUES
							(:driver_id, :daily, :weekly, :date, :address, :latitude, :longitude, :alert_when_not_there, :alert_when_there,
							 :accuracy, :email_other_admin, :mail_time, NOW())
						";
			
			$stmt=$dbh->prepare($sql1);
	
			$param = array(
	
					'driver_id' => $data['driver_id'],
					'daily' => $data['daily'],
					'weekly' => $data['weekly'],
					'date' => $data['date'],
					'address' => $data['address'],
					'latitude' => $data['latitude'],
					'longitude' => $data['longitude'],
					'alert_when_not_there' => $data['alert_when_not_there'],
					'alert_when_there' => $data['alert_when_there'],
					'accuracy' => $data['accuracy'],
					'email_other_admin' => $data['email_other_admin'],
					'mail_time' => $data['mail_time'],
	
			);
			
			$stmt->execute($param);
			$id = $dbh->lastInsertId();
			$dbh->commit();
			return $id;
			
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
		}
	}
	
	//編集した時のアラーム情報、DBにupdate
	public static function UpdateAlarmDB($data,$set_alarmId,$set_driverId){
	
		try{
				
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
				
			$sql1="
					UPDATE alarms
					SET
						driver_id = :driver_id, daily = :daily, weekly = :weekly, date = :date, address = :address, latitude = :latitude, longitude = :longitude,
						alert_when_not_there = :alert_when_not_there, alert_when_there = :alert_when_there,	accuracy = :accuracy, 
						email_other_admin = :email_other_admin, mail_time = :mail_time, modified = now()
					WHERE
						id = $set_alarmId AND driver_id = $set_driverId ";
			
			$stmt=$dbh->prepare($sql1);
	
			$param = array(
	
					'driver_id' => $data['driver_id'],
					'daily' => $data['daily'],
					'weekly' => $data['weekly'],
					'date' => $data['date'],
					'address' => $data['address'],
					'latitude' => $data['latitude'],
					'longitude' => $data['longitude'],
					'alert_when_not_there' => $data['alert_when_not_there'],
					'alert_when_there' => $data['alert_when_there'],
					'accuracy' => $data['accuracy'],
					'email_other_admin' => $data['email_other_admin'],
					'mail_time' => $data['mail_time'],	
			);
				
			$stmt->execute($param);
			
			$dbh->commit();
			
			
	
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
		}
	}
	
	//アラーム情報一覧のデータ取得
	public static function searchAlarms($company_id, $query, $branch_id = null){
		
		try{
			$keywords = array_unique(mb_split("[[:space:]]+", $query));
			$criteria = '';
			$params = array(':company_id' => $company_id,);
	
			
			
			for($i=0; $i < count($keywords); $i++) {
				$keyword = $keywords[$i];
				if(empty($keyword)) {
					continue;
				}
				
				$keyword = preg_replace('/([%_])/', '\$1', $keyword);
				
				$criteria .=<<<EOL
AND (
	driver_id COLLATE utf8_unicode_ci LIKE :keyword_${i} OR
	address COLLATE utf8_unicode_ci LIKE :keyword_${i} OR
	mail_time COLLATE utf8_unicode_ci LIKE :keyword_${i}
)
EOL;
				$params[":keyword_${i}"] = "%${keyword}%";
			}
			
			if(!empty($branch_id)){
				
				$criteria .= " AND 
								drivers.geographic_id = $branch_id";
			}
			
			
			$sql=<<<EOL
SELECT
	alarms.id,drivers.id,drivers.last_name,drivers.first_name,alarms.address,alarms.alert_when_not_there,
	alarms.alert_when_there,alarms.mail_time,next_alarms.alarm_id,drivers.id as driver_id
FROM
alarms
JOIN drivers ON alarms.driver_id = drivers.id
JOIN company ON drivers.company_id = company.id
JOIN next_alarms ON alarms.id = next_alarms.alarm_id 
WHERE
company.id = $company_id
${criteria}
EOL;
				
			$dbh=SingletonPDO::connect();
				
			$stmt=$dbh->prepare($sql);
			$stmt->execute($params);
			//$stmt->debugDumpParams();
			$data=$stmt->fetchAll();
	
			return $data;
	
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
		}
	}

		/*
		 *アラームデータベースの情報を取得
		*/
		
		public static function getAlarm($id){
		
			try{
			
				$dbh=SingletonPDO::connect();
		
				$sql="
				SELECT
				*
				FROM
				alarms
				JOIN drivers ON alarms.driver_id=drivers.id
				JOIN next_alarms ON alarms.id=next_alarms.alarm_id
				WHERE
				alarms.id = $id";
				
				$stmt=$dbh->prepare($sql);
				$stmt->execute();
				$mail_data=$stmt->fetchAll();
		
				return $mail_data;
		
			}catch(PDOException $e){
		
				echo $e->getMessage();
		
			}
		}
		
		
	/*
	 * idを指定して、データ内容を取得 アラーム
	 * @param $id ID
	 */
	public static function getDrivers($set_driverId , $set_alarmId){
		
		try{
			$dbh=SingletonPDO::connect();

			$sql="
				SELECT 
					*
				FROM 
					alarms
				JOIN drivers ON	alarms.driver_id=drivers.id	
				WHERE alarms.id = $set_alarmId AND driver_id = $set_driverId";
			
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas=$stmt->fetchAll();
			return $datas;			
			
		}catch(Exception $e){

			echo $e->getMessage();

		}
	}


	//ID単位でアラーム情報を削除するメソッド
	public static function deleteAlarm($id){

		try{
			$dbh=SingletonPDO::connect();

			$sql="DELETE
					FROM 
						alarms
					WHERE 
						id = :id 
				";

			$res = $dbh->prepare($sql);

			$param=array(
				'id'=>$id
			);

			$res->execute($param);

		}
		catch(Exception $e){
			$dbh->rollback();
			die($e->getMessage());
		}
	}
	
	//新規次のアラーム情報のデータ投入
	public static function setNextAlarmDB($data,$id){
	
		try{
				
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
				
			$sql1="
						INSERT INTO
							next_alarms
							(alarm_id, alarm_time, created)
						VALUES
							(:alarm_id, :alarm_time, NOW())
						";
				
			$stmt=$dbh->prepare($sql1);
	
			$param = array(
	
					'alarm_id' => $id,
					'alarm_time' => $data['alarm_time'],
	
			);
				
			$stmt->execute($param);
			$dbh->commit();
			
				
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
		}
	}
	
	//update次のアラーム情報のデータ投入
	public static function updateNextAlarmDB($data,$set_alarmId){
	
		try{
						
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
	
			$sql1="
						UPDATE next_alarms
							SET
							alarm_id = :alarm_id, alarm_time = :alarm_time, modified = now()
						WHERE
							alarm_id = $set_alarmId";
			
			$stmt=$dbh->prepare($sql1);
	
			$param = array(
	
					'alarm_id' => $set_alarmId,
					'alarm_time' => $data['alarm_time'],
	
			);
	
			$stmt->execute($param);
			$dbh->commit();
				
	
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
		}
	}

	
	//ID単位で「next_alarm」情報を削除する
	public static function deleteNextAlarm($id){
	
		try{
			$dbh=SingletonPDO::connect();
	
			$sql="DELETE
					FROM
						next_alarms
					WHERE
						alarm_id = $id
				";
	
			$res = $dbh->prepare($sql);
	
			$param=array(
				'alarm_id' => $id
			);
	
			$res->execute($param);
	
		}
		catch(Exception $e){
			
			die($e->getMessage());
		}
	}
	
	/*
	 *アラームデータベースの情報を取得
	*/
	
	public static function getNextAlarm(){
	
		try{
	
			$dbh=SingletonPDO::connect();
	
			$sql="
			SELECT
				next_alarms.alarm_time, next_alarms.alarm_id, alarms.id, alarms.daily, alarms.weekly, alarms.date, 
				ADDTIME(now(),'0 00:05:00'), SUBTIME(now(),'0 00:05:00')
			FROM
				next_alarms
			JOIN alarms ON alarms.id=next_alarms.alarm_id
			WHERE
				next_alarms.alarm_time < ADDTIME(now(),'0 00:05:00') 
					AND next_alarms.alarm_time > SUBTIME(now(),'0 00:05:00')
				";
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$NextAlarms=$stmt->fetchAll();
			return $NextAlarms;
	
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
		}
	}
	
	
	/*
	 *「alarm_logs」データベースの情報取得
	*/
	
	public static function getAlarmLog($alarm_id){
	
		try{
	
			$dbh=SingletonPDO::connect();
	
			$sql="
			SELECT
			*
			FROM
			alarm_logs
			WHERE
			alarm_logs.alarm_id = $alarm_id";
	
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas=$stmt->fetchAll();
	
			return $datas;
	
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
		}
	}
	
	/*
	 *「alarm_logs」に情報を入れる
	*/
	
	public static function insertAlarmLog($data){
	
		try{
	
			$dbh=SingletonPDO::connect();
	
			$sql="
			INSERT INTO 
				alarm_logs
				(alarm_id, is_alarm_on, alarm_sent, created)
			VALUES
				(:alarm_id, :is_alarm_on, :alarm_sent, NOW())
			";
				
			$stmt=$dbh->prepare($sql);
			
			$param = array(
			
			'alarm_id' => $data['alarm_id'],
			'is_alarm_on' => $data['is_alarm_on'],
			'alarm_sent' => $data['alarm_sent']
			
			
			);
			
			$stmt=$dbh->prepare($sql);
			$stmt->execute($param);
			
			
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
		}
	}
	
	/*
	 * アラームメールを送る人のcompany_idを使って管理者のメールアドレスを取得
	*/
	public static function adminEmail($company_id){
	
		try{
			$dbh=SingletonPDO::connect();
	
			$sql="
			SELECT
			*
			FROM
			company
			WHERE company.id = $company_id";
				
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$admin=$stmt->fetchAll();
			return $admin;
				
		}catch(Exception $e){
	
			echo $e->getMessage();
	
		}
	}
	
	/*
	 *直近のデータを取得
	*/
	
	public static function last_driver_status($driver_id){
	
		try{
				
			$dbh=SingletonPDO::connect();
	
			$sql="
			SELECT
			*
			FROM
			last_driver_status
			WHERE
			last_driver_status.driver_id=$driver_id";
			
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$last_driver_status=$stmt->fetchAll();
			
			return $last_driver_status;
	
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
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