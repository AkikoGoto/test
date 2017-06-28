<?php

if($_POST['company_id']){
	$company_id=sanitizeGet($_POST['company_id'],ENT_QUOTES);
}

$date = $_POST['date'];

$transportRouteDrivers = $_SESSION['transportRouteDrivers'][$date];

TransportRouteDriver::insert($transportRouteDrivers, $company_id, $date);

unset($_SESSION['pre_data']);
unset($_SESSION['transportRouteDrivers']);

header("Location:index.php?action=message_mobile&situation=after_put_transport_route_drivers&company_id=$company_id");
