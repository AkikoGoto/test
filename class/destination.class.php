<?php
/*
 * Destinationクラス
 * 配送先を設定
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 *
 */

class Destination{

	public $id;
	public $destination_name;
	public $daddress;
	public $destination_latitude;
	public $destination_longitude;
	public $updated;
	public $created;
	
	
	public static function getByDriverId($driver_id){
		
		try{
			
			$dbh=SingletonPDO::connect();
			
			$destination_sql = "SELECT
				destinations.id,
				destinations.destination_name,
				destination_categories.name,
				destinations.address,
				destinations.latitude,
				destinations.longitude
				FROM
				destinations
				INNER JOIN drivers ON drivers.id = $driver_id
				INNER JOIN company ON company.id = drivers.company_id
				INNER JOIN destination_categories ON destination_categories.company_id = company.id
				INNER JOIN destination_categories_destinations ON
				destination_categories_destinations.destination_id = destinations.id
				AND
				destination_categories_destinations.destination_categories_id = destination_categories.id
				WHERE
				company.id = destinations.company_id
				";
			
			$stmt=$dbh->prepare($destination_sql);
			$stmt->execute();
			$destination_data=$stmt->fetchAll();
			
			return $destination_data;
		
		}catch(PDOException $e){
			
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
		}
	}
	

/**
 * 特定の緯度経度から、近い配送先を抽出
 * 日報で利用
 * @param latitude, longitude, company_id
 */
	public static function getNearDestinations($latitude, $longitude, $company_id){

		try{
			
			$dbh=SingletonPDO::connect();

				$sql="
					SELECT 
						near.destination_name, near.id
					FROM (	
						SELECT 
							destination_name ,id ,company_id,
							(sqrt(pow( (latitude - $latitude) * 111, 2 ) + pow((longitude - $longitude) * 91, 2 ))) AS squared_distance
						FROM 		
							destinations
					) AS near
					join company
						on near.company_id = company.id
					WHERE
						company.id = $company_id
					AND
						squared_distance < 0.1
					ORDER BY 
						squared_distance
					LIMIT 1
					";			


			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetch();

			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}

	/**
	 * スマホにJsonで配送先情報を出力する
	 * @param latitude, longitude, company_id
	 * 
	 */
	public static function getNearDestinationsForSp($latitude, $longitude, $company_id, $category = null, $name = null){

		try{
			
			$dbh=SingletonPDO::connect();
		
			if($category !=null ){
				$category_sql = "AND destination_categories_destinations.destination_categories_id = $category";
			}

			if($name !=null ){
				$name_sql = "AND near.destination_name COLLATE utf8_unicode_ci LIKE \"%$name%\"";
			}
			
			
			//50キロ以内 200個以内
				$sql="
					SELECT 
						near.destination_name, near.id, near.postal, near.address,
						near.tel, near.fax, near.department, near.contact_person, near.email, 
						near.information, near.latitude, near.longitude,
						destination_categories.name as category_name
					FROM (	
						SELECT 
							destination_name ,id ,company_id, postal, address,
							tel, fax, department, contact_person, email, information, 
							latitude, longitude,
							(sqrt(pow( (latitude - $latitude) * 111, 2 ) + pow((longitude - $longitude) * 91, 2 ))) AS squared_distance
						FROM 		
							destinations
					) AS near
					JOIN 
						company
						on near.company_id = company.id
					LEFT JOIN 
						 destination_categories_destinations
						 on near.id = destination_categories_destinations.destination_id
					LEFT JOIN
						destination_categories
						on destination_categories_destinations.destination_categories_id = destination_categories.id	 
					WHERE
						company.id = $company_id
					AND
						squared_distance < 50
					$category_sql
					$name_sql	
					ORDER BY 
						squared_distance
					LIMIT 100
					";			


			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll(PDO::FETCH_ASSOC);

			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();
			exit(0);
		}
	}	
	
	
	
	/*配送先一覧
	 *
	 */

	public static function viewDestinations($company_id){

		try{

			$dbh=SingletonPDO::connect();

			$sql="
					SELECT
						* 
					FROM 
						destinations
					WHERE 
						destinations.company_id = $company_id";


			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();

			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();
			exit(0);

		}
	}
	
	public static function searchDestinations($company_id, $query){
	
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
	destination_name COLLATE utf8_unicode_ci LIKE :keyword_${i} OR
	address COLLATE utf8_unicode_ci LIKE :keyword_${i} OR
	information COLLATE utf8_unicode_ci LIKE :keyword_${i} OR
	destination_categories.name COLLATE utf8_unicode_ci LIKE :keyword_${i}
)
EOL;
				$params[":keyword_${i}"] = "%${keyword}%";
			}
			
