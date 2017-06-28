<?php
/**
 * 営業所情報閲覧
 */

try{

	//どの区別でデータを取得するか
	$by=sanitizeGet($_GET['by']);
	$city=sanitizeGet($_GET["city"]);
	$prefecture=sanitizeGet($_GET["prefecture"]);
	
	switch($by){
		
		//会社別の営業所情報閲覧
		case company:
			//会社のIDを取得して、データを取得
			$id=sanitizeGet($_GET["company_id"]);
			
			if($id){
				$dataList=Branch::getByCompanyId($id);
				$company_name=Data::getCompanyName($id);
			}
		break;
			
		//区別の営業所情報閲覧
		case ward:
			//市と区の名前を取得して、市と区の名前でデータを取得
			$ward=sanitizeGet($_GET["ward"]);			
			
			//区名での営業所情報取得
			$dataList=branch::getBranchesByWard($ward,$city,$prefecture);
		break;

		//町村別の営業所情報閲覧
		case town:
			//区の名前を取得して、区の名前でデータを取得
			$town=sanitizeGet($_GET["town"]);
		
			//町名での営業所情報取得
			$dataList=branch::getBranchesByTown($town,$city,$prefecture);
		break;		
		
	}
		
	 }catch(Exception $e){
	
		$message=$e->getMessage();
	}

list($data, $links)=make_page_link($dataList);

$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign('company_id',$id);

$smarty->assign('company_name',$company_name['company_name']);
$smarty->assign('city',$city);
$smarty->assign('ward',$ward);
$smarty->assign('town',$town);

if($company_name){
	$page_title=$company_name['company_name'];
	}elseif($city||$ward||$town){
		
		if($city){
			$page_title=$city;
		}
		if($ward){
			$page_title.=$ward;
		}
		if($town){
			$page_title.=$town;
		}
		$page_title.=COMMON_OF_TAXI;
		
	}
	
$smarty->assign("links",$links['all']);	
$smarty->assign("page_title",$page_title);
$smarty->assign("meta_description",$page_title.'。');
$smarty->assign("filename","viewBranches.html");
$smarty->display("template.html");

?>