<?php

//パスワード忘れの場合、パスワードをメール送信

if(isSet($_POST['u_id'])) {

    // フォームからのデータの取り込み

    $u_id = htmlSpecialChars($_POST['u_id']);


	//エラーなどの場合、前回入力した値をデフォルトにする
	
	$_SESSION['pre_u_id']=$u_id;
	
	//エラー時戻る処理のため
	$lasturl=$_SERVER['SELF'];


	//入力データ検証
	$form_validate=new Validate();
	
	$errors=$form_validate->u_validate_address($u_id);
	
	if($errors){
	
		$form_validate->show_form($errors);
	
	//入力データにエラーがあれば、登録画面を表示しない　ダブルでの表示になってしまうから
		exit();
	
		}else{
	
		//新パスワード生成
	
		try{
			
	       // データベース接続オブジェクトの取得
	 	       $dbh = SingletonPDO::connect();
	
	 	   //ユーザー情報検索
		        $sql = "SELECT * from company WHERE email= '$u_id'";
	
		        $res=$dbh->prepare($sql);
			
		        $res->execute();  
		        $idArray = $res->fetchAll(PDO::FETCH_ASSOC);
	
			    // 存在すれば、ログイン成功
		    	if(isSet($idArray[0]['id'])) {
		
			 	   	$u_pass=getRandomString();   
		       		
			 	   	// ユーザー情報編集
			        $sql = "UPDATE company SET passwd = MD5('$u_pass') WHERE email= '$u_id'";
		
					$stmt=$dbh->prepare($sql);
					$stmt->execute();
					
					$u_name=$idArray[0]['company_name'];
					
					mail_password($u_id,$u_no,$u_name,$u_pass);
			
					$passurl = DATE_ERROR_PASS;
		    
					header("$passurl");
					
		    	}else{
			
					//該当するIDがない場合メッセージ画面を表示する
					$noid = DATE_ERROR_NOID;
			        header("$noid");
			  
			        exit(0);
		
			 	}
		 // 例外処理
		}catch(Exception $e)	{
			
			echo $e->getMessage();
			
		}
	}
}

$smarty->assign("message",$message);
$smarty->assign("title",$title);

$smarty->assign("filename","password.html");
$smarty->display("template.html");

?>