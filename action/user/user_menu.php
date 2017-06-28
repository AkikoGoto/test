<?php
/** 
 * ユーザーのログインした後の画面
 * @author Akiko Goto
 * @since 2014/09/26
 */
	 
	 // 共通設定、セッションチェック読み込み	 
	require_once('user_check_session.php');
	$user_id = htmlentities( $_SESSION['user_id'], ENT_QUOTES, mb_internal_encoding());
	$user = Users::getUserById( $user_id );
	$company_id = $user['company_id'];
	$company_option = CompanyOptions::getPublicCompanyDataById($company_id);
	$viewers_unique_id = $company_option['viewers_unique_id'];
	$viewers_url = TEMPLATE_URL."?action=publicMap";
	$smarty->assign("viewers_url", $viewers_url);
	$smarty->assign("page_title",USER_MENU);
	$smarty->assign("filename","user/user_menu.html");
	$smarty->display('template.html');
	

?>