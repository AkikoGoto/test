<?php
/*
 * 配送先詳細一覧を表示
 * 指定されたドライバーの、指定された日付のデータを取得
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */


//会社ID, ドライバーID、日付が来ているか

$keys=array('company_id', 'driver_id', 'date');

foreach($keys as $key){
	if($_GET[$key]){
		$datas[$key] = sanitizeGet($_GET[$key],ENT_QUOTES);
	}else{
		//不正なアクセスです、というメッセージ					
		header("Location:index.php?action=message_mobile&situation=wrong_access");	
	}
}

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $datas['company_id'], $branch_manager_id);
	
	if(!empty($branch_manager_id)){
			
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $datas['driver_id']);
	
	}


try{		

	$rootData = Root::getByDate( $datas['driver_id'], $datas['date']);
	$dataList = RootDetail::viewRootDetails($datas['company_id'], $datas['driver_id'],
		$datas['date']);
		

	//ドライバー氏名を取得
	$driver_name_array = Driver::getNameById($datas['driver_id']);
	
	$driver_last_name= $driver_name_array[0]['last_name'];
	$driver_first_name= $driver_name_array[0]['first_name'];
	
}catch(Exception $e){

	$message=$e->getMessage();

}

list($data, $links)=make_page_link($dataList);
$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("date",$datas['date']);
$smarty->assign("driver_last_name",$driver_last_name);
$smarty->assign("driver_first_name",$driver_first_name);
$smarty->assign("root_id",$rootData['id']);


$smarty->assign("links",$links['all']);
$smarty->assign("company_id",$datas['company_id']);
$smarty->assign("driver_id",$datas['driver_id']);
$smarty->assign("filename","root_detail/view_root_detail.html");
$smarty->display("template.html");

?>