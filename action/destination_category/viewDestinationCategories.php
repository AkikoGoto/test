<?php
/**
 * 配送先カテゴリー一覧を表示するだけ
 * @author Akiko Goto
 * @since 2014/9/18
 * @version 4.4
 */


//会社IDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

try{
	$dataList = DestinationCategory::searchDestinationCategories($company_id);
	
}catch(Exception $e){

	$message=$e->getMessage();

}

list($data, $links)=make_page_link($dataList, 20);	

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("links",$links['all']);
$smarty->assign("company_id",$company_id);
$smarty->assign("filename","destination_category/view_destination_categories.html");
$smarty->display("template.html");

?>