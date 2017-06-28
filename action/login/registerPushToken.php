<?php
/**
 * スマートフォンから来るドライバーのプッシュ通知用トークンを更新
 *　@author Ichinose Hiroto
 * @since 2014/02/21
 */

if(isSet($_POST['login_id']) && isSet($_POST['passwd']) && isSet($_POST['ios_device_token'])) {

	$keys = $drivers_array;
	
	foreach($keys as $key){

		if(is_garapagos()){
			$_POST[$key]=mb_convert_encoding($_POST[$key], "UTF-8", "SJIS");
		}

		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());

	}
	
	//データの整形など
	if($datas['login_id']){
		$datas['login_id'] = trim($datas['login_id']);
	}

	if($datas['passwd']){
		$datas['passwd'] = trim($datas['passwd']);
	}
	//すでに登録のあるIDかどうか調べる
	$idArray = Driver::login($datas['login_id'], $datas['passwd']);
		
	// 存在すれば、アカウント編集
	if(isSet($idArray[0]['id'])) {
	
		$datas['driver_id'] = $idArray[0]['id'];
		
		if (!empty($datas['ios_device_token'])) {
			//ios_device_tokenがある場合は、ios_device_tokenのみを更新
			$status = Driver::updateIOSDeviceToken($datas['driver_id'], $datas['ios_device_token']);
		}
		
		print $status;
		
	}

}else{
	//不正なアクセスです、の表示
	print "INVALID_ACCESS";

}