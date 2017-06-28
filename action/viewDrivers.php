<?php

//ドライバー情報詳細

//営業所IDが来ているか
if($_GET['id']){
	$id=sanitizeGet($_GET['id']);
}

if($_GET['branch_id']){
	$branch_id=sanitizeGet($_GET['branch_id']);
}

try{

	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id, $id);

	//営業所番号がある時は、営業所ごとのドライバーが帰ってくる
	$dataList=Driver::getDrivers($id , $branch_id);
	$companyName=Data::getCompanyName($id);
	$companyName=$companyName['company_name'];

	$branchList=Branch::getByCompanyId($id);


}catch(Exception $e){

	$message=$e->getMessage();
}


list($data, $links)=make_page_link($dataList, 20);

$smarty->assign('dataList',$dataList);
$smarty->assign('branchList',$branchList);

$smarty->assign("data",$data);
$smarty->assign("companyName",$companyName);
$smarty->assign("id",$id);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","viewDrivers.html");
$smarty->display("template.html");

?>