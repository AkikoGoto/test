<?php
/**
 * 日付選択ドライバールート一覧
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

$date=sanitizeGet($_GET['date']);

$form_validate=new Validate();
$errors=$form_validate->validate_date_string($date);

if($errors){
	$form_validate->show_form($errors);
	$form_validate->lasturl='index.php?action=transport_route/setRouteToDriver';
}else{
	
	$nextDay = date('Ymd', strtotime($date . ' +1 day'));
	$theDayBefore = date('Ymd', strtotime($date . ' -1 day'));
	
	$driverRoutes = Driver::selectDriverRoute($company_id, $date);
	
	$noRouteDrivers = array();
	foreach($driverRoutes as $route){
		if(empty($route['transport_route_drivers_id'])){
			$noRouteDrivers[] = $route['last_name'] . $route['first_name'];
		}
	}
	
	$destinations = Destination::getByCompanyIdDestinationsKeyId($company_id);

	$smarty->assign("date",$date);
	$smarty->assign("nextDay",$nextDay);
	$smarty->assign("theDayBefore",$theDayBefore);
	$smarty->assign("company_id",$company_id);
	$smarty->assign("driverRoutes",$driverRoutes);
	$smarty->assign("noRouteDrivers",$noRouteDrivers);
	$smarty->assign("destinations",$destinations);
	$smarty->assign("filename","transport_route/view_day_drivers_route_area.html");
	$smarty->display("template.html");
}