<?php

//共通定数ファイル読み込み
require_once("inc/jv_const.php");
require_once("inc/server_const.php");
require_once("inc/application_const.php");


//DB設定ファイル
require_once("inc/dbconfig.php");

//設定ファイル読み込み
require_once("inc/config.php");
require_once("inc/user_info.php");
require_once("inc/mobile_config.php");

if(!empty($_GET["action"])){
	$action=$_GET["action"];
}else{
	$action= null;
}

if($action){
	
	//ローカル
	require_once("action/$action.php");
	
	}else{
	
	//ローカル
	require_once("action/top.php");
	
	}
?>