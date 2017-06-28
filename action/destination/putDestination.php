<?php
/*
 * 配送先情報入力　フォームを表示するだけ
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */


//会社ID、配送先IDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if($_GET['destination_id']){
	$destination_id=sanitizeGet($_GET['destination_id'],ENT_QUOTES);
}

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

try{		

	//配送先IDが指定されていれば、編集のため、元のデータを表示
	if($destination_id){
		$status='EDIT';
		$smarty->assign("status",$status);
		
		$data = Destination::getById($destination_id);
		
		if(!empty($data['category_id'])){
			$data['categories']=explode(',',$data['category_id']);
		}
		
		$existed_categoryList = $data['categories'];
		$smarty->assign("existed_categoryList",$existed_categoryList);
		
		$smarty->assign("data",$data);
		
	}

	//カテゴリーリスト
	$categoryList = DestinationCategory::searchDestinationCategories($company_id);

	//フォームなどで戻った時のために、セッションにデータを格納
	if($_SESSION['destination_data']){
		$smarty->assign("pre_data",$_SESSION['destination_data']);
	}


}catch(Exception $e){

	$message=$e->getMessage();

}

$smarty->assign("js",'<script src="'.GOOGLE_MAP.' "type="text/javascript"></script>');

$smarty->assign("categoryList",$categoryList);
$smarty->assign("company_id",$company_id);
$smarty->assign("filename","destination/put_destination.html");
$smarty->display("template.html");

?>