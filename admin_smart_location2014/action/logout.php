<?php
/**
 * 管理画面　ログアウト
 */

session_start();
$_SESSION = array();
session_destroy();

setcookie('u_id','');

header('Location:index.php?action=login');

?>