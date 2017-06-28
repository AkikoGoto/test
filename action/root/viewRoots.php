<?php
/*
 * ルート一覧を表示するだけ
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */


//会社IDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}else{
	//不正なアクセスです、というメッセージ					
	header("Location:index.php?action=message_mobile&situation=wrong_access");		
}

if($_GET['driver_id']){
	$driver_id=sanitizeGet($_GET['driver_id'],ENT_QUOTES);
}else{
	//不正なアクセスです、というメッセージ					
	header("Location:index.php?action=message_mobile&situation=wrong_access");		
}


	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

	if(!empty($branch_manager_id)){
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $driver_id);
		
	}
	
	
try{		

	$dataList = Root::viewRoots($company_id, $driver_id);

}catch(Exception $e){

	$message=$e->getMessage();

}

list($data, $links)=make_page_link($dataList);	

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("links",$links['all']);
$smarty->assign("company_id",$company_id);
$smarty->assign("driver_id",$driver_id);
$smarty->assign("filename","root/view_root.html");
$smarty->display("template.html");

?>