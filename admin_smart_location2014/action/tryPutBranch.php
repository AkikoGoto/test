<?php
//営業所データ登録確認画面
require_once('admin_check_session.php');

$id_array=array('id');
$keys=array();
$keys=array_merge($id_array,$geographic_array);

//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		$_SESSION[$key]=$datas[$key];
	}

	//編集画面か、新規データ投入か

	if($_POST['edit']){
		
		//編集画面の場合は、営業所番号を投入
		$datas['id']=htmlentities($_POST['id'],ENT_QUOTES, mb_internal_encoding());
		
	}	
				
	try{
		
		//入力データ検証
		
		//県名の取得	
		$prefecture_data=Data::getPrefecturesName($datas['prefecture']);	
		$prefecture_name=$prefecture_data[0]->prefecture_name;
			
		$form_validate=new Validate();
		
		//Google Map APIが反応しない時のために、バリデーションしない
		//$errors2=$form_validate->validate_form_geo($datas);
		
		//$errors=array_merge($errors2);		
		
			if($errors){
		
				$form_validate->show_form($errors);
				$form_validate->lasturl='index.php?action=putBranch';
				
			}else{
					
				foreach($datas as $key => $value){
					
					$smarty->assign("$key",$value);
					
				}
				
					$smarty->assign("js",'<script src="'.GOOGLE_MAP.'"type="text/javascript"></script>
					<script type="text/javascript" src="'.GEOCODING_JS_ADMIN.'">
					</script>');
									//geocodingする住所
					$geocode_address=$prefecture_name.$datas['city'].$datas['ward'].$datas['town'].$datas['address'];
				
					print "<div id=\"geocode_address\">$geocode_address</div>";
					
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

?>