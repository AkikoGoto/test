<?php


if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id']);
}


$transportRoutes = TransportRoute::selectCompanyRoutes($company_id);

list($transportRoutes, $links)=make_page_link($transportRoutes, 20);

$smarty->assign("links",$links['all']);
$smarty->assign("transportRoutes",$transportRoutes);
$smarty->assign("id",$company_id);
$smarty->assign("filename","root/view_routes.html");
$smarty->display("template.html");