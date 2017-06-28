<?php
/*
 * ルートコピー　フォームを表示するだけ
 * @author Akiko Goto
 * @since 2013/2/22
 * @version 2.8
 */


//会社ID、ルートIDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if($_GET['root_id']){
	$root_id=sanitizeGet($_GET['root_id'],ENT_QUOTES);
}


try{		
		
	$data = Root::getById($root_id);
	$smarty->assign("data",$data[0]);
	
	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);

	if(!empty($branch_manager_id)){
		
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $data[0]['driver_id']);
	
		$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);
		$dataList = Driver::getDrivers($company_id, $branch_id);
		
	}else{
		
		$dataList = Driver::getDrivers($company_id);
		
	}
	
	
	
	//コピーされたドライバーの氏名を取得
	$copied_driver_id = $data[0]['driver_id'];

	$copied_driver_name = Driver::getNameById($copied_driver_id);
	$smarty->assign("copied_driver_name", $copied_driver_name[0]);
	
	
	//ドライバー一覧を取得
//	$dataList=Driver::getDrivers($company_id);
	

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

$smarty->assign("dataList",$dataList);
$smarty->assign("company_id",$company_id);
$smarty->assign("filename","root/copy_root.html");
$smarty->display("template.html");

?>