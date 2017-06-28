<?php
/** 個別ドライバーの業務日誌確認画面　iphone,PC
 * 　MAPでの表示
 * ver2.1から追加
 */

/**
 * 共通設定、セッションチェック読み込み　会社の管理者か、ドライバー本人のみ閲覧可能
 */


//ドライバーIDが来ているか
if($_GET['driver_id']){
	$driver_id=sanitizeGet($_GET['driver_id']);
}
//ドライバーIDが来ているか
if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id']);
}

//会社のユーザーIDと、編集するIDがあっているか,あるいはドライバー本人か確認
driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);

//ドライバー名の取得
$driver_name=Driver::getNameById($driver_id);

//作業ステータス
$workingStatus = Data::getStatusByCompanyId($company_id);
$smarty->assign("working_status", $workingStatus[0]);
	
//ドライバー自身の編集許可ステータス
$is_ban_editing = Data::isBannedDriverEditing($company_id, $u_id);

//作業履歴の取得
$dataList = Work::getWorkRecordsByNew($driver_id);

//作業時間の合計　たくると違うので注意
$this_month_work_time = Work::getWorktimeByDate($driver_id, "all_select", date('Y'), date('m'), 1, 
	 					date('Y'), date('m'), date('t'));

	//業務時間の合計を計算
	$selected_total_time = 0;					
	foreach($this_month_work_time as $each_data){
		$each_data_seconds = h2s($each_data['total_time']);
		$selected_total_time = $selected_total_time + $each_data_seconds;
	}
	
	//秒数を時：分：秒に変換
	$selected_total_time = s2h($selected_total_time);
	
//データベースからコンクリートの既存のデータを取り出す
$datas = concrete::concrete_attendance_date($driver_id);
$count=count($datas);

for($i=0;$i<$count;$i++){
	$concrete_attendance_id['concrete_attendance_id'][$i] = $datas[$i]['id'];
	
	$concrete_attendance_date['concrete_attendance_date'][$i] = $datas[$i]['date'];
	$concrete_attendance_year[$i] = date('Y', strtotime($datas[$i]['date']));
	$concrete_attendance_month[$i] = date('n', strtotime($datas[$i]['date']));
	$concrete_attendance_day[$i] = date('j', strtotime($datas[$i]['date']));
	$concrete_attendance_date_display[$i]['concrete_attendance_date'] = $concrete_attendance_year[$i].'年'.$concrete_attendance_month[$i].'月'.$concrete_attendance_day[$i].'日';
	$concrete_attendance_date_display[$i]['concrete_attendance_id'] = $datas[$i]['id'];
}
$i=0;
$j=0;
$k=0;


/*
foreach ($data as $key => $value ) {
	$comment = Work::getCommentByStatusAndDate($data[$key], $driver_id);
	
	$commentNum = 0; //コメントの数だけ「編集・削除」のrowspanの数を増やすため
	
	if ($comment){
		$commentNum = 1; //thの一行分
		foreach ($comment as $commentKey => $commentValue) {
			$data[$key]['comment'][$commentKey] = $comment[$commentKey]['comment'];
			$commentNum++;
		}
	}
	
	$data[$key]['rowSpanNum'] = $commentNum + 4; //rowspanの数
	
}
*/

//var_dump($concrete_attendance_date_display);

list($data, $links)=make_page_link($dataList);
$smarty->assign("links",$links['all']);
$smarty->assign("driver_name",$driver_name[0]);
$smarty->assign('dataList',$dataList);
$smarty->assign("data",$data);
$smarty->assign('is_ban_editing',$is_ban_editing);
$smarty->assign("selected_total_time",$selected_total_time);

$smarty->assign("driver_id",$driver_id);
$smarty->assign("company_id",$company_id);

//請求時間の編集の箇所
$smarty->assign("concrete_attendance_date_display",$concrete_attendance_date_display);
$smarty->assign("concrete_attendance_date",$concrete_attendance_date);
$smarty->assign("concrete_attendance_id",$concrete_attendance_id);
$smarty->assign("i",$i);
$smarty->assign("j",$j);
$smarty->assign("k",$k);

$smarty->assign("filename","concrete/worktime.html");
$smarty->display('template.html');

