<?php
try{
	
	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	if(empty($branch_manager_id) && empty($u_id)){
		header("Location:index.php?action=login");
	}

	
}catch(Exception $e){
	
	$message=$e->getMessage();

}

$smarty->assign("filename","top.html");
$smarty->display('template.html');

?>