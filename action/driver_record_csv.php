<?php
/**
 * 日報をcsvで出力するアクション
 */

		
	//ドライバーIDが来ているか
	if($_GET['driver_id']){
		$driver_id=sanitizeGet($_GET['driver_id']);
	}
	//会社IDが来ているか
	if($_GET['company_id']){
		$company_id=sanitizeGet($_GET['company_id']);
	}
	//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
	driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);
	
	$driver_name = Driver::getNameById($driver_id);


	$time_get = get_time_from_and_to_ymdhm();
	$time_from = $time_get["0"];
	$time_to = $time_get["1"];
	
    
	//記録の取得
	$dataList=Driver::getStatusById($driver_id, $record_no, $time_from, $time_to);

	//ステータス名の取得
	$workingStatuses = Data::getStatusByCompanyId($company_id);
	$workingStatus = $workingStatuses[0];
	if($dataList != NULL){
						
		
		$today = date('Y').(_).date('m').(_).date('d');
		$file = 'report'.(_).$today.'.csv';					
		file_put_contents($file, $datas);
		header('Content-Disposition: attachment; filename="'. $file . '"');
		header('Content-Type: text/csv;');

		//データのフィールド名
		$head_line_array = array(DRIVER_NAME, DRIVER_STATUS, COMMON_JYUSYO, LATITUDE, LONGITUDE, SPEED, START_TIME, END_TIME, ROUTE_DEVIATED,
							COMMON_CREATE_DATE, COMMON_UPDATE );
							
		$head_line = make_csv_line($head_line_array);
		$head_line_converted=mb_convert_encoding($head_line, "SJIS-win", "UTF-8");
		print $head_line_converted;
					
		foreach($dataList[0] as $each_data) {		
			
				$data['driver_name'] = $driver_name[0]['last_name'].' '.$driver_name[0]['first_name'];
				
				if($each_data['status']==1){
					if(MODE){
						$data['status'] = OFFICE_START;
					}else{
						$data['status'] = $workingStatus->action_1;
					}
				}elseif($each_data['status']==2){
					if(MODE){
						$data['status'] = SCENE_START;
					}else{
						$data['status'] = $workingStatus->action_2;
					}
				}elseif($each_data['status']==3){
					if(MODE){
						$data['status'] = FROM_SCENE_START;
					}else{
						$data['status'] = $workingStatus->action_3;
					}
				}elseif($each_data['status']==4){
					if(MODE){
						$data['status'] = OFFICE_RETURN;
					}else{
						$data['status'] = $workingStatus->action_4;
					}
				}elseif($each_data['status']==5){
					$data['status'] = LUNCH;
				}elseif($each_data['status']==6){
					$data['status'] = CONTACT_HOLD;
				}elseif($each_data['status']==7){
					$data['status'] = CAR_WASH_HOLD;
				}elseif($each_data['status']==8){
					$data['status'] = CAR_WASH;
				}elseif($each_data['status']==9){
					$data['status'] = FEED;
				}elseif($each_data['status']==10){
					$data['status'] = COPY;
				}elseif($each_data['status']==11){
					$data['status'] = OTHER;
				}
				

				$each_data['address'] = preg_replace("/&minus;/", '-', $each_data['address']);

				
				$data['address'] = $each_data['address'];
				$data['latitude'] = $each_data['latitude'];
				$data['longitude'] = $each_data['longitude'];
				$data['speed'] = $each_data['speed'];
				$data['start'] = $each_data['start'];
				$data['end'] = $each_data['end'];
				
				if($each_data['is_deviated'] == '1'){
					$data['is_deviated'] = ROUTE_DEVIATED;
				}else{
					$data['is_deviated'] = '';
				}
				
				$data['created'] = $each_data['created'];
				$data['updated'] = $each_data['updated'];
			
				$csv_data= make_csv_line($data);
		
				//データの文字コード変換が必要な場合、ここで変換します。
				//ここではUTF-8からSJISへ変換しています。
		
				$data_converted=mb_convert_encoding($csv_data, "SJIS-win", "UTF-8");
				print $data_converted;
				
		

			}
			
			//PHP がバイナリデータを誤認識し、コード変換することの無いようにします。
			mb_http_output('pass');
			

	}else{
		
		$smarty->assign("message","該当するデータがありません。");				
		
		$smarty->assign("driver_id",$driver_id);
		$smarty->assign("company_id",$company_id);
		
		if(MODE){
			$smarty->assign("filename","concrete/driver_record_map.html");
		}else{
			$smarty->assign("filename","driver_record_map.html");
		}
		$smarty->display('template.html');
		exit();		
	}
    
