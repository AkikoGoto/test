<?php
/**
 * TransportRouteクラス
 * 輸送ルートを設定 エジソン版限定
 * @author Akiko Goto
 * @since 2017/04/05
 * @version 2.0
 *
 */

class TransportRoute{

	public $id;
	public $name;
	public $destination_id;
	public $geo_json;
	public $company_id;
	public $information;
	public $updated;
	public $created;

	/**
	 * 仮置き場指定でルートを抽出
	 * @param destination_id
	 */
	public static function getTransportRoutesByDestination($destination_id){

		try{

			$dbh=SingletonPDO::connect();

				$sql="
					SELECT
						transport_routes.id, transport_routes.name, transport_routes.destination_id, transport_routes.geo_json,

						destinations.destination_name as destination_name
					FROM
						transport_routes
					LEFT JOIN
						destinations
					ON
						destinations.id = transport_routes.destination_id
					WHERE
						destination_id = $destination_id
					";


			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll(PDO::FETCH_ASSOC);

			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}

	public static function getName(){

		try{

			return $name;

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}

	/**
	 * 輸送ルートテーブルにインサート
	 * @param array $data
	 */
	public static function insert($data){
		try{
			$dbh=SingletonPDO::connect();

			$dbh->beginTransaction();

			$sql="
				INSERT INTO transport_routes
						(name, 	destination_id, geo_json, company_id, information, created, updated)
						VALUES
						(:name, :destination_id, :geo_json, :company_id, :information, NOW(), NOW());";

			$stmt=$dbh->prepare($sql);
			$param = array(
					'name' => $data['name'],
					'destination_id' => $data['destination_id'],
					'geo_json' => html_entity_decode($data['geo_json'], ENT_QUOTES),
					'company_id' => $data['company_id'],
					'information' => $data['information'],
			);

			$stmt->execute($param);

			$id = $dbh->lastInsertId('id');

			$dbh->commit();

			return $id;
		}catch(PDOException $e){
			$dbh->rollback();
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}

	/**
	 * 輸送ルートの一覧を取得
	 */
	public static function selectCompanyRoutes($company_id){

		try{
			$dbh=SingletonPDO::connect();

			$sql="
			SELECT
				*
			FROM
				transport_routes
			WHERE
				company_id = :company_id
			";


			$stmt=$dbh->prepare($sql);

			$param = array(
					'company_id' => $company_id,
			);

			$stmt->execute($param);
			$transportRoutes=$stmt->fetchAll(PDO::FETCH_ASSOC);

			return $transportRoutes;

		}catch(PDOException $e){

			echo $e->getMessage();

		}

	}

	/**
	 * 輸送ルートの一覧を取得
	 */
	public static function selectCompanyRoutesFetchUnique($company_id){

		try{
			$dbh=SingletonPDO::connect();

			$sql="
			SELECT
				id, transport_routes.*
			FROM
				transport_routes
			WHERE
				company_id = :company_id
			AND
				geo_json != ''
			";


			$stmt=$dbh->prepare($sql);

			$param = array(
					'company_id' => $company_id,
			);

			$stmt->execute($param);
			$transportRoutes=$stmt->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);

			return $transportRoutes;

		}catch(PDOException $e){

			echo $e->getMessage();

		}

	}

	public static function selectRoute($id){
		try{
			$dbh=SingletonPDO::connect();

			$sql="
				SELECT
				    transport_routes.*,
				    destinations.destination_name
				FROM
				    transport_routes
				    LEFT JOIN
				        destinations
				    ON  transport_routes.destination_id = destinations.id
				WHERE
				    transport_routes.id = :id
				";
			$stmt=$dbh->prepare($sql);

			$param = array(
					'id' => $id,
			);

			$stmt->execute($param);
			$transportRoute=$stmt->fetchAll(PDO::FETCH_ASSOC);

			return $transportRoute[0];

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}


	/**
	 * 輸送ルートの削除
	 * @param $id
	 */
	public static function delete($id){
		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			$sql="
				DELETE
					FROM
						transport_routes
					WHERE
						id = :id;";

			$stmt=$dbh->prepare($sql);

			$param = array(
					'id' => $id,
			);

			$stmt->execute($param);
			$dbh->commit();
		}catch(PDOException $e){
			$dbh->rollback();
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}

	/**
	 * 全輸送ルート及び、全輸送ルートに紐づく全エリアデータを取得
	 * @param $company_id
	 */
	public static function selectAllRoutesAllAreas($company_id){
		try{
			$dbh=SingletonPDO::connect();

			$sql="
				SELECT
					*
				FROM
					transport_routes
				LEFT JOIN navi_areas ON transport_routes.id=navi_areas.transport_route_id
				WHERE
					company_id = :company_id
				AND
					navi_areas.latitude != ''
				";

			$stmt=$dbh->prepare($sql);

			$param = array(
					'company_id' => $company_id,
			);

			$stmt->execute($param);

			$allRoutesAllAreas=$stmt->fetchAll(PDO::FETCH_ASSOC);

			return $allRoutesAllAreas;

		}catch(PDOException $e){
			$dbh->rollback();
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}


	/**
	 * 輸送ルートIDから、エリアの全ての情報を取得する
	 * @param $id
	 */
	public static function selectRouteAreas($id){
		try{
			$dbh=SingletonPDO::connect();

			$sql="
			SELECT
				*, transport_routes.name as transport_routes_name
			FROM
				transport_routes
			LEFT JOIN navi_areas ON transport_routes.id=navi_areas.transport_route_id
			WHERE
				transport_routes.id = :id
			";
			$stmt=$dbh->prepare($sql);

			$param = array(
					'id' => $id,
			);

			$stmt->execute($param);
			$transportRoute=$stmt->fetchAll(PDO::FETCH_ASSOC);

			return $transportRoute;

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}

	/**
	 * 輸送ルートコピー時のinsert
	 * @param $id
	 */
	public static function copy_insert($one_root_areas, $datas){
		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			$root_id = TransportRoute::copy_root_insert($one_root_areas, $datas);

			if($one_root_areas[0]['name'] != null){
				NaviArea::copy_area_insert($one_root_areas, $root_id);
			}

			$dbh->commit();
			return $root_id;

		}catch(PDOException $e){
			$dbh->rollback();
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}

	/**
	 * 輸送ルートコピー時のinsert
	 * @param $id
	 */
	public static function copy_root_insert($one_root_areas, $datas){
		try{

			$dbh=SingletonPDO::connect();

			$sql="
				INSERT INTO transport_routes
						(name, 	destination_id, geo_json, company_id, information, created, updated)
						VALUES
						(:name, :destination_id, :geo_json, :company_id, :information, NOW(), NOW());";

			$stmt=$dbh->prepare($sql);
			$param = array(
					'name' => $datas['name'],
					'destination_id' => $one_root_areas[0]['destination_id'],
					'geo_json' => $one_root_areas[0]['geo_json'],
					'company_id' => $one_root_areas[0]['company_id'],
					'information' => $datas['information'],
			);

			$stmt->execute($param);
			$id = $dbh->lastInsertId('id');

			return $id;

		}catch(PDOException $e){
			$dbh->rollback();
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}

	/**
	 * カンパニーIDとドライバーIDと輸送ルートIDを使って、ドライバーと輸送ルートが紐づいているか確認する
	 * @param $company_id
	 * @param $driver_id
	 * @param $transport_route_id
	 */
	public static function selectDriverRouteAreas($company_id, $driver_id, $transport_route_id, $date){
		try{

			$dbh=SingletonPDO::connect();

			$sql="
			SELECT
				*
			FROM
				transport_route_drivers
			WHERE
				transport_route_id = :transport_route_id
			AND
				driver_id = :driver_id
			AND
				company_id = :company_id
			AND
				date = :date
			";
			$stmt=$dbh->prepare($sql);

			$param = array(
					'company_id' => $company_id,
					'driver_id' => $driver_id,
					'transport_route_id' => $transport_route_id,
					'date' => $date,
			);

			$stmt->execute($param);
			$driverTransportRoute=$stmt->fetchAll(PDO::FETCH_ASSOC);

			return $driverTransportRoute;

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}

	/**
	 * 輸送ルートマスタ、更新
	 * @param $datas
	 */
	public static function update($datas){
		try{

			$dbh=SingletonPDO::connect();

			$sql="
			UPDATE
				transport_routes
			SET
				name=:name, destination_id=:destination_id, geo_json=:geo_json, information=:information
			WHERE
				id = ".$datas['id']
			;
			$stmt=$dbh->prepare($sql);

			$param = array(
					'name' => $datas['name'],
					'destination_id' => $datas['destination_id'],
					'geo_json' => $datas['geo_json'],
					'information' => $datas['information']
			);

			$stmt->execute($param);

		}catch(PDOException $e){
			echo $e->getMessage();
		}
	}

	/**
	 * ドライバーに紐づく輸送ルート、更新
	 * @param $datas
	 */
	public static function updateDriverRoute($datas){
		try{

			$dbh=SingletonPDO::connect();

			$sql="
			UPDATE
				transport_route_drivers
			SET
				transport_route_id=:transport_route_id,
				information = :information
			WHERE
				id = :id"

			;
			$stmt=$dbh->prepare($sql);

			$param = array(
				'id' => $datas['transport_route_drivers_id'],
				'transport_route_id' => $datas['select_root_id'],
				'information' => $datas['information'],
			);

			$stmt->execute($param);

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