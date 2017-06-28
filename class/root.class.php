<?php
/*
 * Rootクラス
 * 配送先ルートを設定
 * @author Akiko Goto
 * @since 2012/12/21
 * @version 2.6
 *
 */

class Root{

	public $id;
	public $date;
	public $driver_id;
	public $updated;
	public $created;


	/*配送先一覧
	 *@param $company_id 会社ID
	 *@param $driver_id ドライバーID
	 */

	public static function viewRoots($company_id,$driver_id){

		try{

			$dbh=SingletonPDO::connect();

			$sql="
					SELECT
						drivers.id as driver_id, roots.id as root_id,
						drivers.last_name, drivers.first_name, roots.date,
						roots.information 
					FROM 
						roots
					JOIN
						drivers
					ON
						drivers.id = roots.driver_id
					WHERE 
						roots.company_id = $company_id
					AND
						roots.driver_id = $driver_id
					ORDER BY
						roots.date DESC";


			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();

			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}

	/*
	 *　日付でデータ内容を取得　ルート
	 * @param $date Date
	 * @param $driver_id id
	 */
	public static function getByDate( $driver_id, $date){

		try{
			$dbh=SingletonPDO::connect();

			$sql="
				SELECT 
					id
				FROM 
					roots
				WHERE
					driver_id = $driver_id
				AND
					date = CAST(\"$date\" as DATE)
				LIMIT 1";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetch(PDO::FETCH_ASSOC);

			return $data;

		}catch(Exception $e){

			echo $e->getMessage();

		}
	}
	
	/*
	 * idを指定して、データ内容を取得　ルート
	 * @param $id ID
	 */
	public static function getById($id){

		try{
			$dbh=SingletonPDO::connect();

			$sql="
				SELECT 
					*
				FROM 
					roots
				WHERE 
					id = $id ";

			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();

			return $data;

		}catch(Exception $e){

			echo $e->getMessage();

		}
	}

	/*データ投入と編集　$statusの値でスイッチ
	 /*const NEWDATA=1; 新規データ登録
	 /*const EDIT=2; データ編集
	 *
	 */

	public static function putInRoot($datas, $status){

		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			switch($status){
				//新規データ登録
				case "NEWDATA":

					$sql="
						INSERT INTO 
							roots
							(company_id, date, driver_id, information, created)
						VALUES
							(:company_id, :date, :driver_id, :information, NOW())
						";

					break;


					//データ編集
				case "EDIT":

					$sql="
						UPDATE 
							roots
						SET 
							company_id=:company_id, 
							date=:date,
							driver_id=:driver_id, 
							information=:information
						WHERE id=".$datas['id'];
					break;
			}
			$stmt=$dbh->prepare($sql);

			$param = array(

				'company_id' => $datas['company_id'],
				'date' => $datas['date'],
				'driver_id' => $datas['driver_id'],
				'information' => $datas['information']
				
			);

			$stmt->execute($param);

			
			if($status =="NEWDATA"){
				$root_id =  $dbh->lastInsertid();
			}
			
			$dbh->commit();
			return $root_id;


		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}

	//ID単位でルート情報を削除するメソッド
	//紐づくルート詳細データも消去

	public static function deleteRoot($id){

		try{
			$dbh=SingletonPDO::connect();

			$sql="DELETE
						roots, root_details
					FROM 
						roots
					LEFT JOIN
						root_details
					ON
						roots.id = root_details.root_id						
					WHERE 
						roots.id = :id 
				";

			$res = $dbh->prepare($sql);

			$param=array(
				'id'=>$id
			);

			$res->execute($param);

		}
		catch(Exception $e){
			die($e->getMessage());
		}
	}


	//存在しないプロパティに値をセットした場合エラーを表示させる
	public function __set($prop,$value){
		print("存在しないプロパティ'{$prop}'に値'{$value}'を設定しました\n");
		debug_print_backtrace();
		exit();
	}

	//存在しないプロパティを参照した場合にエラーを表示させる
	public function __get($prop){
		print("存在しないプロパティ'{$prop}'を参照しました\n");
		exit();
	}




	//クラスMessageの終了
}
?>