<?php

/**
 *管理画面　ドライバー情報詳細
 */

require_once("../class/admin.class.php");

require_once('admin_check_session.php');


//営業所IDが来ているか
if($_GET['id']){	
	$id=sanitizeGet($_GET['id']);
	}


try{

	//営業所番号がある時は、営業所ごとのドライバーが帰ってくる
//	$dataList=Driver::getDrivers($id , $branch_id);
	$dataList=Admin::getDrivers($id);

	}catch(Exception $e){
	
		$message=$e->getMessage();
	}


list($data, $links)=make_page_link($dataList);	


$smarty->assign("data",$data);
$smarty->assign("companyName",$companyName);
$smarty->assign("id",$id);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","viewDrivers.html");
$smarty->display("template.html");

?>