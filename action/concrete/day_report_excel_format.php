<?php
$reviser = new Excel_Reviser;
//保存文字コード（スクリプトとも合わせる）
$reviser->setInternalCharset('UTF-8');

//ドライバーIDが来ているか
if($_POST['driver_id'])
	$driver_id = htmlentities($_POST["driver_id"], ENT_QUOTES, mb_internal_encoding());

//company idが来ているか
if($_POST['company_id'])
	$company_id = htmlentities($_POST["company_id"], ENT_QUOTES, mb_internal_encoding());

//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);

$is_bill = false;
if ($_POST['is_bill'] == 1) {
	$is_bill = true;
}

	try{
		
		if(!empty($_POST['driver_id']) && $_POST['concrete_attendance_id']){
			
			foreach($daily_report as $key){
				$_POST[$key]=trim( mb_convert_kana($_POST[$key], "s"));
				$post_datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			}
			
			// driver_name
			$driver_names = concrete::driver_names($post_datas['driver_id']);
			$driver_name = $driver_names['last_name'].' '.$driver_names['first_name'];
			
			/************************************************
			 * 一日の日報全データ
			 ************************************************/
			$daily_report_data = concrete::daily_report_data($post_datas);
			if (empty($daily_report_data))
				header('Location:index.php?action=message_mobile&situation=fail_concrete_report');
 			$today_report_data = $daily_report_data[0];
			$concrete_attendance_id = $daily_report_data[0]['concrete_attendance_id'];
			
			/************************************************
			 * 給油
			 ************************************************/
			$oils = concrete::daily_oil_data($concrete_attendance_id);
			if (!empty($oils)) {
			
				$oil_1 = array();
				$oil_2 = array();
				$oil_3 = array();
				$oil_4 = array();
				foreach ($oils as $oil) {
					if ($oil['oil_replenishment_type'] == 1) {
						$oil_1 = $oil;
					} else if ($oil['oil_replenishment_type'] == 2) {
						$oil_2 = $oil;
					} else if ($oil['oil_replenishment_type'] == 3) {
						$oil_3 = $oil;
					} else if ($oil['oil_replenishment_type'] == 4) {
						$oil_4 = $oil;
					}
				}
				
				$oil_1_replenishment = "";
				$oil_1_replenishment = $oil_1['oil_replenishment'];
				$oil_1_start_time = separate_time($oil_1['start_time']);
				$oil_1_end_time = separate_time($oil_1['end_time']);
				
				$oil_2_replenishment = "";
				$oil_2_replenishment = $oil_2['oil_replenishment'];
				$oil_2_start_time = separate_time($oil_2['start_time']);
				$oil_2_end_time = separate_time($oil_2['end_time']);
				
				$oil_3_replenishment = $oil_3['oil_replenishment'];
				$oil_3_start_time = separate_time($oil_3['start_time']);
				$oil_3_end_time = separate_time($oil_3['end_time']);
				
				$oil_4_replenishment = $oil_4['oil_replenishment'];
				$oil_4_start_time = separate_time($oil_4['start_time']);
				$oil_4_end_time = separate_time($oil_4['end_time']);
				
			}
			
			/************************************************
			 * 現場データ
			 ************************************************/
			$scenes_data = concrete::scenes_data($concrete_attendance_id);
				
			/***********************************************
			 * 出勤データ
			 ***********************************************/
 			// 日付
 			$date = $today_report_data['date'];
 			$exploded_date = explode( "-", $date);
 			$display_year = $exploded_date[0];
 			$date_year = $exploded_date[0];
 			$date_month = $exploded_date[1];
 			$date_day = $exploded_date[2];
 			
 			if ($date_year == 2014) {
 				$date_year = "26";
 			} else if ($date_year > 2014) {
 				$more_year = $date_year - 2014;
 				$date_year = 26 + $more_year;
 			}
 			
 			$date_day = sprintf("%02d", $date_day);
 			$date_month = sprintf("%02d", $date_month);
 			$date_years = explode_number($date_year);
 			$date_months = explode_number($date_month);
 			$date_days = explode_number($date_day);
 			
 			//　ドア番号
 			$door_number = $today_report_data['door_number'];
 			$exploded_door = explode_number($door_number);
 			
 			if (count($exploded_door) == 3) {
 				$hundred_door = get_number_from_number_array($exploded_door, 0);
	 			$ten_door = get_number_from_number_array($exploded_door, 1);
	 			$one_door = get_number_from_number_array($exploded_door, 2);
 			} else if (count($exploded_door) == 2) {
	 			$hundred_door = "";
	 			$ten_door = get_number_from_number_array($exploded_door, 0);
	 			$one_door = get_number_from_number_array($exploded_door, 1);
 			} else if (count($exploded_door) == 1) {
	 			$hundred_door = "";
	 			$ten_door = "";
	 			$one_door = get_number_from_number_array($exploded_door, 0);
 			}
			
 			// 積込工場
 			$loading_factory = $today_report_data['loading_factory'];
 				
 			//　営業所名
 			$branch_name = $today_report_data['branch_name'];
 			
 			//配合
 			$mix = $today_report_data['mix'];
 			
 			// 出勤時間
 			$attendance_datetime = $today_report_data['attendance_time'];
 			//退勤時間
 			$leaving_datetime = $today_report_data['leaving_time'];
 			
 			$explode_attendance = explode( " ", $attendance_datetime);
 			$attendance_times = explode(":",$explode_attendance[1]);
 			$attendance_time = $attendance_times[0].$attendance_times[1];
 			$attendance_numbers = explode_number($attendance_time);
 			
 			$explode_leaving = explode( " ", $leaving_datetime);
 			$leaving_times = explode(":", $explode_leaving[1]);
 			$leaving_time = $leaving_times[0].$leaving_times[1];
 			$leaving_numbers = explode_number($leaving_time);
 			
 			/************************************************
 			 * 発着
 			 ************************************************/
 			// 往の発
 			$depart_leaving_time = $today_report_data['depart_leaving_time'];
 			$explode_depart_leaving_time = explode( " ", $depart_leaving_time);
 			$depart_leaving_times = explode(":", $explode_depart_leaving_time[1]);
 			$depart_leaving_time = $depart_leaving_times[0].':'.$depart_leaving_times[1];
 			
 			// 往の着
 			$depart_arrived_time = $today_report_data['depart_arrived_time'];
 			$explode_depart_arrived_time = explode( " ", $depart_arrived_time);
 			$depart_arrived_times = explode(":", $explode_depart_arrived_time[1]);
 			$depart_arrived_time = $depart_arrived_times[0].':'.$depart_arrived_times[1];
 			
 			// 復の発
 			$return_leaving_time = $today_report_data['return_leaving_time'];
 			if (!empty($return_leaving_time)) { 
 				$explode_return_leaving_time = explode( " ", $return_leaving_time);
	 			$return_leaving_times = explode(":", $explode_return_leaving_time[1]);
	 			$return_leaving_time = $return_leaving_times[0].':'.$return_leaving_times[1];
 			}
 			
 			// 復の着
 			$return_arrived_time = $today_report_data['return_arrived_time'];
 			if (!empty($return_arrived_time)) {
	 			$explode_return_arrived_time = explode( " ", $return_arrived_time);
	 			$return_arrived_times = explode(":", $explode_return_arrived_time[1]);
	 			$return_arrived_time = $return_arrived_times[0].':'.$return_arrived_times[1];
 			}
 			
 			/***********************************************
 			 * メーター
 			 ***********************************************/
 			// 始業メーター
 			$start_meter = $today_report_data['start_meter'];
 			$start_meter_count = strlen($start_meter);
 			$small_start_meter = $start_meter;
 			$big_start_meter = 0;
 			if ($start_meter_count > 3) {
	 			$split_start_meter = $start_meter_count - 3;
	 			$big_start_meter = substr( $start_meter, 0, $split_start_meter);
	 			$small_start_meter = substr( $start_meter, $split_start_meter, 3);
 			}
 			
 			// 終業メーター
 			$end_meter = $today_report_data['end_meter'];
 			$end_meter_count = strlen($end_meter);
 			$small_end_meter = $end_meter;
 			$big_end_meter = 0;
 			if ($end_meter_count > 3) {
	 			$split_end_meter = $end_meter_count - 3;
	 			$big_end_meter = substr( $end_meter, 0, $split_end_meter);
	 			$small_end_meter = substr( $end_meter, $split_end_meter, 3);
 			}
 			//走行メーター
 			if (!empty($start_meter) && !empty($end_meter)) {
 			
	 			$real_meters = $end_meter - $start_meter;
	 			$real_meter_count = strlen($real_meters);
	 			
	 			if ($real_meter_count >= 7) {
	 				$real_meters = round($real_meters);
	 			}
	 			
	 			if ($real_meter_count >= 3) {
		 			$split_real_meter = $real_meter_count - 3;
		 			$big_real_meter = substr( $real_meters, 0, $split_real_meter);
		 			$small_real_meter = substr( $real_meters, $split_real_meter, 3);
	 			} else {
	 				$big_real_meter = "";
		 			$small_real_meter = $real_meters;
	 			}
	 			
	 			$count_big_real_meter = strlen($big_real_meter);
	 			$counter_big_real_meter = 4 - $count_big_real_meter;
				if ($counter_big_real_meter != 0) {
	 				for ($i = 0; $i < $counter_big_real_meter; $i++) {
	 					if ($counter_big_real_meter == 3 && $i == 2)
		 					$big_real_meter = $big_real_meter;
		 				else 
		 					$big_real_meter = $big_real_meter;
	 				}
	 			}
	 			
 			}
 			
 			// 工場内始業メーター
 			$start_in_factory_meter = $today_report_data['start_in_factory_meter'];
 			// 工場内終業メーター
 			$end_in_factory_meter = $today_report_data['end_in_factory_meter'];
 			if (!empty($end_in_factory_meter) &&
	 			!empty($start_in_factory_meter)) {
	 			//　工場内走行メーター
	 			$real_meter_in_factory = $end_in_factory_meter - $start_in_factory_meter;
	 			// 1/2
	 			$half_real_meter_in_factory = $real_meter_in_factory / 2;
 			}
 			
 			/************************************************
 			 * 修正時間
 			 ************************************************/
 			$attendance_display_times_hour = $attendance_times[0];
 			$attendance_display_times_minute = $attendance_times[1];
 			$leaving_display_times_hour = $leaving_times[0];
 			$leaving_display_times_minute = $leaving_times[1];
 			if ($is_bill) {
	 			
	 			$demand_start_time = $today_report_data['demand_start_time'];
	 			$demand_end_time = $today_report_data['demand_end_time'];
	 			
	 			if (!empty($demand_start_time)) {
	 				$exploded_demand_start_datetime = explode(" ", $demand_start_time);
	 				$exploded_demand_start_time = explode(":", $exploded_demand_start_datetime[1]);
		 			$attendance_display_times_hour = $exploded_demand_start_time[0];
		 			$attendance_display_times_minute = $exploded_demand_start_time[1];
	 			}
	 			
	 			if (!empty($demand_end_time)) {
	 				$exploded_demand_end_datetime = explode(" ", $demand_end_time);
	 				$exploded_demand_end_time = explode(":", $exploded_demand_end_datetime[1]);
		 			$leaving_display_times_hour = $exploded_demand_end_time[0];
		 			$leaving_display_times_minute = $exploded_demand_end_time[1];
	 			}
	 			
 			}
 			
 			/************************************************
 			 * 運搬
 			 ************************************************/
 			$delivery_times = count( $scenes_data );
			
			/************************************************
			 * 業務履歴
			 ************************************************/
			$concrete_statuses = array ( 5, 6, 7, 8, 10, 11);
			$lunch = array();
			$waiting_for_contact = array();
			$waiting_for_washing = array();
			$washing = array();
			$copy = array();
			$other = array();
			if (!empty($concrete_statuses)) {
				
				foreach ($concrete_statuses as $concrete_status ) {
					$dataList = Concrete::getStatusStartEnd(
															$concrete_status,
															$post_datas['driver_id'],
															$date,
															$attendance_datetime,
															$leaving_datetime);
															
					if($dataList){
						//スタートの時間
						$start_data = $dataList[0]['start_time'];
						$strToTimeStart = strtotime($start_data);
						$start = date(Hi, $strToTimeStart);
						
						//終了の時間
						$end_data = $dataList[0]['end_time'];
						$strToTimeEnd = strtotime($end_data);
						$end = date(Hi, $strToTimeEnd);
						
						switch ($concrete_status) {
							case 5:
								$lunch['start'] = $start;
								$lunch['end'] = $end;
							break;
							case 6:
								$waiting_for_contact['start'] = $start;
								$waiting_for_contact['end'] = $end;
							break;
							case 7:
								$waiting_for_washing['start'] = $start;
								$waiting_for_washing['end'] = $end;
							break;
							case 8:
								$washing['start'] = $start;
								$washing['end'] = $end;
							break;
							case 10:
								$copy['start'] = $start;
								$copy['end'] = $end;
							break;
							case 11:
								$other['start'] = $start;
								$other['end'] = $end;
							break;
						}
						
					}
				}
				
			}
			
			/************************************************
			 * コメント
			 ************************************************/
			$other_detail = "";
			$comments = "";
			$work_comments = Concrete::getCommentsByWorkId( $post_datas['driver_id'],
															$date,
															$attendance_datetime,
															$leaving_datetime );
			$is_first_comment = true;
			$is_first_comment_for_waiting_for_contact = true;
			foreach ( $work_comments as $work_comment) {
				
//				if ($work_comment['status'] == 6) {
//					if ($is_first_comment_for_waiting_for_contact) {
//						$other_detail .= "【連絡待ち】";
//					}
//					$other_detail .= $work_comment['comment']."(".$comment_time.")";
//					$is_first_comment_for_waiting_for_contact = false;
//				} else {
					$comment_datetime = $work_comment['created'];
					$strToTimeComment = strtotime( $comment_datetime );
					$comment_time = date("H時i分", $strToTimeComment);
//					if (!$is_first_comment)
	//					$comments .= "、";
					$comments .= $work_comment['comment']."(".$comment_time.")";
					$is_first_comment = false;
//				}
			}
			
			/************************************************
			 * その他
			 ************************************************/
			$is_first_other_content = true;
			$other_datas = concrete::other_data( $concrete_attendance_id );
			if (!empty($other_datas)) {
				foreach ($other_datas as $other_data) {
					$other_datetime = $other_data['created'];
					$strToTime = strtotime( $other_datetime );
					$other_time = date("H時i分", $strToTime);
					if (!$is_first_other_content) {
//						$other_detail .= "、";
						$other_detail .= "";
					} else {
						if (!empty($other_detail)) {
							$other_detail .= "\n";
						}
						$other_detail .= "【その他】";
					}
					$other_detail .= $other_data['other']."(".$other_time.")";
					$is_first_other_content = false;
				}
			}
			/************************************************
			 * 点検
			 ************************************************/
			$inspections = concrete::getInspection( $concrete_attendance_id );
			$problem_details = array();
			$is_first_inspection_detail_insert = true;
			
			$inspection_titles = array(
								'fuel_leaks' => '燃料',
								'engine' => 'エンジン',
								'brake_pedal' => 'ブレーキペダル',
								'brake_lever' => 'ブレーキレバー',
								'car_horn' => '警音器・窓ふき器　方向指示器',
								'defroster' => '計器・デフロスタ',
								'steering_handle' => 'かじ取りハンドル',
								'reflecting_mirror' => '後写鏡及び反射鏡',
								'air_brake' => 'エア･ブレーキ',
								'clutch' => 'クラッチ',
								'clip_bolt' => 'クリップボルト',
								'auxiliary_equipment' => '前照灯・方向指示器　車巾灯・登録番号標',
								'tire' => 'タイヤ',
								'radiator' => 'ラジエーター',
								'radiator_cap' => 'ラジエーターキャップ',
								'fan_belt' => 'ファンベルト',
								'oil' => 'オイル',
								'jet_cleaning' => '洗浄噴射装置',
								'brake_and_clutch_oil' => 'ブレーキ・オイル　クラッチ・オイル',
								'chassis_spring' => 'シャシバネ',
								'door_lock' => 'ドアロック　座席ベルト',
								'rear_clip_bolt' => 'クリップボルト（後部③）',
								'rear_air_tank' => 'エア・タンク',
								'rear_tire' => 'タイヤ（スペアタイヤ）',
								'rear_battery' => 'バッテリー',
								'rear_auxiliary_equipment' => '番号灯・方向指示器・尾灯・制動灯・後退灯・反射器　登録番号標・その他',
								'loading_device' => '物品積載装置',
								'car_bed' => '車体及び荷台',
								'gas_color' => '排気の色',
								'rear_chassis_spring' => 'シャシバネ（後部③）',
								'tachograph' => '運行記録計　その他計器',
								'alarm_tool' => '非常信号用具　停止表示板',
								'automobile_inspection_certificate' => '自動車検査証・保険証',
								'implement' => '工具');
			
			if (!empty($inspections)) {
				foreach ($inspections as $key => $value) {
					
					if ($key == "inspection_time")
						continue;
					
					$status = "";
					if ($value === 0) {
						$status = "否";
					} else if ($value == 1) {
						$status = "良";
					} else {
						$status = "否";
						if (!empty($other_detail)) {
							if ($is_first_inspection_detail_insert)
								$other_detail .= "\n";
//							else
//								$other_detail .= "、";
						}
						
						if ($is_first_inspection_detail_insert)
							$other_detail .= "【点検内容】";
						$is_first_inspection_detail_insert = false;
						$inspection_title = $inspection_titles[$key];
						$other_detail .= "[".$inspection_title."]".$value;
					}
					$inspections->$key = $status;
					
				}
				$strToInspectionTime = strtotime( $inspections->inspection_time . "- 5 min" );
				$inspection_time = date("H時i分", $strToInspectionTime);
				$strToInspectionEndTime = strtotime( $inspections->inspection_time );
				$inspection_end_time = date("H時i分", $strToInspectionEndTime);
			}
			
			/************************************************
			 * エアーチェック？
			 ************************************************/
			if(!($daily_report_data) && !($scenes_data) && !($other_data) && !($wash_data)){
				
				$form_validate = new Concrete_Validate();
				$form_validate->show_form($errors);
					
			}
			
		} else {
			$form_validate = new Concrete_Validate();
			$form_validate->show_form($errors);
		}
	
	}catch(Exception $e){
		
		$message=$e->getMessage();
		exit;
	
	}
	
	function explode_number ($number) {
		$count = strlen($number);
		$numbers = array();
		for ($i = 0; $i < $count; $i++) {
			$exploded_number = substr( $number, $i, 1);
			$numbers[] = $exploded_number;
		}
		return $numbers;
	}
	
	function get_number_from_number_array ( $array, $target_number ) {
		if (count($array) > $target_number) {
			return $array[$target_number];
		}
		return null;
	}
	
	function separate_time ( $datetime ) {
		
		$separated_datetime = explode(" ", $datetime);
		$time = $separated_datetime[1];
		$separated_time = explode(":", $time);
		$times = $separated_time[0].$separated_time[1];
		return $times;
		
	}
	
	function hour_minute ( $time ) {
		
		$strToTimeStart = strtotime($time);
		$new_time = date(Hi, $strToTimeStart);
		return $new_time;
		
	}
	
	function majar_zero ( $value ) {
		if ($value == 0)
			return "０";
			
		return $value;
	}
	
