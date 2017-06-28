<?php
class CompanyOptions {
	
	public $id;
	public $company_id;
	public $public_unique_id;
	public $created;
	public $updated;
	
	/*
	 * ユニークIDで、リアルタイムマップを公開している会社を検索
	 * @param public_unique_id
	 * @return 一般公開している会社のcompany_id
	 */
	public static function getPublicCompanyByUniqueId( $unique_id ) {
		
		try {
			
			$dbh=SingletonPDO::connect();
			$sql="	SELECT 
						id, company_id
					FROM 
						company_options
					WHERE
						public_unique_id = '$unique_id'
					AND
						is_public = 1
					LIMIT 1";
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$newtopics=$stmt->fetch(PDO::FETCH_ASSOC);
			
			return $newtopics;
		
		} catch(Exception $e) {
			
			$dbh->rollback();
			echo $e->getMessage();
			
		}
		
	}
	
	/*
	 * ユニークIDで、リアルタイムマップを公開している会社を検索
	 * @param public_unique_id
	 * @return 一般公開している会社のcompany_id
	 */
	public static function CanUsersViewRealtimeMap( $company_id ) {
		
		try {
			
			$dbh=SingletonPDO::connect();
			$sql="	SELECT 
						id
					FROM 
						company_options
					WHERE
						company_id = $company_id
					AND
						is_users_viewing = 1
					LIMIT 1";
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$newtopics=$stmt->fetch(PDO::FETCH_ASSOC);
			
			return $newtopics;
		
		} catch(Exception $e) {
			
			$dbh->rollback();
			echo $e->getMessage();
			
		}
		
	}
	
	/*
	 * 会社のリアルタイムマップの公開状態を挿入および変更
	 * @param public_unique_id
	 * @return 一般公開している会社のcompany_id
	 */
	public static function setCompanyOption( $company_id, $is_public, $is_users_viewing ) {
		
		try {
			
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			// public url
			$current_datetime = date("YmdHis", time());
			$natural_unique_id = $company_id.$current_datetime;
			$unique_id = sha1( $natural_unique_id );
			
			// viewers url
//			$natural_viewing_id = $natural_unique_id.'viewing';
//			$viewers_unique_id = sha1( $natural_viewing_id );
			
			$sql="
			INSERT INTO
				company_options 
					(company_id, is_public, public_unique_id,
					 is_users_viewing, viewers_unique_id, created, updated)
			VALUES
					(:company_id, :is_public, :public_unique_id,
					 :is_users_viewing, :viewers_unique_id, NOW(), NOW())
			ON DUPLICATE KEY UPDATE
				is_public = VALUES(is_public),
				public_unique_id = IF(public_unique_id != 0, public_unique_id, VALUES(public_unique_id)),
				is_users_viewing = VALUES(is_users_viewing),
				viewers_unique_id = IF(viewers_unique_id != 0, viewers_unique_id, VALUES(viewers_unique_id)),
				updated = NOW()";
			
			$stmt=$dbh->prepare($sql);
			$param = array(
				'company_id' => $company_id,
				'is_public' => $is_public,
				'public_unique_id' => $unique_id,
				'is_users_viewing' => $is_users_viewing,
				'viewers_unique_id' => $viewers_unique_id
			);
			
			$stmt->execute($param);
		
		} catch(Exception $e) {
			
			$dbh->rollback();
			echo $e->getMessage();
			
		}
		
	}
	
	/*
	 * 検索したい会社が、リアルタイムマップを一般公開しているか
	 * @param company_id
	 * @return 地図を一般公開しているか、一般公開用ユニークID
	 */
	public static function getPublicCompanyDataById( $company_id ) {
		
		try {
			
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			$sql="	SELECT 
						is_public, public_unique_id,
						is_users_viewing, viewers_unique_id
					FROM 
						company_options
					WHERE
						company_id = '$company_id'
					LIMIT 1";
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$newtopics=$stmt->fetch(PDO::FETCH_ASSOC);
			
			return $newtopics;
		
		} catch(Exception $e) {
			
			$dbh->rollback();
			echo $e->getMessage();
			
		}
		
	}
	
	/*
	 * 検索したい会社が、リアルタイムマップを一般公開しているか
	 * @param company_id
	 * @return 地図を一般公開しているか、一般公開用ユニークID
	 */
	/*
	public static function getPublicCompanyDataById( $company_id ) {
		
		try {
			
			$dbh=SingletonPDO::connect();
			$sql="	SELECT 
						is_public, public_unique_id
					FROM 
						options
					WHERE
						company_id = '$company_id'
					LIMIT 1";
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$newtopics=$stmt->fetch(PDO::FETCH_ASSOC);
			
			return $newtopics;
		
		} catch(Exception $e) {
			
			$dbh->rollback();
			echo $e->getMessage();
			
		}
		
	}
	*/
}