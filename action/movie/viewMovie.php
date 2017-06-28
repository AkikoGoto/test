<?php
/**
 * 動画を表示
 * @author Akiko Goto
 * @since 2015/4/13
 */

//会社IDが来ているか

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
}


if(isset($_GET['movie_id'])) {
	$movie_id = $_GET['movie_id'];
}else{
	//不正なアクセスです、というメッセージ					
	header("Location:index.php?action=message_mobile&situation=wrong_access");	
}


	//会社のユーザーIDと、編集するIDがあっているか、営業所長か確認
	company_and_branch_manager_auth($u_id, $company_id, $branch_manager_id);
	
	if(!empty($branch_manager_id)){
			
		$branch_id = Branch::getBranchIdByManagerId($branch_manager_id);	
		
	}else{
		
		$branch_id = null;
	}


	try{
	
		$data = Movie::viewMovie($movie_id);

	}catch(Exception $e){
	
		$message=$e->getMessage();
	
	}

$smarty->assign("data",$data[0]);
$smarty->assign("links",$links['all']);
$smarty->assign("company_id",$company_id);
$smarty->assign("filename","movie/view_movie.html");
$smarty->display("template.html");

?>