//$reviser->addString( $branch_name

/*
 * それぞれの項目の始まりの列と行を指定
 */
//出勤情報
$BASIC_LINE_ROW = 5;
//現場情報
$SCENE_START_ROW = 10;
//業務
$STATUSES_START_COLUMN = 34;
$STATUSES_END_COLUMN = 38;
//メーター
$METERS_START_COLUMN = 47;
$METERS_START_SUB_COLUMN = 51;
//工場内メーター
$METERS_IN_FACTORY_COLUMN = 46;

/*
 * 年月日、ドア番号、ドライバー名、出退勤時間
 */
//0だったらエクセルに何も表示されないので、大文字の０を表示させる
$date_years[1] = majar_zero($date_years[1]);
$date_months[0] = majar_zero($date_months[0]);
$date_months[1] = majar_zero($date_months[1]);
$date_days[0] = majar_zero($date_days[0]);
$date_days[1] = majar_zero($date_days[1]);
$hundred_door = majar_zero($hundred_door);
$ten_door = majar_zero($ten_door);
$one_door = majar_zero($one_door);
$attendance_numbers[0] = majar_zero($attendance_numbers[0]);
$attendance_numbers[1] = majar_zero($attendance_numbers[1]);
$attendance_numbers[2] = majar_zero($attendance_numbers[2]);
$attendance_numbers[3] = majar_zero($attendance_numbers[3]);
$leaving_numbers[0] = majar_zero($leaving_numbers[0]);
$leaving_numbers[1] = majar_zero($leaving_numbers[1]);
$leaving_numbers[2] = majar_zero($leaving_numbers[2]);
$leaving_numbers[3] = majar_zero($leaving_numbers[3]);
//
//var_dump($hundred_door);
//var_dump($ten_door);
//var_dump($one_door);
//exit;

