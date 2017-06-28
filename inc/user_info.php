<?php

/**
 * どんなユーザーでログインしているか、と　ユーザーごとの処理
 * @author Akiko Goto
 * @since 2015/02/03
 */

$template_dir=TEMPLATE_DIR;
$compile_dir=TEMPLATE_COMPILE;

//Smartyオブジェクト作成

$smarty=new Smarty();
$smarty->template_dir=$template_dir;
$smarty->compile_dir=$compile_dir;

//セッションスタート
session_start();

//会社　自動ログインの場合
if(empty($_SESSION['u_id'])){
	if (isset($_COOKIE['company_credential'])) {
		$credential = $_COOKIE['company_credential'];
		$idArray = Data::authenticateCredential($credential);
			
		if(isset($idArray[0]['id'])) {
			$_SESSION['u_id'] = $idArray[0]['id'];
			$_SESSION['u_company_name'] = $idArray[0]['company_name'];
			$_SESSION['user_last_name'] = $idArray[0]['last_name'];
			$_SESSION['user_first_name'] = $idArray[0]['first_name'];
			// credentialを新しいものに置き換える
			$credential = Data::regenerateCredential($idArray[0]['id']);
		}
	}

	if (isset($_COOKIE['user_credential'])) {
		$credential = $_COOKIE['user_credential'];
		$idArray = Users::authenticateCredential($credential);

		if(isset($idArray[0]['id'])) {
			$_SESSION['user_id'] = $idArray[0]['id'];
			$_SESSION['user_last_name'] = $idArray[0]['last_name'];
			$_SESSION['user_first_name'] = $idArray[0]['first_name'];
			// credentialを新しいものに置き換える
			$credential = Users::regenerateCredential($idArray[0]['id']);
		}
	}
	
	//セッションがない場合は、明示的にNULLを設定する
	$u_id = NULL;

}else{
	
	//ログインしている会社のユーザーID、会社名
	$u_id = $_SESSION['u_id'];
	$u_company_name = $_SESSION['u_company_name'];
	
	
}


//ドライバー　自動ログインの場合

if (empty($_SESSION['driver_id'])) {

	if (isset($_COOKIE['driver_credential'])) {
		$credential = $_COOKIE['driver_credential'];
		$idArray = Driver::authenticateCredential($credential);

		if(isset($idArray[0]['id'])) {
			$_SESSION['driver_id'] = $idArray[0]['id'];
			$_SESSION['driver_company_id'] = $idArray[0]['company_id'];
			$_SESSION['driver_last_name'] = $idArray[0]['last_name'];
			$_SESSION['driver_first_name'] = $idArray[0]['first_name'];
			// credentialを新しいものに置き換える
			$credential = Driver::regenerateCredential($idArray[0]['id']);
		}
	}else{
		
		$session_driver_id = null;
	
	}
	

}else{

	//セッションでドライバーIDがある場合
	$auto_driver_id = $_SESSION['driver_id'];
	$idArray=Driver::autoLogin($auto_driver_id);

	$_SESSION['driver_id'] = $idArray[0]['id'];
	$_SESSION['driver_company_id'] = $idArray[0]['company_id'];
	$_SESSION['driver_last_name'] = $idArray[0]['last_name'];
	$_SESSION['driver_first_name'] = $idArray[0]['first_name'];

	$session_driver_id = $_SESSION['driver_id'];
	$session_driver_company_id = $_SESSION['driver_company_id'];

	$smarty->assign('driver_last_name', $_SESSION['driver_last_name']);
	$smarty->assign('driver_first_name', $_SESSION['driver_first_name']);
	$smarty->assign('session_driver_id', $session_driver_id);


	define('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1Ijoib25saW5lY29uc3VsdGFudCIsImEiOiJ0NXNSdE1VIn0.48aKT-tYUwPSibdAXP_NAQ');
	define('MAPBOX_PROJECT_ID','onlineconsultant.igf41ndi');

	$_SESSION['is_branch_manager'] = $idArray[0]['is_branch_manager'];

}


if(!empty($_SESSION['is_branch_manager'])){
	
	$is_branch_manager = true;
	$branch_manager_id = $session_driver_id;
	$company_name_array = Driver::getCompanyName($_SESSION['driver_company_id']);
	$u_company_name = $company_name_array['company_name'];
	$session_branch_name = $_SESSION['branch_manager_branch_name'];
	
	$smarty->assign('is_branch_manager', $is_branch_manager);
	$smarty->assign('session_branch_name', $session_branch_name);
	
}else{
	$is_branch_manager = false;
	$branch_manager_id = NULL;
}


