<?php
/**
 * TransportRouteDriverクラス
* 輸送ルートドライバークラスエジソン版限定
* @author Yuji Hamada
* @since 2017/04/26
* @version 2.0
*
*/

class TransportRouteDriver{

	public $id;
	public $transport_route_id;
	public $driver_id;
	public $company_id;
	public $date;
	public $information;
	public $updated;
	public $created;

	/**
	 * 引数で受け取った情報を輸送ルートドライバーテーブルにインサート
	 * @param array $transportRouteDrivers[
	 * @param string$company_id
	 * @param string $date
	 */
	public function insert($transportRouteDrivers, $company_id, $date) {
	
		try {
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
				
			$sql = "INSERT transport_route_drivers(transport_route_id, driver_id, information, date, company_id, created, updated) 
						VALUES
					";
				
			$query = array();
			$values = array();
			$i = 0;
			$values['date'] = $date;
			$values['company_id'] = $company_id;
			
			foreach($transportRouteDrivers as $key => $transportRouteDriver){
				$query[$key] = '(:transport_route_id' . $i . ', :driver_id' . $i . ', :information' . $i . ', :date, :company_id' .', NOW(), NOW())';
				$values['driver_id'. $i] = $key;
				$values['transport_route_id'. $i] = $transportRouteDriver['transportRoute'];
				$values['information'. $i] = $transportRouteDriver['information'];
				$i++;
			}
			
			$sql .= implode(',', $query);
			
			//日付とドライバーIDが同じものがあった場合は輸送ルートIDと備考だけ更新する
			$sql .= ' ON DUPLICATE KEY UPDATE transport_route_id = VALUES(transport_route_id), information = VALUES(information)';
			
			$stmt = $dbh->prepare($sql);
			$stmt->execute($values);
				
			$dbh->commit();
			
		}catch(PDOException $e){
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	/**
	 * 輸送ルートドライバーテーブルから会社IDと日付でセレクト
	 * @param unknown $company_id
	 */
	public function selectByCompany($company_id, $date){
		try{
			$dbh=SingletonPDO::connect();
				
			$sql = "SELECT driver_id, transport_route_drivers.* FROM transport_route_drivers WHERE company_id = :company_id and date = :date";
			$stmt=$dbh->prepare($sql);
				
			$param = array('company_id' => $company_id, 'date' => $date);
			$stmt->execute($param);
				
			$transportRouteDrivers=$stmt->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);
			return $transportRouteDrivers;
		}catch(PDOException $e){
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	public function delete($id){
		try {
			$dbh=SingletonPDO::connect();
		
			$sql = "DELETE
					FROM
						transport_route_drivers
					WHERE
						id = :id
					";
		
			$param = array('id' => $id);				
				
			$stmt = $dbh->prepare($sql);
			$stmt->execute($param);
		
		}catch(PDOException $e){
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	public function getRoutesByDriverIdAndDate($driver_id, $date){
		
		try{
			
			$dbh=SingletonPDO::connect();
			
			$sql = "SELECT 
						transport_routes.id, transport_routes.name, transport_routes.geo_json, transport_route_drivers.information
					FROM 
						transport_route_drivers 
					JOIN
						transport_routes
					ON
						transport_routes.id = transport_route_drivers.transport_route_id
					WHERE 
						driver_id = $driver_id 
					AND 
						date = \"$date\"";

	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			
			$routes = $stmt->fetch(PDO::FETCH_ASSOC);

			return $routes;
			
		}catch(PDOException $e){
			
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
		}
		
	}
	
}