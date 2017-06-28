<?php
class KomatsuData extends Data {
	/*komatsu_company_options*/
	public $photo_interval_distance;
	public $photo_interval_time;
	
	//ステータスと位置情報取得の間隔を取得
	public static function getStatusAndInterval($company_id){
	
		try{
			$dbh=SingletonPDO::connect();
	
			$sql=<<<EOL
SELECT
	action_1, action_2, action_3, action_4,
	distance, time,
	IFNULL(photo_interval_distance, 50) as photo_interval_distance,
	IFNULL(photo_interval_time, 5) as photo_interval_time
FROM actions
LEFT JOIN intervals
ON actions.company_id = intervals.company_id
LEFT JOIN komatsu_company_options
ON komatsu_company_options.company_id = actions.company_id 
WHERE actions.company_id = $company_id
LIMIT 1
EOL;
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
	
			while($res=$stmt->fetchObject(__CLASS__)){
				$data[]=$res;
			}
			return $data;
	
		} catch(Exception $e){
			echo $e->getMessage();
		}
	}

	public static function komatsu_saveCompanyOptions($options){
	
		try {
			$dbh=SingletonPDO::connect();
			
			$sql=<<<EOL
INSERT INTO komatsu_company_options
(
	company_id,
	photo_interval_distance,
	photo_interval_time,
	created,
	updated
)
VALUES(
	:company_id,
	:photo_interval_distance,
	:photo_interval_time,
	now(),
	now()
)
ON DUPLICATE KEY UPDATE
photo_interval_distance=VALUES(photo_interval_distance),
photo_interval_time=VALUES(photo_interval_time),
updated=VALUES(updated)
EOL;
			$dbh->beginTransaction();
			$stmt=$dbh->prepare($sql);
			$param = array(
					'company_id' => $options['company_id'],
					'photo_interval_distance' => $options['photo_interval_distance'],
					'photo_interval_time' => $options['photo_interval_time']
			);
			var_dump($param);
			$stmt->execute($param);
			return $dbh->commit();
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}
}
