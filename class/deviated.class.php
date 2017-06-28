<?php
/**
 * あるドライバーが現在ルート外れの状況かどうか
 * @author Akiko Goto
 * @since 2017/5/08
 * @version 2.1
 *
 */

class Deviated {
	
	public $driver_id;
	public $is_deviated;
	public $created;
	public $updated;
		
	
	public static function getIsDeviated($driver_id){

		try{

			$dbh=SingletonPDO::connect();

			// ドライバーのIDを取得する
			$sql="
			SELECT
				is_deviated
			FROM 
				last_deviated
			WHERE 
				driver_id = $driver_id";
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetch();
			$is_deviated = $result['is_deviated'];

			return $is_deviated;

		}catch(PDOException $e){
			
			echo $e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
			
		}
		
	}

}
?>