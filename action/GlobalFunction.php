<?php
//いろいろ使うfunction

function getSpeedBySeconds($recent_latitude, $recent_longitude, $datas, $last_created){
	
	//最後に地点登録されたところと、今登録されようとしている地点の距離を算出　単位はkm
	$distance = (sqrt(pow(($recent_latitude - $datas['latitude']) * 111, 2 )
			+ pow(($recent_longitude - $datas['longitude']) * 91, 2 )));
	
	//GPSの飛びを防止するため、短い時間で移動距離が長すぎる場合、誤差の可能性があるので地点登録しない
	$time_diff = $last_created['time_diff'];
	
	//秒に変換
	$second_diff = h2s($time_diff);
	
	//秒速の判定
	if($second_diff >0 && $distance > 0){
		
		//秒速何キロか　　$time_diffを秒などに直す
		$speed_per_seconds = $distance / $second_diff;
		
	}elseif($distance == 0){
		
		//全く移動していない場合　秒速0Kmで移動
		$speed_per_seconds = 0;
		
	}
	
	return $speed_per_seconds;
	
}


/**
 * 配送先との距離を求める
 * @param unknown $datas
 * @param unknown $destination_data
 * @return number
 */
function getDistanceToDestinations($datas, $destination_data, $i){
	
	//2直線間の距離を求める
	$earth_r = 6378.137; //地球の半径
	$idoSa   = deg2rad($datas['latitude'] - $destination_data[$i]['latitude']);     //緯度差をラジアンに
	$keidoSa = deg2rad($datas['longitude'] - $destination_data[$i]['longitude']); //経度差をラジアンに
	$nanbokuKyori =  $earth_r * $idoSa;                        //南北の距離
	$touzaiKyori  = cos(deg2rad($destination_data[$i]['latitude'])) * $earth_r * $keidoSa; //東西の距離
	$distance= (sqrt(pow($touzaiKyori,2) + pow( $nanbokuKyori,2)))*1000; //三平方の定理で配送先と今いる位置の距離を求める
	
	return $distance;
}

/**
 * スマホからのinputを成形する
 * @param unknown $datas
 * @return string|mixed
 */
function formatSmartPhonePost($datas){
	
	//iPhoneからは、終了と開始がない場合(null)という文字列で来るので、Android版と統一
	if($datas['end'] == "(null)"||$datas['end'] == "NULL"){
		$datas['end'] = "NULL";
	}
	
	if($datas['start'] == "(null)"){
		$datas['start'] = "NULL";
	}
	
	//iPhoneから、速度がマイナスの値の場合、0に補正
	if($datas['speed'] < 0){
		$datas['speed'] = 0;
	}
	
	//iPhoneから、住所に郵便番号が入る場合、削除
	$matches = array();
	$match_count = preg_match("/^(〒[-\d]+)?(\s+)?(.+)$/", $datas['address'], $matches);
	
	if($match_count > 0) {
		
		$datas['address'] = $matches[3];
		
	}
	
	return $datas;

}

/**
 * 改行、シングルクォテーション、ダブルクォテーションを削除
 */
function removeNewLineAndQuotation($str){
	
	//シングルクォテーションとダブルクォテーションを削除
	$str = str_replace(array("'","’","\""), '', $str);
	
	//改行を削除
	$str = str_replace(array("\r\n", "\r", "\n"), '', $str);
	
	//前後の空白を削除
	$str = trim($str);
	
	return $str;
	
}




/**
 * セッションからSmartyへステータス名をアサインする
 */
function assignStatusName($smarty){
	;	
	if(!empty($_SESSION['status_1']) ){
		$status_1 = $_SESSION['status_1'];
		$status_2 = $_SESSION['status_2'];
		$status_3 = $_SESSION['status_3'];
		$status_4 = $_SESSION['status_4'];
		
		$smarty->assign("status_1", $status_1);
		$smarty->assign("status_2", $status_2);
		$smarty->assign("status_3", $status_3);
		$smarty->assign("status_4", $status_4);
	}
	
	return $smarty;
}

/**
 * セッションにステータス名を入れる
 */
function putStatusToSession($session_company_id){
	
	$status = Data::getStatusALL($session_company_id);
	if(!empty($status)){
		$_SESSION['status_1'] = $status[0]->action_1;
		$_SESSION['status_2'] = $status[0]->action_2;
		$_SESSION['status_3'] = $status[0]->action_3;
		$_SESSION['status_4'] = $status[0]->action_4;
	}
	
}


/**
 * ステータスのIDから、ステータスの日本語名を返す
 */
function statusToJapanese($status, $working_status){

	if(	$status == 1){
		$status_ja = $working_status->action_1;
	}else if($status == 2){
		$status_ja = $working_status->action_2;
	}else if($status == 3){
		$status_ja = $working_status->action_3;
	}else if($status == 4){
		$status_ja = $working_status->action_4;
	}else{
		$status_ja = "なし";
	}

	return $status_ja;
}

/**
 * ステータスのIDで色を返す
 * @param $status （ステータスの番号)
 */
function statusToColor($status){

	if(	$status == 1){
		$color = "#cf714a";
	}elseif($status == 2){
		$color = "#ff00ff";
	}elseif($status == 3){
		$color = "#4169e1";
	}elseif($status == 4){
		$color = "#009944";
	}else{
		$color = "#FF0000";
	}

	return $color;

}

function getUniqueDatesFromDriverStatus($driver_locations){
	
	$unique_dates = array();
	foreach ($driver_locations as $each_driver_location) {
		$time_stamp = strtotime($each_driver_location['created']);
		$date = date("Y-m-d", $time_stamp);
		
		if(empty($last_date) || $date != $last_date){
			$unique_dates[] = $date;
		}
		
		$last_date = $date;
	}
	
	return $unique_dates;
}

/**
 * 輸送ルートが重複している場合、重複しているルートを取り除いて返す
 * @param unknown $t_routes
 * @return unknown
 */
function getUniqueRoutesFromRoutesArray($unique_dates, $driver_id, $date){
	
	foreach($unique_dates as $date){
		
		$t_routes = TransportRouteDriver::getRoutesByDriverIdAndDate($driver_id, $date);
	
		if(empty($transport_routes)){
			
			$transport_routes[] = $t_routes;
			
		}else{
			
			foreach($transport_routes as $value){
				
				if($t_routes['id']==$value['id']){
					
					$is_exisited = true;
					break;
					
				}
			}
			
			if(!$is_exisited){
				
				$transport_routes[] = $t_routes;
			}
			
		}
	}
	
	return $transport_routes;
}


/**
 * 現在時刻との差を秒で返す
 * @param unknown_type $dateTime
 */
function secondDiffFromNow($dateTime) {

	$endSec = strtotime("now");
	$startSec   = strtotime($dateTime);

	$diff = $endSec - $startSec;

	return $diff;

}


/**
 * ステータスによりトラックの画像の種類を切り替える
 */
