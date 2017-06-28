<?php
//営業所データ登録確認画面
$id_array=array('id');
$keys=array();
$keys=array_merge($id_array,$geographic_array);

//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}

	//編集画面か、新規データ投入か

	if($_POST['edit']){
		
		//編集画面の場合は、営業所番号を投入
		$datas['id']=htmlentities($_POST['id'],ENT_QUOTES, mb_internal_encoding());
		
	}	

	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id,$datas['company_id']);	
				
	$ip=getenv("REMOTE_ADDR");

	$ban_ip=Data::banip();
	
	//禁止リストにあるIPとの照合
	if($ip==$ban_ip){

	//メッセージ画面を表示する
      header('Location:index.php?action=message&situation=ban_ip');

	}else{

		try{
		
		//入力データ検証
		
		//県名の取得	
		$prefecture_data=Data::getPrefecturesName($datas['prefecture']);	
		$prefecture_name=$prefecture_data[0]->prefecture_name;

		$form_validate=new Validate();
		
		$errors2=$form_validate->validate_form_geo($datas);
		
		$errors=array_merge($errors2);
				
			if($errors){
		
				$form_validate->show_form($errors);
				$form_validate->lasturl='index.php?action=putBranch';
				
			}else{
					
				foreach($datas as $key => $value){
					
				//	$value=mb_convert_encoding($value, "UTF-8", "SJIS");									
					$smarty->assign("$key",$value);
				}
				
					//JS割り当て
					if($carrier=='softbank'||$carrier=='au'||$carrier=='docomo'){
				
							$smarty->assign("message",'携帯では自社情報の編集はできません。<br>
							必ずパソコンやiPhoneから操作を行ってください。');		
						}else{
					
							$smarty->assign("js",'<script src="'.GOOGLE_MAP.'"type="text/javascript"></script>
								<script type="text/javascript" src="'.GEOCODING_JS.'">
								</script>');
					
						}
					
					//geocodingする住所
					$geocode_address_city=$datas['city'].$datas['ward'].$datas['town'].$datas['address'];
				
					$geocode_address=$prefecture_name.$geocode_address_city;
					//geocodingする住所
					   $geocode_address='<div id="geocode_address">'.$geocode_address.'</div>';
					
					$smarty->assign("onload_js","onload=\"doGeocode()\"");
					$smarty->assign("filename","confirmBranch.html");
					$smarty->assign("prefecture_name",$prefecture_name);
					$smarty->assign("target","putindbBranch");
					$smarty->assign("google_map_js",$google_map_js);
					$smarty->assign("geocoding_js",$geocoding_js);
					$smarty->assign("geocode_address",$geocode_address);
					$smarty->display("template.html");

					
				
			
			}
		
		}catch(Exception $e){
		die($e->getMessage());
		
		}
	}

?>