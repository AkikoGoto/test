<?php
//$_POST = array('json'=>'{"oil_replenishment_1":{"login_id":"oc","passwd":"akagi999","start_time":"2014-7-7 17:23:55","oil_replenishment":"6","oil_replenishment_type":"1","concrete_attendance_id":"96","end_time":"2014-7-7 17:45:32"},"oil_replenishment_2":{"login_id":"oc","passwd":"akagi999","start_time":"2014-7-7 17:23:55","oil_replenishment":"7","oil_replenishment_type":"2","concrete_attendance_id":"96","end_time":"2014-7-7 17:45:32"},"engine_oil":{"login_id":"oc","passwd":"akagi999","start_time":"2014-7-7 17:23:55","oil_replenishment":"8","oil_replenishment_type":"4","concrete_attendance_id":"96","end_time":"2014-7-7 17:45:32"}}' );
$objParam = json_decode($_POST["json"]);

if( !empty($objParam) ) {
	
	$driver_id;
	$datas = array();
	$i = 0;
	foreach ($objParam as $oil) {
		
		if (empty($driver_id)) {
			$login_id = htmlentities($oil->login_id,ENT_QUOTES, mb_internal_encoding());
			$passwd = htmlentities($oil->passwd,ENT_QUOTES, mb_internal_encoding());
			$idArray=Driver::login($login_id,$passwd);
			$driver_id = $idArray[0]['id'];
		}
		
		foreach($start_oil_replenishments as $number => $key){
			if($key=='start_time' || $key=='end_time'){
				$datas[$i][$key] = htmlentities($oil->$key,ENT_QUOTES, mb_internal_encoding());
			}else{
				$value[$key]=trim( mb_convert_kana($oil->$key, "s"));
				$datas[$i][$key] = htmlentities($value[$key],ENT_QUOTES, mb_internal_encoding());
			}
		}
		$i++;
		
	}
		
	// 存在すれば、ログイン成功
	if(isSet($driver_id)) {
		
		$count=count($datas);
		
		$ip=getenv("REMOTE_ADDR");
		
		$ban_ip=Data::banip();
		
		//禁止リストにあるIPとの照合
		if($ip==$ban_ip){
				
			//メッセージ画面を表示する
			header('Location:index.php?action=message&situation=ban_ip');
		
		}else{
		
			try{
			
				//データチェック
				$form_validate = new Concrete_Validate();
				
					for($i=0;$i<$count;$i++){
						$errors = $form_validate->validate_form_oil_replenishment($datas,$i);	
					}
				
				if($errors){
					
					//データが不足していたらエラー出力
					echo 'INVALID_ACCESS';
					//$form_validate->show_form($errors);
									
				}else{
					for($i=0;$i<$count;$i++){
						//データベースへ投入
						concrete::insert_oil_replenishment($datas,$i);
					}
						echo 'SUCCESS';

				}
			
			}catch(Exception $e){
			
				$message=$e->getMessage();
			}
		}
	}else{
	
		//ログインに失敗した場合の画面出力
		print "LOGIN_FAILED";
	}
	
}else{
	
	//IDがPOSTされていない場合
	print "NOT_POST_ID";
}
	?>