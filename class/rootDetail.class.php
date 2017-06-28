<?php
/*
 * RootDetailクラス
 * 配送先ルートの詳細を設定
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 *
 */

class RootDetail{

	public $id;
	public $root_id;
	public $deliver_time;
	public $destination_id;
	public $root_address;
	public $latitude;
	public $longitude;
	public $updated;
	public $created;


	/*配送ルート詳細一覧
	 * 日付とドライバーを指定して参照
	 *@param company_id 会社ID
	 *@param driver_id　ドライバーID
	 *@param date　日付
	 */

	public static function viewRootDetails($company_id, $driver_id, $date = null){

		try{

			$dbh=SingletonPDO::connect();

			//日付が投げられている場合は日付のデータを、ない場合は最新版を表示（スマホアプリでの表示など）
			if(!empty($date)){
				$sql="
					SELECT
						* ,
						roots.information as root_information,
						root_details.information as root_detail_information,
						roots.id as root_id,
						root_details.id as root_detail_id
					FROM 
						root_details
					LEFT JOIN
						roots
					ON
						roots.id = root_details.root_id
					WHERE 
						roots.company_id = $company_id
					AND
						roots.driver_id = $driver_id
					AND
						roots.date = CAST(\"$date\" as DATE)
					ORDER BY
						root_details.deliver_time,
						root_details.id
						
						";
			}else{

				$sql="
					SELECT
						root_details.deliver_time, root_details.destination_name ,
						root_details.root_address, root_details.latitude, root_details.longitude,
						roots.information as root_information,
						root_details.information as root_detail_information,
						roots.id as root_id,
						root_details.id as root_detail_id
					FROM 
						root_details
					LEFT JOIN
						roots
					ON
						roots.id = root_details.root_id
					WHERE 
						roots.company_id = $company_id
					AND
						roots.driver_id = $driver_id
					AND
						roots.id IN
							(SELECT MAX(id) FROM roots WHERE driver_id = $driver_id GROUP BY driver_id)	
					ORDER BY
						root_details.deliver_time,					
						root_details.id

						";
			}
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();
			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}
	
	/*配送ルート　コピー
	 *@param $copied_data コピーされるデータの会社ID、ドライバーID,日付
	 *@param driver_id　コピー先のデータの会社ID、ドライバーID,日付
	 *@root_id　コピー先のID
	 */

	public static function copyRootDetails($copied_data, $root_id, $company_id){
		
		$driver_id = $copied_data['driver_id'];
		$date = $copied_data['date'];

		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			if(!empty($date)){
				$sql="
					CREATE TEMPORARY TABLE tmptable_1
						SELECT
							root_details.id as id,
							root_details.root_id,
							root_details.deliver_time,
							root_details.destination_id,		
							root_details.destination_name,
							root_details.root_address,		
							root_details.latitude,		
							root_details.longitude,		
							root_details.information,
							root_details.created,
							root_details.updated
						FROM 
							root_details
						LEFT JOIN
							roots
						ON
							roots.id = root_details.root_id
						WHERE 
							roots.company_id = $company_id
						AND
							roots.driver_id = $driver_id
						AND
							roots.date = CAST(\"$date\" as DATE)";

				$stmt=$dbh->prepare($sql);
				$stmt->execute();
				
				$sql = "UPDATE 
							tmptable_1 
						SET 
							id = NULL, 
							root_id = $root_id
						";

				$stmt=$dbh->prepare($sql);
				$stmt->execute();
				
				$sql = "INSERT INTO root_details SELECT * FROM tmptable_1";							
				
				$stmt=$dbh->prepare($sql);
				$param = array(
	
						'root_id' => $root_id
				
					);				
				$stmt->execute($param);

				
				$sql = "DROP TEMPORARY TABLE IF EXISTS tmptable_1";
				$stmt=$dbh->prepare($sql);
				$stmt->execute($param);
				
				$dbh->commit();
			}	
	

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}	
	
	
	//idを指定して、データ内容を取得　配送先
	public static function getById($id){

		try{
			$dbh=SingletonPDO::connect();

			$sql="
				SELECT 
					*
				FROM 
					root_details
				WHERE 
					id = $id ";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();

			return $data;

		}catch(Exception $e){

			echo $e->getMessage();

		}
	}

	/*データ投入と編集　$statusの値でスイッチ
	 /*const NEWDATA=1; 新規データ登録
	 /*const EDIT=2; データ編集
	 *
	 */

	public static function putInRootDetail($datas, $status){

		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			foreach($datas as $data){
				switch($status){
					//新規データ登録
					case "NEWDATA":
	
						$sql1="
							INSERT INTO 
								root_details
								(root_id, deliver_time,destination_name, 
									root_address, latitude, longitude, 
									information, created)
							VALUES
								(:root_id, CAST(:deliver_time as TIME),
								:destination_name,  :root_address, :latitude, :longitude, 
									:information, NOW())
							";
	
						break;
	
	
						//データ編集
					case "EDIT":
	
						$sql1="
							UPDATE 
								root_details
							SET 
								root_id=:root_id,
								deliver_time=:deliver_time,
								destination_name=:destination_name, 
								root_address=:root_address, 
								latitude=:latitude, 
								longitude=:longitude, 
								information=:information
							WHERE id=".$data['id'];
						break;
				}
	
				$stmt=$dbh->prepare($sql1);
	
				//配送時刻は時間と分をつなげる
				if(!empty($data['hour'])){
					$data['deliver_time'] = $data['hour'].':'.$data['minit'].':00';
				}else{
					$data['deliver_time'] = NULL;
				}
				
				$param = array(
	
					'root_id' => $data['root_id'],
					'deliver_time' => $data['deliver_time'],
					'destination_name' => $data['destination_name'],
					'root_address' => $data['address'],
					'latitude' => $data['latitude'],
					'longitude' => $data['longitude'],
					'information' => $data['information']
				
				);
	
				$stmt->execute($param);
				
			}
			$dbh->commit();

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}

	//ID単位で配送先情報を削除するメソッド

	public static function deleteRootDetail($id){

		try{
			$dbh=SingletonPDO::connect();

			$sql="DELETE
					FROM 
						root_details
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




	//クラスMessageの終了
}
?>