<?php

class Data{
	
	/* company */
	public $id;
	public $is_company;
	public $company_name;
	public $business_hours_24;
	public $business_hours;
	public $car_number;
	public $pick_up;
	public $credit;
	public $debit;
	public $regist_number;
	public $info;
	public $created;
	public $updated;
	public $fare;
	public $from_web;
	public $email;	
	public $email_display;	
	public $contact_person_last_name;
	public $contact_person_first_name;
	public $contact_tel;	
	public $note;	
	public $url;	
	public $passwd;	
	public $driver_number;	
	public $company_group_id;
	public $is_ban_driver_editing;
	public $is_lite;
	public $remarks;
	public $company_roll_id;
	
	/* geographic */
	public $company_id;
	public $latitude;
	public $longitude;
	public $postal;
	public $prefecture;
	public $city;
	public $ward;
	public $town;
	public $address;
	public $tel;
	public $mobile_tel;
	public $mobile_email;
	public $name;
	public $type;
	public $relay_server_id;
	
	//DBから取得してくる際のID用
	public $geographic_id;
	public $squared_distance;

	/* service */
	public $service;
	public $service_name;
	public $service_company_id;
	public $service_order;
	public $service_information;
	
	/* service_company */
	public $service_id;
	
	/* driver */
	public $experience;
	public $no_accident;
	public $car_type;
	public $equipment;
	public $sex;
	public $birthday;
	public $erea;
	public $message;
	public $last_name;
	public $first_name;
	public $furigana;
	public $driver_number_actual;
	public $drivers_number;
	public $invoice;
	
	/* driver_status */
	
	public $driver_id;
	public $status;
	public $detail;
	public $login_id;
	public $mobile_id;
	
	
	/*県名  */
	public $prefecture_name;
	public $prefectures_id;

	/*仮登録データベース*/
	public $link_pass;
	public $mail_confirmed;
	public $driver_last_name;
	public $driver_first_name;
	
	/* actions */
	public $action_1;
	public $action_2;
	public $action_3;
	public $action_4;
	public $action_5;
	public $track_always;
	public $accuracy;
	
	/* interval */
	public $distance;
	public $time;
	
	/* join_requests */
	public $registration_id;
	
