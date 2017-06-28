<?php
//DB設定ファイル
require_once("inc/dbconfig.php");

//共通定数ファイル読み込み
//require_once("inc/concrete_const.php");
require_once("inc/server_const.php");
require_once("inc/application_const.php");

//設定ファイル読み込み
//テストサーバー
//require_once("../smart_location_test/inc/config.php");
//require_once("../smart_location_test/inc/mobile_config.php");
//ローカル
require_once("inc/config.php");
require_once("inc/user_info.php");
require_once("inc/mobile_config.php");

if(!empty($_GET["action"])){
	$action=$_GET["action"];
}else{
	$action= null;
}

if($action){
	
	//テストサーバー
	//require_once("../smart_location_test/action/$action.php");
	
	//ローカル
	require_once("action/$action.php");
	
	}else{
	//テストサーバー
	//require_once("../smart_location_test/action/top.php");
	
	//ローカル
	require_once("action/top.php");
	
	}
?>