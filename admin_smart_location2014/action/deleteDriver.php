<?php

//データ単位でドライバー情報消去 

$id=sanitizeGet($_GET['id']);

	Driver::deleteDriver($id);

    header('Location:index.php?action=message&situation=deleteData');
	
?>