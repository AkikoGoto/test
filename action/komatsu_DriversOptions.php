<?php

//ドライバー情報詳細

//営業所IDが来ているか
if($_GET['id']){	
	$id=sanitizeGet($_GET['id']);
	}

if($_GET['branch_id']){	
	$branch_id=sanitizeGet($_GET['branch_id']);
	}

	user_auth($u_id,$id);
try{
	if(!empty($_POST['options'])) {
		$isSuccess = KomatsuDriver::komatsu_ReplaceDriversOptions($id, $_POST['options']);
		if(!isSuccess) {
			$form_validate->show_form($errors);
			$form_validate->lasturl = $_SERVER[REQUEST_URI];
		}
	}
	

	//営業所番号がある時は、営業所ごとのドライバーが帰ってくる
	$dataList=KomatsuDriver::getDrivers($id , $branch_id);
	$companyName=Data::getCompanyName($id);
	$companyName=$companyName['company_name'];
	
	$branchList=Branch::getByCompanyId($id);


	}catch(Exception $e){
	
		$message=$e->getMessage();
	}
	$uploaddir = Komatsu_obstacle::get_uploaded_directory($u_id);
	$csvMetaData = Komatsu_obstacle::get_file_info($uploaddir);

list($data, $links)=make_page_link($dataList);	

$smarty->assign('dataList',$dataList);
$smarty->assign('branchList',$branchList);
$smarty->assign('csvMetaData',$csvMetaData);
$smarty->assign("data",$data);
$smarty->assign("companyName",$companyName);
$smarty->assign("id",$id);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","komatsu_DriversOptions.html");
$smarty->display("template.html");

?>