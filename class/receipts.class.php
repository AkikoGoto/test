<?php
/*
 * Receiptsクラス
 * 定期購読のレシート情報を設定
 * @author Ichinose
 * @since 2014/03/03
 * @version ?
 *
 */

class Receipts {
	
	public $id;
	public $driver_id;
	public $receipt_unique_id;
	public $wallet_order_id;
	public $expiration_date;
	public $created;
	public $updated;
	
	//新規レシート情報のデータ投入
	public static function registerNewReceipt($datas) {
		
		try{
			
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			
			$sql1="
			INSERT INTO 
				receipts(
				driver_id, receipt_unique_id, wallet_order_id, expiration_date, created)
			VALUES
				(:driver_id, :receipt_unique_id, :wallet_order_id, :expiration_date, NOW())";
			
			$stmt=$dbh->prepare($sql1);
	
			$param = array(
				'driver_id' => $datas['driver_id'],
				'receipt_unique_id' => $datas['receipt_unique_id'],
				'wallet_order_id' => $datas['wallet_order_id'],
				'expiration_date' => $datas['expiration_date']
			);
			
			$stmt->execute($param);
			//$id = $dbh->lastInsertId();
			$dbh->commit();
			
			$status = "SUCCESS";
			return $status;
			
		}catch(PDOException $e){
	
			echo $e->getMessage();
	
			$status = "DB_ERROR";
			return $status;
		}
	}
	
	//レシート情報の更新
	public static function updateReceipt($datas, $receipt_id, $driver_id) {
		try{
			$dbh=SingletonPDO::connect();
			$dbh->beginTransaction();
			$sql1="
			UPDATE receipts
			SET
				receipt_unique_id = \"{$datas['receipt_unique_id']}\",
				wallet_order_id = \"{$datas['wallet_order_id']}\",
				expiration_date = \"{$datas['expiration_date']}\",
				updated = now()
			WHERE id = $receipt_id AND driver_id = $driver_id";
			
			$stmt=$dbh->prepare($sql1);
//			$param = array(
//				'receipt_unique_id' => $datas['receipt_unique_id'],
//				'expiration_date' => $datas['expiration_date'],
//			);
//			$stmt->execute($param);
//			$stmt=$dbh->prepare($sql2);
			$stmt->execute();
			$dbh->commit();
			
			$status = "SUCCESS";
			return $status;
			
		}catch(PDOException $e){
			echo $e->getMessage();
			$status = "DB_ERROR";
			return $status;
		}
	}
	
	//レシート情報の読み込み
	public static function getReceiptByDriverId($driver_id) {
		try{
			$dbh=SingletonPDO::connect();
			$sql="
				SELECT id, receipt_unique_id, wallet_order_id, expiration_date
				FROM receipts
				WHERE driver_id = $driver_id";
				$stmt=$dbh->prepare($sql);
				$stmt->execute();
				$data=$stmt->fetchAll();
				return $data;
		}catch(PDOException $e){
			echo $e->getMessage();
		}
	}
	
	//レシートを削除
	public static function deleteReceiptByDriverId($driver_id) {
		try{
			$dbh=SingletonPDO::connect();
			$sql="DELETE FROM receipts WHERE driver_id = :driver_id";
			$res = $dbh->prepare($sql);
			$param=array(
				'driver_id'=>$driver_id
			);
			$res->execute($param);
		}
		catch(Exception $e){
			$dbh->rollback();
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
}