<?php
//データ登録確認画面
require_once('admin_check_session.php');

$id_array=array('id','invoice');
$keys=array();
$keys=array_merge($id_array,$company_array,$geographic_array,$invices_array);

	//仮登録情報かどうかのフラグ
	$datas['activation']=htmlentities($_POST['activation'],ENT_QUOTES, mb_internal_encoding());	
	
//serviceのみ配列
if($_POST['service']){
	 
		foreach($_POST['service'] as $services){

			//エラー時のデータ表示のため
			$_SESSION['services'][]=$services;			 
			$datas['services'][]=htmlentities($services,ENT_QUOTES, mb_internal_encoding());
		}
	}

//$datas[データ名]でサニタイズされたデータが入っている
	foreach($keys as $key){
	
		//$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());	
		$datas[$key]=$_POST[$key];	
		
		$_SESSION[$key]=$datas[$key];

	}	

$ip=getenv("REMOTE_ADDR");

$ban_ip=Data::banip();
	
	//禁止リストにあるIPとの照合
	if($ip==$ban_ip){

	//メッセージ画面を表示する
      header('Location:index.php?action=message&situation=ban_ip');

	}else{

		try{
	
		//県名の取得	
		$prefecture_data=Data::getPrefecturesName($datas['prefecture']);	
		$prefecture_name=$prefecture_data[0]->prefecture_name;
		
		//サービス名の取得
// 		if($datas['services']){

// 			$service_data=Data::getServiceName($datas['services']);	
		
// 			for($i=0,$num_service=count($service_data);$i<$num_service;$i++){

// 				$service_names[]=$service_data[$i][0];
			
// 			}
// 		}
		
		
		//入力データ検証
		
		$form_validate=new Validate();
		
		$errors1=$form_validate->validate_form_company($datas, $_FILES);
		$errors2=$form_validate->validate_form_geo($datas);
		
			if($datas['id']){
				
			$errors3=$form_validate->validate_form_fromWeb_exited($datas);
			$errors4=Array();
		
			}else{
					
				$errors3=$form_validate->validate_form_fromWeb($datas);
				$errors4=$form_validate->validate_form_mail($datas);
			}
			
			$errors=array_merge($errors1,$errors2,$errors3,$errors4);
				
		
			if($errors){
		
				$form_validate->show_form($errors);
		
			}else{
				
				//トピックのみテキストでの改行を<br>に変換
				//	$topic=nl2br($topic);	
					
				//確認画面表示
					
				foreach($datas as $key => $value){
					
					$smarty->assign("$key",$value);
								
				}
				
				$image_info = try_image_upload($_FILES, $datas);
					
				//画像イメージのみ、別のSmartyデータ形式にする
				$smarty->assign("image_info",$image_info);
				
				$smarty->assign("js",'<script src="'.GOOGLE_MAP.'"type="text/javascript"></script>
						<script type="text/javascript" src="'.GEOCODING_JS_ADMIN.'">
						</script>');
				
				//geocodingする住所
				$geocode_address=$prefecture_name.$datas['city'].$datas['ward'].$datas['town'].$datas['address'];
			
				//print "<div id=\"geocode_address\">$geocode_address</div>";	
				$smarty->assign("geocode_address_div","<div id=\"geocode_address\">$geocode_address</div>");
				$smarty->assign("onload_js","onload=\"doGeocode()\"");
				$smarty->assign("prefecture_name",$prefecture_name);
				$smarty->assign("service_names",$service_names);
				$smarty->assign("target","putindb");
				$smarty->assign("google_map_js",$google_map_js);
				$smarty->assign("geocoding_js",$geocoding_js);
				$smarty->assign("geocode_address",$geocode_address);
				$smarty->assign("activation",$datas['activation']);
				
				$smarty->assign("filename","confirm.html");
				$smarty->display("template.html");
						
			}
		
		}catch(Exception $e){
		die($e->getMessage());
		
		}
}



?>