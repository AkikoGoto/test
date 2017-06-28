<?php
//ドライバー情報入力
//エラーなどの場合、前回入力した値をデフォルトにする
$session=$_SESSION;

//会社ID、ドライバーIDが来ているか
if($_GET['driver_id']){	
	$driver_id=sanitizeGet($_GET['driver_id'],ENT_QUOTES, 'Shift_JIS');
	}

if($_GET['company_id']){
	$company_id=sanitizeGet($_GET['company_id']);
}

if ($_GET['id']){
	$id=sanitizeGet($_GET['id'],ENT_QUOTES, 'Shift_JIS');
}

//作業ステータス
$workingStatus = Data::getStatusByCompanyId($company_id);
$smarty->assign("working_status", $workingStatus[0]);

	if(!empty($branch_manager_id)){
			
		//営業所のマネージャーがその会社の情報にアクセスしてよいか
		branch_manager_auth($branch_manager_id, $company_id);
	
		//このドライバーが許可された営業所のドライバー情報か
		branch_manager_driver_auth($branch_manager_id, $driver_id);
	
	}else{
	
		//会社のユーザーIDと、編集するIDがあっているか確認
		driver_company_auth($driver_id,$session_driver_id,$company_id,$u_id);
		
		//ドライバー本人がログインしている場合、会社が編集許可を子なっていなければエラー表示
		driver_editing_banned_check($driver_id,$session_driver_id,$company_id,$u_id);
	}

	try{
		//編集画面の場合、現在のデータを表示
		if($id){
			
			//作業履歴の取得
			$dataList = Work::EditWorkRecordsById($id);
			//編集かどうかのフラグ
			$smarty->assign("edit",1);

			//DBの開始,終了時間を年に分解、繰り返し
			if($dataList[0]['start']){
				
				//年月日、時刻に分解
				$start_year = date('Y', strtotime($dataList[0]['start']));
				$start_month = date('n', strtotime($dataList[0]['start']));
				$start_day = date('j', strtotime($dataList[0]['start']));
				$start_hour = date('G', strtotime($dataList[0]['start']));
				$start_minit = date('i', strtotime($dataList[0]['start']));
				$start_second = date('s', strtotime($dataList[0]['start']));
				
				$smarty->assign('start_year',$start_year);
				$smarty->assign('start_month',$start_month);
				$smarty->assign('start_day',$start_day);
				$smarty->assign('start_hour',$start_hour);
				$smarty->assign('start_minit',$start_minit);
				$smarty->assign('start_second',$start_second);
			}
			
			
			if($dataList[0]['end']){
				if ($dataList[0]['end'] != '0000-00-00 00:00:00') {
					$end_year = date('Y', strtotime($dataList[0]['end']));
					$end_month = date('n', strtotime($dataList[0]['end']));				
					$end_day = date('j', strtotime($dataList[0]['end']));
					$end_hour = date('G', strtotime($dataList[0]['end']));	
					$end_minit = date('i', strtotime($dataList[0]['end']));
					$end_second = date('s', strtotime($dataList[0]['end']));
					
					$smarty->assign('end_year',$end_year);
					$smarty->assign('end_month',$end_month);
					$smarty->assign('end_day',$end_day);
					$smarty->assign('end_hour',$end_hour);
					$smarty->assign('end_minit',$end_minit);
					$smarty->assign('end_second',$end_second);
				} else {
					$smarty->assign('end_year',$start_year);
					$smarty->assign('end_month',$start_month);
					$smarty->assign('end_day',$start_day);
					$smarty->assign('end_hour',$start_hour);
					$smarty->assign('end_minit',$start_minit);
					$smarty->assign('end_second',$start_second);
				}
			}
			
		}
		
	}catch(Exception $e){
		
			$message=$e->getMessage();

	}


$this_year = date('Y');
$this_month = date('n');
$today = date('j');

for($i=5; $i>=0; $i--){
	        $select_menu_year[$i] = $this_year-$i;
		}
for($j=1; $j<13; $j++){
	        $select_menu_month[$j] = $j;
		}
		
for($k=1; $k<32; $k++){
	        $select_menu_day[$k] = $k;
		}
		
for($l=1; $l<25; $l++){
	        $select_menu_hour[$l] = $l;
		}

for($m=0; $m<60; $m++){
	        $select_menu_minit[$m] = $m;
		}

for($s=0; $s<60; $s++){
	        $select_menu_second[$s] = $s;
		}		
		

//セッションのid判定
try{
	if(!empty($_SESSION['worktime_id'])){
		if($dataList[0]['id'] == $_SESSION['worktime_id']){
			//セッション
			$smarty->assign('worktime_status',$_SESSION['worktime_status']);
			//セッション（日付、開始）
			$smarty->assign('worktime_from_year',$_SESSION['worktime_from_year']);
			$smarty->assign('worktime_from_month',$_SESSION['worktime_from_month']);
			$smarty->assign('worktime_from_day',$_SESSION['worktime_from_day']);
			$smarty->assign('worktime_from_hour',$_SESSION['worktime_from_hour']);
			$smarty->assign('worktime_from_minit',$_SESSION['worktime_from_minit']);
			$smarty->assign('worktime_from_second',$_SESSION['worktime_from_second']);
			//セッション（日付：終了）
			$smarty->assign('worktime_to_year',$_SESSION['worktime_to_year']);
			$smarty->assign('worktime_to_month',$_SESSION['worktime_to_month']);
			$smarty->assign('worktime_to_day',$_SESSION['worktime_to_day']);
			$smarty->assign('worktime_to_hour',$_SESSION['worktime_to_hour']);
			$smarty->assign('worktime_to_minit',$_SESSION['worktime_to_minit']);
			$smarty->assign('worktime_to_second',$_SESSION['worktime_to_second']);
						
			//エラーで戻ってきているときは、エラー前の情報表示
			$smarty->assign('session',$session);
		}
	}
}catch(Exception $e){
		
			$message=$e->getMessage();

	}
				
//DBからのデータ
$smarty->assign('data',$dataList[0]);


/*現在の時間*/
$smarty->assign('this_year',$this_year);
$smarty->assign('this_month',$this_month);
$smarty->assign('today',$today);


//foreachで繰り返すセレクトメニュー用の日付
$smarty->assign('select_menu_year',$select_menu_year);
$smarty->assign('select_menu_month',$select_menu_month);
$smarty->assign('select_menu_day',$select_menu_day);
$smarty->assign('select_menu_hour',$select_menu_hour);
$smarty->assign('select_menu_minit',$select_menu_minit);
$smarty->assign('select_menu_second',$select_menu_second);

$smarty->assign("driver_id",$driver_id);
$smarty->assign("id",$id);
$smarty->assign("company_id",$company_id);
$smarty->assign("filename","worktime_edit.html");
$smarty->display("template.html");

?>