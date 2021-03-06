<?php
/*
 * 配送先一覧を、ルート詳細からポップアップでフォームを表示する
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */


//会社IDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

$query = '';
if(isset($_GET['query'])) {
	$query = $_GET['query'];
}

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

try{		

	$dataList = Destination::searchDestinations($company_id, $query);
	
}catch(Exception $e){

	$message=$e->getMessage();

}

list($data, $links)=make_page_link_internal($dataList);	

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("links",$links['all']);
$smarty->assign("company_id",$company_id);
$smarty->assign("query",$query);
$smarty->assign("filename","destination/view_destination_pop_up.html");
$smarty->display("template_pop_up.html");

?>