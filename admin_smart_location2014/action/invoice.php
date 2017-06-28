<?php
/**
 * 管理画面　TOP画面および仮登録情報アクティベート画面
 */

require_once('admin_check_session.php');

try{

//データ一覧

	//仮登録情報を表示するのか、本番を表示するのかのフラグ
	//$from_web=$_GET['from_web'];	
	
	$invoices_data=Data::getInvoices();
	
}catch(Exception $e){
	
		$message=$e->getMessage();
}



list($data, $links)=make_page_link($invoices_data);

$smarty->assign('from_web',$from_web);
$smarty->assign('dataList',$dataList);
$smarty->assign('newtopicList',$newtopicList);
$smarty->assign("links",$links['all']);
$smarty->assign("data",$data);
$smarty->assign("driver_number",$driver_number);
$smarty->assign("invoice_id",$invoices_data[0]['id']);

//新着レスがあるかどうかの表示時間
$smarty->assign('is_new_time',$is_new_time);
$smarty->assign("filename","invoice.html");
$smarty->display('template.html');

?>