function makeDriverStatusImage($status_id){
	
	$image_url = null;
	$image = null;
	
	if($status_id == 1 || $status_id == 5){
		
		$image = "track_front_working_middle.png";
		
	}else if($status_id == 2){
		
		$image = "track_rightDirection_transit.png";
		
	}else if($status_id == 3){
		
		$image = "track_leftDirection_returnRunning.png";
		
	}else if($status_id == 4){
		
		$image = "track_front_break_wait_middle.png";
		
	}
	
	if(!empty($image)){
		$image_url = TEMPLATE_URL."templates/image/".$image;
	}
	
	return $image_url;
	
}

/**
 * ステータスと古い情報かどうか、からマーカーの種類を返す
 */
function makeDriverStatusMarker($status_id, $is_old){

	$marker = null;
	
	if($status_id == 1 && $is_old){

		$marker = "pinkcar-11";

	}else if($status_id == 1){

		$marker = "redcar-15";

	}else if($status_id == 2 && $is_old){

		$marker = "palebluecar-11";

	}else if($status_id == 2){

		$marker = "bluecar-15";

	}else if($status_id == 3 && $is_old){

		$marker = "palegreencar-11";

	}else if($status_id == 3){

		$marker = "greencar-15";

	}else if($status_id == 4 && $is_old){

		$marker = "palepurplecar-11";

	}else if($status_id == 4){

		$marker = "purplecar-15";

	}

	return $marker;

}


/**
 * 進行方向を計算
 */

function geoDirection($lat1, $lng1, $lat2, $lng2, $speed) {
	
	if(($lat1 !=$lat2 || $lng1 != $lng2) && $speed > 0 ){

	  // 緯度経度 lat1, lng1 の点を出発として、緯度経度 lat2, lng2 への方位
	  // 北を０度で右回りの角度０～３６０度
	  $y = cos($lng2 * pi() / 180) * sin($lat2 * pi() / 180 - $lat1 * pi() / 180);
	  $x = cos($lng1 * pi() / 180) * sin($lng2 * pi() / 180) - sin($lng1 * pi() / 180) * cos($lng2 * pi()/ 180) * cos($lat2 * pi() / 180 - $lat1 * pi() / 180);
	  $dirE0 = 180 * atan2($y, $x) / pi(); // 東向きが０度の方向
	  if ($dirE0 < 0) {
	    $dirE0 = $dirE0 + 360; //0～360 にする。
	  }
	  $dirN0 = ($dirE0 + 90) % 360; //(dirE0+90)÷360の余りを出力 北向きが０度の方向
	
	}else{
		
		//2点間がおなじ場所の場合は、確度をnullにする
		$dirN0 =null;
	
	}
  return $dirN0;
}


// セッションを用いてログイン状態をチェックする関数
// これを呼び出すと、セッションが開始される
// $_SESSION['u_id']がセットされているかをBooleanで返す

function between_day($month, $day, $year = null){

	//BETWEENで計算するために、日付に1日プラスする
	//月末だったら次の月の1日をTOにする
	if($day == date('t', mktime(0, 0, 0, $month, 1, $year))){
		$to_date['day'] = 1;
		
		//12月だったら次の月は1月、年もプラス1
		if($month == 12){

			$to_date['month'] = 1;			
			$to_date['year'] = $year + 1;			
		
		}else{
			
			$to_date['month'] = $month+ 1;			
			$to_date['year'] = $year;			
		}
		
	}else{
		$to_date['day'] = $day+ 1;
		$to_date['month'] = $month;			
		$to_date['year'] = $year;			
	}
	return ($to_date);
}

//ログインIDを生成・チェック
function creatDriverLoginId($company_group_id) {
	
	$today = date("mdGi");
	$login_id = $company_group_id."-".$today;

	
	if (Driver::isExistingDriverId($login_id)){
		//ログインIDが重複してたら、もう一回ログインIDを生成
		$login_id = $company_group_id."-".$today;
		creatDriverLoginId();
		
	} else {
		//重複してなかったら、そのまま生成したログインIDを使う
		return $login_id ;
	}
}

//ランダムな文字列、6文字生成
function RandomString($nLengthRequired = 6){
	$sCharList = 'abcdefghijklmnopqrstuvwxyz0123456789-_';
	mt_srand();
	$sRes = '';
	for($i = 0; $i < $nLengthRequired; $i++)
		$sRes .= $sCharList{mt_rand(0, strlen($sCharList) - 1)};
		return $sRes;	
}




function check_session() {
    session_start();
    return !empty($_SESSION['u_id']);
}

