<?php
//Driverクラス　Dataクラスを継承

class KomatsuDriver extends Driver{
	
	/* komatsu_company_options */
	public $photo_interval_time;
	public $photo_interval_distance;

	/* komatsu_driver_options */
	public $should_save_power_when_unplugged;
	public $should_take_photo;
	public $should_notice_obstacles;
	public $obstacles_file_name;
	public $obstacles_file_time;
	
	//ステータスと位置情報取得の間隔を取得
	public static function komatsu_getAppConfig($driver_id){
	
		try{
			$dbh=SingletonPDO::connect();
	
			$sql=<<<EOL
SELECT
	drivers.company_id,
	action_1, action_2, action_3, action_4,
	distance, time,
	IFNULL(should_save_power_when_unplugged, 1) as should_save_power_when_unplugged, 
	IFNULL(should_take_photo, 0) as should_take_photo, 
	IFNULL(should_notice_obstacles, 0) as should_notice_obstacles,
	obstacles_file_name, 
	photo_interval_time,
	photo_interval_distance
FROM drivers 
LEFT JOIN komatsu_driver_options
ON komatsu_driver_options.driver_id = drivers.id
LEFT JOIN actions
ON actions.company_id = drivers.company_id
LEFT JOIN intervals
ON intervals.company_id = drivers.company_id
LEFT JOIN komatsu_company_options
ON komatsu_company_options.company_id = drivers.company_id
WHERE drivers.id = $driver_id
LIMIT 1
EOL;
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
	
			$obstacles = new Komatsu_obstacle;
			
			while($res=$stmt->fetchObject(__CLASS__)){
				$res->obstacles_file_time = $obstacles->get_file_time($res->company_id, $res->obstacles_file_name); 
				if(!$res->obstacles_file_time) {
					$res->obstacles_file_name = ''; 
				}
				$data[]=$res;
					
			}
			return $data;
	
		}
		catch(Exception $e)
		{
	
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
			drivers.id as id, geographic.id as geographic_id,
			drivers.last_name, drivers.first_name,
			drivers.company_id, drivers.regist_number,
			drivers.created,
			IFNULL(komatsu_driver_options.should_save_power_when_unplugged, 1) as should_save_power_when_unplugged,
			IFNULL(komatsu_driver_options.should_take_photo, 0) as should_take_photo,
			IFNULL(komatsu_driver_options.should_notice_obstacles, 0) as should_notice_obstacles,
			komatsu_driver_options.obstacles_file_name
			FROM
			drivers
			LEFT JOIN
			geographic
			ON
			drivers.geographic_id = geographic.id
			LEFT JOIN
			komatsu_driver_options
			ON drivers.id = komatsu_driver_options.driver_id
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

	//会社IDによりドライバーリスト取得 　
	public static function komatsu_ReplaceDriversOptions($company_id, $options){
		try{
	
			$dbh=SingletonPDO::connect();
			
			// 変更対象のドライバーが指定された会社に所属しているかチェックする
			$driver_ids = implode(',', array_keys($options));
			$sql = "SELECT id FROM drivers WHERE company_id != :company_id and id IN (:driver_ids) LIMIT 1";
			$stmt=$dbh->prepare($sql);
			$param = array(
					'company_id' => $company_id,
					'driver_ids' => $driver_ids
			);
			$stmt->execute($param);
			if($stmt->rowCount() > 0) {
				echo 'Drivers not belongs to your company exists.';
				return false;
			}
			
			foreach($options as $driverOptions) {
				if(!isset($driverOptions['should_save_power_when_unplugged']) || !isset($driverOptions['should_take_photo']) || !isset($driverOptions['should_notice_obstacles'])){
					echo 'Required options are not sent.';
					return false;
				}
			}
			
			// ドライバー別APP設定を更新する
			$replace_values_rows = array();
			//var_dump($options);
		
			foreach($options as $driver_id => $option_value) {
				$obstacles_file_name = mysql_real_escape_string($option_value['obstacles_file_name']);
				
				$row_str = implode(',', array(
						$driver_id,
						(empty($option_value['should_save_power_when_unplugged'])? 0:1),
						(empty($option_value['should_take_photo'])? 0:1),
						(empty($option_value['should_notice_obstacles'])? 0:1),
						"'${obstacles_file_name}'",
						'now()',
						'now()'
				));
				$replace_values_rows[]= "(${row_str})";
				//var_dump($row_str);
			}
			$replace_values = implode(',', $replace_values_rows);
			//die(var_dump($replace_values));
			$sql=<<<EOL
INSERT INTO komatsu_driver_options
(
	driver_id,
	should_save_power_when_unplugged,
	should_take_photo,
	should_notice_obstacles,
	obstacles_file_name,
	created,
	updated
)
VALUES
$replace_values
ON DUPLICATE KEY UPDATE
should_save_power_when_unplugged=VALUES(should_save_power_when_unplugged),
should_take_photo=VALUES(should_take_photo),
should_notice_obstacles=VALUES(should_notice_obstacles),
obstacles_file_name = VALUES(obstacles_file_name),
updated=VALUES(updated)
EOL;
			$stmt=$dbh->prepare($sql);
			
			$dbh->beginTransaction();
			$stmt->execute();
			return $dbh->commit();
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
			$dbh->rollback();
		}
	}

//クラスDataの終了
}
?>