<?php

/**
 *管理画面　会社情報検索結果画面
 */

require_once('admin_check_session.php');

try{

	$company_id=$_POST['company_id'];

	header("Location:index.php?action=content&id=$company_id&from_web=0");
	
	 }catch(Exception $e){
	
		$message=$e->getMessage();
	}

?>