$reviser->addString( 0, 2, 3, $branch_name);
$reviser->addString( 0, $BASIC_LINE_ROW, 0, $date_years[0]);
$reviser->addString( 0, $BASIC_LINE_ROW, 1, $date_years[1]);
$reviser->addString( 0, $BASIC_LINE_ROW, 2, $date_months[0]);
$reviser->addString( 0, $BASIC_LINE_ROW, 3, $date_months[1]);
$reviser->addString( 0, $BASIC_LINE_ROW, 4, $date_days[0]);
$reviser->addString( 0, $BASIC_LINE_ROW, 5, $date_days[1]);
$reviser->addString( 0, $BASIC_LINE_ROW, 6, $hundred_door);
$reviser->addString( 0, $BASIC_LINE_ROW, 7, $ten_door);
$reviser->addString( 0, $BASIC_LINE_ROW, 8, $one_door);
$reviser->addString( 0, $BASIC_LINE_ROW, 9, $driver_name);
$reviser->addString( 0, $BASIC_LINE_ROW, 23, $attendance_numbers[0]);
$reviser->addString( 0, $BASIC_LINE_ROW, 24, $attendance_numbers[1]);
$reviser->addString( 0, $BASIC_LINE_ROW, 25, $attendance_numbers[2]);
$reviser->addString( 0, $BASIC_LINE_ROW, 26, $attendance_numbers[3]);
$reviser->addString( 0, $BASIC_LINE_ROW, 27, $leaving_numbers[0]);
$reviser->addString( 0, $BASIC_LINE_ROW, 28, $leaving_numbers[1]);
$reviser->addString( 0, $BASIC_LINE_ROW, 29, $leaving_numbers[2]);
$reviser->addString( 0, $BASIC_LINE_ROW, 30, $leaving_numbers[3]);

