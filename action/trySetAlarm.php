<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php

if($_GET['set_alarmId']){
	$set_alarmId=sanitizeGet($_GET['set_alarmId'],ENT_QUOTES);
	$smarty->assign("set_alarmId",$set_alarmId);
}

if($_GET['set_driverId']){
	$set_driverId=sanitizeGet($_GET['set_driverId'],ENT_QUOTES);
	$smarty->assign("set_driverId",$set_driverId);
}

		$all = ($_POST);
		
		//最終的なメールを送る時間の設定
		if( $all['mail_before_or_after'] == 0 ){
			$decrease_time = "00:".$all['mail_timing'];
			$post_time = $all['time_from_hour'].':'.$all['time_from_minit'];
			$setting_time = date('H:i',strtotime($post_time)-strtotime($decrease_time)-60*60);
		
		}elseif( $all['mail_before_or_after'] == 1 ){
			$increase_time = array($all['mail_timing']);
			$post_time = $all['time_from_hour'].':'.$all['time_from_minit'];
			$explode = explode(":", $post_time);
			$setting_time = date('H:i',mktime($explode[0],$explode[1]+$increase_time[0]));
		}
		
		//指定時間にいたら、いなかったらの判定
		if($all['alert_when_there'] == '0'){
			$all['alert_when_not_there'] = $all['alert_when_there'];
			$all['alert_when_not_there'] = 1;
		}
		
		
		$day = array('毎週月曜','毎週火曜','毎週水曜','毎週木曜','毎週金曜','毎週土曜','毎週日曜');
 		//毎日か、毎週○○か、日付かの判定部分
 		if($all['daily'] == '毎日'){
 			$all['weekly'] = '';
 			$all['date'] = '';
 			$all['mail_time'] = $all['daily'].'/'.$setting_time;
 		}
 		
 		for( $i=0 ; $i<7 ; $i++ ){
 		
 			if($all['daily'] == $day[$i]){
 				$all['weekly'] = $all['daily'];
 				$all['daily'] = '';
 				$all['date'] = '';
 				$all['mail_time'] = $all['weekly'].'/'.$setting_time;
 				break;
 			}
 		}
 		
 		if( !($all['daily'] == '毎日') && !($all['daily']== '')){
 			$all['date'] = $all['daily'];
 			$all['daily'] = '';
 			$all['weekly'] = '';
 			$all['mail_time'] = $all['date'].'/'.$setting_time;
 		}		
	
 	$array=array('company_id','time_from_hour','time_from_minit','status');
 	$keys=array();
 	$keys=array_merge($array,$alerms_array);
	
  	foreach($keys as $key){
 		$datas[$key]=htmlentities($all[$key],ENT_QUOTES, mb_internal_encoding());
  	}
 	
 	$ip=getenv("REMOTE_ADDR");
	
 	$ban_ip=Data::banip();
	
 	//禁止リストにあるIPとの照合
 	if($ip==$ban_ip){
 		
		//メッセージ画面を表示する
	      header('Location:index.php?action=message&situation=ban_ip');
	
	}else{
		
		try{
			
		//入力データ検証
	
		$form_validate=new Validate();
		
		$errors=$form_validate->validate_form_driverCheck($datas);
		
		//エラーの吐き出し処理
		if($errors){
		
				$form_validate->show_form($errors);
				$form_validate->lasturl='index.php?action=putBranch';
				
			}else{
				
				//Smartyへの割り当て
				foreach($datas as $key => $value){
							
					//ガラケーの場合は一度UTF8データに変換する
					if(is_garapagos()){
						$value=mb_convert_encoding($value, "UTF-8", "SJIS");
					}
					$smarty->assign("$key",$value);
				}
								
				// 自データの編集か
				if($_POST['id']){						
										
					//会社のユーザーIDと、編集するIDがあっているか確認
					user_auth($u_id,$datas['id']);	
					$status='EDIT';
					$smarty->assign("status",$status);

				}
				
				//住所のジオコーディング 住所があればする
				if(!empty($datas['address'])){
					$smarty->assign("js",'<script src="'.GOOGLE_MAP.'"type="text/javascript"></script>
									<script type="text/javascript" src="'.GEOCODING_JS.'"></script>');
						
					$geocode_address = $datas['address'];
					$geocode_address ='<div id="geocode_address">'.$geocode_address.'</div>';
						
					$smarty->assign("geocode_address",$geocode_address);
					$smarty->assign("onload_js","onload=\"doGeocode()\"");
// 						$smarty->assign("google_map_js",$google_map_js);
// 						$smarty->assign("geocoding_js",$geocoding_js);
					
				}
					
				$driver_name = Driver::getNameById($datas['driver_id']);
				
					$target="setAlarmdb";
					$smarty->assign("driver_name",$driver_name[0]['last_name'].' '.$driver_name[0]['first_name']);
					$smarty->assign("filename","trySetAlarm.html");
					$smarty->assign("datas",$datas);
					$smarty->assign("prefecture_name",$prefecture_name);
					$smarty->assign("service_names",$service_names);
					$smarty->assign("drivers",$drivers);
					$smarty->assign("target",$target);
					
					$smarty->display("template.html");
			
			}
		  	
		}catch(Exception $e){
		die($e->getMessage());
		
		}

 	}
	

	
?>