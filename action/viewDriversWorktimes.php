<?php

//ドライバー業務日誌目次


//会社IDが来ているか
if($_GET['company_id']){	
	$id=sanitizeGet($_GET['company_id']);
	}

	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id,$id);
	

try{
	$dataList=Driver::getDrivers($id);
	$companyName=Data::getCompanyName($id);
	$companyName=$companyName['company_name'];
	
	 }catch(Exception $e){
	
		$message=$e->getMessage();
	}


list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("companyName",$companyName);
$smarty->assign("id",$id);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","viewDriversWorktimes.html");
$smarty->display("template.html");

?>