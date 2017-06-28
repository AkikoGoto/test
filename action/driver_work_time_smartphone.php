<?php
/** ドライバーのスマホからのターゲット情報参照
 * JSONでターゲット情報を出力
 * ver2.4から追加
 * 画面なし
 */

//ドライバーのログインチェック
//ログインIDとパスワードでチェック
//テストデータ
/*
$_POST['login_id']='kiryuuin';
$_POST['passwd']='hiroto';

$_POST['latitude']=35.455892;
$_POST['longitude']=139.592055;
*/
if($_POST['login_id']) {

	$login_id = htmlentities($_POST['login_id'],ENT_QUOTES, mb_internal_encoding());
	$passwd = htmlentities($_POST['passwd'],ENT_QUOTES, mb_internal_encoding());
	
	$idArray=Driver::login($login_id,$passwd);

	// 存在すれば、ログイン成功
	if(isSet($idArray[0]['last_name'])) {
			
		$driver_id = $idArray[0]['id'];

		try{
			
			$statuses = array('only_work',1,2,3,4,5);
			
			//それぞれのステータスの業務時間を合計
			foreach($statuses as $key => $each_status){
			
				$this_month_work_time = Work::getWorktimeByDate($driver_id, $each_status, date('Y'), date('m'), 1, 
		 					date('Y'), date('m'), date('t'));
				//業務時間の合計を計算
				$selected_total_time = 0;					
				foreach($this_month_work_time as $each_data){
					
					$each_data_seconds = h2s($each_data['total_time']);
					$selected_total_time = $selected_total_time + $each_data_seconds;
				}
				
				//ステータス名を取得
				$company_id = $idArray[0]['company_id'];
				$workStatusDatas = Data::getStatusByCompanyId($company_id);
				$work_statues = $workStatusDatas[0];
				$status_1 = $work_statues->action_1;
				$status_2 = $work_statues->action_2;
				$status_3 = $work_statues->action_3;
				$status_4 = $work_statues->action_4;

				//ステータスコードを名称に変更
				switch($each_status){
					case "only_work":
						$status_name = TOTAL_TIME_SUM;
						break;
					case 1:
						$status_name = $status_1;
						break;
					case 2:
						$status_name = $status_2;
						break;
					case 3:
						$status_name = $status_3;
						break;
					case 4:
						$status_name = $status_4;
						break;
					case 5:
						$status_name = DRIVER_OTHER;
						break;

				}

				$total_time[$key]['status_name'] = $status_name;

				//秒数を時：分：秒に変換
				$total_time[$key]['each_work_time'] = s2h($selected_total_time);

			
			}

			$work_time['work_time'] = $total_time; 
						
			echo json_encode($work_time);


		}catch(Exception $e){

			$message=$e->getMessage();
		}


	}else{

		//ログインに失敗した場合の画面出力
		print "LOGIN_FAILED";
	}
}else{

	//IDがPOSTされていない場合
	print "INVALID_ACCESS";
}

?>