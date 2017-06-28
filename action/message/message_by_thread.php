<?php

//メッセージ送信履歴スレッドごとの表示画面

	//会社IDが来ているか
	if($_GET['company_id']){	
		$company_id = sanitizeGet($_GET['company_id']);
	}

	//スレッドIDが来ているか
	if($_GET['thread_id']){	
		$thread_id=sanitizeGet($_GET['thread_id']);
	}elseif($_GET['thread_id'] =="0"){
		$thread_id = 0;
	}else{
		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
	}

	try{
		
		$dataList = Message::getMessagesByThread($company_id, $thread_id);
		
		company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
		
		if(!empty($branch_manager_id)){
	
			$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);		
					
			
			foreach($dataList as $each_message){
				if($each_message['thread_parent'] == 1){
	
					$is_receiver = Message::isReceiver($each_message['id'], $branch_manager_id);
					
					if((!$each_message['sender_id']==$branch_manager_id) && !$is_receiver){
						//不正なアクセスです、というメッセージ					
						header("Location:index.php?action=message_mobile&situation=wrong_access");
					}				
				}
			}
			
			//そのスレッドの営業所長宛のメッセージを全部既読にする
			Message::hasReadMessageByThread($branch_manager_id, $thread_id);
		
		}
	
		//メッセージごとの処理
		foreach($dataList as $key => $each_data){
	
			$each_thread_driver_ids = explode(",", $dataList[$key]['driver_id']);
			
			//メッセージ内のドライバーごとの処理
			foreach ($each_thread_driver_ids as $key_driver => $each_driver_id){
		
				 
				 $info = Driver::getById($each_driver_id, null);
				 $dataList[$key]['driver_infos'][$key_driver]= $info[0];
	
	 			 $has_read_info = Message::getHasReadMessage($each_driver_id, $each_data['id']);
				 $dataList[$key]['driver_infos'][$key_driver]->has_read = $has_read_info;
			}
			
		}
	
		
	}catch(Exception $e){
		
			$message=$e->getMessage();
	}


list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign('first_data',$dataList[0]);
$smarty->assign("data",$data);
$smarty->assign("id",$company_id);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","messages/message_by_thread.html");
$smarty->display("template.html");

?>