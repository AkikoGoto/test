<?php
//全会社情報表示

try{
	
	//全会社情報表示の取得
	$dataList=getData::getAllCompanyData();
	
}catch(Exception $e){
	
		$message=$e->getMessage();
}

	
list($data, $links)=make_page_link($dataList);

$smarty->assign("links",$links['all']);
$smarty->assign("data",$data);

$smarty->assign("filename","all.html");
$smarty->display('template.html');

?>