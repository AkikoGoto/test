<?php
/**
 * ドライバールート一括設定
 * @author Yuji Hamada
 * @since 2017/05/08
 * @version 2.0
 *
 */

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
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

//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

$drivers = Driver::getDriversByCompany($company_id);

$transportRoutes = TransportRoute::selectCompanyRoutes($company_id);
if(!empty($_GET['date'])){
	$date = sanitizeGet($_GET['date'],ENT_QUOTES);
	unset($_SESSION['transportRouteDrivers']);
}else{
	$date = date('Ymd');
}

$form_validate=new Validate();
$errors=$form_validate->validate_date_string($date);

if($errors){
	$form_validate->show_form($errors);
	$form_validate->lasturl='index.php?action=transport_route/setRouteToDriver';
}else{
	$transportRoutesDrivers = TransportRouteDriver::selectByCompany($company_id, $date);
	
	$smarty->assign('date',$date);
	$smarty->assign("drivers",$drivers);
	$smarty->assign("transportRoutes",$transportRoutes);
	$smarty->assign("transportRoutesDrivers",$transportRoutesDrivers);
	$smarty->assign("company_id",$company_id);
	$smarty->assign("filename","transport_route/set_route_to_driver.html");
	$smarty->display("template.html");
}
