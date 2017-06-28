<?php
//営業所情報クラス　Dataクラスを継承

class Branch extends Data{
	
	/**
	 * 営業所長のIDから、その営業所のドライバー全員のIDを返す
	 * @param unknown_type $branch_manager_id
	 */
	public static function getBranchDriverByBranchManagerId($branch_manager_id){

		try{

			$dbh=SingletonPDO::connect();

			$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);


			// ドライバーのIDを取得する
			$sql1="
			SELECT
				id,
				last_name,
				first_name
			FROM 
				drivers
			WHERE 
				geographic_id = $branch_id
			AND EXISTS (
				SELECT id FROM driver_status WHERE driver_status.driver_id = drivers.id)
			GROUP BY drivers.id";

			$stmt=$dbh->prepare($sql1);
			$stmt->execute();
			$drivers=$stmt->fetchAll();

			return $drivers;

	}
	catch(Exception $e)
	{

		echo $e->getMessage();

	}		
		
	}

	/**
	 * 営業所長のIDから、営業所のIDを取得する
	 * @param int $branch_manager_id
	 */
	public static function getBranchIdByManagerId($branch_manager_id){

		try{
			
			$dbh=SingletonPDO::connect();
				
			$sql="
				SELECT 
					geographic_id
				FROM 
					drivers
				WHERE 
					id = $branch_manager_id
				AND
					is_branch_manager = 1 ";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();

			$res = $stmt->fetch();
			$branch_id = $res["geographic_id"];
			
			return $branch_id;

		}catch(Exception $e){

			echo $e->getMessage();

		}

	}
		
	/**
	 * TODO 後で消去する
	 * PHP Unitテストのためのメソッド
	 * @return string
	 */
	public static function hogehoge(){
	
		try{
				
			return "hogehoge";
	
		}catch(Exception $e){
	
			echo $e->getMessage();
	
		}
	
	}
	
	//idを指定して、データ内容を取得　コンテンツ
	public static function getById($id){

		try{
			$dbh=SingletonPDO::connect();

				
			$sql="
		SELECT *
		FROM geographic
		WHERE geographic.id = $id ";

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

	//会社idを指定して、座標を取得　コンテンツ
	public static function getlatLng($company_id){

		try{
			$dbh=SingletonPDO::connect();
				
			$sql="
		SELECT 
			latitude,longitude
		FROM geographic
		WHERE company_id = $company_id
		ORDER BY
			id 
		LIMIT 1 ";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();

			$data=$stmt->fetch();
				
			return $data;

		}
		catch(Exception $e)
		{

			echo $e->getMessage();

		}
	}


	/**
	 * 営業所の緯度経度を取得
	 * @param geographic_id
	 */
	public static function getlatLngByBranch($geographic_id){

		try{
			$dbh=SingletonPDO::connect();
				
			$sql="
		SELECT 
			latitude,longitude
		FROM geographic
		WHERE id = $geographic_id
		ORDER BY
			id 
		LIMIT 1 ";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();

			$data=$stmt->fetch();
				
			return $data;

		}
		catch(Exception $e)
		{

			echo $e->getMessage();

		}
	}



	//会社idを指定して、データ内容を取得　会社名と県名をとってくる
	public static function getByCompanyId($id){

		try{
			$dbh=SingletonPDO::connect();
				
			$data = null;
			
			$sql="
		SELECT 
			company.*,
			geographic.id as geographic_id,
			geographic.*,
			prefectures.id as prefectures_id,
			prefectures.*
		FROM geographic
		JOIN company
		ON company.id=geographic.company_id
		JOIN prefectures
		ON prefectures.id=geographic.prefecture
		WHERE company.id = $id 
		";

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

	//営業所情報　データ投入と編集　$statusの値でスイッチ
	const NEWDATA=1; //新規データ登録
	const EDIT=2; //データ編集

	public static function putInBranch($datas, $status){

		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			switch($status){
				//新規データ登録
				case NEWDATA:

					$sql1="
							INSERT INTO geographic(
							    company_id,
							    latitude,
							    longitude,
							    postal,
							    prefecture,
							    city,
							    ward,
							    town,
							    address,
							    tel,
							    name,
							    relay_server_id,
							    created,
							    updated
							)
							VALUES(
							    :company_id,
							    :latitude,
							    :longitude,
							    :postal,
							    :prefecture,
							    :city,
							    :ward,
							    :town,
							    :address,
							    :tel,
							    :name,
							    (
							        SELECT
							            id
							        FROM
							            relay_servers
							        WHERE
							            company_id = :company_id
							        AND 
										is_production = 1
							    ),
							    now(),
							    now()
							)
					";
					break;

					//データ編集
				case EDIT:

					$sql1="
			UPDATE geographic SET
				company_id=:company_id, latitude=:latitude, longitude=:longitude, postal=:postal,name=:name,
				prefecture=:prefecture, city=:city, ward=:ward, town=:town, address=:address, tel=:tel, 
				updated=now()		
			WHERE id=".$datas['id'];		

					break;
			}

			$stmt=$dbh->prepare($sql1);

			$param = array(

		'company_id' => $datas['company_id'],
		'latitude' => $datas['latitude'],
		'longitude' => $datas['longitude'],
		'postal' => $datas['postal'],
		'name' => $datas['name'],
		'prefecture' => $datas['prefecture'],
		'city' => $datas['city'],
		'ward' => $datas['ward'],
		'town' => $datas['town'],
		'address' => $datas['address'],
		'tel' => $datas['tel'],

			);
			$stmt->execute($param);
			$dbh->commit();


		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}

	//営業所リスト取得 　地理情報のみ
	public static function getBranches($id){
		try{

			$dbh=SingletonPDO::connect();

			$sql="SELECT * FROM geographic WHERE company_id=$id";

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




	//市名から絞り込みの営業所リスト取得 　
	public static function getBranchesByCity($city,$prefecture){
		try{

			$dbh=SingletonPDO::connect();

			$sql="
		SELECT 
			company.*,
			geographic.id as geographic_id,
			geographic.*,
			prefectures.id as prefectures_id,
			prefectures.*
		FROM geographic
		JOIN company
		ON company.id=geographic.company_id
		JOIN prefectures
		ON prefectures.id=geographic.prefecture
		WHERE geographic.city ='".$city."'
		AND geographic.prefecture=$prefecture";


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

	//市と区名から絞り込みの営業所リスト取得 　
	public static function getBranchesByWard($ward,$city,$prefecture){
		try{

			$dbh=SingletonPDO::connect();

			$sql="
		SELECT 
			company.*,
			geographic.id as geographic_id,
			geographic.*,
			prefectures.id as prefectures_id,
			prefectures.*
		FROM geographic
		JOIN company
		ON company.id=geographic.company_id
		JOIN prefectures
		ON prefectures.id=geographic.prefecture
		WHERE geographic.ward ='".$ward."'
		AND geographic.city ='".$city."'
		AND geographic.prefecture =$prefecture
		";


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

	//町村名から絞り込みの営業所リスト取得 　
	public static function getBranchesByTown($town,$city,$prefecture){
		try{

			$dbh=SingletonPDO::connect();

			$sql="
		SELECT 
			company.*,
			geographic.id as geographic_id,
			geographic.*,
			prefectures.id as prefectures_id,
			prefectures.*
		FROM geographic
		JOIN company
		ON company.id=geographic.company_id
		JOIN prefectures
		ON prefectures.id=geographic.prefecture
		WHERE geographic.town ='".$town."'
		AND geographic.city ='".$city."'
		AND geographic.prefecture =$prefecture
		";


			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			while($res=$stmt->fetchObject(__CLASS__)){
				$dataList[]=$res;

			}
			return $dataList;
			var_dump($dataList);
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}

	/**
	 *管理画面でID単位で営業所情報を削除するメソッド
	 */
	public static function deleteBranch($id){

		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			//	$id=$id;

			$sql="DELETE
	FROM geographic
	WHERE geographic.id = :id 
	";

			$res = $dbh->prepare($sql);

			$param=array(
	'id'=>$id
			);

			$res->execute($param);

			$dbh->commit();

		}
		catch(Exception $e){
			$dbh->rollback();
			return 'error';
			//	die($e->getMessage());
		}
	}
	
	public function getByType($company_id, $type){
		try{
			$dbh = SingletonPDO::connect();
		
			$sql = "SELECT
					    *
					FROM
					    geographic
					WHERE
					    company_id = :company_id
					AND type = :type
					";
		
			$stmt = $dbh->prepare($sql);
				
			$param = array(
					'company_id' => $company_id,
					'type' => $type
			);
			$stmt->execute($param);
		
			$geographic = $stmt->fetch(PDO::FETCH_ASSOC);
				
			return $geographic;
		
		}
		catch(Exception $e){
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

	//クラスBranchの終了
}
?>