<?php
//Workクラス　Driverクラスを継承

class Work extends Driver{
	
	/* work */
	public $id;
	public $driver_id;
	public $start;
	public $end;
	public $start_address;
	public $end_address;
	public $start_latitude;
	public $start_longitude;
	public $end_latitude;
	public $end_longitude;
	public $distance;
	public $status;
	public $plate_number;
	public $destination_company_name;
	public $amount;
	public $toll_fee;
	public $drive_memo;
	
	/**
	 * ドライバーステータスアップデートでスタートフラグがある場合に、日報データを作成する
	 * @param unknown $datas
	 * @param unknown $created
	 * @return mixed
	 */
	public static function insertWorkWhenStart($datas, $created){
		
		
		try{
			
			$dbh=SingletonPDO::connect();
			
			$sql = "INSERT
						INTO work
							(driver_id, start, end, status, start_address, start_latitude,
							start_longitude, created)
						VALUES
							(:driver_id, :start, :end, :status, :start_address, :start_latitude,
							:start_longitude, :created)
							";
			
			$stmt=$dbh->prepare($sql);
			
			//最新のデータと、POSTされているデータで、ステータスが違う場合は、$createdにプラス1秒させた値を保存させる
			if($datas['flug'] == 1 ){
				$created = $datas['start'];
			}
			
			$param = array(
					'driver_id' => $datas['driver_id'],
					'start' => $datas['start'],
					'end' => $datas['end'],
					'status' => $datas['status'],
					'start_address' => $datas['address'],
					'start_latitude' => $datas['latitude'],
					'start_longitude' => $datas['longitude'],
					'created' => $created
			);
			
			$stmt->execute($param);
			
			$sql = "SELECT LAST_INSERT_ID()";
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$last_work_id = $stmt->fetch();
			
			return $last_work_id;
		
		}catch(PDOException $e){
			
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
		}
		
	}
	
	
	/**
	 * ドライバーステータスの終了時に、終了があった場合日報をアップデートする
	 * @param unknown $recent_work_id
	 * @param unknown $datas
	 * @param unknown $distance
	 * @param unknown $nearDestination
	 * @param unknown $created
	 */
	public static function updateWorkForEnd($recent_work_id, $datas, $distance, $nearDestination, $created){
		
		try{
		
			$dbh=SingletonPDO::connect();		
			
			$sql = "UPDATE
			work
			SET
			end=:end, end_address =:end_address,
			end_latitude =:end_latitude, end_longitude =:end_longitude,
			distance =:distance,
			destination_company_name =:destination_company_name,
			updated = :updated
			WHERE
			id = $recent_work_id
			AND
			driver_id =". $datas['driver_id'];
			
			$stmt=$dbh->prepare($sql);
			
			$param = array(
					'end' => $datas['end'],
					'end_address' => $datas['address'],
					'end_latitude' => $datas['latitude'],
					'end_longitude' => $datas['longitude'],
					'distance' => $distance,
					'destination_company_name' => $nearDestination['destination_name'],
					'updated' => $created
			);
			
			$stmt->execute($param);
		
		}catch(PDOException $e){
			
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
		}
		
	}
	
//ドライバーの作業履歴取得
public static function getWorkRecordsByNew($driver_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
		//直近100件のデータを取得
		$sql = "SELECT 
					work.*,
					TIMEDIFF(end, start) as total_time,
					GROUP_CONCAT(comment.comment SEPARATOR '<br>') as comment
				FROM 
					work
				LEFT JOIN
					comment
				ON
					work.id = comment.work_id
				WHERE
					work.driver_id = $driver_id
				GROUP BY
					work.id
				ORDER BY
					start DESC
				LIMIT 
					100
				";
				
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
		}catch(Exception $e){
	
			echo $e->getMessage();
			return 4;

		}
}

//ドライバーごとの作業履歴を編集
public static function EditWorkRecordsById($id){

	try{
		$dbh=SingletonPDO::connect();
	
		$sql="
		SELECT 
			*
		FROM work
		WHERE work.id = $id

		";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		
		$data=$stmt->fetchAll();

		return $data;

		}
	catch(Exception $e)
		{
	
			echo $e->getMessage();

		}
}

