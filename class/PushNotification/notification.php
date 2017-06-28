<?php
/**
 * @file
 * sample_push.php
 *
 * Push demo
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://code.google.com/p/apns-php/wiki/License
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to aldo.armiento@gmail.com so we can send you a copy immediately.
 *
 * @author (C) 2010 Aldo Armiento (aldo.armiento@gmail.com)
 * @version $Id: sample_push.php 65 2010-12-13 18:38:39Z aldo.armiento $
 */

/**
 * 
 * 開発用、本番用で切り替えるとき
 * 1.各証明書のファイル名を変更
 * 2.ENVIRONMENT_SANDBOXまたはENVIRONMENT_PRODUCTIONに変える
 */

// Adjust to your timezone
//date_default_timezone_set('Europe/Rome');

// Report all PHP errors
//error_reporting(-1);

// Using Autoload all classes are loaded on-demand
//require_once 'ApnsPHP/Autoload.php';

/*
$testDeviceToken['ios_device_token'] = "d210a95b8f4a22193f11f1b0115740b65d92b3e20b25074e5e8fd8f5ac80b889";
PushNotification::iosPushNotification( $testDeviceToken );
*/
class PushNotification {
	
	private static $certificatePath = 'smart_location_admin_certificates_production.pem';	//本番用
	//private static $certificatePath = 'smart_location_admin_certificates_sandbox.pem';	//開発用
	
	private static $rootCertificationAuthorityPath = "entrust_root_certification_authority.pem";
	
	static public function iosPushNotification ( $datas, $is_user, $popup_message, $device_token ) {
		
		// Instanciate a new ApnsPHP_Push object
		$certificatePathForPush = self::$certificatePath;
		
		$certificatePathForDifinitePush = PUSH_NOTIFICATION_CER_PATH.$certificatePathForPush;
		$rootCertificationAuthorityDifinitePath = PUSH_NOTIFICATION_CER_PATH.self::$rootCertificationAuthorityPath;
		
		$push = new ApnsPHP_Push(
			ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
			//ENVIRONMENT_PRODUCTION -> 本番用
			//ENVIRONMENT_SANDBOX -> 開発用
			//'server_cerificates_bundle_sandbox.pem'
			$certificatePathForDifinitePush
		);
		
		// Set the Root Certificate Autority to verify the Apple remote peer
		//$push->setRootCertificationAuthority( self::$rootCertificationAuthorityPath );
		$push->setRootCertificationAuthority( $rootCertificationAuthorityDifinitePath );
		
		// Connect to the Apple Push Notification Service
		$push->connect();
		
		// Instantiate a new Message with a single recipient
		//$message = new ApnsPHP_Message('1e82db91c7ceddd72bf33d74ae052ac9c84a065b35148ac401388843106a7485');
		$message = new ApnsPHP_Message( $device_token );	//$deviceToken
		
		// Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
		// over a ApnsPHP_Message object retrieved with the getErrors() message.
		$message->setCustomIdentifier("Message-Badge-0");
		
		// Set badge icon to "3"
		$message->setBadge(0);
		
		// Set a simple welcome text
		$message->setText( $popup_message );
		
		// Play the default sound
		$message->setSound();
		
		// Set a custom property
		
	    /*
		$headers = array("Content-Type:" . "application/json", "Authorization:" . "key=" . $apiKey);
	    $data = array(
	        'data' => $datas
	    );
	    
	    $ch = curl_init();
	 
	    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); 
	    curl_setopt( $ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send" );
	    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
	    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
	 
	    $response = curl_exec($ch);
	    
	 	if(!curl_errno($ch))
		{
			$info = curl_getinfo($ch);
		}
	    
	    curl_close($ch);
	    
	    echo 'json info---------------------------<br><br>';
	    var_dump($info);
	    */
	    $message->setCustomProperty('acme2', $datas );
		
		// Set another custom property
		$message->setCustomProperty('acme3', null);
		
		// Set the expiry value to 30 seconds
		$message->setExpiry(30);
		
		// Add the message to the message queue
		$push->add($message);
		
		// Send all messages in the message queue
		$push->send();
		
		// Disconnect from the Apple Push Notification Service
		$push->disconnect();
		
		// Examine the error message container
		$aErrorQueue = $push->getErrors();
		if (!empty($aErrorQueue)) {
			//Androidアプリにもエラーとして認識されるので、とりあえずコメントアウト
	//		var_dump($aErrorQueue);
		}
//		
//		var_dump($certificatePathForDifinitePush);
//		var_dump($rootCertificationAuthorityDifinitePath);
//		var_dump($device_token);
//		
	}

}