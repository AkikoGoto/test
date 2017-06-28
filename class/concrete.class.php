<?php
/*
 * Concreteクラス
 * コンクリート業界だけの設定
 * @author Koichi Saito
 * @since 2014/06/13
 * @version ?
 *
 */

class Concrete{

	public $id;
	public $driver_id;
	public $daily;
	public $weekly;
	public $date;
	public $address;
	public $latitude;
	public $longitude;
	public $alert_when_not_there;
	public $alert_when_there;
	public $mail_timing;
	public $mail_before_or_after;
	public $accuracy;
	public $email_other_admin;
	public $mail_time;
	public $updated;
	public $created;
	public $company_id;
	public $name;
	public $datas;
	public $set_alarmId;
	public $increase_time;
	public $decrease_time;
	public $concrete_attendances_id;
	public $login_id;
	public $mix;
	
	
//出勤時のデータ登録
static public function insert_attendances($datas){
	
	try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			$sql1="
					INSERT INTO
						concrete_attendances
						(driver_id, date, door_number, attendance_time, branch_name, 
						loading_factory, created)
					VALUES
						(:driver_id, :date, :door_number, :attendance_time, :branch_name, 
						:loading_factory, NOW())
					";
			
			$stmt=$dbh->prepare($sql1);
			
			$param = array(
			
					'driver_id' => $datas['driver_id'],
					'date' => $datas['date'],
					'door_number' => $datas['door_number'],
					'attendance_time' => $datas['attendance_time'],
					'branch_name' => $datas['branch_name'],
					'loading_factory' =>$datas['loading_factory']
			);
			
			$stmt->execute($param);
			$concrete_attendance_id = $dbh->lastInsertId();
			$dbh->commit();
			
			return $concrete_attendance_id;
			
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
		}
}	
	
	
//出勤時メーターのデータ登録
static public function start_meters($datas,$concrete_attendance_id){

	try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
				
			$sql="
					INSERT INTO
						concrete_for_meters
						(concrete_attendance_id, start_meter, created)
					VALUES
						(:concrete_attendance_id, :start_meter, NOW())
						";
			
			$stmt=$dbh->prepare($sql);
				
			$param = array(
					'concrete_attendance_id' => $concrete_attendance_id,
					'start_meter' => $datas['start_meter'],
			);
				
			$stmt->execute($param);
			$dbh->commit();
					
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//出勤時メーター、既に登録があったら更新する
static public function update_start_meters($datas, $check_date_data){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
				UPDATE concrete_for_meters
				SET
					start_meter = :start_meter
				WHERE
					concrete_for_meters.concrete_attendance_id={$check_date_data[0]['id']}";

		
		$stmt=$dbh->prepare($sql);
		$param = array( 'start_meter' => $datas['start_meter']);
		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}


//工場内始業メーターのデータ登録
static public function start_factory_meters($datas){

	try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			$sql="
						UPDATE concrete_for_meters
						SET
							start_in_factory_meter = :start_in_factory_meter
						WHERE
							concrete_for_meters.concrete_attendance_id =".$datas['concrete_attendance_id'];
	
			$stmt=$dbh->prepare($sql);
	
			$param = array(
					'start_in_factory_meter' => $datas['start_in_factory_meter'],
			);
	
			$stmt->execute($param);
			$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//工場内終業メーターのデータ登録
static public function end_factory_meters($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
			
		$sql="
					UPDATE concrete_for_meters
					SET
						end_in_factory_meter = :end_in_factory_meter
					WHERE
						concrete_for_meters.concrete_attendance_id =".$datas['concrete_attendance_id'];

		$stmt=$dbh->prepare($sql);

		$param = array( 'end_in_factory_meter' => $datas['end_in_factory_meter'] );

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//出発時のデータ登録
static public function depart_leaving_time($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
		
		$sql="
					INSERT INTO
						concrete_departs_and_returns
						(driver_id, concrete_attendance_id, depart_leaving_time, created)
					VALUES
						(:driver_id, :concrete_attendance_id, :depart_leaving_time, NOW())
						";

		$stmt=$dbh->prepare($sql);

		$param = array(
				'driver_id' => $datas['driver_id'],
				'concrete_attendance_id' => $datas['concrete_attendance_id'],
				'depart_leaving_time' => $datas['depart_leaving_time']
				
		);

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//出発ステータスでのストップ時のデータ登録
static public function depart_arrived_time($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
		
		$sql="
					UPDATE concrete_departs_and_returns
					SET
						depart_arrived_time = :depart_arrived_time
					WHERE
						concrete_departs_and_returns.driver_id =".$datas['driver_id']."
					AND 
						concrete_departs_and_returns.concrete_attendance_id =".$datas['concrete_attendance_id']
						;

		$stmt=$dbh->prepare($sql);

		$param = array( 'depart_arrived_time' => $datas['depart_arrived_time'] );

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

static public function get_concrete_attendance_date($concrete_attendance_id){
	
	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
			
		$sql="
			SELECT date, oil_replenishment_type, oil_replenishment, start_time, end_time
			FROM concrete_attendances
			LEFT JOIN concrete_oil_replenishments ON concrete_attendance_id=$concrete_attendance_id
			WHERE
				concrete_attendances.id = $concrete_attendance_id
			ORDER BY concrete_oil_replenishments.oil_replenishment_type ASC
			";
				
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();
		
		return $datas;
					
	}catch(PDOException $e){

		echo $e->getMessage();

	}
	
}

//配達開始時データ登録(concrete_for_scenes)
static public function start_concrete_for_scenes($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql=" INSERT INTO concrete_for_scenes
					(concrete_attendance_id, destination_company, scene_name, number, quantity,
					loading_state, created)
				VALUES
					(:concrete_attendance_id, :destination_company, :scene_name, :number, :quantity,
					:loading_state, NOW())";
				

		$stmt=$dbh->prepare($sql);

		$param = array( 
				'concrete_attendance_id' => $datas['concrete_attendance_id'],
				'destination_company' => $datas['destination_company'],
				'scene_name' => $datas['scene_name'],
				'number' => $datas['number'],
				'quantity' => $datas['quantity'],
				'loading_state' => $datas['loading_state']
		);

		$stmt->execute($param);
		$scenes_for_concrete_id=$dbh->lastInsertId();
		$dbh->commit();
		
		return $scenes_for_concrete_id;
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}



//配達開始時のデータ登録(concrete_driver_status)
static public function start_concrete_driver_status($datas,$scenes_for_concrete_id){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
					INSERT INTO
						concrete_driver_status
						(driver_id, scenes_for_concrete_id, status, start_time, created)
					VALUES
						(:driver_id, :scenes_for_concrete_id, :status, :start_time, NOW())
						";

		$stmt=$dbh->prepare($sql);

		$param = array(
				'driver_id' => $datas['driver_id'],
				'scenes_for_concrete_id' => $scenes_for_concrete_id,
				'status' => $datas['status'],
				'start_time' => $datas['start_time']
		);

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//配達開始時の配合登録(concrete_attebdances)
static public function set_mix_in_concrete_attendances($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
					UPDATE concrete_attendances
					SET
						mix = :mix
					WHERE
						concrete_attendances.driver_id = ".$datas['driver_id']."
					AND
						concrete_attendances.id =".$datas['concrete_attendance_id']
						;
		
			$stmt=$dbh->prepare($sql);

			$param = array( 'mix' => $datas['mix'] );
			
			$stmt->execute($param);
			$dbh->commit();
							
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//現場編集時のデータ修正
static public function edit_concrete_scene($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
			
		$sql="
					UPDATE concrete_for_scenes
					SET
						destination_company = :destination_company,
						scene_name = :scene_name,
						number = :number,
						quantity = :quantity,
						loading_state = :loading_state
					WHERE
						concrete_for_scenes.id =".$datas['scenes_for_concrete_id']."
					AND
						concrete_for_scenes.concrete_attendance_id =".$datas['concrete_attendance_id'];

		$stmt=$dbh->prepare($sql);

		$param = array( 
				'destination_company' => $datas['destination_company'],
				'scene_name' => $datas['scene_name'],
				'number' => $datas['number'],
				'quantity' => $datas['quantity'],
				'loading_state' => $datas['loading_state'] );

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//配達開始ステータスのストップ時のデータ修正(concrete_driver_status)
static public function end_concrete_driver_status($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
					UPDATE concrete_driver_status
					SET
						end_time = :end_time
					WHERE
						concrete_driver_status.driver_id =".$datas['driver_id']."
					AND
						concrete_driver_status.scenes_for_concrete_id =".$datas['scenes_for_concrete_id']."
					AND
						concrete_driver_status.status = ".$datas['status']
						;

						$stmt=$dbh->prepare($sql);

						$param = array( 'end_time' => $datas['end_time'] );

						$stmt->execute($param);
						$dbh->commit();
							
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//配達開始ステータスのストップ時のデータ修正(concrete_for_scenes)
static public function end_concrete_scene($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
				UPDATE concrete_for_scenes
				SET
					remaining_water = :remaining_water
				WHERE
					concrete_for_scenes.concrete_attendance_id =".$datas['concrete_attendance_id']."
				AND
					concrete_for_scenes.id =".$datas['scenes_for_concrete_id']
					;

					$stmt=$dbh->prepare($sql);

					$param = array( 
							'remaining_water' => $datas['remaining_water']
					 );

					$stmt->execute($param);
					$dbh->commit();
					
					return true;
							
	}catch(PDOException $e){

//		echo $e->getMessage();
		return false;

	}
}

//給油データの登録
static public function insert_oil_replenishment($datas,$i){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
					INSERT INTO
						concrete_oil_replenishments
						(concrete_attendance_id, oil_replenishment_type, oil_replenishment, start_time, end_time, created)
					VALUES
						(:concrete_attendance_id, :oil_replenishment_type, :oil_replenishment, :start_time, :end_time, NOW())
						";

		$stmt=$dbh->prepare($sql);

		$param = array(
				'concrete_attendance_id' => $datas[$i]['concrete_attendance_id'],
				'oil_replenishment_type' => $datas[$i]['oil_replenishment_type'],
				'oil_replenishment' => $datas[$i]['oil_replenishment'],
				'start_time' => $datas[$i]['start_time'],
				'end_time' => $datas[$i]['end_time']
		);
		
		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//点検データの登録
static public function inspection($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		// EDIT
		$inspection_id = $datas['inspection_id'];
		if ( $inspection_id ) {
			$sql=
				"UPDATE
					concrete_inspections
				SET
					concrete_attendance_id = :concrete_attendance_id,
					inspection_time = :inspection_time,
					fuel_leaks = :fuel_leaks,
					engine = :engine,
					brake_pedal = :brake_pedal,
					brake_lever = :brake_lever,
					car_horn = :car_horn,
					defroster = :defroster,
					steering_handle = :steering_handle,
					reflecting_mirror = :reflecting_mirror,
					air_brake = :air_brake,
					clutch = :clutch,
					clip_bolt = :clip_bolt,
					auxiliary_equipment = :auxiliary_equipment,
					tire = :tire,
					radiator = :radiator,
					radiator_cap = :radiator_cap, 
					fan_belt = :fan_belt, 
					oil = :oil,
					jet_cleaning = :jet_cleaning, 
					brake_and_clutch_oil = :brake_and_clutch_oil, 
					chassis_spring = :chassis_spring,
					door_lock = :door_lock, 
					rear_clip_bolt = :rear_clip_bolt, 
					rear_air_tank = :rear_air_tank,
					rear_tire = :rear_tire, 
					rear_battery = :rear_battery, 
					rear_auxiliary_equipment = :rear_auxiliary_equipment,
					loading_device = :loading_device,
					car_bed = :car_bed,
					gas_color = :gas_color,
					rear_chassis_spring = :rear_chassis_spring, 
					tachograph = :tachograph, 
					alarm_tool = :alarm_tool,
					automobile_inspection_certificate = :automobile_inspection_certificate, 
					implement = :implement
				WHERE
					id = ".$inspection_id;
		} else {
			// NEW
			
			$sql="
					INSERT INTO
						concrete_inspections
						(
						concrete_attendance_id, inspection_time, fuel_leaks,
						engine, brake_pedal, brake_lever,
						car_horn, defroster, steering_handle,
						reflecting_mirror, air_brake, clutch, clip_bolt,
						auxiliary_equipment, tire, radiator,
						radiator_cap, fan_belt, oil,
						jet_cleaning, brake_and_clutch_oil, chassis_spring,
						door_lock, rear_clip_bolt, rear_air_tank,
						rear_tire, rear_battery, rear_auxiliary_equipment,
						loading_device,	car_bed, gas_color,
						rear_chassis_spring, tachograph, alarm_tool,
						automobile_inspection_certificate, implement,
						created)
					VALUES
						(:concrete_attendance_id, :inspection_time, :fuel_leaks,
						 :engine, :brake_pedal, :brake_lever,
						 :car_horn, :defroster, :steering_handle,
						 :reflecting_mirror, :air_brake, :clutch,
						  :clip_bolt,
						:auxiliary_equipment, :tire, :radiator,
						:radiator_cap, :fan_belt, :oil,
						:jet_cleaning, :brake_and_clutch_oil, :chassis_spring,
						:door_lock, :rear_clip_bolt, :rear_air_tank,
						:rear_tire, :rear_battery, :rear_auxiliary_equipment,
						:loading_device, :car_bed, :gas_color,
						:rear_chassis_spring, :tachograph, :alarm_tool,
						:automobile_inspection_certificate, :implement, NOW())
						";
			
		}
		
		$stmt=$dbh->prepare($sql);

		$param = array(
				'concrete_attendance_id' => $datas['concrete_attendance_id'],
				'inspection_time' => $datas['inspection_time'],
				'fuel_leaks' => $datas['fuel_leaks'],
				'engine' => $datas['engine'],
				'brake_pedal' => $datas['brake_pedal'],
				'brake_lever' => $datas['brake_lever'],
				'car_horn' => $datas['car_horn'],
				'defroster' => $datas['defroster'],
				'steering_handle' => $datas['steering_handle'],
				'reflecting_mirror' => $datas['reflecting_mirror'],
				'air_brake' => $datas['air_brake'],
				'clutch' => $datas['clutch'],
				'clip_bolt' => $datas['clip_bolt'],
				'auxiliary_equipment' => $datas['auxiliary_equipment'],
				'tire' => $datas['tire'],
				'radiator' => $datas['radiator'],
				'radiator_cap' => $datas['radiator_cap'], 
				'fan_belt' => $datas['fan_belt'], 
				'oil' => $datas['oil'],
				'jet_cleaning' => $datas['jet_cleaning'], 
				'brake_and_clutch_oil' => $datas['brake_and_clutch_oil'], 
				'chassis_spring' => $datas['chassis_spring'],
				'door_lock' => $datas['door_lock'], 
				'rear_clip_bolt' => $datas['rear_clip_bolt'], 
				'rear_air_tank' => $datas['rear_air_tank'],
				'rear_tire' => $datas['rear_tire'], 
				'rear_battery' => $datas['rear_battery'], 
				'rear_auxiliary_equipment' => $datas['rear_auxiliary_equipment'],
				'loading_device' => $datas['loading_device'],
				'car_bed' => $datas['car_bed'],
				'gas_color' => $datas['gas_color'],
				'rear_chassis_spring' => $datas['rear_chassis_spring'], 
				'tachograph' => $datas['tachograph'], 
				'alarm_tool' => $datas['alarm_tool'],
				'automobile_inspection_certificate' => $datas['automobile_inspection_certificate'], 
				'implement' => $datas['implement']
		);
		
		$stmt->execute($param);
		if (empty($inspection_id))
			$inspection_id = $dbh->lastInsertId();
			
		$dbh->commit();

		return $inspection_id;
		
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//終業メーターのデータ登録
static public function end_meters($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
			
		$sql="
					UPDATE concrete_for_meters
					SET
						end_meter = :end_meter
					WHERE
						concrete_for_meters.concrete_attendance_id =".$datas['concrete_attendance_id'];

		$stmt=$dbh->prepare($sql);

		$param = array( 'end_meter' => $datas['end_meter'] );

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//退勤時のデータ登録
static public function end_concrete_attendance($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
			
		$sql="
					UPDATE concrete_attendances
					SET
						leaving_time = :leaving_time
					WHERE
						concrete_attendances.id =".$datas['concrete_attendance_id'];

		$stmt=$dbh->prepare($sql);

		$param = array( 'leaving_time' => $datas['leaving_time'] );

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//その他のデータ登録
static public function concrete_other($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
			
		$sql="
					INSERT INTO
						concrete_other
						(concrete_attendance_id, other, created)
					VALUES
						(:concrete_attendance_id, :other, NOW())"
					;
						

		$stmt=$dbh->prepare($sql);

		$param = array( 
				'concrete_attendance_id' => $datas['concrete_attendance_id'],
				'other' => $datas['other']);

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//請求時間の取得
static public function exist_demand_time($driver_id,$concrete_attendance_id){

	try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
				
			$sql="
				SELECT
					concrete_attendances.id as concrete_attendance_id,
					concrete_demand_time.id as demand_time_id,
					date,
					start_time,
					end_time,
					attendance_time,
					leaving_time
				FROM concrete_attendances
				LEFT JOIN concrete_demand_time 
					ON concrete_attendances.id = concrete_demand_time.concrete_attendance_id
				WHERE 
					concrete_attendances.driver_id = $driver_id
				AND 
					concrete_attendances.id = '$concrete_attendance_id'"
				;

			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$datas=$stmt->fetchAll();
			
			return $datas[0];			
					
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//出勤時のデータ編集
static public function edit_concrete_attendances($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();
			
		$sql1="
					UPDATE concrete_attendances
					SET
						door_number = :door_number,
						branch_name = :branch_name,
						loading_factory = :loading_factory
					WHERE
						concrete_attendances.id =".$datas['concrete_attendance_id']
					;
		
		$stmt=$dbh->prepare($sql1);
			
		$param = array(
				'door_number' => $datas['door_number'],
				'branch_name' => $datas['branch_name'],
				'loading_factory' => $datas['loading_factory']
		);
			
		$stmt->execute($param);
		$dbh->commit();
				
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//行きの出発時のデータ登録(concrete_demand_time)
static public function concrete_return_demand_time_end_time($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
					INSERT INTO
						concrete_demand_time
						(concrete_attendance_id, start_time, created)
					VALUES
						(:concrete_attendance_id, :start_time, NOW())
						";

		$stmt=$dbh->prepare($sql);

		$param = array(
				'concrete_attendance_id' => $datas['concrete_attendance_id'],
				'start_time' => $datas['start_time']
		);

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//帰りの出発時、データ登録(concrete_departs_and_returns)
static public function return_leaving_time($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
				UPDATE
					concrete_departs_and_returns
				SET
					return_leaving_time = :return_leaving_time
				WHERE
					driver_id =".$datas['driver_id']."
				AND 
					concrete_attendance_id =".$datas['concrete_attendance_id'];

		$stmt=$dbh->prepare($sql);

		$param = array(	'return_leaving_time' => $datas['return_leaving_time'] );

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}


//帰りの到着時のデータ登録(concrete_departs_and_returns)
static public function return_arrived_time($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
				UPDATE concrete_departs_and_returns
				SET
					return_arrived_time = :return_arrived_time
				WHERE
					concrete_departs_and_returns.driver_id =".$datas['driver_id']."
				AND
					concrete_departs_and_returns.concrete_attendance_id =".$datas['concrete_attendance_id']
					;

				$stmt=$dbh->prepare($sql);

				$param = array( 'return_arrived_time' => $datas['return_arrived_time'] );

				$stmt->execute($param);
				$dbh->commit();
							
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//帰りの出発時、データ登録(concrete_demand_time)
static public function return_time_end_time($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
				UPDATE 
					concrete_demand_time
				SET 
					end_time = :end_time
				WHERE 
					concrete_demand_time.concrete_attendance_id =".$datas['concrete_attendance_id'];

		$stmt=$dbh->prepare($sql);

		$param = array(	'end_time' => $datas['end_time'] );

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//出勤登録時、同じドライバーで同じ日付のデータがあるかどうか確認
static public function check_date($datas){

	try{
		$dbh=SingletonPDO::connect();
		

		$sql="
				SELECT * FROM concrete_attendances
				WHERE 
					concrete_attendances.driver_id=".$datas['driver_id']."
				AND
					concrete_attendances.date='{$datas['date']}'";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		$check_date_data=$stmt->fetchAll();
				
		return $check_date_data;
		
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//行きの出発時、同じドライバーで同じ日付のデータがあるかどうか確認
static public function check_depart_leaving_time($datas){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
				SELECT * FROM concrete_departs_and_returns
				WHERE
					concrete_departs_and_returns.driver_id =".$datas['driver_id']."
				AND
					concrete_departs_and_returns.concrete_attendance_id =".$datas['concrete_attendance_id']
					;
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
	
		$check_depart_leaving_time=$stmt->fetchAll();
		
		return $check_depart_leaving_time;

	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//行きの出発時、同じドライバーで同じ日付のデータがあった場合更新
static public function update_depart_leaving_time($datas){

	try{
		$dbh=SingletonPDO::connect();


		$sql="
				UPDATE concrete_departs_and_returns
				SET
					depart_leaving_time=:depart_leaving_time
				WHERE
					concrete_departs_and_returns.driver_id =".$datas['driver_id']."	
				AND
					concrete_departs_and_returns.concrete_attendance_id =".$datas['concrete_attendance_id']
					;
		
		$stmt=$dbh->prepare($sql);
		
		$param = array(	'depart_leaving_time' => $datas['depart_leaving_time'] );

		$stmt->execute($param);
		
	}catch(PDOException $e){

	echo $e->getMessage();

	}
}

//行きの到着時、同じドライバーで同じ日付のデータがあるかどうか確認
static public function check_depart_arrived_time($datas,$source_day,$next_day){

	try{
		$dbh=SingletonPDO::connect();


		$sql="
				SELECT * FROM concrete_departs_and_returns
				WHERE
					concrete_departs_and_returns.driver_id=".$datas['driver_id']."
				AND
					concrete_departs_and_returns.depart_arrived_time >= '$source_day'
				AND
					concrete_departs_and_returns.depart_arrived_time <= '$next_day'";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		$check_depart_arrived_time = $stmt->fetchAll();

		return $check_depart_arrived_time;

	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//日報データの取得
static public function daily_oil_data($concrete_attendance_id){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
		SELECT
			oil_replenishment_type, oil_replenishment, start_time, end_time
		FROM concrete_oil_replenishments
		WHERE
		concrete_attendance_id = ".$concrete_attendance_id
		." ORDER BY oil_replenishment_type DESC";
				
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$daily_report_data=$stmt->fetchAll();
		
		return $daily_report_data;
			
	}catch(PDOException $e){

		echo $e->getMessage();
		exit;

	}
}



//日報データの取得
static public function driver_names($driver_id){

	try{
		$dbh=SingletonPDO::connect();
		

		$sql="
		SELECT last_name, first_name
		FROM drivers
		WHERE id =".$driver_id;
				
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$all_driver_names =$stmt->fetchAll();
		$driver_names = $all_driver_names[0];
		
		return $driver_names;
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//帰りの出発時、同じドライバーで同じ日付のデータがあるかどうか確認
static public function check_return_leaving_time($datas){

	try{
		$dbh=SingletonPDO::connect();


		$sql="
				SELECT * FROM concrete_departs_and_returns
				WHERE
					concrete_departs_and_returns.driver_id=".$datas['driver_id']."
					AND
					concrete_departs_and_returns.return_leaving_time='{$datas['return_leaving_time']}'";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		$check_return_leaving_time=$stmt->fetchAll();

		return $check_return_leaving_time;

	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//ドライバーステータス、配達、開始時間のデータ登録
static public function driver_status_start_time($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
				INSERT INTO
					concrete_driver_status
					(driver_id, scenes_for_concrete_id, status, start_time, created)
				VALUES
					(:driver_id, :scenes_for_concrete_id, :status, :start_time, NOW())
					";

		$stmt=$dbh->prepare($sql);

		$param = array(
				'driver_id' => $datas['driver_id'],
				'scenes_for_concrete_id' => $datas['scenes_for_concrete_id'],
				'status' => $datas['status'],
				'start_time' => $datas['start_time']
		);

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//ドライバーステータス、配達、終了時間のデータ更新
static public function driver_status_end_time($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
				UPDATE concrete_driver_status
				SET
					end_time = :end_time
				WHERE
					concrete_driver_status.scenes_for_concrete_id =".$datas['scenes_for_concrete_id']."
				AND
					concrete_driver_status.status =".$datas['status']
			;

		$stmt=$dbh->prepare($sql);

		$param = array(	'end_time' => $datas['end_time'] );

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//コンクリート用業務ステータス　開始
static public function concrete_driver_status_start_time($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
				INSERT INTO
					concrete_driver_status
					(driver_id, status, start_time, created)
				VALUES
					(:driver_id, :status, :start_time, NOW())
					";

		$stmt=$dbh->prepare($sql);

		$param = array(
				'driver_id' => $datas['driver_id'],
				'status' => $datas['status'],
				'start_time' => $datas['start_time']
		);

		$stmt->execute($param);
		$dbh->commit();
		
		$sql = "SELECT LAST_INSERT_ID()";
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$last_id = $stmt->fetch();
		
		return $last_id[0];
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//コンクリート用業務ステータス　終了
static public function concrete_driver_status_end_time($datas){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
				UPDATE concrete_driver_status
				SET
					end_time = :end_time
				WHERE
					id =".$datas['concrete_driver_status_id']."
				AND
					concrete_driver_status.status =".$datas['status']
			;

		$stmt=$dbh->prepare($sql);

		$param = array(	'end_time' => $datas['end_time'] );

		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}		
		
//管理画面から請求時間の変更を行う時の更新プログラム(concrete_attendance_time)
static public function concrete_attendance_date($driver_id){

	try{
		$dbh=SingletonPDO::connect();
			
		$sql="
			SELECT * 
			FROM concrete_attendances
			WHERE
				concrete_attendances.driver_id = $driver_id
			ORDER BY date DESC";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$concrete_attendance_date = $stmt->fetchAll();
	
		return $concrete_attendance_date;
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//管理画面から請求時間の変更を行う時の更新プログラム(UPDATE時)
static public function modified_demand_time($datas,$start_time,$end_time){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$param = array();
		if (!empty($datas['demand_time_id'])) {
			
			$sql="
				UPDATE concrete_demand_time
				SET
					start_time = :start_time,
					end_time = :end_time
				WHERE
					concrete_demand_time.id =".$datas['demand_time_id'];
			
		} else {
			
			$sql="
				INSERT INTO
					concrete_demand_time
					(concrete_attendance_id, start_time, end_time, created)
				VALUES
					(:concrete_attendance_id, :start_time, :end_time, NOW())";
				
			$param['concrete_attendance_id'] = $datas['concrete_attendance_id'];
		}

		$param['start_time'] = $start_time;
		$param['end_time'] = $end_time;
//		echo $sql;
//		var_dump($param);
//		exit;
		$stmt=$dbh->prepare($sql);
			
		$stmt->execute($param);
		$dbh->commit();
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//管理画面から軽油3のみを入力できる画面(データ取得)
static public function concrete_oil_data($concrete_attendance_id){

	try{
		$dbh=SingletonPDO::connect();
			
		$sql="
			SELECT * 
			FROM concrete_oil_replenishments
			JOIN concrete_attendances
			ON concrete_attendances.id = $concrete_attendance_id
			WHERE
				concrete_oil_replenishments.concrete_attendance_id = $concrete_attendance_id";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$concrete_oil_data = $stmt->fetchAll();
	
		return $concrete_oil_data;
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//管理画面から軽油3のみを入力できる画面(新規登録のDB挿入)
static public function admin_insert_oil_replishment($datas){

	try{
		$dbh=SingletonPDO::connect();
			
		
		$sql="
			INSERT INTO
				concrete_oil_replenishments
				(concrete_attendance_id, oil_replenishment_type, oil_replenishment, start_time, end_time, created)
			VALUES
				(:concrete_attendance_id, :oil_replenishment_type, :oil_replenishment, :start_time, :end_time, NOW())
				";

		$stmt=$dbh->prepare($sql);
		$param=array(
				'concrete_attendance_id' => $datas['concrete_attendance_id'],
				'oil_replenishment_type' => $datas['oil_replenishment_type'],
				'oil_replenishment' => $datas['oil_replenishment'],
				'start_time'=>$datas['start_time'],
				'end_time'=>$datas['end_time']
				)
		;
			
		$stmt->execute($param);
		
					
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//管理画面から軽油3のみを入力できる画面(既に登録がある場合のUPDATEのDB挿入)
static public function update_oil_replishment($datas){

	try{
		$dbh=SingletonPDO::connect();
			

		$sql="
			UPDATE　concrete_oil_replenishments
			SET　oil_replenishment = :oil_replenishment_type,
				start_time = :start_time, 
				end_time = :end_time
			WHERE id=".$datas['concrete_attendance_id']."
			AND oil_replenishment_type=3
			";

				$stmt=$dbh->prepare($sql);
		$param=array(
				'oil_replenishment' => $datas['oil_replenishment'],
				'start_time'=>$datas['start_time'],
				'end_time'=>$datas['end_time']
		)
		;
			
		$stmt->execute($param);

			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//洗車・うがいの時間を取得
static public function washing_time($status){

	try{
		$dbh=SingletonPDO::connect();
		$dbh->beginTransaction();

		$sql="
		SELECT *
		FROM work
		JOIN concrete_other
		ON concrete_other.created > work.start
		AND concrete_other.created < work.end
		WHERE
		work.status = $stauts
		"
		;

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$work_data=$stmt->fetchAll();
			
		return $work_data;
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//日報データの取得
static public function daily_report_data($post_datas){

	try{
		$dbh=SingletonPDO::connect();
		

		$sql="
		SELECT
			concrete_attendances.id as concrete_attendance_id,
			concrete_attendances.date,
			concrete_attendances.mix,
			concrete_attendances.door_number,
			concrete_attendances.attendance_time,
			concrete_attendances.leaving_time,
			concrete_attendances.loading_factory,
			concrete_attendances.branch_name,
			concrete_departs_and_returns.*,
			concrete_for_meters.*,
			concrete_demand_time.start_time AS demand_start_time, concrete_demand_time.end_time AS demand_end_time
		FROM concrete_attendances
		LEFT JOIN concrete_demand_time ON concrete_demand_time.concrete_attendance_id = concrete_attendances.id
		LEFT JOIN concrete_departs_and_returns ON concrete_departs_and_returns.concrete_attendance_id = concrete_attendances.id
		LEFT JOIN concrete_for_meters ON concrete_for_meters.concrete_attendance_id = concrete_attendances.id
		WHERE
		concrete_attendances.driver_id =".$post_datas['driver_id']."
		AND
		concrete_attendances.id ='{$post_datas['concrete_attendance_id']}'"
		;
				
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$daily_report_data=$stmt->fetchAll();
			
		return $daily_report_data;
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

static public function getWorkRecordsByStatus($status, $driver_id, $date, $attendance_datetime, $leaving_datetime){

	try{
		$dbh=SingletonPDO::connect();

		if (empty($leaving_datetime))
			$where_end_time = $date." 23:59:59";
		else
			$where_end_time = $leaving_datetime;
			
		$sql="
		SELECT 
			*
		FROM 
			work
		WHERE 
			work.status = $status
		AND
			work.driver_id = $driver_id
		AND
			( work.start >= \"$attendance_datetime\"
				AND
			work.start <= \"$where_end_time\" )
		AND
			( work.end >= \"$attendance_datetime\"
				AND
				work.end < \"$where_end_time\" )
		ORDER BY created DESC
		";
		
//		echo $sql;
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		$data=$stmt->fetchAll();

		return $data;

	}catch(Exception $e){

		echo $e->getMessage();

	}
}

static public function getStatusStartEnd($status, $driver_id, $date, $attendance_datetime, $leaving_datetime){

	try{
		$dbh=SingletonPDO::connect();

		if (empty($leaving_datetime))
			$where_end_time = $date." 23:59:59";
		else
			$where_end_time = $leaving_datetime;
			
		$sql="
		SELECT 
			start_time, end_time
		FROM 
			concrete_driver_status
		WHERE 
			concrete_driver_status.status = $status
		AND
			concrete_driver_status.driver_id = $driver_id
		AND
			( concrete_driver_status.start_time >= \"$attendance_datetime\"
				AND
			concrete_driver_status.start_time <= \"$where_end_time\" )
		AND
			( concrete_driver_status.end_time >= \"$attendance_datetime\"
				AND
				concrete_driver_status.end_time < \"$where_end_time\" )
		ORDER BY created DESC
		LIMIT 1
		";
		
//		echo $sql;
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		$data=$stmt->fetchAll();

		return $data;

	}catch(Exception $e){

		echo $e->getMessage();

	}
}

// comment
static public function getCommentsByWorkId($driver_id, $date, $attendance_datetime, $leaving_datetime){

	try{
		$dbh=SingletonPDO::connect();
		if (empty($leaving_datetime))
			$where_end_time = $date." 23:59:59";
		else
			$where_end_time = $leaving_datetime;
			
		$sql="
		SELECT 
			status, comment, created
		FROM 
			comment
		WHERE 
			driver_id = $driver_id
		AND
			( created >= \"$attendance_datetime\"
				AND
			created <= \"$where_end_time\" )
		";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		$data=$stmt->fetchAll();

		return $data;

	}catch(Exception $e){

		echo $e->getMessage();

	}
}

//日報データの取得(concrete_for_scenesとconcrete_driver_status)
static public function scenes_data($concrete_attendance_id){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
		SELECT
			concrete_for_scenes.id,
			destination_company,
			scene_name,
			number,
			quantity,
			remaining_water, 
			loading_state,
			t2.start_time as start_time,
			t2.end_time as arrived_time,
			t4.start_time as leaving_from_scene_time,
			t4.end_time as return_from_scene_time
		FROM `concrete_for_scenes` 
			LEFT JOIN (
				SELECT *
				FROM concrete_driver_status
				WHERE status=2) t2
			on concrete_for_scenes.id = t2.scenes_for_concrete_id
			LEFT JOIN (
				SELECT *
				FROM concrete_driver_status
				WHERE status=3) t4
			on concrete_for_scenes.id = t4.scenes_for_concrete_id
		WHERE
			concrete_for_scenes.concrete_attendance_id = ".$concrete_attendance_id;
//		echo $sql;
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$scenes_data=$stmt->fetchAll();
			
		return $scenes_data;
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//その他のデータの取得
static public function other_data( $concrete_attendance_id ){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
		SELECT other, created
		FROM concrete_other
		WHERE
			concrete_attendance_id = ".$concrete_attendance_id;

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$other_data = $stmt->fetchAll();
		
		return $other_data;
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//その他のデータの取得
static public function getInspection( $concrete_attendance_id ){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
		SELECT
			inspection_time, fuel_leaks, engine,
			brake_pedal, brake_lever, car_horn,
			defroster, steering_handle, reflecting_mirror,
			air_brake, clutch, clip_bolt,
			auxiliary_equipment, tire, radiator,
			radiator_cap, fan_belt, oil,
			jet_cleaning, brake_and_clutch_oil, chassis_spring,
			door_lock, rear_clip_bolt, rear_air_tank,
			rear_tire, rear_battery, rear_auxiliary_equipment,
			loading_device,	car_bed, gas_color,
			rear_chassis_spring, tachograph, alarm_tool,
			automobile_inspection_certificate, implement
		FROM concrete_inspections
		WHERE
			concrete_attendance_id = ".$concrete_attendance_id;
//echo $sql;
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas = $stmt->fetchObject();
			
		return $datas;
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}

//エラーログの挿入
static public function log_error ( $table, $error ){

	try{
		$dbh=SingletonPDO::connect();
		
		$sql="
			INSERT INTO
				error_logs
				( table_name, error, created)
			VALUES
				( :table_name, :error, NOW())
				";

		$stmt=$dbh->prepare($sql);
		$param=array(
				'table_name' => $table,
				'error' => $error
				)
		;
			
		$stmt->execute($param);
			
	}catch(PDOException $e){

		echo $e->getMessage();

	}
}


	//存在しないプロパティに値をセットした場合エラーを表示させる
	public function __set($prop,$value){
		print("存在しないプロパティ'{$prop}'に値'{$value}'を設定しました\n");
		debug_print_backtrace();
		exit();
	}

	//存在しないプロパティを参照した場合にエラーを表示させる
	public function __get($prop){
		print("存在しないプロパティ'{$prop}'を参照しました\n");
		exit();
	}

	
}
?>