//AndroidGCMを利用して、Androidにメッセージを送る
 function sendNotification( $registrationIdsArray, $messageData ) {   
 	
 	$apiKey = GCM_API;
    $headers = array("Content-Type:" . "application/json", "Authorization:" . "key=" . $apiKey);
    $data = array(
        'data' => $messageData,
	     'collapse_key' => "collapse_key",
        'registration_ids' => $registrationIdsArray
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
  
    return array($response,$info);
 }

 /**
 *
 * 同じ位置情報ががないか、配列の中を探す
 * @param $latitude
 * @param $longitude
 * @param $haystack　探す配列
 * @param $original_key もともとの配列のキー番号　自分自身を探さないように投げる
 */
function in_array_field($latitude, $longitude, $haystack, $original_key) {

	$same_position_person = null;
	$same_position_person = Array();

	$searched_keys = null;
	$searched_keys = Array();

	//latitudeが一致し、自分自身ではない 　
	foreach ($haystack as $key => $item){

		if (isset($item["latitude"]) &&
		$item["latitude"] == $latitude &&
		isset($item["longitude"]) &&
		$item["longitude"] == $longitude &&
		$key != $original_key
		//&&
		//$original_key < $key
		){
			 
			$same_position_person_name = $item["last_name"]. "&nbsp;" .$item["first_name"];
			$same_position_person[] = $same_position_person_name;
			$searched_keys[] = $key;
		}
	}
	return array($same_position_person, $searched_keys);

} 
 
//
////距離の計算
//function calculateDistance($datas, $recent_work_id){
//	
//	$dbh=SingletonPDO::connect();
//	$sql = "SELECT 
//				start_latitude,
//				start_longitude
//			FROM
//				work
//			WHERE
//				id = $recent_work_id
//			AND					
//				driver_id =". $datas['driver_id']."
//			ORDER BY created DESC
//			LIMIT 1";
//	echo "<br><br>".$sql;
//	$stmt=$dbh->prepare($sql);
//	$stmt->execute();
//	$start_points = $stmt->fetchAll();
//	
//	var_dump($start_points);
//	exit;
//	
//	$sql = "SELECT 
//			  id,
//			  start_latitude,
//			  start_longitude,
//		      (6371 * acos(
//			        cos( radians(".$datas['latitude'].") ) * cos( radians( start_latitude ) ) *
//			        cos( radians( start_longitude ) - radians(".$datas['longitude'].") ) + 
//			        sin( radians(".$datas['latitude'].") ) * sin( radians( start_latitude ) ) 
//		      	)
//		  ) AS distance
//			FROM
//				work
//			WHERE
//				id = $recent_work_id
//			AND						
//				driver_id =". $datas['driver_id'];
//		  
//	$stmt=$dbh->prepare($sql);
//	$stmt->execute();
//	$distances = $stmt ->fetch();
//	$distance = round( $distances['distance'], 2 );
//	
//	return $distance;
//
//}



function get_time_from_and_to_ymdhm(){

	if(!empty($_GET["time_from_year"])){ 
		
		$hour = sanitizeGet($_GET["time_from_hour"]);
		if ($hour == null) {
			$hour = "0";
		}
		
		$time_from_minute = sanitizeGet($_GET["time_from_minit"]);
		if ($_GET["time_from_minit"] == 0) {
			$time_from_minute = 0;
		}
		$time_from = sanitizeGet($_GET["time_from_year"]);
		$time_from .= "-";
		$time_from .= sanitizeGet($_GET["time_from_month"]);
		$time_from .= "-";
		$time_from .= sanitizeGet($_GET["time_from_day"]);
		$time_from .= " ";
		$time_from .= $hour;
		$time_from .= ":";
		$time_from .= sprintf("%02d",$time_from_minute);
		
		$_SESSION['time_from_year']=sanitizeGet($_GET["time_from_year"]);
		$_SESSION['time_from_month']=sanitizeGet($_GET["time_from_month"]);
		$_SESSION['time_from_day']=sanitizeGet($_GET["time_from_day"]);
		$_SESSION['time_from_hour']=$hour;
		$_SESSION['time_from_minit']=$time_from_minute;
	
	}else{
	
		$time_from = null;
		
	}

	if(!empty($_GET["time_to_year"])){ 
	
		$hour = sanitizeGet($_GET["time_to_hour"]);
		if ($hour == null) {
			$hour = "0";
		}
		$time_to_minute = sanitizeGet($_GET["time_to_minit"]);
		if ($_GET["time_to_minit"] == 0) {
			$time_to_minute = 0;
		}
		$time_to = sanitizeGet($_GET["time_to_year"]);
		$time_to .= "-";
		$time_to .= sanitizeGet($_GET["time_to_month"]);
		$time_to .= "-";
		$time_to .= sanitizeGet($_GET["time_to_day"]);
		$time_to .= " ";
		$time_to .= $hour;
		$time_to .= ":";
		$time_to .= sprintf("%02d", $time_to_minute);

		$_SESSION['time_to_year']=sanitizeGet($_GET["time_to_year"]);
		$_SESSION['time_to_month']=sanitizeGet($_GET["time_to_month"]);
		$_SESSION['time_to_day']=sanitizeGet($_GET["time_to_day"]);
		$_SESSION['time_to_hour']=$hour;
		$_SESSION['time_to_minit']=$time_to_minute;
	
	}else{
		
		$time_to = null;
		
	}
	
	return array($time_from, $time_to);
}

// 目立つカラーコードを生成
function get_attention_color () {
//	$red_ran = rand(100,200);
	
	$red_ran = rand(0,255);
	$green_ran = rand(0,255);
	$blue_ran = rand(0,255);
	
	if ($red_ran > 200) {
		if ($green_ran > 200) {
			$blue_ran = rand(0, 100);
		} else {
			$blue_ran = rand(100, 255);
		}
	} else {
		$green_ran = rand(0,158);
		$blue_ran = rand(0, 255);
	}
	
	$green = dechex($green_ran);
	$blue = dechex($blue_ran);
	$red = dechex($red_ran);
	
	/*
	// 200以上
	if ($red_ran > 200) {
		
		$green_type_ran = rand(1, 3);
		if ($green_type_ran == 1) {
			// pink or purple
			$green_ran = rand(0,0);
			$blue_ran = rand(50,130);
		} else if ($green_type_ran == 2) {
			// orange
			$green_ran = rand(100,130);
			$blue_ran = 0;
		} else if ($green_type_ran == 3) {
			// yellow
			$green_ran = rand(220,251);
			$blue_ran = 0;
		}
		$green = dechex($green_ran);
		$blue = dechex($blue_ran);
		
	} else if ($red_ran > 100 && $red_ran < 200) {
		// 200以下、100以上
		$green_ran = rand(158,255);
		$green = dechex($green_ran);
		if ($green_ran > 100) {
			$blue = dechex(rand(100,200));
		}
	}
	*/
//	$green = dechex(rand(0,255));
//	$blue = dechex(rand(0,200));
	
	if (strlen($red) == 1) { $red = $red.$red; }
	if (strlen($green) == 1) { $green = $green.$green; }
	if (strlen($blue) == 1) { $blue = $blue.$blue; }
	$color = "$red$green$blue";
	return $color;
}

//携帯キャリア判別
function get_carrier(){
	
	$agent = $_SERVER['HTTP_USER_AGENT']; 

	$carrier = null;
	if(ereg("^DoCoMo", $agent)){
		
			$carrier='docomo';
	
		}else if(ereg("^J-PHONE|^Vodafone|^SoftBank", $agent)){

			$carrier='softbank';
			
		}else if(ereg("^UP.Browser|^KDDI", $agent)){
	
			$carrier='au';
			
		}else if(ereg("iPhone", $agent)){

			$carrier='iPhone';
		
		}else if(ereg("Android", $agent)){

			$carrier='Android';
		}	

	return $carrier;	
}

//携帯キャリア判別
function is_garapagos(){
	
	//ガラケーだったらTrueを返す
	$carrier = get_carrier();
					
	if( $carrier=='docomo'||$carrier=='au'||$carrier=='softbank'){
		
		return true;
	
	}else{

		return false;
	
	}	
}

//携帯の個体識別番号の取得
function unit_no($carrier){

    $ua = $_SERVER['HTTP_USER_AGENT'];
    if($carrier === 'docomo'){
      if(preg_match("/ser([a-zA-Z0-9]+)/",$ua, $dprg)){
        if(strlen($dprg[1]) === 11){
          $mobile_id = $dprg[1];
        }elseif(strlen($dprg[1]) === 15){
          $mobile_id = $dprg[1];
        }
      }
    }elseif($carrier === 'softbank'){
    	if(preg_match("/^.+\/SN([0-9a-zA-Z]+).*$/", $ua, $match)){    	
        $mobile_id = $match[1];
      }
    }elseif($carrier === 'au'){
      $mobile_id = $_SERVER['HTTP_X_UP_SUBNO'];
    }
	return $mobile_id;
}

//逆ジオコーディング　OCのサーバーからテキストを読み込む
function re_geocoding_node($lat, $long){
	
 	$json=file_get_contents("http://onlineconsultant.tv:8125/?lat=$lat&long=$long");
 	
 	if($json){
		$obj = json_decode($json);
	
/*		$full_address = $obj->results[1]->formatted_address;
		
		$exploded_address = explode(',',$full_address); 
		$address = trim($exploded_address[1]);
*/		

		$obj = json_decode($json);
		$result_array = $obj->results;

		foreach($result_array as $each_obj){
		
			$type = $each_obj->types[0];
		
			switch ($type){
				case "street_address":
					$full_address = $each_obj->formatted_address;
					break;
				case "sublocality_level_4":
					$full_address = $each_obj->formatted_address;
					break;
				case "sublocality_level_3":
					$full_address = $each_obj->formatted_address;
					break;
				case "sublocality_level_2":
					$full_address = $each_obj->formatted_address;
					break;
				case "sublocality_level_1":
					$full_address = $each_obj->formatted_address;
					break;
					
			}
			
		}
	
				
		if(!empty($full_address)){
			$address = str_replace('日本,','',$full_address);
		}else{
			$address = NULL;
		}
		
 	}else{
		$address = NULL; 		
 	}
 	return $address;
}

//携帯キャリア別　GPS取得リンク生成　近くの営業所取得
function get_gps_link($carrier){
	
	if($carrier=='docomo'){

		$gps_link='"index.php?action=docomo_getNearData"'.' lcs';

	}elseif($carrier=='au'){

		$gps_link="device:location?url=http://".SERVER_URL."au_getNearData.php";		
		
	}elseif($carrier=='softbank'){

		$gps_link="location:auto?url=index.php&action=softbank_getNearData&";		

	}elseif($carrier==''||$carrier=='iPhone'||$carrier=='Android'){

		$gps_link="index.php?action=iphone_geo&redirectTo=iphone_getNearData";		
		
	}	
	
	return $gps_link;	
}

//携帯キャリア別　GPS取得リンク生成　近くの空車取得
function get_gps_link_vacancy($carrier){
	
	if($carrier=='docomo'){

		$gps_link_vacancy='"index.php?action=docomo_getNearVacancy"'.' lcs';

	}elseif($carrier=='au'){

		$gps_link_vacancy="device:location?url=http://".SERVER_URL."au_getNearVacancy.php";		
		
	}elseif($carrier=='softbank'){

		$gps_link_vacancy="location:auto?url=index.php&action=softbank_getNearVacancy&";		

	}elseif($carrier==''||$carrier=='iPhone'||$carrier=='Android'){

		$gps_link_vacancy="index.php?action=iphone_geo&redirectTo=getNearVacancy";		
		
	}	
	
	return $gps_link_vacancy;	
}

//携帯キャリア別　現在地住所取得
function get_gps_link_regeocoding($carrier){
	
	if($carrier=='docomo'){

		$gps_link='"index.php?action=docomo_getNearData"'.' lcs';

	}elseif($carrier=='au'){

		$gps_link="device:location?url=http://".SERVER_URL."au_getNearData.php";		
		
	}elseif($carrier=='softbank'){

		$gps_link="location:auto?url=index.php&action=test_reverse_geocoding_sb&";		

	}elseif($carrier==''||$carrier=='iPhone'||$carrier=='Android'){

		$gps_link="index.php?action=iphone_geo&redirectTo=test_reverse_geocoding_iphone";		
		
	}	
	
	return $gps_link;	
}


//携帯キャリア別　ドライバー情報Update用のGPS取得リンク生成
function get_gps_link_driver_status($carrier){
	
	if($carrier=='docomo'){

		//後で変更
		//$gps_link='"index.php?action=docomo_getNearData"'.' lcs';
		$gps_link="index.php?action=docomo_driver_status_update&".SID;
//		$gps_link="index.php?action=docomo_driver_status_update";
		
	}elseif($carrier=='au'){
		
		
	}elseif($carrier=='softbank'){

		$gps_link="location:auto?url=index.php&action=softbank_driver_status_update";		

	}elseif($carrier==''||$carrier=='iPhone'||$carrier=='Android'){

		$gps_link="index.php?action=iphone_geo";		
		
	}	
	
	return $gps_link;	
}

//AU用GPS整形
function au_gps_trim($lat,$long){
	list($lat_h, $lat_m, $lat_s, $lat_ms) = explode(".", $lat);
	$lat= $lat_h + ($lat_m * 60 + $lat_s + $lat_ms / 100) / 3600;

	list($long_h, $long_m, $long_s, $long_ms) = explode(".", $long);
	$long= $long_h + ($long_m * 60 + $long_s + $long_ms / 100) / 3600;
	
	return array($lat,$long);
}

//検索条件と日本語の配列
function search_string($search_ids){
	
	$search_string_words=array();
	$search_strings_words['business_hours_24']=COMMON_BUSINESS_HOURS_24;
	$search_strings_words['company']=COMMON_CORPORATE;
	$search_strings_words['individual']=COMMON_INDIVIDUAL;
	$search_strings_words['credit']=COMMON_CREDIT_OK;
	$search_strings_words['debit']=COMMON_DEBIT_OK;

	//サービスは別の配列なので、削除
	unset($search_ids['services']);
	
	foreach($search_ids as $key=>$value){

		if($value&&$search_strings_words[$key]){
			$search_strings[]=$search_strings_words[$key];
		}
		
	}	
	
	
	return $search_strings;	
}

//ドライバー本人か、会社担当者か認証
//セッションのdriver_idと、GETのドライバーID、あるいはドライバーの会社IDとセッションの会社IDがあっているか判別
function driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id){
	
	if(Data::isGovernmentUser($u_id)){
		
		return true;
	
	}elseif($driver_id==$session_driver_id){

		return true;
	
	}elseif($u_id||$session_driver_company_id){
	
		//スーパーユーザーか、その会社の管理者か
		if($u_id==$company_id ||$u_id == SUPER_USER){
			return true;
		}
		
		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
		
	}else{

		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	

	}
}

