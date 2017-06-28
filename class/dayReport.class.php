<?php
//日報のためだけに利用するデータのクラス　Driverクラスを継承

class DayReport extends Driver{
	
	/* driver_report */
	public $id;
	public $drive_date;
	public $start_meter;
	public $arrival_meter;
	public $supplied_oil;
	
//ドライバーの作業履歴取得
public static function getDayReportDatasByNew($driver_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
		//直近100件のデータを取得
		$sql = "SELECT 
					* 
				FROM 
					day_report
				WHERE
					driver_id = $driver_id
				ORDER BY
					drive_date DESC
				LIMIT 
					100				
				";
				
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
		}catch(Exception $e){
	
			echo $e->getMessage();

		}
}

//IDでデータを取得
public static function getById($id){

	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql="SELECT
				* 
			  FROM 
			  	day_report
			  WHERE id = $id
		";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		
		while($res=$stmt->fetchObject(__CLASS__)){
			
			$data[]=$res;	
			
		}
			
		return $data;	

	}catch(Exception $e){
		die($e->getMessage());
	}
}

//ドライバーごとの作業履歴を編集
public static function EditWorkRecordsById($id){

	try{
		$dbh=SingletonPDO::connect();
	
		$sql="
		SELECT 
			work.id,
			work.driver_id,
			work.start,
			work.end,
			work.status,
			work.created
		FROM work
		WHERE work.id = $id

		";

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		
		$data=$stmt->fetchAll();

		return $data;

		}
	catch(Exception $e)
		{
	
			echo $e->getMessage();

		}
}


//日報用のデータ投入と編集　$statusの値でスイッチ
//const NEWDATA=1; //新規データ登録
//const EDIT=2; //データ編集

public static function putInDayReportData($datas, $status){

	try{
	
	$dbh=SingletonPDO::connect();
	$dbh->beginTransaction();

	switch($status){
	//新規データ登録
		case "NEWDATA":

			$sql1="
			INSERT INTO 
				day_report
				(driver_id, drive_date, start_meter, arrival_meter, supplied_oil, created)
			VALUES
				(:driver_id, :drive_date, :start_meter, :arrival_meter, :supplied_oil, NOW())
			";

		break;


	//データ編集
		case "EDIT":

			$sql1="
			UPDATE 
				day_report
			SET 
				driver_id=:driver_id, drive_date=:drive_date, start_meter=:start_meter, arrival_meter=:arrival_meter, 
				supplied_oil=:supplied_oil 
			WHERE id=".$datas['id'];
		break;
	}

	$stmt=$dbh->prepare($sql1);

		$param = array(

			'driver_id' => $datas['driver_id'],
			'drive_date' => $datas['drive_date'],
			'start_meter' => $datas['start_meter'],
			'arrival_meter' => $datas['arrival_meter'],
			'supplied_oil' => $datas['supplied_oil']
		
		);
			
	$stmt->execute($param);	
	
	$dbh->commit();
		
	}catch(PDOException $e){

		echo $e->getMessage();
	
	}
}


//日報用のデータを削除するメソッド

public static function deleteDayreportById($id){
	
	try{
		$dbh=SingletonPDO::connect();
		
		$sql="DELETE 
				FROM day_report
				WHERE id = :id
		";
		
		$res = $dbh->prepare($sql);
		
		$param=array(
			'id'=>$id
		);
	
		$res->execute($param);

	}catch(Exception $e){
		$dbh->rollback();
		die($e->getMessage());
	}
}

//日付を指定して、作業時間データの集計
public static function getDayReportByDate($driver_id, $time_from_year, $time_from_month, $time_from_day, 
	 					$time_to_year, $time_to_month, $time_to_day){

					
	try{
		$dbh=SingletonPDO::connect();		
		
		//BETWEENで計算するために、日付に1日プラスする
		//月末だったら次の月の1日をTOにする
		if($time_to_day == date('t')){
			$time_to_day = 1;
			$time_to_month = $time_to_month+1;
		}else{
			$time_to_day = $time_to_day+1;			
		}

		$selected_start = $time_from_year."-".$time_from_month."-".$time_from_day;
		$selected_end = $time_to_year."-".$time_to_month."-".$time_to_day;
		

		$sql = "SELECT 
					*
				FROM 
					day_report
				WHERE
					drive_date BETWEEN CAST( '$selected_start' AS DATETIME) AND CAST( '$selected_end' AS DATETIME)
				AND
					driver_id = $driver_id
				ORDER BY
					drive_date
				";		
				

		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$datas=$stmt->fetchAll();

		return $datas;
		
		}catch(Exception $e){
	
			echo $e->getMessage();

		}	 						
	 						
	}



//クラスDriverReportの終了
}
?>