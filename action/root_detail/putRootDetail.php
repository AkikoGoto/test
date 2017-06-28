<?php
/*
 * ルート詳細情報入力　フォームを表示するだけ
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */


//エラーなどの場合、前回入力した値をデフォルトにする
$pre_data = $_SESSION['root_detail'];


//会社ID、配送先IDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if($_GET['root_detail_id']){
	$root_detail_id=sanitizeGet($_GET['root_detail_id'],ENT_QUOTES);
}

if($_GET['root_id']){
	$root_id=sanitizeGet($_GET['root_id'],ENT_QUOTES);
}



try{		

	//ルートの日付を取得
	$rootData = Root::getById( $root_id );

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	if(!empty($branch_manager_id)){
		
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $rootData[0]['driver_id']);
	
	}
	
	$root_date = date('Y年n月j日', strtotime($rootData[0]['date']));
	
	//配送先IDが指定されていれば、編集のため、元のデータを表示
	if($root_detail_id){
		$status='EDIT';
		$smarty->assign("status",$status);
		
		$data = RootDetail::getById($root_detail_id);
		//時間に分解、繰り返し
		$deliver_hour = date('H', strtotime($data[0]['deliver_time']));
		$smarty->assign('deliver_hour',$deliver_hour);
	
		//分に分解、繰り返し			
		$deliver_minit = date('i', strtotime($data[0]['deliver_time']));
		$smarty->assign('deliver_minit',$deliver_minit);
		$smarty->assign("data",$data[0]);
	}

	//フォームなどで戻った時のために、セッションにデータを格納
	if($_SESSION['root_detail_data']){
		$smarty->assign("pre_data",$_SESSION['root_detail_data']);
	}

	//時：分のセレクトメニュー用
	for($l=0; $l<24; $l++){
        $select_menu_hour[$l] = sprintf("%02d",$l);
	}
	
	for($m=0; $m<60; $m++){
        $select_menu_minit[$m] = sprintf("%02d",$m);
	}
		
	

}catch(Exception $e){

	$message=$e->getMessage();

}

//モーダルウィンドウのPrettyPopinを追加
$css = "<link rel=\"stylesheet\" href=\"".TEMPLATE_URL."/templates/css/prettyPopin.css\" type=\"text/css\" media=\"screen\">";
$css .= "<link rel=\"stylesheet\" href=\"".TEMPLATE_URL."templates/css/pc.css\"	type=\"text/css\" media=\"screen\">";

$smarty->assign("js","<script type=\"text/javascript\" src=\"".TEMPLATE_URL."templates/js/jquery.prettyPopin_mod.js\"></script>");
$smarty->assign("css",$css);


$smarty->assign("company_id",$company_id);
$smarty->assign("root_id",$root_id);
$smarty->assign("root_date", $root_date );

$smarty->assign("pre_data",$pre_data);
$smarty->assign('select_menu_hour',$select_menu_hour);
$smarty->assign('select_menu_minit',$select_menu_minit);

//新規登録の場合と、編集時で画面を分ける
if($status == 'EDIT'){
	$smarty->assign("filename","root_detail/edit_root_detail.html");
	
}else{
	$smarty->assign("filename","root_detail/put_root_detail.html");	
}
$smarty->display("template.html");

?>