<?php
//タクシー会社からのデータ登録確認画面

$id_array=array('id');
$keys=array();

//初期登録時と、データ編集時で投入する値が違う
if($_POST['id']){

	$keys=array_merge($id_array,$company_array,$geographic_array);

}else{

	$keys=array_merge($id_array,$company_array,$registration_array,$geographic_array);
		
}


//$datas[データ名]でサニタイズされたデータが入っている
foreach($keys as $key){

	$_SESSION[$key]=$_POST[$key];
	$datas[$key]=$_POST[$key];

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

		if($datas['services']){
			$service_data=Data::getServiceName($datas['services']);
		}

		for($i=0,$num_service=count($service_data);$i<$num_service;$i++){
			$service_names[]=$service_data[$i][0];
		}


		//入力データ検証

		$form_validate=new Validate();

		$errors1=$form_validate->validate_form_company($datas);
		$errors2=$form_validate->validate_form_geo($datas);

		if($datas['id']){
				
			$errors3=$form_validate->validate_form_fromWeb_exited($datas);
			$errors4=array();

		}else{
				
			$errors3=$form_validate->validate_form_fromWeb($datas);
			$errors4=$form_validate->validate_form_mail($datas);
		}

		$errors=array_merge($errors1,$errors2,$errors3,$errors4);


		if($errors){

			$form_validate->show_form($errors);
			//なんで営業所になっているの？調べる
//			$form_validate->lasturl='index.php?action=putBranch';
			$form_validate->lasturl='index.php?action=putin';
			
		}else{
				
			//Smartyへの割り当て
			foreach($datas as $key => $value){
				$smarty->assign("$key",$value);
			}

			$target="putindb";

			//　自データの編集か
			if($_POST['id']){

				//会社のユーザーIDと、編集するIDがあっているか確認
				user_auth($u_id,$datas['id']);

			}

			//JS割り当て
			if(($carrier=='softbank'||$carrier=='au'||$carrier=='docomo')&&($datas['id'])){

				$smarty->assign("message",'携帯では自社情報の編集はできません。<br>
							必ずパソコンやiPhoneから操作を行ってください。');		
			}elseif($carrier=='softbank'||$carrier=='au'||$carrier=='docomo'){
					

			}else{
					
				$smarty->assign("js",'<script src="'.GOOGLE_MAP.'"type="text/javascript"></script>
								<script type="text/javascript" src="'.GEOCODING_JS.'"></script>');
					
			}

			//バリデーションでエラーがない場合のみ、確認画面表示
			//geocodingする住所
			$geocode_address_city=$datas['city'].$datas['ward'].$datas['town'].$datas['address'];

			//県名のみ、一度Shift_JISデータに変換する
			if(is_garapagos()){
				$geo_prefecture_name=mb_convert_encoding($prefecture_name, "SJIS", "UTF-8");
			}else{
				$geo_prefecture_name=$prefecture_name;
			}
			$geocode_address=$geo_prefecture_name.$geocode_address_city;
				
			//geocodingする住所
			//ガラケーの場合UTF-8からShift_JISに
			if(is_garapagos()){
				$geocode_address_convert=mb_convert_encoding($geocode_address, "UTF-8", "SJIS");
			}else{
				$geocode_address_convert=$geocode_address;
			}
			$geocode_address='<div id="geocode_address">'.$geocode_address_convert.'</div>';
				
			$smarty->assign("onload_js","onload=\"doGeocode()\"");
				
			if($_POST['id']){

				$smarty->assign("filename","confirm.html");

			}else{

				$smarty->assign("filename","confirm_registration.html");
					
			}
			$smarty->assign("filename","confirm.html");
				
				
			$smarty->assign("prefecture_name",$prefecture_name);
			$smarty->assign("service_names",$service_names);
			$smarty->assign("target",$target);
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