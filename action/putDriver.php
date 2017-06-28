<?php
//ドライバー情報入力


//会社ID、ドライバーIDが来ているか
if($_GET['driver_id']){	
	$driver_id=sanitizeGet($_GET['driver_id'],ENT_QUOTES, 'Shift_JIS');
	}
	
if($_GET['company_id']){	
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES, 'Shift_JIS');
	}
	
	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id,$company_id);		

	try{
			
		if($company_id){
			$from_web=0;
			$companyData=Data::getById($company_id, $from_web);
			$smarty->assign("company_id",$company_id);

			//営業所リストの表示
			$branchList=Branch::getBranches($company_id);
			$smarty->assign("branchList",$branchList);
		
		}

		//ドライバーIDが指定されていれば、編集のため、元のデータを表示
		if($driver_id){
			$from_web=0;
			$driverData=Driver::getById($driver_id, $from_web = 0);
			$smarty->assign("driverData",$driverData[0]);
			$status='EDIT';
			$smarty->assign("status",$status);			
		}

	    //フォームなどで戻った時のために、セッションにデータを格納	
	    if($_SESSION['pre_data']){			
	    	$smarty->assign("pre_data",$_SESSION['pre_data']);				    	
	    }
		
	}catch(Exception $e){
		
			$message=$e->getMessage();

	}
	
$smarty->assign('companyData',$companyData[0]);
$smarty->assign("companyList",$companyList);
$smarty->assign("serviceList",$serviceList);
$smarty->assign("filename","put_driver.html");
$smarty->display("template.html");

?>