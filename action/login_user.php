<?php
//Webから申し込みのあったグループのみ、ログイン可能

if($session_driver_id){

	$message= "ドライバーとしてログインしています。<br>グループとしてログインしたい場合、一度ログアウトしてからグループとしてログインしてください。";

}elseif($u_id){

	$message= "グループユーザーとしてすでにログインしています";

}else{

	// id がPOSTでわたって来ていたら、ログインの試み
	try {
		if($_POST['id']) {

			$id = htmlentities($_POST['id'],ENT_QUOTES, mb_internal_encoding());
			$pass = htmlentities($_POST['pass'],ENT_QUOTES, mb_internal_encoding());

			$idArray=User::login($id, $pass);

			// 存在すれば、ログイン成功
			if(isSet($idArray[0]['id'])) {
					
				//ログインを記憶する場合、クッキーを設定
				if($_POST['autologin']){

					//1か月後にクッキーの有効期限が切れる
					setcookie('user_id',$idArray[0]['id'],time()+60*60*24*30);
						
				}
				 
				$_SESSION['user_id'] = $idArray[0]['id'];
				$_SESSION['nick_name'] = $idArray[0]['nick_name'];
				$message= LOGIN_SUCCESS;
    
		 	// 存在しなければ、ログイン不成功
			} else {

				$faillogin = DATE_ERROR_FAILLOGIN;
				header("$faillogin");
				 
			}
		}
		// 例外の捕捉
	} catch (PDOException $e) {
		die($e->getMessage() . 'データベースのエラーです。管理者に連絡してください');
	}
}

$smarty->assign('message', $message);
$smarty->assign('user_id', $_SESSION['user_id']);
$smarty->assign('nick_name', $_SESSION['nick_name']);
$smarty->assign("filename","login_user.html");
$smarty->display("template.html");

?>
