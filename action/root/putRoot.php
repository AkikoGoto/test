<?php
/*
 * ルート情報入力　フォームを表示するだけ
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */


//会社ID、ルートIDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if($_GET['root_id']){
	$root_id=sanitizeGet($_GET['root_id'],ENT_QUOTES);
}

if($_GET['driver_id']){
	$driver_id=sanitizeGet($_GET['driver_id'],ENT_QUOTES);
}

	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	if(!empty($branch_manager_id)){
			
		//ドライバー指定ありの場合は、このドライバーが許可された営業所のドライバー情報か
		if($driver_id){
			branch_manager_driver_auth($branch_manager_id, $driver_id);
		}
	
	}

try{		

	//配送先IDが指定されていれば、編集のため、元のデータを表示
	if($root_id){
		$status='EDIT';
		$smarty->assign("status",$status);
		
		$data = Root::getById($root_id);
		$smarty->assign("data",$data[0]);
		

		//DBの開始,終了時間を年に分解、繰り返し
		if($data[0]['date']){
				
			//年に分解
			$db_year = date('Y', strtotime($data[0]['date']));
			$smarty->assign('db_year',$db_year);				
		
			//月に分解
			$db_month = date('n', strtotime($data[0]['date']));		
			$smarty->assign('db_month',$db_month);
			
			//日に分解			
			$db_day = date('j', strtotime($data[0]['date']));
			$smarty->assign('db_day',$db_day);
			
		}
	}

	//フォームなどで戻った時のために、セッションにデータを格納
	if($_SESSION['root_data']){
		$smarty->assign("pre_data",$_SESSION['root_data']);
	}

	//ドライバー一覧を取得
	if($is_branch_manager && $branch_manager_id){
		
		$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);
		$dataList = Driver::getDrivers($company_id, $branch_id);
		
	}elseif(!empty($u_id)){

		$dataList = Driver::getDrivers($company_id);
	
	}
	

}catch(Exception $e){

	$message=$e->getMessage();

}

//セレクトメニューで表示させるための年
$this_year=date('Y');
for($i=$this_year-5; $i<$this_year+5; $i++){
       $select_menu_year[$i] = $i;
		}

//セレクトメニューで表示させるための月		
for($j=1; $j<13; $j++){
        $select_menu_month[$j] = $j;
		}		
		
//セレクトメニューで表示させるための日
 for($k=1; $k<32; $k++){
	        $select_menu_day[$k] = $k;
		}

//foreachで繰り返すセレクトメニュー用の日付
$smarty->assign('select_menu_year',$select_menu_year);
$smarty->assign('select_menu_month',$select_menu_month);
$smarty->assign('select_menu_day',$select_menu_day);		

//今日の日付（年月日）をSmartyにアサイン
$this_year=date('Y');
$this_month=date('n');
$today=date('d');
    
$smarty->assign('this_year',$this_year);
$smarty->assign('this_month',$this_month);
$smarty->assign('today',$today);
	
$smarty->assign("driver_id",$driver_id);
$smarty->assign("dataList",$dataList);
$smarty->assign("company_id",$company_id);
$smarty->assign("filename","root/put_root.html");
$smarty->display("template.html");

?>