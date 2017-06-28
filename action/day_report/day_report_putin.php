<?php
//日報用のデータ投入画面を表示するだけ

//エラーなどの場合、前回入力した値をデフォルトにする
$session=$_SESSION;
	
	//ドライバーIDが来ているか
	if($_GET['driver_id']){
		$driver_id = sanitizeGet($_GET["driver_id"]);
	}
	//ドライバーIDが来ているか
	if($_GET['company_id']){
		$company_id = sanitizeGet($_GET["company_id"]);
	}
	
	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $company_id);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $driver_id);
	
	}else{
	
		//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
		driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);
		
		//ドライバー本人がログインしている場合、会社が編集許可を子なっていなければエラー表示
		driver_editing_banned_check($driver_id,$session_driver_id,$company_id,$u_id);
		
	}

	if($_GET['id']){	
	
		$id=sanitizeGet($_GET['id']);		
		$smarty->assign("id", $id);
	}

try{

	//新規データ投入ではなく、データ編集で、セッションに入っている会社IDとIDが同じ場合　データの取得	

	if($id){
		$data=DayReport::getById($id);		
		$data_year = date('Y', strtotime($data[0]->drive_date));				
		$data_month = date('n', strtotime($data[0]->drive_date));				
		$data_day = date('d', strtotime($data[0]->drive_date));				
		
		$smarty->assign("data_year",$data_year);
		$smarty->assign("data_month",$data_month);
		$smarty->assign("data_day",$data_day);
		
	}
	
}catch(Exception $e){
	
		$message=$e->getMessage();
		}

//エラーで戻ってきているときは、エラー前の情報表示
$smarty->assign('session',$session);
$smarty->assign("data",$data[0]);

$smarty->assign("driver_id",$driver_id);
$smarty->assign("company_id",$company_id);

$smarty->assign("filename","day_report/day_report_putin.html");
$smarty->display("template.html");

?>