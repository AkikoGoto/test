<?php

/**
 *管理画面　ドライバー情報一覧
 */

require_once('admin_check_session.php');

try{

	$id=$_GET['id'];
	$dataList=Driver::getById($id);
			
	 }catch(Exception $e){
	
		$message=$e->getMessage();
	}


list($data, $links)=make_page_link($dataList);


//画像
$image_info = make_image_array($dataList);
$smarty->assign('image_info',$image_info);	

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data[0]);
$smarty->assign("filename","viewDriver.html");
$smarty->display("template.html");

?>