//ドライバー本人がログインしていて、会社がドライバーに編集の許可を行っていない場合にエラー画面を表示する
function driver_editing_banned_check($driver_id,$session_driver_id,$company_id,$u_id){

	//ドライバーがログインしていたら以下を処理
	if(!empty($session_driver_id)){

		//ドライバー自身の編集許可ステータス
		$is_ban_editing = Data::isBannedDriverEditing($company_id, $u_id);
		
		if ($is_ban_editing == 0){
			
			return true;
			
		}else{
		
			//編集は許可されていません、というメッセージ					
			header("Location:index.php?action=message_mobile&situation=not_allowed_edit_by_driver");
			exit(0);	
		}
	
	}
}

function existed_service_check($id){
			$existed_service_list=Data::getServiceForCompany($id);
		
		//編集画面、仮データ登録時のサービス名データ表示
		if($existed_service_list){
			foreach($existed_service_list as $each_data){
			
				$existed_service_ids[]=$each_data['service_id'];
				$session['services']=NULL;
			}
		}
		return $existed_service_ids;
	
}

//セッションのu_idと、会社IDがあっているか判別
function user_auth($u_id, $company_id){
	if(Data::isGovernmentUser($u_id)){
		
		return true;
	
	}elseif(($u_id==$company_id) && (!empty($u_id))){

		//会社の管理者
		return true;
		
	}elseif($u_id ==SUPER_USER ){
		
		//スーパーユーザー
		return true;
		
	}else{	

		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
		exit(0);

	}
}

