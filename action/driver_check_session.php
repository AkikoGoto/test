<?php

/**
 * ドライバー画面セッションチェックとドライバー名前など情報をSmartyにアサイン
 * ドライバー以外はドライバー管理画面に入れない
 */


if(!$_SESSION['driver_id']){
		 
	header('Location:index.php?action=driverLogin');
	 
	}
?>