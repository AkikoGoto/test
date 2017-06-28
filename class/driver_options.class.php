<?php
class DriverOptions {
	
	public $id;
	public $driver_id;
	public $is_public;
	public $created;
	public $updated;
		
	/*
	 * 会社のリアルタイムマップの公開状態を挿入および変更
	 * @param public_unique_id
	 * @return 一般公開している会社のcompany_id
	 */
	public static function setDriverIntoPublic( $company_id, $drivers ) {
		
		try {
			$dbh=SingletonPDO::connect();
			
			//会社IDに所属するドライバーが、オプションテーブルに存在するとき、
			//すべてのドライバーを一度非公開にする
			$clear_sql="
			DELETE FROM driver_options
			WHERE
			EXISTS(
				SELECT id
				FROM drivers
				WHERE company_id = $company_id)";
			
			$clear_stmt=$dbh->prepare($clear_sql);
			$clear_stmt->execute($clear_param);
			
			//選択したドライバーのみ公開にする
			$sql="
			INSERT INTO
				driver_options 
					(driver_id, is_public, created, updated)
			VALUES
				( :driver_id, 1, NOW(), NOW())";
			$stmt=$dbh->prepare($sql);
			foreach ($drivers as $driver_id) {
				$param = array(
					'driver_id' => $driver_id
				);
				$stmt->execute($param);
			}
			
			
		} catch(Exception $e) {
			
			$dbh->rollback();
			echo $e->getMessage();
			
			exit;
		}
		
	}
	
	/*
	 * 公開するドライバーを取得する
	 * @param company_id
	 * @return driber_idの配列
	 */
	public static function getPublicDriversByCompanyId( $company_id ) {
		
		try {
			
			$dbh=SingletonPDO::connect();
			$sql="	SELECT 
						id, driver_id
					FROM 
						driver_options
					WHERE
						EXISTS(
							SELECT id
							FROM drivers
							WHERE drivers.id = driver_options.driver_id
							AND drivers.company_id = $company_id)
					AND
						is_public = 1";
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$newtopics=$stmt->fetchAll(PDO::FETCH_ASSOC);
			
			return $newtopics;
			
			
		} catch (Exception $e) {
			
		}
		
	}
}