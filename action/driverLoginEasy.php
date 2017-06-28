<?php

/**
 * ドライバー　ログイン 簡単ログイン
 */
if($session_driver_id){

	$message= "すでにログインしています";

}else{
	try {

		//個別識別番号があるかどうか

		$mobile_id=unit_no($carrier);

		if($mobile_id) {
	
			//識別番号が送られていれば、データベースを検索
		
		   	$sql = "SELECT * FROM drivers WHERE mobile_id = '$mobile_id'";
			
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			$res=$dbh->prepare($sql);
			$res->execute();
		
		  	$idArray = $res->fetchAll(PDO::FETCH_ASSOC);
		
		    // 存在すれば、ログイン成功
		   	if(isSet($idArray[0]['last_name'])) {
				
		   		$_SESSION['driver_id'] = $idArray[0]['id'];
		        $_SESSION['driver_last_name'] = $idArray[0]['last_name'];
		        $_SESSION['driver_first_name'] = $idArray[0]['first_name'];
		        $_SESSION['driver_company_id'] = $idArray[0]['company_id'];
		   				        
		        
		      if($carrier=='docomo'){  
	    	    header('Location:index.php?action=driver_status'.'&'.$sid);
		      }else{
	    	    header('Location:index.php?action=driver_status');
		      }

		      // 存在しなければ、ログイン不成功
	    	} else {
	
	   			header('Location:index.php?action=message_mobile&situation=no_unit_no');
	
		    }
		}else{
	
	   		header('Location:index.php?action=message_mobile&situation=no_unit_no');	
		
		}
	// 例外の捕捉
	} catch (PDOException $e) {
		die($e->getMessage() . 'データベースのエラーです。管理者に連絡してください');
	}
}

$smarty->assign('message', $message);

$smarty->assign("filename","driverLogin.html");
$smarty->display("template.html");

?>