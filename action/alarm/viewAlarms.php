<?php
/*
 * 配送先一覧を表示するだけ
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 */
//ini_set( 'display_errors', 1 );

//会社IDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}

if(isset($_GET['query'])) {
	$query = $_GET['query'];
}


	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	if(!empty($branch_manager_id)){
			
		$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);	
		
	}else{
		
		$branch_id = null;
	}

//user_auth($u_id,$company_id);

	try{
	
		$dataList = Alarms::searchAlarms($company_id, $query, $branch_id);
		$i=0;
		$j=1;
		foreach($dataList as $dataLists){
			
			$date = substr($dataLists['mail_time'], 0 , -5);
			$setting_time = substr($dataLists['mail_time'], -5 , strlen($dataLists['mail_time']));
			
			//いてほしい時間
			$dataList[$i]['should_time'] = $date.$setting_time;
			$dataList[$i]['number'] = $j;
			$i++;
			$j++;
			
		}

	}catch(Exception $e){
	
		$message=$e->getMessage();
	
	}

list($data, $links)=alarms_page_link($dataList);	

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign("links",$links['all']);
$smarty->assign("company_id",$company_id);
$smarty->assign("query",$query);
$smarty->assign("filename","alarm/view_alarms.html");
$smarty->display("template.html");

?>