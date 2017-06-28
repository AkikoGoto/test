<?php

//サービス追加
require_once('admin_check_session.php');

$service=$_POST['service'];

try{

	$service=Data::putService($service);
	
	header("Location:index.php");

	exit(0);
	}catch(Exception $e){
	die($e->getMessage());
	
	}

?>