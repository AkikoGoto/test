<?php

/*
 * ルート詳細表示画面　ドライバーアプリ用にJSONで出力
 * 最新の日付のルートデータを表示
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */
/*
$_POST['login_id']='ichiro';
$_POST['passwd']='ichiro';
*/

if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	if($_POST['date']){
		$date = htmlentities($_POST['date'],ENT_QUOTES, mb_internal_encoding());
	}
	
	$idArray=Driver::login($login_id,$passwd);
	
	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
			
		$driver_id = $idArray[0]['id'];
       	$_SESSION['driver_id'] = $driver_id;
        $_SESSION['driver_last_name'] = $idArray[0]['last_name'];
        $_SESSION['driver_first_name'] = $idArray[0]['first_name'];
        $_SESSION['driver_company_id'] = $idArray[0]['company_id'];

        //今日の日付を投げる場合
//        $today = date("Y-m-d");
        $company_id = $idArray[0]['company_id'];
        
		try{
			
			$datas['root_detail'] = RootDetail::viewRootDetails($company_id, $driver_id, $date);	
			
			if($datas){

				echo json_encode($datas);
					
			}else{

				print "NO_DATA";
					
			}
			
		 }catch(Exception $e){
				
			$message=$e->getMessage();
			//DBエラーの場合の画面出力
			print "DB_ERROR";

		 }
	}else{

		//ログインに失敗した場合の画面出力
		print "LOGIN_FAILED";
	}
}else{

	//IDがPOSTされていない場合
	print "INVALID_ACCESS";
}
	
?>