<?php
/**
 * Movieクラス
 * 動画の登録と呼び出し　エジソンさん向け
 * @author Akiko Goto
 * @since 2015/4/13
 *
 */

class Movie{

	public $id;
	public $name;
	public $driver_id;
	public $latitude;
	public $longitude;
	public $speed;
	public $size;
	public $recorded_time;
	public $address;
	public $updated;
	public $created;

	
	/**
	 *　動画一覧
	 */

	public static function viewMovies($company_id,$post_branch_id){

		try{

			$dbh=SingletonPDO::connect();
			
			if($post_branch_id == null){
				$sql="
						SELECT
							movies.id AS movie_id, movies.name, movies.size, movies.speed, movies.recorded_time,
							movies.address,
							movies.created, movies.updated, drivers.last_name, drivers.first_name,
							company.id AS company_id,
							drivers.id AS driver_id,
							drivers.car_type 
						FROM 
							movies
						LEFT JOIN
							drivers
						ON
							drivers.id = movies.driver_id
						LEFT JOIN
							company
						ON
							company.id = drivers.company_id
						WHERE 
							company.id = $company_id
						ORDER BY movies.id DESC";
			}else{
				
				$sql="
					SELECT
						movies.id AS movie_id, movies.name, movies.size, movies.speed, movies.recorded_time,
						movies.address,
						movies.created, movies.updated, drivers.last_name, drivers.first_name,
						company.id AS company_id,
						drivers.id AS driver_id
					FROM
						movies
					LEFT JOIN
							drivers
						ON
							drivers.id = movies.driver_id
					LEFT JOIN
							company
						ON
							company.id = drivers.company_id
					WHERE
						company.id = $company_id
					AND
						drivers.geographic_id=$post_branch_id
					ORDER BY movies.id DESC";
				
			}

			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();

			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();
			exit(0);

		}
	}
	


	/**
	 * データ投入
	 */

	public static function putInMovie($datas){

		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			$sql1="
				INSERT INTO 
					movies
					(name, driver_id, latitude, longitude, 
					speed, size, recorded_time, address, created)
				VALUES
					(:name, :driver_id, :latitude, :longitude, 
					:speed, :size, :recorded_time, :address,  NOW())
				";


			$stmt=$dbh->prepare($sql1);

			$param = array(

				'name' => $datas['file_name'],
				'driver_id' => $datas['driver_id'],
				'latitude' => $datas['latitude'], 
				'longitude' => $datas['longitude'], 
				'speed' => $datas['speed'], 
				'size' => $datas['size'], 
				'recorded_time' => $datas['recorded_time'],				
				'address' => $datas['address']				
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
	 *　個別の動画情報取得
	 */

	public static function viewMovie($movie_id){

		try{

			$dbh=SingletonPDO::connect();

			$sql="
					SELECT
						movies.id, movies.name, movies.size, movies.speed, movies.recorded_time,
						movies.address,
						movies.created, movies.updated, drivers.last_name, drivers.first_name,
						company.id AS company_id,
						drivers.id, drivers.car_type 
					FROM 
						movies
					LEFT JOIN
						drivers
					ON
						drivers.id = movies.driver_id
					LEFT JOIN
						company
					ON
						company.id = drivers.company_id
					WHERE 
						movies.id = $movie_id";


			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();

			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();
			exit(0);

		}
	}
	
	//ID単位で配送先情報を削除するメソッド
	public static function deleteMovie($id){

		try{
			$dbh=SingletonPDO::connect();

			$movie_info = Movie::viewMovie($id);

			$file_name = $movie_info[0]['name'];
			
			$file = SERVER_MOVIE_PATH.$file_name;
			var_dump($file);
			if(unlink($file)){			
					
				$sql="DELETE
						FROM 
							movies
						WHERE 
							id = :id 
					";
	
				$res = $dbh->prepare($sql);
	
				$param=array(
					'id'=>$id
				);
	
				$res->execute($param);
				
				return true;
				
			}else{
				
				return false;
			
			}

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

	public static function alertMovie($movie_id){
	
		try{
	
			$dbh=SingletonPDO::connect();
	
			$sql="
			SELECT
			drivers.company_id,
			movies.created, movies.name,movies.address, 
			CONCAT( drivers.last_name, drivers.first_name ) AS driver_name, drivers.car_type
			FROM
			movies
			LEFT JOIN
			drivers
			ON
			drivers.id = movies.driver_id
			WHERE
			movies.id = $movie_id";
	
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();
	
			return $data;
	
		}catch(PDOException $e){
	
			echo $e->getMessage();
			exit(0);
	
		}
	}

}
?>