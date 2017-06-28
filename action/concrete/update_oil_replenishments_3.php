<?php
if(!empty($_POST['concrete_attendance_id'])){ 

	$concrete_attendance_id = htmlentities($_POST['concrete_attendance_id'],ENT_QUOTES, mb_internal_encoding());
	
	$ip=getenv("REMOTE_ADDR");
	
	$ban_ip=Data::banip();
	
	//禁止リストにあるIPとの照合
	if($ip==$ban_ip){
	
		//メッセージ画面を表示する
		header('Location:index.php?action=message&situation=ban_ip');
	
	}else{
	
		try{
			
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
			
			if(!empty($_SESSION['oil_concrete_attendance_id'])){
				
				//セッション（日付、開始）
				$smarty->assign('oil_from_hour',$_SESSION['oil_from_hour']);
				$smarty->assign('oil_from_minit',$_SESSION['oil_from_minit']);
				
				//セッション（日付：終了）
				$smarty->assign('oil_to_hour',$_SESSION['oil_to_hour']);
				$smarty->assign('oil_to_minit',$_SESSION['oil_to_minit']);
				
				$smarty->assign('oil_concrete_attendance_id',$_SESSION['oil_concrete_attendance_id']);
				$smarty->assign('oil_replenishment',$_SESSION['oil_replenishment']);
				
				//エラーで戻ってきているときは、エラー前の情報表示
				$smarty->assign('session',$session);
				
			}
			
			$concrete_oil_data = concrete::get_concrete_attendance_date($concrete_attendance_id);
			$count=count($concrete_oil_data);
			
			for($i=0;$i<$count;$i++){
				if($concrete_oil_data[$i]['oil_replenishment_type']==3){
					$status='edit';
					$concrete_dates = explode( "-", $concrete_oil_data[$i]['date']);
					$check_year = $concrete_dates[0];
					$check_month = $concrete_dates[1];
					$check_day = $concrete_dates[2];
					
					$concrete_start_time = substr( $concrete_oil_data[$i]['start_time'], -8 );
					$concrete_end_time = substr( $concrete_oil_data[$i]['end_time'], -8);
					
					$concrete_oil_replenishment=$concrete_oil_data[$i]['oil_replenishment'];
					$smarty->assign('concrete_oil_replenishment',$concrete_oil_replenishment);
					$smarty->assign('status',$status);
					
				}else{
					$concrete_dates = explode( "-", $concrete_oil_data[$i]['date']);
					$check_year = $concrete_dates[0];
					$check_month = $concrete_dates[1];
					$check_day = $concrete_dates[2];
													
				}
			}
					
			if($concrete_start_time){
				$concrete_start_time = explode( ":", $concrete_start_time);
				$concrete_start_time_hour = $concrete_start_time[0];
				//時間の値の最初が「0」だったとき、0をなくす 「06」⇒「6」
				if(substr($concrete_start_time_hour,0,1)==0){
					$concrete_start_time_hour = substr($concrete_start_time_hour,1);
				}
				
				$concrete_start_time_minute = $concrete_start_time[1];
				//時間の値の最初が「0」だったとき、0をなくす 「06」⇒「6」
				if(substr($concrete_start_time_minute,0,1)==0){
					$concrete_start_time_minute = substr($concrete_start_time_minute,1);
				}
							
				$smarty->assign('concrete_start_time_hour',$concrete_start_time_hour);
				$smarty->assign('concrete_start_time_minute',$concrete_start_time_minute);
			}
			
			if($concrete_end_time){
				$concrete_end_time = explode( ":", $concrete_end_time);
				$concrete_end_time_hour = $concrete_end_time[0];
				//時間の値の最初が「0」だったとき、0をなくす 「06」⇒「6」
				if(substr($concrete_end_time_hour,0,1)==0){
					$concrete_end_time_hour = substr($concrete_end_time_hour,1);
				}
				
				$concrete_end_time_minute = $concrete_end_time[1];
				//時間の値の最初が「0」だったとき、0をなくす 「06」⇒「6」
				if(substr($concrete_end_time_minute,0,1)==0){
					$concrete_end_time_minute = substr($concrete_end_time_minute,1);
				}
				
				
				$smarty->assign('concrete_end_time_hour',$concrete_end_time_hour);
				$smarty->assign('concrete_end_time_minute',$concrete_end_time_minute);
			}
						
			$check_date=$check_year.'年'.$check_month.'月'.$check_day.'日';
			
			$smarty->assign('this_year',$this_year);
			$smarty->assign('this_month',$this_month);
			$smarty->assign('this_day',$this_day);
			
			$smarty->assign('check_year',$check_year);
			$smarty->assign('check_month',$check_month);
			$smarty->assign('check_day',$check_day);
			
			
			//foreachで繰り返すセレクトメニュー用の日付
			$smarty->assign('select_menu_year',$select_menu_year);
			$smarty->assign('select_menu_month',$select_menu_month);
			$smarty->assign('select_menu_day',$select_menu_day);
			$smarty->assign('select_menu_hour',$select_menu_hour);
			$smarty->assign('select_menu_minit',$select_menu_minit);
			
			//軽油(3)のデータの箇所
			$smarty->assign("check_date",$check_date);
			$smarty->assign("concrete_attendance_id",$concrete_attendance_id);
						
			$smarty->assign("filename","concrete/update_oil_replenishments_3.html");
			$smarty->display('template.html');
			
			
		}catch(Exception $e){
				
			$message=$e->getMessage();
		}
	}
}else{
	echo 'test1';
}
	?>