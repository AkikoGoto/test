<?php
//include ("/var/www/vhosts/doutaikanri.com/public_html/smart_location_test/mpdf/mpdf.php");
//$img_url = TEMPLATE_URL."templates/image/concrete_mixer_day_report.png";
include ("mpdf/mpdf.php");
$img_url = "http://doutaikanri.com/smart_location_test/templates/image/concrete_mixer_day_report.png";

$mpdf=new mPDF( 'ja+aCJK', 'A4-L', 0, '', 0, 0, 0, 0, 0, 0);

$is_bill = false;
if ($_POST['is_bill'] == 1) {
	$is_bill = true;
}

	try{
			
			$exploded_date = explode("-", $_POST['date']);
			$_POST['time_from_year'] = $exploded_date[0];
			$_POST['time_from_month'] = $exploded_date[1];
			$_POST['time_from_day'] = $exploded_date[2];
			
		if(!empty($_POST['driver_id']) && $_POST['time_from_year'] && $_POST['time_from_month'] && $_POST['time_from_day']){
			
			
			foreach($daily_report as $key){
				$_POST[$key]=trim( mb_convert_kana($_POST[$key], "s"));
				$post_datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			}
			
			$post_datas['date'] = $post_datas['time_from_year'].'-'.$post_datas['time_from_month'].'-'.$post_datas['time_from_day']; 
			
			//主な一日のデータ
			$daily_report_data = concrete::daily_report_data($post_datas);
			
			if (empty($daily_report_data)) {
				header('Location:index.php?action=message_mobile&situation=fail_concrete_report');
			}
			
			// driver_name
			$driver_name = concrete::driver_name($post_datas['driver_id']);
			
			$concrete_attendance_id = $daily_report_data[0]['concrete_attendance_id'];
			
			// oil
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
				
				$oil_1_replenishment = "&nbsp;&nbsp;";
				$oil_1_replenishment = $oil_1['oil_replenishment'];
				$oil_1_start_time = separate_time($oil_1['start_time']);
				$oil_1_end_time = separate_time($oil_1['end_time']);
				if (empty($oil_1_replenishment)) {
					$oil_1_replenishment = "&nbsp;&nbsp;";
				}
				
				$oil_2_replenishment = "&nbsp;&nbsp;";
				$oil_2_replenishment = $oil_2['oil_replenishment'];
				$oil_2_start_time = separate_time($oil_2['start_time']);
				$oil_2_end_time = separate_time($oil_2['end_time']);
				if (empty($oil_2_replenishment)) {
					$oil_2_replenishment = "&nbsp;&nbsp;";
				}
				
				$oil_3_replenishment = $oil_3['oil_replenishment'];
				$oil_3_start_time = separate_time($oil_3['start_time']);
				$oil_3_end_time = separate_time($oil_3['end_time']);
				if (empty($oil_3_replenishment)) {
					$oil_3_replenishment = "&nbsp;&nbsp;";
				}
				
				$oil_4_replenishment = $oil_4['oil_replenishment'];
				$oil_4_start_time = separate_time($oil_4['start_time']);
				$oil_4_end_time = separate_time($oil_4['end_time']);
				if (empty($oil_4_replenishment)) {
					$oil_4_replenishment = "&nbsp;&nbsp;";
				}
				
			}
			
			//現場名などのデータ
			$scenes_data = concrete::scenes_data($concrete_attendance_id);
			
			//その他のデータ
			//$other_data = concrete::other_data($post_datas,$concrete_attendance_id);
				
			//うがいと洗車の時間データ
			//$wash_data = concrete::wash_data($post_datas,$concrete_attendance_id);
			
			//うがいと洗車の時間合算値
			//$total_wash_time = gmdate('H:i:s',strtotime($wash_data[1]['end'])-strtotime($wash_data[0]['start']));
						
			/**
			 * 細かいデータ
			 * 
			 * Enter description here ...
			 * @var unknown_type
			 */
 			$today_report_data = $daily_report_data[0];
 			
 			// 日付
 			$date = $today_report_data['date'];
 			$exploded_date = explode( "-", $date);
 			$date_year = $exploded_date[0];
 			$date_month = $exploded_date[1];
 			$date_day = $exploded_date[2];
 			
 			if ($date_year == 2014) {
 				$date_year = "25";
 			} else if ($date_year > 2014) {
 				$more_year = $date_year - 2014;
 				$date_year = 25 + $more_year;
 			}
 			
 			$date_day = sprintf("%02d", $date_day);
 			$date_month = sprintf("%02d", $date_month);
 			$date_years = explode_number($date_year);
 			$date_months = explode_number($date_month);
 			$date_days = explode_number($date_day);
 			
 			//　ドア番号
 			$door_number = $today_report_data['door_number'];
 			$exploded_door = explode_number($door_number);
 			$hundred_door = get_number_from_number_array($exploded_door, 0);
 			$ten_door = get_number_from_number_array($exploded_door, 1);
 			$one_door = get_number_from_number_array($exploded_door, 2);
 			
 			if ($one_door == null && $ten_door == null) {
 				$one_door = $hundred_door;
 			} else if ($one_door == null && $ten_door != null) {
 				$one_door = $ten_door;
 				$ten_door = $hundred_door;
 			}
			
 			// 積込工場
 			$loading_factory = $today_report_data['loading_factory'];
			$count_loading_factory = mb_strlen($loading_factory);
			if ($count_loading_factory > 13) {
 				$substring_loading_factory = mb_substr( $loading_factory, 0, 12, "UTF-8");
				$loading_factory = $substring_loading_factory."...";
 			}
 				
 			//　営業所名
 			$branch_name = $today_report_data['branch_name'];
 			$count_branch_name = mb_strlen($branch_name);
 			if ($count_branch_name <= 9) {
	 			$padding_style = "padding-top: 10px; height: 22px;";
 			} else if ($count_branch_name > 9) {
 				if ($count_branch_name > 19) {
 					$substring_branch_name = mb_substr( $branch_name, 0, 17, "UTF-8");
					$branch_name = $substring_branch_name."...";
 				}
	 			$padding_style = "margin-top: -3px; height: 18px;";
 			}
 			
 			// 出勤時間
 			$attendance_time = $today_report_data['attendance_time'];
 			//退勤時間
 			$leaving_time = $today_report_data['leaving_time'];
 			
 			$explode_attendance = explode( " ", $attendance_time);
 			$attendance_times = explode(":",$explode_attendance[1]);
 			$attendance_time = $attendance_times[0].$attendance_times[1];
 			$attendance_numbers = explode_number($attendance_time);
 			
 			$explode_leaving = explode( " ", $leaving_time);
 			$leaving_times = explode(":", $explode_leaving[1]);
 			$leaving_time = $leaving_times[0].$leaving_times[1];
 			$leaving_numbers = explode_number($leaving_time);
 			
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
 			
 			// 始業メーター
 			$start_meter = $today_report_data['start_meter'];
 			$start_meter_count = strlen($start_meter);
 			$split_start_meter = $start_meter_count - 3;
 			$big_start_meter = substr( $start_meter, 0, $split_start_meter);
 			$small_start_meter = substr( $start_meter, $split_start_meter, 3);
 			$count_big_start_meter = strlen($big_start_meter);
 			$counter_big_start_meter = 4 - $count_big_start_meter;
 			if ($counter_big_start_meter != 0) {
 				for ($i = 0; $i < $counter_big_start_meter; $i++) {
 					$big_start_meter = "&nbsp;&nbsp;".$big_start_meter;
 				}
 			}
 			
 			// 終業メーター
 			$end_meter = $today_report_data['end_meter'];
 			$end_meter_count = strlen($end_meter);
 			$split_end_meter = $end_meter_count - 3;
 			$big_end_meter = substr( $end_meter, 0, $split_end_meter);
 			$small_end_meter = substr( $end_meter, $split_end_meter, 3);
 			$count_big_end_meter = strlen($big_end_meter);
 			$counter_big_end_meter = 4 - $count_big_end_meter;
 			if ($counter_big_end_meter != 0) {
 				for ($i = 0; $i < $counter_big_end_meter; $i++) {
 					$big_end_meter = "&nbsp;&nbsp;".$big_end_meter;
 				}
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
	 				$big_real_meter = "&nbsp;";
		 			$small_real_meter = $real_meters;
	 			}
	 			
	 			$count_big_real_meter = strlen($big_real_meter);
	 			$counter_big_real_meter = 4 - $count_big_real_meter;
				if ($counter_big_real_meter != 0) {
	 				for ($i = 0; $i < $counter_big_real_meter; $i++) {
	 					if ($counter_big_real_meter == 3 && $i == 2)
		 					$big_real_meter = "&nbsp;".$big_real_meter;
		 				else 
		 					$big_real_meter = "&nbsp;&nbsp;".$big_real_meter;
	 				}
	 			}
	 			
 			}
 			
 			// 工場内始業メーター
 			$start_in_factory_meter = $today_report_data['start_in_factory_meter'];
 			// 工場内終業メーター
 			$end_in_factory_meter = $today_report_data['end_in_factory_meter'];
 			//　工場内走行メーター
 			$real_meter_in_factory = $end_in_factory_meter - $start_in_factory_meter;
 			// 1/2
 			$half_real_meter_in_factory = $real_meter_in_factory / 2;
 			
 			// 修正時間
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
 			
 			// 運搬
 			$delivery_times = count( $scenes_data );
			
			//作業履歴の取得
			$concrete_statuses = array ( 5, 6, 7, 8, 10, 11);
			$lunch = array();
			$waiting_for_contact = array();
			$waiting_for_washing = array();
			$washing = array();
			$copy = array();
			$other = array();
			
