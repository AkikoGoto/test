<?php
/**
 * 管理画面　ここから各プログラムを呼び出し
 */

//共通定数ファイル読み込み
require_once("../inc/dbconfig.php");
require_once("../inc/application_const.php");

//共通設定ファイル読み込み

require_once("../inc/config.php");
require_once("../inc/user_info.php");

$action=$_GET["action"];

if($action){
	require_once("action/$action.php");
	}else{
	
	require_once("action/top.php");
	}
?>