//セッションのbranch_manager_idと、会社IDがあっているか判別
function branch_manager_auth($branch_manager_id, $company_id){

	//サブグループマネージャー
	$driver_info_array = Driver::getById($branch_manager_id, $from_web);
	
	if(($driver_info_array[0]->company_id == $company_id) && (!empty($branch_manager_id))){

		return true;
		
	}else{

		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
		exit(0);

	}
}

/**
 * ドライバーの営業所と営業所長の管轄営業所が同じかどうか
 * @param int $branch_manager_id
 * @param int $driver_id
 */
function branch_manager_driver_auth($branch_manager_id, $driver_id){

	$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);
	
	$driver_info = Driver::getById($driver_id, $from_web);
	
	if(($driver_info[0]->geographic_id == $branch_id) && (!empty($branch_id) && (!empty($driver_info)))){

		return true;
		
	}else{

		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=not_authorized");	
		exit(0);
	}
}

/**
 * 会社のマネージャーとサブグループマネージャーの認証を一度でやる
 * @param int $company_id
 * @param int $branch_manager_id
 */
function company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id){

	if(!empty($u_id)){

		user_auth($u_id, $company_id);
	
	}elseif(!empty($branch_manager_id)){
			
		branch_manager_auth($branch_manager_id, $company_id);
	
	}else{

		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
		exit(0);

	}
}

//セッションのuser_idと、ユーザーIDがあっているか判別
function user_user_auth($user_id,$id){
	
	if($user_id==$id){

		return true;
	
	}else{

		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	

	}
}

//IDをサニタイズして、不正だったら戻す
function sanitizeGet($data){
	
	if($data){
		
		$sanitized_data=htmlentities($data,ENT_QUOTES, mb_internal_encoding());

		return $sanitized_data;
	
	}
}



//時・分・秒の緯度を、10進法に変更

function decimal_base_lat($lat){

	list($lat_h, $lat_m, $lat_s, $lat_ms) = explode(".", $lat);
	$lat= $lat_h + ($lat_m * 60 + $lat_s + $lat_ms / 100) / 3600;
	return $lat;
	
}

//時・分・秒の緯度を、10進法に変更

function decimal_base_long($long){

	list($long_h, $long_m, $long_s, $long_ms) = explode(".", $long);
	$long= $long_h + ($long_m * 60 + $long_s + $long_ms / 100) / 3600;
	return $long;
	
}

//時：分：秒を秒に変更
function h2s($hours) {
        $t = explode(":", $hours);
        $h = $t[0];
        if (isset($t[1])) {
         $m = $t[1];
        } else {
         $m = "0";
        } 
        if (isset($t[2])) {
         $s = $t[2];
        } else {
         $s = "0";
        } 
        return ($h*60*60) + ($m*60) + $s;
}


//秒を時：分：秒に変更
function s2h($seconds) {
		
	$hours = floor($seconds / 3600);
	$minutes = floor(($seconds / 60) % 60);
	$seconds = $seconds % 60;
	
	$hms = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
	
	return $hms;
	
}

//ドライバー本人がログインしていて、ドライバーに編集許可がない場合はエラーメッセージを表示



/*

function driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id){
	
	if($driver_id==$session_driver_id){

		return true;
	
	}elseif($u_id||$session_driver_company_id){
		
		if($u_id==$company_id){
			return true;
		}
		
		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
		
	}else{

		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	

	}
}
*/

//----------------------------------------------------
//最大データ件数/1ページを超えた分の前・次ページの表示　スレッドの中身用　ページジャンプ機能あり

function make_page_link_con($topicList,$page_id){

$page_max=PAGE_MAX;

require_once 'Pager.php';
$params = array(
    'mode'       => 'Jumping',
    'perPage'    => $page_max,
    'delta'      => 10,
    'itemData'   => $topicList,
	'currentPage'=> $page_id );
$pager = & Pager::factory($params);
$data  = $pager->getPageData();
$links = $pager->getLinks();

return array($data, $links);

}
//----------------------------------------------------

//----------------------------------------------------
//最大データ件数/1ページを超えた分の前・次ページの表示

function make_page_link($topicList, $page_max = null){

	if($page_max){
	
	}else{
		$page_max=PAGE_MAX;
	}
	
require_once 'Pager.php';
$params = array(
    'mode'       => 'Jumping',
    'perPage'    => $page_max,
    'delta'      => 30,
    'itemData'   => $topicList
	);
$pager = & Pager::factory($params);
$data  = $pager->getPageData();
$links = $pager->getLinks();

return array($data, $links);

}

//最大データ件数/1ページを超えた分の前・次ページの表示

function alarms_page_link($topicList, $page_max = null){

	if($page_max){

	}else{
		$page_max=MESSAGEMINIMUM;
	}

	require_once 'Pager.php';
	$params = array(
			'mode'       => 'Jumping',
			'perPage'    => $page_max,
			'delta'      => 30,
			'itemData'   => $topicList
	);
	$pager = & Pager::factory($params);
	$data  = $pager->getPageData();
	$links = $pager->getLinks();

	return array($data, $links);

}

//----------------------------------------------------

//----------------------------------------------------
//ページング　POP Up用

function make_page_link_internal($topicList, $page_max = null){

	if($page_max){
	
	}else{
		$page_max=PAGE_MAX;
	}
	
require_once 'Pager.php';
$params = array(
    'mode'       => 'Jumping',
    'perPage'    => $page_max,
    'delta'      => 30,
    'itemData'   => $topicList,
	'linkClass' => "internal"
	);
$pager = & Pager::factory($params);
$data  = $pager->getPageData();
$links = $pager->getLinks();

return array($data, $links);

}
//----------------------------------------------------

//----------------------------------------------------
//男女の判定

function sex($topicList){

	for($i=0,$num_datas=count($topicList);$i<$num_datas;$i++){

   if($topicList[$i]->sex=='Woman'||$topicList[$i]->sex=='女'){
	
	$topicList[$i]->sex='<img src="templates/image/heart.gif" alt="'.COMMON_COL_WOMAN.'">';
	
	}else{

	$topicList[$i]->sex='<img src="templates/image/blue_star.gif" alt="'.COMMON_COL_MAN.'">';

     }   
  }
  
  return($topicList);
}

//----------------------------------------------------


//----------------------------------------------------
// パスワードを忘れた人へメール送信

