<?php
//Driverクラス　Dataクラスを継承
class Driver extends Data{

	/* driver */
	public $id;
	public $company_id;
	public $last_name;
	public $first_name;
	public $furigana;
	public $mobile_tel;
	public $mobile_email;
	public $experience;
	public $no_accident;
	public $car_type;
	public $equipment;
	public $sex;
	public $birthday;
	public $erea;
	public $updated;
	public $created;
	public $driver_message;
	public $regist_number;
	public $driver_number_actual;
	public $sales;
	public $address;
	public $detail;
	public $speed;
	public $start;
	public $end;
	public $work_id;
	public $registration_id;
	public $ios_device_token;
	public $image_name;
	public $is_group_manager;
	public $is_branch_manager;
	public $geographic_name;
	public $has_read;
	public $isTransporting;


	
	//ドライバーの現在位置住所　、ステータスをDBへ入力

	public static function statusUpdate($datas){

		try{
			
			$datas = formatSmartPhonePost($datas);

			$driver_id = $datas["driver_id"];
			
			//保存された時間
			if($datas['saved_time']){
				$created = $datas['saved_time'];
			}else{
				$created = date("Y-m-d H:i:s");
			}
			
 			//ストップボタンが押された場合のロジックを記載
			if($datas['end'] && $datas['end']!=="NULL"){

				$is_success = Driver::stop_update( $datas, $driver_id, $created );				
			
			}else{
			
				//ステータスが「休憩/待機」だったら、登録のみ行う
				if($datas['status'] != 4){
				
					//今日のデータがあるかないか
					$today_data = Driver::today_data($driver_id);
					
					//今日のデータがなければ以下を通る
					if($today_data == NULL){
						
						$datas['status'] = 3;
						//作業中(積込)で今いる場所を登録するロジック記載
						$is_success = Driver::register_data( $datas, $driver_id, $created );
						
					//今日のデータがあれば以下を通る
					}else{
						

						$destination_data = Destination::getByDriverId($driver_id);
						$count = count($destination_data);
						
						if($destination_data !== NULL){
							
							for($i = 0; $i<$count; $i++){
								
								$distance = getDistanceToDestinations($datas, $destination_data, $i);
							
								if($distance < 500 && !($distance==null)){
									$datas['category_name'] = $destination_data[$i]['name'];
										break;
								}
								
							}
							
						}
						
						//配送先と今いる位置の離れている距離が指定した距離内かどうか(積込エリアもしくは荷下エリア)
						if($distance < 500 && !($distance==null)){
							
							$datas = Driver::changeParameterNearDestinations($datas, $driver_id, $created);
													
							
						//配送先と今いる位置の離れている距離が指定した距離内かどうか(輸送中エリア、帰走中エリア)
						}else{							
							
							$datas = Driver::changeParameterFarFromDestinations($datas, $driver_id, $created);
								
						}
					
					}
					
				}
			

				$is_success = Driver::register_data( $datas, $driver_id, $created );
				
				return $is_success;
			}
						
		}catch(PDOException $e){

			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
			return 4;

		}
	}

	/**
	 * 配送先の近くにいない場合にパラメータを変更する
	 * @param unknown $datas
	 * @param unknown $driver_id
	 * @param unknown $created
	 * @return unknown|mixed
	 */
	private static function changeParameterFarFromDestinations($datas, $driver_id, $created){
		
		$datas['flug'] = 0;
		
		//最新のデータを確認する
		$newest_data = Driver::last_driver_data($driver_id);
		$datas['work_id'] = $newest_data['work_id'];
		
		//POSTされたデータに、END値とSTART値を設定する。
		if( $newest_data['status'] == 1 || $newest_data['status'] == 5 ){
			
			$datas['end'] = $created;
			$start_date = date_add(date_create($created), date_interval_create_from_date_string('1 second'));
			$datas['start'] = date_format($start_date, 'Y-m-d H:i:s');
			$datas['flug'] = 2;
			$datas['category_name'] = $destination_data['name'];
			
		}
		

		if(!($newest_data['status']==1)){
			$datas['status'] = $newest_data['status'] ;
		}
		
		return $datas;
		
	}
	
	
	/**
	 * 配送先の近くにいた場合、ステータスを変更したり、終了を入れたりする
	 * @param unknown $datas
	 * @param unknown $driver_id
	 * @param unknown $created
	 * @return number
	 */
	private static function changeParameterNearDestinations($datas, $driver_id, $created){
		
		//最新のデータを確認する
		$newest_data = Driver::last_driver_data($driver_id);
		$datas['work_id'] = $newest_data['work_id'];

		$datas['flug'] = 0;
		
		//最新のデータが2 or 3の場合、POSTされたデータに、END値とSTART値を設定する。
		if( $newest_data['status'] == 2 || $newest_data['status'] == 3 ){
			
			$datas['end'] = $created;
			$start_date = date_add(date_create($created), date_interval_create_from_date_string('1 second'));
			$datas['start'] = date_format($start_date, 'Y-m-d H:i:s');
			$datas['status'] = $newest_data['status']; //例　輸送中もしくは、帰走中の場合　いらないかも
			$datas['flug'] = 1;
			
			//ステータスが同じ場合
		}else{
			//ステータスを判別する。荷卸エリアの場合は、ステータスを変更、積込エリアの場合は既に定義されているので変更しない
			if( $datas['category_name'] == '荷卸先' || $datas['category_name'] == '中間貯蔵施設' ){
				
				$datas['status'] = 5 ;
				
			}
		}
		
		return $datas;
	}
	


	/**
	 * 直近の日報のレコードと現在地の距離の計算
	 * @param unknown $datas
	 * @param unknown $recent_work_id
	 * @return number
	 */
	public static function calculateDistance($datas, $recent_work_id){

		try{
			$dbh=SingletonPDO::connect();

			$sql = "SELECT
				  id,
				  start_latitude,
				  start_longitude,
			      (6371 * acos(
				        cos( radians(".$datas['latitude'].") ) * cos( radians( start_latitude ) ) *
				        cos( radians( start_longitude ) - radians(".$datas['longitude'].") ) + 
				        sin( radians(".$datas['latitude'].") ) * sin( radians( start_latitude ) ) 
			      	)
			  ) AS distance
				FROM
					work
				WHERE
					id = $recent_work_id
				AND
					driver_id =". $datas['driver_id'];
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$distances = $stmt ->fetch();
			$distance = round( $distances['distance'], 2 );
	
	
			return $distance;
		
		}catch(PDOException $e){
			
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
		}

	}

