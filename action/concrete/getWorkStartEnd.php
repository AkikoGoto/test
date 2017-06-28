<?php
//仮で昼食・連絡待ち・洗車待ち・洗車・コピー・その他の開始と終了を取得する

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

//作業履歴の取得
$status = 6;
$driver_id = 1130;
$date = "2014-06-17";

$dataList = getWorkRecordsByStatus($status, $driver_id, $date);

var_dump($dataList);

if($dataList){
	//スタートの時間
	$start_data = $dataList[0]['start'];
	$strToTimeStart = strtotime($start_data);
	$start = date(Hi, $strToTimeStart);
	echo $start;

	//終了の時間
	$end_data = $dataList[0]['end'];
	$strToTimeEnd = strtotime($end_data);
	$end = date(Hi, $strToTimeEnd);
	echo $end;
}else{
	echo "該当するデータがありません";
}

//クラスに移した方がいいですが、とりあえずここ
//ステータスごとの作業履歴

function getWorkRecordsByStatus($status, $driver_id, $date){

	try{
		$dbh=SingletonPDO::connect();

		$sql="
		SELECT 
			*
		FROM 
			work
		WHERE 
			work.status = $status
		AND
			work.driver_id = $driver_id
		AND
			work.start > \"$date\"
		AND
			work.end < \"$date\" + INTERVAL 1 DAY

		";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();

		$data=$stmt->fetchAll();
print $sql;
		return $data;

	}catch(Exception $e){

		echo $e->getMessage();

	}
}




?>