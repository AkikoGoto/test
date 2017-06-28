<?php
//ターゲット用のデータ投入・編集画面を表示するだけ

//エラーなどの場合、前回入力した値をデフォルトにする
$session=$_SESSION;
		
	//ドライバーIDが来ているか
	if($_GET['company_id']){
		$company_id = sanitizeGet($_GET["company_id"]);
	}
	
	//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
	driver_company_auth($driver_id, $session_driver_id, $company_id, $u_id);

	if($_GET['id']){	
	
		$id=sanitizeGet($_GET['id']);		
		$smarty->assign("id", $id);
	}

	try{
	
		//会社のドライバー一覧
		$drivers = Driver::getDrivers($company_id);		

		//新規データ投入ではなく、データ編集で、セッションに入っている会社IDとIDが同じ場合　データの取得	
	
		if($id){
			
			$data = Target::getTargetbyId($id);				

			
			$data_year = date('Y', strtotime($data[0]['created']));				
			$data_month = date('n', strtotime($data[0]['created']));				
			$data_day = date('d', strtotime($data[0]['created']));				
		
			$smarty->assign("data_year",$data_year);
			$smarty->assign("data_month",$data_month);
			$smarty->assign("data_day",$data_day);

			//引き取り日がすでにある場合
		
			if($data[0]['picked_date'] == "0000-00-00" || $data[0]['picked_date'] == NULL ){
			}else{

				$picked_date_year = date('Y', strtotime($data[0]['picked_date']));				
				$picked_date_month = date('n', strtotime($data[0]['picked_date']));				
				$picked_date_day = date('d', strtotime($data[0]['picked_date']));				
			
				$smarty->assign("picked_date_year",$picked_date_year);
				$smarty->assign("picked_date_month",$picked_date_month);
				$smarty->assign("picked_date_day",$picked_date_day);
			}
			
		}
		
	}catch(Exception $e){
		
		$message=$e->getMessage();

	}

//エラーで戻ってきているときは、エラー前の情報表示
$smarty->assign('session',$session);
$smarty->assign("data",$data[0]);

$smarty->assign("company_id",$company_id);
$smarty->assign("drivers",$drivers);

$smarty->assign("filename","target/target_edit.html");
$smarty->display("template.html");

?>