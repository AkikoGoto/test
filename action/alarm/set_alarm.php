<?php

//会社IDが来ているか
if($_GET['company_id']){	
	$company_id=sanitizeGet($_GET['company_id']);
	}
	
if($_GET['set_alarmId']){
		$set_alarmId=sanitizeGet($_GET['set_alarmId'],ENT_QUOTES);
	}

if($_GET['set_driverId']){
		$set_driverId=sanitizeGet($_GET['set_driverId'],ENT_QUOTES);
	}
	
	
	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	
try{
		$status = 'NEW';
		
	//アラームIDが指定されていれば、編集のため、元のデータを表示
	if($set_driverId && $set_alarmId){
		$status='EDIT';
		
		$datas = alarms::getDrivers($set_driverId , $set_alarmId);
		$smarty->assign("datas",$datas[0]);
		
		$date = str_replace (  "-", "/" , $datas[0]['date']);
		$date = str_replace (  " 00:00:00", "" , $date);
		$smarty->assign("date",$date);
		
		//〇〇：〇〇(何時何分を表す)
		$setting_time = substr($datas[0]['mail_time'], -5 , strlen($datas[0]['mail_time']));
		$db_time = array($datas[0]['mail_timing']);
		
					
		$three = 3;
		$time = substr ( $setting_time, 0 , strlen($setting_time)-$three);
		$minute = substr ( $setting_time, 3 , strlen($setting_time)-$three);
		
		
		//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
		if(!empty($branch_manager_id)){
					
			//このドライバーが許可された営業所のドライバー情報か
			branch_manager_driver_auth($branch_manager_id, $set_driverId);
		
		}
	}

	//company_idが〇〇のドライバーの情報を取得する
	if($is_branch_manager && $branch_manager_id){
		
		$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);
		$dataList = Driver::getDrivers($company_id, $branch_id);
		
	}elseif(!empty($u_id)){

		$dataList = Driver::getDrivers($company_id);
	
	}
	
//	$dataList=Driver::getDrivers($company_id);
	
				
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
	
	//フォームなどで戻った時のために、セッションにデータを格納
	if($_SESSION['setAlarm']){
		$smarty->assign("session",$_SESSION['setAlarm']);
	}
	
	//list　複数の変数への代入を行う
list($data, $links)=make_page_link($dataList);
//モーダルウィンドウのPrettyPopinを追加
$css = "<link rel=\"stylesheet\" href=\"templates/css/prettyPopin.css\" type=\"text/css\" media=\"screen\">";
$css .= "<link rel=\"stylesheet\" href=\"templates/css/pc.css\"	type=\"text/css\" media=\"screen\">";

$smarty->assign("js","<script type=\"text/javascript\" src=\"templates/js/jquery.prettyPopin_mod.js\"></script>");
$smarty->assign("css",$css);
$smarty->assign("time",$time);
$smarty->assign("mail_time",$mail_time);
$smarty->assign("minute",$minute);
$smarty->assign("array",$array);
$smarty->assign("status",$status);
$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("id",$company_id);
$smarty->assign('session_address',$session_address);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","alarm/set_alarm.html");
$smarty->display("template.html");

?>