//			$dataList = Concrete::getWorkRecordsByStatus( 10, $post_datas['driver_id'], $date);
			
			foreach ($concrete_statuses as $concrete_status ) {
				$dataList = Concrete::getWorkRecordsByStatus($concrete_status, $post_datas['driver_id'], $date);
				if($dataList){
					//スタートの時間
					$start_data = $dataList[0]['start'];
					$strToTimeStart = strtotime($start_data);
					$start = date(Hi, $strToTimeStart);
					
					//終了の時間
					$end_data = $dataList[0]['end'];
					$strToTimeEnd = strtotime($end_data);
					$end = date(Hi, $strToTimeEnd);
					
					
				}else{
					$start = "&nbsp;"; $end = "&nbsp;";
				}
				
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
			
			if(!($daily_report_data) && !($scenes_data) && !($other_data) && !($wash_data)){
				
				$form_validate->show_form($errors);
					
			}else{
			
				foreach($daily_report_data[0] as $key => $value){
					$smarty->assign("$key",$value);
				}
				
				if (!empty($other_data)) {
					foreach($other_data[0] as $number => $test){
						$smarty->assign("$key",$value);	
					}
				}

			}
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
	
$html = <<<EOF
<!-- EXAMPLE OF CSS STYLE -->
<html>
	<body>
		<div class="dummy_top_bar">  </div>
		<div class="branch_name">{$branch_name}</div>
		<div class="time_erea">
			<span class="left_number">{$date_years[0]}</span>
			<span class="right_number">{$date_years[1]}</span>
			<span class="left_number">{$date_months[0]}</span>
			<span class="right_number">{$date_months[1]}</span>
			<span class="left_number">{$date_days[0]}</span>
			<span class="right_number">{$date_days[1]}</span>
		</div>
		<div class="door_number">
			<span class="left_number">{$hundred_door}</span>
			<span class="left_number">{$ten_door}</span>
			<span>{$one_door}</span>
		</div>
		<div class="driver_name">
			<span>{$driver_name}</span>
		</div>
		<div class="attendance_time">
			<span class="left_number">{$attendance_numbers[0]}</span>
			<span class="right_number">{$attendance_numbers[1]}</span>
			<span class="left_number">{$attendance_numbers[2]}</span>
			<span class="right_number">{$attendance_numbers[3]}</span>
		</div>
		<div class="leaving_time">
			<span class="left_number">{$leaving_numbers[0]}</span>
			<span class="right_number">{$leaving_numbers[1]}</span>
			<span class="left_number">{$leaving_numbers[2]}</span>
			<span class="right_number">{$leaving_numbers[3]}</span>
		</div>
		
		<div style="float: clear; width: 100px; float: left; margin:0;"></div>
		
		<div class="scenes">
EOF;

$all_quantity = 0;
$is_first_row = true;
foreach ($scenes_data as $scene) {
	
	$all_quantity = $all_quantity + $scene['quantity'];
	$quantities = explode(".", $scene['quantity']);
	
	//会社名
	$destination_company = $scene['destination_company'];
	$count_destination_company = mb_strlen($destination_company);
	if ($count_destination_company > 6) {
		$substring_destination_company = mb_substr( $destination_company, 0, 7, "UTF-8");
		$destination_company = $substring_destination_company."...";
	}
	
	//現場名
	$scene_name = $scene['scene_name'];
	$count_scene_name = mb_strlen($scene_name);
	if ($count_scene_name > 10) {
		$substring_scene_name = mb_substr( $scene_name, 0, 10, "UTF-8");
		$scene_name = $substring_scene_name."...";
	}
	
	// 数量　左枠
	if (count($quantities) > 1) {
		$left_quantity = $quantities[0];
		$right_quantity = $quantities[1];
	} else {
		$left_quantity = $quantities[0];
		$right_quantity = "&nbsp;";
	}
	
	// 数量　右枠
	if (strlen($right_quantity) <= 1) {
		$right_quantity = "&nbsp;".$right_quantity;
	}
	
	//積込状況
	$loading_state = "&nbsp;&nbsp;";
	if ($scene['loading_state'] == 1) {
		$loading_state = "✔";
	}
	
	//残水確認
	$remaining_water = "&nbsp;&nbsp;";
	if ($scene['remaining_water'] == 1) {
		$remaining_water = "✔";
	}
	
	//　最初の行以外はすべてマージンを設ける
	if (!$is_first_row) {
		$margin = 'style="margin-top: -2px;"';
	}
	
	//各時間
	$start_time = hour_minute($scene['start_time']);
	$arrived_time = hour_minute($scene['arrived_time']);
	$leaving_from_scene_time = hour_minute($scene['leaving_from_scene_time']);
	$return_from_scene_time = hour_minute($scene['return_from_scene_time']);
	
$html .= <<<EOF
			<div class="row" {$margin}>
				<div class="order left_number">{$scene['number']}</div>
				<div class="company_name">{$destination_company}</div>
				<div class="scene">{$scene_name}</div>
				<div class="quantity">
					<span class="left_number" style="letter-spacing: 0.1em; font-size: 16px; text-align: center; display: block;">{$left_quantity}</span>
					<span align="center" class="right_number" style="letter-spacing: 0.1em; font-size: 16px; text-align: center; display: block;">{$right_quantity}</span>
				</div>
				<div class="leaving_for_scene_time">
					<div class="" style="width:80px; float: left; letter-spacing: 0.2em;">{$start_time}</div>
				</div>
				<div class="arrived_at_scene_time">
					<div class="" style="width:80px; float: left; letter-spacing: 0.2em;">{$arrived_time}</div>
				</div>
				<div class="leaving_from_scene_time">
					<div class="" style="width:80px; float: left; letter-spacing: 0.2em;">{$leaving_from_scene_time}</div>
				</div>
				<div class="return_from_scene_time">
					<div class="" style="width:80px; float: left; letter-spacing: 0.2em;">{$return_from_scene_time}</div>
				</div>
				<div class="other_scene_item">
					<span class="water_number">{$remaining_water}</span>
					<span class="">{$loading_state}</span>
				</div>
			</div>
EOF;
$is_first_row = false;
}

$html .= <<<EOF
		
		</div>
		<div class="other_actions">
			<div class="lunch">
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -2px;">{$lunch['start']}</div>
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -6px;">{$lunch['end']}</div>
			</div>
			<div class="waiting_for_contact">
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -2px;">{$waiting_for_contact['start']}</div>
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -6px;">{$waiting_for_contact['end']}</div>
			</div>
			<div class="waiting_for_wash">
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -2px;">{$waiting_for_washing['start']}</div>
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -6px;">{$waiting_for_washing['end']}</div>
			</div>
			<div class="wash">
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -2px;">{$washing['start']}</div>
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -6px;">{$washing['end']}</div>
			</div>
			<div class="supply_oil">
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -2px;">{$oil_1_start_time}</div>
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -6px;">{$oil_1_end_time}</div>
			</div>
			<div class="copy">
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -2px;">{$copy['start']}</div>
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -6px;">{$copy['end']}</div>
			</div>
			<div class="other">
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -2px;">{$other['start']}</div>
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -6px;">{$other['end']}</div>
			</div>
			<div class="supply_oil_2">
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -2px;">{$oil_2_start_time}</div>
				<div class="" style="width:80px; float: left; letter-spacing: 0.3em; margin-left: -6px;">{$oil_2_end_time}</div>
			</div>
		</div>
		
		<div class="meter_and_oils">
			<div class="meters">
				<div style="margin-top: 0px;">
					<span>{$big_end_meter}</span>
					<span class="">{$small_end_meter}</span>
				</div>
				<div style="margin-top: -2px; position:relative;">
					<span class="">{$big_start_meter}</span>
					<span class="">{$small_start_meter}</span>
				</div>
				<div style="width: 100px; height: 23px; overflow: hidden; margin-top: -4px; margin-left: -1px;">
					<span style="width: 50px; height: 23px;">{$big_real_meter}</span>
					<span style="width: 40px; height: 23px;">{$small_real_meter}</span>
				</div>
			</div>
			
			<div class="meters_in_factory">
				<div class="meters_in_factory_detail">
					<div style="margin-top: 0px" align="right">
						<span class="">{$end_in_factory_meter}</span>
					</div>
					<div style="margin-top: -2px" align="right">
						<span>{$start_in_factory_meter}</span>
					</div>
					<div style="margin-top: -2px" align="right">
						<span>{$real_meter_in_factory}</span>
					</div>
				</div>
				<div class="meters_in_factory_sum">
					<div class="">{$half_real_meter_in_factory}</div>
				</div>
			</div>
			
			<div class="oil_replenishments" style="text-align:right;">
				<div style="margin-top: -1px">
					<span class="" style="letter-spacing: 0.75em;">{$oil_1_replenishment}</span>
				</div>
				<div style="margin-top: -3px">
					<span class="" style="letter-spacing: 0.75em;">{$oil_2_replenishment}</span>
				</div>
				<div style="margin-top: 2px">
					<span class="" style="letter-spacing: 0.75em;">{$oil_3_replenishment}</span>
				</div>
				<div style="margin-top: -8px;">
					<span class="" style="letter-spacing: 0.75em;">{$oil_4_replenishment}</span>
				</div>
			</div>
			
			<div class="factory_name">
				{$loading_factory}
			</div>
			
		</div>
		
		<div class="delivery_times">
			<div class="delivery_detail_times">{$delivery_times}</div>
			<div class="delivery_detail_meters">{$all_quantity}</div>
		</div>
		
		<div class="branch_leaving_and_return">
			<div class="branch_leaving_and_arrived">
				<div class="leaving_for_branch">{$depart_leaving_time}</div>
				<div class="arrived_at_branch">{$depart_arrived_time}</div>
			</div>
			<div class="branch_return_and_arrived">
				<div class="return_for_branch">{$return_leaving_time}</div>
				<div class="return_at_branch">{$return_arrived_time}</div>
			</div>
		</div>
		
		<div class="bill_time">
			<div class="start_hour">{$attendance_display_times_hour}</div>
			<div class="start_minute">{$attendance_display_times_minute}</div>
			<div class="end_hour">{$leaving_display_times_hour}</div>
			<div class="end_minute">{$leaving_display_times_minute}</div>
		</div>
		
	</body>
</html>
EOF;

$css =  <<<EOF
<style>
	@page{
		background-image: url({$img_url});
		background-image-resize: 6;
	}
	
	body {
		padding: 100px;
		font-size: 20px;
		font-weight: bold;
		font-family:'Lucida Grande',
		 'Hiragino Kaku Gothic ProN', 'ヒラギノ角ゴ ProN W3',
		 Meiryo, メイリオ, sans-serif;
	}
	
	.dummy_top_bar {
		height: 50px;
	}
	
	.branch_name {
		margin-left: 98px;
		width: 132px;
		height: 33px;
		text-align: center;
		font-size: 14px;
		{$padding_style}
	}
	
	.time_erea {
		margin-top: 27px;
		margin-left: 44px;
		width: 148px;
		font-size: 24px;
		float: left;
	}
	
	.left_number {
		letter-spacing: 0.15em;
	}
	
	.right_number {
		letter-spacing: 0.2em;
	}
	
	.door_number {
		margin-left: 5px;
		font-size: 24px;
		float: left;
		width: 70px;
	}
	
	.driver_name {
		margin-top: 3px;
		margin-left: 6	px;
		font-size: 18px;
		float: left;
		width: 198px;
		text-align: center;
	}
	
	.attendance_time {
		float: left;
		width: 100px;
		font-size: 24px;
		margin-top: -3px;
		margin-left: 9px;
	}
	
	.leaving_time {
		float: left;
		width: 100px;
		font-size: 24px;
		margin-left: 3px;
	}
	
	.row {
		float: left;
		width: 630px;
	}
	
	.order {
		float: left;
		width: 20px;
	}
	
	.company_name,
	.scene {
		font-size: 12px;
		text-align: center;
		margin-right: 100px;
		width: 100px;
		float: left;
		padding-top: 5px;
		height: 18px;
		vertical-align: middle;
	}
	
	.scene {
		width: 130px;
	}

	.quantity {
		margin-left: 10px;
		width: 40px;
		float: left;
	}
	
	.leaving_for_scene_time,
	.arrived_at_scene_time,
	.leaving_from_scene_time,
	.return_from_scene_time,
	.other_scene_item {
		margin-left: 4px;
		width: 70px;
		float: left;
		letter-spacing: -0.02em;
	}
	
	.arrived_at_scene_time,
	.leaving_from_scene_time,
	.return_from_scene_time,
	.other_scene_item {
		margin-left: -4px;
	}
	
	.other_scene_item {
		width: 40px;
		float: left;
		letter-spacing: -0.02em;
		margin-top: 2px;
		margin-left: -4px;
		font-size: 16px;
	}
	
	.water_number {
		width: 20px;
		letter-spacing: 0.2em;
		position: relative;
	}
	
	.scenes {
		float: left;
		width: 620px;
		height: 250px;
		margin-top: 26px;
		margin-left: 69px;
		position: relative;
	}
	
	.other_actions {
		float: left;
		width: 160px;
		height: 255px;
		margin-top: -6px;
		margin-left: 32px;
		position: relative;
	}
	
	.waiting_for_contact,
	.waiting_for_wash,
	.wash,
	.supply_oil,
	.copy,
	.other,
	.supply_oil_2 {
		margin-top: 5px;
		display:block;
		height: 27px:
	}
	
	.meter_and_oils {
		float: left;
		width: 230px;
		height: 275px;
		margin-top: -23px;
		margin-left: -20px;
		position: relative;
	}
	
	.meters {
		width: 180px;
		height: 73px;
		margin-left: 101px;
		position:relative;
	}
	
	.meters_in_factory {
		width: 160px;
		height: 50px;
		margin-left: 75px;
		margin-top: -2px;
	}
	
	.meters_in_factory_detail {
		width: 44px
		height: 70px;
		margin-left: -4px;
		float: left;
		font-size: 15px;
	}
	
	.meters_in_factory_sum {
		width: 60px
		height: 70px;
		float: left;
		margin-top: 15px;
		margin-left: 30px;
		text-align: center;
	}
	
	.oil_replenishments {
		width: 176px;
		height: 98px;
		margin-left: 13px;
		margin-top: 0px;
		letter-spacing: 0.16em;
	}
	
	.factory_name {
		width: 190px;
		height: 27px;
		text-align: center;
		margin-top: 27px;
		letter-spacing: 0.16em;
		font-size: 12px;
	}
	
	.delivery_times {
		float: left;
		width: 150px;
		height: 50px;
		margin-top: 0px;
		margin-left: 500px;
	}
	
	.delivery_detail_times {
		float: left;
		width: 60px;
		height: 50px;
		margin-top: 6px;
		font-size: 30px;
		text-align: center;
	}
	
	.delivery_detail_meters {
		float: left;
		width: 60px;
		height: 50px;
		margin-left: 20px;
		margin-top: 6px;
		font-size: 22px;
		text-align: center;
	}
	
	.branch_leaving_and_return {
		float: left;
		width: 140px;
		height: 56px;
		margin-top: 0px;
		margin-left: 64px;
	}
	
	.branch_leaving_and_arrived,
	.branch_return_and_arrived {
		width: 140px;
		height: 10px;
	}
	
	.leaving_for_branch,
	.return_for_branch {
		float: left;
		width: 62px;
		text-align: center;
	}
	
	.arrived_at_branch,
	.return_at_branch {
		float: left;
		width: 62px;
		margin-left: 14px;
		text-align: center;
	}

	.bill_time {
		float: left;
		width: 182px;
		height: 20px;
		margin-top: 16px;
		margin-left: 34px;
		font-size: 16px;
	}
	
	.start_hour,
	.start_minute {
		float: left;
		width: 43px;
	}
	
	.end_hour,
	.end_minute{
		float: left;
		width: 40px;
	}
	
	.start_minute {
		margin-left: -4px;
	}
	
	.end_hour {
		margin-left: 15px;
	}
	
	.end_minute {
		margin-left: -3px;
	}
	
	.target_editing {
		background-color: #000; color: #fff;
	}
	
</style>
EOF;

$html = $css . $html;
$mpdf->WriteHTML($html);
$mpdf->Output();
exit;