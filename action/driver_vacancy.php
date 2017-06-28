<?php
/** 
 * 使ってないから消してもOK？
 * ドライバーのステータスアップデート空車　データベースへ状況を入力　iphone,PC
 * ver2.0から追加
 */

/**
 * 共通設定、セッションチェック読み込み
 */
require_once('driver_check_session.php');
require('getNearDataCommon.php');

$keys=array();
$keys=array_merge($driver_status_array);

//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}


	try{
		
		//取得した緯度・経度
		if($datas["latitude"]&&$datas["longitude"]){
			$latitude=$datas["latitude"];
			$longitude=$datas["longitude"];
		}else{
			
			//緯度経度が取得できなかった場合のメッセージ
			$smarty->assign("no_gps",NO_GPS);
		}
		
		//ドライバー情報をアップデート
		 $datas['driver_id']=$_SESSION["driver_id"];
		 
 
		Driver::statusUpdate($datas);
		
	}catch(Exception $e){
		
			$message=$e->getMessage();
	}

	
	//JS割り当て 携帯以外だったら逆ジオコーディング
	if($carrier=='softbank'||$carrier=='au'||$carrier=='docomo'){
					
				
		}else{				
				
			$smarty->assign("js",'<script src="'.GOOGLE_MAP.'"type="text/javascript"></script>
			<script type="text/javascript" src="'.REVERSE_GEOCODING_JS.'"></script>');

			$smarty->assign("onload_js","onload=\"initialize($latitude,$longitude)\"");				
		}
	
	$smarty->assign("status",$datas['status']);
	$smarty->assign("filename","driverVacancy.html");
	$smarty->display('template.html');
	

?>