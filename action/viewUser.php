<?php

try{

	$id = sanitizeGet($_GET['id']);
	$dataList = Users::getUserById( $id );
	$drivers = Users::getDriversUserViewing( $id, $dataList['company_id'] );
	
}catch(Exception $e){
	
	$message=$e->getMessage();
}

$smarty->assign('dataList',$dataList);
$smarty->assign('drivers',$drivers);
$smarty->assign("filename","viewUser.html");
$smarty->display("template.html");

?>