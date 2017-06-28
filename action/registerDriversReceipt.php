<?php
/**
 * スマートフォンから来るドライバーの定期購読情報を保存
 *　@author Ichinose Hiroto
 * @since 2014/02/21
 */

//$_POST['login_id'] = "ichinose";
//$_POST['passwd'] = "ichinose";
//$_POST['receipt_unique_id'] = "2222111111wwe1fjauhasklfduiavgsdfohyauiof"; // ios in app purchase
//$_POST['wallet_order_id'] = "33322222199231429009293902839"; // google wallet
//$_POST['expiration_date'] = "1990-12-07 10:10:12";

if(	isSet($_POST['login_id']) &&
	isSet($_POST['passwd']) &&
	isSet($_POST['expiration_date']) &&
	(	isSet($_POST['receipt_unique_id']) ||
		isset($_POST['wallet_order_id'])	)) {

	$keys = $drivers_array;
	$keys = array_merge($keys, $subscription_array);
	
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
		//var_dump($datas);
		if (!empty($datas['receipt_unique_id']) || 
			!empty($datas['wallet_order_id'])) {
			
			$status = null;
			
			$receipts = Receipts::getReceiptByDriverId( $datas['driver_id'] );
			$receipt = $receipts[0];
			
			if ($receipt['id'] != null) {
				
				// iOS用課金のユニークIDが送られてない、かつ、DBには保存されてる場合
				//　DBのデータを保存配列に格納
				if ($datas['receipt_unique_id'] == null) {
					$datas['receipt_unique_id'] = $receipt['receipt_unique_id'];
				}
			
				// Android用課金のユニークIDが送られてない、かつ、DBには保存されてる場合
				//　DBのデータを保存配列に格納
				if ($datas['wallet_order_id'] == null) {
					$datas['wallet_order_id'] = $receipt['wallet_order_id'];
				}
				
				$status = Receipts::updateReceipt( $datas, $receipt['id'], $datas['driver_id']);
			} else {
				$status = Receipts::registerNewReceipt( $datas );
			}
			echo $status;
		} else {
			echo "INVALID_ORDER_ID";
		}
		
		print $status;
		
	}

}else{
	//不正なアクセスです、の表示
	print "INVALID_ACCESS";

}