	//invoices
	public $invoices_id;
	public $iphone_device_number;
	public $android_device_number;
	public $registration_driver;

	
/**
 * 政府ユーザーのためにJV一覧を作る　エジソン用
 */	
public static function getJvs(){
	try{
	
		$dbh=SingletonPDO::connect();
	
		$sql="	SELECT 
					company.id, company.company_name
				FROM 
					company
				LEFT JOIN
					company_rolls
				ON
					company_rolls.id = company.company_roll_id
				WHERE
					company_rolls.type = 'JV'					
				ORDER BY 
					company.updated 
				DESC ";	
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$newtopics=$stmt->fetchAll();
	
		return $newtopics;
	
	}catch(Exception $e){
		
		echo $e->getMessage();
	}
}

/**
 * 引数のユーザーIDが、政府のIDかどうか調べる
 */	
public static function isGovernmentUser($company_id){
	try{
	
		$dbh=SingletonPDO::connect();
	
		$sql="	SELECT 
					company_rolls.type
				FROM 
					company
				LEFT JOIN
					company_rolls
				ON
					company_rolls.id = company.company_roll_id
				WHERE
					company.id = $company_id					
				";	
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$data=$stmt->fetchAll();
		if(!empty($data[0]) && $data[0]['type'] == "GOVERNMENT"){
			$is_government = true;
		}else{
			$is_government = false;
		}

		return $is_government;
	
	}catch(Exception $e){
		
		echo $e->getMessage();
	}
}
	
	
	
//TOPページ用新着の取得メソッド
public static function getNewDataList(){
try{

	$dbh=SingletonPDO::connect();

	$sql="	SELECT 
				id, company_name
			FROM 
				company
			ORDER BY 
				updated 
			DESC LIMIT 10";	
	
	$stmt=$dbh->prepare($sql);
	$stmt->execute();
	$newtopics=$stmt->fetchAll();

	return $newtopics;

	}
	catch(Exception $e)
	{
	echo $e->getMessage();
		}
	}

//会社情報データ数の取得メソッド
public static function countDataList(){
try{

	$dbh=SingletonPDO::connect();

	$sql="SELECT COUNT(*) FROM company;";

	$stmt=$dbh->prepare($sql);
	$stmt->execute();

	$res=$stmt->fetchAll();

	return $res;

	}
	catch(Exception $e)
	{
	echo $e->getMessage();
		}
	}
	
	
/**
*管理画面でID単位で会社情報を削除するメソッド
*/
public static function deleteData($id, $from_web, $driver_id,$i){

try{
	
	$dbh=SingletonPDO::connect();
	
	//仮登録データか、本番データかでテーブルを分岐
		switch($from_web){
			case 1:

				$sql="DELETE company.*, geographic.*
					FROM company
					JOIN geographic
						ON geographic.company_id=company.id 
					WHERE company.id = :id 
				";
				
			break;	
	
	//		case 0:
			default:
				
				//以下company_idが影響しているテーブル
// 				$actions = "actions";
// 				$company = "company";
// 				$company_credentials = "company_credentials";
// 				$drivers = "drivers";
// 				$geographic = "geographic";
// 				$intervals = "intervals";
// 				$join_requests = "join_requests";
// 				$roots = "roots";
								
				//以下、driver_idが影響しているテーブル
// 				$alarms = "alarms";
// 				$comment = "comment";
// 				$day_report = "day_report";
// 				$driver_credentials = "driver_credentials";
// 				$driver_status = "driver_status";
// 				$last_driver_status = "last_driver_status";
// 				$messages = "messages";
// 				$target = "target";
// 				$work = "work";
				
				if($i==0){
					$sql="DELETE actions.*, company.*, company_credentials.*,
									geographic.*, intervals.*, join_requests.*, roots.*
						FROM company
						LEFT JOIN actions ON actions.company_id = $id
						LEFT JOIN company_credentials ON company_credentials.company_id=$id 
						LEFT JOIN geographic ON geographic.company_id=$id
						LEFT JOIN intervals ON intervals.company_id=$id
						LEFT JOIN join_requests ON join_requests.company_id=$id	
						LEFT JOIN roots ON roots.company_id=$id
						WHERE company.id = $id 
					";
				}
								
				$sql1="DELETE alarms.*, comment.*, day_report.*, drivers.*, driver_credentials.*,
				driver_status.*, last_driver_status.*, messages.*, target.*,work.*
				FROM drivers
				LEFT JOIN alarms ON alarms.driver_id=$driver_id
				LEFT JOIN comment ON comment.driver_id =$driver_id
				LEFT JOIN day_report ON day_report.driver_id=$driver_id
				LEFT JOIN driver_credentials ON driver_credentials.driver_id=$driver_id
				LEFT JOIN driver_status ON driver_status.driver_id=$driver_id
				LEFT JOIN last_driver_status ON last_driver_status.driver_id=$driver_id
				LEFT JOIN messages ON messages.driver_id=$driver_id
				LEFT JOIN target ON target.driver_id=$driver_id
				LEFT JOIN work ON work.driver_id=$driver_id
				WHERE drivers.id = $driver_id"
				;
		break;	
			
		}
		
		
		
		if($i==0){
			$res = $dbh->prepare($sql);
			$res->execute();
			$res->closeCursor();
		}
			
		$driver_delete = $dbh->prepare($sql1);
		$driver_delete->execute();
		$driver_delete->closeCursor();
		
	//$dbh->commit();

}
	catch(Exception $e){
	//$dbh->rollback();
	die($e->getMessage());
	}
} 		
	
//会社情報の登録
public static function putCompany($datas){
	$dbh=SingletonPDO::connect();
	$dbh->beginTransaction();
	
	$sql="INSERT INTO company 
	( is_company, company_name, business_hours_24, business_hours, car_number,
	 info, email, contact_person_last_name, contact_person_first_name, contact_tel, note,
	  url, passwd, is_ban_driver_editing, is_lite, created, updated)
	VALUES(:is_company, :company_name, :business_hours_24, :business_hours, :car_number,
	 :info, :email, :contact_person_last_name, contact_person_first_name , :contact_tel, note,
	  :url, :passwd, :is_ban_driver_editing, :is_lite, now(),now())";
	
	$stmt=$dbh->prepare($sql);
	
	$param = array(
		'service' => $service
		);
	
	$stmt->execute($param);
		
	$put_service=new self();
	$put_service->service =$service;

	$dbh->commit();
	
	return $put_service;
}

//グループ参加申請者登録
public static function putJoinRequest($datas){
	
	try{
	
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
		
		$sql="INSERT INTO join_requests
		( company_id, registration_id, ios_device_token, geographic_id, last_name, first_name, login_id, passwd, mobile_tel, mobile_email, created)
		VALUES(:company_id, :registration_id, :ios_device_token, :geographic_id, :last_name, :first_name, :login_id, :passwd, :mobile_tel, :mobile_email, now())";
		
		$stmt=$dbh->prepare($sql);
		
		$param = array(
			'company_id' => $datas['company_id'],
			'registration_id' => $datas['registration_id'],
			'ios_device_token' => $datas['ios_device_token'],
			'geographic_id' => $datas['geographic_id'],
			'last_name' => $datas['last_name'],
			'first_name' => $datas['first_name'],
			'login_id' => $datas['login_id'],
			'passwd' => $datas['passwd'],
			'mobile_tel' => $datas['mobile_tel'],
			'mobile_email' => $datas['mobile_email']
		);
		
		$stmt->execute($param);
	
		$dbh->commit();
		
		$status = "SUCCESS";
			
		return $status;
			
	  }catch(PDOException $e){

		echo $e->getMessage();
	
		$status = "DB_ERROR";
			
		return $status;
	}
}

//申請されたグループを検索
public static function getCompanyByGroupId($company_group_id){
	
	try{
		
		$dbh=SingletonPDO::connect();
		
		$sql = "SELECT 
					company.id AS company_id,
					drivers.id AS driver_id
				FROM
					company
				INNER JOIN
					drivers
					ON drivers.is_group_manager = 1
					AND drivers.company_id = company.id
				WHERE
					company_group_id = \"$company_group_id\"
				LIMIT 1
				";
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}
}


//申請者リスト
public static function getJoinRequestsByCompanyId($company_id) {
	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql = "SELECT 
					join_requests.id,
					join_requests.first_name,
					join_requests.last_name,
					join_requests.created,
					company.company_group_id
				FROM
					join_requests
				JOIN company
				ON company.id = join_requests.company_id
				WHERE
					join_requests.company_id = $company_id
				ORDER BY created DESC
				";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}
}

//　申請者情報を取得
public static function getJoinRequestByRequestId($join_request_id) {
	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql = "SELECT 
					join_requests.id,
					join_requests.first_name,
					join_requests.last_name,
					join_requests.created,
					join_requests.company_id,
					company.company_group_id
				FROM
					join_requests
				JOIN company
					ON company.id = join_requests.company_id
				WHERE
					join_requests.id = $join_request_id
				LIMIT 1
				";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}
}

//申請者IDを検索
public static function getJoinRequestId($registration_id, $ios_device_token, $company_id) {
	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql = "SELECT 
					id
				FROM
					join_requests
				WHERE
					company_id = $company_id
					AND
					(
						registration_id = '$registration_id'
						OR
						ios_device_token = '$ios_device_token'
					)
				ORDER BY created DESC
				LIMIT 1
				";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}
}

//申請者データを検索
public static function getJoinRequestDataId($id) {
	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql = "SELECT 
					company_id, registration_id, ios_device_token, geographic_id, last_name, first_name, login_id, passwd, mobile_tel, mobile_email
				FROM
					join_requests
				WHERE
					id = $id
				LIMIT 1
				";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}
}

//申請者データを検索
public static function deleteJoinRequestById($id) {
	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql="DELETE 
				FROM join_requests
				WHERE id = :id
			";
		$res = $dbh->prepare($sql);
	
		$param=array(
			'id'=>$id
		);
		
		$res->execute($param);
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}
}

