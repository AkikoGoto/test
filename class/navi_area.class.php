<?php
/**
 *　areaクラス
 * @author Yuji Hamada
 * @since 2017/4/13
 * @version 2.0
 */

class NaviArea{

	public $id;
	public $name;
	public $geographic_id;
	public $message;
	public $latitude;
	public $longitude;
	public $radius;
	public $created;
	public $updated;

	/**
	 * ナビエリアテーブルにインサート
	 * @param array $data
	 */
	public function insert($data){
		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			$sql="
				INSERT INTO
					navi_areas(name, transport_route_id, message, latitude, longitude, radius, created, updated)
				VALUES
					(:name, :transport_route_id, :message, :latitude, :longitude, :radius,  NOW(), NOW());";

			$stmt=$dbh->prepare($sql);

			$param = array(
					'name' => $data['name'],
					'transport_route_id' => $data['transport_route_id'],
					'message' => $data['navi_message'],
					'latitude' => $data['latitude'],
					'longitude' => $data['longitude'],
					'radius' => $data['radius'],
			);

			$stmt->execute($param);

			$dbh->commit();
		}catch(PDOException $e){
			$dbh->rollback();
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}


	/**
	 * 輸送ルートコピー時のinsert
	 * @param $id
	 */
	public function copy_area_insert($one_root_area, $root_id){
		try{

			foreach($one_root_area as $areas){

				$dbh=SingletonPDO::connect();

				$sql="
				INSERT INTO
					navi_areas(name, transport_route_id, message, latitude, longitude, radius, created, updated)
				VALUES
					(:name, :transport_route_id, :message, :latitude, :longitude, :radius,  NOW(), NOW());";

				$stmt=$dbh->prepare($sql);

				$param = array(
						'name' => $areas['name'],
						'transport_route_id' => $root_id,
						'message' => $areas['message'],
						'latitude' => $areas['latitude'],
						'longitude' => $areas['longitude'],
						'radius' => $areas['radius']
				);

				$stmt->execute($param);
			}

		}catch(PDOException $e){
			$dbh->rollback();
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	/**
	 * 輸送ルートに紐づくナビエリアの取得
	 * @param string $transportRouteId
	 */
	public function selectbyTransportRouteId($transportRouteId){
		try{
			$dbh=SingletonPDO::connect();
		
			$sql="
			SELECT
				*
			FROM
				navi_areas
			WHERE
				transport_route_id = :transport_route_id
			";
			$stmt=$dbh->prepare($sql);
		
			$param = array(
					'transport_route_id' => $transportRouteId,
			);
		
			$stmt->execute($param);
			$naviAreas=$stmt->fetchAll(PDO::FETCH_ASSOC);
		
			return $naviAreas;
		
		}catch(PDOException $e){
		
			echo $e->getMessage();
		
		}
	}
	
	/**
	 * ナビエリアIDよりナビエリアを取得
	 * @param string $id
	 */
	public function getById($id){
		try{
			$dbh=SingletonPDO::connect();
		
			$sql="
			SELECT
				*
			FROM
				navi_areas
			WHERE
				id = :id
			";
			$stmt=$dbh->prepare($sql);
		
			$param = array(
					'id' => $id,
			);
		
			$stmt->execute($param);
			$naviArea=$stmt->fetchAll(PDO::FETCH_ASSOC);
		
			return $naviArea[0];
		
		}catch(PDOException $e){
		
			echo $e->getMessage();
		
		}
	}
	
	/**
	 * ナビエリアのアップデート
	 * @param array $data
	 */
	public function update($data){
		try{
			$dbh=SingletonPDO::connect();
		
			$sql="
			UPDATE
				navi_areas
			SET
				name = :name, transport_route_id = :transport_route_id, message = :message, latitude = :latitude, longitude = :longitude, radius = :radius
			WHERE
				id = :id;
			";
			$stmt=$dbh->prepare($sql);

			$param = array(
					'id' => $data['id'],
					'name' => $data['name'],
					'transport_route_id' => $data['transport_route_id'],
					'message' => $data['navi_message'],
					'latitude' => $data['latitude'],
					'longitude' => $data['longitude'],
					'radius' => $data['radius'],
			);
			
			$stmt->execute($param);

		}catch(PDOException $e){
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	/**
	 * ナビエリアの削除
	 * @param $id
	 */
	public static function delete($id){
		try{
			$dbh=SingletonPDO::connect();
	
			$sql="
				DELETE
					FROM
						navi_areas
					WHERE
						id = :id;";
	
			$stmt=$dbh->prepare($sql);
	
			$param = array(
					'id' => $id,
			);
	
			$stmt->execute($param);
		}catch(PDOException $e){
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	/**
	 * 複数の輸送ルートからそれぞれに紐付いたナビエリアを取得
	 * @param array $transportRoutes
	 */
	public function transportRoutesNaviareas($transportRoutes){
		try{
			$dbh=SingletonPDO::connect();
			
			$naviAreas = array();
			if(empty($transportRoutes)){
				return;
			}
			foreach($transportRoutes as $transportRoute){
				$sql="
					SELECT
						*
					FROM
						navi_areas
					WHERE
						transport_route_id = :transport_route_id
					";
				$stmt=$dbh->prepare($sql);
				
				$param = array(
						'transport_route_id' => $transportRoute['id'],
				);
				
				$stmt->execute($param);
				$routeAreas = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if(!empty($routeAreas)){
					//ナビエリアが存在する場合は配列に加える
					$naviAreas[$transportRoute['id']] = $routeAreas;
				}
			}
		
			return $naviAreas;
		
		}catch(PDOException $e){
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
		
	}
}