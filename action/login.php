<?php
/**
 * ログイン　グループ用ログインとドライバー用のログインを兼用
 */


if(!empty($u_id) || !empty($session_driver_id)){

	$message= "すでにログインしています";	
	
}else{
	
	
	// id がPOSTでわたって来ていたら、ログインの試み
	try {
	     if(!empty($_POST['id'])) {
	
	     	$id = htmlentities($_POST['id'],ENT_QUOTES, mb_internal_encoding());
	    	$pass = htmlentities($_POST['pass'],ENT_QUOTES, mb_internal_encoding());

	    	//グループ管理者かどうか
	    	$group_admin_id_Array=Data::login($id, $pass);
		    
	    	if(isSet($group_admin_id_Array[0]['id'])) {
		
				//ログインを記憶する場合、クッキーを設定
		     	if($_POST['autologin']){
	
		     		//自動ログイン用クレデンシャル生成
					$credential = Data::createCredential($idArray[0]['id']);
				}
	    		
	        	$_SESSION['u_id'] = $group_admin_id_Array[0]['id'];
		        $_SESSION['u_company_name'] = $group_admin_id_Array[0]['company_name'];
		        
		        //エジソン版のみ、会社タイプを取得
		        $_SESSION['u_company_roll'] = $group_admin_id_Array[0]['company_roll'];
				
				$message= LOGIN_SUCCESS;
	    	  	
				// header("Location: http://edison.doutaikanri.com/smart_location_admin/index.php");
				header("Location:". TEMPLATE_URL);
				exit(0);
	    		    	
		    } 

		    //ドライバーか、サブグループマネージャーか
	    	if(empty($group_admin_id_Array[0]['id'])){

	    		$driver_id_Array=Driver::login($id,$pass);

	    		// 存在すれば、ログイン成功
				if(isSet($driver_id_Array[0]['last_name'])) {
					
					//ログインを記憶する場合、クッキーを設定
					if($_POST['autologin']){
						//自動ログイン用クレデンシャル生成
						$credential = Driver::createCredential($driver_id_Array[0]['id']);
							
					}
	
					$_SESSION['driver_id'] = $driver_id_Array[0]['id'];
					$_SESSION['driver_last_name'] = $driver_id_Array[0]['last_name'];
					$_SESSION['driver_first_name'] = $driver_id_Array[0]['first_name'];
					$_SESSION['driver_company_id'] = $driver_id_Array[0]['company_id'];
					$_SESSION['driver_company_id'] = $driver_id_Array[0]['company_id'];
					$_SESSION['is_branch_manager'] = $driver_id_Array[0]['is_branch_manager'];
	
					if ($driver_id_Array[0]['is_branch_manager']){
						
						$branch_id = Branch::getBranchIdByManagerId($driver_id_Array[0]['id']);
						$branch_info_array = Branch::getById($branch_id);
					
						$_SESSION['branch_manager_branch_name'] = $branch_info_array[0]->name; 
						header("Location: http://edison.doutaikanri.com/smart_location_admin/index.php");
						exit(0);
					}else{
						header('Location: http://edison.doutaikanri.com/smart_location_admin/index.php?action=driver_status');
						exit(0);
					}
				}
	    		
	    	}
		    
   			$faillogin = DATE_ERROR_FAILLOGIN;
	 		header("$faillogin");    
	    	
	     }
	     
 
	     
	// 例外の捕捉
	} catch (PDOException $e) {
		die($e->getMessage() . 'データベースのエラーです。管理者に連絡してください');
	}
}

if(!empty($message)){
	$smarty->assign('message', $message);
}

$smarty->assign("filename","login.html");
$smarty->display("template.html");

?>
