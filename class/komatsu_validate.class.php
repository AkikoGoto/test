<?php
class KomatsuValidate extends Validate {
	//アプリステータスの必須項目
	public function komatsu_validate_form_app_status($datas){
	
		$errors= $this->validate_form_app_status($datas);
		
		$photo_interval_distance = trim($datas['photo_interval_distance']);
		if(! strlen($photo_interval_distance)){
			$errors[] = '写真を撮影する間隔の距離がありません';
		} else if($photo_interval_distance == 0 or !preg_match('/^\d+$/', $photo_interval_distance)){
			$errors[] = '写真を撮影する間隔の距離は1以上の整数を入力してください';
		}
	
		$photo_interval_time = trim($datas['photo_interval_time']);
		if(! strlen($photo_interval_time)){
			$errors[] = '写真を撮影する時間の間隔がありません';
		} else if($photo_interval_time == 0 or !preg_match('/^\d+$/', $photo_interval_time)){
			$errors[] = '写真を撮影する時間の間隔は1以上の整数を入力してください';
		}
	
	
		return $errors;
	}
	
	public function komatsu_validateCompanyOptions($company_id, $driversOptions) {
		
		$dbh=SingletonPDO::connect();
			
		// 変更対象のドライバーが指定された会社に所属しているかチェックする
		$driver_ids = implode(',', array_keys($driversOptions));
		$sql = "SELECT id FROM drivers WHERE company_id != :company_id and id IN (:driver_ids) LIMIT 1";
		$stmt=$dbh->prepare($sql);
		$param = array(
				'company_id' => $company_id,
				'driver_ids' => $driver_ids
		);
		$stmt->execute();
		if($stmt->rowCount() > 0) {
			$errors[] = "非所属ドライバーが含まれています";
		}
		
		foreach($driversOptions as $options) {
			if(empty($options['should_save_power_when_unplugged']) or empty($options['should_take_photo'])) {
				$errors[] = "設定項目が存在しません";
				break;
			}
		}
				
	}
}