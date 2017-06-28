<?php
/*Liteクラス　
 * Lite版かどうか調べる
 *@author Akiko Goto
 *@since 2013/01/22
 *ver 2.6から 
 */


class Lite extends Data{
	
	/* lite */
	public $is_lite;
	
/*
 * Lite版かどうか調査
 * @param $company_id
 * @return true //Liteだった場合
 */

public static function isLite($company_id){
	
	try{
		$dbh=SingletonPDO::connect();		
		
			
			$sql = "SELECT
						is_lite
					FROM
						company 
					WHERE
						id = $company_id
					";
			
			$stmt=$dbh->prepare($sql);			
			$stmt->execute();
			$is_true = $stmt->fetchAll();
			$is_lite = $is_true[0]["is_lite"];
					
			return $is_lite;
					
		}catch(Exception $e){
	
			echo $e->getMessage();
			return 4;

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
	
//クラスDataの終了
}
?>