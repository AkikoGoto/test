<?php

//データ検証

class Concrete_Validate{
		
	//出退勤の検証
	public function validate_form_concrete_attendances($datas){
				
		$errors=array();
	
		if(! strlen(trim($datas['driver_id']))){
			$errors[]="ドライバーが選択されていません";
		}
		
		if(! strlen(trim($datas['date']))){
			$errors[]="日付がありません";
		}
		
		if(! strlen(trim($datas['door_number']))){
			$errors[]="ドア番号がありません";
		}
		
		if(! strlen(trim($datas['attendance_time']))){
			$errors[]="出勤時間がありません";
		}
		
		if(! strlen(trim($datas['branch_name']))){
			$errors[]="営業所名がありません";
		}
		
		if(! strlen(trim($datas['start_meter']))){
			$errors[]="始業メーターの情報がありません";
		}
		return $errors;
	 
	}
	
	//工場内始業メーターデータの検証
	public function validate_form_start_factory_meter($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['start_in_factory_meter']))){
			$errors[]="工場内始業メーターがありません";
		}
	
		return $errors;
	
	}
	
	//工場内終業メーターデータの検証
	public function validate_form_end_factory_meter($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['end_in_factory_meter']))){
			$errors[]="工場内終業メーターがありません";
		}
	
		return $errors;
	
	}
	
	//工場内始業メーターデータの検証
	public function validate_form_depart_leaving_time($datas){
	
		$errors=array();

		if(! strlen(trim($datas['driver_id']))){
			$errors[]="ドライバーIDがありません";
		}
		
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['depart_leaving_time']))){
			$errors[]="出発時間がありません";
		}
			
		return $errors;
	
	}
	
	//工場内始業メーターデータの検証
	public function validate_form_depart_arrived_time($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['driver_id']))){
			$errors[]="ドライバーIDがありません";
		}
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['depart_arrived_time']))){
			$errors[]="行きの到着時間がありません";
		}
			
		return $errors;
	
	}
	
	//配達開始時のデータ検証
	public function validate_form_start_concrete_scene($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['driver_id']))){
			$errors[]="ドライバーIDがありません";
		}
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['status']))){
			$errors[]="業務ステータスがありません";
		}
		
		/*
		if(! strlen(trim($datas['destination_company']))){
			$errors[]="配達先会社名がありません";
		}
		
		if(! strlen(trim($datas['scene_name']))){
			$errors[]="現場名がありません";
		}
		
		if(! strlen(trim($datas['number']))){
			$errors[]="台目がありません";
		}
		
		if(! strlen(trim($datas['quantity']))){
			$errors[]="数量がありません";
		}
		*/
		
		if(! strlen(trim($datas['start_time']))){
			$errors[]="開始時間がありません";
		}
		
		return $errors;
	
	}
	
	//現場の編集時のデータ検証
	public function validate_form_edit_concrete_scene($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['scenes_for_concrete_id']))){
			$errors[]="現場IDがありません";
		}
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	/*
		if(! strlen(trim($datas['destination_company']))){
			$errors[]="配達先会社名がありません";
		}
	
		if(! strlen(trim($datas['scene_name']))){
			$errors[]="現場名がありません";
		}
	
		if(! strlen(trim($datas['number']))){
			$errors[]="台目がありません";
		}
	
		if(! strlen(trim($datas['quantity']))){
			$errors[]="数量がありません";
		}
	*/
		return $errors;
	
	}
		
	//配達開始ステータスのストップ時のデータ検証
	public function validate_form_end_concrete_scene($datas){
	
		$errors=array();
		
		if(! strlen(trim($datas['driver_id']))){
			$errors[]="ドライバーIDがありません";
		}
		
		if(! strlen(trim($datas['scenes_for_concrete_id']))){
			$errors[]="現場IDがありません";
		}
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['status']))){
			$errors[]="業務ステータスがありません";
		}
	
