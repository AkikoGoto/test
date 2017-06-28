<?php
/**
 * 積雪・渋滞一覧を表示
 * @author Akiko Goto
 * @since 2015/5/08
 */

//会社IDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if($_GET['branch_id']){
	$post_branch_id=sanitizeGet($_GET['branch_id'],ENT_QUOTES);
	
}


	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	//営業所の取得
	$branchList=Branch::getByCompanyId($company_id);
	
	if(!empty($branch_manager_id)){
			
		$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);	
		
	}else{
		
		$branch_id = null;
	}


	try{
	
		$dataList = Information::viewInformations($company_id,$post_branch_id);

	}catch(Exception $e){
	
		$message=$e->getMessage();
	
	}

list($data, $links)=alarms_page_link($dataList);	

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
if(count($branchList) > 1){
	$smarty->assign("branch_list",$branchList);
}
$smarty->assign("links",$links['all']);
$smarty->assign("company_id",$company_id);
$smarty->assign("filename","information/view_informations.html");
$smarty->display("template.html");

?>