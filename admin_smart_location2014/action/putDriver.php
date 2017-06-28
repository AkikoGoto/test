<?php
//ドライバー情報入力

require_once('admin_check_session.php');

//会社ID、ドライバーIDが来ているか
if($_GET['driver_id']){	
	$driver_id=htmlentities($_GET['driver_id'],ENT_QUOTES, mb_internal_encoding());
	}
	
if($_GET['company_id']){	
	$company_id=htmlentities($_GET['company_id'],ENT_QUOTES, mb_internal_encoding());
	}
	
	try{
			
		if($company_id){
			$from_web=0;
			$companyData=Data::getById($company_id, $from_web);
			$smarty->assign("company_id",$company_id);
	
		}

		//ドライバーIDが指定されていれば、編集のため、元のデータを表示
		if($driver_id){
			$from_web=0;
			$driverData=Driver::getById($driver_id);
			$smarty->assign("driverData",$driverData[0]);
	
			$smarty->assign('driver_id',$driver_id);	
		}

	$branchList=Branch::getBranches($company_id);
	//会社一覧の取得　メモリーを大量に利用するのでコメントアウト
	//	$companyList=Data::getCompanies();
		
	}catch(Exception $e){
		
			$message=$e->getMessage();

	}

//画像
$image_info = make_image_array($driverData);
$smarty->assign('image_info',$image_info);	
	
$smarty->assign('companyData',$companyData[0]);
$smarty->assign("branchList",$branchList);
$smarty->assign("serviceList",$serviceList);

$smarty->assign("filename","put_driver.html");
$smarty->display("template.html");

?>