	public static function putInRecord($datas, $status){
		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			switch($status){
				//新規データ登録
				case NEWDATA:
					// driver_statusとlast_driver_status両方にinsertする。
					$sql1="
			INSERT INTO driver_status
				(driver_id, latitude, longitude, status, sales, address, detail, speed, start, end, created)
			VALUES
				(:driver_id, :latitude, :longitude, :status, :sales, :address, :detail, :speed, :start, :end, :recreated);";

					break;


					//データ編集
				case EDIT:

					$sql1="
			UPDATE driver_status
			SET driver_id=:driver_id, latitude = :latitude, longitude = :longitude, status=:status, 
			sales=:sales, address=:address, speed=:speed, start=:start, end=:end, detail=:detail, created=:recreated
			WHERE id=${datas['id']};";
					break;
		}

		$stmt=$dbh->prepare($sql1);

		$param = array(

		'driver_id' => $datas['driver_id'],
		'status' => $datas['status'],
		'latitude' => $datas['latitude'],
		'longitude' => $datas['longitude'], 
		'sales' => $datas['sales'],
		'address' => $datas['address'],
		'detail' => $datas['detail'],
		'start' => $datas['start'],
		'end' => $datas['end'],
		'speed' => $datas['speed'],
		'recreated' => $datas['recreated'],

		);
			
		$stmt->execute($param);

		//開始フラグがあった場合
		if($datas['start']){

			switch($status){
				//新規データ登録
				case NEWDATA:

					$sql1="
				INSERT INTO work
					(driver_id, start, end, status, created, updated)
				VALUES
					(:driver_id, :start, :end, :status, NOW(), NOW())
				";

					break;

					//データ編集
				case EDIT:

					$sql1="
				UPDATE 
					work
				SET 
					driver_id = :driver_id, start = :start, end = :end, status = :status, 
					updated = NOW()
				WHERE id=".$datas['id'];
					break;
			}

			$stmt=$dbh->prepare($sql1);

			$param = array(

			'driver_id' => $datas['driver_id'],
			'start' => $datas['start'],
			'end' => $datas['end'],
			'status' => $datas['status']

			);

			$stmt->execute($param);

		}

		//終了フラグがあった場合
		if($datas['end']){

			$driver_id = $datas['driver_id'];
			$driver_status = $datas['status'];
			$endtime = $datas['end'];

			switch($status){

				//新規データ登録
				case NEWDATA:

					$sql1="SELECT
							id
						FROM
							work
						WHERE
							driver_id = $driver_id
						AND
							status = $driver_status
						AND
							end IS NULL
						AND
							start < CAST('$endtime' AS DATETIME)
						ORDER BY
							start DESC
						LIMIT 
							1 	
						";

					$stmt=$dbh->prepare($sql1);
					$stmt->execute();
					$data=$stmt->fetchAll();

					if($data[0]['id']){
						$existed_work_id = $data[0]['id'];
							
						$sql1="UPDATE
						work
					SET 
						end = :end, 
						updated = NOW()
					WHERE id = $existed_work_id";
					}

					break;

					//データ編集
				case EDIT:

					$sql1="UPDATE
						work
					SET 
						end = :end, 
						updated = NOW()
					WHERE end = CAST('$endtime' AS DATETIME)";
					break;
			}

			$stmt=$dbh->prepare($sql1);

			$param = array(

			'end' => $datas['end'],

			);

			$stmt->execute($param);

		}


		$dbh->commit();

		Driver::_updateLastDriverStatus($dbh, $datas['driver_id']);

	}catch(PDOException $e){

		echo $e->getMessage();

	}
}


/**
 *
 * GCM用のregistration_idを更新するメソッド
 * @param unknown_type $datas
 */
