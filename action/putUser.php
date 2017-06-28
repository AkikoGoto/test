<?php
//ドライバー情報入力


//会社ID、ドライバーIDが来ているか
if($_GET['user_id']){	
	$get_user_id = sanitizeGet($_GET['user_id'],ENT_QUOTES, 'Shift_JIS');
}
	
if($_GET['id']){
	$company_id=sanitizeGet($_GET['id'],ENT_QUOTES, 'Shift_JIS');
}
	
	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id,$company_id);		

	try{
			
		if($company_id){
			$from_web=0;
			$companyData=Data::getById($company_id, $from_web);
			$smarty->assign("company_id",$company_id);
		}

		//ドライバーIDが指定されていれば、編集のため、元のデータを表示
		if($get_user_id){
			$from_web=0;
			$userData=Users::getUserById($get_user_id);
			$smarty->assign("userData",$userData);
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
	
$smarty->assign('id',$get_user_id);
$smarty->assign('companyData',$companyData[0]);
$smarty->assign("companyList",$companyList);
$smarty->assign("serviceList",$serviceList);
$smarty->assign("filename","put_user.html");
$smarty->display("template.html");

?>