<?php
/**
 * 運転情報を表示、出力するaction。まずは急ブレーキ情報のみ。
 * @author Yuji Hamada
 * @since 2017/3/16
 * @version 2.0
 */

$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
$branch_id = sanitizeGet($_GET['suddenBrakingBranchId'],ENT_QUOTES);

if($_GET['time_from_year']){
	//日付の各値を取得
	$time_from_year = sanitizeGet($_GET['time_from_year'],ENT_QUOTES);
	$time_from_month = sanitizeGet($_GET['time_from_month'],ENT_QUOTES);
	$time_from_day = sanitizeGet($_GET['time_from_day'],ENT_QUOTES);

	$time_to_year = sanitizeGet($_GET['time_to_year'],ENT_QUOTES);
	$time_to_month = sanitizeGet($_GET['time_to_month'],ENT_QUOTES);
	$time_to_day = sanitizeGet($_GET['time_to_day'],ENT_QUOTES);
	//between指定のためtime_to_dayを一日プラスする。time_to_dayが月末日より大きい場合は翌月１日にする
	
	list($time_to_year, $time_to_month, $time_to_day) = getNextDay($time_to_year, $time_to_month, $time_to_day);
	
	$selected_start = $time_from_year.sprintf("%02d", $time_from_month) . sprintf("%02d", $time_from_day);
	$selected_end = $time_to_year.sprintf("%02d", $time_to_month) . sprintf("%02d", $time_to_day);

	//CSVに出力する情報の取得
	$informations = Information::suddenBrakingInformation($selected_start, $selected_end, $company_id, $branch_id);
}
//CSV出力のデータが存在するか
if($informations){
	
	$fileName = 'IFT0160_' . date(Ymd) . '.csv';
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . $fileName);
	
	$stream = fopen('php://output', 'w');
	foreach($informations as $key => $row){
		fputs($stream, implode($row, ',')."\n");
	}
	
	fclose($stream);
	
}else{
	$company_id=sanitizeGet($_GET['company_id'],ENT_QUOTES);
	
	$testGroup = Branch::getByType($company_id, 'TEST');
	$testGroupId = $testGroup['id'];
	$patrol = Branch::getByType($company_id, 'PATROL');
	$patrolId = $patrol['id'];
	
	if($_GET['time_from_year']){
		$smarty->assign("message","該当するCSVデータがありません。");
	}
	
	$smarty->assign("branch_id", $branch_id);
	$smarty->assign("company_id", $company_id);
	$smarty->assign("testGroupId", $testGroupId);
	$smarty->assign("patrolId", $patrolId);
	$smarty->assign("filename","information/driving_information.html");
	$smarty->display("template.html");	
}