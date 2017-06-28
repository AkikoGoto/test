<?php

	$id=sanitizeGet($_GET['geographic_id']);
	$company_id=sanitizeGet($_GET['company_id']);
		
//会社のユーザーIDと、編集するIDがあっているか確認
	if($u_id==$company_id){	
		Branch::deleteBranch($id);
	    header('Location:index.php?action=message_mobile&situation=deleteData');
	}else{
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
	}
	
?>