/*
 * 現場情報
 */
$all_quantity = 0;
$scene_row = $SCENE_START_ROW;
$sheet_number = 0;
$scene_number = 0;
$MAX_SCENE_NUMBER = 10;
if (!empty($scenes_data)) {
	foreach ($scenes_data as $scene) {
		
		$all_quantity = $all_quantity + $scene['quantity'];
		$quantities = explode(".", $scene['quantity']);
		
		$destination_company = $scene['destination_company'];//会社名
		$scene_name = $scene['scene_name'];//現場名
		
		// 数量　左枠
		if (count($quantities) > 1) {
			$left_quantity = $quantities[0];
			$right_quantity = $quantities[1];
		} else {
			$left_quantity = $quantities[0];
			$right_quantity = "";
		}
		
		// 数量　右枠
		if (strlen($right_quantity) <= 1) {
			$right_quantity = $right_quantity;
		}
		
		if ($right_quantity == "" ||
			$right_quantity == "0" ||
			$right_quantity == null ) {
			
			$right_quantity = "０";
			
		}
		
		//積込状況
		$loading_state = "";
		if ($scene['loading_state'] == 1) $loading_state = "✓";
		
		//残水確認
		$remaining_water = "";
		if ($scene['remaining_water'] == 1) $remaining_water = "✓";
		
		//各時間
		$start_time = "";
		$arrived_time = "";
		$leaving_from_scene_time = "";
		$return_from_scene_time = "";
		if ($scene['start_time'])
			$start_time = hour_minute($scene['start_time']);
		if ($scene['arrived_time'])
			$arrived_time = hour_minute($scene['arrived_time']);
		if ($scene['leaving_from_scene_time'])
			$leaving_from_scene_time = hour_minute($scene['leaving_from_scene_time']);
		if ($scene['return_from_scene_time'])
			$return_from_scene_time = hour_minute($scene['return_from_scene_time']);
	
		$reviser->addString( $sheet_number, $scene_row, 1, $scene['number']);
		$reviser->addString( $sheet_number, $scene_row, 2, $destination_company);
		$reviser->addString( $sheet_number, $scene_row, 6, $scene_name);
		$reviser->addString( $sheet_number, $scene_row, 11, $left_quantity);
		$reviser->addString( $sheet_number, $scene_row, 12, $right_quantity);
		$reviser->addString( $sheet_number, $scene_row, 13, $start_time);
		$reviser->addString( $sheet_number, $scene_row, 17, $arrived_time);
		$reviser->addString( $sheet_number, $scene_row, 21, $leaving_from_scene_time);
		$reviser->addString( $sheet_number, $scene_row, 25, $return_from_scene_time);
		$reviser->addString( $sheet_number, $scene_row, 29, $remaining_water);
		$reviser->addString( $sheet_number, $scene_row, 30, $loading_state);
		
		$scene_row++;
		$scene_row++;
		$scene_number++;
		if ($scene_number > $MAX_SCENE_NUMBER) {
			$scene_number = 0;
			$scene_row = $SCENE_START_ROW;
			$sheet_number++;
			$reviser->addString( $sheet_number, 2, 3, $branch_name);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 0, $date_years[0]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 1, $date_years[1]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 2, $date_months[0]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 3, $date_months[1]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 4, $date_days[0]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 5, $date_days[1]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 6, $hundred_door);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 7, $ten_door);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 8, $one_door);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 9, $driver_name);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 23, $attendance_numbers[0]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 24, $attendance_numbers[1]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 25, $attendance_numbers[2]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 26, $attendance_numbers[3]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 27, $leaving_numbers[0]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 28, $leaving_numbers[1]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 29, $leaving_numbers[2]);
			$reviser->addString( $sheet_number, $BASIC_LINE_ROW, 30, $leaving_numbers[3]);
		}
	}
}

