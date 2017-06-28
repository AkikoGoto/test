<?php
class Users{
	
	public $id;
	public $company_id;
	public $name;
	public $login_id;
	public $passwd;
	public $created;
	public $updated;
	public $driver_id;
	public $user_id;

	/**
	 * 
	 * 閲覧できるドライバーの一覧を取得
	 * @param $company_id
	 * @return $users
	 */
	public static function getVisibleDriversByUserId ( $user_id ) {
		
		try{
	
			$dbh=SingletonPDO::connect();
			$sql=
				"SELECT
					driver_id
				FROM 
					drivers_users
				WHERE
				 	user_id = $user_id";
	
		 	$stmt=$dbh->prepare($sql);
		 	$stmt->execute();
			$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
		 	return $users;
	
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
		
	}
	
	/**
	 * 閲覧できるドライバーを登録
	 * @param $drivers_users[ user_id => [driver_id, ...], ... ]
	 * @return $status
	 */
	public static function setDriversUsers( $company_id, $drivers_users ) {
		
		$dbh=SingletonPDO::connect();
		try{
			
			//会社IDに所属するユーザーを取得
			$users = Users::getUsersByCompanyId($company_id);
			
			foreach ( $users as $user ) {
				
				$user_id = $user['user_id'];
				
				$sql=
					"DELETE FROM drivers_users WHERE user_id = $user_id";
			 	$stmt=$dbh->prepare($sql);
			 	$stmt->execute();
			 	
			 	if (!empty($drivers_users) &&
			 		array_key_exists( $user_id , $drivers_users ) ) {
			 		
			 		$i_sql="
						INSERT INTO drivers_users
								(driver_id, user_id, created, updated)
						VALUES
								(:driver_id, :user_id, NOW(), NOW())";
					$i_stmt=$dbh->prepare($i_sql);
					foreach ( $drivers_users[$user_id] as $driver_id ) {
					 	$param = array(
							'driver_id' => $driver_id,
							'user_id' => $user_id
						);
						$i_stmt->execute($param);
					}
					
			 	}
				
			}
			
		}
		catch(Exception $e)
		{
			$dbh->rollback();
			echo $e->getMessage();
		}
		
	}
	
	/**
	 * 
	 * 閲覧ユーザーの一覧の取得
	 * @param $company_id
	 * @return $users
	 */
	public static function getUsersByCompanyId ( $company_id ) {
		
		try{
	
			$dbh=SingletonPDO::connect();
			$sql=
				"SELECT
					users.id as user_id,
					CONCAT(last_name, ' ', first_name) as user_name
				FROM 
					users
				WHERE
				 	users.company_id = $company_id";
	
		 	$stmt=$dbh->prepare($sql);
		 	$stmt->execute();
			$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			// driversを検索
			foreach ($users as $key => $user) {
				// driverを取得
				$users[$key]['drivers'] = array();
				$user_id = $user['user_id'];
				
				$users[$key]['drivers'] = Users::getDriversUserViewing ( $user_id, $company_id ) ;
			}
			
		 	return $users;
	
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
		
	}
	
	/**
	 * konbanwa
	 */
	public static function getDriversUserViewing ( $user_id, $company_id ) {
		
		try {
			$dbh=SingletonPDO::connect();
			$d_sql = 
				"SELECT
					id as driver_id,
					CONCAT(last_name, ' ', first_name) as driver_name
				FROM 
					drivers
				WHERE 
					drivers.company_id = $company_id
				AND
					EXISTS(
						SELECT
							id
						FROM
							drivers_users
						WHERE
							driver_id = drivers.id
						AND
							user_id = $user_id
					)
				ORDER BY driver_id ASC";
			
			$d_stmt=$dbh->prepare($d_sql);
			$d_stmt->execute();
			$drivers = $d_stmt->fetchAll(PDO::FETCH_ASSOC);
			
			
			return $drivers;
		} catch (Exception $e) {
			
			echo $e->getMessage();
			
		}
		
	}


	/**
	 * ユーザーログイン
	 * 
	 */
	public static function login($login_id,$passwd){
	
		try{
			$dbh=SingletonPDO::connect();
	
			// idとパスワードでユーザーテーブルを検索
			$sql = "SELECT
		    			* FROM users
		           	WHERE 
		           		login_id = '$login_id'
		            AND 
		             	passwd = MD5('$passwd')";
			 
			$res=$dbh->prepare($sql);
			$res->execute();
				
			$idArray = $res->fetchAll(PDO::FETCH_ASSOC);
				
			return $idArray;
	
		}catch(Exception $e){
	
			echo $e->getMessage();
	
		}
	}	
	
	
	/**
	 * 
	 * 閲覧ユーザーの取得
	 * @param $user_id
	 * @return $users
	 */
	public static function getUserById ( $user_id ) {
		
		try{
	
			$dbh=SingletonPDO::connect();
			$sql=
				"SELECT
					users.id,
					users.company_id,
					last_name,
					first_name,
					furigana,
					mobile_tel,
					mobile_email,
					users.login_id,
					users.passwd
				FROM 
					users
				WHERE
				 	users.id = $user_id
				LIMIT 1";
	
		 	$stmt=$dbh->prepare($sql);
		 	$stmt->execute();
			$users = $stmt->fetch(PDO::FETCH_ASSOC);
			
		 	return $users;
	
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
		
	}
	
	/**
	 * 
	 * 閲覧ユーザーの登録
	 * @param $datas
	 * @param $status
	 * @return none
	 */
	public static function putInUser ( $datas, $status ) {
		
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
		
		try{
	
			switch($status){
				//新規データ登録
				case "NEWDATA":
					$md5_passwd = md5($datas['passwd']);
					$sql = "INSERT
							INTO users
								(company_id, last_name, first_name,
								 furigana, mobile_tel, mobile_email,
								 login_id, passwd, created, updated)
							VALUES
								(:company_id, :last_name, :first_name,
								 :furigana, :mobile_tel, :mobile_email,
								 :login_id, :passwd, NOW(), NOW())
					";
				break;
	
					//データ編集
				case "EDIT":
					
					if($datas['passwd']){
						
						$md5_passwd = md5($datas['passwd']);
						
					} else {
						$sql_passwd="SELECT passwd FROM users WHERE id = ".$datas['id'];
						$stmt=$dbh->prepare($sql_passwd);
						$stmt->execute();
						$res=$stmt->fetch();
						$exited_passwd=$res[0];
						$md5_passwd=$exited_passwd;
					}
						
					$sql = 
						"UPDATE
							users
						SET
							company_id = :company_id,
							last_name = :last_name,
							first_name = :first_name,
							furigana = :furigana, 
							mobile_tel = :mobile_tel, 
							mobile_email = :mobile_email,
							login_id = :login_id,
							passwd = :passwd
						WHERE id = ".$datas['id'];
	
					break;
			}
			
			$stmt=$dbh->prepare($sql);
		
			$param = array (
				'company_id' => $datas['company_id'],
				'last_name' => $datas['last_name'],
				'first_name' => $datas['first_name'],
				'furigana' => $datas['furigana'],
				'mobile_tel' => $datas['mobile_tel'],
				'mobile_email' => $datas['mobile_email'],
				'login_id' => $datas['login_id'],
				'passwd' => $md5_passwd
			);

			$stmt->execute($param);
			$dbh->commit();
			
		}catch(PDOException $e){
	
			$dbh->rollback();
			echo $e->getMessage();
			exit;
			
		}
		
	}
	
	/**
	 * 閲覧ユーザーをドライバー情報と一緒に取得する
	 * ドライバーは、閲覧ユーザー毎に閲覧可能かどうかもチェックして取得する
	 * @param 会社ID
	 * @return ユーザー情報	 * 
	 */
	public static function getViewersWithDriversByCompanyId( $company_id ) {
		
		try{
	
			$dbh=SingletonPDO::connect();
			// userを取得
			$u_sql = "SELECT
					id as user_id,
					CONCAT(last_name, ' ', first_name) as user_name
				FROM 
					users
				WHERE 
					users.company_id = $company_id
				ORDER BY user_id ASC";
			$u_stmt=$dbh->prepare($u_sql);
			$u_stmt->execute();
			$users = $u_stmt->fetchAll(PDO::FETCH_ASSOC);
			
			// driversを検索
			foreach ($users as $key => $user) {
				// driverを取得
				$users[$key]['drivers'] = array();
				$user_id = $user['user_id'];
				$d_sql = 
					"SELECT
						id as driver_id,
						CONCAT(last_name, ' ', first_name) as driver_name,
						(
							SELECT
								id
							FROM
								drivers_users
							WHERE
								driver_id = drivers.id
							AND
								user_id = $user_id
						) as is_viewered
					FROM 
						drivers
					WHERE 
						drivers.company_id = $company_id
					ORDER BY driver_id ASC";
				$d_stmt=$dbh->prepare($d_sql);
				$d_stmt->execute();
				$drivers = $d_stmt->fetchAll(PDO::FETCH_ASSOC);
				
				$users[$key]['drivers'] = $drivers;
				
			}
			
		 	return $users;
	
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
		
	}
	
	
	/**
	 * 
	 * 閲覧ユーザーの削除
	 * @param $user_id
	 * @return none
	 */
	public static function deleteUser( $user_id ){
	
		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
	
			$sql =
				"DELETE
				 FROM users
				 WHERE id = :id";
			$res = $dbh->prepare($sql);
	
			$param = array( 'id' => $user_id );
	
			$res->execute($param);
			$dbh->commit();
	
		}catch(Exception $e){
			$dbh->rollback();
			die($e->getMessage());
		}
		
	}
	
	/**
	 * 自動ログイン用クレデンシャルで認証する
	 * @param string $credential クレデンシャル
	 * @return array 会社情報:
	 */
	public static function authenticateCredential($credential){
		try{
	
			$dbh = SingletonPDO::connect();
	
			// idでドライバーテーブルを検索
			$sql ="SELECT users.*
				FROM users
				INNER JOIN user_credentials
				ON users.id = user_credentials.user_id
				WHERE credential = :credential
				AND expires > now( )
				LIMIT 1";
			$res=$dbh->prepare($sql);
			$param = array(':credential' => $credential);
			$res->execute($param);
				
			$idArray = $res->fetchAll(PDO::FETCH_ASSOC);
	
			return $idArray;
	
		}catch(Exception $e){
	
			die($e->getMessage());
	
		}
	}

	/**
	 * 自動ログイン用クレデンシャル再生成
	 * @param int $user_id 
	 * @return string 再生成されたクレデンシャル
	 */
	public static function regenerateCredential($user_id){
		try{
			Users::deleteCredential();
			$new_credential = Users::createCredential($user_id);
			return $new_credential;
	
		}catch(Exception $e){
	
			die($e->getMessage());
	
		}
	}
	
	
	/**
	 * 自動ログイン用クレデンシャル削除
	 */
	public static function deleteCredential() {
		if (!isset($_COOKIE['user_credential'])) return;
	
		$credential = $_COOKIE['user_credential'];
		try{
	
			$dbh = SingletonPDO::connect();
	
			$sql ="DELETE FROM user_credentials
				WHERE credential = :credential";
			$res=$dbh->prepare($sql);
			$param = array(':credential' => $credential);
			$res->execute($param);
			setcookie('user_credential', '', 1, '/',  $_SERVER['SERVER_NAME'], FALSE, TRUE);
	
			return;
	
		}catch(Exception $e){
	
			die($e->getMessage());
	
		}
	
	}
		
	/**
	 * 自動ログイン用クレデンシャル生成
	 * @param int $user_id 
	 * @return string クレデンシャル
	 */
	public static function createCredential($user_id) {
		try{
	
			$credential = uniqid("", TRUE);
			$dbh = SingletonPDO::connect();
	
			// idでドライバーテーブルを検索
			$sql ="INSERT INTO user_credentials(credential, user_id, expires, created)
			VALUES(:credential, :user_id, now() + INTERVAL 1 MONTH, now())";
				
			$res = $dbh->prepare($sql);
			$param = array(
					':credential' => $credential,
					':user_id' => $user_id,
			);
	
			$res->execute($param);
			setcookie('driver_credential', $credential, time()+60*60*24*30, '/',  $_SERVER['SERVER_NAME'], FALSE, TRUE);
	
			return $credential;
	
		}catch(Exception $e){
	
			die($e->getMessage());
	
		}
	}
	

	/**
	 * 自動ログイン時の、IDのみで情報を返す処理
	 */
	public static function autoLogin($id){
		try{
	
			$dbh = SingletonPDO::connect();
	
			// idとパスワードでユーザーテーブルを検索 　Webから登録した場合のみ、ログインできる。
			$sql = "SELECT
	    				* FROM users
	            	WHERE 
	            		id=$id
	            	LIMIT 1
					";
	
			$res=$dbh->prepare($sql);
			$res->execute();
	
			$idArray = $res->fetchAll(PDO::FETCH_ASSOC);
				
			return $idArray;
	
		}catch(Exception $e){
	
			die($e->getMessage());
	
		}
	}
	
	
	
}