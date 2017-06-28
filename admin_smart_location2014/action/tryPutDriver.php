<?php
//データ登録確認画面
require_once('admin_check_session.php');

$keys=$drivers_array;

$driver_id = sanitizeGet($_GET['driver_id']);

//$datas[データ名]でサニタイズされたデータが入っている

foreach($keys as $key){
	$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}
$ip=getenv("REMOTE_ADDR");

$ban_ip=Data::banip();
	
//禁止リストにあるIPとの照合
if($ip==$ban_ip){

	//メッセージ画面を表示する
      header('Location:index.php?action=message&situation=ban_ip');

}else{

	try{
	
	//入力データ検証

	$form_validate=new Validate();

	if($driver_id){
		$status='EDIT';
		$smarty->assign("id",$driver_id);
	}else{
		$status='NEWDATA';		
	}

	$errors=$form_validate->validate_form_driver($datas, $status, $_FILES);
	
		if($errors){
	
		$form_validate->show_form($errors);
		$form_validate->lasturl='index.php?action=putDriver';
		}else{
				
			//確認画面表示
			
			foreach($datas as $key => $value){

				$smarty->assign("$key",$value);
			
			}		
	
			//画像アップロード
			$image_info = try_image_upload($_FILES, $datas);
					
			//画像イメージのみ、別のSmartyデータ形式にする
			$smarty->assign("image_info",$image_info);

	
			//営業所情報を名前で取ってくる
			$geographic_name=Branch::getById($datas['geographic_id']);
			$geographic_name=$geographic_name[0]->name;

		}
	
	}catch(Exception $e){
	die($e->getMessage());
	
	}
}

	$smarty->assign("geographic_name",$geographic_name);
	$smarty->assign("company_name",$company_name);
	$smarty->assign("geographic_name",$geographic_name);
	$smarty->assign("filename","confirmDriver.html");
	$smarty->assign("target",'putindbDriver');
	$smarty->display("template.html");


?>