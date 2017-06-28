<?php

if($_GET['set_alarmId']){
	$set_alarmId=sanitizeGet($_GET['set_alarmId'],ENT_QUOTES);
	$smarty->assign("set_alarmId",$set_alarmId);
}

if($_GET['set_driverId']){
	$set_driverId=sanitizeGet($_GET['set_driverId'],ENT_QUOTES);
	$smarty->assign("set_driverId",$set_driverId);
}

		$all_post_data = ($_POST);
		
		//最終的なメールを送る時間の設定
		$setting_time = date('H:i',mktime($all_post_data['time_from_hour'],$all_post_data['time_from_minit']));
		
		//指定時間にいたら、いなかったらの判定
		if($all_post_data['alert_when_there'] == 1){
			$all_post_data['alert_when_not_there'] = 1;
			$all_post_data['alert_when_there'] = 0;
		}elseif($all_post_data['alert_when_there'] == 2){
			$all_post_data['alert_when_there'] = 1;
			$all_post_data['alert_when_not_there'] = 0;
		}
		
		$today = date('Y/m/d');
		$time = date('H:i');
		//現在の時刻
		$a = $today.' '.$setting_time;
				
		$day = array('毎週月曜','毎週火曜','毎週水曜','毎週木曜','毎週金曜','毎週土曜','毎週日曜');
		//毎日か
 		if($all_post_data['daily'] == '毎日'){
 			$all_post_data['weekly'] = '';
 			$all_post_data['date'] = '';
 			$all_post_data['mail_time'] = $all_post_data['daily'].'/'.$setting_time;
 			
 			if($all_post_data['status'] == 'NEW'){

 				//今日の時間か、明日の時間かを設定
 				if( $time < $setting_time ){
 				
 					$all_post_data['alarm_time'] = date("Y-m-d").' '.$setting_time;
 				
 				}elseif( $time > $setting_time){
 					
 					$all_post_data['alarm_time'] = date("Y-m-d", strtotime("+1 day")).' '.$setting_time;
 				}
 				
 			}elseif($all_post_data['status'] == 'EDIT'){
				
 				
 				//今日の時間か、明日の時間かを設定
 				if( $time < $setting_time ){

 					//今日の時間
 					$all_post_data['alarm_time'] = date("Y-m-d").' '.$setting_time;
 						
 				}elseif( $time > $setting_time){
 					
 					//明日の日付を取得
 					$all_post_data['alarm_time'] = date("Y-m-d", strtotime("+1 day")).' '.$setting_time;
 				}
 				
 			}
 			
 		}
 		
 		//毎週〇〇か
 		for( $i=0 ; $i<7 ; $i++ ){
 		
 			if($all_post_data['daily'] == $day[$i]){
 				
 				$all_post_data['weekly'] = $all_post_data['daily'];
 				$all_post_data['daily'] = '';
 				$all_post_data['date'] = '';
 				$all_post_data['mail_time'] = $all_post_data['weekly'].'/'.$setting_time;
 				
 				$week = array("月", "火", "水", "木", "金", "土", "日");
 				$weeks = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
 				$target = str_replace ( "毎週", "" , $all_post_data['weekly']);
 				$target = str_replace ( "曜", "" , $target);
 				
 				for($i = 0 ; $i<7 ; $i++){
 						
 					if($week[$i] == $target){
 						//何曜日か
 						$this_week = $weeks[$i];
 						$next_alarm = 'next '.$weeks[$i];
 					}
 				}
 				
 				if($all_post_data['status'] == 'NEW'){
 					
 					//今日の時間か、来週の時間かを設定
 					if( $time < $setting_time && $today == date('Y/m/d',strtotime($this_week))){
						
 						//今日の時間
 						$all_post_data['alarm_time'] = date("Y-m-d").' '.$setting_time;
 							
 					}else{
 						
 						//来週の時間に変更
 						$all_post_data['alarm_time'] = date('Y-m-d',strtotime($next_alarm)).' '.$setting_time;
 					}
 					
 					
 				}elseif($all_post_data['status'] == 'EDIT'){
 					
 					//今日の時間か、来週の時間かを設定
 					if( $time < $setting_time && $today == date('Y/m/d',strtotime($this_week))){
 						
 						//今日の時間
 						$all_post_data['alarm_time'] = date("Y-m-d").' '.$setting_time;
 					
 					}else{
 						
 						//来週の時間に変更
 						$all_post_data['alarm_time'] = date('Y-m-d',strtotime($next_alarm)).' '.$setting_time;
 					}
 					
 				}
								
 				break;
 			}
 		}
 		
 		//指定した日にちか
 		if( !($all_post_data['daily'] == '毎日') && !($all_post_data['daily']== '')){
 			$all_post_data['date'] = $all_post_data['daily'];
 			$all_post_data['daily'] = '';
 			$all_post_data['weekly'] = '';
 			$all_post_data['mail_time'] = $all_post_data['date'].'/'.$setting_time;
 			$all_post_data['alarm_time'] = $all_post_data['date'].' '.$setting_time;
 		}	
 		
 	$array=array('company_id','time_from_hour','time_from_minit','status','alarm_time');
 	$keys=array();
 	$keys=array_merge($array,$alerms_array);
	
  	foreach($keys as $key){
 		$datas[$key]=htmlentities($all_post_data[$key],ENT_QUOTES, mb_internal_encoding());
 		$_SESSION['setAlarm'][$key]=$all_post_data[$key];
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
					$smarty->assign("filename","alarm/trySetAlarm.html");
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