//
function mail_password($u_id,$u_no,$u_name,$u_pass, $company_or_user = NULL){
		$to      = $u_id;
		$subject = DATE_MAIL_SUBMITPASS;
		$u_pass=$u_pass;
		$u_no=$u_no;

//メール本文用
		$wearelm = DATE_MAIL_WEARELM;
		$youpass = DATE_MAIL_YOUPASS;
		$youpass2 = DATE_MAIL_YOUPASS2;
		$chapass = DATE_MAIL_CHAPASS;
		$delmail = DATE_MAIL_PLDELMAIL;
		$hello = DATE_MAIL_HELLO;
		$keisho = DATE_MAIL_KEISHO;
		$forpc = DATE_MAIL_FORPC;
		$formb = DATE_MAIL_FORMOBILES;
		$en = DATE_MAIL_EN;
		$teamlm = DATE_MAIL_TEAMLOVETALK;
		$webteam = DATE_MAIL_WEBTEAM;
		$add = DATE_MAIL_ADD;
		$from_address=ADMIN_MAIL;
		$server_url=SERVER_URL;

//

//http://ml.php.gr.jp/pipermail/php-users/2004-April/021972.htmlを見てつけてみた
 $subject  = mb_convert_encoding( $subject, "iso-2022-jp", "auto" );

$message =<<<EOM
{$u_name}$keisho

$wearelm
$youpass {$u_name} $youpass2

{$u_pass}

$chapass
EOM;


if($company_or_user=="User"){
	$message .= "http://{$server_url}index.php?action=login_user\n";
}else{
	$message .= "http://{$server_url}index.php?action=login\n";
	}

$message .=<<<EOM
	
$delmail

$teamlm


=========================================================
$webteam
$add
TEL：045-306-9506
FAX：03-6862-5814
Email：web@onlineconsultant.jp
=========================================================
EOM;

$message  = mb_convert_encoding( $message, "iso-2022-jp", "auto" );

//Linuxの場合
$add_header = "From:$from_address";
		
mb_send_mail($to, $subject, $message, $add_header);

	}

//----------------------------------------------------

/**
 * ランダムな文字列を生成する。
 * @param int $nLengthRequired 必要な文字列長。省略すると 8 文字
 * @return String ランダムな文字列
 */
function getRandomString($nLengthRequired = 8){
    $sCharList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
    mt_srand();
    $sRes = "";
    for($i = 0; $i < $nLengthRequired; $i++)
        $sRes .= $sCharList{mt_rand(0, strlen($sCharList) - 1)};
    return $sRes;
}

//----------------------------------------------------


//----------------------------------------------------
// 仮登録者へメール送信

function mail_to_pre_member($u_id,$company_id,$u_name,$link_pass){
		$to      = $u_id;
		$subject = MAIL_SITE_NAME.DATE_MAIL_REGCONF;
		$u_no=$u_no;
		$link_pass=$link_pass;
//メール本文用
		$thankjoining = DATE_MAIL_THANKJOINING;
		$placc = DATE_MAIL_PLACC;
		$plcopy = DATE_MAIL_PLCOPY;
		$hello = DATE_MAIL_HELLO;
		$keisho = DATE_MAIL_KEISHO;
		$forpc = DATE_MAIL_FORPC;
		$formb = DATE_MAIL_FORMOBILES;
		$teamlm = DATE_MAIL_TEAMLOVETALK;
		$webteam = DATE_MAIL_WEBTEAM;
		$add = DATE_MAIL_ADD;
		$delmail = DATE_MAIL_PLDELMAIL;
		$forpc = DATE_MAIL_FORPC;
		$formb = DATE_MAIL_FORMOBILES;
		$from_address=ADMIN_MAIL;
		$from_string=FROM_STRING;
		$server_url=SERVER_URL;


 $subject  = mb_convert_encoding( $subject, "iso-2022-jp", "auto" );

$message =<<<EOM
$hello{$u_name}$keisho

$thankjoining
$placc
$plcopy

http://{$server_url}index.php?action=pre_confirm&company_id={$company_id}&link_pass={$link_pass}\n


$delmail

$teamlm


=========================================================
$webteam
$add
TEL：045-306-9506
FAX：03-6862-5814
Email：$from_address
=========================================================
EOM;

$message  = mb_convert_encoding( $message, "iso-2022-jp", "auto" );

//Linuxの場合
//$add_header = mb_convert_encoding( "From:".$from_address, "iso-2022-jp", "auto" );
$add_header = "From:$from_address";
//$add_header = mb_encode_mimeheader($from_string, "UTF-7", "Q") . " <" . $from_address . ">";

mb_send_mail($to, $subject, $message, $add_header);

	}

//----------------------------------------------------
//----------------------------------------------------
// ユーザー仮登録者へメール送信

function mail_to_pre_member_user($u_id,$user_id,$u_name,$link_pass){
		$to      = $u_id;
		$subject = MAIL_SITE_NAME.DATE_MAIL_REGCONF;
		$u_no=$u_no;
		$link_pass=$link_pass;
//メール本文用
		$thankjoining = DATE_MAIL_THANKJOINING;
		$placc = DATE_MAIL_PLACC;
		$plcopy = DATE_MAIL_PLCOPY;
		$hello = DATE_MAIL_HELLO;
		$keisho = DATE_MAIL_KEISHO;
		$forpc = DATE_MAIL_FORPC;
		$formb = DATE_MAIL_FORMOBILES;
		$teamlm = DATE_MAIL_TEAMLOVETALK;
		$webteam = DATE_MAIL_WEBTEAM;
		$add = DATE_MAIL_ADD;
		$delmail = DATE_MAIL_PLDELMAIL;
		$forpc = DATE_MAIL_FORPC;
		$formb = DATE_MAIL_FORMOBILES;
		$from_address=ADMIN_MAIL;
		$from_string=FROM_STRING;
		$server_url=SERVER_URL;


 $subject  = mb_convert_encoding( $subject, "iso-2022-jp", "auto" );

$message =<<<EOM
$hello{$u_name}$keisho

$thankjoining
$placc
$plcopy

http://{$server_url}index.php?action=pre_confirm_user&user_id={$user_id}&link_pass={$link_pass}\n


$delmail

$teamlm

=========================================================
$webteam
$add
TEL：045-306-9506
FAX：03-6862-5814
Email：$from_address
=========================================================
EOM;

$message  = mb_convert_encoding( $message, "iso-2022-jp", "auto" );

//Linuxの場合
//$add_header = mb_convert_encoding( "From:".$from_address, "iso-2022-jp", "auto" );
$add_header = "From:$from_address";
//$add_header = mb_encode_mimeheader($from_string, "UTF-7", "Q") . " <" . $from_address . ">";

mb_send_mail($to, $subject, $message, $add_header);

	}
	
	//----------------------------------------------------
	// 新規会社登録完了時に管理者へメール送信
	