			$sql =<<<EOL
SELECT
destinations.id,
destinations.destination_name,
destinations.address,
destinations.latitude,
destinations.longitude,
destinations.information,
destination_categories.color as color,
GROUP_CONCAT(destination_categories.name) as category_name
FROM
destinations
LEFT JOIN destination_categories_destinations
ON destination_id = destinations.id
LEFT JOIN destination_categories
ON destination_categories_destinations.destination_categories_id = destination_categories.id
WHERE
destinations.company_id = :company_id
${criteria}
GROUP BY
destinations.id
ORDER BY destinations.updated DESC
EOL;

			$dbh=SingletonPDO::connect();
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute($params);
			//$stmt->debugDumpParams();
			$data=$stmt->fetchAll(PDO::FETCH_ASSOC);
	
			return $data;
	
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
					destinations.id as id,
					destinations.destination_name,
					destinations.destination_kana,
					destinations.postal,
					destinations.tel,
					destinations.fax,
					destinations.department,
					destinations.contact_person,
					destinations.email,
					destinations.company_id,
					destinations.address,
					destinations.latitude,
					destinations.longitude,
					destinations.information,		
					GROUP_CONCAT(destination_categories_destinations.destination_categories_id) as category_id,				
					GROUP_CONCAT(destination_categories.name) as category_name
				FROM 
					destinations
				LEFT JOIN
					destination_categories_destinations
				ON
					destination_categories_destinations.destination_id = destinations.id
				LEFT JOIN
					destination_categories
				ON
					destination_categories.id = destination_categories_destinations.destination_categories_id	
				WHERE 
					destinations.id = $id 
				";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetch();

