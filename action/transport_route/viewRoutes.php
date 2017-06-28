<?php
/**
 * 輸送ルート一覧表示
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

$transportRoutes = TransportRoute::selectCompanyRoutes($company_id);

list($transportRoutes, $links)=make_page_link($transportRoutes, 20);

$smarty->assign("links",$links['all']);
$smarty->assign("transportRoutes",$transportRoutes);
$smarty->assign("id",$company_id);
$smarty->assign("filename","transport_route/view_routes.html");
$smarty->display("template.html");