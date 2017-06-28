<?php
/*
 * 配送先一件を表示
 * @author Akiko Goto
 * @since 2014/09/22
 * @version 4.4
 */


//会社IDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if(isset($_GET['destination_id'])) {
	$destination_id = sanitizeGet($_GET['destination_id']);

}

company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

try{

	$data = Destination::getById($destination_id);	

}catch(Exception $e){

	$message=$e->getMessage();

}

$smarty->assign("data",$data);
$smarty->assign("links",$links['all']);
$smarty->assign("company_id",$company_id);
$smarty->assign("query",$query);
$smarty->assign("filename","destination/view_destination.html");
$smarty->display("template.html");

?>