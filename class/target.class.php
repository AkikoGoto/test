<?php
//Targetクラス　Driverクラスを継承

class Target extends Driver{
	
	/* target */
	public $id;
	public $target_id;
	public $target_time;
	
public static function targetsUpdate($datas){
	
	try{
			$dbh=SingletonPDO::connect();		
				
			//iPhoneから、住所に郵便番号が入る場合、削除
			$matches = array();
			$match_count = preg_match("/^(〒[-\d]+)?(\s+)?(.+)$/", $datas['address'], $matches);
	
			if($match_count > 0) {
	
				$datas['address'] = $matches[3];
				
			}
			
			/*** iPhone用の補正　ここまで ***/
			
			
			$driver_id = $datas["driver_id"];
			
			//idがあれば、新規
			if(!$datas['id']){
				
					$sql = "INSERT 
								INTO target
									(target_id, driver_id, latitude, longitude, address, created)
								VALUES
									(:target_id, :driver_id, :latitude, :longitude, :address, now())
							";
					
					$stmt=$dbh->prepare($sql);
				
					$param = array(
						'target_id' => $datas['target_id'],
						'driver_id' => $datas['driver_id'],
						'latitude' => $datas['latitude'],
						'longitude' => $datas['longitude'],
						'address' => $datas['address']				
					);
					
					$stmt->execute($param);
					return 1;
					
			}
					
		}catch(Exception $e){
	
			echo $e->getMessage();
			return 2;

		}
}

//ターゲットをIDで取得
public static function getTargetById($id){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
			SELECT 
				target.*,
				drivers.first_name, drivers.last_name
			FROM 
				target
			JOIN 
				drivers
				ON target.driver_id=drivers.id
			WHERE
				target.id = $id
			
			";
					
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		
		$data=$stmt->fetchAll();

		return $data;
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}
}

//ターゲットのデータ投入と編集　$statusの値でスイッチ

public static function putInTargetData($datas, $status){

	try{
	
	$dbh=SingletonPDO::connect();
	$dbh->beginTransaction();
	$target_set_date = $datas['target_set_date'];
			
	switch($status){
	//新規データ登録
		case "NEWDATA":

			$sql1="
			INSERT INTO 
				target
				(target_id, driver_id, latitude, longitude, address, target_time, is_picked, picked_date, created)
			VALUES
				(:target_id, :driver_id, :latitude, :longitude, :address, :target_time, :is_picked, :picked_date, \"$target_set_date\")
			";


		break;

	//データ編集
		case "EDIT":
		

		
			$sql1="
			UPDATE 
				target 
			SET 
				target_id=:target_id, driver_id=:driver_id, latitude=:latitude, longitude=:longitude, 
				address=:address, target_time=:target_time, is_picked=:is_picked , picked_date=:picked_date, 
				created=\"$target_set_date\"
			WHERE id=".$datas['id'];
		break;
	}

	
	$stmt=$dbh->prepare($sql1);

		$param = array(

			'target_id' => $datas['target_id'],
			'driver_id' => $datas['driver_id'],
			'latitude' => $datas['latitude'],
			'longitude' => $datas['longitude'],
			'address' => $datas['address'],
			'target_time' => $datas['target_time'],
			'is_picked' => $datas['is_picked'],
			'picked_date' => $datas['picked_date']
		
		);
			
	$stmt->execute($param);	
	
	$dbh->commit();
		
	}catch(PDOException $e){

		echo $e->getMessage();
	
	}
}




//ターゲットの位置情報を取得
public static function getTargetMap($refine = null, $company_id= null){

	try{
		$dbh=SingletonPDO::connect();

		if($refine){
			
			//BETWEENで計算するために、日付に1日プラスする
			//月末だったら次の月の1日をTOにする
			$returned_to_date = between_day($refine['time_to_month'], $refine['time_to_day'],$refine['time_to_year']);
			
			$time_to_month = $returned_to_date['month'];
			$time_to_day = $returned_to_date['day'];

			
			$selected_start = $refine['time_from_year']."-".$refine['time_from_month']."-".$refine['time_from_day'];
			$selected_end = $refine['time_to_year']."-".$time_to_month."-".$time_to_day;

			$refine_sql = "AND 
								target.created 
							BETWEEN
								CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)";
		} 
		
		$sql="
			SELECT 
				target.*,
				drivers.first_name, drivers.last_name
			FROM 
				target
			JOIN 
				drivers
				ON target.driver_id=drivers.id
			WHERE
				drivers.company_id = $company_id
			AND
				(target.is_picked is NULL
			OR
				target.is_picked = 0)
			$refine_sql
			ORDER BY
				target.created 
			
			";
				
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		
		$data=$stmt->fetchAll();

		return $data;
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}
}

