<?php
try{
	//会社IDが来ているか
	if($_GET['id']) {
		$company_id = sanitizeGet($_GET['id']);
	}else{
		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");
		exit;
	}
	//営業所番号がある時は、営業所ごとのドライバーが帰ってくる
	$companyName=Data::getCompanyName( $company_id );
	$companyName=$companyName['company_name'];
	$dataList=Users::getUsersByCompanyId( $company_id );
	
	list($data, $links)=make_page_link($dataList);
	
}catch(Exception $e){
	$message=$e->getMessage();
}

$smarty->assign("company_id",$company_id);
$smarty->assign("companyName",$companyName);
$smarty->assign('dataList',$dataList);
$smarty->assign('data', $data);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","viewUsers.html");
$smarty->display("template.html");

?>