//ドライバー取得 　IDから作業記録を取得
public static function getWorkRecords($datas){

	try{
		$dbh=SingletonPDO::connect();
	
		$sql="
		SELECT 
			*
		FROM work
		WHERE work.id = $datas[id]

		";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		
		$data=$stmt->fetchAll();

		return $datas;

		}
	catch(Exception $e)
		{
	
			echo $e->getMessage();

		}
}

//ドライバーの作業履歴　データ投入と編集　$statusの値でスイッチ
const NEWDATA=1; //新規データ登録
const EDIT=2; //データ編集

public static function putInWorkRecord($datas, $status){

	try{
	
	$dbh=SingletonPDO::connect();
	$dbh->beginTransaction();

	switch($status){
	//新規データ登録
		case NEWDATA:

			$sql1="
			INSERT INTO work
				(driver_id, start, end, status, start_address, end_address, 
				start_latitude, start_longitude, end_latitude, end_longitude,
				distance, plate_number, destination_company_name, amount, toll_fee,
				drive_memo, created)

			VALUES
				(:driver_id, :start, :end, :status, :start_address, :end_address, 
				:start_latitude, :start_longitude, :end_latitude, :end_longitude,
				:distance, :plate_number, :destination_company_name, :amount, :toll_fee,
				:drive_memo, NOW())
			";

		break;


	//データ編集
		case EDIT:

		$sql1="
			UPDATE work
			SET driver_id=:driver_id, start=:start, end=:end, status=:status, 
			start_address=:start_address, end_address=:end_address, 
			start_latitude=:start_latitude, start_longitude=:start_longitude, 
			end_latitude=:end_latitude, end_longitude=:end_longitude,
			distance=:distance, plate_number=:plate_number,
			destination_company_name=:destination_company_name, amount=:amount, 
			toll_fee=:toll_fee,	drive_memo=:drive_memo,  
			created=NOW()
			WHERE id=".$datas['id'];
		break;
	}

	$stmt=$dbh->prepare($sql1);

		$param = array(

		'driver_id' => $datas['driver_id'],
		'start' => $datas['start'],
		'end' => $datas['end'],
		'status' => $datas['status'],
		'start_address' =>$datas['start_address'],
		'end_address' =>$datas['end_address'],
		'start_latitude' => $datas['start_latitude'],
		'start_longitude' => $datas['start_longitude'], 
		'end_latitude' => $datas['end_latitude'], 
		'end_longitude' => $datas['end_longitude'],
		'distance' => $datas['distance'], 
		'plate_number' => $datas['plate_number'],
		'destination_company_name' => $datas['destination_company_name'], 
		'amount' => $datas['amount'], 
		'toll_fee' => $datas['toll_fee'],
		'drive_memo' => $datas['drive_memo']
		
		);
			
	$stmt->execute($param);	
	
	$dbh->commit();
		
	}catch(PDOException $e){

		echo $e->getMessage();
	
	}
}

//ドライバーごとの、乗車記録を削除するメソッド

public static function deleteWorkTimeById($id){

	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql="DELETE 
		FROM work
		WHERE work.id = :id
		";
		
		$res = $dbh->prepare($sql);
		
		$param=array(
		'id'=>$id
		);
		
		$res->execute($param);
	
	}catch(Exception $e){
		$dbh->rollback();
		die($e->getMessage());
	}
}

