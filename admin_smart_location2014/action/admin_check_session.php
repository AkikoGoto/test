<?php

/**
 * 管理画面セッションチェック
 * 管理者以外は管理画面に入れない
 */


if($_SESSION['u_id']!=ADMIN_ID){
	 header('Location:index.php?action=login');
}

?>