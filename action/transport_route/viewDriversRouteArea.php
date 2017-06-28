<?php
/**
 * ドライバールート一覧表示
 * @author Yuji Hamada
 * @since 2017/05/08
 * @version 2.0
 *
 */

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id']);
}
if(!empty($_GET['branch_id'])){
	$branch_id=sanitizeGet($_GET['branch_id'],ENT_QUOTES);
}else{
	$branch_id = null;
}

//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
if(!empty($branch_manager_id)){
	//ドライバー指定ありの場合は、このドライバーが許可された営業所のドライバー情報か
	if($driver_id){
		branch_manager_driver_auth($branch_manager_id, $driver_id);
	}
}

if($_GET['date']){
	$theDate=sanitizeGet($_GET['date']);
}else{
	$theDate = date('Ymd');
}

$tomorrow = date('Ymd', strtotime($theDate . ' +1 day'));
$yesterday = date('Ymd', strtotime($theDate . ' -1 day'));

$theDateRoutes = Driver::selectDriverRoute($company_id, $theDate);
$tomorrowRoutes = Driver::selectDriverRoute($company_id, $tomorrow);
$yesterdayRoutes = Driver::selectDriverRoute($company_id, $yesterday);

$driversRoutes[$yesterday] = $yesterdayRoutes;
$driversRoutes[$theDate] = $theDateRoutes;
$driversRoutes[$tomorrow] = $tomorrowRoutes;

$smarty->assign("theDate",$theDate);
$smarty->assign("tomorrow",$tomorrow);
$smarty->assign("yesterday",$yesterday);
$smarty->assign("company_id",$company_id);
$smarty->assign("driversRoutes",$driversRoutes);
$smarty->assign("filename","transport_route/view_drivers_route_area.html");
$smarty->display("template.html");