function mail_to_new_company_admin($company_data){
		$to      = $company_data['email'];
		$subject = MAIL_NEW_COMPANY_SUBJECT;
		$from_address = ADMIN_MAIL;
		$from_string = FROM_STRING;
		
		//メール本文用
		$keisho = DATE_MAIL_KEISHO;
		$webteam = DATE_MAIL_WEBTEAM;
		$add = DATE_MAIL_ADD;
		
		$server_url = SERVER_URL;
		$thanks = MAIL_NEW_COMPANY_THANKS;
		$login = MAIL_NEW_COMPANY_LOGIN;
		
		$group_id_title = MAIL_NEW_COMPANY_GROUPID;
		$group_id = $company_data['company_group_id'];
		
		$password = MAIL_NEW_COMPANY_PASSWORD;
		$smartphone_app = MAIL_NEW_COMPANY_SMARTPHONE_APP;
		$support = MAIL_NEW_COMPANY_SUPPORT;
		$notice = MAIL_NEW_COMPANY_NOTICE;
	
	
		$subject  = mb_convert_encoding( $subject, "iso-2022-jp", "auto" );
	
		$message =<<<EOM
${company_data['contact_person_last_name']}${company_data['contact_person_first_name']}$keisho

$thanks
		
$login
		
https://{$server_url}index.php?action=login

$group_id_title

$group_id

$password
		
https://{$server_url}index.php?action=password

----------------------------------------------------------------
$smartphone_app
		
----------------------------------------------------------------
$support

-------------------------------------------------------
$notice
		
=========================================================
$webteam
		
$add
TEL：045-306-9506
FAX：03-6862-5814
Email：$from_address
=========================================================
EOM;
	
		$message  = mb_convert_encoding( $message, "iso-2022-jp", "auto" );
	
		//Linuxの場合
		//$add_header = mb_convert_encoding( "From:".$from_address, "iso-2022-jp", "auto" );
		$add_header = "From:$from_address";
		//$add_header = mb_encode_mimeheader($from_string, "UTF-7", "Q") . " <" . $from_address . ">";
	
		mb_send_mail($to, $subject, $message, $add_header);
	
	}
	
//画像アップロード関数
//$image_id はただ単に画像の名前がかぶらないようにidをつけている
function image_upload($company_id, $upload_file){

//Windows用	$image_dir = SERVER_IMAGE_PATH.$company_id.'\\';
	$image_dir = SERVER_IMAGE_PATH.$company_id.'/';
	
	//会社ごとのディレクトリがあるかどうか調べる　調べてなかったら中身のディレクトリも合わせて新しく作る
	if(!file_exists($image_dir)){
			mkdir($image_dir,0777,true);
	}

	//ファイル名はファイルをアップロードした時間で名前をつける
	$now = date("ymd_His_$image_id");     
	$type = $upload_file['type'];	
	$ext = str_replace('image/', '', $type);
	
	$file_name=$now.'.'.$ext;
	
	$image_path = $image_dir.$file_name;
						
	move_uploaded_file($upload_file["tmp_name"], $image_path);
						
	//リサイズ 最大横30px　縦30pxまで
	image_resize($image_path, ICON_WIDTH, ICON_WIDTH, $company_id, $file_name);
	
	//リサイズ前の画像破棄
	unlink($image_path);
	
	$image_info = array($file_name, $image_path);
	return($image_info);
}

//アップロード画像のリサイズ
function image_resize($orig_file, $resize_width, $resize_height, $company_id, $file_name)
{
	// GDライブラリがインストールされているか
	if (!extension_loaded('gd')) {
	    // エラー処理
	    return false;	
	}

	// 画像情報取得
	$result = getimagesize($orig_file);
	list($orig_width, $orig_height, $image_type) = $result;
	
	// 画像をコピー
	switch ($image_type) {
	    // 1 IMAGETYPE_GIF
	    // 2 IMAGETYPE_JPEG
	    // 3 IMAGETYPE_PNG
            case 1: $im = imagecreatefromgif($orig_file); break;
            case 2: $im = imagecreatefromjpeg($orig_file);  break;
            case 3: $im = imagecreatefrompng($orig_file); break;
            default:return false;
        }	

	// コピー先となる空の画像作成
	//タテヨコ比が狂わないようにリサイズの画像サイズを決定
	list($new_width, $new_height) = image_size($resize_width, $resize_height, $orig_width, $orig_height);
	
	$new_image = imagecreatetruecolor($new_width, $new_height);
//        if (!$new_file) {
         if (!$new_image) {
             // エラー処理 
	    // 不要な画像リソースを保持するメモリを解放する
            imagedestroy($im);
    	return false;
        }
	// GIF、PNGの場合、透過処理の対応を行う
	if (($image_type == 1) OR ($image_type==3)) {
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
        }

	// コピー画像を指定サイズで作成
 	if (!imagecopyresampled($new_image, $im, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height)) {
            //不要な画像リソースを保持するメモリを解放する
            imagedestroy($im);
            imagedestroy($new_image);
            return false;
        }

        // コピー画像を保存
	// $new_image : 画像データ
	// $new_fname : 保存先と画像名
        // クオリティ
        
		//会社ごとのリサイズディレクトリがあるかどうか調べる 調べてなかったら新しく作る
// Windows用		$image_dir=SERVER_IMAGE_PATH.$company_id.'\resized\\';
		$image_dir=SERVER_IMAGE_PATH.$company_id.'/resized/';
		if(!file_exists($image_dir)){
				mkdir($image_dir);
			}
        
        $new_fname = $image_dir.$file_name;
			
        switch ($image_type) {
	    // 1 IMAGETYPE_GIF
	    // 2 IMAGETYPE_JPEG
	    // 3 IMAGETYPE_PNG
            case 1: $result = imagegif($new_image, $new_fname); break;
            case 2: $result = imagejpeg($new_image, $new_fname, 100); break;
            case 3: $result = imagepng($new_image, $new_fname, 0); break;
            default:return false;
        }

        if (!$result) {
            // エラー処理 
	    // 不要な画像リソースを保持するメモリを解放する
            imagedestroy($im);
            imagedestroy($new_image);
            return false;
        }

	// 不要になった画像データ削除
	imagedestroy($im);
        imagedestroy($new_image);

}


function image_size($w, $h, $width, $height){
	
	$newwidth = 0; // 新しい横幅
	$newheight = 0; // 新しい縦幅
	
	// 両方オーバーしていた場合
	if ($h < $height && $w < $width){
		if ($w < $h) {
		  $newwidth = $w;
		  $newheight = $height * ($w / $width); 
	 	} else if ($h < $w) {
		  $newwidth = $width * ($h / $height);
		  $newheight = $h; 
	 	} else { 
	   		if ($width <= $height) {
	   			$newwidth = $width * ($h / $height);
	   			$newheight = $h; 
	  		} else if($height < $width) {
			   $newwidth = $w;
			   $newheight = $height * ($w / $width); 
	  		}
	 	}
	} else if ($height < $h && $width < $w) { // 両方オーバーしていない場合 
		 $newwidth = $width;
		 $newheight = $height; 
	} else if ($h < $height && $width <= $w) {
		// 縦がオーバー、横は新しい横より短い場合
		// 縦がオーバー、横は同じ長さの場合
	 	$newwidth = $width * ($h / $height);
	 	$newheight = $h; 
	} else if ($height <= $h && $w < $width) {
		// 縦が新しい縦より短く、横はオーバーしている場合
		// 縦は同じ長さ、横はオーバーしている場合
	 	$newwidth = $w;
	 	$newheight = $height * ($w / $width); 
	} else if ($height == $h && $width < $w) {
		// 横が新しい横より短く、縦は同じ長さの場合 
	 	$newwidth = $width * ($h / $height);
	 	$newheight = $h; 
	} else if ($height < $h && $width = $w) { 
		// 縦が新しい縦より短く、横は同じ長さの場合
	 	$newwidth = $w;
	 	$newheight = $height * ($w / $width); 
	} else {
		// 縦も横も、新しい長さと同じ長さの場合
		// または、縦と横が同じ長さで、かつ最大サイズを超えない場合
	 	$newwidth = $width;
	 	$newheight = $height;
	}
	
	$array = Array($newwidth,$newheight);
	return($array);
   }

 //画像のSmartyアサイン用配列を作る
