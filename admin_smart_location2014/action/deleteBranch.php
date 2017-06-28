<?php

/**
 *管理画面　データ単位で営業所情報消去 
 */

require_once('admin_check_session.php');

$id=$_GET['id'];

	Branch::deleteBranch($id);

    header('Location:index.php?action=message&situation=deleteData');
	
?>