//引き取られたターゲットの情報を取得
public static function getPickedTarget($refine = null, $company_id = null){

	try{
		$dbh=SingletonPDO::connect();

		if($refine){
			
			$returned_to_date = between_day($refine['time_to_month'], $refine['time_to_day'], $refine['time_to_year']);			
			$time_to_month = $returned_to_date['month'];
			$time_to_day = $returned_to_date['day'];
						
			$selected_start = $refine['time_from_year']."-".$refine['time_from_month']."-".$refine['time_from_day'];
			$selected_end = $refine['time_to_year']."-".$time_to_month."-".$time_to_day;

			$refine_sql = "AND 
								target.picked_date 
							BETWEEN
								CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)";
		} 
		
		$sql="
			SELECT 
				target.*,
				drivers.first_name, drivers.last_name
			FROM 
				target
			JOIN
				drivers
				ON target.driver_id=drivers.id
			WHERE
				target.is_picked = 1
			AND
				drivers.company_id = $company_id 	
			$refine_sql
			ORDER BY
				target.picked_date 
			
			";
			
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		
		$data=$stmt->fetchAll();

		return $data;
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}
}


//現在地から近いターゲットの位置情報を取得
public static function getNearTarget($datas, $driver_id){

	try{
		$dbh=SingletonPDO::connect();
		
		//緯度、経度の上限と下限を計算

		$lat = $datas['latitude'];
		$long = $datas['longitude'];

		$min_lat = $lat - GPS_DISTANCE;
		$max_lat = $lat + GPS_DISTANCE;

		$min_long = $long - GPS_DISTANCE;
		$max_long = $long + GPS_DISTANCE;
		
		$data_max = DATA_MAX;

		//会社IDを特定

		$driver_info = Driver::getById($driver_id, null);
		$drivers_info2 = $driver_info[0];
		$company_id = $drivers_info2->company_id;
		$drivers = Driver::getDrivers($company_id);

		foreach($drivers as $each_driver){
			$drivers_array[] = $each_driver->id;
		}
		$drivers_array_imploded = implode(',', $drivers_array);
		
		$sql="
			SELECT *
			FROM (	
				SELECT 
					* ,
					(sqrt(pow( (latitude - $lat) * 111, 2 ) + pow((longitude - $long) * 91, 2 ))) AS squared_distance
				FROM 		
					target
				WHERE
					latitude BETWEEN $min_lat AND $max_lat
					AND longitude BETWEEN $min_long AND $max_long
					AND (is_picked is NULL OR is_picked = 0)
			) AS near
			WHERE
				driver_id IN ($drivers_array_imploded)
			ORDER BY squared_distance
			LIMIT $data_max
		";
		
					
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		
		$data=$stmt->fetchAll(PDO::FETCH_ASSOC);

		return $data;
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}
}	

//コンテナを引き取り
public static function PickTarget($datas){

	try{
		$dbh=SingletonPDO::connect();
				
		$id = $datas['id'];
		$target_id = $datas['target_id'];
		
		$sql="
			UPDATE
				target
			SET
				is_picked = 1,
				picked_date = NOW()
			WHERE
				id = $id
			AND	 
				target_id = \"$target_id\"			
		";
		$rows = $dbh->exec($sql);

		if($rows){			
			return 1;
		}else{
			return 2;
		}
		
	}catch(Exception $e){
	
		echo $e->getMessage();
		return 3;

	}
}	

//コンテナのデータを削除するメソッド

public static function deleteTargetById($id){
	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql="DELETE 
				FROM target
				WHERE id = :id
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



//クラスTargetの終了
}
?>