<?php
/**
 * Informationクラス
 * 渋滞と積雪の情報　エジソンさん向け
 * @author Akiko Goto
 * @since 2015/5/07
 *
 */

class Information{

	public $id;
	public $type;
	public $driver_id;
	public $latitude;
	public $longitude;
	public $speed;
	public $address;
	public $updated;
	public $created;

	
	/**
	 *　情報一覧
	 */

	public static function viewInformations($company_id,$post_branch_id){

		try{

			$dbh=SingletonPDO::connect();
			
			if($post_branch_id==null){
				$sql="
						SELECT
							informations.id AS information_id, informations.type, informations.speed,
							informations.address, informations.latitude, informations.longitude,
							informations.created, informations.updated, drivers.last_name, drivers.first_name,
							company.id AS company_id,
							drivers.id AS driver_id,
							drivers.car_type 
						FROM 
							informations
						LEFT JOIN
							drivers
						ON
							drivers.id = informations.driver_id
						LEFT JOIN
							company
						ON
							company.id = drivers.company_id
						WHERE 
							company.id = $company_id
						ORDER BY informations.id DESC";
			}else{
				
				$sql="
					SELECT
						informations.id AS information_id, informations.type, informations.speed,
						informations.address, informations.latitude, informations.longitude,
						informations.created, informations.updated, drivers.last_name, drivers.first_name,
						company.id AS company_id,
						drivers.id AS driver_id
					FROM
						informations
					INNER JOIN
							drivers
						ON
							drivers.id = informations.driver_id
					INNER JOIN
							company
						ON
							company.id = drivers.company_id
					WHERE
							company.id = $company_id
					AND
							drivers.geographic_id=$post_branch_id					
					ORDER BY informations.id DESC";
			}
			
			
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();
// 			echo '<pre>';
// 			var_dump($data);
// 			echo '</pre>';
			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();
			exit(0);

		}
	}
	


	/**
	 * データ投入
	 */

	public static function putInInformation($datas){

		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			$sql1="
				INSERT INTO 
					informations
					(type, driver_id, geographic_id, latitude, longitude, 
					speed, address, created)
				VALUES
					(:type, :driver_id, (SELECT geographic_id FROM drivers WHERE id = :driver_id), :latitude, :longitude, 
					:speed, :address, NOW())
				";


			$stmt=$dbh->prepare($sql1);

			$param = array(

				'type' => $datas['type'],
				'driver_id' => $datas['driver_id'],
				'latitude' => $datas['latitude'], 
				'longitude' => $datas['longitude'], 
				'speed' => $datas['speed'],
			 	'address' => $datas['address'],
			);

			$stmt->execute($param);

			$datas['id'] = $dbh->lastInsertId(); 
			
			$dbh->commit();
			
			return $datas['id'];

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}
	
	/**
	 *　個別の情報取得
	 */

	public static function viewInformation($information_id){

		try{

			$dbh=SingletonPDO::connect();

			$sql="
					SELECT
						informations.id, informations.type, informations.speed, informations.recorded_time,
						informations.created, informations.updated, drivers.last_name, drivers.first_name,
						company.id AS company_id,
						drivers.id 
					FROM 
						informations
					LEFT JOIN
						drivers
					ON
						drivers.id = informations.driver_id
					LEFT JOIN
						company
					ON
						company.id = drivers.company_id
					WHERE 
						informations.id = $movie_id";


			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();

			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();
			exit(0);

		}
	}
	
	public static function deleteInformation($id){

		try{

			$dbh=SingletonPDO::connect();

			
				$sql="DELETE
						FROM 
							informations
						WHERE 
							id = :id 
					";
	
				$res = $dbh->prepare($sql);
	
				$param=array(
					'id'=>$id
				);
	
				$res->execute($param);
				
				return true;
				
		}catch(Exception $e){

			$dbh->rollback();			
			die($e->getMessage());
		
		}
	}

	public static function alertInformation($information_id){
	
		try{
	
			$dbh=SingletonPDO::connect();
	
			$sql="
			SELECT
			drivers.company_id,
			informations.created, informations.type,informations.address,
			CONCAT( drivers.last_name, drivers.first_name ) AS driver_name, drivers.car_type
			FROM
			informations
			LEFT JOIN
			drivers
			ON
			drivers.id = informations.driver_id
			WHERE
			informations.id = $information_id";
	
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();
	
			return $data;
	
		}catch(PDOException $e){
	
			echo $e->getMessage();
			exit(0);
	
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

	/**
	 * //急ブレーキ情報の取得
	 * @param string $start
	 * @param string $end
	 */
	public static function suddenBrakingInformation($start, $end, $company_id, $branchId){
		
		if($branchId == TEMPORARY_STORAGE){
			$information = self::temporaryStorageSuddenBraking($start, $end, $company_id);
		}else{
			$information = self::branchedSuddenBraking($start, $end, $company_id, $branchId);
		}
		return $information;
	}
	
	/**
	 * 急ブレーキ情報の取得
	 * @param string $start
	 * @param string $end
	 */
	public function branchedSuddenBraking($start, $end, $company_id, $branchId){
		
		try{
			$dbh=SingletonPDO::connect();
			
			$sql="SELECT 
					'I' as change_kind, driver_id as vclId, DATE_FORMAT(informations.created, '%Y/%m/%d %H:%i:%S') as created, '' as driver_id, '0' as event_kind, '' as about_event, latitude, longitude
					FROM 
						informations
					LEFT JOIN
						drivers
					ON
						drivers.id = informations.driver_id
					WHERE type = 'SUDDEN_BRAKING' 
					AND informations.created BETWEEN date(:start) AND (:end) 
					AND drivers.company_id = :company_id";
			
			if(!empty($branchId)){
				$sql .= " AND informations.geographic_id= :branchId ";
			}
			
			$orderBy = "ORDER BY informations.created";
			$stmt=$dbh->prepare($sql);
				
			$param = array(
					'start' => $start,
					'end' => $end,
					'company_id' => $company_id
			);
			
			if(!empty($branchId)){
				$param += array('branchId' => $branchId);
			}
			
			$stmt->execute($param);
		
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			return $data;	
		}catch(PDOException $e){
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	/**
	 * 仮置場の急ブレーキ判定を取得する
	 * @param string $start
	 * @param string $end
	 * @param string $company_id
	 */
	public function temporaryStorageSuddenBraking($start, $end, $company_id){
		try{
			$dbh=SingletonPDO::connect();
				
			$sql="
					SELECT
					    'I' as change_kind,
					    driver_id as vclId,
					    DATE_FORMAT(informations.created, '%Y/%m/%d %H:%i:%S') as created,
					    '' as driver_id,
					    '0' as event_kind,
					    '' as about_event,
					    latitude,
					    longitude
					FROM
					    informations
					    LEFT JOIN
					        drivers
					    ON  drivers.id = informations.driver_id
					WHERE
					    type = 'SUDDEN_BRAKING'
					AND informations.created BETWEEN date(:start) AND (:end)
					AND drivers.company_id = :company_id
					AND drivers.geographic_id IS NOT NULL
					AND informations.geographic_id IN(
					        SELECT
					            geographic.id
					        FROM
					            geographic,
					            relay_servers
					        WHERE
					            relay_server_id = relay_servers.id
					        AND relay_servers.is_production = 1
					    )
					ORDER BY
					    informations.created";
				
			$stmt=$dbh->prepare($sql);
		
			$param = array(
					'start' => $start,
					'end' => $end,
					'company_id' => $company_id
			);
				
			$stmt->execute($param);
		
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
			return $data;
		}catch(PDOException $e){
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}

}
?>