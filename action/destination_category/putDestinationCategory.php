<?php
/*
 * 配送先カテゴリー入力　フォームを表示するだけ
 * @author Akiko Goto
 * @since 2014/9/12
 * @version 4.4
 */


//会社ID、配送先IDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if($_GET['destination_categories_id']){
	$destination_categories_id=sanitizeGet($_GET['destination_categories_id'],ENT_QUOTES);
}


	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

try{		

	//配送先IDが指定されていれば、編集のため、元のデータを表示
	if($destination_categories_id){
		$status='EDIT';
		$smarty->assign("status",$status);
		
		$data = DestinationCategory::getById($destination_categories_id);
		$smarty->assign("data",$data[0]);
		
	}

	//フォームなどで戻った時のために、セッションにデータを格納
	if($_SESSION['destination_data']){
		$smarty->assign("pre_data",$_SESSION['destination_data']);
	}


}catch(Exception $e){

	$message=$e->getMessage();

}

$smarty->assign("company_id",$company_id);
$smarty->assign("filename","destination_category/put_destination_category.html");
$smarty->display("template.html");

?>