<?php
/*
 * Rootクラス
 * 配送先の指定など
 * @author Akiko Goto
 * ver2.6から
 * 
 */

class Message{
	
	/* message */
	public $id;
	public $driver_id;
	public $drive_date;
	public $updated;
	public $created;
	
	public $is_message;
	public $action_1;
	public $action_2;
	public $action_3;
	public $action_4;
		
//メッセージの送信

public static function SendMessage($datas, $driver_ids){
	
	try{
		$dbh=SingletonPDO::connect();		
		
		$drivers = implode(',', $driver_ids);

			$sql="
			SELECT 
				registration_id,
				ios_device_token
			FROM
				drivers
			WHERE
				id 
			IN 
				($drivers)";

		
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$notification_ids = $stmt->fetchAll();

			/********************************
			 * DBから取得したregistration_id、ios_device_tokenをそれぞれの配列に格納する
			 *******************************/
			$regIds = Array();
			$iOSDeviceTokens = Array();
			foreach($notification_ids as $notification_id){
				// registration id for Android
				$string_regId = $notification_id['registration_id'];
				if ($string_regId != null) {
					array_push($regIds, $string_regId);
				}
				// ios_device_token for iOS
				$string_iod_device_token = $notification_id['ios_device_token'];
				if ($string_iod_device_token != null) {
					array_push($iOSDeviceTokens, $string_iod_device_token);
				}
			}
			
			//スレッドIDが来ていれば、返信、スレッドIDがなければ新規スレッド		
			if(empty($datas['thread_id'])){
				//スレッドIDを取得
				
				$sql="SELECT
						thread_id
					  FROM
						messages
					  ORDER BY 
						thread_id
					  DESC
					  LIMIT 
						1;
						";
							
				$stmt=$dbh->prepare($sql);
				$stmt->execute();
				$id = $stmt->fetch();
	
				$thread_id = $id[0]+1;
				$thread_parent = 1;
				
			}else{
				
				$thread_id = $datas['thread_id'];
				$thread_parent = 0;
			}

			
			$sql="
			INSERT 
			INTO
				messages
				(driver_id, thread_id, thread_parent, gcm_message, address, message_latitude, 
					message_longitude, has_read_admin, sender, sender_id, created)		
			VALUES
				(:driver_id, :thread_id, :thread_parent, :gcm_message, :address, :message_latitude, 
					:message_longitude, :has_read_admin, :sender, :sender_id, now())";
			
			
			$stmt=$dbh->prepare($sql);
			
			$param = array(
				'driver_id' => $drivers,
				'thread_id' => $thread_id,
				'thread_parent' => $thread_parent,
				'gcm_message' => $datas['gcm_message'],
				'address' => $datas['address'],
				'message_latitude' => $datas['message_latitude'],
				'message_longitude' => $datas['message_longitude'],
				'has_read_admin' => 1,
				'sender' => $datas['sender'],
				'sender_id' => $datas['sender_id']			
			);
			
			$stmt->execute($param);			
			
			//メッセージIDを取得
			
			$sql="SELECT
					LAST_INSERT_ID();
					";
						
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$id = $stmt->fetch();
			$message_id = $id[0];
			
			
			//メッセージ受信者とメッセージのテーブルにデータを入れる
		
			foreach ($driver_ids as $each_driver_id){
				$sql="
				INSERT 
				INTO
					messages_receivers
					(message_id, receiver_id, is_receiver_admin, admin_company_id, created)		
				VALUES
					(:message_id, :receiver_id, :is_receiver_admin, :admin_company_id, now())";			
				
				$stmt=$dbh->prepare($sql);
			
				if($datas['sender_id']==0){

					//管理者の場合
					$param = array(
						'message_id' => $message_id,
						'receiver_id' => $each_driver_id,
						'is_receiver_admin' => 0,
						'admin_company_id' => $datas['company_id']			
					);

				}else{
					//営業所長の場合
					$param = array(
						'message_id' => $message_id,
						'receiver_id' => $each_driver_id,
						'is_receiver_admin' => 0,
						'admin_company_id' => 0			
					);
					
				}
				
				$stmt->execute($param);
			}

			//営業所長の場合、自分が送ったメッセージは既読フラグをつける
			if($datas['sender_id'] != 0){
				
				$sender_id = $datas['sender_id'];
				
				$sql="
					UPDATE 
						messages_receivers
					SET
						has_read = 1
					WHERE		
						message_id = $message_id
					AND
						receiver_id = $sender_id";			
					
				$stmt=$dbh->prepare($sql);				
				$stmt->execute($param);
			}
			
			
			
			//スマホに送信される住所のマイナスなどが&minusになるのを防ぐ
			$datas['address'] = html_entity_decode($datas['address'], ENT_QUOTES, mb_internal_encoding());
			$datas['message'] = html_entity_decode($datas['message'], ENT_QUOTES, mb_internal_encoding());
			/********************************
			 * Push Notification For Android 
			 *******************************/
			$android_notification_error = null;
			$isPushedToAndroid = true;
			try {
				list($response_status, $response_result) = sendNotification( 
					$regIds, 
					array( 
						'status' => "MESSAGE",
						'message_id' => $message_id,
						'message' => $datas['gcm_message'],
						'address'=>$datas['address'],
						'latitude'=>$datas['message_latitude'],
						'longitude'=>$datas['message_longitude'])
				);
				$decoded_result = json_decode($response_status);
			} catch (Exception $e) {
				$android_notification_error = "ANDROID ERROR :: ".$e;
				$isPushedToAndroid = false;
			}
			//送信失敗したか、エラーがあるかチェック
			if ($isPushedToAndroid && $decoded_result->success == 0) {
				$isPushedToAndroid = false;
			}
			
			/********************************
			 * Push Notification For iOS
			 *******************************/
			$ios_notification_error = null;
			$iosPushNotificationStatus = true;
		    $pushDatas = array( 'status' => 'MESSAGE', 'message_id' => $message_id);
		    try {
			    foreach ( $iOSDeviceTokens as $device_token ) {
			    	if ($device_token != null) {
						PushNotification::iosPushNotification( $pushDatas, $is_user, "新着メッセージがあります", $device_token );
			    	}
			    }
		    }catch (Exception $e){
		    	$ios_notification_error = "iOS ERROR :: ".$e;
		    	$iosPushNotificationStatus = false;
		    }
			
			//メッセージ送信が成功したかどうか
			if($isPushedToAndroid || $iosPushNotificationStatus){
				
				$status = "SUCCESS";
				$response_status = "";
				
			}else{
				
				$status = "ERROR";
				$error_sanitized = htmlentities(strip_tags($response_status),ENT_QUOTES, mb_internal_encoding());

			}
			
			//ステータス,エラーインフォをDBに登録する
			if($error_sanitized){
				$sql2="
					UPDATE 
						messages
					SET
						status = \"$status\",
						error_info = \"$error_sanitized\"
					WHERE
						id = $message_id";
			}else{
				$sql2="
					UPDATE 
						messages
					SET
						status = \"$status\"
					WHERE
						id = $message_id";
				
			}

			$stmt=$dbh->prepare($sql2);
			$stmt->execute();
						
			
			return array($status, $response_status);
			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

/**
 * 既読か既読でないかを取得
 * @param int $driver_id
 * @param int $message_id
 */

public static function getHasReadMessage($driver_id, $message_id){
	
	try{
			$dbh=SingletonPDO::connect();		
					
			$sql="
					SELECT
						has_read
					FROM
						messages_receivers 
					WHERE
						message_id = $message_id
					AND
						receiver_id = $driver_id
					";
						
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			
			$datas = $stmt->fetch();

			return $datas['has_read'];			
			

		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}


//メッセージの送信履歴

public static function getMessages($company_id){
	
	try{
		$dbh=SingletonPDO::connect();		

		
		$sql="
			SELECT 
				messages.gcm_message, messages.created, messages.status, 
				messages.address, messages.error_info,  messages.sender,
				messages.driver_id,	messages.thread_id
			FROM
				messages
			JOIN
				drivers
				ON
				messages.driver_id = drivers.id
			JOIN
				company
				ON
				drivers.company_id = company.id
			WHERE
				company.id = $company_id
			AND
				messages.thread_parent = 1	

			ORDER BY
				thread_id DESC,
				messages.created DESC 
				";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas = $stmt->fetchAll();

			return $datas;			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

//メッセージの送信履歴　ドライバー用
public static function getMessagesDriver($driver_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
		//まず、スレッドIDとドライバーIDのみ取得
		$sql="
			SELECT 
				created, driver_id,	thread_id
			FROM
				messages
			GROUP BY
				thread_id
				";
		
		
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas = $stmt->fetchAll();	

			//複数ドライバーIDがある際のために、ドライバーIDをばらしてから検索
			$thread_array = array();
			foreach($datas as $each_data){
				$each_driver_id_exploded = explode(',', $each_data['driver_id']);
				$thread_array[$each_data['thread_id']] = $each_driver_id_exploded;
			}


			//該当のドライバーが宛先に含まれるスレッド番号を検索　つまり、スレッドの返信で自分がしていない返信も閲覧ができる仕様
			$thread_key = array();
			foreach($thread_array as $key => $each_thread){
				
				foreach($each_thread as $each_driver){
					if($each_driver == $driver_id){
						$thread_key[] = $key;
					}
				}
			}


			$thread_ids = implode(',',$thread_key);			
			
			if(!empty($thread_ids)){
				$sql="
					SELECT 
						messages.id, messages.gcm_message, messages.address, 
						messages.message_latitude, messages.message_longitude, messages.thread_id, messages.created,
						GROUP_CONCAT(messages_receivers.has_read) as has_read_array
					FROM
						messages
					LEFT JOIN
						messages_receivers
					ON
						messages.id = messages_receivers.message_id
					WHERE
						thread_id IN ($thread_ids)
					
					GROUP BY
						thread_id
					ORDER BY
						created DESC
					LIMIT
						40
					";

				$stmt=$dbh->prepare($sql);
				$stmt->execute();
				$messages = $stmt->fetchAll();
			}

			return $messages;			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

/**
 * スレッドIDを指定して、メッセージIDを取得
 * @param int $thread_id
 */
public static function getMessagesByThreadId($thread_id) {
	
	try {
		$dbh=SingletonPDO::connect();
		$sql="
			SELECT
				*				
			FROM
				messages
			WHERE
				thread_id = $thread_id
			ORDER BY
				created
				";
	
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas = $stmt->fetchAll();									
			
			return $datas;			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
	
}


// メッセージIDでメッセージを取得
public static function getMessageByMessageId($driver_id, $message_id) {
	
	try {
		$dbh=SingletonPDO::connect();
		$sql="
			SELECT
				id, 
				gcm_message,
				address,
				message_latitude,
				message_longitude,
				status, 
				has_read_admin,
				sender
			FROM
				messages
			WHERE
				(
					driver_id = $driver_id
					OR
					driver_id LIKE '$driver_id,%'
					OR
					driver_id LIKE '%,$driver_id'
					OR
					driver_id LIKE '%,$driver_id,%'
				)
			AND
				id = $message_id
			LIMIT 1
			";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas = $stmt->fetchAll();
		
		return $datas;
		
	}catch(Exception $e){
		echo $e->getMessage();
	}
	
}

//スレッドごとにメッセージを取得
public static function getMessagesByThread($company_id, $thread_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
		$sql="
			SELECT
				messages.id, 
				messages.gcm_message, messages.created, messages.status, 
				messages.address, messages.error_info,messages.sender,
				messages.thread_id, messages.thread_parent, 
				messages.driver_id, messages.sender_id,
				drivers.last_name, drivers.first_name				
			FROM
				messages
			JOIN
				drivers
				ON
				messages.driver_id = drivers.id				
			JOIN
				company
				ON
				drivers.company_id = company.id
			WHERE
				company.id = $company_id
			AND
				messages.thread_id = $thread_id
			ORDER BY
				messages.created
				";

		
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas = $stmt->fetchAll();
			
		//メッセージの既読について調べる	
		$sql="
			SELECT
				*
			FROM
				messages_receivers
			JOIN
				messages
				ON
				messages.id = messages_receivers.message_id				
			WHERE
				messages.thread_id = $thread_id
			ORDER BY
				messages.created
				";

		
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$has_read_datas = $stmt->fetchAll();
				
			
			//そのスレッドのメッセージを全部既読にする
			$sql="
					UPDATE 
						messages
					JOIN
						drivers
					ON
						messages.driver_id = drivers.id
					JOIN
						company
					ON
						drivers.company_id = company.id

					SET
						messages.has_read_admin = 1
					WHERE
						company.id = $company_id
					AND
						messages.thread_id = $thread_id";
							
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
						
			
			return $datas;			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

/**
 * 受信者ごとにメッセージをスレッド単位で取得
 */
public static function getThreadsByReceiver($driver_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
		$sql="
			SELECT
				messages.id, 
				messages.gcm_message, messages.created, messages.status, 
				messages.address, messages.error_info, messages.sender,
				messages.thread_id, messages.thread_parent, 
				messages.driver_id, 
				drivers.last_name, drivers.first_name,
				messages_receivers.*				
			FROM
				messages
			JOIN
				messages_receivers
			ON
				messages_receivers.message_id = messages.id
			JOIN
				drivers
			ON
				messages_receivers.receiver_id = drivers.id
			WHERE
				messages_receivers.receiver_id = $driver_id
			OR
				messages.sender_id = $driver_id
			GROUP BY 
				messages.thread_id
			ORDER BY
				messages.updated DESC
				
				";
		
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas = $stmt->fetchAll();
			
			return $datas;			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

/**
 * あるドライバーが、そのメッセージの受信者かどうか
 * @param int $message_id
 * @param int $driver_id
 */
public static function isReceiver($message_id, $driver_id){
	try{
		$dbh=SingletonPDO::connect();		
		
		$sql="
			SELECT
				*
			FROM
				messages_receivers
			WHERE
				receiver_id = $driver_id
			AND
				message_id = $message_id
				";

		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas = $stmt->fetchAll();

		$is_receiver = false;
		
		if(!empty($datas)){
			$is_receiver = true;
		}			
		
		return $is_receiver;			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

//スレッドごとにメッセージを取得　ドライバー用
public static function getMessagesByThreadDriver($driver_id, $thread_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
		$sql="
			SELECT 
				messages.gcm_message, messages.created, messages.status, 
				messages.address, messages.error_info, messages.sender,
				messages.thread_id, messages.thread_parent		
			FROM
				messages
			WHERE				
				messages.thread_id = $thread_id
			ORDER BY
				messages.created
				";

		
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas = $stmt->fetchAll();
			
			//そのスレッドのメッセージを全部既読にする
			$messages= Message::getMessagesByThreadId($thread_id);
		
			foreach ($messages as $each_message){
				
				Message::hasReadMessage($driver_id, $each_message['id']);
			
			}

			return $datas;			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}


//管理者が読んでいない投稿があるかどうか　管理者の既読フラグがないメッセージを確認
public static function getMessagesNotAdminRead($company_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
		$sql="
			SELECT 
				messages.thread_id, messages.gcm_message
			FROM
				messages
			JOIN
				drivers
				ON
				messages.driver_id = drivers.id
			JOIN
				company
				ON
				drivers.company_id = company.id
			WHERE
				company.id = $company_id
			AND
				messages.has_read_admin = 0
			GROUP BY 
				messages.thread_id
				";

		
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas = $stmt->fetchAll();

			return $datas;			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

public static function getMessagesNotBranchManagerRead($branch_manager_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
		$sql="
			SELECT 
				messages.thread_id, messages.gcm_message
			FROM
				messages
			JOIN
				drivers
				ON
				messages.driver_id = drivers.id
			JOIN
				company
			ON
				drivers.company_id = company.id
			JOIN
				messages_receivers
			ON
				messages_receivers.message_id = messages.id				
			WHERE
				messages_receivers.receiver_id = $branch_manager_id
			AND
				messages_receivers.has_read = 0
			GROUP BY 
				messages.thread_id
				";

		
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas = $stmt->fetchAll();

			return $datas;			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}


//メッセージの返信として、了解をスマホから受信
public static function Roger($datas, $driver_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
			//スレッドIDを取得
			
			$sql="SELECT
					thread_id
				  FROM
				  	messages
				  WHERE
				  	id = :id
					;
					";
						
			$stmt=$dbh->prepare($sql);
			
			$param = array(
				'id' => $datas['message_id']
			);
			
			$stmt->execute($param);
			$id = $stmt->fetch();
			$thread_id = $id[0];
		
			
			//ドライバーの氏名を取得
			$driver_name_array = Driver::getNameById($driver_id);

			$last_name = $driver_name_array[0]['last_name'];
			$first_name = $driver_name_array[0]['first_name'];
			$driver_name = $last_name.$first_name;
			
			$sql="
			INSERT 
			INTO
				messages
				(thread_id, driver_id, gcm_message, sender, created)		
			VALUES
				(:thread_id, :driver_id, :gcm_message, :sender, now())";
			
			
			$stmt=$dbh->prepare($sql);

			$param = array(
				'thread_id' => $thread_id,
				'driver_id' => $driver_id,
				'gcm_message' => ROGER,
				'sender' => $driver_name
			);
			
			$stmt->execute($param);			

			$sql="SELECT
					LAST_INSERT_ID();
					";
						
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$id = $stmt->fetch();
			$message_id = $id[0];

			//メッセージの最初のスレッドの送信者が、管理者か営業所長かを調べる
			$thread_info = Message::getMessagesByThreadId($thread_id);

			$receiver_id = $thread_info[0]['sender_id'];

			if($thread_info[0]['sender_id']=="0"){
				$is_receiver_admin = 1;
			}else{
				$is_receiver_admin = 0; 
			}		
			Message::putMessagesReceivers($message_id, $receiver_id, $is_receiver_admin, $admin_company_id);

			
			//既読フラグをメッセージに登録する
			$message_id = $datas['message_id'];
			
			Message::hasReadMessage($driver_id, $message_id);
						
			$status = "SUCCESS";
			
			return $status;
			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

			$status = "DB_ERROR";
			
			return $status;

		}
}

//メッセージの返信スマホから受信
public static function Reply($datas, $driver_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
			//スレッドIDを取得
			
			$sql="SELECT
					thread_id
				  FROM
				  	messages
				  WHERE
				  	id = :id
					;
					";
						
			$stmt=$dbh->prepare($sql);
			
			$param = array(
				'id' => $datas['message_id']
			);
			
			$stmt->execute($param);
			$id = $stmt->fetch();
			$thread_id = $id[0];
			
			//ドライバーの氏名を取得
			$driver_name_array = Driver::getNameById($driver_id);

			$last_name = $driver_name_array[0]['last_name'];
			$first_name = $driver_name_array[0]['first_name'];
			$driver_name = $last_name.$first_name;
					
			
			$sql="
			INSERT 
			INTO
				messages
				(thread_id, driver_id, gcm_message, sender, created)		
			VALUES
				(:thread_id, :driver_id, :gcm_message, :sender, now())";
			
			
			$stmt=$dbh->prepare($sql);

			$param = array(
				'thread_id' => $thread_id,
				'driver_id' => $driver_id,
				'gcm_message' => $datas['gcm_message'],
				'sender' => $driver_name
			);
			
			$stmt->execute($param);

			$sql="SELECT
					LAST_INSERT_ID();
					";
						
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$id = $stmt->fetch();
			$message_id = $id[0];

			//メッセージの最初のスレッドの送信者が、管理者か営業所長かを調べる
			$thread_info = Message::getMessagesByThreadId($thread_id);

			$receiver_id = $thread_info[0]['sender_id'];

			if($thread_info[0]['sender_id']=="0"){
				$is_receiver_admin = 1;
			}else{
				$is_receiver_admin = 0; 
			}		
			Message::putMessagesReceivers($message_id, $receiver_id, $is_receiver_admin, $admin_company_id);
			
			
			
			//既読フラグをメッセージに登録する
			$message_id = $datas['message_id'];
			Message::hasReadMessage($driver_id, $message_id);
						
			$status = "SUCCESS";
			
			return $status;
			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

			$status = "DB_ERROR";
			
			return $status;

		}
}

/**
 * 受信者とメッセージのリレーションテーブルに値を登録
 */
public static function putMessagesReceivers($message_id, $receiver_id, $is_receiver_admin, $admin_company_id){
	
	try{

		$dbh=SingletonPDO::connect();	
			
		$sql="
		INSERT 
		INTO
			messages_receivers
			(message_id, receiver_id, is_receiver_admin, admin_company_id, created)		
		VALUES
			(:message_id, :receiver_id, :is_receiver_admin, :admin_company_id,  now())";
		
		
		$stmt=$dbh->prepare($sql);

		$param = array(
			'message_id' => $message_id,
			'receiver_id' => $receiver_id,
			'is_receiver_admin' => $is_receiver_admin,
			'admin_company_id' => $admin_company_id
		);
		
		$stmt->execute($param);
		
	}catch(Exception $e){
	
		echo $e->getMessage();

	}

}

/**
 * スレッド単位でドライバーがメッセージを読んだら既読フラグをつける
 * @param int $driver_id
 * @param int $thread_id
 */

public static function hasReadMessageByThread($driver_id, $thread_id){
	
	try{

			$dbh=SingletonPDO::connect();
			
			//サブグループマネージャー
			$driver_info_array = Driver::getById($driver_id, $from_web);
				
			$messageList = Message::getMessagesByThread($driver_info_array[0]->company_id, $thread_id);				
			
			foreach ($messageList as $each_message){
				
				$message_id = $each_message['id'];
				Message::hasReadMessage($driver_id, $message_id);
				
			}

		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}




/**
 * ドライバーがメッセージを読んだら既読フラグをつける
 * @param int $driver_id
 * @param int $message_id
 */

public static function hasReadMessage($driver_id, $message_id){
	
	try{
		$dbh=SingletonPDO::connect();		
					
			
			//メッセージと送ったドライバーの表のIDを既読にする
			$sql="
					UPDATE 
						messages_receivers
					SET
						has_read = 1
					WHERE
						receiver_id = $driver_id
					AND
						message_id = $message_id
					";
						
			$stmt=$dbh->prepare($sql);
			$stmt->execute();

		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}


//ID単位でメッセージを削除するメソッド

public static function deleteMessage($id){

		try{
			$dbh=SingletonPDO::connect();

			$sql="DELETE
					FROM 
						messages
					WHERE 
						id = :id 
				";

			$res = $dbh->prepare($sql);

			$param=array(
				'id'=>$id
			);

			$res->execute($param);

		}
		catch(Exception $e){
			$dbh->rollback();
			die($e->getMessage());
		}
	}

	
//ステータスの変更をアプリへ送信する

public static function SendStatusChanging($datas, $company_id){
	
	try{
		$dbh=SingletonPDO::connect();
		
			$sql="
			SELECT 
				registration_id,
				ios_device_token
			FROM
				drivers
			WHERE
				company_id = $company_id";
		
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$notification_ids = $stmt->fetchAll();

			/********************************
			 * DBから取得したregistration_id、ios_device_tokenをそれぞれの配列に格納する
			 *******************************/
			$regIds = Array();
			$iOSDeviceTokens = Array();
			foreach($notification_ids as $notification_id){
				// registration id for Android
				$string_regId = $notification_id['registration_id'];
				if ($string_regId != null) {
					array_push($regIds, $string_regId);
				}
				// ios_device_token for iOS
				$string_iod_device_token = $notification_id['ios_device_token'];
				if ($string_iod_device_token != null) {
					array_push($iOSDeviceTokens, $string_iod_device_token);
				}
			}
			
			/********************************
			 * Push Notification For Android 
			 *******************************/
			
			$android_notification_error = null;
			$isPushedToAndroid = true;
			try {
				list($response_status, $response_result) = sendNotification(
					$regIds,
					array(
						'status' => "CHANGE_STATUS",
						'action_1' => $datas['action_1'], 'action_2' => $datas['action_2'],
						'action_3' => $datas['action_3'], 'action_4' => $datas['action_4'],
						'time' => $datas['time'], 'distance' => $datas['distance'],
						'track_always' => $datas['track_always'], 'accuracy' => $datas['accuracy'])
					);
			    $decoded_result = json_decode($response_status);
			} catch (Exception $e) {
		    	$android_notification_error = "ANDROID ERROR :: ".$e;
				$isPushedToAndroid = false;
			}
			
			//送信失敗したか、エラーがあるかチェック
			if ($isPushedToAndroid && $decoded_result->success == 0) {
				$isPushedToAndroid = false;
			}
			
			/********************************
			 * Push Notification For iOS
			 *******************************/
			$ios_notification_error = null;
			$iosPushNotificationStatus = true;
		    $pushDatas = array( 'status' => 'CHANGE_STATUS');
		    try {
			    foreach ( $iOSDeviceTokens as $device_token ) {
			    	if ($device_token != null) {
						PushNotification::iosPushNotification( $pushDatas, $is_user, "アプリの設定が変更されました。", $device_token );
			    	}
			    }
		    }catch (Exception $e){
		    	$ios_notification_error = "iOS ERROR :: ".$e;
		    	$iosPushNotificationStatus = false;
		    }
			
			//メッセージ送信が成功したかどうか
			if($isPushedToAndroid || $iosPushNotificationStatus){
				
				$status = "SUCCESS";
				$response_status = "";
				
			}else{
				
				$status = "ERROR";
				$error_sanitized = htmlentities(strip_tags($response_status),ENT_QUOTES, mb_internal_encoding());
				$error_sanitized .= "\n".$android_notification_error."\n".$ios_notification_error;
				
			}
			
			return array($status, $response_status);
			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

//管理者へグループ参加申請を通知
public static function SendJoinGroup($datas){
	
	try{
			
			/********************************
			 * Push Notification For Android 
			 *******************************/

			$android_notification_error = null;
			$isPushedToAndroid = true;
			try {
				//メッセージ送信　本体
				$regIds = array($datas['registration_id']);
				list($response_status, $response_result) = sendNotification(
					$regIds,
					array(
						'status' => 'JOIN_REQUEST',
						'join_request_id' => $datas['join_request_id'],
						'company_group_id' => $datas['company_group_id'],
						'first_name' => $datas['first_name'],
					 	'last_name' => $datas['last_name']
					)
				);
			    $decoded_result = json_decode($response_status);
			} catch (Exception $e) {
		    	$android_notification_error = "ANDROID ERROR :: ".$e;
				$isPushedToAndroid = false;
			}
			
			//送信失敗したか、エラーがあるかチェック
			if ($isPushedToAndroid && $decoded_result->success == 0) {
				$isPushedToAndroid = false;
			}
		
			/********************************
			 * Push Notification For iOS
			 *******************************/
			$ios_notification_error = null;
			$iosPushNotificationStatus = true;
		    $pushDatas = array( 'status' => 'JOIN_REQUEST', 'join_request_id' => $datas['join_request_id']);
		    try {
		    	if ($datas['ios_device_token'] != null) {
					PushNotification::iosPushNotification( $pushDatas, $is_user, "グループへ参加申請が行われました", $datas['ios_device_token'] );
			    }
		    }catch (Exception $e){
		    	$ios_notification_error = "iOS ERROR :: ".$e;
		    	$iosPushNotificationStatus = false;
		    }
			
			//メッセージ送信が成功したかどうか
		    if($isPushedToAndroid || $iosPushNotificationStatus){
				
				$status = "SUCCESS";
				$response_status = "";
				
			}else{
				
				$status = "ERROR";
				$error_sanitized = htmlentities(strip_tags($response_status),ENT_QUOTES, mb_internal_encoding());
				$error_sanitized .= "\n".$android_notification_error."\n".$ios_notification_error;
				
			}
			
			return array($status, $response_status);
			
					
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

//申請者へ承認の通知
public static function SendAcceptJoinGroup($datas) {
	
	try{

		/********************************
		 * Push Notification For Android 
		 *******************************/
		
		$android_notification_error = null;
		$isPushedToAndroid = true;
		try {
			$regIds = array($datas['registration_id']);
			list($response_status, $response_result) = sendNotification(
				$regIds,
				array(
					'status' => "ACCEPT_JOIN",
					'login_id' => $datas['login_id'],
					'passwd' => $datas['passwd'],
					'first_name' => $datas['first_name'],
				 	'last_name' => $datas['last_name'],
					'driver_id' => $datas['driver_id'],
					'company_group_id' => $datas['company_group_id'],
					'company_login_id' => $datas['company_login_id'],
					'is_group_manager' => $datas['is_group_manager'],
					'action_1' => $datas['action_1'],
					'action_2' => $datas['action_2'],
					'action_3' => $datas['action_3'],
					'action_4' => $datas['action_4'],
					'distance' => $datas['distance'],
					'time' => $datas['time'],
					'track_always' => $datas['track_always'],
					'accuracy' => $datas['accuracy']
				)
			);
			$decoded_result = json_decode($response_status);
		} catch (Exception $e) {
		    $android_notification_error = "ANDROID ERROR :: ".$e;
			$isPushedToAndroid = false;
		}
			
		//送信失敗したか、エラーがあるかチェック
		if ($isPushedToAndroid && $decoded_result->success == 0) {
			$isPushedToAndroid = false;
		}

		/********************************
		 * Push Notification For iOS
		 *******************************/
		$ios_notification_error = null;
		$iosPushNotificationStatus = true;
		$pushDatas = array( 'status' => 'ACCEPT_JOIN', 
					'login_id' => $datas['login_id'],
					'passwd' => $datas['passwd']);
		try {
			if ($datas['ios_device_token'] != null) {
				PushNotification::iosPushNotification( $pushDatas, $is_user, "グループへ参加申請が承認されました。", $datas['ios_device_token'] );
			}
		}catch (Exception $e){
			$ios_notification_error = "iOS ERROR :: ".$e;
			$iosPushNotificationStatus = false;
		}
		
		//メッセージ送信が成功したかどうか
		if($isPushedToAndroid || $iosPushNotificationStatus){
		
			$status = "SUCCESS";
			$response_status = "";
			
		}else{
			
			$status = "ERROR";
			$error_sanitized = htmlentities(strip_tags($response_status),ENT_QUOTES, mb_internal_encoding());

		}
		
		return array($status, $response_status);
			
					
	}catch(Exception $e){

		echo $e->getMessage();

	}
}


//クラスMessageの終了
}
?>