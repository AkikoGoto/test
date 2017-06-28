<?php

//メッセージ送信履歴表示画面　ドライバーが送受信をWebで見る用　


if($_GET['driver_id']) {
	
	$driver_id = sanitizeGet($_GET['driver_id']);
			
		try{
			
			$dataList = Message::getMessagesDriver($driver_id);		
			
			foreach($dataList as $key => $each_data){
				
				$has_read_array = array($each_data['has_read_array']);
			
				//ひとつでも未読のメッセージがあるか調べる
				if(in_array("0", $has_read_array)){
					$not_has_read = 1;
				}else{
					$not_has_read = 0;
				}
				$dataList[$key]['not_has_read']=$not_has_read;
			}
			
		 }catch(Exception $e){
				
			$message=$e->getMessage();

		 }
	}else{
		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
	}

	//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
	driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);


list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("id",$id);
$smarty->assign("links",$links['all']);

//ドライバー用メッセージ表示画面の場合は、リンク先を変更する
$smarty->assign("by_driver", 1);
$smarty->assign("driver_id", $driver_id);

$smarty->assign("filename","messages/message_history_driver.html");
$smarty->display("template_sp.html");

?>