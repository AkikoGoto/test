<?php
//タクシー会社の情報登録依頼フォームを表示するだけ

//エラーなどの場合、前回入力した値をデフォルトにする
	$session=$_SESSION;

	$company_id=sanitizeGet($_GET['company_id']);
	
	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth( $u_id, $company_id);
	$smarty->assign("filename","setting.html");
	
try{
	
	//ステータスを取得
	if($company_id){
		
		$dataList=Data::getStatusAndInterval( $company_id );
		$driverList=Driver::getDriversWithRegId( $company_id, false , null );
		$viewerList = Users::getViewersWithDriversByCompanyId( $company_id );
//		var_dump($viewerList[0]['drivers']);
		
		$publicCompanyData = CompanyOptions::getPublicCompanyDataById( $company_id );
		$is_public = $publicCompanyData['is_public'];
		$unique_id = $publicCompanyData['public_unique_id'];
		$is_users_viewing = $publicCompanyData['is_users_viewing'];
//		$viewers_unique_id = $publicCompanyData['viewers_unique_id'];
		
		if (!empty($unique_id))
			$public_url = TEMPLATE_URL."?action=publicMap&unique_id=".$unique_id;
			
		if (!empty($is_users_viewing))
			$viewers_url = TEMPLATE_URL."?action=user/user_menu";
		
		//ミリセカンドの形式から、分に直す
		//入力値×60×1000 = 入力値ミリセカンド
		//ミリセカンド = ミリセカンド/60/1000
		$mili_second = $dataList[0]->time;
		$time = $mili_second / 1000;
		
	}
	
}catch(Exception $e){
	
	$message=$e->getMessage();
	
}

	$smarty->assign('company_id',$company_id);
	if($dataList){
		//編集画面、仮データ登録時のデータ表示
		$smarty->assign('data',$dataList[0]);
		$smarty->assign('is_public', $is_public);
		$smarty->assign('is_users_viewing', $is_users_viewing);
		$smarty->assign("viewers_url", $viewers_url);
		$smarty->assign('time',$time);
		$smarty->assign('public_url', $public_url);
		$smarty->assign('viewers_url', $viewers_url);
		
		$smarty->assign('driverList',$driverList);
		$smarty->assign('viewerList',$viewerList);
		
		
	}else{
		//エラーで戻ってきているときは、エラー前の情報表示
		$smarty->assign('session',$session);
	}
	
//メッセージの最大文字数
$smarty->assign('max_message',MESSAGE_MAX);

$smarty->display("template.html");



?>