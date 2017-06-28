<?php
//日報用データ確認画面
$id_array=array('id','company_id');
$keys=array();
$keys=array_merge($id_array,$target_array);
//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		if(!empty($_POST[$key])){
			$datas[$key] = htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
			$_SESSION[$key] = $datas[$key];
		}
	}
			
	//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
	driver_company_auth($datas['driver_id'],$session_driver_id,$datas['company_id'],$u_id);
	
	//設置日
	$datas['target_set_date'] = $datas['target_set_date_year']."-".$datas['target_set_date_month']."-". 
									$datas['target_set_date_day'];

									
	//引き取り日
	$datas['picked_date'] = $datas['picked_date_year']."-".$datas['picked_date_month']."-". 
									$datas['picked_date_day'];
									
									
try{
				
	$ip=getenv("REMOTE_ADDR");

	$ban_ip=Data::banip();
	
	//禁止リストにあるIPとの照合
	if($ip==$ban_ip){

	//メッセージ画面を表示する
      header('Location:index.php?action=message&situation=ban_ip');

	}else{


		//入力データ検証
		//開始と終了が、既存のデータにかぶっていないか
		$form_validate=new Validate();
		
		$errors=$form_validate->validate_target($datas);

		if($errors){
		
				$form_validate->show_form($errors);
				$form_validate->lasturl='index.php?action=/target/target_edit';
				
		}else{		
						
			foreach($datas as $key => $value){

				$smarty->assign("$key",$value);
	
			}
			
			
			
			//geocodingする住所
			$geocode_address = $datas['address'];
					
	 	    $geocode_address='<div id="geocode_address">'.$geocode_address.'</div>';
			
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
															
			$smarty->assign("google_map_js",$google_map_js);
			$smarty->assign("geocoding_js",$geocoding_js);
			$smarty->assign("geocode_address",$geocode_address);
			
			//ドライバー氏名をIDから取得
			$driver_name = Driver::getNameById($datas['driver_id']);
			
			$smarty->assign("driver_name", $driver_name[0]['last_name'].'&nbsp;'.$driver_name[0]['first_name']);
			
			$smarty->assign("filename","target/target_confirm.html");
			$smarty->display("template.html");	
			
		}
			
	}
				
			
	}catch(Exception $e){

		die($e->getMessage());
		
	}
	

	
?>