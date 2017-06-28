<?php

if(!empty($_POST['company_id'])){
	$company_id=sanitizeGet($_POST['company_id'],ENT_QUOTES);
}

$navi_area_id=sanitizeGet($_POST['navi_area_id'],ENT_QUOTES);

foreach($area_array as $key){

	$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());

	//フォームに戻った場合のため、過去のデータを保持
	$_SESSION['pre_data'][$key]=$_POST[$key];

}

//テストコード（company_idかgeographic_idか決まっていないため）
$datas['company_id'] = $company_id;

$transport_route_id = $_POST['transport_route_id'];

$datas['transport_route_id'] = $transport_route_id;

$form_validate=new Validate();
$errors=$form_validate->validate_form_area($datas);

if($errors){

	$form_validate->show_form($errors);
	$form_validate->lasturl='index.php?action=/navi_area/putArea';

}else{
	if(isset($navi_area_id)){
		//ナビエリアIDがある場合は編集なのでアップデート
		$datas['id'] = $navi_area_id;
		NaviArea::update($datas);
		unset($_SESSION['pre_data']);
		header("Location:index.php?action=message_mobile&situation=after_edit_navi_area&transport_route_id=$transport_route_id&company_id=$company_id");
	}else{
		//ナビエリアIDが無い場合は新規登録なのでインサート
		NaviArea::insert($datas);
		unset($_SESSION['pre_data']);
		header("Location:index.php?action=message_mobile&situation=after_put_navi_area&transport_route_id=$transport_route_id&company_id=$company_id");
	}
}