public static function updateRegId($driver_id, $registration_id){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
			UPDATE
				drivers
			SET
				registration_id = :registration_id
			WHERE
				id = :driver_id	
		";

		$stmt=$dbh->prepare($sql);
		$param = array(
				'registration_id' => $registration_id,
				'driver_id' => $driver_id			
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

/**
 *
 * iOS用のプッシュ通知トークンの管理
 */
//プッシュ通知用トークンの更新
public static function updateIOSDeviceToken($driver_id, $ios_device_token){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
			UPDATE
				drivers
			SET
				ios_device_token = :ios_device_token
			WHERE
				id = :driver_id	
		";

		$stmt=$dbh->prepare($sql);
		$param = array(
				'ios_device_token' => $ios_device_token,
				'driver_id' => $driver_id			
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

//プッシュ通知用トークンの登録
public static function PutinIOSDeviceToken($id, $ios_device_token){
	try{

		$dbh=SingletonPDO::connect();
			
		$sql="UPDATE
						drivers
					SET
						ios_device_token = \"$ios_device_token\" 
					WHERE 
						id = $id";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		return $dataList;

	}catch(Exception $e){

		echo $e->getMessage();
	}
}

//プッシュ通知用トークンの取得
public static function getIOSDeviceToken($driver_id) {
	try{

		$dbh=SingletonPDO::connect();
			
		$sql="
					SELECT
						ios_device_token
					FROM drivers
					WHERE
						id = $driver_id
					LIMIT 1";
			
		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		while($res=$stmt->fetchObject(__CLASS__)){
			$dataList[]=$res;
		}
			
		return $dataList;

	}catch(Exception $e){

		echo $e->getMessage();
	}
}



//現在空車を登録しているドライバーと会社名を取得
public static function getDriverVacant(){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
			SELECT driver_status.latitude, driver_status.longitude, driver_status.updated, 
			driver_status.status, driver_status.address, driver_status.driver_id, drivers.last_name, 
			drivers.first_name, drivers.mobile_tel, drivers.company_id, company.company_name, prefectures.prefecture_name
			FROM driver_status
			JOIN drivers ON driver_status.driver_id = drivers.id
			JOIN company ON drivers.company_id = company.id
			JOIN geographic ON geographic.id = drivers.geographic_id
			JOIN prefectures ON geographic.prefecture = prefectures.id
			WHERE EXISTS (
			
				SELECT *
				FROM (
				
					SELECT driver_id, MAX( updated ) AS updated
					FROM driver_status
					WHERE STATUS =1
					GROUP BY driver_id
					
				) AS last_driver_status
				WHERE last_driver_status.driver_id = driver_status.driver_id
				AND last_driver_status.updated = driver_status.updated
			
				)
			GROUP BY company.id
			LIMIT 30 ";

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

//現在空車を登録しているドライバーと会社名を全部取得
public static function getDriverVacantAll(){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
			SELECT driver_status.latitude, driver_status.longitude, driver_status.updated, 
			driver_status.status, driver_status.address, driver_status.driver_id, drivers.last_name, 
			drivers.first_name, drivers.mobile_tel, drivers.company_id, company.company_name, prefectures.prefecture_name
			FROM driver_status
			JOIN drivers ON driver_status.driver_id = drivers.id
			JOIN company ON drivers.company_id = company.id
			JOIN geographic ON geographic.id = drivers.geographic_id
			JOIN prefectures ON geographic.prefecture = prefectures.id
			WHERE EXISTS (
				SELECT *
				FROM last_driver_status
				WHERE last_driver_status.driver_id = driver_status.driver_id
				AND last_driver_status.updated = driver_status.updated
				AND last_driver_status.status = 1
				)
			ORDER BY driver_status.updated DESC
			LIMIT 30 ";

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

//ドライバーログイン
public static function login($login_id,$passwd){

	try{
		$dbh=SingletonPDO::connect();

		// idとパスワードでユーザーテーブルを検索
		$sql = "SELECT
	    			* FROM drivers
	           	WHERE 
	           		login_id = '$login_id'
	            AND 
	             	passwd = MD5('$passwd')";
		 
		$res=$dbh->prepare($sql);
		$res->execute();
			
		$idArray = $res->fetchAll(PDO::FETCH_ASSOC);
			
		return $idArray;

	}catch(Exception $e){

		echo $e->getMessage();

	}
}

/*アイコン画像を消去
 * ver2.6から
 * @param $driver_id
 */
public static function deleteImage($driver_id){

	try{
		$dbh=SingletonPDO::connect();

		//物理ファイルを消去

		$sql="SELECT
				image_name
			 FROM 
			 	drivers 
			 WHERE 
			 	id = :driver_id";

		$res = $dbh->prepare($sql);

		$param=array(
			'driver_id'=>$driver_id
		);

		$res->execute($param);
		$file_names = $res->fetchAll(PDO::FETCH_NUM);
		$file_url = $file_names[0][0];
		$file_name_array =explode('/',$file_url);
		$file_name = array_pop($file_name_array);

		if($file_name){

			//縮小版と、通常版を削除
			if(file_exists(SERVER_IMAGE_PATH."\\$company_id\\resized\\$file_name")){
				unlink(SERVER_IMAGE_PATH."\\$company_id\\resized\\$file_name");
				unlink(SERVER_IMAGE_PATH."\\$company_id\\$file_name");
			}
		}

		//DELETEではなく、UPDATE


		$sql="UPDATE
					drivers
				SET	
					image_name = NULL
				WHERE id = :driver_id 
				";

		$res = $dbh->prepare($sql);

		$param=array(
			'driver_id'=>$driver_id
		);

		$res->execute($param);

	}
	catch(Exception $e){
		die($e->getMessage());
	}
}


//自動ログイン時の、ドライバーIDのみで情報を返す処理
public static function autoLogin($id){
	try{

		$dbh = SingletonPDO::connect();

		// idとパスワードでユーザーテーブルを検索 　Webから登録した場合のみ、ログインできる。
		$sql = "SELECT
    				* FROM drivers
            	WHERE 
            		id=$id
            	LIMIT 1
				";

		$res=$dbh->prepare($sql);
		$res->execute();

		$idArray = $res->fetchAll(PDO::FETCH_ASSOC);
			
		return $idArray;

	}catch(Exception $e){

		echo $e->getMessage();

	}
}

/**
 * 自動ログイン用クレデンシャル生成
 * @param int $driver_id 会社ID
 * @return string クレデンシャル
 */
public static function createCredential($driver_id) {
	try{

		$credential = uniqid("", TRUE);
		$dbh = SingletonPDO::connect();

		// idでドライバーテーブルを検索
		$sql =<<< EOL
INSERT INTO driver_credentials(credential, driver_id, expires, created)
VALUES(:credential, :driver_id, now() + INTERVAL 1 MONTH, now())
EOL;
			
		$res = $dbh->prepare($sql);
		$param = array(
				':credential' => $credential,
				':driver_id' => $driver_id,
		);

		$res->execute($param);
		setcookie('driver_credential', $credential, time()+60*60*24*30, '/',  $_SERVER['SERVER_NAME'], FALSE, TRUE);

		return $credential;

	}catch(Exception $e){

		echo $e->getMessage();

	}
}

/**
 * 自動ログイン用クレデンシャルで認証する
 * @param string $credential クレデンシャル
 * @return array 会社情報:
 */
public static function authenticateCredential($credential){
	try{

		$dbh = SingletonPDO::connect();

		// idでドライバーテーブルを検索
		$sql =<<<EOL
SELECT drivers.*
FROM drivers
INNER JOIN driver_credentials
ON drivers.id = driver_credentials.driver_id
WHERE credential = :credential
AND expires > now( )
LIMIT 1
EOL;
		$res=$dbh->prepare($sql);
		$param = array(':credential' => $credential);
		$res->execute($param);
			
		$idArray = $res->fetchAll(PDO::FETCH_ASSOC);

		return $idArray;

	}catch(Exception $e){

		echo $e->getMessage();

	}
}

/**
 * 自動ログイン用クレデンシャル削除
 */
public static function deleteCredential() {
	if (!isset($_COOKIE['driver_credential'])) return;

	$credential = $_COOKIE['driver_credential'];
	try{

		$dbh = SingletonPDO::connect();

		$sql =<<<EOL
DELETE FROM driver_credentials
WHERE credential = :credential
EOL;
		$res=$dbh->prepare($sql);
		$param = array(':credential' => $credential);
		$res->execute($param);
		setcookie('driver_credential', '', 1, '/',  $_SERVER['SERVER_NAME'], FALSE, TRUE);

		return;

	}catch(Exception $e){

		echo $e->getMessage();

	}

}

/**
 * 自動ログイン用クレデンシャル再生成
 * @param int $driver_id 会社ID
 * @return string 再生成されたクレデンシャル
 */
public static function regenerateCredential($driver_id){
	try{
		Driver::deleteCredential();
		$new_credential = Driver::createCredential($driver_id);
		return $new_credential;

	}catch(Exception $e){

		echo $e->getMessage();

	}
}




/**
 * idを指定して、データ内容を取得　コンテンツ
 * @param $id, $from_web 第二引数には意味ない…
 * 
 */
public static function getById($id, $from_web){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
		SELECT 
			company.*,
			geographic.*,
			geographic.id as geographic_id,
			drivers.*
		FROM drivers
		JOIN company
			ON drivers.company_id=company.id
		LEFT JOIN geographic
			ON drivers.geographic_id=geographic.id
		WHERE drivers.id = $id ";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		while($res=$stmt->fetchObject(__CLASS__)){

			$data[]=$res;

		}
			
		return $data;

	}
	catch(Exception $e)
	{

		echo $e->getMessage();

	}
}

//company_idを指定して、会社単位のドライバーの位置情報を取得
public static function getDriverMap($company_id, $vacancy){

	try{
		$dbh=SingletonPDO::connect();

		$vacancy_sql= '';
		if($vacancy==1||$vacancy==2||$vacancy==3||$vacancy==4||$vacancy==5||$vacancy==6||$vacancy==7||$vacancy==8||
				$vacancy==9||$vacancy==10||$vacancy==11){
			$vacancy_sql=' AND last_driver_status.status='.$vacancy;
		}
		
		$sql =<<<EOL
SELECT 
	last_driver_status.latitude,
	last_driver_status.longitude,
	last_driver_status.created,
	last_driver_status.status,
	last_driver_status.address,
	last_driver_status.direction,
	last_driver_status.driver_id,
	drivers.last_name,
	drivers.first_name,
	drivers.mobile_tel,
	drivers.image_name,
	drivers.car_type,
	drivers.geographic_id
FROM 
	last_driver_status
INNER JOIN drivers
	ON last_driver_status.driver_id = drivers.id
WHERE
	company_id = :company_id
AND
	last_driver_status.created > NOW()-INTERVAL 1 MONTH
	${vacancy_sql}
ORDER BY last_driver_status.created DESC	
EOL;
	$stmt=$dbh->prepare($sql);

	$params = array(
				':company_id' => $company_id
	);
	$stmt->execute($params);

	$data = $stmt->fetchAll();

	return $data;

}
catch(Exception $e)
{

	echo $e->getMessage();

}
}

public static function getDriverMapByBranch($company_id, $vacancy, $branch){
	try{
		$dbh=SingletonPDO::connect();

		if($vacancy==1||$vacancy==2||$vacancy==3||$vacancy==4||$vacancy==5||$vacancy==6||$vacancy==7||$vacancy==8||
				$vacancy==9||$vacancy==10||$vacancy==11){
			$vacancy_sql=' AND last_driver_status.status='.$vacancy;
		}
		
	$sql =<<<EOL
SELECT 
	last_driver_status.latitude,
	last_driver_status.longitude,
	last_driver_status.created,
	last_driver_status.status,
	last_driver_status.address,
	last_driver_status.direction,
	last_driver_status.driver_id,
	drivers.last_name,
	drivers.first_name,
	drivers.car_type,
	drivers.mobile_tel,
	drivers.image_name,
	drivers.id as driver_id,
	geographic.id,
	geographic.id as geographic_id		
FROM 
	last_driver_status
INNER JOIN drivers
	ON last_driver_status.driver_id = drivers.id
INNER JOIN company
	ON drivers.company_id = company.id
INNER JOIN geographic 
	ON geographic.company_id = company.id

WHERE
	drivers.company_id = :company_id
AND
	last_driver_status.created > NOW()-INTERVAL 1 MONTH
	${vacancy_sql}
AND 
	drivers.geographic_id = $branch
AND
	geographic.id = $branch
ORDER BY last_driver_status.created DESC	
EOL;


	$stmt=$dbh->prepare($sql);

	$params = array(
		':company_id' => $company_id
	);
	$stmt->execute($params);

	$data = $stmt->fetchAll();

	return $data;

}
catch(Exception $e)
{

	echo $e->getMessage();

}
	
}


/**
 * idを指定して、ドライバーの名前のみを取得
 * @param $id ドライバーID
 * @return $data[0]['last_name'] 名字
 * @return $data[0]['first_name']　名前
 */
public static function getNameById($id){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
		SELECT 
			first_name,last_name
		FROM drivers
		WHERE drivers.id = $id ";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		while($res=$stmt->fetch()){

			$data[]=$res;

		}
			
		return $data;

	}
	catch(Exception $e)
	{

		echo $e->getMessage();

	}
}


//ドライバーごとの、乗車記録を取得　コンテンツ
public static function getStatusById($driver_id, $limit = null, $time_from = null, $time_to = null){

	if($limit){
		//		$max_driver_records = $limit;
		$limit_condition = "LIMIT $limit";
	}elseif(($limit ==null) && ($time_from !=NULL)){

		$limit_condition = "";

	}else{

		$limit_condition = "LIMIT ".MAX_DRIVER_RECORDS;

	}

	if($time_from && $time_to){
		$condition = "AND created >= \"$time_from\" AND created <= \"$time_to\"";
	}else{
		$condition = "";
	}


	try{
		$dbh=SingletonPDO::connect();
			
		$sql="
		SELECT 
			id,
			status,
			address,
			sales,
			detail,
			latitude,
			longitude,
			speed,
			start,
			end,
			work_id,
			is_deviated,
			created,
			updated
		FROM driver_status
		WHERE driver_id = $driver_id
		$condition
		ORDER BY created DESC, driver_status.id DESC
		$limit_condition ";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		$data = null;
		
		while($res=$stmt->fetchAll()){

			$data[]=$res;

		}

		return $data;

	}
	catch(Exception $e)
	{

		echo $e->getMessage();

	}
}

//ドライバー全員の乗車記録を取得　コンテンツ
public static function getAllDriverStatus($display_driver_ids, $allDrivers, $time_from = null, $time_to = null){

	try{

		$dbh=SingletonPDO::connect();

		$drivers = array();
		if (count($display_driver_ids) > 0) {
			foreach ($allDrivers as $driver) {
				if (in_array( $driver['id'], $display_driver_ids)) {
					array_push( $drivers, $driver);
				}
			}
		} else {
			$drivers = $allDrivers;
		}

		//ドライバーの乗車記録を格納する配列を生成
		$driverStatuses = array();
		//一人ずつ、ドライバーの乗車記録を取得し、上の配列に格納する
		if (count($drivers) > 0) {
			foreach ($drivers as $driver) {
				$driverStatus = self::getStatusById($driver['id'], null, $time_from, $time_to);
				if ($driverStatus != null) {
					$driver['driver_status'] = $driverStatus[0];
					// カラーをランダム生成
					$driver['color'] = get_attention_color();
					array_push( $driverStatuses, $driver);
				}
			}
		}
			
		return $driverStatuses;

	}
	catch(Exception $e)
	{

		echo $e->getMessage();

	}
}

//ドライバー全員の乗車記録を取得　コンテンツ
public static function getDriverIDsHaveStatusRecorded($display_driver_ids, $allDrivers, $time_from = null, $time_to = null){

	if (count($display_driver_ids) == 0) {
		return array();
	}
	$condition = 'driver_id in ('. join(',', $display_driver_ids) .') ';
	
	if($time_from && $time_to){
		$condition .= "AND created >= \"$time_from\" AND created <= \"$time_to\"";
	}
	
	try{

		$dbh=SingletonPDO::connect();

		$sql1=<<<EOL
SELECT distinct driver_id
FROM driver_status
WHERE $condition
ORDER BY driver_id
EOL;

		$stmt=$dbh->prepare($sql1);
		$stmt->execute();
		$drivers=$stmt->fetchAll();
		
		$driver_ids = array();
		foreach($drivers as $driver) {
			$driver_ids[] = $driver['driver_id'];
		}
		
		return $driver_ids;

	}
	catch(Exception $e)
	{

		echo $e->getMessage();

	}
}

//ドライバー全員の乗車記録を取得　コンテンツ
public static function getAllDriverByCompanyId($company_id){

	try{

		$dbh=SingletonPDO::connect();

		$is_user_selected_drivers_for_dislay = false;

		if (count($display_driver_ids) > 0) {
			$is_user_selected_drivers_for_dislay = true;
		}

		//		$startTime = microtime(true);

		// ドライバーのIDを取得する
		$sql1="
		SELECT
			id,
			last_name,
			first_name
		FROM drivers
		WHERE company_id = $company_id
		AND EXISTS (
			SELECT id FROM driver_status WHERE driver_status.driver_id = drivers.id)
		GROUP BY drivers.id";

		/*
		 $sql1="
		 SELECT
			drivers.id,
			last_name,
			first_name
			FROM drivers
			JOIN driver_status ON driver_status.driver_id = drivers.id
			WHERE company_id = $company_id
			GROUP BY drivers.id";
			*/
		$stmt=$dbh->prepare($sql1);
		$stmt->execute();
		$drivers=$stmt->fetchAll();

		//		$endTime = microtime(true);
		//		echo $endTime - $startTime . '秒';

		return $drivers;

	}
	catch(Exception $e)
	{

		echo $e->getMessage();

	}
}

//ドライバーごとの、乗車記録を削除するメソッド

public static function deleteStatusById($id){


	try{
		$dbh=SingletonPDO::connect();

		// last_driver_statusからドライバーのデータを削除
		// 指定されたid以外で最新のデータをinsertする。
		// ※　ドライバーのデータが一つしかない場合に対応するため
		// 最後に指定idのdriver_statusを削除

		$sql_select ="SELECT driver_id FROM driver_status WHERE id = :id";

		$sql_delete =<<<EOL
DELETE 
FROM driver_status
WHERE driver_status.id = :id;
EOL;

		$dbh->beginTransaction();

		// driver_id取得
		$res = $dbh->prepare($sql_select);

		$param=array(
		':id' => $id
		);

		$res->execute($param);
		$driver_id = $res->fetchColumn();

		// driver_status削除、last_driver_status更新
		$res = $dbh->prepare($sql_delete);

		$res->execute($param);

		$dbh->commit();

		echo 'update last_driver_status driver_id' . $driver_id;
		Driver::_updateLastDriverStatus($dbh, $driver_id);
	}
	catch(Exception $e){
		$dbh->rollback();
		die($e->getMessage());
	}
}

//ドライバー取得 　IDから乗車記録を取得
public static function getDriversRecord($datas){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
		SELECT
			*
		FROM driver_status
		WHERE driver_status.id = $datas[id]
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

//既に同じログインIDが存在するかチェック
public static function isExistingDriverId($login_id){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
		SELECT 
			*
		FROM drivers
		WHERE login_id = '$login_id'
		ORDER BY created DESC
		LIMIT 1
		";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		$data=$stmt->fetchAll();

		if ($data) {
			return true;
		}

		return false;

	} catch(Exception $e) {

		echo $e->getMessage();

	}

}


//ドライバーごとの、乗車記録を編集するメソッド
public static function EditStatusById($id){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
		SELECT 
			driver_status.id,
			driver_status.status,
			driver_status.sales,
			driver_status.address,
			driver_status.speed,
			driver_status.start,
			driver_status.end,
			driver_status.work_id,
			driver_status.detail,
			driver_status.created
		FROM driver_status
		WHERE driver_status.id = $id

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

//ドライバー情報　データ投入と編集　$statusの値でスイッチ
//const DRIVER_NEWDATA=4; //新規データ登録
//const DRIVER_EDIT=5; //データ編集

public static function putInDriver($datas, $status){

	try{
		$dbh=SingletonPDO::connect();

		switch($status){
			//新規データ登録
			case "NEWDATA":

				$md5_passwd=md5($datas['passwd']);

				$sql1="
			INSERT INTO drivers 
			(
				company_id,last_name,first_name,furigana, mobile_tel, mobile_email, experience,
				no_accident,
				car_type,equipment,sex,birthday,erea,regist_number,driver_message,
				geographic_id,login_id,passwd,image_name, is_branch_manager, created, updated
				)
			VALUES(
				:company_id,:last_name,:first_name,:furigana, :mobile_tel, :mobile_email,
				:experience,:no_accident,
				:car_type,:equipment,:sex,:birthday,:erea,:regist_number,:driver_message, 
				:geographic_id,:login_id,:passwd,:image_name, :is_branch_manager,now(),now()
			)
			";
				break;

				//データ編集
			case "EDIT":
				//$company_id=$datas['id'];
				if($datas['passwd']){
					$md5_passwd=md5($datas['passwd']);

				}else{

					$sql_passwd="SELECT passwd FROM drivers WHERE id=".$datas['id'];
					$stmt=$dbh->prepare($sql_passwd);
					$stmt->execute();

					$res=$stmt->fetch();
					$exited_passwd=$res[0];
					$md5_passwd=$exited_passwd;

				}
					
				$sql1="UPDATE drivers SET
				company_id=:company_id, last_name=:last_name,
				first_name=:first_name,furigana=:furigana, 
				mobile_tel=:mobile_tel,	mobile_email=:mobile_email, 
				experience=:experience,
				no_accident=:no_accident,car_type=:car_type, equipment=:equipment, 
				sex=:sex, birthday=:birthday, erea=:erea, regist_number=:regist_number,
				driver_message=:driver_message, 
				geographic_id=:geographic_id,
				login_id=:login_id,passwd=:passwd,
				image_name=:image_name,
				is_branch_manager = :is_branch_manager,
				created=now()
				WHERE id=".$datas['id']	;		

				break;


		}

		$stmt=$dbh->prepare($sql1);

		$param = array(
		'company_id' => $datas['company_id'],
		'last_name' => $datas['last_name'],
		'first_name' => $datas['first_name'],
		'furigana' => $datas['furigana'],
		'mobile_tel' => $datas['mobile_tel'],
		'mobile_email' => $datas['mobile_email'],
		'experience' => $datas['experience'],
		'no_accident' => $datas['no_accident'],
		'car_type' => $datas['car_type'],
		'equipment' => $datas['equipment'],
		'sex' => $datas['sex'],
		'birthday' => $datas['birthday'],
		'erea' => $datas['erea'],
		'regist_number' => $datas['regist_number'],
		'driver_message' => $datas['driver_message'],
		'geographic_id'=>$datas['geographic_id'],
		'login_id'=> $datas['login_id'],
		'image_name'=> $datas['image_name'],
		'is_branch_manager' => $datas['is_branch_manager'],
		'passwd'=> $md5_passwd
		);

		$stmt->execute($param);


	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

public static function registerDriverFromApp($datas){

	try{
		$dbh=SingletonPDO::connect();

		$md5_passwd=MD5($datas['passwd']);

		$sql1="
			INSERT INTO drivers 
			(
				company_id, last_name, first_name, mobile_tel, mobile_email,
				geographic_id, login_id, passwd, registration_id, ios_device_token, created, updated
				)
			VALUES(
				:company_id, :last_name, :first_name, :mobile_tel, :mobile_email, 
				:geographic_id, :login_id, :passwd, :registration_id, :ios_device_token, now(), now()
			)
			";

		$stmt=$dbh->prepare($sql1);

		$param = array(
		'company_id' => $datas['company_id'],
		'last_name' => $datas['last_name'],
		'first_name' => $datas['first_name'],
		'mobile_tel' => $datas['mobile_tel'],
		'mobile_email' => $datas['mobile_email'],
		'geographic_id'=>$datas['geographic_id'],
		'login_id'=> $datas['login_id'],
		'passwd'=> $md5_passwd,
		'registration_id' => $datas['registration_id'],
		'ios_device_token' => $datas['ios_device_token']
		);

		$stmt->execute($param);


	}catch(PDOException $e){

		echo $e->getMessage();

	}
}


//AndroidGCMのRegistrationIDを登録 途中
public static function PutinGCM($id, $registaion_id){
	try{

		$dbh=SingletonPDO::connect();

		$sql="UPDATE
					drivers
				SET
					registration_id = \"$registaion_id\" 
				WHERE 
					id = $id";
			
		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		return $dataList;

	}catch(Exception $e){

		echo $e->getMessage();
	}
}

//AndroidGCMのRegistrationIDを取得
public static function getGCM($driver_id) {
	try{

		$dbh=SingletonPDO::connect();

		$sql="
				SELECT
					registration_id
				FROM drivers
				WHERE
					id = $driver_id
				LIMIT 1";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		while($res=$stmt->fetchObject(__CLASS__)){
			$dataList[]=$res;
		}

		return $dataList;

	}catch(Exception $e){

		echo $e->getMessage();
	}
}

/**
 * 政府ユーザーの場合、JVの下のドライバーの記録を取得する
 */
public static function getJvsDrivers(){
	try{

		$dbh=SingletonPDO::connect();

		if(!empty($geographic_id)){
			$condition = " AND geographic.id = $geographic_id";
		}else{
			$condition = "";
		}


		$sql="SELECT
				drivers.id as id,
				geographic.id as geographic_id,
				geographic.name as geographic_name,
				drivers.last_name, 
				drivers.first_name,
				drivers.company_id, 
				drivers.regist_number,
				drivers.is_group_manager,
				drivers.is_branch_manager,
				drivers.created
			 FROM 
				drivers 
			LEFT JOIN
				geographic
			ON
				drivers.geographic_id = geographic.id
			LEFT JOIN
				company
			ON
				drivers.company_id = company.id
			LEFT JOIN
				company_rolls
			ON
				company_rolls.id = company.company_roll_id	
			WHERE 
			 	company_rolls.type = 'JV' 
			
				";

		 	$stmt=$dbh->prepare($sql);
		 	$stmt->execute();
		 	
		 	while($res=$stmt->fetchObject(__CLASS__)){

		 		$dataList[]=$res;

		 	}

		 	return $dataList;

	}catch(Exception $e){
		echo $e->getMessage();
	}
}


//会社IDによりドライバーリスト取得 　
public static function getDrivers($id, $geographic_id = null){
	try{

		$dbh=SingletonPDO::connect();

		if(!empty($geographic_id)){
			$condition = " AND geographic.id = $geographic_id";
		}else{
			$condition = "";
		}


		$sql="SELECT
				drivers.id as id,
				geographic.id as geographic_id,
				geographic.name as geographic_name,
				drivers.last_name, 
				drivers.first_name,
				drivers.company_id, 
				drivers.regist_number,
				drivers.is_group_manager,
				drivers.is_branch_manager,
				drivers.created
			 FROM 
				drivers 
			LEFT JOIN
				geographic
			ON
				drivers.geographic_id = geographic.id
			 WHERE 
			 	drivers.company_id=$id 
			 	$condition
			ORDER BY
				drivers.furigana";

			 	$stmt=$dbh->prepare($sql);
			 	$stmt->execute();
			 	while($res=$stmt->fetchObject(__CLASS__)){
			 		$dataList[]=$res;

			 	}
			 	return $dataList;

	}
	catch(Exception $e)
	{
		echo $e->getMessage();
	}
}


/**
 * 会社IDによりドライバーリスト取得 　RegIdが空でないもの
 * @param int $id 会社ID
 * @param boolean $is_message メッセージフォームかどうか
 * @param int $branch_id 営業所だけのメッセージの場合の営業所番号
 */
public static function getDriversWithRegId( $id, $is_message, $branch_id = null ){
	
	try{

		$dbh=SingletonPDO::connect();
		
		if($branch_id != null){
			$branch_where = " AND 
								drivers.geographic_id = $branch_id
								";
		}
		
		if ($is_message) {
			$reg_where = "AND
					(
						(
							registration_id IS NOT NULL
								AND
							registration_id != \"\"
						)
						OR
						(
							ios_device_token IS NOT NULL
								AND
							ios_device_token != \"\"
								AND
							ios_device_token != \"(null)\"
						)
					)";
		}
		
		$sql = "SELECT
					drivers.id, first_name, last_name, geographic.name as geo_name, geographic_id,
					(
						SELECT
							is_public
						FROM
							driver_options
						WHERE
							driver_id = drivers.id
					) as is_public
				FROM 
					drivers
				LEFT JOIN
					geographic
				ON
					drivers.geographic_id = geographic.id
				WHERE 
					drivers.company_id = $id
					$reg_where
					$branch_where
				ORDER BY geographic_id ASC
					";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		while($res=$stmt->fetch(PDO::FETCH_OBJ)){
			if (!$res->geo_name) {
				$branch_name = "営業所名未登録";
			} else {
				$branch_name = $res->geo_name;
			}
			$geo_id = $res->geographic_id;
	 		$dataList[$geo_id]['geo_id'] = $geo_id;
	 		$dataList[$geo_id]['geo_name'] = $branch_name;
	 		$dataList[$geo_id]['drivers'][]=$res;
	 	}
	 	
	 	if ( !empty( $dataList ) )
		 	return array_chunk($dataList, 5);
	 	
	 	return null;

	}
	catch(Exception $e)
	{
		echo $e->getMessage();
	}
}

//会社ごとドライバーのカウントリスト取得 　
public static function countDrivers($id){
	try{

		$dbh=SingletonPDO::connect();

		$sql="SELECT COUNT(*) FROM drivers WHERE company_id=$id GROUP BY company_id";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$res=$stmt->fetch(PDO::FETCH_ASSOC);

		return $res;

	}
	catch(Exception $e)
	{
		echo $e->getMessage();
	}
}

//ID単位でドライバー情報を削除するメソッド

public static function deleteDriver($id){

	try{
		$dbh=SingletonPDO::connect();

		$sql="DELETE
	FROM drivers
	WHERE drivers.id = :id 
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

//ドライバーのスマホからSmart動態管理がアンインストールされた時に、RegIdを消去する
public static function deleteRegId($driver_id){

	try{
		$dbh=SingletonPDO::connect();
			
		$sql="
					UPDATE 
						drivers
					SET
						registration_id = NULL
					WHERE
						id = $driver_id
					";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();

	}catch(Exception $e){

		echo $e->getMessage();

	}
}



//複数ページ
public static function make_page_link($topicList){
	try{
		$page_max=PAGE_MAX;

		require_once 'Pager.php';
		$params = array(
    'mode'       => 'Jumping',
    'perPage'    => $page_max,
    'delta'      => 10,
    'itemData'   => $topicList);
		$pager = & Pager::factory($params);
		$data  = $pager->getPageData();
		$links = $pager->getLinks();

		return array($data, $links);

	}catch(Exception $e)
	{
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

//申請されたグループを検索
public static function getCompanyByGroupId($company_group_id){

	try{
		$dbh=SingletonPDO::connect();

		$sql = "SELECT
					company.id AS company_id,
					drivers.id AS driver_id
				FROM
					company
				INNER JOIN
					drivers
					ON drivers.is_group_manager = 1
					AND drivers.company_id = company.id
				WHERE
					company_group_id = \"$company_group_id\"
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
 * 編集などの手動で編集する場合に、last_driver_statusをアップデートするメソッド
 * @param unknown $dbh
 * @param unknown $driver_id
 * @return string
 */
public static function _updateLastDriverStatus($dbh, $driver_id) {
	/*
	 if(!is_numeric($driver_id) || $driver_id == 0) {
		die(__CLASS__.__METHOD__.'invalid driver id');
		}
		*/
	$sql_update =<<<EOL
REPLACE INTO last_driver_status
(driver_id, latitude, longitude, status, sales, address, detail, speed, start, end, created)
SELECT
	driver_id,
	latitude,
	longitude,
	status,
	sales,
	address,
	detail,
	speed,
	start,
	end,
	created
FROM driver_status
INNER JOIN (
	SELECT
		driver_id AS last_driver_id, 
		max(created) AS last_created
	FROM driver_status AS last
	WHERE driver_id = :driver_id
	AND created > (NOW() - INTERVAL 1 MONTH)
) t
ON driver_id = last_driver_id
AND created = last_created
ORDER BY updated DESC
LIMIT 1
EOL;

	$params = array(':driver_id' => $driver_id);
	$stmt = $dbh->prepare($sql_update);
	$stmt->execute($params);
	$rowCount = $stmt->rowCount();

	if($rowCount == 0) {
		$stmt = $dbh->prepare('DELETE FROM last_driver_status WHERE driver_id = :driver_id;');
		$stmt->execute($params);
	}
	$stmt->closeCursor();

	return rowCount;
}

//エジソン版　今日のデータがあるかどうか取得
public static function today_data( $driver_id ){
	
	$dbh=SingletonPDO::connect();
	$today = date("Y-m-d");
	
	//今日の一番最初のデータがあるかどうか
	$today_sql = "
				SELECT
					status,
					created
				FROM
					last_driver_status
				WHERE
					driver_id = $driver_id
				AND
					created LIKE '$today %'
				LIMIT 1";
	
	$stmt=$dbh->prepare($today_sql);
	$stmt->execute();
	$today_data = $stmt->fetchAll();
	
	return $today_data; 
	
}

//開始フラグがあった時のwork,driver_status,last_driver_statusテーブルへの登録
public static function register_data( $datas, $driver_id, $created ){

	//終了フラグがあれば、開始フラグより先に作業テーブルに終了時間を記録
	if($datas['end']!="NULL" && $datas['end']){
		
		Driver::notSmartPhoneEnd($datas, $created);
	
		$datas = Driver::changeDatasAfterRegistEnd($datas);			
			
	}
	
	//開始フラグがあれば、作業テーブルに開始時間を記録し、作業IDを取得
	if($datas['start']!="NULL" && $datas['start'] ){
		
		$last_work_id = Work::insertWorkWhenStart($datas, $created);

	}

	$last_created = LastDriverStatus::getLastDriverStatusByDriver($driver_id);

	if($last_created){

		$recent_latitude = $last_created['latitude'];
		$recent_longitude = $last_created['longitude'];
		
		
		$speed_per_seconds = getSpeedBySeconds($recent_latitude, $recent_longitude, $datas, $last_created);

		//ありえない速度で移動している
		if($speed_per_seconds > MIN_SPEED_PER_SECONDS ){
			//実際は10は使われていない
			return 10;
		}
					
	}
	
	//startが投げられていない場合は今までと同じwork_idを入力していく
	if($last_work_id[0]){
		
		$recent_work_id = $last_work_id[0];
		
	}elseif(!empty($last_created['work_id'])){
		
		$recent_work_id = $last_created['work_id'];
		
	}else{
		
		$last_work_id_from_woks = Work::getRecentWorkIdByDriverId($datas['driver_id']);
		$recent_work_id = $last_work_id_from_woks[0]['id'];
		
	}
	

	$is_deviated = Deviated::getIsDeviated($datas['driver_id']);	
	$direction = geoDirection($recent_latitude, $recent_longitude, $datas['latitude'], $datas['longitude'], $datas['speed']);
	
	$driver_status_id = Driver::insertDriverStatus($is_deviated, $direction, $datas, $created);
	
	CommunicationDriverStatus::insert($driver_status_id, $datas, $created);

	LastDriverStatus::updateLastDriverStatus($datas, $recent_work_id, $direction, $is_deviated, $created);
	
	$is_success = 1;

	return $is_success;

}


/**
 * スマートフォンからの終了ではなく、データ操作されたEndがある場合に行われる処理
 * @param unknown $datas
 * @param unknown $created
 */
private static function notSmartPhoneEnd($datas, $created){
	
	$recent_work_id = $datas['work_id'];
	//距離を計算
	$distance = Driver::calculateDistance($datas, $recent_work_id);
	
	//ドライバーの会社IDを取得
	$driver_datas = Driver::getById($datas['driver_id'], $from_web);
	$company_id = $driver_datas[0]->company_id;
	
	//登録された近い配送先がないか調べる
	$nearDestination = Destination::getNearDestinations($datas['latitude'],
			$datas['longitude'],
			$company_id);
	
	Work::updateWorkForEnd($recent_work_id, $datas, $distance, $nearDestination, $created);
	
	//ステータスが変わる時だけ、driver_statusのendをデータベースにinsertする
	if($datas['flug'] == 1 || $datas['flug'] == 2){
		
		$data_for_status_change = $datas;
		$data_for_status_change['start'] = '0000-00-00 00:00:00';
		
		$is_deviated = Deviated::getIsDeviated($datas['driver_id']);
		$direction = geoDirection($recent_latitude, $recent_longitude, $datas['latitude'], $datas['longitude'], $datas['speed']);
		
		$driver_status_id = Driver::insertDriverStatus($is_deviated, $direction, $data_for_status_change, $created);
		
		CommunicationDriverStatus::insert($driver_status_id, $datas, $created);
		
	}
}


private static function changeDatasAfterRegistEnd($datas){
	//workテーブル,driver_statusテーブルの為のENDを初期化させる
	$datas['end']=NULL;
	
	//ステータス切り替えの場合、ステータスを切り替える(現在位置が積込、荷卸の場合)
	if( $datas['flug'] == 1 ){
		
		//送られたステータスが輸送中の場合
		if( $datas['category_name'] == '荷卸先' || $datas['category_name'] == '中間貯蔵施設' ){
			//Postされたデータをこれから登録するので、カテゴリーから考える
			$datas['status'] = 5;
		}elseif( $datas['category_name'] == '積込先' || $datas['category_name'] == '仮置場' ){
			$datas['status'] = 1;
		}
		
		
	}elseif( $datas['flug'] == 2 ){
		
		//最新のステータスが積込の場合、保存するステータスは輸送中になる
		if( $datas['status'] == 1 ){
			
			$datas['status'] = 2;
			
			//最新のステータスが荷下の場合、保存するステータスは帰走中になる
		}elseif( $datas['status'] == 5 ){
			
			$datas['status'] = 3;
			
		}
		
	}
	
	return $datas;
}


//最新のステータスを取得するが休憩以外の過去の最新のステータスを取得が積込場か、荷卸場かデータを取得
public static function last_driver_data( $driver_id ){
	$dbh=SingletonPDO::connect();
	
	$newest_sql = "
		SELECT
			driver_id,
			status,
			address,
			work_id
		FROM
			last_driver_status
		WHERE
			driver_id = $driver_id
	";
	
	$stmt=$dbh->prepare($newest_sql);
	$stmt->execute();
	$newest_data=$stmt->fetch();
	
	//最新のステータスが休憩だった場合、それより前のworkデータを検索して、そのステータスを確認する
	if( $newest_data['status'] == 4 ){
		
		$newest_second_sql = "
				SELECT 
					driver_id,
					status, 
					created
				FROM
					work
				WHERE 
					driver_id = $driver_id 
				AND
					status != 4
				ORDER BY created DESC
				LIMIT 1 
			";
		
		$stmt=$dbh->prepare($newest_second_sql);
		$stmt->execute();
		$newest_data=$stmt->fetch();
	}
	
	return $newest_data;
	
}

//ストップのステータス(endが入っている)が送られてきたらストップの情報のみ記録
public static function stop_update( $datas, $driver_id, $created){

	
	try{
			$dbh=SingletonPDO::connect();
			
			$last_created = LastDriverStatus::getLastDriverStatusByDriver($driver_id);
		
			if($last_created){
			
				$recent_latitude = $last_created['latitude'];
				$recent_longitude = $last_created['longitude'];

				//ストップの登録なので、最新のデータのステータスのストップを記録する
				$datas['status'] = $last_created['status'];
			}
			
			
			if(!empty($last_created['work_id'])){
				
				$recent_work_id = $last_created['work_id'];
				
			}else{
				
				$last_work_id_from_woks = Work::getRecentWorkIdByDriverId($datas['driver_id']);
				$recent_work_id = $last_work_id_from_woks[0]['id'];
				
			}
						
			
			$is_deviated = Deviated::getIsDeviated($datas['driver_id']);
			$direction = geoDirection($recent_latitude, $recent_longitude, $datas['latitude'], $datas['longitude'], $datas['speed']);
			$driver_status_id = Driver::insertDriverStatus( $is_deviated, $direction, $datas, $created);	
				
			CommunicationDriverStatus::insert($driver_status_id, $datas, $created);
		
			LastDriverStatus::updateLastDriverStatus($datas, $recent_work_id, $direction, $is_deviated, $created);
		
			//距離を計算
			$distance = Driver::calculateDistance($datas, $recent_work_id);
			
			//ドライバーの会社IDを取得
			$driver_datas = Driver::getById($datas['driver_id'], $from_web);
			$company_id = $driver_datas[0]->company_id;
			
			//登録された近い配送先がないか調べる
			$nearDestination = Destination::getNearDestinations($datas['latitude'],
			$datas['longitude'],
			$company_id);
		
			Work::updateWorkForEnd($recent_work_id, $datas, $distance, $nearDestination, $created);			
		
			return 1;				
	
	}catch(PDOException $e){
		
		echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
		error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		
	}
	

}

	/**
	 * 中継サーバーへ送信するドライバーステータスを取得・作成し、送信すべきURLへ送信する
	 * @param string $transportUrl
	 * @param string $testUrl
	 */
	public function sendDriverStatus(){
		//sendIdsテーブルにドライバーステータスIDをインサート
		SendId::insertDriverStatusId();
		//インサートしたドライバーステータスIDをもとに送信IDを作成
		SendId::createDriverStatusSendId();
		//中継サーバーの情報を取得
		$servers = RelayServer::getAll();
		
		foreach($servers as $server){
			$driver_status = CommunicationDriverStatus::getByRelayServerId($server['id']);
			Communication::sendDriverStatus($server, $driver_status);
		}
	}
	
	//会社IDによりドライバーリスト取得 　
	public static function getDriversByCompany($company_id){
		try{
	
			$dbh=SingletonPDO::connect();
	
			$sql="SELECT *
				FROM
					drivers
				WHERE
					drivers.company_id=:id
				ORDER BY
					drivers.furigana";
			
			$stmt=$dbh->prepare($sql);
			$param = array(
					'id' => $company_id,
					);
				
			$stmt->execute($param);
			$drivers=$stmt->fetchAll(PDO::FETCH_ASSOC);
			
			return $drivers;
	
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	/**
	 * 引数で受け取ったドライバーIDの配列のドライバーを取得
	 * @param arrau $ids
	 */
	public function selectDriversByIds($ids) {
	
		try {
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
				
			$sql = "SELECT * 
						FROM 
							drivers
						WHERE 
							id IN(
					";
				
			$query = array();
			$selectDriverIds = array();
			foreach($ids as $key => $id){
				$query[$key] = ':id' . $key;
				$selectDriverIds['id'. $key] = $id;
			}
				
			$sql .= implode(',', $query);
			$sql .= ')';
			$sql .= ' ORDER BY drivers.furigana'; 
			
			$stmt = $dbh->prepare($sql);
			$stmt->execute($selectDriverIds);
				
			$drivers=$stmt->fetchAll(PDO::FETCH_ASSOC);
			
			return $drivers;
		}catch(PDOException $e){
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	public function selectDriverRoute($company_id, $date){
		try{
			$dbh=SingletonPDO::connect();
		
			$sql = "SELECT
						drivers.* , 
						transport_routes.name as transport_route_name, 
						transport_routes.destination_id,
						count(navi_areas.transport_route_id) as count_navi_area, 
						transport_route_drivers.id as transport_route_drivers_id , 
						transport_route_drivers.information as route_driver_information,
						transport_route_drivers.date,
						transport_route_drivers.transport_route_id
					FROM
						drivers
					LEFT OUTER JOIN
						transport_route_drivers 
							INNER JOIN 
								transport_routes
							ON 
								transport_route_drivers.transport_route_id  = transport_routes.id
						LEFT OUTER JOIN 
							navi_areas
						ON
							navi_areas.transport_route_id = transport_route_drivers.transport_route_id
					ON 
						drivers.id = transport_route_drivers.driver_id
					AND
						date = :date
					WHERE
						drivers.company_id = :company_id
					GROUP BY 
						drivers.id
					ORDER BY
						drivers.furigana
					";
			$stmt=$dbh->prepare($sql);
		
			$param = array(
					'company_id' => $company_id,
					'date' => $date
						
			);
			$stmt->execute($param);
		
			$transportRouteDrivers=$stmt->fetchAll(PDO::FETCH_ASSOC);
				
			return $transportRouteDrivers;
		}catch(PDOException $e){
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	

	
	public function insertDriverStatus($is_deviated, $direction, $datas, $created){

		try{

			$dbh=SingletonPDO::connect();
			
			$sql ="
				INSERT INTO driver_status
					(driver_id, status, latitude, longitude, detail, sales,
					address, speed, start, end, work_id, is_transporting, is_deviated, created)
				VALUES
					(:driver_id, :status, :latitude, :longitude, :detail, :sales, :address, :speed, :start,
					:end, :work_id, (SELECT (CASE WHEN is_transporting = 1 THEN 1 ELSE 0 END) FROM last_transport_status where driver_id = :driver_id), :is_deviated, :created);
				";
			
			$stmt=$dbh->prepare($sql);
			
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
					'is_deviated' => $is_deviated,
					'created' => $created
			);
			
			$stmt->execute($param);
			
			$driver_status_id = $dbh->lastInsertId('id');
			
			$stmt->closeCursor();
		
		}catch(PDOException $e){
			
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
		}
		
		
		return $driver_status_id;
	}
	

//クラスDataの終了
}
?>