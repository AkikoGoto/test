<?php
	if($_GET['id']){	
	$id=sanitizeGet($_GET['id']);
	}
	user_auth($u_id,$id);
	//メッセージ
	$msg = "";
	if(!empty($_SESSION['komatsu_obstacle']['msg'])){
		$msg = $_SESSION['komatsu_obstacle']['msg'];
	}
	
	//保存ディレクトリ
	$uploaddir = Komatsu_obstacle::get_uploaded_directory($u_id);
	
	//ファイル削除
	if(!empty($_SESSION['komatsu_obstacle']['filename'])){
		$msg = Komatsu_obstacle::delete_file($_SESSION['komatsu_obstacle']['filename'],$uploaddir);
	}
	$csvMetaData = Komatsu_obstacle::get_file_info($uploaddir);
	
	
	
	$smarty->assign("id",$u_id);
	$smarty->assign("msg",$msg);
	$smarty->assign("csvInfo",$csvMetaData);
	$smarty->assign("filename","komatsu_obstacle.html");
	$smarty->display("template.html");
	$_SESSION['komatsu_obstacle']=null;