/*
 * 業務記録
 */
$reviser->addString( 0, 8, $STATUSES_START_COLUMN, $lunch['start']);
$reviser->addString( 0, 8, $STATUSES_END_COLUMN, $lunch['end']);
$reviser->addString( 0, 11, $STATUSES_START_COLUMN, $waiting_for_contact['start']);
$reviser->addString( 0, 11, $STATUSES_END_COLUMN, $waiting_for_contact['end']);
$reviser->addString( 0, 14, $STATUSES_START_COLUMN, $waiting_for_washing['start']);
$reviser->addString( 0, 14, $STATUSES_END_COLUMN, $waiting_for_washing['end']);
$reviser->addString( 0, 17, $STATUSES_START_COLUMN, $washing['start']);
$reviser->addString( 0, 17, $STATUSES_END_COLUMN, $washing['end']);
$reviser->addString( 0, 20, $STATUSES_START_COLUMN, $oil_1_start_time);
$reviser->addString( 0, 20, $STATUSES_END_COLUMN, $oil_1_end_time);
$reviser->addString( 0, 23, $STATUSES_START_COLUMN, $copy['start']);
$reviser->addString( 0, 23, $STATUSES_END_COLUMN, $copy['end']);
$reviser->addString( 0, 26, $STATUSES_START_COLUMN, $other['start']);
$reviser->addString( 0, 26, $STATUSES_END_COLUMN, $other['end']);
$reviser->addString( 0, 29, $STATUSES_START_COLUMN, $oil_2_start_time);
$reviser->addString( 0, 29, $STATUSES_END_COLUMN, $oil_2_end_time);

