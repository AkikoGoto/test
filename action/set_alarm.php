<?php

//会社IDが来ているか
if($_GET['company_id']){	
	$id=sanitizeGet($_GET['company_id']);
	}
	
if($_GET['set_alarmId']){
		$set_alarmId=sanitizeGet($_GET['set_alarmId'],ENT_QUOTES);
	}

if($_GET['set_driverId']){
		$set_driverId=sanitizeGet($_GET['set_driverId'],ENT_QUOTES);
	}
	
	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id,$id);
	
try{
		$status = 'NEW';
		
	//配送先IDが指定されていれば、編集のため、元のデータを表示
	if($set_driverId && $set_alarmId){
		$status='EDIT';
		
		$datas = alarms::getDrivers($set_driverId , $set_alarmId);
		$smarty->assign("datas",$datas[0]);
		
	}

	//company_idが〇〇のドライバーの情報を取得する
	$dataList=Driver::getDrivers($id);
	
				
	}catch(Exception $e){
	
		$message=$e->getMessage();
	}
	
	$date = strtotime("now");
	$day = array('毎日','毎週月曜','毎週火曜','毎週水曜','毎週木曜','毎週金曜','毎週土曜','毎週日曜');
	
	//今日の日付から1ヶ月後までを出力
	for( $i = 0 ; $i<32 ; $i++ ){
		$str_date[$i] = date('Y/m/d',$date + $i *60*60*24);
	}
	
	$array = array_merge($day,$str_date);
	
	$date = str_replace (  "-", "/" , $datas[0]['date']);
	$date = str_replace (  " 00:00:00", "" , $date);
	
	
	$mail_time = substr($datas[0]['mail_time'], -5 , strlen($datas[0]['mail_time']));
	
	//指定時間
	if( $datas[0]['mail_before_or_after'] == 1 ){
		$increase_time = "00:".$datas[0]['mail_timing'];
		$post_time = $mail_time;
		$setting_time = date('H:i',strtotime($mail_time)-strtotime($increase_time)-60*60);
	
	}elseif( $datas[0]['mail_before_or_after'] == 0 ){
		$decrease_time = array($datas[0]['mail_timing']);
		$post_time = $mail_time;
		$explode = explode(":", $post_time);
		$setting_time = date('H:i',mktime($explode[0],$explode[1]+$decrease_time[0]));
	}
	
	$three = 3;
	$time = substr ( $setting_time, 0 , strlen($setting_time)-$three);
	$minute = substr ( $setting_time, 3 , strlen($setting_time)-$three);
	
	
	
	//list　複数の変数への代入を行う
list($data, $links)=make_page_link($dataList);
//モーダルウィンドウのPrettyPopinを追加
$css = "<link rel=\"stylesheet\" href=\"templates/css/prettyPopin.css\" type=\"text/css\" media=\"screen\">";
$css .= "<link rel=\"stylesheet\" href=\"templates/css/pc.css\"	type=\"text/css\" media=\"screen\">";

$smarty->assign("js","<script type=\"text/javascript\" src=\"templates/js/jquery.prettyPopin_mod.js\"></script>");
$smarty->assign("css",$css);
$smarty->assign("time",$time);
$smarty->assign("minute",$minute);
$smarty->assign("date",$date);
$smarty->assign("array",$array);
$smarty->assign("status",$status);
$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("id",$id);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","set_alarm.html");
$smarty->display("template.html");

?>