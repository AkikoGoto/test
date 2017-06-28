<?php
//営業所情報入力・編集画面の出力

//エラーなどの場合、前回入力した値をデフォルトにする
$defaults=$_SESSION;

//営業所IDが来ているか
if($_GET['geographic_id']){	
	$geographic_id=sanitizeGet($_GET['geographic_id']);
	}
	
if($_GET['company_id']){	
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
	}

	//会社のユーザーIDと、編集するIDがあっているか確認
	user_auth($u_id,$company_id);	
	
	try{

		if($company_id){
			$from_web=0;
			$companyData=Data::getById($company_id, $from_web);
			$smarty->assign("company_id",$company_id);
		}
		
		//編集画面の場合、現在のデータを表示
		if($geographic_id){
			//現在のデータの取得
			$dataList=Branch::getById($geographic_id);
			//編集かどうかのフラグ
			$smarty->assign("edit",1);
		}
		
	//県名、サービス一覧の取得
	//	$companyList=Data::getCompanies();
		$prefecturesList=Data::getPrefectures();
		
	}catch(Exception $e){
		
			$message=$e->getMessage();
			}


$smarty->assign("id",$id);

$smarty->assign('companyData',$companyData[0]);
$smarty->assign('data',$dataList[0]);
$smarty->assign("companyList",$companyList);
$smarty->assign("prefecturesList",$prefecturesList);

//エラー時データの保存のため
if($defaults){
$smarty->assign('defaults',$defaults);
}

$smarty->assign("filename","putBranch.html");
$smarty->display("template.html");

?>