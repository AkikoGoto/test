<?php
//ドライバーデータ登録確認画面
$id_array=array('company_id');
$keys=array();
$keys=array_merge($id_array, $app_setting_array);

	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id,$_POST['company_id']);
	//$datas[データ名]でサニタイズされたデータが入っている
	foreach($keys as $key){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
		//フォームに戻った場合のため、過去のデータを保持
		$_SESSION['pre_data'][$key]=$_POST[$key];
	}

	$status = EDIT;
	//編集画面の場合は、営業所番号を投入
	$datas['company_id'] = htmlentities($_POST['company_id'],ENT_QUOTES, Shift_JIS);
	$company_id = $datas['company_id'];
	
	$ip=getenv("REMOTE_ADDR");

	$ban_ip=Data::banip();
//禁止リストにあるIPとの照合
if($ip==$ban_ip){

	//メッセージ画面を表示する
	header('Location:index.php?action=message&situation=ban_ip');
	
}else{

	//入力データ検証
	$form_validate=new KomatsuValidate();
	$errors=$form_validate->komatsu_validate_form_app_status($datas);
	
	if($errors){
		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=putBranch';
	}else{
		//Smartyへの割り当て
		foreach($datas as $key => $value){
			//ガラケーの場合は一度UTF8データに変換する
			if(is_garapagos()){
				$value = mb_convert_encoding($value, "UTF-8", "SJIS");
			}
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
			$smarty->assign("message",'携帯では自社情報の編集はできません。<br>必ずパソコンやiPhoneから操作を行ってください。');		
		}elseif($carrier=='softbank'||$carrier=='au'||$carrier=='docomo'){
		}else{
			$smarty->assign("js",'<script src="'.GOOGLE_MAP.'"type="text/javascript"></script>
			<script type="text/javascript" src="'.GEOCODING_JS.'"></script>');
		}
	
		//バリデーションでエラーがない場合のみ、確認画面表示
			$smarty->assign( "filename", "komatsu_confirmSetting.html");
			$smarty->assign("datas",$datas);
			$smarty->assign("company_id",$company_id);
			$smarty->assign("prefecture_name",$prefecture_name);
			$smarty->assign("service_names",$service_names);
			$smarty->assign( "target", 'komatsu_putInDbSetting');
			$smarty->assign("google_map_js",$google_map_js);
			$smarty->assign("geocoding_js",$geocoding_js);
			$smarty->assign("geocode_address",$geocode_address);
			$smarty->display("template.html");
	}
}



?>