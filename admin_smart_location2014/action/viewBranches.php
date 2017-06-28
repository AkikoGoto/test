<?php

/**
 *管理画面　営業所情報詳細
 */

require_once('admin_check_session.php');

try{

	$id=$_GET['id'];
	$dataList=Branch::getByCompanyId($id);
	$company_name=Data::getCompanyName($id);
	$company=$company_name['company_name'];
	
	 }catch(Exception $e){
	
		$message=$e->getMessage();
	}

list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign('company_id',$id);
$smarty->assign("links",$links['all']);

$smarty->assign('company',$company);
$smarty->assign("filename","viewBranches.html");
$smarty->display("template.html");

?>