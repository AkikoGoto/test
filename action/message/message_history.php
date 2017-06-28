<?php

//メッセージ送信履歴表示画面

//会社IDが来ているか
	if($_GET['company_id']){	
		$company_id=sanitizeGet($_GET['company_id']);
	}

		

	try{
		
		//会社のユーザーIDと、編集するIDがあっているか,営業所長かあるいはドライバー本人か確認
		company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
			
		if(!empty($u_id)){
			
			$dataList = Message::getMessages($company_id);
		
		}elseif(!empty($branch_manager_id)){
				
			$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);
		
			$dataList = Message::getThreadsByReceiver($branch_manager_id);
	
		}
	
	
	
		//ドライバー氏名を取得　複数送信先がある時のため、別に取り出す
		for ($i = 0; $i < count($dataList); ++$i){
			$driver_ids = $dataList[$i]['driver_id'];
			$drivers_array = explode(",", $driver_ids);
			$driver_name_array = Array();
			
			foreach($drivers_array as $each_driver){
							
				$driver_name = Driver::getNameById($each_driver);
				$driver_name_array[] = $driver_name[0]['last_name'].' '.$driver_name[0]['first_name'];
			}
	
			$driver_name_imploded = implode(',', $driver_name_array);
			$dataList[$i]['driver_names'] = $driver_name_imploded;
		}
	
	 }catch(Exception $e){
	
		$message=$e->getMessage();
	}


list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("id",$company_id);
$smarty->assign("links",$links['all']);
$smarty->assign("filename","messages/message_history.html");
$smarty->display("template.html");

?>