/*
 * メーター
 */
$reviser->addString( 0, 6, $METERS_START_COLUMN, $big_end_meter);
$reviser->addString( 0, 6, $METERS_START_SUB_COLUMN, $small_end_meter);
$reviser->addString( 0, 8, $METERS_START_COLUMN, $big_start_meter);
$reviser->addString( 0, 8, $METERS_START_SUB_COLUMN, $small_start_meter);
$reviser->addString( 0, 10, $METERS_START_COLUMN, $big_real_meter);
$reviser->addString( 0, 10, $METERS_START_SUB_COLUMN, $small_real_meter);

/*
 * 工場内走行メーター
 */
$reviser->addString( 0, 12, $METERS_IN_FACTORY_COLUMN, $end_in_factory_meter);
$reviser->addString( 0, 14, $METERS_IN_FACTORY_COLUMN, $start_in_factory_meter);
$reviser->addString( 0, 16, $METERS_IN_FACTORY_COLUMN, $real_meter_in_factory);
$reviser->addString( 0, 12, 51, $half_real_meter_in_factory);

/*
 * 給油
 */
$reviser->addString( 0, 18, $METERS_START_COLUMN, $oil_1_replenishment);
$reviser->addString( 0, 20, $METERS_START_COLUMN, $oil_2_replenishment);
$reviser->addString( 0, 22, $METERS_START_COLUMN, $oil_3_replenishment);
$reviser->addString( 0, 24, $METERS_START_COLUMN, $oil_4_replenishment);

