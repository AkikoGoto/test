<?php
/**
 * 日報をcsvで出力するアクション
 */

		
	//ドライバーIDが来ているか
	if($_POST['driver_id']){
		$driver_id = htmlentities($_POST["driver_id"], ENT_QUOTES, mb_internal_encoding());
	}
	//ドライバーIDが来ているか
	if($_POST['company_id']){
		$company_id = htmlentities($_POST["company_id"], ENT_QUOTES, mb_internal_encoding());
	}
	
	//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
		
	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $company_id);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $driver_id);
	
	}else{

		driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);
	
	}
	
	$driver_name = Driver::getNameById($driver_id);

	$status = htmlentities($_POST['status'], ENT_QUOTES, mb_internal_encoding());
    
    $time_from_year = htmlentities($_POST["time_from_year"], ENT_QUOTES, mb_internal_encoding());
    $time_from_month = htmlentities($_POST["time_from_month"], ENT_QUOTES, mb_internal_encoding());
    $time_from_day = htmlentities($_POST["time_from_day"], ENT_QUOTES, mb_internal_encoding());
    
    $time_to_year = htmlentities($_POST["time_to_year"], ENT_QUOTES, mb_internal_encoding());
    $time_to_month = htmlentities($_POST["time_to_month"], ENT_QUOTES, mb_internal_encoding());
    $time_to_day = htmlentities($_POST["time_to_day"], ENT_QUOTES, mb_internal_encoding());
    
    //記録の取得
	$dataList = Work::getWorktimeByDate($driver_id, $status, $time_from_year, $time_from_month, $time_from_day, 
	 					$time_to_year, $time_to_month, $time_to_day);

	//ステータス名の取得
	$workingStatuses = Data::getStatusByCompanyId($company_id);
	$workingStatus = $workingStatuses[0];	 					
	 					
	//日報データの取得　出庫メータなど 
	$dayReportList = DayReport::getDayReportByDate($driver_id, $time_from_year, $time_from_month, 
						$time_from_day, $time_to_year, $time_to_month, $time_to_day);
						
	if($dataList != NULL){
						
		
		$today = date('Y').(_).date('m').(_).date('d');
		$file = 'report'.(_).$today.'.csv';					
		file_put_contents($file, $datas);
		header('Content-Disposition: attachment; filename="'. $file . '"');
		header('Content-Type: text/csv;');

		//データのフィールド名
		$head_line_array = array('id', DRIVER_NAME, START_TIME, END_TIME, DRIVER_STATUS, START_ADDRESS, END_ADDRESS,
							START_LATITUDE, START_LONGITUDE, END_LATITUDE, END_LONGITUDE, DISTANCE,
							PLATE_NUMBER, DESTINATION_COMPANY_NAME, AMOUNT, TOLL_FEE, DRIVER_COMMENT,
							COMMON_CREATE_DATE, COMMON_UPDATE, TOTAL_TIME);
							
		$head_line = make_csv_line($head_line_array);
		$head_line_converted=mb_convert_encoding($head_line, "SJIS-win", "UTF-8");
		print $head_line_converted;
				
		
		//記録を日付ごとにグループ分け
		$i=0;
	
		foreach ($dataList as $each_data){
			$each_start_date[$i] = substr($each_data['start'], 0, 10);
			if($i != 0){
				
				if($each_start_date[$i]==$each_start_date[$i-1]){
					$data_by_date[$j][]=$each_data;
				}else{ 
					$j++ ;		
					$data_by_date[$j][]=$each_data;	
				}
				
			}else{
				
				$j = 0;	
				$data_by_date[$i][]=$each_data;		
			
			}
		
			$i++ ;  
		
		}
	
		
		
		foreach ( $data_by_date as $each_date){
			
			//給油量、スタートメーターなどを日付に紐付け
			if($dayReportList !=null ){
				
				foreach($dayReportList as $each_day_report_list){

					if($each_day_report_list['drive_date']==substr($each_date[0]['start'],0,10)){
						$start_meter = $each_day_report_list['start_meter'];
						$arrival_meter = $each_day_report_list['arrival_meter'];
						$supplied_oil = $each_day_report_list['supplied_oil'];

					}
				
				}
			}
			
			
			$total_distance = 0;
			$total_time = 0;
			$break_time = 0;
		
			//走行距離と時間の合計と休憩時間を計算
			foreach($each_date as $data_calculate){
				$total_distance = + $data_calculate['distance'];
				$each_data_seconds = h2s($data_calculate['total_time']);
				$total_time = + $each_data_seconds;
				
				//休憩時間
				if($data_calculate['status']==4){
					$break_time = $each_data_seconds;
				}
			}
			
			$total_time = s2h($total_time);
			$break_time = s2h($break_time);
			
			//車庫到着時間を計算
			$temporary_array = $each_date;
			$final_data = array_pop($temporary_array);
			$arrival_garage_time = $final_data['end'];
		
						
			// Data			
			$fill = 0;
			foreach($each_date as $data) {		
				
				if($data['status']==1){
					$data['status'] = $workingStatus->action_1;
				}elseif($data['status']==2){
					$data['status'] = $workingStatus->action_2;
				}elseif($data['status']==3){
					$data['status'] = $workingStatus->action_3;
				}elseif($data['status']==4){
					$data['status'] = $workingStatus->action_4;
				}
				
				$data['driver_id']=$driver_name[0]['last_name'].' '.$driver_name[0]['first_name'];
				
				$csv_data= make_csv_line($data);
		
				//データの文字コード変換が必要な場合、ここで変換します。
				//ここではUTF-8からSJISへ変換しています。
		
				$data_converted=mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
				print $data_converted;
				
		

			}
			
			//PHP がバイナリデータを誤認識し、コード変換することの無いようにします。
			mb_http_output('pass');
			
		
		
		}

		//最後にファイルを削除
		unlink($file);
		

	}else{
		
		$smarty->assign("message","該当するデータがありません。");				
		
		$smarty->assign("driver_id",$driver_id);
		$smarty->assign("company_id",$company_id);
		
		
		$smarty->assign("filename","worktime.html");
		$smarty->display('template.html');
		exit();		
	}
    
