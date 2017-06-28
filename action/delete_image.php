<?php
/*
 * Mapアイコンの消去
 * ver2.6から
 */
	$company_id=sanitizeGet($_GET['company_id']);
	$driver_id=sanitizeGet($_GET['driver_id']);
	
	//会社のユーザーIDと、編集するIDがあっているか確認

	if($u_id==$company_id){	
		driver::deleteImage($driver_id);
	    header("Location:index.php?action=putDriver&driver_id=$driver_id&company_id=$company_id");
	}else{
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
	}
	
?>