			return $data;

		}catch(Exception $e){

			echo $e->getMessage();

		}
	}

	/**
	 * データ投入と編集　$statusの値でスイッチ
	 * NEWDATA=1; 新規データ登録
	 * EDIT=2; データ編集
	 */

	public static function putInDestination($datas, $status){

		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			switch($status){
				//新規データ登録
				case "NEWDATA":

					$sql1="
						INSERT INTO 
							destinations
							(destination_name, company_id, destination_kana, postal, 
							tel, fax, department, contact_person, email,
							address, latitude, longitude, information, created)
						VALUES
							(:destination_name, :company_id, :destination_kana, :postal, 
							:tel, :fax, :department, :contact_person, :email,
							:address, :latitude, :longitude, :information, NOW())
						";

					break;


					//データ編集
				case "EDIT":

					$sql1="
						UPDATE 
							destinations
						SET 
							destination_name=:destination_name, 
							company_id=:company_id,
							destination_kana=:destination_kana, 
							postal=:postal, 
							tel=:tel, 
							fax=:fax, 
							department=:department, 
							contact_person=:contact_person, 
							email=:email,
							address=:address, 
							latitude=:latitude, 
							longitude=:longitude,
							information=:information
						WHERE id=".$datas['id'];
					break;
			}

			$stmt=$dbh->prepare($sql1);

			$param = array(

				'destination_name' => $datas['destination_name'],
				'company_id' => $datas['company_id'],
				'destination_kana' => $datas['destination_kana'], 
				'postal' => $datas['postal'], 
				'tel' => $datas['tel'], 
				'fax' => $datas['fax'], 
				'department' => $datas['department'], 
				'contact_person' => $datas['contact_person'], 
				'email' => $datas['email'],
				'address' => $datas['address'],
				'latitude' => $datas['latitude'],
				'longitude' => $datas['longitude'],
				'information' => $datas['information']
				
			);

			$stmt->execute($param);

			switch($status){

				case "NEWDATA":
					$datas['id'] = $dbh->lastInsertId(); 
			 	break;
			}
			 	
			
			$dbh->commit();
			
			return $datas['id'];

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}
	
	/*　CSVアップロードからの配送先を解析して配列にする
	 * @param CSVアップロードデータ
	 * @return 配列
	 */
	public static function analyzeDesctinationCSV( $company_id, $file_data ) {
		
		try {
			
			//アップロードされたCSVファイルの行毎にデータを登録
			$destinations = array();
			while ($row = fgetcsv( $file_data )) {

				// 空行はスキップ
				if ($row === array(null))
					continue;
					
				if ($row[0] == "配送先の種類" &&
					$row[1] == "配送先名" &&
					$row[2] == "フリガナ")
					continue;

				// カラム数が異なる無効なフォーマット
				if (count($row) !== 11)
					return 'INVALID_CSV_FORMAT';
				
				//配送先の登録
				$destination = array(
					'category' => $row[0],
					'destination_name' => $row[1],
					'company_id' => $company_id,
					'destination_kana' => $row[2], 
					'postal' => $row[3], 
					'address' => $row[4],
					'tel' => $row[5], 
					'fax' => $row[6], 
					'department' => $row[7], 
					'contact_person' => $row[8], 
					'email' => $row[9],
					'information' => $row[10]
				);
				
				array_push( $destinations, $destination );
            }
            
            return $destinations;
			
		} catch(PDOException $e){
			
			$dbh->rollBack();
			return 'FALSE';
			exit;

		}
		
		
	}
	
	
	
	/*　CSVアップロードからの配送先登録メソッド
	 * @param 配送先名、配送先住所、配送先情報
	 * @return ステータス
	 */
	public static function putInDestinationsUploadedByCSV( $datas ){

		$dbh=SingletonPDO::connect();
		
		try{

			$dbh->beginTransaction();

			//配送先の登録
			$sql = "INSERT INTO 
						destinations
						(destination_name, company_id, destination_kana, postal, 
						tel, fax, department, contact_person, email,
						address, latitude, longitude, information, created)
					VALUES
						(:destination_name, :company_id, :destination_kana, :postal, 
						:tel, :fax, :department, :contact_person, :email,
						:address, :latitude, :longitude, :information, NOW())";
			$stmt=$dbh->prepare($sql);
			

			//アップロードされたCSVファイルの行毎にデータを登録
			foreach ( $datas as $destination ) {
				
				$param = $destination;
				unset( $param['category'] );
				
				//配送先の登録
				/*
				$param = array(

					'destination_name' => $row[1],
					'company_id' => $company_id,
					'destination_kana' => $row[2], 
					'postal' => $row[3], 
					'address' => $row[4],
					'tel' => $row[5], 
					'fax' => $row[6], 
					'department' => $row[7], 
					'contact_person' => $row[8], 
					'email' => $row[9],
					'information' => $row[10]
					
				);
				*/
				try {
					$stmt->execute($param);
				} catch (PDOException $e) {
					echo $e->getMessage();
					var_dump($param);
					var_dump($destination);
					var_dump($datas);
				}
				
				//配送先の登録IDを取得
				$last_id_sql = "SELECT LAST_INSERT_ID()";
				$last_id_stmt=$dbh->prepare($last_id_sql);
				$last_id_stmt->execute();
				$destination_ids = $last_id_stmt->fetch();
				$destination_id = $destination_ids[0];
				
				//配送先のカテゴリーの登録
				$category_id = self::insertDestinationCategories( $destination['company_id'], $destination['category'] );
				
				//配送先とカテゴリーをひもつける
				self::insertDestinationCategoriesDestinations( $destination_id, $category_id );
				
            }
			$dbh->commit();
            
			return 'SUCCESS';

		}catch(PDOException $e){
			
			$dbh->rollBack();
			echo $e->getMessage();
			return 'FALSE';
			exit;

		}
	}
	
	/**
	 * CSVアップロード時のカテゴリーの新規追加と、カテゴリーIDを取得する
	 * @param カテゴリー名
	 * @return 引数のカテゴリー名のカテゴリーID
	 */
	public static function insertDestinationCategories( $company_id, $category ) {
		
		$dbh=SingletonPDO::connect();
		
		try{
			$sql="
			SELECT id
			FROM destination_categories
			WHERE name = '$category'
			AND company_id = '$company_id'
			LIMIT 1";
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas=$stmt->fetch();
			
			if (!empty($datas)) {
				//データがあるとして、そのIDをカテゴリーIDとして利用する
				$category_id = $datas['id'];
			} else {
				//新しいデータとしてINSERTする
				$category_sql="
					INSERT INTO
						destination_categories 
							(company_id,name,created)
					VALUES
						(:company_id, :name, NOW())";
				$category_param = array(
					'company_id' => $company_id,
					'name' => $category
				);
				$category_stmt=$dbh->prepare($category_sql);
				$category_stmt->execute($category_param);
				
				//配送先のカテゴリーのIDを取得
				$last_id_sql = "SELECT LAST_INSERT_ID()";
				$last_id_stmt=$dbh->prepare($last_id_sql);
				$last_id_stmt->execute();
				$category_ids = $last_id_stmt->fetch();
				$category_id = $category_ids[0];
				
			}
			return $category_id;
			
		}catch(PDOException $e){
			
			$dbh->rollBack();
			echo $e->getMessage();
			exit;

		}
		
	}
	
	/**
	 * CSVアップロード時のカテゴリーの新規追加と、カテゴリーIDを取得する
	 * @param カテゴリー名
	 * @return 引数のカテゴリー名のカテゴリーID
	 */
	public static function insertDestinationCategoriesDestinations( $destination_id, $destination_categories_id ) {
		
		$dbh=SingletonPDO::connect();
		
		try {
			
			$sql = "SELECT
						id
					FROM
						destination_categories_destinations
					WHERE
						destination_id = $destination_id
					AND
						destination_categories_id = $destination_categories_id
					LIMIT 1";
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas=$stmt->fetch();
			$param = array(
				'destination_id' => $destination_id,
				'destination_categories_id' => $destination_categories_id
			);
			
			if (!empty($datas)) {
				
				$sql="
				UPDATE
					destination_categories_destinations
				SET
					destination_id = :destination_id,
					destination_categories_id = :destination_categories_id,
					updated = NOW())
				WHERE
					id = :id";
				$param['id'] = $datas['id'];
				
			} else {
				
				$sql="
				INSERT INTO
					destination_categories_destinations 
						(destination_id, destination_categories_id, created, updated)
				VALUES
					(:destination_id, :destination_categories_id, NOW(), NOW())";
				
			}
			
			$stmt=$dbh->prepare($sql);
			$stmt->execute($param);
		
		} catch(Exception $e) {
			
			$dbh->rollback();
			echo $e->getMessage();
			exit;
		}
		
	}

	
	
	//ID単位で配送先情報を削除するメソッド
	public static function deleteDestination($id){

		try{
			$dbh=SingletonPDO::connect();

			$sql="DELETE
					FROM 
						destinations
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
	//カテゴリー名から配送先を取得
	public static function getByCategoryName($company_id, $name){
	
		try{
			$dbh=SingletonPDO::connect();
	
			$sql="
			SELECT
				*
			FROM
				destinations,
				destination_categories,
				destination_categories_destinations
			WHERE
				destinations.id = destination_categories_destinations.destination_id
			AND
				destination_categories.id = destination_categories_destinations.destination_categories_id
			AND
				destination_categories.name = :name
			AND
				destinations.company_id = :company_id
				
			";
	
			$param = array(
						'company_id' => $company_id,
						'name' => $name
			);
			
			$stmt=$dbh->prepare($sql);
			
			$stmt->execute($param);
		
			$destinations=$stmt->fetchAll(PDO::FETCH_ASSOC);
	
			return $destinations;
	
		}catch(Exception $e){
	
			echo $e->getMessage();
	
		}
	}
	
	/**
	 * カンパニーIDから配送先一覧を取得。その際配列のキーはdestinationsのID
	 * @param string $company_id
	 */
	public function getByCompanyIdDestinationsKeyId($company_id){
		try{
			$dbh=SingletonPDO::connect();

			$sql="
			SELECT
				*
			FROM
				destinations
			WHERE
				company_id = :company_id
			";
			$stmt=$dbh->prepare($sql);

			$param = array(
					'company_id' => $company_id,
			);

			$stmt->execute($param);
			$transportRoute=$stmt->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);

			return $transportRoute;

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