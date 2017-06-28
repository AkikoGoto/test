<?php

	try{
			
		if(!empty($_POST['driver_id'])){

			$driver_id=htmlentities($_POST['driver_id'],ENT_QUOTES, mb_internal_encoding());
			$date_id = htmlentities( $_POST['date'],ENT_QUOTES, mb_internal_encoding());
			$_POST['demand_time_year'] = htmlentities($_POST['demand_time_year'],ENT_QUOTES, mb_internal_encoding());
			$_POST['demand_time_month'] = htmlentities($_POST['demand_time_month'],ENT_QUOTES, mb_internal_encoding());
			$_POST['demand_time_day'] = htmlentities($_POST['demand_time_day'],ENT_QUOTES, mb_internal_encoding());
			
			//データベースから既存のデータを取り出す
			$datas = concrete::exist_demand_time($driver_id,$date_id);
//			var_dump($datas);
			
			$demand_time_id = $datas['demand_time_id'];
			
			$demand_time_year=date('Y', strtotime($datas['date']));
			$demand_time_month=date('n', strtotime($datas['date']));
			$demand_time_day=date('j', strtotime($datas['date']));
			
			$this_year = date('Y');
			$this_month = date('n');
			$today = date('j');
			
			//年
			for($i=5; $i>=0; $i--){
				$select_menu_year[$i] = $this_year-$i;
			}
			
			//月
			for($j=1; $j<13; $j++){
				$select_menu_month[$j] = $j;
			}
			
			//日
			for($k=1; $k<32; $k++){
				$select_menu_day[$k] = $k;
			}
			
			//時
			for($l=1; $l<25; $l++){
				$select_menu_hour[$l] = $l;
			}
			
			//分
			for($m=0; $m<60; $m++){
				$select_menu_minit[$m] = $m;
			}
			
			$start_year = date('Y', strtotime($datas['attendance_time']));
			$start_month = date('n', strtotime($datas['attendance_time']));
			$start_day = date('j', strtotime($datas['attendance_time']));
			$start_hour = date('G', strtotime($datas['attendance_time']));
			$start_minit = date('i', strtotime($datas['attendance_time']));
			
			if (!empty($datas['leaving_time'])) {
				$end_year = date('Y', strtotime($datas['leaving_time']));
				$end_month = date('n', strtotime($datas['leaving_time']));
				$end_day = date('j', strtotime($datas['leaving_time']));
				$end_hour = date('G', strtotime($datas['leaving_time']));
				$end_minit = date('i', strtotime($datas['leaving_time']));
			} else {
				$end_year = date('Y', strtotime($datas['attendance_time']));
				$end_month = date('n', strtotime($datas['attendance_time']));
				$end_day = date('j', strtotime($datas['attendance_time']));
				$end_hour = date('G', strtotime($datas['attendance_time']));
				$end_minit = date('i', strtotime($datas['attendance_time']));
			}
			
			//編集したスタートタイムがあれば定義
			if($datas['start_time']){
				
				$modified_start_year = date('Y', strtotime($datas['start_time']));
				$modified_start_month = date('n', strtotime($datas['start_time']));
				$modified_start_day = date('j', strtotime($datas['start_time']));
				$modified_start_hour = date('G', strtotime($datas['start_time']));
				$modified_start_minit = date('i', strtotime($datas['start_time']));
				
				$smarty->assign('modified_start_year',$modified_start_year);
				$smarty->assign('modified_start_month',$modified_start_month);
				$smarty->assign('modified_start_day',$modified_start_day);
				$smarty->assign('modified_start_hour',$modified_start_hour);
				$smarty->assign('modified_start_minit',$modified_start_minit);
				
			}

			//編集したエンドタイムがあれば定義
			if($datas['end_time']){
				
				$modified_end_year = date('Y', strtotime($datas['end_time']));
				$modified_end_month = date('n', strtotime($datas['end_time']));
				$modified_end_day = date('j', strtotime($datas['end_time']));
				$modified_end_hour = date('G', strtotime($datas['end_time']));
				$modified_end_minit = date('i', strtotime($datas['end_time']));
					
				$smarty->assign('modified_end_year',$modified_end_year);
				$smarty->assign('modified_end_month',$modified_end_month);
				$smarty->assign('modified_end_day',$modified_end_day);
				$smarty->assign('modified_end_hour',$modified_end_hour);
				$smarty->assign('modified_end_minit',$modified_end_minit);
					
			}
			
			if(!empty($_SESSION['demand_from_year'])){
				
				//セッション（日付、開始）
				$smarty->assign('demand_from_year',$_SESSION['demand_from_year']);
				$smarty->assign('demand_from_month',$_SESSION['demand_from_month']);
				$smarty->assign('demand_from_day',$_SESSION['demand_from_day']);
				$smarty->assign('demand_from_hour',$_SESSION['demand_from_hour']);
				$smarty->assign('demand_from_minit',$_SESSION['demand_from_minit']);
				
				//セッション（日付：終了）
				$smarty->assign('demand_to_year',$_SESSION['demand_to_year']);
				$smarty->assign('demand_to_month',$_SESSION['demand_to_month']);
				$smarty->assign('demand_to_day',$_SESSION['demand_to_day']);
				$smarty->assign('demand_to_hour',$_SESSION['demand_to_hour']);
				$smarty->assign('demand_to_minit',$_SESSION['demand_to_minit']);
						
				//エラーで戻ってきているときは、エラー前の情報表示
				$smarty->assign('session',$session);
				
			}
		}else{
	
			//IDがPOSTされていない場合
			print "IDが送られていません";
		}
	
	}catch(Exception $e){
	
		$message=$e->getMessage();
	}
	
	$date = $demand_time_year.' 年 '.$demand_time_month.' 月 '.$demand_time_day.' 日';
	
	//list　複数の変数への代入を行う
	list($data, $links)=make_page_link($datas);
	
	/*現在の時間*/
	$smarty->assign('this_year',$this_year);
	$smarty->assign('this_month',$this_month);
	$smarty->assign('today',$today);
	
	//修正前の時間及び、修正されていない時の、修正後の時間
	//start_time
	$smarty->assign('start_year',$start_year);
	$smarty->assign('start_month',$start_month);
	$smarty->assign('start_day',$start_day);
	$smarty->assign('start_hour',$start_hour);
	$smarty->assign('start_minit',$start_minit);
	//end_time
	$smarty->assign('end_year',$end_year);
	$smarty->assign('end_month',$end_month);
	$smarty->assign('end_day',$end_day);
	$smarty->assign('end_hour',$end_hour);
	$smarty->assign('end_minit',$end_minit);
	
	
	//foreachで繰り返すセレクトメニュー用の日付
	$smarty->assign('select_menu_year',$select_menu_year);
	$smarty->assign('select_menu_month',$select_menu_month);
	$smarty->assign('select_menu_day',$select_menu_day);
	$smarty->assign('select_menu_hour',$select_menu_hour);
	$smarty->assign('select_menu_minit',$select_menu_minit);
	
	$smarty->assign("concrete_attendance_id",$datas['concrete_attendance_id']);
	$smarty->assign("demand_time_id",$demand_time_id);
	$smarty->assign("date",$date);
	$smarty->assign("data",$datas);
	$smarty->assign("data",$data);
	$smarty->assign("driver_id",$driver_id);
	$smarty->assign('session_address',$session_address);
	$smarty->assign("links",$links['all']);
	$smarty->assign("filename","concrete/demand_time.html");
	$smarty->display("template.html");

?>