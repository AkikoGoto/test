<?php
//記録データ確認画面
$id_array=array('id', 'company_id');
$keys=array();
$keys=array_merge($id_array,$driver_status_array,$recreated_array,$chk_start_end_array);
//$datas[データ名]でサニタイズされたデータが入っている

		foreach($keys as $key){
		if(!empty($_POST[$key])){
			$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		}
	}

	//編集画面か、新規データ投入か

	if($_POST['edit']){
		
		//編集画面の場合は、記録番号を投入
		$datas['id']=htmlentities($_POST['id'],ENT_QUOTES, mb_internal_encoding());
		
	}
	
	//作業ステータス
	$workingStatus = Data::getStatusByCompanyId($datas['company_id']);

//分解した日付を結合
$date_unit = array($datas['time_from_year'], $datas['time_from_month'], $datas['time_from_day']);
$recreated = $datas['time_from_year']."-".$datas['time_from_month']."-". $datas['time_from_day']." ". $datas['time_from_hour'].":". $datas['time_from_minit'].":". $datas['time_from_seconds'];

//開始時刻、終了時刻
$start_worktime = $recreated;
$end_worktime = $datas['time_to_year']."-".$datas['time_to_month']."-". $datas['time_to_day']." ". $datas['time_to_hour'].":". $datas['time_to_minit'].":". $datas['time_to_seconds'];


try{
	$_SESSION['driver_record_map_id'] = $datas['id'];
	$_SESSION['driver_record_map_status'] = $datas['status'];
	$_SESSION['driver_record_map_adderss'] = $datas['address'];
	$_SESSION['driver_record_map_speed'] = $datas['speed'];
	$_SESSION['driver_record_map_sales'] = $datas['sales'];
	$_SESSION['driver_record_map_detail'] = $datas['detail'];
	$_SESSION['driver_record_map_from_year'] = $datas['time_from_year'];
	$_SESSION['driver_record_map_from_month'] = $datas['time_from_month'];
	$_SESSION['driver_record_map_from_day'] = $datas['time_from_day'];
	$_SESSION['driver_record_map_from_hour'] = $datas['time_from_hour'];
	$_SESSION['driver_record_map_from_minit'] = $datas['time_from_minit'];
/*	$_SESSION['driver_record_map_from_seconds'] = $datas['time_from_seconds'];*/
	$_SESSION['driver_record_map_start_time'] = $datas['start'];
	$_SESSION['driver_record_map_end_time'] = $datas['end'];
	
	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $datas['company_id']);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $datas['driver_id']);
	
	}else{

		//会社のユーザーIDと、編集するIDがあっているか確認
		driver_company_auth($datas['driver_id'],$session_driver_id,$datas['company_id'],$u_id);
		
		//ドライバー本人がログインしている場合、会社が編集許可を子なっていなければエラー表示
		driver_editing_banned_check($datas['driver_id'],$session_driver_id,$datas['company_id'],$u_id);
	
	}
				
	$ip=getenv("REMOTE_ADDR");

	$ban_ip=Data::banip();
	
	//禁止リストにあるIPとの照合
	if($ip==$ban_ip){

	//メッセージ画面を表示する
      header('Location:index.php?action=message&situation=ban_ip');

	}else{

		
		//編集データの取得

		if($datas['id']){
			$record_data=Driver::getDriversRecord($datas);
		}
		
		foreach($datas as $key => $value){
			
				//	$value=mb_convert_encoding($value, "UTF-8", "SJIS");									
			$smarty->assign("$key",$value);

		}
			
		//JS割り当て
		if(($carrier=='softbank'||$carrier=='au'||$carrier=='docomo')&&($datas['id'])){
				
				$smarty->assign("message",'携帯では情報の編集はできません。<br>
							必ずパソコンやiPhoneから操作を行ってください。');		
				
			}elseif($carrier=='softbank'||$carrier=='au'||$carrier=='docomo'){
					
				
			}else{
							
	/*			$smarty->assign("js",'<script src="'.GOOGLE_MAP.'" type="text/javascript"></script>
						<script type="text/javascript" src="'.GEOCODING_JS.'"></script>');*/
				
				$js = '<script src="'.GOOGLE_MAP.'" type="text/javascript"></script>
						<script type="text/javascript" src="'.GEOCODING_JS.'"></script>
						';
				$js .= '
				<script type="text/javascript" src="https://www.google.com/jsapi"></script>
				<script type="text/javascript">
				google.setOnLoadCallback(doGeocode); 
				</script>
				';
			$smarty->assign("js", $js);	
							
		}

		//バリデーションでエラーがない場合のみ、確認画面表示	
		//geocodingする住所
		$geocode_address_city=$datas['address'];
				
		$geocode_address='<div id="geocode_address">'.$geocode_address_city.'</div>';
					
//		$smarty->assign("onload_js","onload=\"doGeocode()\"");
					
		$smarty->assign("google_map_js",$google_map_js);
		$smarty->assign("geocoding_js",$geocoding_js);
		$smarty->assign("geocode_address",$geocode_address);
				
		$smarty->assign("record_data",$record_data);
		$smarty->assign("working_status", $workingStatus[0]);
		$smarty->assign("recreated",$recreated);			
		$smarty->assign("filename","driver_record_map_comfirm.html");
		$smarty->assign("target","driver_record_map_putindb");
		$smarty->display("template.html");
	
		}
				
			
	}catch(Exception $e){

		die($e->getMessage());
		
	}
	

?>