//ログインしているユーザー名
if(!empty($u_company_name)){
	$smarty->assign("u_company_name", $u_company_name);
}

//会社IDのアサイン
if(!empty($u_id)){
	
	$smarty->assign("u_id", $u_id);	
	$session_company_id = $u_id;
	$smarty->assign("company_id", $session_company_id);	
	
	//政府の場合、他のJVの履歴が閲覧できる エジソン版のみの機能
	if(!empty($_SESSION['u_company_roll']) && $_SESSION['u_company_roll']=="GOVERNMENT"){
		$is_government = true;
		$smarty->assign("is_government", $is_government);	
	}
	
	
}elseif(!empty($session_driver_company_id)){

	$session_company_id = $session_driver_company_id;	
	$smarty->assign("company_id", $session_company_id);	

}elseif(!empty($is_branch_manager) && $is_branch_manager){
	
	$driver_info_array = Driver::getById($branch_manager_id, $from_web);
	$session_company_id = $driver_info_array[0]->company_id;	
	$smarty->assign("company_id", $session_company_id);	
	
}

//全ての作業ステータスを取得

if (empty($_SESSION['status_1'])||empty($_SESSION['status_2'])||empty($_SESSION['status_3'])||empty($_SESSION['status_4'])) {
	if (!empty($u_id) || !empty($is_branch_manager)) {
	
		if($session_company_id){
			putStatusToSession($session_company_id);
		}

	}
}


$smarty = assignStatusName($smarty);

//ユーザーログイン中の、各種情報があるかどうか
if(!empty($_SESSION['user_id']) && $_SESSION['user_id']){

	$smarty->assign('user_last_name', $_SESSION['user_last_name']);
	$smarty->assign('user_first_name', $_SESSION['user_first_name']);
	$smarty->assign('session_user_id', $_SESSION['user_id']);

}

//未読メッセージがあるかどうか
if(!empty($u_id) || !empty($is_branch_manager)){
	
	if($u_id){
		$has_notread = Message::getMessagesNotAdminRead($u_id);
	}elseif ($is_branch_manager){
		$has_notread = Message::getMessagesNotBranchManagerRead($branch_manager_id);		
	}

	if(!empty($has_notread)){

		$message_link = "<ul class=\"accordion\"><li>";
		$message_link .= "<div class=\"accordion_head\">";
		$message_link .= THERE_IS_MESSAGE_NOT_READ;
		$message_link .= "</div>";
		$message_link .="<ul>";
		
		foreach($has_notread as $each){

			$thread_id = $each['thread_id'];
			$gcm_message = $each['gcm_message'];
			$message_link .= "<li>";
			$empty_message = EMPTY_MESSAGE;
			if(!empty($gcm_message)){
				$message_link .= "<a href = \"?action=/message/message_by_thread&company_id=$session_company_id&thread_id=$thread_id\">$gcm_message</a>";
			}else{
				$message_link .= "<a href = \"?action=/message/message_by_thread&company_id=$session_company_id&thread_id=$thread_id\">$empty_message</a>";
			}
			$message_link .= "</li>";
		}
			
		$message_link .= "</ul></ul>";
			
		$smarty->assign('message', $message_link);
			
	}

	//LITE版かどうか
	$is_lite = Lite::isLite($session_company_id);
	if($is_lite){
		$smarty->assign('is_lite', $is_lite);
	}

	//Mapboxの地図の変更
	if($session_company_id == 10294){

		//福道建設
		define('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1IjoiZnVrdWRva2Vuc2V0c3UiLCJhIjoiSVQ3Xzl2OCJ9.qgv6PxWY70h2NkeJVm82Rg');
		define('MAPBOX_PROJECT_ID','fukudokensetsu.l02nkh2n');

	}else{

		define('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1Ijoib25saW5lY29uc3VsdGFudCIsImEiOiJ0NXNSdE1VIn0.48aKT-tYUwPSibdAXP_NAQ');
		define('MAPBOX_PROJECT_ID','onlineconsultant.igf41ndi');

	}
}
?>