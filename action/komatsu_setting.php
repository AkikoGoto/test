<?php
//タクシー会社の情報登録依頼フォームを表示するだけ

//エラーなどの場合、前回入力した値をデフォルトにする
	$session=$_SESSION;

	$company_id=sanitizeGet($_GET['company_id']);
	
	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth( $u_id, $company_id);
	$smarty->assign("filename","komatsu_setting.html");
	
try{
	
	//ステータスを取得
	if($company_id){
		
		$dataList=KomatsuData::getStatusAndInterval( $company_id );
		//ミリセカンドの形式から、分に直す
		//入力値×60×1000 = 入力値ミリセカンド
		//ミリセカンド = ミリセカンド/60/1000
		$mili_second = $dataList[0]->time;
		$time = $mili_second / 60 / 1000;
		$photo_interval_distance = $dataList[0]->photo_interval_distance;
		$photo_interval_time = $dataList[0]->photo_interval_time;
	}
}catch(Exception $e){
	$message=$e->getMessage();
}

	$smarty->assign('company_id',$company_id);
	if($dataList){
		//編集画面、仮データ登録時のデータ表示
		$smarty->assign('data', $dataList[0]);
		$smarty->assign('time', $time);
		$smarty->assign('photo_interval_distance', $photo_interval_distance);
		$smarty->assign('photo_interval_time', $photo_interval_time);
	}else{
		//エラーで戻ってきているときは、エラー前の情報表示
		$smarty->assign('session',$session);
	}

	
//メッセージの最大文字数
$smarty->assign('max_message',MESSAGE_MAX);

$smarty->display("template.html");



?>