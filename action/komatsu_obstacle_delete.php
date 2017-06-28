<?php
	if($_GET['company_id']){	
	$id=sanitizeGet($_GET['company_id']);
	}
	user_auth($u_id,$id);
	$_SESSION['komatsu_obstacle']['msg'] = $_GET['deletedfilename']."を削除しました。";
	$_SESSION['komatsu_obstacle']['filename'] = $_GET['deletedfilename'];

	header("Location:index.php?action=komatsu_obstacle&id={$u_id}");