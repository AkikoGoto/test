<?php

/**
 * ユーザー画面セッションチェック
 * ユーザー以外はユーザー用画面に入れない
 */


if(!$_SESSION['user_id']){
		 
	header('Location:index.php?action=user/userLogin');
	exit;
	
}
?>