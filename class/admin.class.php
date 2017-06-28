<?php
//管理画面用のクラス　Dataクラスを継承

class Admin extends Data{
	
	/* driver */
	public $id;
	public $company_id;
	public $last_name;
	public $first_name;
	public $furigana;
	public $mobile_tel;
	public $mobile_email;
	public $experience;
	public $no_accident;
	public $car_type;
	public $equipment;
	public $sex;
	public $birthday;
	public $erea;
	public $updated;
	public $created;
	public $driver_message;
	public $regist_number;
	public $driver_number_actual;
	public $sales;
	public $address;
	public $detail;
	public $speed;
	public $start;
	public $end;
	public $work_id;
	public $registration_id;
	public $ios_device_token;
	public $image_name;
	public $is_group_manager;
	
//会社ID指定でドライバーの人数と、最終的にドライバーの位置記録があった時間を取得

public static function getDrivers($company_id){
	
	try{
		$dbh=SingletonPDO::connect();		
	

		//一番直近のデータを取得
		$sql = "SELECT 
					drivers.*,
					geographic.*,
					last_driver_status.*,
					drivers.id as driverId
				FROM 
					company
				JOIN
					geographic ON geographic.company_id = company.id
				JOIN 
					drivers ON drivers.geographic_id = geographic.id	
				JOIN
					last_driver_status 
						ON
					last_driver_status.driver_id = drivers.id 
				WHERE
					company.id = $company_id
				ORDER BY
					last_driver_status.created DESC
				";
			
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas = $stmt->fetchAll();
		
		
		return $datas;
					
		}catch(Exception $e){
	
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

//クラスAdminの終了
}
?>