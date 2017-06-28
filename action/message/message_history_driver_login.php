<?php

//メッセージ送信履歴表示画面　ドライバーが送受信をWebで見る用　ログインだけして、ログインできたらリダイレクト

	
if($_GET['login_id']) {

	$login_id = sanitizeGet($_GET['login_id']);
	$passwd = sanitizeGet($_GET['passwd']);
	
	$idArray=Driver::login($login_id,$passwd);
	
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
			
		$driver_id = $idArray[0]['id'];
       	$_SESSION['driver_id'] = $driver_id;
        $_SESSION['driver_last_name'] = $idArray[0]['last_name'];
        $_SESSION['driver_first_name'] = $idArray[0]['first_name'];
        $_SESSION['driver_company_id'] = $idArray[0]['company_id'];
        
        header("Location:index.php?action=/message/message_history_driver&driver_id=$driver_id");
			
	}else{
		$message = "ログインIDとパスワードが間違っています。";
	}
}else{
	$message = "不正なアクセスです。";
}


list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("id",$id);
$smarty->assign("links",$links['all']);

$smarty->assign('message',$message);

//ドライバー用メッセージ表示画面の場合は、リンク先を変更する
$smarty->assign("by_driver", 1);
$smarty->assign("driver_id", $driver_id);

$smarty->assign("filename","messages/message_history_driver.html");
$smarty->display("template_sp.html");

?>