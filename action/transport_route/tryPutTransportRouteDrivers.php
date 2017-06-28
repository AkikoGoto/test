<?php

if($_POST['company_id']){
	$company_id=sanitizeGet($_POST['company_id'],ENT_QUOTES);
}

$date = $_POST['date'];

$_SESSION['transportRouteDrivers'][$date] = $_POST['driver'];

$selectedDriverIds = array_keys($_POST['driver']);
$selectedTransportRouteIds = array_values($_POST['driver']);

$drivers = Driver::selectDriversByIds($selectedDriverIds);
$transportRoutes = TransportRoute::selectCompanyRoutesFetchUnique($company_id);

$smarty->assign('date', $date);
$smarty->assign("drivers",$drivers);
$smarty->assign("selectedDriverIds",$selectedDriverIds);
$smarty->assign("transportRoutes",$transportRoutes);
$smarty->assign("company_id",$company_id);
$smarty->assign("filename","transport_route/confirm_transport_route_drivers.html");
$smarty->display("template.html");