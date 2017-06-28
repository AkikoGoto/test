<?php

//メッセージ送信フォーム表示画面


	//会社IDが来ているか
	if($_GET['company_id']){	
		$company_id = sanitizeGet($_GET['company_id']);
	}

	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	if(!empty($branch_manager_id)){

		$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);
	
	}else{

		$branch_id = null;
	
	}
	

try{
	
	$dataList=Driver::getDriversWithRegId($company_id, true, $branch_id);
	
	 }catch(Exception $e){
	
		$message=$e->getMessage();
	}


list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("id",$company_id);
$smarty->assign("branch_id",$branch_id);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","messages/message_form.html");
$smarty->display("template.html");

?>