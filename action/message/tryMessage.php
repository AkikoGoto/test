<?php
//メッセージ送信確認画面
$id_array=array('id','company_id');
$keys=array();
$keys=array_merge($id_array,$message_array);

	//会社IDが来ているか
	if($_GET['company_id']){
		$company_id=sanitizeGet($_GET['company_id']);
	}else{
		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");		
	}

	//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}
	
	
	//driversのみ配列
	if(!empty($_POST['driver_id'])){ 
		foreach($_POST['driver_id'] as $driver){
					
		    $datas['driver_id'][]=htmlentities($driver, ENT_QUOTES, mb_internal_encoding());	
		}
	}

	//会社のユーザーIDと、編集するIDがあっているか,営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	
	$ip=getenv("REMOTE_ADDR");

	$ban_ip=Data::banip();
	
	//禁止リストにあるIPとの照合
	if($ip==$ban_ip){

	//メッセージ画面を表示する
      header('Location:index.php?action=message&situation=ban_ip');

	}else{

		try{
		
			//入力データ検証
			$form_validate = new Validate();
			
			$errors = $form_validate->validate_message($datas);
		
			if($errors){
		
				$form_validate->show_form($errors);
	//			$form_validate->lasturl='index.php?action=putBranch';
				
			}else{
					
				foreach($datas as $key => $value){					
					$smarty->assign("$key",$value);
				}
				
				$i = 0;
				foreach($datas['driver_id'] as $each_driver_id){
					$driver_name = Driver::getNameById($each_driver_id);
					$driver_name_each = $driver_name[0];
					$driver_name_full = $driver_name_each['last_name'].' '.$driver_name_each['first_name'];
					$drivers[$i]['name'] = $driver_name_full;
					$drivers[$i]['id'] = $each_driver_id;
					
					$i++;
				}
			
				$smarty->assign("company_id",$datas['company_id']);				
				$smarty->assign("drivers",$drivers);
				$smarty->assign("filename","messages/tryMessage.html");
				$smarty->display("template.html");
			
			}
		
		}catch(Exception $e){
			
		die($e->getMessage());
		
		}
	}

?>