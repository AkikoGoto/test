<?php
require_once('GlobalFunction.php');
require_once('concrete.class.php');
require_once('concrete.validate.class.php');
require_once('reviser_lite.php');

//全ての作業ステータスを再設定
$smarty->assign("status_1", '営業所を出発');
$smarty->assign("status_2", '現場へ出発');
$smarty->assign("status_3", '現場から出発中');
$smarty->assign("status_4", '営業所へ戻り中');
$smarty->assign("status_5", '昼食');
$smarty->assign("status_6", '連絡待ち');
$smarty->assign("status_7", '洗車待ち');
$smarty->assign("status_8", '洗車');
$smarty->assign("status_9", '給油');
$smarty->assign("status_10", 'コピー');
$smarty->assign("status_11", 'その他');


//各データの配列
$start_concrete_attendance=array('driver_id', 'loading_factory', 'date', 'door_number', 'attendance_time', 'branch_name', 'start_meter' );

$start_concrete_factory_meter=array('concrete_attendance_id', 'start_in_factory_meter');

$end_concrete_factory_meter=array('concrete_attendance_id', 'end_in_factory_meter');

$depart_leaving_time=array('driver_id', 'concrete_attendance_id', 'depart_leaving_time');

$depart_arrived_time=array('driver_id', 'concrete_attendance_id', 'depart_arrived_time');

$start_concrete_scene=array('driver_id','concrete_attendance_id', 'destination_company', 'scene_name', 'number', 'status', 'quantity' ,'loading_state', 'mix');

$edit_concrete_scene=array('driver_id','scenes_for_concrete_id','concrete_attendance_id', 'destination_company', 'scene_name', 'number', 'quantity' ,'loading_state', 'mix');

$end_concrete_scene=array('driver_id', 'scenes_for_concrete_id','concrete_attendance_id', 'status', 'remaining_water', 'end_time');

$start_oil_replenishments=array('concrete_attendance_id', 'oil_replenishment_type', 'oil_replenishment', 'start_time', 'end_time');

$end_oil_replenishments=array('concrete_attendance_id', 'oil_replenishment_type', 'oil_replenishment', 'end_time');

$inspection=array('inspection_id','concrete_attendance_id', 'inspection_time', 'fuel_leaks', 'engine', 'brake_pedal', 'brake_lever', 'car_horn',
				  'defroster', 'steering_handle', 'reflecting_mirror', 'air_brake', 'clutch',
					'clip_bolt','auxiliary_equipment', 'tire', 'radiator',
					'radiator_cap', 'fan_belt', 'oil',
					'jet_cleaning', 'brake_and_clutch_oil', 'chassis_spring',
					'door_lock', 'rear_clip_bolt', 'rear_air_tank',
					'rear_tire', 'rear_battery', 'rear_auxiliary_equipment',
					'loading_device', 'car_bed', 'gas_color',
					'rear_chassis_spring', 'tachograph', 'alarm_tool',
					'automobile_inspection_certificate', 'implement');

$end_concrete_meter=array('concrete_attendance_id', 'end_meter');

$end_concrete_attendance=array('concrete_attendance_id', 'leaving_time');

$concrete_other=array('concrete_attendance_id','other');

$id_array=array('id', 'driver_id');

$demand_time_array=array('concrete_attendance_id', 'demand_time_id', 'date', 'start_year', 'start_month', 'start_day', 'start_hour', 'start_minit', 'end_year','end_month', 'end_day','end_hour','end_minit',
		'before_repair_start_hour','before_repair_start_minit', 'after_repair_end_hour', 'after_repair_end_minit');

$edit_concrete_attendance=array('concrete_attendance_id', 'loading_factory', 'door_number', 'branch_name');

$return_leaving_time=array('driver_id', 'concrete_attendance_id', 'return_leaving_time');

$return_arrived_time=array('driver_id', 'concrete_attendance_id', 'return_arrived_time');

$driver_status_start_time=array('driver_id','scenes_for_concrete_id', 'status', 'start_time');

$driver_status_end_time=array('scenes_for_concrete_id', 'status', 'end_time');

$concrete_driver_status_start_time = array('driver_id', 'status', 'start_time');
$concrete_driver_status_end_time = array('concrete_driver_status_id', 'status', 'end_time');

$update_oil_replinishment = array('concrete_attendance_id','oil_replenishment_type', 'oil_replenishment','start_time_hour', 'start_time_minit','end_time_hour','end_time_minit','check_year','check_month','check_day','check_date','status');

$daily_report=array('driver_id', 'concrete_attendance_id');

$daily_select_report=array('driver_id', 'driver_name','time_from_year', 'time_from_month', 'time_from_day','time_to_year','time_to_month','time_to_day');
?>
