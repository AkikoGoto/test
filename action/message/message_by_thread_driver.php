<?php

//メッセージ送信履歴スレッドごとの表示画面 ドライバー専用

	//ドライバーIDが来ているか
	if($_GET['driver_id']){	
		$driver_id=sanitizeGet($_GET['driver_id']);
	}

	//スレッドIDが来ているか
	if($_GET['thread_id']){	
		$thread_id=sanitizeGet($_GET['thread_id']);
	}elseif($_GET['thread_id'] =="0"){
		$thread_id = 0;
	}else{
		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
	}
	

	//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
	driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);

try{

	$dataList = Message::getMessagesByThreadDriver($driver_id, $thread_id);
	
	 }catch(Exception $e){
	
		$message=$e->getMessage();
	}


list($data, $links)=make_page_link($dataList);

$smarty->assign("is_driver",1);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("driver_id",$driver_id);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","messages/message_by_thread.html");
$smarty->display("template_sp.html");

?>