<?php
/**
 * ルートを表示するドライバーの一覧
 */


	//会社IDが来ているか
	if($_GET['company_id']){
		$company_id=sanitizeGet($_GET['company_id']);
	}

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

try{

	if($is_branch_manager && $branch_manager_id){

		$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);
		$dataList = Driver::getDrivers($company_id, $branch_id);

	}elseif(!empty($u_id)){

		$dataList = Driver::getDrivers($company_id);

	}

	$companyName=Data::getCompanyName($company_id);
	$companyName=$companyName['company_name'];

	 }catch(Exception $e){

		$message=$e->getMessage();
	}


list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("companyName",$companyName);
$smarty->assign("id",$company_id);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","transport_route/viewDriversRoutes.html");
$smarty->display("template.html");

?>