/*
 * 工場名
 */
$reviser->addString( 0, 28, 42, $loading_factory);

/*
 * 運搬回数
 */
$reviser->addString( 0, 32, 20, $delivery_times);
$reviser->addString( 0, 32, 25, $all_quantity);

/*
 * 往・帰
 */
$reviser->addString( 0, 32, 34, $depart_leaving_time);
$reviser->addString( 0, 32, 38, $depart_arrived_time);
$reviser->addString( 0, 34, 34, $return_leaving_time);
$reviser->addString( 0, 34, 38, $return_arrived_time);

/*
 * 請求時間
 */
//var_dump($attendance_display_times_hour."時".$attendance_display_times_minute."分");
//exit;

/*
if (!empty($attendance_display_times_hour))
	$reviser->addString( 0, 32, 43, $attendance_display_times_hour."時".$attendance_display_times_minute."分");

if (!empty($leaving_display_times_hour))
	$reviser->addString( 0, 32, 50, $leaving_display_times_hour."時".$leaving_display_times_minute."分");
*/

/*
 * 点検
 */
// 運転者席
$reviser->addString( 0, 35, 7, $inspections->fuel_leaks);
$reviser->addString( 0, 36, 7, $inspections->engine);
$reviser->addString( 0, 37, 7, $inspections->steering_handle);
$reviser->addString( 0, 39, 7, $inspections->clutch);
$reviser->addString( 0, 40, 7, $inspections->brake_pedal);
$reviser->addString( 0, 42, 7, $inspections->brake_lever);
$reviser->addString( 0, 43, 7, $inspections->air_brake);
$reviser->addString( 0, 46, 7, $inspections->car_horn);
$reviser->addString( 0, 49, 7, $inspections->defroster);
$reviser->addString( 0, 50, 7, $inspections->reflecting_mirror);
//前部②下
$reviser->addString( 0, 51, 7, $inspections->clip_bolt);
$reviser->addString( 0, 52, 7, $inspections->auxiliary_equipment);
$reviser->addString( 0, 55, 7, $inspections->tire);
//前部②上
$reviser->addString( 0, 35, 16, $inspections->radiator);
$reviser->addString( 0, 36, 16, $inspections->radiator_cap);
$reviser->addString( 0, 37, 16, $inspections->fan_belt);
$reviser->addString( 0, 38, 16, $inspections->oil);
$reviser->addString( 0, 39, 16, $inspections->jet_cleaning);
$reviser->addString( 0, 40, 16, $inspections->brake_and_clutch_oil);
$reviser->addString( 0, 42, 16, $inspections->chassis_spring);
$reviser->addString( 0, 43, 16, $inspections->door_lock);
//後部③下
$reviser->addString( 0, 45, 16, $inspections->rear_clip_bolt);
$reviser->addString( 0, 46, 16, $inspections->rear_air_tank);
$reviser->addString( 0, 47, 16, $inspections->rear_tire);
$reviser->addString( 0, 51, 16, $inspections->rear_battery);
$reviser->addString( 0, 53, 16, $inspections->rear_auxiliary_equipment);
//後部③下
$reviser->addString( 0, 45, 25, $inspections->loading_device);
$reviser->addString( 0, 48, 25, $inspections->car_bed);
$reviser->addString( 0, 49, 25, $inspections->gas_color);
$reviser->addString( 0, 50, 25, $inspections->rear_chassis_spring);
//その他④
$reviser->addString( 0, 51, 25, $inspections->tachograph);
$reviser->addString( 0, 53, 25, $inspections->alarm_tool);
$reviser->addString( 0, 55, 25, $inspections->automobile_inspection_certificate);
$reviser->addString( 0, 57, 25, $inspections->implement);
//点検時間
if (!empty($inspection_time))
	$reviser->addString( 0, 45, 31, $inspection_time);