function make_image_array($data){ 
	for($i=1; $i<4; $i++){
		$image_name="image_name".$i;
		$image_title="image_title".$i;
			$image_info[$i]=array($data[0]->$image_name,$data[0]->$image_title);
	}
	return $image_info;	
}

/*　画像のアップロード
 * @param $_FILES //ファイル情報
 * @param $datas //入力された情報
 * @return $image_name //画像のURL　http://はなし
 */
function try_image_upload($files, $datas){

	//初期化
	$image_name = NULL;
		
	//新しくアップロードされた画像ファイル
	if($files["upload_file"]["tmp_name"]){
			
		$upload_file=$files["upload_file"];
			
		list($file_name, $image_path) = image_upload($datas['id'], $upload_file);
			
		$image_name = SERVER_URL.'uploaded/images/'.$datas['id'].'/resized/'.$file_name;										
			
			
	}elseif($datas['image_name']){

		$image_name = $datas['image_name'];
	}
												
	return $image_name;
}


//----------------------------------------------------
// オートログイン処理
	function auto_login_data($auto_u_id){
	
	    try {
        $dbh = SingletonPDO::connect();
        //$dbh->beginTransaction();
		
	    // idとパスワードでユーザーテーブルを検索
    	$sql = "SELECT * FROM user
            	 WHERE u_id = '$auto_u_id'";

	$res=$dbh->prepare($sql);
	$res->execute();
		
   	$idArray = $res->fetchAll(PDO::FETCH_ASSOC);

	return $idArray;
        // コミット
        //$dbh->commit();

	}catch (PDOException $e) {
	
	//$dbh->rollback();
	die($e->getMessage());    
	}
 }	
 
 //CSV出力用の関数
	function make_csv_line($values){
		foreach($values as $i =>$value){
			if ((strpos($value, ',')  !== false) ||
	           (strpos($value, '"')  !== false) ||
	           (strpos($value, ' ')  !== false) ||
	           (strpos($value, "\t") !== false) ||
	           (strpos($value, "\n") !== false) ||
	           (strpos($value, "\r") !== false)) {
	           $values[$i] = '"' . str_replace('"', '""', $value) . '"';
	       }			
		}
		return implode(',',$values)."\n";
	}
//----------------------------------------------------
// IS_NEW 新着かどうかの処理
	function is_new($topicList){
	
	$is_new_time=IS_NEW;
	
	$now=time();

	for($i=0,$num_datas=count($topicList);$i<$num_datas;$i++){

	$newest_res_Date=Topic::getNewDate($topicList[$i]->thread_id);

	$time_rag=$now-$newest_res_Date;

   if($time_rag < $is_new_time*60*60){
	$topicList[$i]->isNew="<img src=\"templates/image/new.gif\" alt=\"新着\"/>";
	}else{
	$topicList[$i]->isNew='';
   	}
	}	
	return $topicList;
	}
	function isLogin($loginId,$password){
		$idArray=Driver::login($loginId,$password);
		// 存在すれば、ログイン成功
		if(isSet($idArray[0]['last_name'])) {
			return true;
		}else{
			return false;
		}
	}
	/**
	 * エジソンの中継サーバーへの送信に失敗した際にメールを送信する
	 * 送信JSON、受信JSON、HTTPステータス
	 * @param string $to
	 * @param string $sendJson
	 * @param string $response
	 * @param string $httpStatus
	 */
	function mail_about_error($to, $sendJson, $response, $httpStatus){
		$subject = "中継サーバーへの送信エラー発生のお知らせ";
	
		//メール本文用
		$bodyTitle = "エラーが発生しました。以下が送信・エラー内容です";
		$sendJsonTitle = "送信データ：";
		$responseTitle = "受信データ:";
		$httpStatusTitle = "HTTPステータス：";
	
	
		$message =<<<EOM
$bodyTitle
$sendJsonTitle $sendJson
$responseTitle $response
$httpStatusTitle $httpStatus
EOM;
		
		$from_address=ADMIN_MAIL;
		//Linuxの場合
		$add_header = "From:$from_address\r\n";
		
		mb_send_mail($to, $subject, $message, $add_header, "-f". ADMIN_MAIL);
	
	}
	
	/**
	 * エジソンの中継サーバーへの処理中にDBエラーが発生した場合、エラー内容を送信する
	 * @param string $to
	 * @param string $errorBody
	 */
	function mail_about_db_error($to,$errorBody){
		$subject = "データベースエラー発生のお知らせ";
	
		//メール本文用
		$bodyTitle = "データベースエラーが発生しました。以下が送信・エラー内容です";
		$errorTitle = "エラー内容：";
		
	
		$message =<<<EOM
$bodyTitle
$errorTitle $errorBody
EOM;
	
		$from_address=ADMIN_MAIL;
		//Linuxの場合
		$add_header = "From:$from_address\r\n";
	
		mb_send_mail($to, $subject, $message, $add_header, "-f". ADMIN_MAIL);
	
	}
	
	/**
	 * 引数で受け取った中継サーバーとの通信の送信JSON、レスポンス、HTTPステータスをエジソン、OCに宛にメールを送る
	 * @param string $sendJson
	 * @param string $response
	 * @param string $httpStatus
	 */
	function mail_to_edison_oc_about_error($sendJson, $response, $httpStatus){
		mail_about_error(OC_MAILADDRESS ,$sendJson, $response, $httpStatus);
		mail_about_error(EDISON_MAILADDRESS ,$sendJson, $response, $httpStatus);
	}
	
	/**
	 * 引数で受け取ったDBエラー内容をエジソン、OCに宛にメールを送る
	 * @param string $errorBody
	 */
	function mail_to_edison_oc_about_db_error($errorBody){
		mail_about_db_error(OC_MAILADDRESS ,$errorBody);
		mail_about_db_error(EDISON_MAILADDRESS ,$errorBody);
	}
	
	/**
	 * 引数で受け取った年月日の翌日を返却する
	 * @param string $year;
	 * @param string $month;
	 * @param string $day;
	 */
	function getNextDay($year, $month, $day){
		if($day >= date('t',strtotime($year . sprintf("%02d",$month)))){
		//日にちがその月の月末日と同じ、またはそれ以上の場合、翌月の1日にする
		$day = 1;
		if($month == "12"){
			//12月だった場合は翌年1月に設定
			$year = $year + 1;
			$month = 1;
		}else{
			$month = $month+1;
		}
		}else{
			$day = $day+1;
		}
		return array($year, $month, $day);
	}
	
?>