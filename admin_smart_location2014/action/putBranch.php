<?php
//営業所情報入力・編集画面の出力
require_once('admin_check_session.php');

//エラーなどの場合、前回入力した値をデフォルトにする
$session=$_SESSION;

if($_GET['id']){	
	$id=$_GET['id'];
	}
	
if($_GET['company_id']){	
	$company_selected_id=$_GET['company_id'];
	}

	try{
	
			//編集画面の場合、現在のデータを表示
			if($id){
				//現在のデータの取得
				$dataList=Branch::getById($id);
				//編集かどうかのフラグ
				$smarty->assign("edit",1);
			}

			//営業所追加の場合、会社名を表示
			if($company_selected_id){
				$companyName=Data::getCompanyName($company_selected_id);
			}
			
		//県名、サービス一覧の取得
		$prefecturesList=Data::getPrefectures();
		
	}catch(Exception $e){
		
			$message=$e->getMessage();
	}


$smarty->assign("id",$id);
$smarty->assign('data',$dataList[0]);
//$smarty->assign("companyList",$companyList);
$smarty->assign("companyName",$companyName['company_name']);
$smarty->assign("prefecturesList",$prefecturesList);
$smarty->assign("company_selected_id",$company_selected_id);


	if($dataList){
		//編集画面、仮データ登録時のデータ表示
		$smarty->assign('data',$dataList[0]);
		
	}else{
		//エラーで戻ってきているときは、エラー前の情報表示
		$smarty->assign('session',$session);
		
	
	}

$smarty->assign("filename","putBranch.html");
$smarty->display("template.html");

?>