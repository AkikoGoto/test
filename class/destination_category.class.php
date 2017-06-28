<?php
/*
 * DestinationCategoryクラス
 * 配送先カテゴリーを設定
 * 配送先と結び付いたメソッドなどはdestinationクラスにある
 * @author Akiko Goto
 * @since 2014/09/22
 * @version 4.4
 *
 */

class DestinationCategory{

	public $id;
	public $company_id;
	public $daddress;
	public $name;
	public $color;
	public $updated;
	public $created;


	/**
	 * 配送先のアップデートに伴う、配送先と配送先カテゴリのリレーションテーブルのアップデート
	 * @param $datas, $status
	 * 
	 */

	public static function putInDestinationCategoryDestination($datas, $status){

		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			switch($status){

				//データ編集 一旦該当の配送先にひもづいたリレーションを全部消去
				case "EDIT":

					$sql0 = "DELETE 
								
							FROM 
								destination_categories_destinations
							WHERE
								destination_id =".$datas['id'];
					$stmt=$dbh->prepare($sql0);
					$stmt->execute();
					
					break;

			}
		
			foreach($datas['category'] as $each_category) {
			
				$sql1="INSERT INTO 
						destination_categories_destinations
							(destination_id, destination_categories_id, created)
						VALUES
							(:destination_id, :destination_categories_id, NOW())
						";
				
				$stmt=$dbh->prepare($sql1);
	
				$param = array(
	
					'destination_id' => $datas['id'],
					'destination_categories_id' => $each_category
					
				);

				$stmt->execute($param);

			}
			$dbh->commit();

		}catch(PDOException $e){

			echo $e->getMessage();
			exit(0);

		}
	}	
	
	
	/**
	 * 配送先カテゴリーを追加・編集
	 * @param $datas, $status
	 *  
	 */
	public function putInDestinationCategory($datas, $status){
				
		try{

			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			switch($status){
				//新規データ登録
				case "NEWDATA":

					$sql1 = "
						INSERT INTO 
							destination_categories
							(company_id, name, color, created)
						VALUES
							(:company_id, :name, :color, NOW())
						";

					break;


					//データ編集
				case "EDIT":

					$sql1 = "
						UPDATE 
							destination_categories
						SET 
							company_id =:company_id,
							name =:name, 
							color =:color
						WHERE id=".$datas['id'];
					break;
			}

			$stmt=$dbh->prepare($sql1);

			$param = array(

				'company_id' => $datas['company_id'],
				'name' => $datas['name'],
				'color' => $datas['color']
				
			);

			$stmt->execute($param);
			
			$dbh->commit();

		}catch(PDOException $e){

			echo $e->getMessage();

		}
		
	}

	/**
	 * 配送先カテゴリーを検索してから、ない場合に新規に追加
	 * 配送先カテゴリーのIDを返す
	 * @param $datas, $status
	 * @return 配送先カテゴリーID
	 */
	public function putInAndReturnIdDestinationCategory( $company_id, $name, $color ){
				
		try{

			$dbh=SingletonPDO::connect();
//			$dbh->beginTransaction();

			$sql = "SELECT
						id
					FROM
						destination_categories
					WHERE
						name = '$name'";
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();
			
			$category_id = null;
			//idをそのまま利用
			if ( $data ) {
				$category_id = $data[0]['id'];
			} else {
				$insert_sql = "
						INSERT INTO 
							destination_categories
							(company_id, name, color, created)
						VALUES
							(:company_id, :name, :color, NOW())
						";
				$insert_stmt = $dbh->prepare($insert_sql);
				$param = array(
					'company_id' => $company_id,
					'name' => $name,
					'color' => $color
				);
				$insert_stmt->execute($param);
				$category_id = $dbh->lastInsertId();
			}
			
			return $category_id;

		}catch(PDOException $e){

			echo $e->getMessage();

		}
		
	}
		
	/**
	 * 配送先カテゴリー一覧
	 */

	public static function searchDestinationCategories($company_id){

		try{

			$dbh=SingletonPDO::connect();
			

			$sql="
					SELECT
						* 
					FROM 
						destination_categories
					WHERE 
						company_id = $company_id";


			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$data=$stmt->fetchAll();

			return $data;

		}catch(PDOException $e){

			echo $e->getMessage();

		}
	}

	

	/**
	 * idを指定して、データ内容を取得　配送先カテゴリー
	 * @param $id
	 */
	public static function getById($id){

		try{
			$dbh=SingletonPDO::connect();

			$sql="
				SELECT 
					*
				FROM 
					destination_categories
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

	
	/**
	 * ID単位で配送先カテゴリー情報を削除するメソッド
	 */
	public static function deleteDestinationCategory($id){

		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();

			
			//リレーションがあるデータを消去 
			$sql="DELETE
						
					FROM 
						destination_categories, destination_categories_destinations
					USING 
						destination_categories, destination_categories_destinations
					WHERE
						destination_categories.id = destination_categories_destinations.destination_categories_id
					AND 
						destination_categories.id = :id 
				";

			$res = $dbh->prepare($sql);

			$param=array(
				'id'=>$id
			);

			$res->execute($param);

			//リレーションが一件もないデータは上記で消えないので、下記で消去
			$sql2="DELETE
						
					FROM 
						destination_categories
					WHERE
						destination_categories.id = :id 
				";
			
			$res2 = $dbh->prepare($sql2);
			$res2->execute($param);
			
			
			$dbh->commit();

		}catch(Exception $e){
			$dbh->rollback();
			echo($e->getMessage());
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


}
?>