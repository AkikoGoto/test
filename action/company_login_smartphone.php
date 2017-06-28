<?php

//メッセージ送信履歴表示画面　ドライバーが送受信をWebで見る用　ログインだけして、ログインできたらリダイレクト

if($_GET['login_id'] && $_GET['passwd']) {
	
	$login_id = sanitizeGet($_GET['login_id']);
	$passwd = sanitizeGet($_GET['passwd']);
	
	if ($u_id){
		$message= "すでにログインしています";	
	} else {
		
		if($session_driver_id){
			$_SESSION = array();
			setcookie('driver_id','');
			Driver::deleteCredential();
		}
		
		$idArray=Data::login($login_id, $passwd);
			
		// 存在すれば、ログイン成功
		if(isSet($idArray[0]['id'])) {
			//ログインを記憶する場合、クッキーを設定
			if($_POST['autologin']){
				//自動ログイン用クレデンシャル生成
				$credential = Data::createCredential($idArray[0]['id']);
			}
				
			$_SESSION['u_id'] = $idArray[0]['id'];
			$_SESSION['u_company_name'] = $idArray[0]['company_name'];
			$u_id=$_SESSION['u_id'];
			$u_company_name = $_SESSION['u_company_name'];
				    	  	
		   	//LITE版かどうか　ログインした瞬間に、通常版かLITE版か判定するため
			$is_lite = Lite::isLite($u_id);
				
			// 存在しなければ、ログイン不成功
		} else {
			$faillogin = DATE_ERROR_FAILLOGIN;
			header("$faillogin");
			exit;
		}
	}
	
	//目的のページへ遷移
	$target = sanitizeGet($_GET['target']);
	$path = null;
	switch ($target) {
		case "DRIVER_LOGS":
			$path = "viewDriversMemo&company_id=";
			break;
		case "DRIVER_MAP":
			$path = "getDriverMap&company_id=";
			break;
		case "MESSAGE":
			$path = "/message/message_form&company_id=";
			break;
		case "ROUTES":
			$path = "viewDriversMemo&company_id=";
			break;
		case "DESTINATIONS":
			$path = "/destination/viewDestinations&company_id=";
			break;
		case "EDIT_DRIVERS":
			$path = "viewDrivers&id=";
			break;
		case "EDIT_COMPANY":
			$path = "putin&id=";
			break;
		case "GEOGRAPHICS":
			$path = "viewBranches&by=company&company_id=";
			break;
		case "SETTING":
			$path = "setting&company_id=";
			break;
	}
	header("Location:index.php?action=".$path.$u_id);
	exit;
	
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

$smarty->assign("filename","driverStatus.html");
$smarty->display("template_sp.html");