if (!empty($inspection_end_time))
	$reviser->addString( 0, 45, 37, $inspection_end_time);

//備考
$reviser->addString( 0, 36, 20, $other_detail);
//メモ
$reviser->addString( 0, 36, 42, $comments);
//配合
$reviser->addString( 0, 56, 27, $mix);

/***********************************
 * 保存 
 ***********************************/
//本番
$outfile = $driver_names['last_name'].$driver_names['first_name'].'_'.$date_year.'年'.$date_month.'月'.$date_day.'日'.'_'.$concrete_attendance_id.'.xls';// file name
$readfile   = '/var/www/vhosts/doutaikanri.com/public_html/smart_location_test/dayReportExcel/excel_template.xls';// template file
$savepath = '/var/www/vhosts/doutaikanri.com/public_html/smart_location_test/dayReportExcel/reports/'.$driver_id.'/';//保存先（相対パス）

//ローカル
//$readfile   = 'dayReportExcel/excel_template.xls';//読み込みエクセルファイル名
//$savepath = 'dayReportExcel/reports/'.$driver_id;//保存先（相対パス）
//$save_outfile = mb_convert_encoding($outfile, "SJIS", "UTF-8");

//ディレクトリが存在するかを調べる
if( !file_exists( $savepath ) ) {
	//ディレクトリを作成する。
	mkdir( $savepath, 0777 );
	chmod( $savepath, 0777 );
}

//ファイルの生成
$reviser->reviseFile($readfile,$outfile,$savepath);
//$reviser->reviseFile($readfile,$save_outfile,$savepath);

//　ファイルを読み込み、ダウンロードされたら削除する
header('Content-Length: '.filesize($savepath.$outfile));
header("Content-type: application/octet-stream");
header("Content-disposition: inline; filename=" . $outfile);
readfile($savepath.$outfile);
unlink($savepath.$outfile);


/***********************************
 * テンプレートファイルへアサイン
 ***********************************/
//$smarty->assign("date", $display_year.'年'.$date_month.'月'.$date_day.'日');
//$smarty->assign("driver_name", $driver_name);
//$smarty->assign("driver_id", $driver_id);
//$smarty->assign("excel_name", $outfile);
//$smarty->assign("filename","concrete/day_report_excel.html");
//$smarty->display('template.html');

?>