//idを指定して、データ内容を取得　コンテンツ
public static function getById($id, $from_web){

	try{
		$dbh=SingletonPDO::connect();
		
		//仮登録データか、本番データかテーブルを分岐
		switch($from_web){
			
			case 1:

				$company="pre_company";
				$geographic="pre_geographic";
				$company_service="pre_company_service";
			
			break;	
	
			case 0:
				
				$company="company";
				$geographic="geographic";
				$company_service="company_service";				
			
			break;	
			
		}
		
		//会社の営業所の中で、一番若い番号の営業所が、本社の営業所
			$sql="
			SELECT 
				$company.*,
				prefectures.id as prefectures_id,
				prefectures.*,				
				$geographic.id as geographic_id,				
				$geographic.company_id as company_id,				
				$geographic.*
				
			FROM $company
			LEFT JOIN $geographic
			ON $company.id=$geographic.company_id
			LEFT JOIN prefectures
			ON prefectures.id=$geographic.prefecture
			WHERE $company.id = $id 
			ORDER BY geographic_id LIMIT 1
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

//ログイン
public static function login($id,$pass){
	try{

		    $dbh = SingletonPDO::connect();
			
		    // idとパスワードでユーザーテーブルを検索 　Webから登録した場合のみ、ログインできる。
	    	$sql = "SELECT 
	    				company.*, company_rolls.type as company_roll
	    			FROM 
	    				company
	    			JOIN 
	    				drivers
	    			ON 
	    				drivers.company_id = company.id
	    			LEFT JOIN
	    				company_rolls
	    			ON
	    				company.company_roll_id = company_rolls.id 
	            	WHERE
	            	(
		            		drivers.login_id = '$id'
		               	AND 
		               		drivers.passwd = MD5('$pass')
		               	AND 
		               		drivers.is_group_manager = 1
	               	)
	               	OR
	               	(
		               		company.email = '$id'
		               	AND 
		               		company.passwd = MD5('$pass')
	               	)
	               	";
	    		    	
	    	$res=$dbh->prepare($sql);
			$res->execute();
			
	    	$idArray = $res->fetchAll(PDO::FETCH_ASSOC);
    	
	    	return $idArray;
		
	}catch(Exception $e){
	
			echo $e->getMessage();

	}
}

/**
 * 自動ログイン用クレデンシャル生成
 * @param int $company_id 会社ID
 * @return string クレデンシャル
 */
public static function createCredential($company_id) {
	try{
	
		$credential = uniqid("", TRUE);
		$dbh = SingletonPDO::connect();
	
		// idでドライバーテーブルを検索
		$sql =<<< EOL
INSERT INTO company_credentials(credential, company_id, expires, created)
VALUES(:credential, :company_id, now() + INTERVAL 1 MONTH, now())
EOL;
			
		$res = $dbh->prepare($sql);
		$param = array(
				':credential' => $credential,
				':company_id' => $company_id,
		);
		
		$res->execute($param);
		setcookie('company_credential', $credential, time()+60*60*24*30, '/',  $_SERVER['SERVER_NAME'], FALSE, TRUE);
		
		return $credential;
	
	}catch(Exception $e){
	
	echo $e->getMessage();
	
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
		$sql =<<<EOL
SELECT company.*
FROM company
INNER JOIN company_credentials
ON company.id = company_credentials.company_id
WHERE credential = :credential
AND expires > now( )
LIMIT 1
EOL;
		$res=$dbh->prepare($sql);
		$param = array(':credential' => $credential);
		$res->execute($param);
			
		$idArray = $res->fetchAll(PDO::FETCH_ASSOC);

		return $idArray;

	}catch(Exception $e){

	echo $e->getMessage();

	}
}

/**
 * 自動ログイン用クレデンシャル削除
 */
public static function deleteCredential() {
	if (!isset($_COOKIE['company_credential'])) return;
	
	$credential = $_COOKIE['company_credential'];
	try{
	
		$dbh = SingletonPDO::connect();
	
		$sql =<<<EOL
DELETE FROM company_credentials
WHERE credential = :credential
EOL;
		$res=$dbh->prepare($sql);
		$param = array(':credential' => $credential);
		$res->execute($param);
		setcookie('company_credential', '', 1, '/',  $_SERVER['SERVER_NAME'], FALSE, TRUE);
	
		return;
	
	}catch(Exception $e){
	
		echo $e->getMessage();
	
	}
	
}

/**
 * 自動ログイン用クレデンシャル再生成
 * @param int $company_id 会社ID
 * @return string 再生成されたクレデンシャル
 */
public static function regenerateCredential($company_id){
	try{
		Data::deleteCredential();
		$new_credential = Data::createCredential($company_id);
		return $new_credential;

	}catch(Exception $e){

		echo $e->getMessage();

	}
}

//距離が近いデータ一覧の取得　第5引数はテーブル名

public static function getNearData($lat,$long,$distance,$refine_conditions,$table){
try{

	$dbh=SingletonPDO::connect();

	//1ページの最大表示件数

	$page_max=PAGE_MAX;
	$data_max=NEAR_DATA_NUMBER;

	//緯度、経度の上限と下限を計算
	$min_lat=$lat-$distance;
	$max_lat=$lat+$distance;

	$min_long=$long-$distance;
	$max_long=$long+$distance;
	
	if($refine_conditions){
		
		$refine_where='WHERE';
		
		if($refine_conditions['business_hours_24']){
			$refine[]='company.business_hours_24=1';
		}
		
		if($refine_conditions['company']){
			$refine[]='company.is_company=0';
		}
		
		if($refine_conditions['individual']){
			$refine[]='company.is_company=1';
		}
		
		if($refine_conditions['credit']){
			$refine[]='company.credit=1';
		}
		
		if($refine_conditions['debit']){
			$refine[]='company.debit=1';
		}
		
		if($refine_conditions['services']){
			
			//サービスについての絞り込み検索がある場合のみ、サービステーブルを連結
			$refine_service='join company_service
							on near.company_id = company_service.company_id ';
			$refine_service_ids=implode(',', $refine_conditions['services']);
			$refine[]="company_service.service_id in($refine_service_ids)";
	
		}
				
		$refine_sql=implode(' AND ', $refine);
	
	}

	if($table=='geographic'){
			
		//近くの営業所情報検索
		
		$sql="
		SELECT *
		FROM (	
			SELECT 
				* ,
				(sqrt(pow( (latitude - $lat) * 111, 2 ) + pow((longitude - $long) * 91, 2 ))) AS squared_distance
			FROM 		
				geographic
			WHERE
				latitude BETWEEN $min_lat AND $max_lat
				AND longitude BETWEEN $min_long AND $max_long
		) AS near
		join company
			on near.company_id = company.id
		$refine_service
		$refine_where $refine_sql  
		ORDER BY squared_distance
		LIMIT $data_max
		";

	}elseif($table=='driver_status'){
		
		//空車情報検索
		
	/*	$sql="
		SELECT 
			driver_id, last_name, first_name,
			drivers.mobile_tel,
			company.company_name,company.id AS company_id,
			geographic.tel,
			near.latitude,near.longitude,
			near.updated		
		FROM (	
			SELECT 
				* ,
				sqrt(pow( (latitude - $lat) * 111, 2 ) + pow((longitude - $long) * 91, 2 )) AS squared_distance
			FROM driver_status
				
			WHERE
				latitude BETWEEN $min_lat AND $max_lat
				AND longitude BETWEEN $min_long AND $max_long
				AND updated IN (SELECT MAX(updated) FROM driver_status GROUP BY driver_id)
				
			) AS near
		JOIN drivers
			on near.driver_id = drivers.id
		LEFT JOIN company
			on drivers.company_id = company.id
		LEFT JOIN geographic
			on drivers.geographic_id = geographic.id
		WHERE
			near.status=1
		 AND
		 	near.updated >=SUBTIME(NOW(),'".UPDATE_INTERVAL."')
		GROUP BY
			near.driver_id
		ORDER BY squared_distance
		LIMIT $data_max 		
		";		
*/
		//時間制限をしばらく外しておく
		$sql="
		SELECT 
			driver_id, last_name, first_name,
			drivers.mobile_tel,
			company.company_name,company.id AS company_id,
			geographic.tel,
			near.latitude,near.longitude,near.address,
			near.updated		
		FROM (	
			SELECT 
				* ,
				sqrt(pow( (latitude - $lat) * 111, 2 ) + pow((longitude - $long) * 91, 2 )) AS squared_distance
			FROM driver_status
				
			WHERE
				latitude BETWEEN $min_lat AND $max_lat
				AND longitude BETWEEN $min_long AND $max_long
				AND updated IN (SELECT MAX(updated) FROM driver_status GROUP BY driver_id)
				
			) AS near
		JOIN drivers
			on near.driver_id = drivers.id
		LEFT JOIN company
			on drivers.company_id = company.id
		LEFT JOIN geographic
			on drivers.geographic_id = geographic.id
		WHERE
			near.status=1
		GROUP BY
			near.driver_id
		ORDER BY squared_distance
		LIMIT $data_max 		
		";		
		
		
	}

	$stmt=$dbh->prepare($sql);
	$stmt->execute();

	$datas=$stmt->fetchAll();	
	return $datas;


	}
	catch(Exception $e)
	{
	echo $e->getMessage();
		}
	}

//データリストの取得

const ALL=0; //全トピック
const TOP=1; //新しいレスがついた順

//会社一覧の取得メソッド
public static function getDataList($status, $from_web){
try{

	$dbh=SingletonPDO::connect();
	$dbh->beginTransaction();
	//1ページの最大表示件数

	$page_max=PAGE_MAX;

	//仮登録データか、本番データかでテーブルを分岐
	switch($from_web){
		case 1:
		$company="pre_company";
		break;	

		case 0:
		$company="company";
		break;	
		
	}
	
	switch($status){
	//全記事参照
	case self::ALL:
	$condition="";
	$date="date";
	break;
	
	//TOPのみ
	case self::TOP:
	$condition="WHERE res_id=0";
	$date="last_update DESC, id ";
	break;

	
	default:
	$condition="";

	}

//	$data_max=DATA_MAX;

/*	$sql="SELECT * FROM $company
	$condition
	ORDER BY updated DESC LIMIT $data_max";
*/	
	$sql="SELECT $company.id, $company.company_name, $company.created, $company.updated, 
			drivers.company_id, drivers.id AS driver_id, COUNT(drivers.id) AS drivers_number,
			invoices.id AS invoices_id, invoices.iphone_device_number+invoices.android_device_number AS registration_driver,
			MAX(last_driver_status.created) AS last_driver_status_created
			FROM company
			LEFT JOIN drivers ON $company.id=drivers.company_id
			LEFT JOIN invoices ON $company.id=invoices.company_id
			LEFT JOIN last_driver_status ON drivers.id = last_driver_status.driver_id
			GROUP BY $company.id
			ORDER BY updated DESC LIMIT 200";
	
	$stmt=$dbh->prepare($sql);
	$stmt->execute();

//	while($res=$stmt->fetchObject(__CLASS__)){
	while($res=$stmt->fetchAll()){
	
	$dataList[]=$res;
		}		
	return $dataList;

	}
	catch(Exception $e)
	{
	echo $e->getMessage();
		}
	}	

//データ投入と編集　$statusの値でスイッチ
//引数は順番に、データ本体、新規か編集かのステータス、Webからかどうか
public static function putIn($datas, $status){
	
	try{
	
	$dbh=SingletonPDO::connect();
	$dbh->beginTransaction();
	
	switch($status){
		
	//データ編集
		case "EDIT":
			$company_id=$datas['id'];
			
			//パスワードは入力があったときのみアップデート
			if($datas['passwd']){
	
				$md5_passwd=MD5($datas['passwd']);	
	
			}else{
			//パスワード　編集時は、現在入っている値をそのまま入力
				$sql_passwd="SELECT passwd FROM company WHERE id=".$datas['id'];
				$stmt=$dbh->prepare($sql_passwd);
				$stmt->execute();	
						
				$res=$stmt->fetch();
				$exited_passwd=$res[0];
				$md5_passwd=$exited_passwd;
			}
				
			$sql1="
				UPDATE company SET
					is_company=:is_company,
					company_name=:company_name, 
					business_hours_24=:business_hours_24,
					business_hours=:business_hours,
					car_number=:car_number, 
					info=:info,
					email=:email, 
					contact_person_last_name=:contact_person_last_name,
					contact_person_first_name=:contact_person_first_name,
					contact_tel=:contact_tel,
					note=:note,
					url=:url,
					passwd=:passwd,
					is_ban_driver_editing=:is_ban_driver_editing,
					company_group_id = :company_group_id,
					updated=now()
				WHERE id=".$datas['id']	;		
	
			$sql2="
				UPDATE geographic SET
					company_id=:company_id, latitude=:latitude, longitude=:longitude, postal=:postal,
					prefecture=:prefecture, city=:city, ward=:ward, town=:town, address=:address, tel=:tel, 
					updated=now()		
				WHERE company_id=".$datas['id'].' ORDER BY id LIMIT 1';
			
			break;
			
		case "NEWDATA":
			//パスワードの暗号化
			$md5_passwd=MD5($datas['passwd']);
			$md5_driver_passwd=MD5($datas['driver_passwd']);
			
			$sql1="
				INSERT INTO company 
					(is_company, company_name, business_hours_24, business_hours, car_number,
					info, email, contact_person_last_name, contact_person_first_name, contact_tel,
					note, url, passwd, is_ban_driver_editing, company_group_id, company_roll_id,
					created, updated )
				VALUES
					( :is_company, :company_name, :business_hours_24, :business_hours, :car_number, 
					:info, :email, :contact_person_last_name, :contact_person_first_name, :contact_tel,
					:note, :url, :passwd, :is_ban_driver_editing, :company_group_id, :company_roll_id, NOW(), NOW())";

			//住所情報
			$sql2="
				INSERT INTO geographic SET
					company_id=:company_id, latitude=:latitude, longitude=:longitude, postal=:postal,
					prefecture=:prefecture, city=:city, ward=:ward, town=:town, address=:address, tel=:tel, 
					created = now(),
					updated=now()";
			//作業ステータス
			$sql3="
				INSERT INTO actions SET
					company_id = :company_id, action_1 = :action_1, action_2 = :action_2, action_3 = :action_3, action_4 = :action_4,
					created = now(), updated = now()";
			//位置情報取得する間隔
			$sql4="
				INSERT INTO intervals SET
					company_id = :company_id, distance = :distance, time = :time, created = now(), updated = now()";
				
			//ドライバー
			$sql5="
				INSERT INTO drivers SET
					company_id = :company_id , geographic_id = :geographic_id , last_name = :last_name,
					first_name = :first_name , mobile_tel = :mobile_tel , mobile_email = :mobile_email,
					login_id = :login_id , passwd = :passwd , registration_id = :registration_id ,
					is_group_manager = :is_group_manager , created = now(), updated = now()";
			
			//請求書
			$sql6="
				INSERT INTO invoices SET
					company_id = :company_id , iphone_device_number= :iphone_device_number , android_device_number = :android_device_number,
					remarks = :remarks , created = now()";
			
			break;

	}
	
	$stmt=$dbh->prepare($sql1);
	
		$param = array(
			'is_company' => $datas['is_company'],
			'company_name' => $datas['company_name'],
			'business_hours_24' => $datas['business_hours_24'],
			'business_hours' => $datas['business_hours'],
			'car_number' => $datas['car_number'],
			'info' => $datas['info'],
			'email' => $datas['email'],
			'contact_person_last_name' => $datas['contact_person_last_name'],
			'contact_person_first_name' => $datas['contact_person_first_name'],
			'contact_tel' => $datas['contact_tel'],
			'note' => $datas['note'],
			'url' => $datas['url'],
			'passwd' =>$md5_passwd,
			'is_ban_driver_editing' => $datas['is_ban_driver_editing'],
			'company_group_id' => $datas['company_group_id'],
			'company_roll_id' => 2
		);
		
	$stmt->execute($param);
	
	//geographiｃテーブルへの、company_id取得
	switch($status){
	//新規データ登録
		case "NEWDATA":

		//company_idを取得
		$sql="SELECT MAX(id) FROM company";		
		$stmt=$dbh->query($sql);
	
		$res=$stmt->fetch();
		$max_company_id=$res[0];
		$company_id=$max_company_id;

		break;
		
		case "EDIT":
		$company_id=$datas['id'];
		break;
			
	}	

	//geographicテーブルにデータを投入
	$stmt=$dbh->prepare($sql2);
	
		$param = array(
		'company_id' => $company_id,
		'latitude' => $datas['latitude'],
		'longitude' => $datas['longitude'],
		'postal' => $datas['postal'],
		'prefecture' => $datas['prefecture'],
		'city' => $datas['city'],
		'ward' => $datas['ward'],
		'town' => $datas['town'],
		'address' => $datas['address'],
		'tel' => $datas['tel'],
		
		);
		
	$stmt->execute($param);
	
	if ($status == "NEWDATA") {
		//作業ステータスのデフォルトを保存
		$stmt=$dbh->prepare($sql3);
		$param = array(
			'company_id' => $company_id,
			'action_1' => ACTION_1,
			'action_2' => ACTION_2,
			'action_3' => ACTION_3,
			'action_4' => ACTION_4,
		);
		$stmt->execute($param);
		
		//位置情報取得の間隔のデフォルトを保存
		$stmt=$dbh->prepare($sql4);
		$param = array(
			'company_id' => $company_id,
			'distance' => DEFAULT_DISTANCE_INTERVAL,
			'time' => DEFAULT_TIME_INTERVAL
		);
		$stmt->execute($param);

		//driversテーブルにデータを投入
		$stmt=$dbh->prepare($sql5);
		$param = array(
			'company_id' => $company_id,
			'geographic_id' => $datas['geographic_id'],
			'last_name' => $datas['contact_person_last_name'],
			'first_name' => $datas['contact_person_first_name'],
			'mobile_tel' => $datas['contact_tel'],
			'mobile_email' => $datas['email'],
			'login_id' => $datas['login_id'],
			'passwd' => $md5_driver_passwd,
			'registration_id' => $datas['registration_id'],
			'is_group_manager' => $datas['is_group_manager'],

		);
		$stmt->execute($param);
	
		if($datas['invoice']==1){
			//invoicesテーブルにデータを投入
			$stmt=$dbh->prepare($sql6);
			$param = array(
				'company_id' => $company_id,
				'iphone_device_number' => $datas['iphone_device_number'],
				'android_device_number' => $datas['android_device_number'],
				'remarks' => $datas['remarks']
			);
			$stmt->execute($param);
		}
	}
	
	$dbh->commit();
	
  }catch(PDOException $e){
  	
		$dbh->rollback();
		echo $e->getMessage();
	
 }
}	

public static function getCompanyGroupId($company_id) {
	
	try{
		$dbh=SingletonPDO::connect();
		$sql="
			SELECT 
				company_group_id
			FROM company
			WHERE id = $company_id
			LIMIT 1
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

public static function getCompanyLoginId($company_id) {
	
	try{
		$dbh=SingletonPDO::connect();
		$sql="
			SELECT 
				email
			FROM company
			WHERE id = $company_id
			LIMIT 1
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

// ステータス
public static function putAppStatusIn($datas){
	
	try{
		
		/**
		 * ステータス
		 * Enter description here ...
		 * @var unknown_type
		 */
		$dbh=SingletonPDO::connect();
		$sql="SELECT id FROM actions WHERE company_id = ".$datas['company_id']." LIMIT 1";	
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		while($res=$stmt->fetchObject(__CLASS__)){
			$dataList[]=$res;
		}
		
		if (count($dataList)) {
			//作業ステータス
			$sql3="
			UPDATE actions SET
				company_id = :company_id, action_1 = :action_1, action_2 = :action_2, action_3 = :action_3, action_4 = :action_4,
				track_always = :track_always, accuracy = :accuracy, created = now(), updated = now() WHERE company_id=".$datas['company_id'];
		} else {
			//作業ステータス
			$sql3="
			INSERT INTO actions SET
				company_id = :company_id, action_1 = :action_1, action_2 = :action_2, action_3 = :action_3, action_4 = :action_4,
				track_always = :track_always, accuracy = :accuracy, created = now(), updated = now()";
		}
		
		/** ************************
		 * 位置情報取得の間隔
		 * ************************/
		$sql="SELECT id FROM intervals WHERE company_id = ".$datas['company_id']." LIMIT 1";	
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		while($res=$stmt->fetchObject(__CLASS__)){
			$dataList[]=$res;
		}
		
		if (count($dataList)) {
			//位置情報取得する間隔
			$sql4="
			UPDATE intervals SET
				company_id = :company_id, distance = :distance, time = :time, created = now(), updated = now() WHERE company_id=".$datas['company_id'];
		} else {
			$sql4="
			INSERT INTO intervals SET
				company_id = :company_id, distance = :distance, time = :time, created = now(), updated = now()";
		}
		
		//作業ステータスのデフォルトを保存
		$stmt=$dbh->prepare($sql3);
		$param = array(
			'company_id' => $datas['company_id'],
			'action_1' => $datas['action_1'],
			'action_2' => $datas['action_2'],
			'action_3' => $datas['action_3'],
			'action_4' => $datas['action_4'],
			'track_always' => $datas['track_always'],
			'accuracy' => $datas['accuracy'],
		);
		$stmt->execute($param);
		
		//位置情報取得の間隔のデフォルトを保存
		$stmt=$dbh->prepare($sql4);
		$param = array(
			'company_id' => $datas['company_id'],
			'distance' => $datas['distance'],
			'time' => $datas['time']
		);
		$stmt->execute($param);
		
		/** ************************
		*	リアルタイムマップの一般公開と閲覧ユーザーへの公開設定
		 ***************************/
		CompanyOptions::setCompanyOption($datas['company_id'], $datas['is_public'], $datas['is_users_viewing']);
			
		/** ************************
		*	公開するドライバーの設定
		 ***************************/
		DriverOptions::setDriverIntoPublic( $datas['company_id'], $datas['visible_driver_id'] );
		
		/** ************************
		*	閲覧ユーザー毎に公開するドライバーの設定
		 ***************************/
		Users::setDriversUsers( $datas['company_id'], $datas['viewed_driver_id'] );

		$dbh->commit();
		
	}catch(PDOException $e){
		echo $e->getMessage();
		exit;
	}
	
}

//会社名一覧取得
public static function getCompanies(){
try{

	$dbh=SingletonPDO::connect();
	
	$sql="SELECT * FROM company";
	
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

//会社名取得 　IDから会社名を取得
public static function getCompanyName($id){
try{

	$dbh=SingletonPDO::connect();

	$sql="SELECT company_name FROM company WHERE id=$id";

	$stmt=$dbh->prepare($sql);
	$stmt->execute();

//	while($res=$stmt->fetchObject(__CLASS__)){
	$res=$stmt->fetch(PDO::FETCH_ASSOC);
	
	$company_name=$res;
			
	return $company_name;
	
	}
	catch(Exception $e)
	{
	echo $e->getMessage();
		}
	}
	
//会社がドライバーに編集許可のステータスを取得
public static function isBannedDriverEditing($id, $u_id){
try{

	//会社ログインであったら無条件に編集可能状態
	if($u_id == $id){
		
		return 0;
		
	}
	
	$dbh=SingletonPDO::connect();

	$sql="SELECT is_ban_driver_editing FROM company WHERE id=$id";

	$stmt=$dbh->prepare($sql);
	$stmt->execute();

//	while($res=$stmt->fetchObject(__CLASS__)){
	$res=$stmt->fetch(PDO::FETCH_ASSOC);
	
	$banDriverEditing=$res;
			
	return $banDriverEditing['is_ban_driver_editing'];
	
	}
	catch(Exception $e)
	{
	echo $e->getMessage();
		}
	}

//ステータスと位置情報取得の間隔を取得
public static function getStatusALL($company_id){

	try{
		$dbh=SingletonPDO::connect();
		
		$sql="
			SELECT 
				action_1,action_2,action_3,action_4
			FROM actions
			WHERE actions.company_id = $company_id
			LIMIT 1
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

//ステータスと位置情報取得の間隔を取得
public static function getStatusAndInterval($company_id){

	try{
		$dbh=SingletonPDO::connect();
		
		$sql="
			SELECT 
				action_1, action_2, action_3, action_4,
				distance, time, track_always, accuracy
			FROM actions, intervals 
			WHERE actions.company_id = $company_id AND intervals.company_id = $company_id
			LIMIT 1
		";
		
		"
		SELECT
		action_1, action_2, action_3, action_4,
		distance, time, track_always, accuracy
		FROM actions, intervals
		WHERE actions.company_id = $company_id AND intervals.company_id = $company_id
		LIMIT 1
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

//ステータスを取得
public static function getStatusByCompanyId($company_id){

	try{
		$dbh=SingletonPDO::connect();
		
		$data = null;
		$sql="
			SELECT 
				action_1, action_2, action_3, action_4, action_5
			FROM actions
			WHERE company_id = $company_id
			LIMIT 1
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
	
//県名一覧取得
public static function getPrefectures(){
try{

	$dbh=SingletonPDO::connect();
	$sql="SELECT * FROM prefectures";
	
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

//特定の県名取得
public static function getPrefecturesName($id){
try{

	$dbh=SingletonPDO::connect();
	$sql="SELECT * FROM prefectures WHERE id=$id";
	
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
	

//サービスリスト取得
public static function getService(){
try{

	$dbh=SingletonPDO::connect();
//	$sql="SELECT * FROM service";
	$sql="SELECT * FROM service order by service_order";
	
	$stmt=$dbh->prepare($sql);
	$stmt->execute();
	while($res=$stmt->fetchObject(__CLASS__)){
	$serviceList[]=$res;
	}
	return $serviceList;
	
	}
	catch(Exception $e)
	{
	echo $e->getMessage();
		}
	}
	
//サービスリスト取得 　複数の引数から、サービス名を取得
public static function getServiceName($ids){
try{

	$dbh=SingletonPDO::connect();
	
	foreach($ids as $id){
		$sql="SELECT * FROM service WHERE id=$id";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
			while($res=$stmt->fetchAll()){
			$serviceList[]=$res;
			}		
	}
	return $serviceList;
	}
	catch(Exception $e)
	{
	echo $e->getMessage();
		}
	}

//サービス名取得 　ひとつの引数から、複数のサービス名を取得
public static function getServiceNameEach($id){
try{

	$dbh=SingletonPDO::connect();
	
	$sql="SELECT service_name FROM service WHERE id=$id";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
			while($res=$stmt->fetch(PDO::FETCH_ASSOC)){
			$serviceList[]=$res;
			}		
	
	return $serviceList;
	}
	catch(Exception $e)
	{
	echo $e->getMessage();
		}
	}	
	
//会社別のサービスリスト取得
public static function getServiceForCompany($id){
try{

	$dbh=SingletonPDO::connect();
	

		$sql="SELECT service_id FROM company_service WHERE company_id=$id";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
			while($res=$stmt->fetch(PDO::FETCH_ASSOC)){
			$serviceList[]=$res;
			}
	
	return $serviceList;
	}
	catch(Exception $e)
	{
	echo $e->getMessage();
		}
	}
	
	
//サービスリスト追加
public static function putService($service){
	$dbh=SingletonPDO::connect();
	$dbh->beginTransaction();
	
	$sql="INSERT INTO service 
	(service_name, created, updated)
	VALUES(:service, now(),now())";
	
	$stmt=$dbh->prepare($sql);
	
	$param = array(
		'service' => $service
		);
	
	$stmt->execute($param);
		
	$put_service=new self();
	$put_service->service =$service;

	$dbh->commit();
	
	return $put_service;
	}
	
	//会社名一覧取得
	public static function getCompany_admin($id){
		try{
	
			$dbh=SingletonPDO::connect();
	
			$sql="SELECT company.id, company.is_company, company.company_name, company.email, company.contact_person_last_name,
			company.contact_person_first_name, company.contact_tel, company.company_group_id,
			drivers.company_id, drivers.mobile_tel, 
			invoices.iphone_device_number, invoices.android_device_number, invoices.remarks,
			geographic.latitude, geographic.longitude, geographic.postal, geographic.prefecture, geographic.city,
			geographic.ward, geographic.town, geographic.address, prefectures.id, prefectures.prefecture_name
			FROM company
			LEFT JOIN drivers ON company.id=drivers.company_id
			LEFT JOIN invoices ON company.id=invoices.company_id
			JOIN geographic ON company.id=geographic.company_id
			JOIN prefectures ON prefectures.id=geographic.prefecture
			WHERE company.id=$id
			GROUP BY company.id";
			
			//$sql="SELECT * FROM company WHERE id=$id";
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			
			$dataList = $stmt->fetch();
			
			return $dataList;
			
			}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}	

	
//管理画面からデータ削除のためのdriver_id取得
public static function select_driver_id($id){
	
	$dbh=SingletonPDO::connect();
	
	$sql="SELECT drivers.id FROM drivers WHERE drivers.company_id=$id";
	
	$stmt=$dbh->prepare($sql);
	$stmt->execute();
	
	$select_driver_id=$stmt->fetchAll();
	
	return $select_driver_id;
	
}

public static function getInvoices(){
	
	$dbh=SingletonPDO::connect();
	
	$sql="SELECT 
				*, 
				COUNT(drivers.id) AS drivers_number, 
				MAX(last_driver_status.created) AS last_driver_status_created 
			FROM company 
			JOIN 
				invoices ON invoices.company_id = company.id
			LEFT JOIN 
				drivers ON drivers.company_id = company.id
			LEFT JOIN
				last_driver_status ON last_driver_status.driver_id = drivers.id
			GROUP BY invoices.id
			ORDER BY invoices.created DESC LIMIT 30";
	
	$stmt=$dbh->prepare($sql);

	$stmt->execute();
	
	$invoices_data=$stmt->fetchAll();
	
	return $invoices_data;
}

//検索用の請求書情報取得
public static function searchInvoices($id){

	$dbh=SingletonPDO::connect();

	$sql="SELECT iphone_device_number, android_device_number, remarks 
			FROM invoices
			WHERE invoices.company_id=$id";

	$stmt=$dbh->prepare($sql);

	$stmt->execute();

	$invoices_data=$stmt->fetchAll();

	return $invoices_data;
}



//複数ページ
public static function make_page_link($topicList){
try{
$page_max=PAGE_MAX;

require_once 'Pager.php';
$params = array(
    'mode'       => 'Jumping',
    'perPage'    => $page_max,
    'delta'      => 10,
    'itemData'   => $topicList);
$pager = & Pager::factory($params);
$data  = $pager->getPageData();
$links = $pager->getLinks();

return array($data, $links);

}catch(Exception $e)
	{
	echo $e->getMessage();
		}
	
}

//禁止リストにあるIPと重複してないか

public static function banip(){
	$dbh=SingletonPDO::connect();
	
	$sql="SELECT ip FROM ban_ip";
	
	$stmt=$dbh->prepare($sql);
	$stmt->execute();

	$res=$stmt->fetch();
	$u_ip=$res[0];

	return $u_ip;
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
	
//クラスDataの終了
}
?>