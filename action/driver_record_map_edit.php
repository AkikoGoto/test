<?php
//ドライバー情報入力

//会社ID、ドライバーIDが来ているか
if($_GET['driver_id']){	
	$driver_id=sanitizeGet($_GET['driver_id'],ENT_QUOTES, 'Shift_JIS');
	}
	
if($_GET['company_id']){	
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES, 'Shift_JIS');
	}

if ($_GET['id']){
	$id=sanitizeGet($_GET['id'],ENT_QUOTES, 'Shift_JIS');
}

	
	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $company_id);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $driver_id);
	
	}else{
	
		//会社のユーザーIDと、編集するIDがあっているか確認	
		driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);		

		//ドライバー本人がログインしている場合、会社が編集許可をONにしていなければエラー表示
		driver_editing_banned_check($driver_id,$session_driver_id,$company_id,$u_id);
	
	}
	
	//作業ステータス
	$workingStatus = Data::getStatusByCompanyId($company_id);
	$smarty->assign("working_status", $workingStatus[0]);
	
	try{
		//編集画面の場合、現在のデータを表示
		if($id){
			//現在のデータの取得

			$dataList=Driver::EditStatusById($id);

			$driver_record_map_start_time=$dataList[0]['start'];
			if(($driver_record_map_start_time == "0000-00-00 00:00:00")||($driver_record_map_start_time == "")){
				
			}else{
				$smarty->assign("driver_record_map_start_time", $driver_record_map_start_time);
			}

			$driver_record_map_end_time=$dataList[0]['end'];
			
			if(($driver_record_map_end_time == "0000-00-00 00:00:00")||($driver_record_map_end_time == "")){						
				
			}else{
				$smarty->assign("driver_record_map_end_time", $driver_record_map_end_time);
			}
			
			//編集かどうかのフラグ
			$smarty->assign("edit",1);
		}
		
	}catch(Exception $e){
		
			$message=$e->getMessage();

	}
//取得した記録の時刻を年に分解、繰り返し
$db_year = date('Y', strtotime($dataList[0]['created']));
$this_year=date('Y');
	  for($i=5; $i>=0; $i--){
	        $select_menu_year[$i] = $this_year-$i;
		}
//月に分解、繰り返し
$db_month = date('n', strtotime($dataList[0]['created']));
	  for($j=1; $j<13; $j++){
	        $select_menu_month[$j] = $j;
		}

//日に分解、繰り返し
$db_day = date('d', strtotime($dataList[0]['created']));
	  for($k=1; $k<32; $k++){
	        $select_menu_day[$k] = $k;
		}

//時間に分解、繰り返し
$db_hour = date('G', strtotime($dataList[0]['created']));
	  for($l=1; $l<25; $l++){
	        $select_menu_hour[$l] = $l;
		}

//分に分解、繰り返し
$db_minit = date('i', strtotime($dataList[0]['created']));
	  for($m=0; $m<60; $m++){
	        $select_menu_minit[$m] = $m;
		}
/*
//秒に分解、繰り返し
$db_seconds = date('s', strtotime($dataList[0]['created']));
	  for($m=0; $m<60; $m++){
	        $select_menu_seconds[$m] = $m;
		}
*/
//セッションのid判定
try{
	if(!empty($_SESSION['driver_record_map_id'])){
		if($dataList[0]['id'] == $_SESSION['driver_record_map_id']){
			//セッション
			$smarty->assign('driver_record_map_status',$_SESSION['driver_record_map_status']);
			$smarty->assign('driver_record_map_address',$_SESSION['driver_record_map_adderss']);
			$smarty->assign('driver_record_map_speed',$_SESSION['driver_record_map_speed']);
			$smarty->assign('driver_record_map_sales',$_SESSION['driver_record_map_sales']);
			$smarty->assign('driver_record_map_detail',$_SESSION['driver_record_map_detail']);
			//セッション（日付、開始）
			$smarty->assign('driver_record_map_from_year',$_SESSION['driver_record_map_from_year']);
			$smarty->assign('driver_record_map_from_month',$_SESSION['driver_record_map_from_month']);
			$smarty->assign('driver_record_map_from_day',$_SESSION['driver_record_map_from_day']);
			$smarty->assign('driver_record_map_from_hour',$_SESSION['driver_record_map_from_hour']);
			$smarty->assign('driver_record_map_from_minit',$_SESSION['driver_record_map_from_minit']);
/*			$smarty->assign('driver_record_map_from_seconds',$_SESSION['driver_record_map_from_seconds']);*/
			//ﾁｪｯｸﾎﾞｯｸｽのセッション
			$smarty->assign('driver_record_map_start_time',$_SESSION['driver_record_map_start_time']);
			$smarty->assign('driver_record_map_end_time',$_SESSION['driver_record_map_end_time']);
/*			セッション（日付：終了）
			$smarty->assign('driver_record_map_to_year',$_SESSION['driver_record_map_to_year']);
			$smarty->assign('driver_record_map_to_month',$_SESSION['driver_record_map_to_month']);
			$smarty->assign('driver_record_map_to_day',$_SESSION['driver_record_map_to_day']);
			$smarty->assign('driver_record_map_to_hour',$_SESSION['driver_record_map_to_hour']);
			$smarty->assign('driver_record_map_to_minit',$_SESSION['driver_record_map_to_minit']);
			$smarty->assign('driver_record_map_to_seconds',$_SESSION['driver_record_map_to_seconds']);*/
		}
	}
}catch(Exception $e){
		
			$message=$e->getMessage();

	}



//DBからのデータ
$smarty->assign('data',$dataList);

//テーブルから分解して取得した時刻
$smarty->assign('db_year',$db_year);
$smarty->assign('db_month',$db_month);
$smarty->assign('db_day',$db_day);
$smarty->assign('db_hour',$db_hour);
$smarty->assign('db_minit',$db_minit);
/*$smarty->assign('db_seconds',$db_seconds);*/

//foreachで繰り返すセレクトメニュー用の日付
$smarty->assign('select_menu_year',$select_menu_year);
$smarty->assign('select_menu_month',$select_menu_month);
$smarty->assign('select_menu_day',$select_menu_day);
$smarty->assign('select_menu_hour',$select_menu_hour);
$smarty->assign('select_menu_minit',$select_menu_minit);
/*$smarty->assign('select_menu_seconds',$select_menu_seconds);*/

//geocoding
$smarty->assign("google_map_js",$google_map_js);
$smarty->assign("geocoding_js",$geocoding_js);
$smarty->assign("geocode_address",$geocode_address);
$smarty->assign("geocoding_js",$geocoding_js);

$smarty->assign("driver_id",$driver_id);
$smarty->assign("company_id",$company_id);
$smarty->assign("filename","driver_record_map_edit.html");
$smarty->display("template.html");

?>