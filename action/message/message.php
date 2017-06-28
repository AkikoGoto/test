<?php

	/*メッセージ送信*/
	
	$id_array=array('id','company_id');
	$keys=array();
	$keys=array_merge($id_array,$message_array);
	

	$_POST['message_latitude']=$_POST['latitude'];
	$_POST['message_longitude']=$_POST['longitude'];


	//$datas[データ名]でサニタイズされたデータが入っている

	foreach($keys as $key){
		$datas[$key]=htmlentities($_POST[$key],ENT_QUOTES, mb_internal_encoding());
	}

	
	//会社のユーザーIDと、編集するIDがあっているか,営業所長か確認
	company_and_branch_manager_auth($u_id, $datas['company_id'], $branch_manager_id);
	
	if(!empty($u_id)){

		//送信が管理者というデータをデータに入れる
		$datas['sender']=ADMINISTRATOR;
		$datas['sender_id']=0;
	
	}elseif(!empty($branch_manager_id)){
			
		//送信者に営業所長の名前をデータに入れる
		$driver_name_array = Driver::getNameById($branch_manager_id);
		$datas['sender'] = $driver_name_array[0]['last_name'].$driver_name_array[0]['first_name'];		
		$datas['sender_id']=$branch_manager_id;

	}
		
	foreach($_POST['driver_id'] as $driver_id){
		$driver_ids[] = htmlentities($driver_id, ENT_QUOTES, mb_internal_encoding());	
	}
	
	
	$result = Message::SendMessage($datas, $driver_ids);


	$status = $result[0];
	$error_info = $result[1];
	
	if($status == "SUCCESS"){
		$result_info = MESSAGE_SUCCESS;
	}else{
		$result_info = MESSAGE_FAILED;		
	}

	$smarty->assign("id", $datas['company_id']);		
	$smarty->assign("result_info", $result_info);	
	$smarty->assign("error_info", $error_info);	
	$smarty->assign("filename","messages/message.html");
	$smarty->display('template.html');

?>