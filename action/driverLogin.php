<?php

/**
 * ドライバー　ログイン
 */
if($session_driver_id){

	$message= "すでにログインしています";

}elseif($u_id){

	$message= "グループとしてログインしています。<br>ドライバー、サブグループマネージャーとしてログインする場合は、いったんログアウトをしてください。";

}else{
	// id がPOSTでわたって来ていたら、ログインの試み
	try {

		$is_garapagos=is_garapagos($carrier);

		if($_POST['login_id']) {

			$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
			$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());

			$idArray=Driver::login($login_id,$passwd);

			// 存在すれば、ログイン成功
			if(isSet($idArray[0]['last_name'])) {
					
				//ログインを記憶する場合、クッキーを設定
				if($_POST['autologin']){
					//自動ログイン用クレデンシャル生成
					$credential = Driver::createCredential($idArray[0]['id']);
						
				}

				$_SESSION['driver_id'] = $idArray[0]['id'];
				$_SESSION['driver_last_name'] = $idArray[0]['last_name'];
				$_SESSION['driver_first_name'] = $idArray[0]['first_name'];
				$_SESSION['driver_company_id'] = $idArray[0]['company_id'];
				$_SESSION['driver_company_id'] = $idArray[0]['company_id'];
				$_SESSION['is_branch_manager'] = $idArray[0]['is_branch_manager'];

				//簡単ログイン用に、個別識別番号を登録
//				$mobile_id=unit_no($carrier);

				//携帯電話個体識別番号があれば登録

			/*	if($mobile_id) {

					$dbh=SingletonPDO::connect();

					$driver_id=$idArray[0]['id'];
						
					$sql = "UPDATE
									drivers
								SET
									mobile_id=:mobile_id
								WHERE
								    id=$driver_id 
								";

						
					$stmt=$dbh->prepare($sql);
					$param = array(
						
						'mobile_id' => $mobile_id
						
					);
					$stmt->execute($param);

				}
*/
				if($carrier=='docomo'){
					header('Location:index.php?action=driver_status'.'&'.$sid);
				}elseif ($idArray[0]['is_branch_manager']){
					header('Location:index.php');
				}else{
					header('Location:index.php?action=driver_status');
				}
				//	exit(0);
				// 存在しなければ、ログイン不成功
			} else {

	   header('Location:index.php?action=message_mobile&situation=fail_login');

			}
		}
		// 例外の捕捉
	} catch (PDOException $e) {
		die($e->getMessage() . 'データベースのエラーです。管理者に連絡してください');
	}
}

$smarty->assign('message', $message);
$smarty->assign('is_garapagos', $is_garapagos);

$smarty->assign("filename","driverLogin.html");
$smarty->display("template.html");

?>