//日付を指定して、作業時間データの集計
public static function getWorktimeByDate($driver_id, $status, $time_from_year, $time_from_month, $time_from_day, 
	 					$time_to_year, $time_to_month, $time_to_day){

					
	try{
		$dbh=SingletonPDO::connect();		
		
		//BETWEENで計算するために、日付に1日プラスする
		//月末だったら次の月の1日をTOにする 　12月だったら次の月は1、で年数をプラス

		$returned_to_date = between_day($time_to_month, $time_to_day, $time_to_year);
		$time_to_month = $returned_to_date['month'];
		$time_to_day = $returned_to_date['day'];
		$time_to_year = $returned_to_date['year'];
		
/*		if($time_to_day == date('t')){
			$time_to_day = 1;
			$time_to_month = $time_to_month+1;
		}else{
			$time_to_day = $time_to_day+1;			
		}
*/
		$selected_start = $time_from_year."-".$time_from_month."-".$time_from_day;
		$selected_end = $time_to_year."-".$time_to_month."-".$time_to_day;
		
		if($status =="all_select"){
			
			$sql = "SELECT 
						*,
						( CASE WHEN end != '0000-00-00 00:00:00' THEN TIMEDIFF(end, start) ELSE '00:00:00' END) as total_time
					FROM 
						work
					WHERE
						start BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
					AND
						end BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
					AND
						driver_id = $driver_id
					ORDER BY
						start DESC
					";
			
		}elseif($status =="only_work"){
			
			//たくるの場合だけ作る
			$sql = "SELECT 
						*,
						TIMEDIFF(end, start) as total_time 
					FROM 
						work
					WHERE
						start BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
					AND
						end BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
					AND
						driver_id = $driver_id
					AND 
						status NOT IN (3)
					ORDER BY
						start DESC
					";
			
		}elseif($status == "day_report"){

			$sql = "SELECT 
						work.*,
						( CASE WHEN end != 0 THEN TIMEDIFF(end, start) ELSE '00:00:00' END) as total_time,
						GROUP_CONCAT(comment.comment SEPARATOR '\n') as comment
					FROM 
						work
					LEFT JOIN
						comment
					ON
						work.id = comment.work_id
					WHERE
						start BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
					AND
						end BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
					AND
						work.driver_id = $driver_id
					GROUP BY
						work.id
					ORDER BY
						start
					";
				
		}elseif($status !=NULL){

			$sql = "SELECT 
						*,
						( CASE WHEN end != 0 THEN TIMEDIFF(end, start) ELSE '00:00:00' END) as total_time
					FROM 
						work
					WHERE
						start BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
					AND
						end BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
					AND 
						status = $status
					AND
						driver_id = $driver_id						
					ORDER BY
						start DESC
					";			
			
		}else{

			$sql = "SELECT 
						*,
						( CASE WHEN end != 0 THEN TIMEDIFF(end, start) ELSE '00:00:00' END) as total_time
					FROM 
						work
					WHERE
						start BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
					AND
						end BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
					ORDER BY
						start DESC
					";
					
		}

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll(PDO::FETCH_ASSOC);
	
		return $datas;
		
		}catch(Exception $e){
	
			echo $e->getMessage();

		}	 						
	 						
	}

//コメントの挿入
public static function Comment($datas, $driver_id, $work_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
			$sql="
			INSERT 
			INTO
				comment
				(driver_id, work_id, status, comment, created)		
			VALUES
				(:driver_id, :work_id, :status, :comment, now())";
			
			$stmt=$dbh->prepare($sql);

			$param = array(
				'driver_id' => $driver_id,
				'work_id' => $work_id,
				'status' => $datas['status'],
				'comment' => $datas['comment']
			);
			
			$stmt->execute($param);
						
			$status = "SUCCESS";
			
			return $status;
			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

			$status = "DB_ERROR";
			
			return $status;

		}
}

//コメントの取得
public static function getCommentByStatusAndDate($datas, $driver_id){
	
	try{
		$dbh=SingletonPDO::connect();
		
		$status = $datas['status'];
		$start = $datas['start'];
		$end = $datas['end'];
		
		$sql = "SELECT 
					comment
				FROM 
					comment
				WHERE
					driver_id = $driver_id
				AND
					status = $status
				AND 
					created >= '$start'
				AND
					created <= '$end'	
				";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

//アプリからコメントを挿入するために、ドライバーIDから最新の作業履歴の1件を取する
public static function getWorkIdByDriverId($status, $driver_id) {
	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql = "SELECT 
					id
				FROM 
					work
				WHERE
					status = $status
				AND
					driver_id = $driver_id
				ORDER BY
					start DESC
				LIMIT 1
				";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}


/**
 * ドライバーを指定して、最新の直近のWorkのIdを取得する
 * endが0000-00-00 00:00:00でないもの
 */
public static function getRecentWorkIdByDriverId($driver_id) {
	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql = "SELECT 
					id
				FROM 
					work
				WHERE
					driver_id = $driver_id
				ORDER BY
					created DESC
				LIMIT 1
				";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}


//クラスWorkの終了
}
?>