//		if(! strlen(trim($datas['remaining_water']))){
//			$errors[]="残水情報がありません";
//		}

		if(! strlen(trim($datas['end_time']))){
			$errors[]="終了時間がありません";
		}
			
		return $errors;
	
	}
	
	//給油データの検証
	public function validate_form_oil_replenishment($datas,$i){
	
		$errors=array();
	
		if(! strlen(trim($datas[$i]['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
		
		if(! strlen(trim($datas[$i]['oil_replenishment_type']))){
			$errors[]="油のタイプのデータがありません";
		}
				
		if(! strlen(trim($datas[$i]['oil_replenishment']))){
			$errors[]="軽油補給量の値がありません";
		}
		
		if(! strlen(trim($datas[$i]['start_time']))){
			$errors[]="開始時間がありません";
		}
		
		if(! strlen(trim($datas[$i]['end_time']))){
			$errors[]="終了時間がありません";
		}
		
		return $errors;
	
	}
	
	//点検時のデータ検証
	public function validate_form_inspection($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
		
		if(! strlen(trim($datas['inspection_time']))){
			$errors[]="点検時間の情報がありません";
		}
		
		return $errors;
	
	}
	
	//終業メーターデータの検証
	public function validate_form_end_meter($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['end_meter']))){
			$errors[]="終業メーターがありません";
		}
	
		return $errors;
	
	}
	
	//退勤時データの検証
	public function validate_form_end_concrete_attendance($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['leaving_time']))){
			$errors[]="退勤時間の情報がありません";
		}
	
		return $errors;
	
	}
	
	
	//その他のデータ検証
	public function validate_form_concrete_other($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="作業IDがありません";
		}
		
		return $errors;
	
	}
	
	//UPDATE時の開始時間と終了時間のデータ検証
	public function validate_form_demand_time($datas,$start_time,$end_time){
	
		$errors=array();
		
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
		
		if(! strlen(trim($datas['start_hour']))){
			$errors[]="更新する開始時間がありません";
		}
	
		if(! strlen(trim($datas['start_minit']))){
			$errors[]="更新する終了時間がありません";
		}
		
		if((strtotime($start_time) > strtotime($end_time))){
			$errors[]="開始時間と終了時間の値が不正です。もう一度値を入力して下さい。";
		}
	
		return $errors;
	}
	
	//ドア番号・営業所名のデータ検証
	public function validate_form_edit_concrete_attendances($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
		
		if(! strlen(trim($datas['door_number']))){
			$errors[]="ドア番号がありません";
		}
	
		if(! strlen(trim($datas['branch_name']))){
			$errors[]="営業所名がありません";
		}
		
		if(! strlen(trim($datas['loading_factory']))){
			$errors[]="工場名がありません";
		}
	
		return $errors;
	
	}
	
	//帰りの出発時のデータの検証
	public function validate_form_return_leaving_time($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['driver_id']))){
			$errors[]="ドライバーIDがありません";
		}
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['return_leaving_time']))){
			$errors[]="帰り時の出発がありません";
		}
		
		return $errors;
	
	}
	
	//帰りの到着時のデータの検証
	public function validate_form_return_arrived_time($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['driver_id']))){
			$errors[]="ドライバーIDがありません";
		}
	
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['return_arrived_time']))){
			$errors[]="帰り時の出発がありません";
		}
						
		return $errors;
	
	}
	
	//配達時、開始時間の時のデータ検証
	public function validate_form_driver_status_start_time($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['driver_id']))){
			$errors[]="ドライバーIDがありません";
		}
	
		if(! strlen(trim($datas['scenes_for_concrete_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['status']))){
			$errors[]="業務ステータスがありません";
		}
	
		if(! strlen(trim($datas['start_time']))){
			$errors[]="開始時間がありません";
		}
	
		return $errors;
	
	}
	
	//配達時、開始時間の時のデータ検証
	public function validate_form_driver_status_end_time($datas){
	
		$errors=array();
		
		if(! strlen(trim($datas['scenes_for_concrete_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['status']))){
			$errors[]="業務ステータスがありません";
		}
	
		if(! strlen(trim($datas['end_time']))){
			$errors[]="終了時間がありません";
		}
	
		return $errors;
	
	}
	
	//業務ステータスの登録検証
	public function validate_form_concrete_driver_status_start_time($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['driver_id']))){
			$errors[]="ドライバーIDがありません";
		}
	
		if(! strlen(trim($datas['status']))){
			$errors[]="業務ステータスがありません";
		}
	
		if(! strlen(trim($datas['start_time']))){
			$errors[]="開始時間がありません";
		}
	
		return $errors;
	
	}
	
	public function validate_form_concrete_driver_status_end_time($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['status']))){
			$errors[]="業務ステータスがありません";
		}
	
		if(! strlen(trim($datas['end_time']))){
			$errors[]="終了時間がありません";
		}
	
		return $errors;
	
	}
	
	//管理画面用、給油データの検証
	public function validate_form_update_oil_replenishment($datas,$start_time,$end_time){
	
		$errors=array();
		
		if(! strlen(trim($datas['concrete_attendance_id']))){
			$errors[]="生コン出退勤IDがありません";
		}
	
		if(! strlen(trim($datas['oil_replenishment_type']))){
			$errors[]="油のタイプのデータがありません";
		}
	
		if(! strlen(trim($datas['oil_replenishment']))){
			$errors[]="軽油補給量の値がありません";
		}
	
		if(! strlen(trim($datas['start_time']))){
			$errors[]="開始時間がありません";
		}
	
		if(! strlen(trim($datas['end_time']))){
			$errors[]="終了時間がありません";
		}
			
		if( strtotime($start_time) > strtotime($end_time)){
			$errors[]='開始時間と終了時間が不正です。もう一度入力して下さい。';
		}
		
		return $errors;
	
	}
	
	//ドア番号・営業所名のデータ検証
	public function validate_form_daily_report_select($datas){
	
		$errors=array();
	
		if(! strlen(trim($datas['driver_id']))){
			$errors[]="ドライバーIDがありません";
		}
	
		if(! strlen(trim($datas['time_from_year']))){
			$errors[]="初めの年の値がありません";
		}
	
		if(! strlen(trim($datas['time_from_month']))){
			$errors[]="初めの月の値がありません";
		}
		
		if(! strlen(trim($datas['time_from_day']))){
			$errors[]="初めの日にちの値がありません";
		}
		
		return $errors;
	
	}
	
	//エラー表示
	public function show_form($errors){
		//$errors=array();
		$smarty=new Smarty();
	
		//config.phpで指定したものが入らなかったためディレクトリを指定
	
		//$smarty->template_dir='./templates';
		//$smarty->compile_dir='./templates_c';
	
		$smarty->template_dir=TEMPLATE_DIR;
		$smarty->compile_dir=TEMPLATE_COMPILE;
	
	
		$smarty->assign("errors",$errors);
	
		//キャリア判別
		$carrier=get_carrier();
	
		//携帯版の場合、Shift_jisフィルタをかぶせる
		$script_path=$_SERVER['SCRIPT_NAME'];
	
		if( $carrier=='docomo'||$carrier=='au'||$carrier=='softbank'){
			//モバイル版のUTF-8からShift_JIS変換
			$smarty->register_outputfilter("Encode_utf8tosjis");
		}
	
	
		//CSS割り当て
		if($carrier=='softbank'||$carrier=='au'){
	
			$smarty->assign("css",'<LINK rel="stylesheet" href="templates/css/mobile.css"
			type="text/css" media="handheld">');
	
		}elseif($carrier=='docomo'){
	
		}elseif($carrier==''){
	
			$smarty->assign("css",'<LINK rel="stylesheet" href="templates/css/pc.css"
			type="text/css" media="screen">');
	
		}
	
		$smarty->assign("carrier",$carrier);
		$smarty->assign("filename","error.html");
		$smarty->display("template.html");
	
	}
	

	
public function confirm_form_driver($datas,$status){

	}
	
public function confirm_form_branch($datas,$status,$prefecture_name){
	
	//config.phpで指定したものが入らなかったためディレクトリを指定
	

	

	}
	
	
	
}
?>